<?php

namespace App\Services;

use App\Http\Controllers\Student\DashboardController;
use App\Jobs\SendDonationReceiptEmailJob;
use App\Models\CartManagement;
use App\Models\CartManagementWebsite;
use App\Models\Course;
use App\Models\DonationOrder;
use App\Models\DonationRecurringSchedule;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Student;
use App\Models\TrialClass;
use App\Models\User;
use App\Traits\SendNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Meezan getOrderStatusExtended + payment completion (LMS orders + donation_orders).
 * Used by return-URL handlers and meezan:reconcile-pending-orders.
 */
class MeezanPaymentCompletionService
{
    use SendNotification;

    public const RESULT_ALREADY_PAID = 'already_paid';
    public const RESULT_COMPLETED = 'completed';
    public const RESULT_PENDING = 'pending';
    public const RESULT_DECLINED = 'declined';
    public const RESULT_ERROR = 'error';

    /**
     * Reconcile an LMS Order against Meezan status API.
     *
     * @return array{result: string, message: string, status: bool, data: ?Order}
     */
    public function reconcileOrder(Order $order): array
    {
        $order = Order::where('id', $order->id)->first();
        if (!$order) {
            return $this->result(self::RESULT_ERROR, 'Order not found', false, null);
        }

        if ($order->payment_status === 'paid') {
            return $this->result(
                self::RESULT_ALREADY_PAID,
                'Amount already deposited and paid.',
                true,
                $order
            );
        }

        if (empty($order->meezan_transaction_id)) {
            return $this->result(self::RESULT_ERROR, 'Missing Meezan transaction id', false, $order);
        }

        $statusCall = $this->getOrderStatusExtended(
            $order->meezan_transaction_id,
            $order->meezan_order_id,
            false
        );

        if (!$statusCall['ok']) {
            return $this->result(
                self::RESULT_ERROR,
                $statusCall['message'] ?? 'Meezan status API error',
                false,
                $order
            );
        }

        $arrayResult = $statusCall['data'];
        $rawResponse = $statusCall['raw'];
        $errorCode = (int) ($arrayResult['errorCode'] ?? -1);

        if ($errorCode !== 0) {
            return $this->result(
                self::RESULT_ERROR,
                $arrayResult['errorMessage'] ?? 'Meezan API error',
                false,
                $order
            );
        }

        $orderStatus = (int) ($arrayResult['orderStatus'] ?? -1);

        if ($orderStatus === 2) {
            $completed = $this->completePaidOrder($order, $arrayResult, $rawResponse);
            return $this->result(
                self::RESULT_COMPLETED,
                'Amount was deposited successfully.',
                true,
                $completed
            );
        }

        $message = $this->messageForOrderStatus(
            $orderStatus,
            $arrayResult['actionCode'] ?? null,
            $arrayResult['actionCodeDescription'] ?? null
        );

        if (in_array($orderStatus, [0, 1], true)) {
            return $this->result(self::RESULT_PENDING, $message, false, $order);
        }

        return $this->result(self::RESULT_DECLINED, $message, false, $order);
    }

    /**
     * Reconcile a website DonationOrder against Meezan status API.
     *
     * @return array{result: string, message: string, status: bool, data: ?DonationOrder}
     */
    public function reconcileDonationOrder(DonationOrder $order): array
    {
        $order = DonationOrder::where('id', $order->id)->first();
        if (!$order) {
            return $this->result(self::RESULT_ERROR, 'Donation order not found', false, null);
        }

        if ($order->payment_status === 'paid') {
            return $this->result(
                self::RESULT_ALREADY_PAID,
                'Amount already deposited and paid.',
                true,
                $order
            );
        }

        if (empty($order->meezan_transaction_id)) {
            return $this->result(self::RESULT_ERROR, 'Missing Meezan transaction id', false, $order);
        }

        $statusCall = $this->getOrderStatusExtended(
            $order->meezan_transaction_id,
            $order->meezan_order_id,
            true
        );

        if (!$statusCall['ok']) {
            return $this->result(
                self::RESULT_ERROR,
                $statusCall['message'] ?? 'Meezan status API error',
                false,
                $order
            );
        }

        $arrayResult = $statusCall['data'];
        $rawResponse = $statusCall['raw'];
        $errorCode = (int) ($arrayResult['errorCode'] ?? -1);

        if ($errorCode !== 0) {
            return $this->result(
                self::RESULT_ERROR,
                $arrayResult['errorMessage'] ?? 'Meezan API error',
                false,
                $order
            );
        }

        $orderStatus = (int) ($arrayResult['orderStatus'] ?? -1);

        if ($orderStatus === 2) {
            $completed = $this->completePaidDonationOrder($order, $arrayResult, $rawResponse);
            return $this->result(
                self::RESULT_COMPLETED,
                'Donation Amount was deposited successfully.',
                true,
                $completed
            );
        }

        $message = $this->messageForOrderStatus(
            $orderStatus,
            $arrayResult['actionCode'] ?? null,
            $arrayResult['actionCodeDescription'] ?? null
        );

        if (in_array($orderStatus, [0, 1], true)) {
            return $this->result(self::RESULT_PENDING, $message, false, $order);
        }

        return $this->result(self::RESULT_DECLINED, $message, false, $order);
    }

    /**
     * @return array{ok: bool, data: ?array, raw: ?string, message: ?string}
     */
    public function getOrderStatusExtended(
        string $meezanTransactionId,
        ?string $meezanOrderNumber = null,
        bool $useDonationCredentialFallback = false
    ): array {
        $config = $this->getMeezanConfig($useDonationCredentialFallback);
        if ($config['userName'] === '' || $config['password'] === '' || $config['portAddress'] === '') {
            return [
                'ok' => false,
                'data' => null,
                'raw' => null,
                'message' => 'Meezan credentials or port address not configured',
            ];
        }

        $tokenApiUrl = $config['portAddress'] . 'getOrderStatusExtended.do';
        $urlPostParams = [
            'userName' => $config['userName'],
            'password' => $config['password'],
            'orderId' => $meezanTransactionId,
            'language' => 'en',
        ];
        if (!empty($meezanOrderNumber)) {
            $urlPostParams['orderNumber'] = $meezanOrderNumber;
        }

        $query = urldecode(http_build_query($urlPostParams));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $tokenApiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_USERAGENT, 'CURL/PHP Meezan API Reconcile');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        $response = curl_exec($ch);
        $curlErr = curl_error($ch);
        curl_close($ch);

        $arrayResult = $response ? json_decode($response, true) : null;
        if (empty($arrayResult) || !is_array($arrayResult)) {
            $msg = $curlErr ? "cURL error: {$curlErr}" : 'Empty or invalid Meezan status response';
            return ['ok' => false, 'data' => null, 'raw' => is_string($response) ? $response : null, 'message' => $msg];
        }

        return [
            'ok' => true,
            'data' => $arrayResult,
            'raw' => is_string($response) ? $response : json_encode($arrayResult),
            'message' => null,
        ];
    }

    /**
     * Complete LMS order after Meezan confirmed paid (orderStatus == 2).
     * Auth-free: uses order.user_id. Preserves order_item created_by_type (monthly fee / donations).
     */
    public function completePaidOrder(Order $order, array $arrayResult, string $rawResponse): Order
    {
        return DB::transaction(function () use ($order, $arrayResult, $rawResponse) {
            $locked = Order::where('id', $order->id)->lockForUpdate()->first();
            if (!$locked) {
                throw new \RuntimeException('Order not found during completion');
            }
            if ($locked->payment_status === 'paid') {
                return $locked;
            }

            $userId = (int) $locked->user_id;
            $user = User::with('student')->find($userId);
            if (!$user) {
                throw new \RuntimeException("Order {$locked->id} has no user");
            }

            $transactionID = $locked->meezan_transaction_id;
            $orderNumber = $arrayResult['orderNumber'] ?? $locked->meezan_order_id;

            $locked->meezan_transaction_id = $transactionID;
            $locked->payment_id = $transactionID;
            $locked->meezan_payment_source = 'Card';
            $locked->meezan_order_number = $orderNumber;
            $locked->meezan_payment_date = date('y-m-d h:i:sa');
            $locked->meezan_trans_response = $rawResponse;

            // Checkout-all style headers (dashboard pay-checkout-all)
            $locked->cart_type = 1;
            $locked->created_by_type = 1;
            $locked->payment_status = 'paid';
            $locked->delivery_status = 1;
            $locked->save();

            $locked->load('items');
            $studentStatus = (int) ($user->student->status ?? 1);

            foreach ($locked->items as $order_item) {
                // Keep placed item flags (is_monthly_cron / created_by_type=2 for monthly fees)
                if ($order_item->cart_type === null) {
                    $order_item->cart_type = $order_item->created_by_type ?? 1;
                }
                $order_item->save();

                if (!$order_item->course_id) {
                    continue;
                }

                $courseExits = Course::find($order_item->course_id);
                if (!$courseExits) {
                    continue;
                }

                $itemCreatedByType = (int) ($order_item->created_by_type ?? 1);

                if ($studentStatus === 0) {
                    $this->upsertEnrollment($order_item, $courseExits, $userId);
                    $getStudent = Student::where('id', $user->student->id)->first();
                    if ($getStudent) {
                        $getStudent->status = 1;
                        $getStudent->save();
                        $studentStatus = 1;
                    }
                    sendEmailNotification([
                        '{{USER_NAME}}' => $user->name,
                        '{{EMAIL}}' => $user->email,
                        '{{PASSWORD}}' => $user->random_password,
                    ], $user->email, 'student-sign-up');
                } elseif ($itemCreatedByType === 1) {
                    $this->upsertEnrollment($order_item, $courseExits, $userId);
                }
                // created_by_type == 2 (monthly fee): paid only, no enrollment change
            }

            try {
                app(DashboardController::class)->sendPaymentEmail($locked->id, $locked->meezan_order_id);
            } catch (\Throwable $e) {
                Log::warning('[MeezanPaymentCompletionService] sendPaymentEmail failed', [
                    'order_id' => $locked->id,
                    'error' => $e->getMessage(),
                ]);
            }

            try {
                $target_url = route('student_payment.completed');
                $text = __('Student payment has been accepted by meezan bank.');
                $this->send($text, 1, $target_url, null);
            } catch (\Throwable $e) {
                Log::warning('[MeezanPaymentCompletionService] admin notify failed', [
                    'order_id' => $locked->id,
                    'error' => $e->getMessage(),
                ]);
            }

            CartManagement::whereUserId($userId)
                ->whereIn('created_by_type', [1, 2])
                ->delete();

            Log::info('[MeezanPaymentCompletionService] LMS order completed', ['order_id' => $locked->id]);

            return $locked->fresh(['items']);
        });
    }

    /**
     * Complete DonationOrder after Meezan confirmed paid.
     */
    public function completePaidDonationOrder(
        DonationOrder $order,
        array $arrayResult,
        string $rawResponse
    ): DonationOrder {
        return DB::transaction(function () use ($order, $arrayResult, $rawResponse) {
            $locked = DonationOrder::where('id', $order->id)->lockForUpdate()->first();
            if (!$locked) {
                throw new \RuntimeException('Donation order not found during completion');
            }
            if ($locked->payment_status === 'paid') {
                return $locked;
            }

            $transactionID = $locked->meezan_transaction_id;
            $orderNumber = $arrayResult['orderNumber'] ?? $locked->meezan_order_id;
            $cartSessionId = $locked->cart_session_id;

            $locked->meezan_transaction_id = $transactionID;
            $locked->payment_id = $transactionID;
            $locked->meezan_payment_source = 'Card';
            $locked->meezan_order_number = $orderNumber;
            $locked->meezan_payment_date = date('y-m-d h:i:sa');
            $locked->meezan_trans_response = $rawResponse;

            if (!empty($arrayResult['recurrentPaymentId'])) {
                $locked->meezan_recurrence_id = $arrayResult['recurrentPaymentId'];
            }

            if ($locked->meezan_order_id == null && $cartSessionId) {
                $cartRow = CartManagementWebsite::where('cart_session_id', $cartSessionId)->first();
                if ($cartRow && $cartRow->meezan_order_id) {
                    $locked->meezan_order_id = $cartRow->meezan_order_id;
                }
            }

            $locked->payment_status = 'paid';
            $locked->delivery_status = 1;
            $locked->save();

            $locked->load('donation_items');

            foreach ($locked->donation_items as $value) {
                $value->updated_at = Carbon::now();
                $value->month = date('m');
                $value->year = date('Y');
                $value->save();
            }

            if ($locked->meezan_recurrence_id) {
                $orderCreatedAt = Carbon::parse($locked->created_at);
                foreach ($locked->donation_items as $item) {
                    if (!in_array($item->cycle, ['monthly', 'annually', 'daily'], true)) {
                        continue;
                    }
                    $exists = DonationRecurringSchedule::where('order_id', $locked->id)
                        ->where('order_item_id', $item->id)
                        ->exists();
                    if ($exists) {
                        continue;
                    }
                    $nextDue = $item->cycle === 'daily'
                        ? $orderCreatedAt->copy()->addDay()
                        : ($item->cycle === 'monthly'
                            ? $orderCreatedAt->copy()->addMonth()
                            : $orderCreatedAt->copy()->addYear());
                    DonationRecurringSchedule::create([
                        'donation_user_id' => $locked->donation_user_id,
                        'order_id' => $locked->id,
                        'order_item_id' => $item->id,
                        'cycle' => $item->cycle,
                        'amount' => $item->unit_price,
                        'currency' => 'PKR',
                        'next_due_date' => $nextDue->toDateString(),
                        'status' => 'active',
                        'gateway_recurrence_id' => $locked->meezan_recurrence_id,
                    ]);
                }
            }

            if ($cartSessionId) {
                CartManagementWebsite::where('cart_session_id', $cartSessionId)->delete();
            }

            $getPaymentData2 = $locked->fresh(['donation_user', 'donation_items']);

            try {
                $webhookService = app(DonationWebhookService::class);
                $hasRecurringItems = $getPaymentData2->donation_items->contains(function ($item) {
                    return in_array(strtolower($item->cycle ?? ''), ['monthly', 'annually', 'daily', 'quarterly'], true);
                });
                $hasOneTimeItems = $getPaymentData2->donation_items->contains(function ($item) {
                    return !in_array(strtolower($item->cycle ?? ''), ['monthly', 'annually', 'daily', 'quarterly'], true);
                });

                $providerRecurrenceId = $getPaymentData2->meezan_recurrence_id ?: null;
                $regularGivingImportId = 'RG' . $getPaymentData2->id;
                $bankOptions = [
                    'bank_response' => $arrayResult,
                    'transaction_id' => $transactionID,
                    'order_id' => $orderNumber,
                    'provider_recurrence_id' => $providerRecurrenceId,
                ];

                if ($hasRecurringItems) {
                    $webhookService->sendRegularGivingCreated($getPaymentData2, array_merge($bankOptions, [
                        'import_id' => $regularGivingImportId,
                    ]));
                    $webhookService->sendDonationCreatedRegular($getPaymentData2, array_merge($bankOptions, [
                        'import_id' => 'DN' . $getPaymentData2->id,
                        'regular_giving_import_id' => $regularGivingImportId,
                        'payment_status' => 'Paid',
                    ]));
                }

                if ($hasOneTimeItems || !$hasRecurringItems) {
                    $webhookService->sendDonationCreatedSingle($getPaymentData2, array_merge($bankOptions, [
                        'import_id' => 'DN' . $getPaymentData2->id . ($hasRecurringItems && $hasOneTimeItems ? '-OT' : ''),
                        'payment_status' => 'Paid',
                    ]));
                }
            } catch (\Throwable $e) {
                Log::warning('[MeezanPaymentCompletionService] Donation webhook failed', [
                    'order_id' => $getPaymentData2->id,
                    'error' => $e->getMessage(),
                ]);
            }

            if ($getPaymentData2->donation_user && !empty(trim($getPaymentData2->donation_user->email ?? ''))) {
                try {
                    SendDonationReceiptEmailJob::dispatch($getPaymentData2->id, 'automatic');
                } catch (\Throwable $e) {
                    Log::warning('[MeezanPaymentCompletionService] Receipt email dispatch failed', [
                        'order_id' => $getPaymentData2->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            Log::info('[MeezanPaymentCompletionService] Donation order completed', ['order_id' => $getPaymentData2->id]);

            return $getPaymentData2;
        });
    }

    private function upsertEnrollment($order_item, Course $courseExits, int $userId): void
    {
        $enrollment = Enrollment::where('user_id', $userId)
            ->where('course_id', $order_item->course_id)
            ->orderBy('id', 'DESC')
            ->first();

        if (!$enrollment) {
            $enrollment = new Enrollment();
            Log::info('[MeezanPaymentCompletionService] Creating enrollment', [
                'order_id' => $order_item->order_id,
                'course_id' => $order_item->course_id,
                'user_id' => $userId,
            ]);
        }

        $enrollment->order_id = $order_item->order_id;
        $enrollment->user_id = $userId;
        $enrollment->course_id = $order_item->course_id;
        $enrollment->owner_user_id = $courseExits->user_id ?? null;
        $enrollment->start_date = now();
        $enrollment->end_date = ($courseExits->access_period)
            ? Carbon::now()->addDays($courseExits->access_period)
            : MAX_EXPIRED_DATE;

        if ($order_item->request_enrollment && $order_item->trial_id != null) {
            $enrollment->trial_id = $order_item->trial_id;
            $getTrialInfo = TrialClass::where('id', $order_item->trial_id)->first();
            if ($getTrialInfo) {
                $getTrialInfo->student_enrolled = 1;
                $getTrialInfo->save();
            }
        }

        $enrollment->main_price = $order_item->unit_price;
        $enrollment->price = $order_item->unit_price;
        $enrollment->original_price = $order_item->original_price;
        $enrollment->discount = $order_item->discount;
        $enrollment->admission_fee = $order_item->admission_fee;
        $enrollment->discount_type = $order_item->discount_type;
        $enrollment->net_original_price = $order_item->net_original_price;
        $enrollment->discount_type_admission_fee = $order_item->discount_type_admission_fee;
        $enrollment->discount_admission_fee = $order_item->discount_admission_fee;
        $enrollment->net_admission_fee = $order_item->net_admission_fee;
        $enrollment->save();
    }

    /**
     * @return array{portAddress: string, userName: string, password: string}
     */
    private function getMeezanConfig(bool $useDonationCredentialFallback): array
    {
        $portAddress = (string) (get_option('meezan_port_address') ?: '');
        $userName = (string) (get_option('meezan_user_name') ?: '');
        $password = (string) (get_option('meezan_user_password') ?: '');

        if ($useDonationCredentialFallback) {
            $portAddress = $portAddress ?: 'https://acquiring.meezanbank.com/payment/rest/';
            $userName = $userName ?: 'Character_api';
            $password = $password ?: 'C987658';
        }

        $portAddress = $portAddress !== '' ? (rtrim($portAddress, '/') . '/') : '';

        return compact('portAddress', 'userName', 'password');
    }

    private function messageForOrderStatus(int $orderStatus, $actionCode, $actionDescription): string
    {
        $errorMsg = 'Payment not completed.';
        if ($orderStatus === 6) {
            $errorMsg = 'Authorization is declined.';
        } elseif ($orderStatus === 0) {
            $errorMsg = 'Order registered, but not paid.';
        } elseif ($orderStatus === 1) {
            $errorMsg = 'Transaction has been approved.';
        } elseif ($orderStatus === 3) {
            $errorMsg = 'Authorization has been reversed.';
        } elseif ($orderStatus === 4) {
            $errorMsg = 'Transaction has been refunded.';
        }

        if ($actionCode != 0 && !empty($actionDescription)) {
            $errorMsg .= ' ' . $actionDescription;
        }

        return $errorMsg;
    }

    private function result(string $result, string $message, bool $status, $data): array
    {
        return [
            'result' => $result,
            'message' => $message,
            'status' => $status,
            'data' => $data,
        ];
    }
}

<?php

namespace App\Console\Commands;

use App\Models\DonationOrder;
use App\Models\DonationOrder_item;
use App\Models\DonationRecurringSchedule;
use App\Services\DonationWebhookService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessRecurringDonationSchedules extends Command
{
    protected $signature = 'donations:process-recurring-schedules';
    protected $description = 'Process due donation recurring schedules (Meezan recurringPayment.do)';

    /** Meezan API: same base/credentials as donation checkout (CartManagementController makeDonationMeezanPayment) */
    private function getMeezanConfig(): array
    {
        $portAddress = get_option('meezan_port_address') ?: 'https://acquiring.meezanbank.com/payment/rest/';
        $userName = get_option('meezan_user_name') ?: 'Character_api';
        $password = get_option('meezan_user_password') ?: 'C987658';
        $portAddress = rtrim($portAddress, '/') . '/';
        return compact('portAddress', 'userName', 'password');
    }

    /**
     * Call Meezan recurringPayment.do (userName, password, recurrenceId, amount in minor units).
     * Returns ['success' => bool, 'orderId' => ?string, 'errorMessage' => ?string].
     */
    private function callMeezanRecurringPayment(string $userName, string $password, string $recurrenceId, int $amountMinor): array
    {
        $portAddress = $this->getMeezanConfig()['portAddress'];
        $tokenApiUrl = $portAddress . 'recurringPayment.do';
        $urlPostParams = [
            'userName' => $userName,
            'password' => $password,
            'recurrenceId' => $recurrenceId,
            'amount' => $amountMinor,
        ];
        $query = urldecode(http_build_query($urlPostParams));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $tokenApiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_USERAGENT, 'CURL/PHP Meezan API Recurring');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        $response = curl_exec($ch);
        $curlErr = curl_error($ch);
        curl_close($ch);
        $arrayResult = $response ? json_decode($response, true) : null;
        $rawResponse = is_string($response) ? $response : null;
        if (empty($arrayResult)) {
            $msg = $curlErr ? "cURL error: {$curlErr}" : 'Empty or invalid Meezan response';
            if ($response !== false && $response !== '') {
                $msg .= ' (response: ' . substr((string) $response, 0, 200) . ')';
            }
            return ['success' => false, 'orderId' => null, 'errorMessage' => $msg, 'rawResponse' => $rawResponse];
        }
        $errorCode = (int) ($arrayResult['errorCode'] ?? -1);
        if ($errorCode !== 0) {
            $apiMsg = $arrayResult['errorMessage'] ?? 'Unknown error';
            $hint = ($errorCode === 5 && stripos($apiMsg, 'SSL') !== false)
                ? ' (Merchant config: ask Meezan to enable SSL/server-to-server payments for Mastercard for your account)'
                : '';
            return [
                'success' => false,
                'orderId' => null,
                'errorMessage' => "Meezan API errorCode {$errorCode}: {$apiMsg}{$hint}",
                'rawResponse' => $rawResponse,
            ];
        }
        return [
            'success' => true,
            'orderId' => $arrayResult['orderId'] ?? null,
            'errorMessage' => null,
            'rawResponse' => $rawResponse,
        ];
    }

    /**
     * @return array{success: bool, gateway: ?array, raw_response: ?string, error_message?: string}
     */
    private function webhookBankResponseForRecurringAttempt(bool $success, ?string $errorMessage, ?string $rawResponse): array
    {
        $parsed = null;
        if (is_string($rawResponse) && $rawResponse !== '') {
            $decoded = json_decode($rawResponse, true);
            if (is_array($decoded)) {
                $parsed = $decoded;
            }
        }

        $out = [
            'success' => $success,
            'gateway' => $parsed,
            'raw_response' => $rawResponse,
        ];
        if (!$success && $errorMessage !== null && $errorMessage !== '') {
            $out['error_message'] = $errorMessage;
        }

        return $out;
    }

    public function handle(): int
    {
        $today = Carbon::today()->toDateString();
        Log::info('[ProcessRecurringDonationSchedules] Started', ['date' => $today]);

        $schedules = DonationRecurringSchedule::with('donation_order_item')
            ->where('status', 'active')
            ->where('next_due_date', '<=', $today)
            ->whereNotNull('gateway_recurrence_id')
            ->orderBy('id')
            ->get();

        if ($schedules->isEmpty()) {
            Log::info('[ProcessRecurringDonationSchedules] No due schedules');
            $this->info('No due schedules.');
            return 0;
        }

        $config = $this->getMeezanConfig();
        $processed = 0;
        $failed = 0;

        foreach ($schedules as $schedule) {
            $period = $schedule->cycle === 'daily'
                ? Carbon::parse($schedule->next_due_date)->format('Y-m-d')
                : ($schedule->cycle === 'monthly'
                    ? Carbon::parse($schedule->next_due_date)->format('Y-m')
                    : Carbon::parse($schedule->next_due_date)->format('Y'));

            // Idempotency: skip if we already created an order for this schedule + period
            if (DonationOrder::where('recurring_schedule_id', $schedule->id)->where('recurring_period', $period)->exists()) {
                Log::info('[ProcessRecurringDonationSchedules] Skip duplicate', ['schedule_id' => $schedule->id, 'period' => $period]);
                continue;
            }

            $amountMinor = (int) round($schedule->amount * 100);
            $result = $this->callMeezanRecurringPayment(
                $config['userName'],
                $config['password'],
                $schedule->gateway_recurrence_id,
                $amountMinor
            );

            if (!$result['success']) {
                $this->error("Schedule ID {$schedule->id} (order_id {$schedule->order_id}, gateway_recurrence_id {$schedule->gateway_recurrence_id}): {$result['errorMessage']}");
                Log::warning('[ProcessRecurringDonationSchedules] recurringPayment failed', [
                    'schedule_id' => $schedule->id,
                    'error' => $result['errorMessage'],
                    'raw_response' => $result['rawResponse'] ?? null,
                ]);
                try {
                    $sourceOrder = DonationOrder::with(['donation_user', 'donation_items'])->find($schedule->order_id);
                    if ($sourceOrder) {
                        app(DonationWebhookService::class)->sendDonationCreatedRegular($sourceOrder, [
                            'import_id' => 'DN' . $schedule->order_id . '-R-' . $schedule->id . '-' . $period,
                            'regular_giving_import_id' => 'RG' . $schedule->order_id,
                            'provider_recurrence_id' => $schedule->gateway_recurrence_id,
                            'payment_status' => 'Failed',
                            'transaction_id' => null,
                            'order_id' => null,
                            'bank_response' => $this->webhookBankResponseForRecurringAttempt(
                                false,
                                $result['errorMessage'] ?? null,
                                $result['rawResponse'] ?? null
                            ),
                        ]);
                    }
                } catch (\Throwable $webhookEx) {
                    Log::warning('[ProcessRecurringDonationSchedules] failed-attempt webhook send failed', [
                        'schedule_id' => $schedule->id,
                        'error' => $webhookEx->getMessage(),
                    ]);
                }
                $failed++;
                continue;
            }

            try {
                DB::transaction(function () use ($schedule, $period, $result) {
                    $order = new DonationOrder();
                    $order->donation_user_id = $schedule->donation_user_id;
                    $order->order_number = rand(100000, 999999);
                    $order->sub_total = $schedule->amount;
                    $order->grand_total = $schedule->amount;
                    $order->payment_method = 'meezan_bank';
                    $order->payment_status = 'paid';
                    $order->delivery_status = 1;
                    $order->is_monthly_cron = 1;
                    $order->recurring_schedule_id = $schedule->id;
                    $order->recurring_period = $period;
                    $order->payment_currency = $schedule->currency ?: 'PKR';
                    if (!empty($result['orderId'])) {
                        $order->meezan_transaction_id = $result['orderId'];
                        $order->payment_id = $result['orderId'];
                    }
                    $order->meezan_payment_source = 'Recurring (cron)';
                    $order->meezan_payment_date = now()->format('y-m-d h:i:sa');
                    $order->meezan_trans_response = $result['rawResponse'] ?? null;
                    $order->save();

                    $originalItem = $schedule->donation_order_item;
                    $item = new DonationOrder_item();
                    $item->order_id = $order->id;
                    $item->donation_user_id = $schedule->donation_user_id;
                    $item->item_name = $originalItem ? $originalItem->item_name : 'Recurring donation';
                    $item->item_id = $originalItem?->item_id;
                    $item->unit_price = $schedule->amount;
                    $item->original_price = $schedule->amount;
                    $item->quantity = 1;
                    $item->cycle = $schedule->cycle;
                    $item->type = $originalItem?->type;
                    $item->sponsor_type = $originalItem?->sponsor_type;
                    $item->is_monthly_cron = 1;
                    $item->month = $schedule->cycle === 'monthly' ? Carbon::parse($schedule->next_due_date)->format('m') : ($schedule->cycle === 'daily' ? Carbon::parse($schedule->next_due_date)->format('m') : null);
                    $item->year = Carbon::parse($schedule->next_due_date)->format('Y');
                    $item->save();

                    $schedule->last_charged_at = now();
                    $schedule->next_due_date = $schedule->cycle === 'daily'
                        ? Carbon::parse($schedule->next_due_date)->addDay()->toDateString()
                        : ($schedule->cycle === 'monthly'
                            ? Carbon::parse($schedule->next_due_date)->addMonth()->toDateString()
                            : Carbon::parse($schedule->next_due_date)->addYear()->toDateString());
                    $schedule->save();

                    try {
                        $order->load(['donation_user', 'donation_items']);
                        app(DonationWebhookService::class)->sendDonationCreatedRegular($order, [
                            'import_id' => 'DN' . $order->id,
                            'regular_giving_import_id' => 'RG' . $schedule->order_id,
                            'provider_recurrence_id' => $schedule->gateway_recurrence_id,
                            'payment_status' => 'Paid',
                            'transaction_id' => $result['orderId'] ?? null,
                            'order_id' => $result['orderId'] ?? null,
                            'bank_response' => $this->webhookBankResponseForRecurringAttempt(
                                true,
                                null,
                                $result['rawResponse'] ?? null
                            ),
                        ]);
                    } catch (\Throwable $webhookEx) {
                        Log::warning('[ProcessRecurringDonationSchedules] success-attempt webhook send failed', [
                            'schedule_id' => $schedule->id,
                            'error' => $webhookEx->getMessage(),
                        ]);
                    }
                });
                $processed++;
                Log::info('[ProcessRecurringDonationSchedules] Charged', ['schedule_id' => $schedule->id, 'period' => $period]);
            } catch (\Throwable $e) {
                Log::error('[ProcessRecurringDonationSchedules] Transaction failed after gateway success', [
                    'schedule_id' => $schedule->id,
                    'exception' => $e->getMessage(),
                ]);
                $failed++;
            }
        }

        $this->info("Processed: {$processed}, Failed: {$failed}");
        Log::info('[ProcessRecurringDonationSchedules] Finished', ['processed' => $processed, 'failed' => $failed]);
        return 0;
    }
}

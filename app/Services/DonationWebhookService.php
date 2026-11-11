<?php

namespace App\Services;

use App\Models\DonationOrder;
use App\Models\DonationRecurringSchedule;
use App\Models\DonationWebhookLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DonationWebhookService
{
    private const MAX_RESPONSE_BODY_LENGTH = 65535;

    private const URL_NOT_CONFIGURED = '(not configured)';

    /**
     * Send donation.created webhook.
     * Backward-compatible defaults to singleDonation when options are omitted.
     *
     * Options:
     * - donation_type: singleDonation|regularDonation
     * - allocation_scope: one_time|recurring|all
     * - regular_giving_import_id: string|null
     * - provider_recurrence_id: string|null
     * - payment_status: Paid|Failed|...
     * - transaction_id: string|null
     * - order_id: string|null
     * - bank_response: mixed|null (omit or null to use gateway data stored on the order)
     */
    public function sendDonationCreated(DonationOrder $order, array $options = []): ?array
    {
        $url = config('services.donation_webhook.donation_created_url');
        $payload = $this->buildDonationCreatedPayload($order, $options);

        if (empty($url)) {
            $this->logOnly($order->id, 'donation.created', self::URL_NOT_CONFIGURED, $payload, 'Webhook URL not configured');
            return null;
        }

        return $this->postAndLog($order->id, 'donation.created', $url, $payload);
    }

    /**
     * Convenience helper for one-time donation webhook.
     */
    public function sendDonationCreatedSingle(DonationOrder $order, array $options = []): ?array
    {
        return $this->sendDonationCreated($order, array_merge($options, [
            'donation_type' => 'singleDonation',
            'allocation_scope' => $options['allocation_scope'] ?? 'one_time',
        ]));
    }

    /**
     * Convenience helper for regular donation webhook.
     */
    public function sendDonationCreatedRegular(DonationOrder $order, array $options = []): ?array
    {
        return $this->sendDonationCreated($order, array_merge($options, [
            'donation_type' => 'regularDonation',
            'allocation_scope' => $options['allocation_scope'] ?? 'recurring',
        ]));
    }

    /**
     * Send regular_giving.created webhook (recurring plan creation).
     *
     * Options:
     * - provider_recurrence_id: string|null
     * - bank_response: mixed|null
     */
    public function sendRegularGivingCreated(DonationOrder $order, array $options = []): ?array
    {
        $url = config('services.donation_webhook.regular_giving_created_url');
        $payload = $this->buildRegularGivingCreatedPayload($order, $options);

        if (empty($url)) {
            $this->logOnly($order->id, 'regular_giving.created', self::URL_NOT_CONFIGURED, $payload, 'Webhook URL not configured');
            return null;
        }

        return $this->postAndLog($order->id, 'regular_giving.created', $url, $payload);
    }

    private function logOnly(int $donationOrderId, string $event, string $url, array $payload, string $errorMessage): void
    {
        DonationWebhookLog::create([
            'donation_order_id' => $donationOrderId,
            'event' => $event,
            'url' => $url,
            'payload' => json_encode($payload),
            'request_headers' => ['Content-Type' => 'application/json'],
            'response_status' => null,
            'response_body' => null,
            'response_headers' => null,
            'duration_ms' => 0,
            'error_message' => $errorMessage,
        ]);
    }

    public function getPreview(DonationOrder $order): array
    {
        $requestHeaders = ['Content-Type' => 'application/json'];
        $donationUrl = config('services.donation_webhook.donation_created_url', '');
        $regularUrl = config('services.donation_webhook.regular_giving_created_url', '');

        $hasRecurring = DonationRecurringSchedule::where('order_id', $order->id)->exists();
        $hasOneTime = false;
        if ($order->relationLoaded('donation_items')) {
            $hasRecurring = $hasRecurring || $order->donation_items->contains(function ($item) {
                return $this->isRecurringCycle($item->cycle ?? null);
            });
            $hasOneTime = $order->donation_items->contains(function ($item) {
                return !$this->isRecurringCycle($item->cycle ?? null);
            });
        }

        $events = [];
        if ($hasRecurring) {
            $regularImportId = 'RG' . $order->id;
            $events[] = [
                'url' => $regularUrl ?: '',
                'event' => 'regular_giving.created',
                'payload' => $this->buildRegularGivingCreatedPayload($order, [
                    'import_id' => $regularImportId,
                    'provider_recurrence_id' => $order->meezan_recurrence_id ?? null,
                ]),
                'request_headers' => $requestHeaders,
            ];
            $events[] = [
                'url' => $donationUrl ?: '',
                'event' => 'donation.created',
                'payload' => $this->buildDonationCreatedPayload($order, [
                    'import_id' => 'DN' . $order->id,
                    'donation_type' => 'regularDonation',
                    'allocation_scope' => 'recurring',
                    'regular_giving_import_id' => $regularImportId,
                    'provider_recurrence_id' => $order->meezan_recurrence_id ?? null,
                ]),
                'request_headers' => $requestHeaders,
            ];
        }

        if ($hasOneTime || !$hasRecurring) {
            $events[] = [
                'url' => $donationUrl ?: '',
                'event' => 'donation.created',
                'payload' => $this->buildDonationCreatedPayload($order, [
                    'import_id' => 'DN' . $order->id . ($hasRecurring && $hasOneTime ? '-OT' : ''),
                    'donation_type' => 'singleDonation',
                    'allocation_scope' => 'one_time',
                ]),
                'request_headers' => $requestHeaders,
            ];
        }

        // Backward compatibility for existing single-preview consumers.
        $first = $events[0] ?? [
            'url' => '',
            'event' => 'donation.created',
            'payload' => [],
            'request_headers' => $requestHeaders,
        ];

        return array_merge($first, ['events' => $events]);
    }

    private function postAndLog(int $donationOrderId, string $event, string $url, array $payload): array
    {
        $start = microtime(true);
        $responseStatus = null;
        $responseBody = null;
        $responseHeaders = null;
        $errorMessage = null;

        $requestHeaders = ['Content-Type' => 'application/json'];

        try {
            $response = Http::timeout(30)
                ->withHeaders($requestHeaders)
                ->post($url, $payload);

            $responseStatus = $response->status();
            $rawBody = $response->body();
            $responseBody = strlen($rawBody) > self::MAX_RESPONSE_BODY_LENGTH
                ? substr($rawBody, 0, self::MAX_RESPONSE_BODY_LENGTH) . '...[truncated]'
                : $rawBody;
            $responseHeaders = $response->headers()->all();
        } catch (\Throwable $e) {
            $responseStatus = 0;
            $errorMessage = $e->getMessage();
            Log::warning('[DonationWebhookService] Webhook request failed', [
                'event' => $event,
                'donation_order_id' => $donationOrderId,
                'url' => $url,
                'error' => $errorMessage,
            ]);
        }

        $durationMs = (int) round((microtime(true) - $start) * 1000);

        DonationWebhookLog::create([
            'donation_order_id' => $donationOrderId,
            'event' => $event,
            'url' => $url,
            'payload' => json_encode($payload),
            'request_headers' => $requestHeaders,
            'response_status' => $responseStatus,
            'response_body' => $responseBody,
            'response_headers' => $responseHeaders,
            'duration_ms' => $durationMs,
            'error_message' => $errorMessage,
        ]);

        return [
            'response_status' => $responseStatus,
            'response_body' => $responseBody,
            'response_headers' => $responseHeaders,
            'error_message' => $errorMessage,
            'duration_ms' => $durationMs,
        ];
    }

    private function buildDonorPayload(DonationOrder $order): array
    {
        $user = $order->donation_user;
        if (!$user) {
            return [
                'title' => '',
                'first_name' => 'Unknown',
                'last_name' => '',
                'email' => '',
                'phone' => '',
                'source' => 'Website',
            ];
        }

        $nameParts = preg_split('/\s+/', trim($user->name), 2);
        $firstName = $nameParts[0] ?? 'Unknown';
        $lastName = $nameParts[1] ?? '';

        $donor = [
            'title' => '',
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $user->email ?? '',
            'phone' => $user->phone_number ?? '',
            'source' => 'Website',
        ];

        if ($user->address || $user->city || $user->postal_code || $user->country) {
            $donor['address'] = [
                'line1' => $user->address ?? '',
                'line2' => '',
                'line3' => '',
                'city' => $user->city ?? '',
                'state' => $user->area ?? '',
                'postalCode' => $user->postal_code ?? '',
                'country' => $user->country ?? 'Pakistan',
            ];
        }

        return $donor;
    }

    private function stipulationFromType(?string $type): string
    {
        if ($type === 'zakat') {
            return 'Zakat';
        }
        return 'Sadaqah';
    }

    private function isRecurringCycle(?string $cycle): bool
    {
        return in_array(strtolower($cycle ?? ''), ['monthly', 'annually', 'daily', 'quarterly']);
    }

    private function buildAllocationsFromItems(DonationOrder $order, string $scope = 'all'): array
    {
        $allocations = [];
        foreach ($order->donation_items as $item) {
            $isRecurring = $this->isRecurringCycle($item->cycle ?? null);
            if ($scope === 'recurring' && !$isRecurring) {
                continue;
            }
            if ($scope === 'one_time' && $isRecurring) {
                continue;
            }

            $amount = (float) ($item->unit_price ?? 0) * max(1, (int) ($item->quantity ?? 1));
            $allocations[] = [
                'product' => $item->item_name ?? 'Donation',
                'allocation_type' => $item->sponsor_type ?? '',
                'stipulation' => $this->stipulationFromType($item->type),
                'currency' => $order->payment_currency ?? 'PKR',
                'amount' => $amount,
            ];
        }
        return $allocations;
    }

    /**
     * Normalize any gateway/callback value to an array for JSON payloads.
     */
    private function normalizeBankResponseValue(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return $decoded;
            }

            return ['raw_body' => $value];
        }
        if ($value !== null && is_scalar($value)) {
            return ['value' => $value];
        }

        return [];
    }

    /**
     * Decode persisted gateway HTTP body / IPN JSON from the donation order.
     */
    private function decodeGatewayResponseFromOrder(DonationOrder $order): array
    {
        $raw = $order->meezan_trans_response ?? null;
        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return $decoded;
            }

            return ['raw_body' => $raw];
        }

        $faysal = $order->faysal_ipn_payload ?? null;
        if (is_string($faysal) && $faysal !== '') {
            $decoded = json_decode($faysal, true);
            if (is_array($decoded)) {
                return $decoded;
            }

            return ['raw_body' => $faysal];
        }

        return [];
    }

    private function minimalBankResponseFallback(DonationOrder $order): array
    {
        return [
            'provider' => $order->payment_method ?? 'unknown',
            'message' => 'No gateway response body is stored for this order.',
            'order_reference' => [
                'donation_order_id' => $order->id,
                'meezan_transaction_id' => $order->meezan_transaction_id,
                'faysal_transaction_id' => $order->faysal_transaction_id,
                'payment_id' => $order->payment_id,
                'meezan_order_id' => $order->meezan_order_id,
                'faysal_order_id' => $order->faysal_order_id,
                'meezan_recurrence_id' => $order->meezan_recurrence_id,
            ],
        ];
    }

    /**
     * bank_response is always present on donation / regular-giving webhooks.
     * Explicit options['bank_response'] wins when provided and non-null; otherwise persisted order data; otherwise a minimal fallback.
     */
    private function buildBankResponsePayload(DonationOrder $order, array $options): array
    {
        $hasExplicit = array_key_exists('bank_response', $options) && $options['bank_response'] !== null;
        if ($hasExplicit) {
            $normalized = $this->normalizeBankResponseValue($options['bank_response']);
            if ($normalized !== []) {
                return $normalized;
            }
        }

        $fromOrder = $this->decodeGatewayResponseFromOrder($order);
        if ($fromOrder !== []) {
            return $fromOrder;
        }

        return $this->minimalBankResponseFallback($order);
    }

    private function buildDonationCreatedPayload(DonationOrder $order, array $options = []): array
    {
        $paymentDate = $order->meezan_payment_date ?? $order->faysal_payment_date ?? $order->updated_at;
        $dateStr = $paymentDate instanceof \DateTimeInterface
            ? Carbon::parse($paymentDate)->format('Y-m-d')
            : date('Y-m-d', strtotime((string) $paymentDate));

        $provider = $order->payment_method === 'faysal_bank' ? 'Faysal Bank' : 'Meezan Bank';
        $transactionId = $options['transaction_id']
            ?? $order->meezan_transaction_id
            ?? $order->faysal_transaction_id
            ?? $order->payment_id
            ?? '';

        $allocationScope = $options['allocation_scope'] ?? 'all';
        $allocations = $this->buildAllocationsFromItems($order, $allocationScope);

        $donationType = $options['donation_type'] ?? 'singleDonation';
        $paymentStatus = $options['payment_status'] ?? 'Paid';
        $providerRecurrenceId = $options['provider_recurrence_id'] ?? $order->meezan_recurrence_id;
        $regularGivingImportId = $options['regular_giving_import_id'] ?? ($providerRecurrenceId ? ('RG' . $order->id) : null);

        $payload = [
            'event' => 'donation.created',
            'id' => $options['import_id'] ?? ('DN' . $order->id),
            'donor' => $this->buildDonorPayload($order),
            'donation' => [
                'type' => $donationType,
                'currency' => $order->payment_currency ?? 'PKR',
                'amount' => (float) $order->grand_total,
                'date' => $dateStr,
                'allocations' => $allocations,
                'payment' => [
                    'method' => 'Card',
                    'provider' => $provider,
                    'currency' => $order->payment_currency ?? 'PKR',
                    'amount' => (float) $order->grand_total,
                    'status' => $paymentStatus,
                    'transactionId' => $transactionId,
                    'order_id' => $options['order_id'] ?? ($order->meezan_order_id ?? $order->faysal_order_id ?? ''),
                ],
            ],
        ];

        $payload['donation']['payment']['bank_response'] = $this->buildBankResponsePayload($order, $options);

        if ($providerRecurrenceId) {
            $payload['donation']['payment']['provider_recurrence_id'] = $providerRecurrenceId;
        }

        if ($donationType === 'regularDonation') {
            $payload['donation']['regularGiving'] = [
                'importId' => $regularGivingImportId,
                'provider_recurrence_id' => $providerRecurrenceId,
            ];
        }

        return $payload;
    }

    private function buildRegularGivingCreatedPayload(DonationOrder $order, array $options = []): array
    {
        $firstSchedule = DonationRecurringSchedule::where('order_id', $order->id)
            ->orderBy('next_due_date')
            ->first();

        $frequency = 'monthly';
        $collectionDay = 1;
        $firstCollectionDate = $order->created_at ? Carbon::parse($order->created_at)->addMonth()->format('Y-m-d') : date('Y-m-d');
        $amount = (float) $order->grand_total;

        if ($firstSchedule) {
            $cycle = $firstSchedule->cycle;
            $frequency = $cycle === 'annually' ? 'yearly' : ($cycle === 'daily' ? 'daily' : ($cycle === 'quarterly' ? 'quarterly' : 'monthly'));
            $firstCollectionDate = Carbon::parse($firstSchedule->next_due_date)->format('Y-m-d');
            $collectionDay = (int) Carbon::parse($firstSchedule->next_due_date)->format('d');
            $amount = (float) $firstSchedule->amount;
        }

        $allocations = $this->buildAllocationsFromItems($order, 'recurring');
        if (empty($allocations)) {
            $allocations = $this->buildAllocationsFromItems($order, 'all');
        }

        $providerRecurrenceId = $options['provider_recurrence_id'] ?? $order->meezan_recurrence_id ?? null;

        $payload = [
            'event' => 'regular_giving.created',
            'source' => 'website',
            'id' => $options['import_id'] ?? ('RG' . $order->id),
            'donor' => $this->buildDonorPayload($order),
            'regularGiving' => [
                'amount' => $amount,
                'currency' => $order->payment_currency ?? 'PKR',
                'frequency' => $frequency,
                'collection_day' => $collectionDay,
                'first_collection_date' => $firstCollectionDate,
                'end_date' => null,
                'provider_recurrence_id' => $providerRecurrenceId,
                'paymentMethod' => [
                    'type' => 'card',
                    'provider' => 'meezan',
                ],
                'allocations' => $allocations,
            ],
        ];

        $payload['regularGiving']['bank_response'] = $this->buildBankResponsePayload($order, $options);

        return $payload;
    }
}

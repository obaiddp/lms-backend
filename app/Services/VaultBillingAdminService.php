<?php

namespace App\Services;

use App\Models\License;
use App\Models\VaultPayment;
use App\Models\VaultPaymentLink;
use App\Models\VaultSubscription;
use App\Models\VaultUser;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VaultBillingAdminService
{
    public function paginateSubscriptions(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $q = VaultSubscription::with(['vaultUser', 'license'])
            ->orderByDesc('id');

        if (! empty($filters['status'])) {
            $q->where('status', $filters['status']);
        }
        if (! empty($filters['vault_user_id'])) {
            $q->where('vault_user_id', (int) $filters['vault_user_id']);
        }
        if (! empty($filters['license_id'])) {
            $q->where('license_id', (int) $filters['license_id']);
        }
        if (! empty($filters['search'])) {
            $s = $filters['search'];
            $q->where(function ($query) use ($s) {
                $query->where('plan_name', 'like', "%{$s}%")
                    ->orWhereHas('vaultUser', fn ($vu) => $vu->where('name', 'like', "%{$s}%"))
                    ->orWhereHas('license', fn ($l) => $l->where('key', 'like', "%{$s}%"));
            });
        }

        return $q->paginate($perPage);
    }

    public function createSubscription(array $data, ?int $adminUserId = null): VaultSubscription
    {
        $this->assertLicenseBelongsToVaultUser((int) $data['license_id'], (int) $data['vault_user_id']);
        $this->assertNoActiveSubscriptionForLicense((int) $data['license_id']);

        return DB::transaction(function () use ($data, $adminUserId) {
            $subscription = VaultSubscription::create([
                'vault_user_id' => $data['vault_user_id'],
                'license_id' => $data['license_id'],
                'plan_name' => $data['plan_name'],
                'amount' => $data['amount'] ?? 0,
                'currency' => $data['currency'] ?? 'PKR',
                'billing_interval' => $data['billing_interval'] ?? 'yearly',
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'status' => $data['status'] ?? 'active',
                'notes' => $data['notes'] ?? null,
                'created_by' => $adminUserId,
            ]);

            $this->syncLicenseDatesFromSubscription($subscription);

            return $subscription->load(['vaultUser', 'license']);
        });
    }

    public function updateSubscription(VaultSubscription $subscription, array $data): VaultSubscription
    {
        if (isset($data['license_id'], $data['vault_user_id'])) {
            $this->assertLicenseBelongsToVaultUser((int) $data['license_id'], (int) $data['vault_user_id']);
        }

        if (isset($data['license_id']) && (int) $data['license_id'] !== (int) $subscription->license_id) {
            $this->assertNoActiveSubscriptionForLicense((int) $data['license_id'], $subscription->id);
        }

        return DB::transaction(function () use ($subscription, $data) {
            $subscription->update([
                'vault_user_id' => $data['vault_user_id'] ?? $subscription->vault_user_id,
                'license_id' => $data['license_id'] ?? $subscription->license_id,
                'plan_name' => $data['plan_name'] ?? $subscription->plan_name,
                'amount' => $data['amount'] ?? $subscription->amount,
                'currency' => $data['currency'] ?? $subscription->currency,
                'billing_interval' => $data['billing_interval'] ?? $subscription->billing_interval,
                'start_date' => $data['start_date'] ?? $subscription->start_date,
                'end_date' => $data['end_date'] ?? $subscription->end_date,
                'status' => $data['status'] ?? $subscription->status,
                'notes' => array_key_exists('notes', $data) ? $data['notes'] : $subscription->notes,
            ]);

            if ($subscription->status === 'active') {
                $this->syncLicenseDatesFromSubscription($subscription->fresh());
            }

            return $subscription->fresh(['vaultUser', 'license']);
        });
    }

    public function updateSubscriptionStatus(VaultSubscription $subscription, string $status): VaultSubscription
    {
        if (! in_array($status, ['active', 'cancelled', 'expired', 'paused'], true)) {
            throw new \InvalidArgumentException('Invalid subscription status.');
        }

        if ($status === 'active') {
            $this->assertNoActiveSubscriptionForLicense((int) $subscription->license_id, $subscription->id);
        }

        $subscription->update(['status' => $status]);

        return $subscription->fresh(['vaultUser', 'license']);
    }

    public function deleteSubscription(VaultSubscription $subscription): void
    {
        if ($subscription->payments()->where('status', 'completed')->exists()) {
            throw new \InvalidArgumentException('Cannot delete subscription with completed payments.');
        }

        $subscription->delete();
    }

    public function paginatePayments(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $q = VaultPayment::with(['vaultUser', 'subscription', 'license', 'recorder'])
            ->orderByDesc('paid_at')
            ->orderByDesc('id');

        if (! empty($filters['status'])) {
            $q->where('status', $filters['status']);
        }
        if (! empty($filters['vault_user_id'])) {
            $q->where('vault_user_id', (int) $filters['vault_user_id']);
        }
        if (! empty($filters['license_id'])) {
            $q->where('license_id', (int) $filters['license_id']);
        }
        if (! empty($filters['vault_subscription_id'])) {
            $q->where('vault_subscription_id', (int) $filters['vault_subscription_id']);
        }
        if (! empty($filters['paid_from'])) {
            $q->whereDate('paid_at', '>=', $filters['paid_from']);
        }
        if (! empty($filters['paid_to'])) {
            $q->whereDate('paid_at', '<=', $filters['paid_to']);
        }
        if (! empty($filters['search'])) {
            $s = $filters['search'];
            $q->where(function ($query) use ($s) {
                $query->where('reference_number', 'like', "%{$s}%")
                    ->orWhereHas('vaultUser', fn ($vu) => $vu->where('name', 'like', "%{$s}%"))
                    ->orWhereHas('license', fn ($l) => $l->where('key', 'like', "%{$s}%"));
            });
        }

        return $q->paginate($perPage);
    }

    public function recordPayment(array $data, ?int $adminUserId = null, ?UploadedFile $receipt = null): VaultPayment
    {
        return DB::transaction(function () use ($data, $adminUserId, $receipt) {
            $receiptPath = null;
            if ($receipt) {
                $receiptPath = $receipt->store('vault-receipts', 'local');
            }

            $payment = VaultPayment::create([
                'vault_user_id' => $data['vault_user_id'],
                'vault_subscription_id' => $data['vault_subscription_id'] ?? null,
                'license_id' => $data['license_id'] ?? null,
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'PKR',
                'paid_at' => $data['paid_at'],
                'method' => $data['method'] ?? 'cash',
                'reference_number' => $data['reference_number'] ?? null,
                'receipt_path' => $receiptPath,
                'period_start' => $data['period_start'] ?? null,
                'period_end' => $data['period_end'] ?? null,
                'status' => $data['status'] ?? 'pending',
                'recorded_by' => $adminUserId,
                'notes' => $data['notes'] ?? null,
            ]);

            if ($payment->status === 'completed') {
                $this->applyCompletedPaymentEffects($payment);
            }

            return $payment->load(['vaultUser', 'subscription', 'license', 'recorder']);
        });
    }

    public function updatePayment(VaultPayment $payment, array $data, ?UploadedFile $receipt = null): VaultPayment
    {
        $previousStatus = $payment->status;

        return DB::transaction(function () use ($payment, $data, $receipt, $previousStatus) {
            $updates = [
                'vault_user_id' => $data['vault_user_id'] ?? $payment->vault_user_id,
                'vault_subscription_id' => array_key_exists('vault_subscription_id', $data)
                    ? $data['vault_subscription_id']
                    : $payment->vault_subscription_id,
                'license_id' => array_key_exists('license_id', $data) ? $data['license_id'] : $payment->license_id,
                'amount' => $data['amount'] ?? $payment->amount,
                'currency' => $data['currency'] ?? $payment->currency,
                'paid_at' => $data['paid_at'] ?? $payment->paid_at,
                'method' => $data['method'] ?? $payment->method,
                'reference_number' => array_key_exists('reference_number', $data)
                    ? $data['reference_number']
                    : $payment->reference_number,
                'period_start' => array_key_exists('period_start', $data) ? $data['period_start'] : $payment->period_start,
                'period_end' => array_key_exists('period_end', $data) ? $data['period_end'] : $payment->period_end,
                'status' => $data['status'] ?? $payment->status,
                'notes' => array_key_exists('notes', $data) ? $data['notes'] : $payment->notes,
            ];

            if ($receipt) {
                if ($payment->receipt_path) {
                    Storage::disk('local')->delete($payment->receipt_path);
                }
                $updates['receipt_path'] = $receipt->store('vault-receipts', 'local');
            }

            $payment->update($updates);
            $payment->refresh();

            if ($previousStatus !== 'completed' && $payment->status === 'completed') {
                $this->applyCompletedPaymentEffects($payment);
            }

            return $payment->load(['vaultUser', 'subscription', 'license', 'recorder']);
        });
    }

    public function updatePaymentStatus(VaultPayment $payment, string $status): VaultPayment
    {
        if (! in_array($status, ['pending', 'completed', 'void'], true)) {
            throw new \InvalidArgumentException('Invalid payment status.');
        }

        $previous = $payment->status;
        $payment->update(['status' => $status]);

        if ($previous !== 'completed' && $status === 'completed') {
            $this->applyCompletedPaymentEffects($payment->fresh());
        }

        return $payment->fresh(['vaultUser', 'subscription', 'license', 'recorder']);
    }

    public function deletePayment(VaultPayment $payment): void
    {
        if ($payment->receipt_path) {
            Storage::disk('local')->delete($payment->receipt_path);
        }
        $payment->delete();
    }

    public function vaultUserBilling(VaultUser $vaultUser): array
    {
        $vaultUser->loadCount('licenses');

        $subscriptions = VaultSubscription::with(['license'])
            ->where('vault_user_id', $vaultUser->id)
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        $payments = VaultPayment::with(['license', 'subscription'])
            ->where('vault_user_id', $vaultUser->id)
            ->orderByDesc('paid_at')
            ->limit(50)
            ->get();

        $licenses = License::where('vault_user_id', $vaultUser->id)
            ->orderByDesc('id')
            ->get(['id', 'key', 'status', 'start_date', 'end_date']);

        return [
            'vault_user' => $vaultUser,
            'licenses' => $licenses,
            'subscriptions' => $subscriptions->map(fn ($s) => $this->serializeSubscription($s)),
            'payments' => $payments->map(fn ($p) => $this->serializePayment($p)),
        ];
    }

    public function licenseBilling(License $license): array
    {
        $license->load(['vaultUser']);

        $subscriptions = VaultSubscription::where('license_id', $license->id)
            ->orderByDesc('id')
            ->get();

        $payments = VaultPayment::where('license_id', $license->id)
            ->orderByDesc('paid_at')
            ->get();

        return [
            'license' => $license,
            'subscriptions' => $subscriptions->map(fn ($s) => $this->serializeSubscription($s)),
            'payments' => $payments->map(fn ($p) => $this->serializePayment($p)),
        ];
    }

    public function billingDashboardStats(): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();

        $activeSubscriptions = VaultSubscription::where('status', 'active')->count();
        $paymentsThisMonth = VaultPayment::where('status', 'completed')
            ->where('paid_at', '>=', $startOfMonth)
            ->sum('amount');
        $pendingPayments = VaultPayment::where('status', 'pending')->count();
        $expiringSoon = VaultSubscription::where('status', 'active')
            ->whereBetween('end_date', [$now->toDateString(), $now->copy()->addDays(30)->toDateString()])
            ->count();

        return [
            'active_subscriptions' => $activeSubscriptions,
            'payments_this_month_total' => (float) $paymentsThisMonth,
            'pending_payments' => $pendingPayments,
            'subscriptions_expiring_soon' => $expiringSoon,
        ];
    }

    /**
     * @return array{licenses:int,subscriptions:int}
     */
    public function expireOverdueLicensesAndSubscriptions(): array
    {
        $today = Carbon::today()->toDateString();
        $licenseCount = 0;
        $subscriptionCount = 0;

        License::query()
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<', $today)
            ->orderBy('id')
            ->chunkById(100, function ($licenses) use (&$licenseCount) {
                foreach ($licenses as $license) {
                    $license->update(['status' => 'inactive']);
                    LicenseUsageLogger::record(
                        $license->id,
                        null,
                        null,
                        LicenseUsageLogger::EVENT_LICENSE_DEACTIVATED,
                        'Auto-expired by scheduled job'
                    );
                    $licenseCount++;
                }
            });

        VaultSubscription::query()
            ->where('status', 'active')
            ->whereDate('end_date', '<', $today)
            ->orderBy('id')
            ->chunkById(100, function ($subscriptions) use (&$subscriptionCount) {
                foreach ($subscriptions as $subscription) {
                    $subscription->update(['status' => 'expired']);
                    $subscriptionCount++;
                }
            });

        return [
            'licenses' => $licenseCount,
            'subscriptions' => $subscriptionCount,
        ];
    }

    public function serializeSubscription(VaultSubscription $subscription): array
    {
        $subscription->loadMissing(['vaultUser', 'license']);

        return [
            'id' => $subscription->id,
            'plan_name' => $subscription->plan_name,
            'amount' => (float) $subscription->amount,
            'currency' => $subscription->currency,
            'billing_interval' => $subscription->billing_interval,
            'start_date' => $subscription->start_date?->format('Y-m-d'),
            'end_date' => $subscription->end_date?->format('Y-m-d'),
            'status' => $subscription->status,
            'notes' => $subscription->notes,
            'vault_user' => $subscription->vaultUser ? [
                'id' => $subscription->vaultUser->id,
                'name' => $subscription->vaultUser->name,
                'email' => $subscription->vaultUser->email,
            ] : null,
            'license' => $subscription->license ? [
                'id' => $subscription->license->id,
                'key' => $subscription->license->key,
                'status' => $subscription->license->status,
            ] : null,
            'created_at' => $subscription->created_at?->toIso8601String(),
        ];
    }

    public function serializePayment(VaultPayment $payment): array
    {
        $payment->loadMissing(['vaultUser', 'subscription', 'license', 'recorder']);

        return [
            'id' => $payment->id,
            'amount' => (float) $payment->amount,
            'currency' => $payment->currency,
            'paid_at' => $payment->paid_at?->format('Y-m-d'),
            'method' => $payment->method,
            'reference_number' => $payment->reference_number,
            'has_receipt' => ! empty($payment->receipt_path),
            'period_start' => $payment->period_start?->format('Y-m-d'),
            'period_end' => $payment->period_end?->format('Y-m-d'),
            'status' => $payment->status,
            'notes' => $payment->notes,
            'vault_user' => $payment->vaultUser ? [
                'id' => $payment->vaultUser->id,
                'name' => $payment->vaultUser->name,
            ] : null,
            'subscription' => $payment->subscription ? [
                'id' => $payment->subscription->id,
                'plan_name' => $payment->subscription->plan_name,
            ] : null,
            'license' => $payment->license ? [
                'id' => $payment->license->id,
                'key' => $payment->license->key,
            ] : null,
            'recorded_by' => $payment->recorder ? [
                'id' => $payment->recorder->id,
                'name' => $payment->recorder->name,
            ] : null,
            'created_at' => $payment->created_at?->toIso8601String(),
        ];
    }

    protected function applyCompletedPaymentEffects(VaultPayment $payment): void
    {
        $license = null;
        if ($payment->license_id) {
            $license = License::find($payment->license_id);
        } elseif ($payment->vault_subscription_id) {
            $sub = VaultSubscription::find($payment->vault_subscription_id);
            $license = $sub ? License::find($sub->license_id) : null;
        }

        if (! $license) {
            return;
        }

        $periodEnd = $payment->period_end
            ? Carbon::parse($payment->period_end)
            : $this->inferPeriodEnd($payment, $license);

        if (! $periodEnd) {
            return;
        }

        $periodStart = $payment->period_start
            ? Carbon::parse($payment->period_start)
            : Carbon::today();

        $license->update([
            'end_date' => $periodEnd->toDateString(),
            'status' => 'active',
        ]);

        if ($payment->vault_subscription_id) {
            VaultSubscription::where('id', $payment->vault_subscription_id)->update([
                'start_date' => $periodStart->toDateString(),
                'end_date' => $periodEnd->toDateString(),
                'status' => 'active',
            ]);
        }

        LicenseUsageLogger::record(
            $license->id,
            null,
            null,
            LicenseUsageLogger::EVENT_LICENSE_ACTIVATED,
            'License extended by vault payment #'.$payment->id
        );
    }

    /**
     * Create a pending payment + public checkout link for cef-dashboard /vault-pay/{token}.
     *
     * @param  array{
     *   amount: float|int|string,
     *   currency?: string,
     *   vault_subscription_id?: int|null,
     *   period_start?: string|null,
     *   period_end?: string|null,
     *   notes?: string|null,
     *   include_offline_pack?: bool,
     *   expires_days?: int
     * }  $data
     */
    public function createPaymentLink(License $license, array $data, ?int $adminUserId = null): VaultPaymentLink
    {
        $license->loadMissing(['vaultUser', 'licenseDevices']);

        if (! $license->vault_user_id) {
            throw new \InvalidArgumentException('License has no vault user.');
        }

        if (strtolower((string) $license->status) === 'revoked') {
            throw new \InvalidArgumentException('Cannot create a payment link for a revoked license.');
        }

        $activeDevices = $license->licenseDevices->whereNull('revoked_at')->count();
        if ($activeDevices < 1) {
            throw new \InvalidArgumentException('Register at least one device before creating a payment link.');
        }

        if (! empty($data['vault_subscription_id'])) {
            $sub = VaultSubscription::find((int) $data['vault_subscription_id']);
            if (! $sub || (int) $sub->license_id !== (int) $license->id) {
                throw new \InvalidArgumentException('Subscription does not belong to this license.');
            }
        }

        $expiresDays = (int) ($data['expires_days'] ?? 30);
        if ($expiresDays < 1 || $expiresDays > 90) {
            $expiresDays = 30;
        }

        return DB::transaction(function () use ($license, $data, $adminUserId, $expiresDays) {
            $payment = VaultPayment::create([
                'vault_user_id' => $license->vault_user_id,
                'vault_subscription_id' => $data['vault_subscription_id'] ?? null,
                'license_id' => $license->id,
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'PKR',
                'paid_at' => Carbon::today()->toDateString(),
                'method' => 'other',
                'reference_number' => null,
                'period_start' => $data['period_start'] ?? null,
                'period_end' => $data['period_end'] ?? null,
                'status' => 'pending',
                'recorded_by' => $adminUserId,
                'notes' => $data['notes'] ?? 'Online payment link',
            ]);

            $link = VaultPaymentLink::create([
                'token' => Str::random(64),
                'vault_payment_id' => $payment->id,
                'license_id' => $license->id,
                'vault_user_id' => $license->vault_user_id,
                'expires_at' => Carbon::now()->addDays($expiresDays),
                'status' => 'open',
                'include_offline_pack' => array_key_exists('include_offline_pack', $data)
                    ? (bool) $data['include_offline_pack']
                    : true,
                'created_by' => $adminUserId,
            ]);

            return $link->load(['payment', 'license', 'vaultUser']);
        });
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listPaymentLinks(License $license): array
    {
        $this->expireStaleLinksForLicense($license->id);

        return VaultPaymentLink::query()
            ->with(['payment', 'offlinePack'])
            ->where('license_id', $license->id)
            ->orderByDesc('id')
            ->get()
            ->map(fn (VaultPaymentLink $link) => $this->serializePaymentLink($link))
            ->all();
    }

    public function revokePaymentLink(VaultPaymentLink $link): VaultPaymentLink
    {
        if ($link->status === 'paid') {
            throw new \InvalidArgumentException('Cannot revoke a paid payment link.');
        }

        $link->update(['status' => 'revoked']);

        $payment = $link->payment;
        if ($payment && $payment->status === 'pending') {
            $payment->update(['status' => 'void']);
        }

        return $link->fresh(['payment', 'license', 'vaultUser', 'offlinePack']);
    }

    public function findOpenLinkByToken(string $token): ?VaultPaymentLink
    {
        $link = VaultPaymentLink::query()
            ->with(['payment', 'license.vaultUser', 'vaultUser', 'offlinePack'])
            ->where('token', $token)
            ->first();

        if (! $link) {
            return null;
        }

        if ($link->status === 'open' && $link->expires_at && $link->expires_at->isPast()) {
            $link->update(['status' => 'expired']);
            $link->refresh();
        }

        return $link;
    }

    public function findLinkById(int $id): ?VaultPaymentLink
    {
        return VaultPaymentLink::query()
            ->with(['payment', 'license.vaultUser', 'vaultUser', 'offlinePack'])
            ->find($id);
    }

    public function findLinkByGatewayReference(string $reference): ?VaultPaymentLink
    {
        return VaultPaymentLink::query()
            ->with(['payment', 'license.vaultUser', 'vaultUser', 'offlinePack'])
            ->where('gateway_reference', $reference)
            ->first();
    }

    public function findLinkByDownloadToken(string $downloadToken): ?VaultPaymentLink
    {
        return VaultPaymentLink::query()
            ->with(['offlinePack', 'payment', 'license'])
            ->where('download_token', $downloadToken)
            ->first();
    }

    /**
     * Idempotent completion after Faysal IPN or Meezan reconcile.
     *
     * @return array{link: VaultPaymentLink, already_paid: bool}
     */
    public function completeVaultOnlinePayment(
        VaultPaymentLink $link,
        string $gateway,
        ?string $gatewayReference = null,
        ?string $transactionRef = null
    ): array {
        return DB::transaction(function () use ($link, $gateway, $gatewayReference, $transactionRef) {
            $link = VaultPaymentLink::query()->lockForUpdate()->findOrFail($link->id);
            $payment = VaultPayment::query()->lockForUpdate()->findOrFail($link->vault_payment_id);

            if ($link->status === 'paid' || $payment->status === 'completed') {
                return [
                    'link' => $link->fresh(['payment', 'license', 'vaultUser', 'offlinePack']),
                    'already_paid' => true,
                ];
            }

            if ($link->status !== 'open') {
                throw new \InvalidArgumentException('Payment link is not open for payment.');
            }

            $payment->update([
                'status' => 'completed',
                'paid_at' => Carbon::today()->toDateString(),
                'method' => $gateway,
                'reference_number' => $transactionRef ?: $gatewayReference ?: $payment->reference_number,
            ]);

            $this->applyCompletedPaymentEffects($payment->fresh());

            $downloadToken = Str::random(64);
            $linkUpdates = [
                'status' => 'paid',
                'gateway' => $gateway,
                'download_token' => $downloadToken,
                'download_expires_at' => Carbon::now()->addDays(7),
            ];
            if ($gatewayReference) {
                $linkUpdates['gateway_reference'] = $gatewayReference;
            }
            $link->update($linkUpdates);

            $link = $link->fresh(['payment', 'license', 'vaultUser', 'offlinePack']);

            if ($link->include_offline_pack) {
                $this->tryQueueOfflinePackAfterPayment($link);
            }

            return [
                'link' => $link->fresh(['payment', 'license', 'vaultUser', 'offlinePack']),
                'already_paid' => false,
            ];
        });
    }

    protected function tryQueueOfflinePackAfterPayment(VaultPaymentLink $link): void
    {
        try {
            $license = License::with(['licenseDevices', 'courses'])->find($link->license_id);
            if (! $license) {
                return;
            }

            $packService = app(VaultOfflinePackService::class);
            $packService->assertCanBuild($license);
            $pack = $packService->queuePack($license, [
                'validity_mode' => 'absolute',
            ], $link->created_by);

            $link->update(['offline_pack_id' => $pack->id]);
        } catch (\Throwable $e) {
            Log::warning('Vault payment link: offline pack not queued after pay', [
                'link_id' => $link->id,
                'license_id' => $link->license_id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function availablePaymentMethods(): array
    {
        $methods = [];

        $faysalOn = ! empty(get_option('faysal_merchant_id'))
            && ! empty(get_option('faysal_secure_key'))
            && (! empty(get_option('faysal_base_url')) || ! empty(env('FAYSAL_BANK_BASE_URL')))
            && (! empty(get_option('faysal_access_token_url')) || ! empty(env('FAYSAL_BANK_ACCESS_TOKEN_URL')));

        if ($faysalOn) {
            $methods[] = 'faysal_bank';
        }

        $meezanOn = ! empty(get_option('meezan_user_name'))
            && ! empty(get_option('meezan_user_password'))
            && ! empty(get_option('meezan_port_address'));

        if ($meezanOn) {
            $methods[] = 'meezan_bank';
        }

        return $methods;
    }

    public function dashboardPayUrl(string $token): string
    {
        $base = rtrim((string) env('CEF_DASHBOARD_URL', ''), '/');
        if ($base === '') {
            $base = rtrim((string) env('APP_URL', ''), '/');
        }

        return $base.'/vault-pay/'.$token;
    }

    public function serializePaymentLink(VaultPaymentLink $link): array
    {
        $link->loadMissing(['payment', 'license', 'vaultUser', 'offlinePack']);

        $pack = $link->offlinePack;
        $packStatus = null;
        if ($pack) {
            $packStatus = [
                'id' => $pack->id,
                'pack_id' => $pack->pack_id,
                'status' => $pack->status,
                'error' => $pack->error,
                'downloadable' => $pack->status === 'ready'
                    && $pack->storage_path
                    && Storage::disk('local')->exists($pack->storage_path),
            ];
        }

        return [
            'id' => $link->id,
            'token' => $link->token,
            'url' => $this->dashboardPayUrl($link->token),
            'status' => $link->status,
            'expires_at' => $link->expires_at?->toIso8601String(),
            'gateway' => $link->gateway,
            'gateway_reference' => $link->gateway_reference,
            'include_offline_pack' => (bool) $link->include_offline_pack,
            'download_token' => $link->status === 'paid' ? $link->download_token : null,
            'download_expires_at' => $link->download_expires_at?->toIso8601String(),
            'offline_pack' => $packStatus,
            'payment' => $link->payment ? $this->serializePayment($link->payment) : null,
            'license' => $link->license ? [
                'id' => $link->license->id,
                'key' => $link->license->key,
                'status' => $link->license->status,
            ] : null,
            'vault_user' => $link->vaultUser ? [
                'id' => $link->vaultUser->id,
                'name' => $link->vaultUser->name,
            ] : null,
            'created_at' => $link->created_at?->toIso8601String(),
        ];
    }

    /**
     * Public checkout summary (no secrets; license key only when paid).
     */
    public function serializePublicLinkSummary(VaultPaymentLink $link): array
    {
        $link->loadMissing(['payment', 'license', 'vaultUser', 'offlinePack']);
        $payment = $link->payment;

        return [
            'token' => $link->token,
            'status' => $link->status,
            'expires_at' => $link->expires_at?->toIso8601String(),
            'holder_name' => $link->vaultUser?->name,
            'amount' => $payment ? (float) $payment->amount : null,
            'currency' => $payment?->currency ?? 'PKR',
            'period_start' => $payment?->period_start?->format('Y-m-d'),
            'period_end' => $payment?->period_end?->format('Y-m-d'),
            'payment_methods' => $link->isOpen() ? $this->availablePaymentMethods() : [],
            'include_offline_pack' => (bool) $link->include_offline_pack,
            'paid' => $link->status === 'paid',
            'license_key' => $link->status === 'paid' ? $link->license?->key : null,
            'license_status' => $link->license?->status,
            'download_token' => $link->status === 'paid' ? $link->download_token : null,
            'download_expires_at' => $link->download_expires_at?->toIso8601String(),
            'offline_pack' => $link->offlinePack ? [
                'status' => $link->offlinePack->status,
                'error' => $link->offlinePack->error,
                'downloadable' => $link->offlinePack->status === 'ready'
                    && $link->offlinePack->storage_path
                    && Storage::disk('local')->exists($link->offlinePack->storage_path),
            ] : null,
        ];
    }

    protected function expireStaleLinksForLicense(int $licenseId): void
    {
        VaultPaymentLink::query()
            ->where('license_id', $licenseId)
            ->where('status', 'open')
            ->where('expires_at', '<', Carbon::now())
            ->update(['status' => 'expired']);
    }

    protected function inferPeriodEnd(VaultPayment $payment, License $license): ?Carbon
    {
        $subscription = $payment->vault_subscription_id
            ? VaultSubscription::find($payment->vault_subscription_id)
            : VaultSubscription::where('license_id', $license->id)->where('status', 'active')->first();

        $interval = $subscription?->billing_interval ?? 'yearly';
        $base = $license->end_date && Carbon::parse($license->end_date)->gte(Carbon::today())
            ? Carbon::parse($license->end_date)
            : Carbon::today();

        return match ($interval) {
            'monthly' => $base->copy()->addMonth(),
            'quarterly' => $base->copy()->addMonths(3),
            'yearly' => $base->copy()->addYear(),
            default => $base->copy()->addYear(),
        };
    }

    protected function syncLicenseDatesFromSubscription(VaultSubscription $subscription): void
    {
        License::where('id', $subscription->license_id)->update([
            'start_date' => $subscription->start_date,
            'end_date' => $subscription->end_date,
        ]);
    }

    protected function assertLicenseBelongsToVaultUser(int $licenseId, int $vaultUserId): void
    {
        $license = License::find($licenseId);
        if (! $license || (int) $license->vault_user_id !== $vaultUserId) {
            throw new \InvalidArgumentException('License does not belong to the selected vault user.');
        }
    }

    protected function assertNoActiveSubscriptionForLicense(int $licenseId, ?int $exceptSubscriptionId = null): void
    {
        $q = VaultSubscription::where('license_id', $licenseId)->where('status', 'active');
        if ($exceptSubscriptionId) {
            $q->where('id', '!=', $exceptSubscriptionId);
        }
        if ($q->exists()) {
            throw new \InvalidArgumentException('This license already has an active subscription.');
        }
    }
}

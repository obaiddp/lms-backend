<?php

namespace App\Services;

use App\Models\VaultPaymentLink;
use Illuminate\Support\Facades\Log;

/**
 * Start Faysal / Meezan checkout for vault payment links (no cart / enrollment).
 */
class VaultOnlineCheckoutService
{
    public function __construct(
        protected VaultBillingAdminService $billing,
        protected MeezanPaymentCompletionService $meezan
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function startCheckout(VaultPaymentLink $link, string $paymentMethod, string $dashboardOrigin): array
    {
        if (! $link->isOpen()) {
            throw new \InvalidArgumentException('This payment link is not available for checkout.');
        }

        if (! in_array($paymentMethod, ['faysal_bank', 'meezan_bank'], true)) {
            throw new \InvalidArgumentException('Unsupported payment method.');
        }

        $methods = $this->billing->availablePaymentMethods();
        if (! in_array($paymentMethod, $methods, true)) {
            throw new \InvalidArgumentException('Selected payment gateway is not configured.');
        }

        $dashboardOrigin = rtrim($dashboardOrigin, '/');

        if ($paymentMethod === 'faysal_bank') {
            return $this->startFaysal($link, $dashboardOrigin);
        }

        return $this->startMeezan($link, $dashboardOrigin);
    }

    /**
     * @return array<string, mixed>
     */
    protected function startFaysal(VaultPaymentLink $link, string $dashboardOrigin): array
    {
        $payment = $link->payment;
        if (! $payment) {
            throw new \InvalidArgumentException('Payment record missing.');
        }

        $baseUrl = get_option('faysal_base_url') ?: env('FAYSAL_BANK_BASE_URL');
        $merchantId = get_option('faysal_merchant_id') ?: env('FAYSAL_BANK_MERCHANT_ID');
        $secureKey = get_option('faysal_secure_key') ?: env('FAYSAL_BANK_SECURE_KEY');
        $currency = get_option('faysal_currency') ?: 'PKR';

        $basketId = 'VAULT-'.$link->id.'-'.time();
        $tranAmount = (int) $payment->amount;

        $token = $this->getFaysalAccessToken($merchantId, $secureKey, $tranAmount, $basketId, $currency);
        if ($token === '') {
            throw new \RuntimeException('Could not obtain Faysal bank access token.');
        }

        $link->update([
            'gateway' => 'faysal_bank',
            'gateway_reference' => $basketId,
        ]);

        $tokenPath = $link->token;
        $successUrl = $dashboardOrigin.'/vault-pay/'.$tokenPath.'/faysal/success';
        $failureUrl = $dashboardOrigin.'/vault-pay/'.$tokenPath.'/faysal/failed';

        $holder = $link->vaultUser;
        $formParametersArray = [
            'CURRENCY_CODE' => $currency,
            'MERCHANT_ID' => $merchantId,
            'TOKEN' => $token,
            'SUCCESS_URL' => $successUrl,
            'FAILURE_URL' => $failureUrl,
            'CANCEL_URL' => $failureUrl,
            'CHECKOUT_URL' => route('checkout_faysal_ipn_vault', ['link_id' => $link->id]),
            'BASKET_ID' => $basketId,
            'SIGNATURE' => uniqid('', true),
            'VERSION' => 'MERCHANT-CART 0.1',
            'PROCCODE' => '00',
            'TRAN_TYPE' => 'ECOMM_PURCHASE',
            'STORE_ID' => '',
            'RECURRING_TXN' => false,
            'CUSTOMER_NAME' => $holder?->name ?? '',
            'MERCHANT_CUSTOMER_ID' => (string) ($holder?->id ?? ''),
            'CUSTOMER_EMAIL_ADDRESS' => $holder?->email ?? '',
            'CUSTOMER_MOBILE_NO' => $holder?->phone ?? '',
            'TXNAMT' => $tranAmount,
            'ORDER_DATE' => date('Y-m-d H:i:s'),
            'TXNDESC' => 'CEF Vault license payment #'.$payment->id,
        ];

        return [
            'payment_method' => 'faysal_bank',
            'frontend_form-action_url' => $baseUrl,
            'form_required_parameters_values' => $formParametersArray,
            'link_id' => $link->id,
            'token' => $link->token,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function startMeezan(VaultPaymentLink $link, string $dashboardOrigin): array
    {
        $payment = $link->payment;
        if (! $payment) {
            throw new \InvalidArgumentException('Payment record missing.');
        }

        $port = rtrim((string) get_option('meezan_port_address'), '/').'/';
        $userName = get_option('meezan_user_name');
        $password = get_option('meezan_user_password');
        $currencyCode = 586; // PKR

        $amount = (int) $payment->amount;
        $amountUpdated = $amount * 100;
        $orderNumber = 'V'.rand(10000, 99999).$link->id.time();

        $returnUrl = $dashboardOrigin.'/vault-pay/'.$link->token.'/success?meezan=1';

        $tokenApiUrl = $port.'register.do';
        $urlPostParams = [
            'userName' => $userName,
            'password' => $password,
            'orderNumber' => $orderNumber,
            'amount' => $amountUpdated,
            'currency' => $currencyCode,
            'returnUrl' => $returnUrl,
        ];
        $query = urldecode(http_build_query($urlPostParams));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $tokenApiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_USERAGENT, 'CURL/PHP Vault Meezan');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        $response = curl_exec($ch);
        $curlErr = curl_error($ch);
        curl_close($ch);

        $arrayResult = $response ? json_decode($response, true) : null;
        if (! is_array($arrayResult)) {
            Log::error('Vault Meezan register failed', ['error' => $curlErr, 'raw' => $response]);
            throw new \RuntimeException('Meezan register failed.');
        }

        if ((int) ($arrayResult['errorCode'] ?? -1) !== 0) {
            throw new \RuntimeException($arrayResult['errorMessage'] ?? 'Meezan register error.');
        }

        $meezanTxnId = $arrayResult['orderId'] ?? null;
        $formUrl = $arrayResult['formUrl'] ?? null;
        if (! $meezanTxnId || ! $formUrl) {
            throw new \RuntimeException('Meezan response missing orderId/formUrl.');
        }

        $link->update([
            'gateway' => 'meezan_bank',
            'gateway_reference' => $orderNumber,
            'meezan_order_id' => $orderNumber,
            'meezan_transaction_id' => $meezanTxnId,
        ]);

        return [
            'payment_method' => 'meezan_bank',
            'meezan_payment_flow_redirect_url' => $formUrl,
            'link_id' => $link->id,
            'token' => $link->token,
        ];
    }

    /**
     * Reconcile Meezan payment for a vault link (return URL / client complete).
     *
     * @return array{result: string, message: string, data: array<string, mixed>|null}
     */
    public function completeMeezan(VaultPaymentLink $link): array
    {
        if ($link->status === 'paid') {
            return [
                'result' => 'already_paid',
                'message' => 'Payment already completed.',
                'data' => $this->billing->serializePublicLinkSummary($link->fresh(['payment', 'license', 'vaultUser', 'offlinePack'])),
            ];
        }

        if (empty($link->meezan_transaction_id)) {
            return [
                'result' => 'error',
                'message' => 'Missing Meezan transaction id. Start checkout again.',
                'data' => null,
            ];
        }

        $statusCall = $this->meezan->getOrderStatusExtended(
            $link->meezan_transaction_id,
            $link->meezan_order_id,
            false
        );

        if (! ($statusCall['ok'] ?? false)) {
            return [
                'result' => 'error',
                'message' => $statusCall['message'] ?? 'Meezan status API error',
                'data' => null,
            ];
        }

        $arrayResult = $statusCall['data'];
        $errorCode = (int) ($arrayResult['errorCode'] ?? -1);
        if ($errorCode !== 0) {
            return [
                'result' => 'declined',
                'message' => $arrayResult['errorMessage'] ?? 'Payment not successful',
                'data' => null,
            ];
        }

        $paymentState = strtolower((string) ($arrayResult['paymentAmountInfo']['paymentState'] ?? $arrayResult['orderStatus'] ?? ''));
        $paidStates = ['deposited', 'approved', 'paid', '2', 'deposit'];
        $isPaid = false;
        foreach ($paidStates as $state) {
            if (str_contains($paymentState, $state) || $paymentState === $state) {
                $isPaid = true;
                break;
            }
        }
        // Meezan often uses orderStatus numeric: 2 = deposited
        if (! $isPaid && isset($arrayResult['orderStatus']) && (int) $arrayResult['orderStatus'] === 2) {
            $isPaid = true;
        }

        if (! $isPaid) {
            return [
                'result' => 'pending',
                'message' => 'Payment not yet completed at the bank.',
                'data' => $this->billing->serializePublicLinkSummary($link),
            ];
        }

        $ref = $arrayResult['authRefNum'] ?? $arrayResult['approvalCode'] ?? $link->meezan_transaction_id;
        $result = $this->billing->completeVaultOnlinePayment(
            $link,
            'meezan_bank',
            $link->gateway_reference,
            is_string($ref) ? $ref : (string) $link->meezan_transaction_id
        );

        return [
            'result' => $result['already_paid'] ? 'already_paid' : 'completed',
            'message' => 'Payment completed.',
            'data' => $this->billing->serializePublicLinkSummary($result['link']),
        ];
    }

    /**
     * Process Faysal IPN for a vault payment link.
     */
    public function handleFaysalIpn(VaultPaymentLink $link, array $payload): array
    {
        $errCode = $payload['err_code'] ?? $payload['ERR_CODE'] ?? null;
        $basketId = $payload['basket_id'] ?? $payload['BASKET_ID'] ?? null;
        $transactionId = $payload['transaction_id'] ?? $payload['TRANSACTION_ID'] ?? $payload['trans_id'] ?? null;

        if ($basketId && $link->gateway_reference && (string) $basketId !== (string) $link->gateway_reference) {
            Log::warning('Vault Faysal IPN basket mismatch', [
                'link_id' => $link->id,
                'expected' => $link->gateway_reference,
                'got' => $basketId,
            ]);
        }

        // PayFast success typically err_code 000 / 00
        $ok = in_array((string) $errCode, ['000', '00', '0'], true)
            || strtolower((string) ($payload['status'] ?? '')) === 'success';

        if (! $ok) {
            return [
                'result' => 'declined',
                'message' => 'Faysal payment not successful',
            ];
        }

        $result = $this->billing->completeVaultOnlinePayment(
            $link,
            'faysal_bank',
            $link->gateway_reference ?: (string) $basketId,
            $transactionId ? (string) $transactionId : null
        );

        return [
            'result' => $result['already_paid'] ? 'already_paid' : 'completed',
            'message' => 'OK',
        ];
    }

    protected function getFaysalAccessToken(
        string $merchantId,
        string $securedKey,
        $transAmount,
        string $basketId,
        ?string $currency = null
    ): string {
        $accessTokenURL = get_option('faysal_access_token_url') ?: env('FAYSAL_BANK_ACCESS_TOKEN_URL');
        $urlPostParams = sprintf(
            'MERCHANT_ID=%s&SECURED_KEY=%s&TXNAMT=%s&BASKET_ID=%s',
            $merchantId,
            $securedKey,
            $transAmount,
            $basketId
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $accessTokenURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $urlPostParams);
        curl_setopt($ch, CURLOPT_USERAGENT, 'CURL/PHP Vault PayFast');
        $response = curl_exec($ch);
        curl_close($ch);

        $payload = json_decode($response);

        return isset($payload->ACCESS_TOKEN) ? (string) $payload->ACCESS_TOKEN : '';
    }
}

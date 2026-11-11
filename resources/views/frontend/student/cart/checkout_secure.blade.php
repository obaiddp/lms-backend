@extends('frontend.layouts.minimal')

@section('content')

<!-- Hidden Inputs -->
<input type="hidden" id="faysalBankURL" value="{{ $faysalBankUrl }}">
<input type="hidden" id="meezanRedirectUrl" value="{{ route('student.meezan.redirect') }}">
<input type="hidden" id="autoPaymentMethod" value="{{ $sessionPaymentMethod }}">
<input type="hidden" name="basketid" value="{{ $paymentCartID }}" />

<!-- Full-Screen Loader -->
<div id="loader" style="
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: #ffffff;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
    text-align: center;
    padding: 20px;
    box-sizing: border-box;
">
    <div id="spinner" style="
        width: 60px;
        height: 60px;
        border: 6px solid #f0f0f0;
        border-top: 6px solid #0056b3;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-bottom: 20px;
    "></div>
    <h3 style="
        font-size: 18px;
        font-weight: 500;
        margin: 0 0 8px 0;
        color: #0056b3;
    ">Secure Checkout</h3>
    <p id="loader-message" style="
        font-size: 15px;
        color: #555;
        margin: 0;
        max-width: 300px;
    ">Processing your payment...</p>
</div>

<!-- Error Message -->
<div id="error-message" style="display:none; text-align:center; padding:50px; font-family:Arial, sans-serif;">
    <h3 style="color: #e74c3c;">Something went wrong</h3>
    <p>Please go back and try again.</p>
    <button onclick="window.history.back()" style="margin-top:10px; padding:8px 16px; background:#3498db; color:white; border:none; border-radius:4px; cursor:pointer;">
        Go Back
    </button>
</div>

<style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<!-- Faysal Auto-Submit Form (Hidden) -->
<form id="faysalAutoForm" action="{{ $faysalBankUrl }}" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="CURRENCY_CODE" value="{{ $faysalCurrency }}"/>
    <input type="hidden" name="MERCHANT_ID" value="{{ $faysalBankMerchantID }}" />
    <input type="hidden" name="TOKEN" value="{{ $faysalBankToken }}" />
    
    <!-- Success/Failure URLs -->
    <input type="hidden" name="SUCCESS_URL" value="{{ route('student.successfulFaysalApp') }}" />
    <input type="hidden" name="FAILURE_URL" value="{{ route('student.pending-payments-app.index') }}" />
    <input type="hidden" name="CANCEL_URL" value="{{ route('student.pending-payments-app.index') }}" />
    
    <!-- IPN Callback -->
    <input type="hidden" name="CHECKOUT_URL" value="{{ route('checkout_faysal_ipn_api', [
        'user_id' => $userInfo->id,
        'payment_type' => $createdByType,
        'cart_id' => null,
        'live_enrollment' => 0
    ]) }}" />
    
    <!-- Basket ID = faysal_order_id -->
    <input type="hidden" name="BASKET_ID" value="{{ $paymentCartID }}" />
    
    <input type="hidden" name="SIGNATURE" value="{{ $randSignature }}" />
    <input type="hidden" name="VERSION" value="MERCHANT-CART 0.1" />
    <input type="hidden" name="PROCCODE" value="00" />
    <input type="hidden" name="TRAN_TYPE" value="ECOMM_PURCHASE" />
    <input type="hidden" name="STORE_ID" value="" />
    <input type="hidden" name="RECURRING_TXN" value="false">
    
    <!-- Customer Info -->
    <input type="hidden" name="CUSTOMER_NAME" value="{{ $userInfo->name }}">
    <input type="hidden" name="MERCHANT_CUSTOMER_ID" value="{{ $userInfo->id }}">
    <input type="hidden" name="CUSTOMER_EMAIL_ADDRESS" value="{{ $userInfo->email }}">
    <input type="hidden" name="CUSTOMER_MOBILE_NO" value="{{ ($userInfo->area_code ?? '') . ($userInfo->mobile_number ?? '') }}">

    <!-- Amount & Description -->
    <input type="hidden" name="TXNAMT" value="{{ $txnAmount }}">
    <input type="hidden" name="ORDER_DATE" value="{{ now()->format('Y-m-d H:i:s') }}">
    <input type="hidden" name="TXNDESC" value="{{ $courseName }} - {{ count($carts) }} Item(s)">
</form>

@endsection


@push('script')
<!-- Pure JavaScript implementation - no jQuery dependency -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const autoMethod = document.getElementById('autoPaymentMethod')?.value;
    const loader = document.getElementById('loader');
    const loaderMessage = document.getElementById('loader-message');
    const errorMessage = document.getElementById('error-message');
    
    // Validate required elements
    if (!autoMethod || !loader || !loaderMessage || !errorMessage) {
        console.error('Critical elements missing');
        if (loader) loader.style.display = 'none';
        if (errorMessage) errorMessage.style.display = 'block';
        return;
    }

    // Handle payment method after brief delay for UX
    setTimeout(() => {
        if (autoMethod === 'faysal_bank') {
            loaderMessage.textContent = 'Connecting to Faysal Bank...';
            const form = document.getElementById('faysalAutoForm');
            if (form) {
                form.submit();
            } else {
                showError('Faysal payment form not found');
            }
        } 
        else if (autoMethod === 'meezan_bank') {
            loaderMessage.textContent = 'Redirecting to Meezan Bank...';
            const redirectUrl = document.getElementById('meezanRedirectUrl')?.value;
            if (redirectUrl) {
                window.location.href = redirectUrl;
            } else {
                showError('Meezan redirect URL not found');
            }
        } 
        else {
            showError(`Unsupported payment method: ${autoMethod}`);
        }
    }, 800);

    function showError(message) {
        console.error(message);
        loader.style.display = 'none';
        errorMessage.style.display = 'block';
        document.querySelector('#error-message p').textContent = message;
    }
});
</script>
@endpush
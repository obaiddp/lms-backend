@extends('frontend.layouts.app')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/css/intlTelInput.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

@section('content')



    <div class="bg-page checkout-page-bg">
        <!-- Page Header Start -->
        <header class="page-banner-header gradient-bg position-relative pt-20">
        </header>
        <div class="courses-crumb course-details-crumb">
            <div class="home-page-container">
                <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="me-2"><svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M11.875 16.25V12.5C11.875 12.3343 11.8092 12.1753 11.6919 12.0581C11.5747 11.9409 11.4158 11.875 11.25 11.875H8.75C8.58424 11.875 8.42527 11.9409 8.30806 12.0581C8.19085 12.1753 8.125 12.3343 8.125 12.5V16.25C8.125 16.4158 8.05915 16.5747 7.94194 16.692C7.82473 16.8092 7.66576 16.875 7.5 16.875H3.75C3.58424 16.875 3.42527 16.8092 3.30806 16.692C3.19085 16.5747 3.125 16.4158 3.125 16.25V9.02345C3.1264 8.93696 3.14509 8.85162 3.17998 8.77245C3.21486 8.69329 3.26523 8.62191 3.32812 8.56252L9.57812 2.88283C9.69334 2.77742 9.84384 2.71896 10 2.71896C10.1562 2.71896 10.3067 2.77742 10.4219 2.88283L16.6719 8.56252C16.7348 8.62191 16.7851 8.69329 16.82 8.77245C16.8549 8.85162 16.8736 8.93696 16.875 9.02345V16.25C16.875 16.4158 16.8092 16.5747 16.6919 16.692C16.5747 16.8092 16.4158 16.875 16.25 16.875H12.5C12.3342 16.875 12.1753 16.8092 12.0581 16.692C11.9408 16.5747 11.875 16.4158 11.875 16.25Z"
                                stroke="#5F6C72" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </li>
                    <li class="breadcrumb-item font-14"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                    <li class="breadcrumb-item font-14" aria-current="page">{{ __('Courses') }}</li>
                    <li class="breadcrumb-item font-14 active" aria-current="page">{{ __($pageTitle) }}</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- Page Header End -->

        <!-- Cart Page Area Start -->
        <section class="checkout-page bg-white">
            <div class="home-page-container pt-10">
                @if ($message = Session::get('error'))
                    <div class="alert alert-danger alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <strong>{{ __('Error!') }}</strong> {{ $message }}
                    </div>
                @endif

                <form method="post" action="{{ route('student.pay') }}" data-cc-on-file="false" id="payment-form"
                    class="require-validation" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="Location" value="{{$Location}}">
                    <input type="hidden" name="LiveEnrollment" value="{{$LiveEnrollment}}">
                    <input type="hidden" name="trialID" value="{{$trialID}}">
                 
                    {{-- <input type="hidden" id="meezanRedirectUrl" value="{{ route('student.meezan.redirect', ['cart_id' => $cartID]) }}"> --}}
                    <input type="hidden" name="createdByType" value="{{$createdByType}}">
                    <input type="hidden" name="cartID" value="{{$cartID}}">
                    <input type="hidden" id="generalURL" name="generalURL" value="{{$generalUrl}}">
                    <input type="hidden" id="faysalBankURL" name="faysalBankURL" value="{{$faysalBankUrl}}">
                    <input type="hidden" class="form-control" name="CURRENCY_CODE" value="PKR"/>
                    <input type="hidden" class="form-control" name="MERCHANT_ID" value="{{$faysalBankMerchantID}}" />
                    <input type="hidden" class="form-control" name="TOKEN" value="{{$faysalBankToken}}" />
                    

                    @if ($Location == 1)
                        {{-- <input type="hidden" class="form-control" name="FAILURE_URL" value="{{ route('course-details',['slug'=>$courseSlug]) }}" />
                        <input type="hidden" class="form-control" name="CANCEL_URL" value=" {{ route('course-details',['slug'=>$courseSlug]) }}" />
                        <input type="hidden" class="form-control" name="SUCCESS_URL" value="{{ route('course-details',['slug'=>$courseSlug]) }}" /> --}}
                        <input type="hidden" class="form-control" name="FAILURE_URL" value="{{ route('main.index') }}" />
                        <input type="hidden" class="form-control" name="CANCEL_URL" value=" {{ route('main.index') }}" />
                        <input type="hidden" class="form-control" name="SUCCESS_URL" value="{{ route('main.index') }}" />
                        {{-- <input type="hidden" class="form-control" name="SUCCESS_URL" value="{{ route('checkout_faysal_ipn_api',['user_id' => $userID, 'payment_type' => $paymentType, 'cart_id'=>$cartID, 'live_enrollment'=> $LiveEnrollment, 'trial_id' => $trialID]) }}" /> --}}
                    @else
                        <input type="hidden" class="form-control" name="FAILURE_URL" value="{{ route('student.pending-payments.index',['cart_id'=>$cartID,'location'=> $Location]) }}" />
                        <input type="hidden" class="form-control" name="CANCEL_URL" value=" {{ route('student.pending-payments.index',['cart_id'=>$cartID,'location'=> $Location]) }}" />
                        <input type="hidden" class="form-control" name="SUCCESS_URL" value="{{ route('student.successfulFaysal',['cart_id'=>$cartID,'location'=> $Location]) }}" />
                    @endif
                   
                    <input type="hidden" class="form-control" name="CHECKOUT_URL" value="{{ route('checkout_faysal_ipn_api',['user_id' => $userID, 'payment_type' => $paymentType, 'cart_id'=>$cartID, 'live_enrollment'=> $LiveEnrollment, 'trial_id' => $trialID]) }}" />
                    <input type="hidden" class="form-control" name="BASKET_ID" value="{{$paymentCartID}}" />
                    {{-- <input type="hidden" class="form-control" name="BASKET_ID" value="{{$cartID}}" /> --}}
                    <input type="hidden" class="form-control" name="SIGNATURE" value="{{$randSignature}}" />
                    <input type="hidden" class="form-control" name="VERSION" value="MERCHANT-CART 0.1" />
                    <input type="hidden" class="form-control" name="PROCCODE" value="00" />
                    <input type="hidden" class="form-control" name="TRAN_TYPE" value='ECOMM_PURCHASE' />
                    <input type="hidden" class="form-control" name="STORE_ID" value='' />
                    <input type="hidden" class="form-control" name="RECURRING_TXN" value="false">
                    <input type="hidden" class="form-control" name="CUSTOMER_NAME" value="{{$userInfo ? $userInfo->name : ''}}">
                    <input type="hidden" class="form-control" name="MERCHANT_CUSTOMER_ID" value="{{$userInfo? $userInfo->id : ''}}">
                    <input type="hidden" class="form-control" name="CUSTOMER_EMAIL_ADDRESS" value="{{$userInfo? $userInfo->email : ''}}" />
                    <input type="hidden" class="form-control" name="CUSTOMER_MOBILE_NO" value="{{$userInfo? $userInfo->area_code.$userInfo->mobile_number : ''}}" />
                    {{-- <input type="hidden" class="form-control" name="TXNAMT" value="{{get_number_format($carts->sum('price'))}}" /> --}}
                    <input type="hidden" class="form-control" name="TXNAMT" value="{{$txnAmount}}" />
                    <input type="hidden" class="form-control" name="ORDER_DATE" value="{{date('Y-m-d H:i:s', time())}}" />
                    <input type="hidden" class="form-control" name="TXNDESC" value="{{$courseName}}" />
                    <div class="row" style="width:100%">
                        <div class="col-lg-8">
                            <div class="checkout-page-left-part ms-3 lg:ms-0">
                                <div class="billing-address-box bg-white rounded-lg !px-4 !py-2 shadow-none">

                                    <h1 class="checkout">{{ __('Contact') }}
                                    </h1>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label
                                                class="label-text-title color-heading font-medium font-16 mb-3">{{ __('First Name') }} <span class="text-danger">*</span></label>
                                            <input type="text" name="first_name" id="first_name" value="{{ $student->first_name }}"
                                                required class="form-control" placeholder="{{ __('First Name') }}">
                                            @if ($errors->has('first_name'))
                                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i>
                                                    {{ $errors->first('first_name') }}</span>
                                            @endif
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label
                                                class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Last Name') }} <span class="text-danger">*</span></label>
                                            <input type="text" name="last_name" id="last_name" value="{{ $student->last_name }}"
                                                required class="form-control" placeholder="{{ __('Last Name') }}">
                                            @if ($errors->has('last_name'))
                                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i>
                                                    {{ $errors->first('last_name') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label
                                                class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                                            <input type="email" name="email" value="{{ $student->user->email }}" id="email"
                                                required class="form-control"
                                                placeholder="{{ __('Email Address') }}">
                                            @if ($errors->has('email'))
                                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i>
                                                    {{ $errors->first('email') }}</span>
                                            @endif
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label
                                                class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Phone') }} <span class="text-danger">*</span></label>
                                            <input type="text" name="phone_number" id="phone_number"
                                                value="{{ $student->phone_number }}" required class="form-control"
                                                placeholder="{{ __('Type your phone number') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="billing-address-box bg-white rounded-lg !px-4 !py-2 shadow-none">
                                    <h1 class="checkout">{{ __('Address') }}
                                    </h1>

                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label
                                                class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Street Address 1') }} <span class="text-danger">*</span></label>
                                            <input type="text" name="address" id="address" value="{{ $student->address }}"
                                                class="form-control" required placeholder="{{ __('Address') }}">
                                            @if ($errors->has('street_address'))
                                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i>
                                                    {{ $errors->first('street_address') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label
                                                class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Street Address 2') }}</label>
                                            <input type="text" name="address" id="address" value="{{ $student->address }}"
                                                class="form-control" placeholder="{{ __('Address') }}">
                                            @if ($errors->has('street_address'))
                                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i>
                                                    {{ $errors->first('street_address') }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label
                                                class="font-medium font-15 color-heading label-text-title">{{ __('Country') }}</label>

                                            @if ($student->country_id && $student->country)
                                                <input type="text" value="{{ $student->country->country_name }}"
                                                    class="form-control" readonly>
                                                <input type="hidden" name="country_id"
                                                    value="{{ $student->country_id }}">
                                            @else
                                                <select name="country_id" id="country_id" class="form-select">
                                                    <option value="">{{ __('Select Country') }}</option>
                                                    @foreach ($countries as $country)
                                                        <option value="{{ $country->id }}"
                                                            @if (old('country_id')) {{ old('country_id') == $country->id ? 'selected' : '' }} @else  {{ $student->country_id == $country->id ? 'selected' : '' }} @endif>
                                                            {{ $country->country_name }}</option>
                                                    @endforeach
                                                </select>
                                            @endif

                                            @if ($errors->has('country_id'))
                                                <span class="text-danger" id="country-error"><i class="fas fa-exclamation-triangle"></i>
                                                    {{ $errors->first('country_id') }}</span>
                                            @endif

                                        </div>
                                        {{-- <div class="col-md-6 mb-3">
                                            <label
                                                class="label-text-title color-heading font-medium font-16 mb-3">{{ __('State') }}</label>

                                            @if ($student->state_id && $student->state)
                                                <input type="text" value="{{ $student->state->name }}"
                                                    class="form-control" readonly>
                                                <input type="hidden" name="state_id" value="{{ $student->state_id }}">
                                            @else
                                                <select name="state_id" id="state_id" class="form-select">
                                                    <option value="">{{ __('Select State') }}</option>
                                                    @if (old('country_id'))
                                                        @foreach ($states as $state)
                                                            <option value="{{ $state->id }}"
                                                                {{ old('state_id') == $state->id ? 'selected' : '' }}>
                                                                {{ $state->name }}</option>
                                                        @endforeach
                                                    @else
                                                        @if ($student->country)
                                                            @foreach ($student->country->states as $selected_state)
                                                                <option value="{{ $selected_state->id }}"
                                                                    {{ $student->state_id == $selected_state->id ? 'selected' : '' }}>
                                                                    {{ $selected_state->name }}</option>
                                                            @endforeach
                                                        @endif
                                                    @endif
                                                </select>
                                            @endif
                                            @if ($errors->has('state_id'))
                                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i>
                                                    {{ $errors->first('state_id') }}</span>
                                            @endif
                                        </div> --}}
                                        <div class="col-md-6 mb-3">
                                       <label class="label-text-title color-heading font-medium font-16 mb-3">
                                           {{ __('State') }}
                                       </label>
                                       <input type="text" 
                                              name="state" 
                                              id="state" 
                                              class="form-control"
                                              value="{{ old('state', $student->state->name ?? $student->state ?? '') }}"
                                              placeholder="{{ __('Enter State') }}">
                                       @if ($errors->has('state'))
                                           <span class="text-danger">
                                               <i class="fas fa-exclamation-triangle"></i>
                                               {{ $errors->first('state') }}
                                           </span>
                                       @endif
                                   </div>
                                    </div>

                                    <div class="row">
                                        
                                        {{-- <div class="col-md-6 mb-3">
                                            <label
                                                class="label-text-title color-heading font-medium font-16 mb-3">{{ __('City') }}</label>

                                            @if ($student->city_id && $student->city)
                                                <input type="text" value="{{ $student->city->name }}"
                                                    class="form-control" readonly>
                                                <input type="hidden" name="city_id" value="{{ $student->city_id }}">
                                            @else
                                                <select name="city_id" id="city_id" class="form-select">
                                                    <option value="">{{ __('Select City') }}</option>
                                                    @if (old('state_id'))
                                                        @foreach ($cities as $city)
                                                            <option value="{{ $city->id }}"
                                                                {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                                                {{ $city->name }}</option>
                                                        @endforeach
                                                    @else
                                                        @if ($student->state)
                                                            @foreach ($student->state->cities as $selected_city)
                                                                <option value="{{ $selected_city->id }}"
                                                                    {{ $student->city_id == $selected_city->id ? 'selected' : '' }}>
                                                                    {{ $selected_city->name }}</option>
                                                            @endforeach
                                                        @endif
                                                    @endif
                                                </select>
                                            @endif
                                            @if ($errors->has('city_id'))
                                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i>
                                                    {{ $errors->first('city_id') }}</span>
                                            @endif
                                        </div> --}}
                                        <div class="col-md-6 mb-3">
                                         <label class="label-text-title color-heading font-medium font-16 mb-3">
                                             {{ __('City') }}
                                         </label>
                                         <input type="text" 
                                                name="city" 
                                                id="city" 
                                                class="form-control"
                                                value="{{ old('city', $student->city->name ?? $student->city ?? '') }}"
                                                placeholder="{{ __('Enter City') }}">
                                         @if ($errors->has('city'))
                                             <span class="text-danger">
                                                 <i class="fas fa-exclamation-triangle"></i>
                                                 {{ $errors->first('city') }}
                                             </span>
                                         @endif
                                     </div>

                                        <div class="col-md-6 mb-3">
                                            <label
                                                class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Postal Code') }}</label>
                                            <input type="text" name="postal_code" id="postal_code"
                                                value="{{ $student->postal_code }}" class="form-control"
                                                placeholder="{{ __('Zip code') }}">
                                        </div>
                                    </div>

                                </div>
                                <div class="payment-method-box bg-white rounded-lg !px-4 !py-2 shadow-none">
                                    <h1 class="checkout">{{ __('Select Payment Method') }}
                                    </h1>

                            @if (isset($paymentType) && $paymentType == 1)
                                @if (get_option('cash_on_status') == 1)
                                    <div class="form-check payment-method-card-box paypal-box mb-15">
                                        <input class="form-check-input" type="radio" name="payment_method"
                                            value="cash_on" {{ old('payment_method') == 'cash_on' ? 'checked' : '' }}
                                            id="cashOnPayment">
                                        <label class="form-check-label" for="cashOnPayment">
                                            <span>
                                                <span class="font-16 color-heading font-medium me-3">{{__('Cash On Delivery')}}</span>
                                            </span>
                                        </label>
                                    </div>
                                @endif
                            @endif

                                @if (get_option('faysal_status') == 1)
                                    <div class="form-check payment-method-card-box paypal-box mb-15">
                                        <input class="form-check-input" type="radio" name="payment_method"
                                            value="faysal_bank" {{ old('payment_method') == 'faysal_bank' ? 'checked' : '' }}
                                            id="faysalBankPayment">
                                       
                                        <label class="form-check-label" for="faysalBankPayment">
                                            <span>
                                                <span class="font-16 color-heading font-medium me-3">{{__('Pay by Debit/Credit Card (1)')}}</span>
                                                {{-- <span class="font-14">{{ __('You will be redirected to the Faysal Bank platform after submitting your order') }}</span> --}}
                                            </span>
                                        
                                        </label>
                                    </div>
                                @endif



                                @if (get_option('meezan_status') == 1)
                                <div class="form-check payment-method-card-box paypal-box mb-15">
                                    <input class="form-check-input" type="radio" name="payment_method"
                                        value="meezan_bank" {{ old('payment_method') == 'meezan_bank' ? 'checked' : '' }}
                                        id="meezanBankPayment">
                                   
                                    <label class="form-check-label" for="meezanBankPayment">
                                        <span>
                                            <span class="font-16 color-heading font-medium me-3">{{__('Pay by Debit/Credit Card (2)')}}</span>
                                            {{-- <span class="font-14">{{ __('You will be redirected to the Meezan Bank platform after submitting your order') }}</span> --}}
                                        </span>
                                   
                                    </label>
                                </div>
                            @endif


                            {{-- <div class="flex flex-wrap gap-2 justify-center mt-4">
                                 <label class="flex items-center justify-center w-[30%] sm:w-[18.2%] h-[3.5rem] sm:h-[4rem] border rounded-md transition-colors border-gray-100 bg-white">
                                    <img alt="Visa" class="h-6 sm:h-7 w-auto !w-18 !h-8" src="data:image/svg+xml,%3csvg%20width='57'%20height='18'%20viewBox='0%200%2057%2018'%20fill='none'%20xmlns='http://www.w3.org/2000/svg'%3e%3cpath%20d='M20.9999%2017.3466L23.8267%200.654328H28.4033L25.5766%2017.3466H20.9999ZM41.8694%201.08585C40.9624%200.747554%2039.5414%200.385254%2037.7668%200.385254C33.2437%200.385254%2030.0577%202.64978%2030.0302%205.89411C30.0051%208.29209%2032.3046%209.62967%2034.0417%2010.4288C35.8231%2011.2454%2036.4222%2011.7693%2036.4135%2012.4991C36.4024%2013.6179%2034.9902%2014.1294%2033.6743%2014.1294C31.8427%2014.1288%2030.8691%2013.8762%2029.3658%2013.2533L28.7761%2012.9873L28.1333%2016.7241C29.2026%2017.1903%2031.18%2017.5945%2033.2326%2017.6149C38.0432%2017.6138%2041.1675%2015.3774%2041.2026%2011.9118C41.2201%2010.0132%2040.0003%208.56818%2037.3596%207.37652C35.7595%206.60531%2034.7796%206.09023%2034.7902%205.30748C34.7902%204.6139%2035.6202%203.872%2037.4116%203.872C38.9083%203.84862%2039.9927%204.17303%2040.838%204.51132L41.2475%204.70386L41.8694%201.08585ZM49.6724%200.654328C48.5758%200.654328%2047.7836%200.989191%2047.3017%202.07593L40.5189%2017.3472H45.3645L46.3066%2014.7893H52.0961L52.6339%2017.3472H56.9418L53.173%200.654797H49.6725L49.6724%200.654328ZM47.5923%2011.4493C47.9703%2010.488%2049.4152%206.7838%2049.4152%206.7838C49.389%206.82761%2049.7913%205.81788%2050.0224%205.19134L50.3321%206.62978C50.3321%206.62978%2051.2076%2010.6145%2051.3914%2011.4497H47.5923V11.4493ZM12.6537%2012.0967L12.2506%209.80832C11.4153%207.1408%208.82292%204.25457%205.91797%202.80848L9.96157%2017.3466H14.8083L22.0771%200.654328H17.2314L12.6537%2012.0967Z'%20fill='%23293688'/%3e%3cpath%20d='M0.941406%200.654297V0.92384C6.68426%202.30507%2010.6713%205.79696%2012.2497%209.80845L10.5902%202.07887C10.3132%201.02252%209.47369%200.691087%208.48033%200.654451L0.941406%200.654297Z'%20fill='%23F7981D'/%3e%3c/svg%3e"></label><label class="flex items-center justify-center w-[30%] sm:w-[18.2%] h-[3.5rem] sm:h-[4rem] border rounded-md transition-colors border-gray-100 bg-white"><img alt="Mastercard" class="h-6 sm:h-7 w-auto !w-22 !h-10" src="/assets/mastercard-Dsa1dl9x.svg"></label><label class="flex items-center justify-center w-[30%] sm:w-[18.2%] h-[3.5rem] sm:h-[4rem] border rounded-md transition-colors border-gray-100 bg-white"><img alt="EasyPaisa" class="h-6 sm:h-7 w-auto !w-20 !h-10" src="/assets/easypaisa-Chk4oDuK.svg">
                                    </label><label class="flex items-center justify-center w-[30%] sm:w-[18.2%] h-[3.5rem] sm:h-[4rem] border rounded-md transition-colors border-gray-100 bg-white"><img alt="JazzCash" class="h-6 sm:h-7 w-auto !w-22 !h-10" src="/assets/jazzcash-DwkyHjkk.svg"></label><label class="flex items-center justify-center w-[30%] sm:w-[18.2%] h-[3.5rem] sm:h-[4rem] border rounded-md transition-colors border-gray-100 bg-white"><img alt="Bank" class="h-6 sm:h-7 w-auto !w-22 !h-10" src="data:image/svg+xml,%3csvg%20width='24'%20height='24'%20viewBox='0%200%2024%2024'%20fill='none'%20xmlns='http://www.w3.org/2000/svg'%3e%3cpath%20d='M10.1959%2011.6533V20.071H13.6883V11.6533H10.1959ZM3.51172%2011.6533V20.071H7.00407V11.6533H3.51172ZM16.9567%2011.6533V20.071H20.4491V11.6533H16.9567Z'%20fill='%23D1DCFF'/%3e%3cpath%20d='M10.1967%2018.6692V20.071H13.689V18.6706L10.1967%2018.6692ZM16.9575%2018.6692V20.071H20.4498V18.6706L16.9575%2018.6692ZM5.65692%2018.6692C5.03187%2018.6706%204.68178%2019.5777%205.0673%2020.071H7.00482V18.6706L5.65692%2018.6692Z'%20fill='%23BFD2FF'/%3e%3cpath%20d='M2.51953%209.69043V11.3771C2.51953%2011.7909%202.85403%2012.1254%203.26931%2012.1254H7.18262C7.5979%2012.1254%207.93381%2011.7909%207.93381%2011.3757V9.69043H2.51953ZM15.9957%209.69043V11.3771C15.9957%2011.7909%2016.3302%2012.1254%2016.7469%2012.1254H20.6588C21.0755%2012.1254%2021.41%2011.7909%2021.41%2011.3757V9.69043H15.9957ZM9.23494%209.69043V11.3771C9.23494%2011.7909%209.56943%2012.1254%209.98472%2012.1254H13.898C14.3147%2012.1254%2014.6492%2011.7909%2014.6492%2011.3757V9.69043H9.23494Z'%20fill='%23666666'/%3e%3cpath%20d='M1.81679%2019.8838H22.0665C22.6901%2019.8838%2023.1919%2020.3855%2023.1919%2021.0077V22.2508C23.1919%2022.8744%2022.6901%2023.3762%2022.0665%2023.3762H1.81679C1.66889%2023.3765%201.52239%2023.3477%201.38568%2023.2913C1.24898%2023.2348%201.12477%2023.152%201.02019%2023.0474C0.915616%2022.9428%200.832735%2022.8186%200.776312%2022.6819C0.719889%2022.5452%200.691036%2022.3987%200.69141%2022.2508V21.0077C0.69141%2020.3841%201.19315%2019.8838%201.81679%2019.8838Z'%20fill='%2355A1FF'/%3e%3cpath%20d='M2.52344%209.70605V10.9363H7.94622V9.70605H2.52344ZM9.25018%209.70605V10.9363H14.673V9.70605H9.25018ZM16.0223%209.70605V10.9363H19.3701C20.2403%2010.9363%2020.1411%209.70605%2019.3701%209.70605H16.0223Z'%20fill='%234D4D4D'/%3e%3cpath%20d='M21.9642%208.39076C21.7898%208.71392%2021.5276%208.73518%2021.2215%208.73943H4.1594C4.02543%208.73943%203.89391%208.77522%203.77845%208.84315C3.66299%208.91109%203.56779%209.00867%203.50274%209.12578C3.43768%209.24288%203.40512%209.37526%203.40843%209.50918C3.41175%209.64311%203.45082%209.77374%203.52159%209.88748H21.3504C22.3043%209.88748%2023.0853%209.18446%2023.21%208.26602C22.7309%207.83089%2022.1782%207.89752%2021.9642%208.39076Z'%20fill='%23488BFF'/%3e%3cpath%20d='M10.6006%201.45977C6.79222%203.60139%203.17798%205.6183%200.693359%207.05266V8.01078C0.693359%209.05112%201.53101%209.88875%202.57135%209.88875H21.3512C22.3916%209.88875%2023.2292%209.05112%2023.2292%208.01078V7.05266C20.0856%205.28522%2017.1913%203.68643%2013.407%201.48387C12.0208%200.683064%2011.9613%200.673145%2010.6006%201.45977Z'%20fill='%2355A1FF'/%3e%3cpath%20d='M11.7407%207.91714C12.7473%207.91714%2013.5634%207.10111%2013.5634%206.09446C13.5634%205.0878%2012.7473%204.27173%2011.7407%204.27173C10.734%204.27173%209.91797%205.0878%209.91797%206.09446C9.91797%207.10111%2010.734%207.91714%2011.7407%207.91714Z'%20fill='%23FFC861'/%3e%3cpath%20d='M12.4216%206.26735C12.3379%206.69539%2011.968%206.80596%2011.6817%206.78895C11.5632%206.77549%2011.4433%206.79042%2011.3317%206.8325C11.2202%206.87459%2011.1202%206.94264%2011.0402%207.031C10.9601%207.11936%2010.9023%207.2255%2010.8714%207.34065C10.8405%207.45581%2010.8374%207.57665%2010.8625%207.69322C11.1605%207.86126%2011.4998%207.94215%2011.8417%207.92666C12.1835%207.91117%2012.5141%207.7999%2012.7957%207.60559C13.0774%207.41127%2013.2987%207.14172%2013.4346%206.82766C13.5704%206.5136%2013.6152%206.16771%2013.564%205.8294C13.0282%205.47931%2012.491%205.83789%2012.4216%206.26735Z'%20fill='%23FFAB61'/%3e%3cpath%20d='M3.75638%2022.2324C3.62307%2022.2329%203.49227%2022.2688%203.37744%2022.3365C3.2626%2022.4043%203.16785%2022.5013%203.10292%2022.6178C3.03799%2022.7342%203.00522%2022.8658%203.00797%2022.9991C3.01072%2023.1324%203.0489%2023.2626%203.11858%2023.3762H22.0686C22.6922%2023.3762%2023.1925%2022.8745%2023.1925%2022.2508V22.2324H3.75638Z'%20fill='%23488BFF'/%3e%3c/svg%3e"></label>
                            </div> --}}
                                    @if (get_option('paypal_status') == 1)
                                        <div class="form-check payment-method-card-box paypal-box mb-15">
                                            <input class="form-check-input" type="radio" name="payment_method"
                                                value="paypal" {{ old('payment_method') == 'paypal' ? 'checked' : '' }}
                                                id="paypalPayment">
                                            <label class="form-check-label" for="paypalPayment">
                                                <span>
                                                    <span class="font-16 color-heading font-medium me-3">PayPal</span>
                                                    <span class="font-14">{{ __('You will be redirected to the PayPal website after submitting your order') }}</span>
                                                </span>
                                                {{-- <span class="payment-card-list">
                                                    <img src="{{ asset('frontend/assets/img/student-profile-img/payment-paypal.png') }}"
                                                        alt="paypal">
                                                </span> --}}
                                            </label>
                                        </div>
                                    @endif

                                    @if (get_option('bank_status') == 1)
                                        <div class="form-check payment-method-card-box other-payment-box pb-0">
                                            <input class="form-check-input" type="radio" name="payment_method"
                                                value="bank" {{ old('payment_method') == 'bank' ? 'checked' : '' }}
                                                id="bankPayment">
                                            <label class="form-check-label" for="bankPayment">
                                                <span class="font-16 color-heading font-medium">{{ __('Bank') }}</span>
                                            </label>
                                            <div class="payment-method-card-info-box">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Name') }}</label>
                                                        <select name="bank_id" id="bank_id" class="form-control">
                                                            <option value="">{{ __('Select One') }}</option>
                                                            @foreach ($banks as $bank)
                                                                <option value="{{ $bank->id }}">
                                                                    {{ $bank->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label
                                                            class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Account Number') }}</label>
                                                        <input type="text" class="form-control account_number"
                                                            id="account_number" readonly>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label
                                                            class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Deposit By') }}</label>
                                                        <input type="text" class="form-control" placeholder="{{ __('Name') }}" readonly
                                                            name="deposit_by" value="{{ auth()->user()->student->name }}">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label
                                                            class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Deposit Slip') }}</label>
                                                        <input type="file" class="form-control" name="deposit_slip">
                                                    </div>
                                                </div>

                                                <div class="form-row row">
                                                    <div class="col-md-12 d-none error form-group">
                                                        <div class="alert-danger alert  bank-error-message">
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    @endif

                                    @if (get_option('stripe_status') == 1)
                                        <div class="form-check payment-method-card-box paypal-box pb-0 mt-30">
                                            <input class="form-check-input" type="radio" name="payment_method"
                                                   value="stripe" {{ old('payment_method') == 'stripe' ? 'checked' : '' }}
                                                   id="stripePayment">
                                            <label class="form-check-label" for="stripePayment">
                                                <span>
                                                    <span class="font-16 color-heading font-medium me-3">Stripe</span>
                                                    <span class="font-14">{{ __('You will be redirected to the stripe website after submitting your order') }}</span>
                                                </span>
                                            </label>
                                        </div>
                                    @endif

                                    @if (get_option('mollie_status') == 1)
                                        <div class="form-check payment-method-card-box other-payment-box pb-0 mt-30">
                                            <input class="form-check-input" type="radio" name="payment_method"
                                                value="mollie" {{ old('payment_method') == 'mollie' ? 'checked' : '' }}
                                                id="molliePayment">
                                            <label class="form-check-label mb-0" for="molliePayment">
                                                <span class="font-16 color-heading font-medium">Mollie</span>
                                            </label>
                                        </div>
                                    @endif

                                    @if (get_option('im_status') == 1)
                                        <div class="form-check payment-method-card-box other-payment-box pb-0 mt-30">
                                            <input class="form-check-input" type="radio" name="payment_method"
                                                value="instamojo"
                                                {{ old('payment_method') == 'instamojo' ? 'checked' : '' }}
                                                id="instamojoPayment">
                                            <label class="form-check-label mb-0" for="instamojoPayment">
                                                <span class="font-16 color-heading font-medium">Instamojo</span>
                                            </label>
                                        </div>
                                    @endif

                                    @if (get_option('razorpay_status') == 1)
                                        <div class="form-check payment-method-card-box other-payment-box pb-0 mt-30">
                                            <input class="form-check-input" type="radio" name="payment_method"
                                                value="razorpay"
                                                {{ old('payment_method') == 'razorpay' ? 'checked' : '' }}
                                                id="razorpayPayment">
                                            <label class="form-check-label mb-0" for="razorpayPayment">
                                                <span class="font-16 color-heading font-medium">Razorpay</span>
                                            </label>
                                        </div>
                                    @endif

                                    @if (get_option('paystack_status') == 1)
                                    <div class="form-check payment-method-card-box other-payment-box pb-0 mt-30">
                                        <input class="form-check-input" type="radio" name="payment_method"
                                               value="paystack"
                                               {{ old('payment_method') == 'paystack' ? 'checked' : '' }}
                                               id="paystackPayment">
                                        <label class="form-check-label mb-0" for="paystackPayment">
                                            <span class="font-16 color-heading font-medium">Paystack</span>
                                        </label>
                                    </div>
                                    @endif

                                    @if (get_option('sslcommerz_status') == 1)
                                        <div class="form-check payment-method-card-box other-payment-box pb-0 mt-30">
                                            <input class="form-check-input" type="radio" name="payment_method"
                                                value="sslcommerz"
                                                {{ old('payment_method') == 'sslcommerz' ? 'checked' : '' }}
                                                id="sslcommerzPayment">
                                            <label class="form-check-label mb-0" for="sslcommerzPayment">
                                                <span class="font-16 color-heading font-medium">SSLCOMMERZ</span>
                                            </label>
                                        </div>
                                    @endif
                                    @if (get_option('mercado_status') == 1)
                                        <div class="form-check payment-method-card-box other-payment-box pb-0 mt-30">
                                            <input class="form-check-input" type="radio" name="payment_method"
                                                value="mercadopago"
                                                {{ old('payment_method') == 'mercadopago' ? 'checked' : '' }}
                                                id="mercadopagoPayment">
                                            <label class="form-check-label mb-0" for="merPayment">
                                                <span class="font-16 color-heading font-medium">MERCADO PAGO</span>
                                            </label>
                                        </div>
                                    @endif
                                    @if (get_option('flutterwave_status') == 1)
                                        <div class="form-check payment-method-card-box other-payment-box pb-0 mt-30">
                                            <input class="form-check-input" type="radio" name="payment_method"
                                                value="flutterwave"
                                                {{ old('payment_method') == 'flutterwave' ? 'checked' : '' }}
                                                id="flutterwavePayment">
                                            <label class="form-check-label mb-0" for="merPayment">
                                                <span class="font-16 color-heading font-medium">Flutterwave</span>
                                            </label>
                                        </div>
                                    @endif
                                    @if (get_option('coinbase_status') == 1)
                                        <div class="form-check payment-method-card-box other-payment-box pb-0 mt-30">
                                            <input class="form-check-input" type="radio" name="payment_method"
                                                value="coinbase"
                                                {{ old('payment_method') == 'coinbase' ? 'checked' : '' }}
                                                id="coinbasePayment">
                                            <label class="form-check-label mb-0" for="coinbasePayment">
                                                <span class="font-16 color-heading font-medium">Coinbase</span>
                                            </label>
                                        </div>
                                    @endif
                                    @if (get_option('zitopay_status') == 1)
                                        <div class="form-check payment-method-card-box other-payment-box pb-0 mt-30">
                                            <input class="form-check-input" type="radio" name="payment_method"
                                                value="zitopay"
                                                {{ old('payment_method') == 'zitopay' ? 'checked' : '' }}
                                                id="zitopayPayment">
                                            <label class="form-check-label mb-0" for="zitopayPayment">
                                                <span class="font-16 color-heading font-medium">Zitopay</span>
                                            </label>
                                        </div>
                                    @endif
                                    @if (get_option('iyzipay_status') == 1)
                                        <div class="form-check payment-method-card-box other-payment-box pb-0 mt-30">
                                            <input class="form-check-input" type="radio" name="payment_method"
                                                value="iyzipay"
                                                {{ old('payment_method') == 'iyzipay' ? 'checked' : '' }}
                                                id="iyzipayPayment">
                                            <label class="form-check-label mb-0" for="iyzipayPayment">
                                                <span class="font-16 color-heading font-medium">Iyzico</span>
                                            </label>
                                        </div>
                                    @endif

                                    @if (get_option('bitpay_status') == 1)
                                        <div class="form-check payment-method-card-box other-payment-box pb-0 mt-30">
                                            <input class="form-check-input" type="radio" name="payment_method"
                                                value="bitpay"
                                                {{ old('payment_method') == 'bitpay' ? 'checked' : '' }}
                                                id="bitpayPayment">
                                            <label class="form-check-label mb-0" for="bitpayPayment">
                                                <span class="font-16 color-heading font-medium">Bitpay</span>
                                            </label>
                                        </div>
                                    @endif

                                    @if (get_option('braintree_status') == 1)
                                        <div class="form-check payment-method-card-box other-payment-box pb-0 mt-30">
                                            <input class="form-check-input" type="radio" name="payment_method"
                                                value="braintree"
                                                {{ old('payment_method') == 'braintree' ? 'checked' : '' }}
                                                id="braintreePayment">
                                            <label class="form-check-label mb-0" for="braintreePayment">
                                                <span class="font-16 color-heading font-medium">Braintree</span>
                                            </label>
                                        </div>
                                    @endif

                                    {{-- <div class="checkout-we-protect-content d-flex align-items-center mt-30">
                                        <div class="flex-shrink-0">
                                            <span class="iconify color-hover font-24"
                                                data-icon="ant-design:lock-filled"></span>
                                        </div>
                                        <div class="flex-grow-1 ms-2 font-13">
                                            {{ __('We protect your payment information using encryption to provide bank-level security') }}
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="checkout-page-right-part ms-3 lg:ms-0">
                                <div class="checkout-right-side-box checkout-order-review-box bg-white rounded-lg !px-4 !py-2 shadow-none">
                                    <h1 class="checkout">Subtotal</h1>

                                    @foreach ($carts as $cart)
                                        <div class="d-flex justify-content-between align-items-center mt-3 border-bottom border-gray-500 pb-3">
                                            <div class="checkout-img">
                                                @if($cart->course_id)
                                                    @if(@$cart->course)
                                                        <a href="{{ route('course-details', @$cart->course->slug) }}"
                                                            target="_blank"><img
                                                                src="{{ getImageFile(@$cart->course->image_path) }}"
                                                                alt="course"
                                                                class="img-fluid"></a>
                                                    @endif
                                                @elseif($cart->bundle_id)
                                                    @if(@$cart->bundle)
                                                        <a href="{{ route('bundle-details', [@$cart->bundle->slug]) }}"
                                                            target="_blank"><img
                                                                src="{{ getImageFile(@$cart->bundle->image) }}"
                                                                alt="bundle course"
                                                                class="img-fluid"></a>
                                                    @endif
                                                @elseif($cart->consultation_slot_id)
                                                    @if(@$cart->consultationSlot)
                                                    <a href="{{ route('userProfile', [@$cart->consultationSlot->user->id]) }}"
                                                        target="_blank"><img
                                                            src="{{ getImageFile(@$cart->consultationSlot->user->image) }}"
                                                            alt="bundle course"
                                                            class="img-fluid"></a>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="w-100">
                                                <h1 class="font-16 font-medium aileron text-black">
                                                    @if($cart->course_id)
                                                        @if(@$cart->course)
                                                        <a href="{{ route('course-details', @$cart->course->slug) }}" target="_blank">
                                                            {{ @$cart->course->title }}</a>
                                                        @endif
                                                    @elseif($cart->bundle_id)
                                                        @if(@$cart->bundle)
                                                        <a href="{{ route('bundle-details', [@$cart->bundle->slug]) }}"
                                                            target="_blank">{{ @$cart->bundle->name }}</a>
                                                        @endif
                                                    @elseif($cart->consultation_slot_id)
                                                        @if(@$cart->consultationSlot)
                                                        <a href="{{ route('userProfile', [@$cart->consultationSlot->user->id]) }}"
                                                            target="_blank">{{ $cart->consultationSlot->user->name }}</a>
                                                        @endif
                                                    @endif
                                                </h1>
                                                @if ($cart->course && $cart->course->instructor)
                                                    <p class="card-text instructor-name-certificate font-medium font-12">
                                                        {{ $cart->course->instructor->name }}
                                                    </p>
                                                @elseif($cart->bundle && $cart->bundle->user->instructor)
                                                    <p class="card-text instructor-name-certificate font-medium font-12">
                                                        {{ $cart->bundle->user->instructor->name }}
                                                    </p>
                                                @elseif($cart->consultation_slot_id)
                                                    <p class="card-text instructor-name-certificate font-medium font-12">
                                                        (Consultation)
                                                    </p>
                                                @endif
                                                <div class="d-flex align-items-center justify-content-between gap-2 cart-quantity-checkout mt-2">
                                                    <div>
                                                        <h1 class="font-16 font-bold aileron text-black">
                                                            @if (get_currency_placement() == 'after')
                                                                {{ get_number_format(@$cart->price, 2) }}
                                                                {{ get_currency_symbol() }}
                                                            @else
                                                                {{ get_currency_symbol() }}
                                                                {{ get_number_format(@$cart->price, 2) }}
                                                            @endif
                                                        </h1>
                                                    </div>
                                                    <div>
                                                        <h1 class="font-16 font-bold aileron text-black">x1</h1>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                        <div class="mt-4 border-bottom border-gray-500 pb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="font-16 font-medium text-black">Subtotal</span>
                                                <span class="font-16 font-medium text-black">
                                                    @if (get_currency_placement() == 'after')
                                                        {{ get_number_format($carts->sum('price')) }}
                                                        {{ get_currency_symbol() }}
                                                    @else
                                                        {{ get_currency_symbol() }}
                                                        {{ get_number_format($carts->sum('price')) }}
                                                    @endif
                                                </span>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="font-16 font-medium text-black">Delivery cost</span>
                                                <span class="font-16 font-medium text-black"></span>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="font-16 font-medium text-black">Tax (GST)</span>
                                                <span class="font-16 font-medium text-black"></span>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <span class="font-16 font-medium text-black">Discount</span>
                                                <span class="font-16 font-medium text-black">
                                                    @if (get_currency_placement() == 'after')
                                                        {{ get_number_format($carts->sum('discount')) }}
                                                        {{ get_currency_symbol() }}
                                                    @else
                                                        {{ get_currency_symbol() }}
                                                        {{ get_number_format($carts->sum('discount')) }}
                                                    @endif
                                                </span>
                                            </div>
                                            {{-- <div class="promo-code-container">
                                                <input type="text" class="form-control" placeholder="Promo code">
                                                <button class="btn btn-primary promo-code-btn">Apply</button>
                                            </div> --}}
                                        </div>
                                        <div class="mt-4">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="font-16 font-bold text-black">Grand Total</span>
                                                <span class="font-16 font-bold text-black">
                                                    @if (get_currency_placement() == 'after')
                                                        <span
                                                            class="grand_total">{{ get_number_format($carts->sum('price'))}}</span>
                                                        {{ get_currency_symbol() }}
                                                    @else
                                                        {{ get_currency_symbol() }} <span
                                                            class="grand_total">{{ get_number_format($carts->sum('price'))}}</span>
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center ">
                                                <span class="font-16 font-medium text-black">{{ __('Conversion Rate') }}</span>
                                                <span class="font-16 font-medium text-black">
                                                    1 {{ get_currency_code() }} =
                                                    <span class="selected_conversation_rate">?</span> <span
                                                        class="selected_currency"></span>
                                                </span>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="font-16 font-medium text-black">In</span>
                                                <span class="font-16 font-medium text-black"></span>
                                            </div>

                                            <button type="submit" class="btn btn-primary w-100 mt-3 checkout-btn">
                                                Checkout
                                            </button>

                                            <div class="d-flex align-items-center gap-2 mt-3">
                                                <input required class="form-check-input terms-checkbox" type="checkbox" value="" id="terms">
                                                <label class="form-check-label terms-label" for="terms">
                                                    By continuing, I confirm that I have read and accept the <a href="{{ route('privacy-policy') }}" class="terms-link">Privacy
                                                                            & Terms Policy</a>
                                                </label>
                                            </div>
                                        </div>
                                </div>

                                {{-- <div class="checkout-right-side-box checkout-billing-summary-box">

                                    <div class="accordion" id="accordionExample3">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingThree">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapseThree" aria-expanded="true"
                                                    aria-controls="collapseThree">
                                                    {{ __('Billing Summary') }}
                                                </button>
                                            </h2>
                                            <div id="collapseThree" class="accordion-collapse collapse show"
                                                aria-labelledby="headingThree" data-bs-parent="#accordionExample3">
                                                <div class="accordion-body">
                                                    <table class="table billing-summary-table">
                                                        <tbody>
                                                            <tr>
                                                                <td>{{ __('Subtotal') }}</td>
                                                                <td>
                                                                    

                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ __('Discount') }}</td>
                                                                <td>-

                                                                    

                                                                </td>
                                                            </tr>
                                                            {{-- @if($carts->sum('shipping_charge') > 0)
                                                            <tr>
                                                                <td>{{ __('Shipping Charge') }} </td>
                                                                <td>
                                                                    @if (get_currency_placement() == 'after')
                                                                        {{ get_number_format($carts->sum('shipping_charge')) }}
                                                                        {{ get_currency_symbol() }}
                                                                    @else
                                                                        {{ get_currency_symbol() }}
                                                                        {{ get_number_format($carts->sum('shipping_charge')) }}
                                                                    @endif
                                                                </td>
                                                            </tr>

                                                            @endif --}}
                                                            {{-- <tr>
                                                                <td>{{ __('Platform Charge') }} </td>
                                                                <td>
                                                                    @if (get_currency_placement() == 'after')
                                                                        {{ get_platform_charge($carts->sum('price')+$carts->sum('shipping_charge')) }}
                                                                        {{ get_currency_symbol() }}
                                                                    @else
                                                                        {{ get_currency_symbol() }}
                                                                        {{ get_platform_charge($carts->sum('price')+$carts->sum('shipping_charge')) }}
                                                                    @endif
                                                                </td>
                                                            </tr> 

                                                            <tr>
                                                                <th scope="col">{{ __('Grand Total') }}</th>
                                                                <th scope="col">
                                                                    @if (get_currency_placement() == 'after')
                                                                        <span
                                                                            class="grand_total">{{ get_number_format($carts->sum('price') + $carts->sum('shipping_charge') + get_platform_charge($carts->sum('shipping_charge')+$carts->sum('price'))) }}</span>
                                                                        {{ get_currency_symbol() }}
                                                                    @else
                                                                        {{ get_currency_symbol() }} <span
                                                                            class="grand_total">{{ get_number_format($carts->sum('price') + $carts->sum('shipping_charge') + get_platform_charge($carts->sum('shipping_charge')+$carts->sum('price'))) }}</span>
                                                                    @endif
                                                                </th>
                                                            </tr> 

                                                            <tr>
                                                                <th scope="col">{{ __('Grand Total') }}</th>
                                                                <th scope="col">
                                                                    
                                                                </th>
                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                    <table class="table billing-summary-table">
                                                        <tbody>
                                                            <tr>
                                                                <td>{ </td>
                                                                <td>
                                                                    
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <th scope="col">In<span
                                                                        class="ms-1 gateway_calculated_rate_currency"></span>
                                                                </th>
                                                                <th scope="col" class="gateway_calculated_rate_price">
                                                                </th>
                                                            </tr>
                                                        </tbody>
                                                    </table>

                                                    <div class="row mb-3">
                                                        <div class="col-md-12">
                                                            <div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        value="" id="flexCheckChecked" checked>
                                                                    <label class="form-check-label mb-0"
                                                                        for="flexCheckChecked">
                                                                        Please check to acknowledge our 
                                                                        <a
                                                                            href=""
                                                                            class="color-hover text-decoration-underline"></a>
                                                                    </label>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        @if (env('APP_DEMO') == 'active')
                                                            <div class="col-md-12">
                                                                <div class="">
                                                                    <button type="button"
                                                                        class="theme-btn theme-button1 theme-button3 font-15 fw-bold w-100 appDemo">
                                                                        {{ __('Pay') }}
                                                                        <span
                                                                            class="ms-1  gateway_calculated_rate_price"></span><span
                                                                            class="ms-1 gateway_calculated_rate_currency"></span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="col-md-12">
                                                                {{-- <div class="sslcz-btn d-none">
                                                                    <input type="hidden" name="">
                                                                    <button
                                                                        class="your-button-class theme-btn theme-button1 theme-button3 font-15 fw-bold w-100"
                                                                        id="sslczPayBtn"
                                                                       token="if you have any token validation"
                                                                        postdata="your javascript arrays or objects which requires in backend"
                                                                       order="If you already have the transaction generated for current order"
                                                                    endpoint="/student/pay-via-sslcommerz"
                                                                    >
                                                                        {{ __('Pay') }}
                                                                        {{ @$sslcommerz_grand_total_with_conversion_rate }}
                                                                        {{ get_option('sslcommerz_currency') }}
                                                                    </button>
                                                                </div> 

                                                                <div id="regular-btn" class="regular-btn">
                                                                    <button type="submit"
                                                                        class="theme-btn theme-button1 theme-button3 font-15 fw-bold w-100 paymentBtn">
                                                                        {{ __('Pay') }}
                                                                        <span
                                                                            class="ms-1  gateway_calculated_rate_price"></span><span
                                                                            class="ms-1 gateway_calculated_rate_currency"></span>
                                                                    </button>
                                                                    {{-- <button type="button"
                                                                    class="theme-btn theme-button1 theme-button3 font-15 fw-bold w-100 paymentBtn">
                                                                    {{ __('Pay') }}
                                                                    <span
                                                                        class="ms-1  gateway_calculated_rate_price"></span><span
                                                                        class="ms-1 gateway_calculated_rate_currency"></span>
                                                                    </button> 
                                                                </div>


                                                                <input type="text" name="" id="" value="{{$faysalBankUrl}}">
                                                            <div id="faysal-bank-btn" class="faysal-bank-btn d-none">
                                                                <form id="faysalPaymentForm_{{$cartID}}" action="{{$faysalBankUrl}}" method="post">

                                                              
                                                                    <button type="submit"
                                                                        class="theme-btn theme-button1 theme-button3 font-15 fw-bold w-100 ">
                                                                        {{ __('Pay Faysal') }}
                                                                        <span
                                                                            class="ms-1  gateway_calculated_rate_price"></span><span
                                                                            class="ms-1 gateway_calculated_rate_currency"></span>
                                                                    </button>
                                                                </form>
                                                            </div> 

                                                            </div>
                                                        @endif
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </form>

                {{-- @php
                    $razorpay_pay_amount = $razorpay_grand_total_with_conversion_rate * 100;
                    $orderId = rand();
                @endphp --}}

                {{-- <div class="d-none">
                    <form action="{{ route('student.razorpay_payment') }}" method="POST" id="razorpay_payment">
                        @csrf
                        <script src="https://checkout.razorpay.com/v1/checkout.js" data-key="{{ env('RAZORPAY_KEY') }}"
                            data-amount="{{ $razorpay_pay_amount }}" data-buttontext="Pay" data-name="{{ get_option('app_name') }}"
                            data-description="Buy Course" data-prefill.name="name" data-prefill.email="email" data-theme.color="#0000FF">
                        </script>
                    </form>
                </div> --}}

                {{-- <div class="d-none">
                    <form action="{{ route('student.pay') }}" method="POST" id="paystack_payment">
                        @csrf
                        <input type="hidden" name="callback_url" value="{{ route('student.paystack_payment.callback') }}">
                        <input type="hidden" name="orderID" value="{{ $orderId }}">
                        <input type="hidden" name="metadata" value="{{ json_encode($array = ['orderID' => $orderId]) }}" >

                        <input type="hidden" name="email" value="{{Auth::user()->email}}">
                        <input type="hidden" name="amount" value="{{$paystack_grand_total_with_conversion_rate * 100}}"> 
                        <input type="hidden" name="currency" value="NGN">
                        <input type="hidden" name="reference" value="{{ Paystack::genTranxRef() }}">
                        <input type="hidden" name="payment_method" value="paystack">
                        {{ csrf_field() }}
                    </form>
                </div> --}}

            </div>
        </section>
        <!-- Cart Page Area End -->
    </div>

    <input type="hidden" class="paypal_currency" value="{{ get_option('paypal_currency') }}">
    <input type="hidden" class="paypal_conversion_rate" value="{{ get_option('paypal_conversion_rate') }}">

    <input type="hidden" class="bank_currency" value="{{ get_option('bank_currency') }}">
    <input type="hidden" class="bank_conversion_rate" value="{{ get_option('bank_conversion_rate') }}">

    <input type="hidden" class="stripe_currency" value="{{ get_option('stripe_currency') }}">
    <input type="hidden" class="stripe_conversion_rate" value="{{ get_option('stripe_conversion_rate') }}">

    <input type="hidden" class="faysal_bank_currency" value="{{ get_option('faysal_currency') }}">
    <input type="hidden" class="faysal_bank_conversion_rate" value="{{ get_option('faysal_conversion_rate') }}">


    {{-- <input type="hidden" class="faysal_bank_currency" value="{{ get_option('faysal_currency') }}">
    <input type="hidden" class="faysal_bank_conversion_rate" value="{{ get_option('faysal_conversion_rate') }}"> --}}


    <input type="hidden" class="meezan_bank_currency" value="{{ get_option('meezan_currency') }}">
    <input type="hidden" class="meezan_bank_conversion_rate" value="{{ get_option('meezan_conversion_rate') }}">

    <input type="hidden" class="razorpay_currency" value="{{ get_option('razorpay_currency') }}">
    <input type="hidden" class="razorpay_conversion_rate" value="{{ get_option('razorpay_conversion_rate') }}">

    <input type="hidden" class="instamojo_currency" value="{{ get_option('im_currency') }}">
    <input type="hidden" class="instamojo_conversion_rate" value="{{ get_option('im_conversion_rate') }}">

    <input type="hidden" class="mollie_currency" value="{{ get_option('mollie_currency') }}">
    <input type="hidden" class="mollie_conversion_rate" value="{{ get_option('mollie_conversion_rate') }}">

    <input type="hidden" class="sslcommerz_currency" value="{{ get_option('sslcommerz_currency') }}">
    <input type="hidden" class="sslcommerz_conversion_rate" value="{{ get_option('sslcommerz_conversion_rate') }}">

    <input type="hidden" class="mercado_currency" value="{{ get_option('mercado_currency') }}">
    <input type="hidden" class="mercado_conversion_rate" value="{{ get_option('mercado_conversion_rate') }}">

    <input type="hidden" class="flutterwave_currency" value="{{ get_option('flutterwave_currency') }}">
    <input type="hidden" class="flutterwave_conversion_rate" value="{{ get_option('flutterwave_conversion_rate') }}">

    <input type="hidden" class="paystack_currency" value="{{ get_option('paystack_currency') }}">
    <input type="hidden" class="paystack_conversion_rate" value="{{ get_option('paystack_conversion_rate') }}">

    <input type="hidden" class="coinbase_currency" value="{{ get_option('coinbase_currency') }}">
    <input type="hidden" class="coinbase_conversion_rate" value="{{ get_option('coinbase_conversion_rate') }}">

    <input type="hidden" class="zitopay_currency" value="{{ get_option('zitopay_currency') }}">
    <input type="hidden" class="zitopay_conversion_rate" value="{{ get_option('zitopay_conversion_rate') }}">

    <input type="hidden" class="iyzipay_currency" value="{{ get_option('iyzipay_currency') }}">
    <input type="hidden" class="iyzipay_conversion_rate" value="{{ get_option('iyzipay_conversion_rate') }}">

    <input type="hidden" class="bitpay_currency" value="{{ get_option('bitpay_currency') }}">
    <input type="hidden" class="bitpay_conversion_rate" value="{{ get_option('bitpay_conversion_rate') }}">

    <input type="hidden" class="braintree_currency" value="{{ get_option('braintree_currency') }}">
    <input type="hidden" class="braintree_conversion_rate" value="{{ get_option('braintree_conversion_rate') }}">

    <input type="hidden" class="fetchBankRoute" value="{{ route('student.fetchBank') }}">
    

@endsection

@push('script')


<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/intlTelInput.min.js"></script>

<script src="{{ asset('frontend/assets/vendor/jquery/jquery-3.6.0.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize Select2 for country
        if($.fn.select2) {
            $('#country_id').select2({
                placeholder: "Select a Country",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#country_id').parent(),
                containerCssClass: "form-control",
                dropdownCssClass: "select2-dropdown"
            });

            // Initialize Select2 for state
            $('#state_id').select2({
                placeholder: "Select a State",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#state_id').parent(),
                containerCssClass: "form-control",
                dropdownCssClass: "select2-dropdown"
            });

            // Initialize Select2 for city
            $('#city_id').select2({
                placeholder: "Select a City",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#city_id').parent(),
                containerCssClass: "form-control",
                dropdownCssClass: "select2-dropdown"
            });
        }
    });
</script>

<style>
    .select2-container {
        width: 100% !important;
    }
    .select2-container .select2-selection--single {
        height: 38px;
        border: 1px solid #ced4da;
        border-radius: 4px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
        padding-left: 12px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .select2-dropdown {
        border: 1px solid #ced4da;
    }
</style>

<script>
    const input = document.querySelector("#phone_number");
    window.intlTelInput(input, {
        loadUtils: () => import("https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/utils.js"),
    });
</script>

<script type="text/javascript" async>

$(document).ready(function () {
    // $('input[type=radio][name=payment_method]').trigger('change');
    // $(".paymentBtn").on('click', function () {
    //     $('input[type=radio][name=payment_method]').trigger('change');
    //     var paymentCartid = $( "input[name='BASKET_ID']" ).val();
    //     var selectedValue = $( "input[name='payment_method']:checked" ).val();
    //     if ( selectedValue == 'faysal_bank')
    //     {
         
    //     }
    //     else{
    //         var newActionUrl = $( "#generalURL").val();
	// 		$( "#payment-form").attr( "action", newActionUrl );
    //         $("#payment-form").attr( "enctype","multipart/form-data");
	// 		$( "#payment-form").submit();
    //     }
     
      
    // });
});
</script>

    @if (get_option('sslcommerz_mode') == 'live')
        <script async>
            var obj = {};
            obj.cus_name = $('#first_name').val() + $('#last_name').val();
            obj.cus_phone = $('#phone_number').val();
            obj.cus_email = $('#email').val();
            obj.cus_addr1 = $('#address').val();
            obj.postal_code = $('#postal_code').val();

            $('#sslczPayBtn').prop('postdata', obj);
            (function(window, document) {
                var loader = function() {
                    var script = document.createElement("script"),
                        tag = document.getElementsByTagName("script")[0];
                    script.src = "https://seamless-epay.sslcommerz.com/embed.min.js?" + Math.random().toString(36)
                        .substring(7); // USE THIS FOR LIVE
                    tag.parentNode.insertBefore(script, tag);
                };
                window.addEventListener ? window.addEventListener("load", loader, false) : window.attachEvent("onload",
                    loader);
            })(window, document);
        </script>
    @else
        <script async>
            var obj = {};
            obj.cus_name = $('#first_name').val() + $('#last_name').val();
            obj.cus_phone = $('#phone_number').val();
            obj.cus_email = $('#email').val();
            obj.cus_addr1 = $('#address').val();
            obj.postal_code = $('#postal_code').val();
            obj.country_name = $('#country_name').val();

            // $('#sslczPayBtn').prop('postdata', obj);
            // (function(window, document) {
            //     var loader = function() {
            //         var script = document.createElement("script"),
            //             tag = document.getElementsByTagName("script")[0];
            //         script.src = "https://sandbox.sslcommerz.com/embed.min.js?" + Math.random().toString(36).substring(
            //             7); // USE THIS FOR SANDBOX
            //         tag.parentNode.insertBefore(script, tag);
            //     };
            //     console.log(loader);
            //     window.addEventListener ? window.addEventListener("load", loader, false) : window.attachEvent("onload",
            //         loader);
            // })(window, document);
        </script>
    @endif

    <script src="{{ asset('frontend/assets/js/custom/student-profile.js') }}" async></script>
    <script src="{{ asset('frontend/assets/js/custom/checkout.js') }}" async></script>
@endpush

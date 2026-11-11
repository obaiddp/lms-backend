<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Application Settings')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a
                                            href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__(@$title)); ?></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-4">
                    <?php echo $__env->make('admin.application_settings.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
                <div class="col-lg-9 col-md-8">
                    <div class="email-inbox__area bg-style admin-dashboard-payment-method">
                        <div class="item-top mb-30">
                            <h2><?php echo e(__(@$title)); ?></h2>
                        </div>
                        <form action="<?php echo e(route('settings.save.setting')); ?>" method="post" class="form-horizontal"
                              enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>

                            <div class="row justify-content-center p-3 pb-0">
                                <div
                                    class="admin-dashboard-payment-title-left col-6 border border-bottom-0 pr-4 text-center">
                                    <h5 class="p-2"><?php echo e(__('PayPal')); ?></h5>
                                </div>
                                <div
                                    class="admin-dashboard-payment-title-right col-6 border border-bottom-0 pl-4 text-center">
                                    <h5 class="p-2"><?php echo e(__('Stripe')); ?></h5>
                                </div>
                            </div>

                            <div class="row justify-content-center px-3 pb-0 mb-3">

                                <div class="payment-getaway admin-dashboard-payment-content-box-left col-md-6 border p-3">
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Currency ISO Code')); ?> </label>
                                                <select  name="paypal_currency" class="form-control paypal_currency currency">
                                                    <?php $__currentLoopData = getCurrency(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($code); ?>" <?php echo e(get_option('paypal_currency') == $code ? 'selected' : ''); ?>><?php echo e($currency); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label><?php echo e(__('Conversion Rate')); ?> </label>
                                            <div class="input-group mb-3">
                                                
                                                <span
                                                    class="input-group-text"><?php echo e('1 ' . get_currency_code() . ' = '); ?></span>
                                                <input type="number" step="any" min="0"
                                                       name="paypal_conversion_rate"
                                                       value="<?php echo e(get_option('paypal_conversion_rate') ? get_option('paypal_conversion_rate') : 1); ?>"
                                                       class="form-control">
                                                <span class="input-group-text paypal_append_currency append_currency"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Status')); ?> </label>
                                                <select name="paypal_status" class="form-control">
                                                    <option value="1"
                                                        <?php echo e(get_option('paypal_status') == 1 ? 'selected' : ''); ?>>
                                                        <?php echo e(__('Enable')); ?> </option>
                                                    <option value="0"
                                                        <?php echo e(get_option('paypal_status') == '0' ? 'selected' : ''); ?>>
                                                        <?php echo e(__('Disable')); ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('PayPal Mode')); ?> </label>
                                                <select name="PAYPAL_MODE" class="form-control">
                                                    <option value="sandbox"
                                                        <?php echo e(get_option('PAYPAL_MODE') == 'sandbox' ? 'selected' : ''); ?>>
                                                        <?php echo e(__('Sandbox')); ?> </option>
                                                    <option value="live"
                                                        <?php echo e(get_option('PAYPAL_MODE') == 'live' ? 'selected' : ''); ?>>
                                                        <?php echo e(__('Live')); ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">

                                            <div class="form-group text-black">
                                                <label><?php echo e(__('PayPal Client ID')); ?> </label>
                                                <input type="text" name="PAYPAL_CLIENT_ID"
                                                       value="<?php echo e(get_option('PAYPAL_CLIENT_ID')); ?>" class="form-control">
                                            </div>


                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">

                                            <div class="form-group text-black">
                                                <label><?php echo e(__('PayPal Secret')); ?> </label>
                                                <input type="text" name="PAYPAL_SECRET"
                                                       value="<?php echo e(get_option('PAYPAL_SECRET')); ?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="payment-getaway admin-dashboard-payment-content-box-right col-md-6 border p-3">
                                    <div class="row mb-3">

                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Currency ISO Code')); ?> </label>
                                                <select  name="stripe_currency" class="form-control stripe_currency currency">
                                                    <?php $__currentLoopData = getCurrency(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($code); ?>" <?php echo e(get_option('stripe_currency') == $code ? 'selected' : ''); ?>><?php echo e($currency); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label><?php echo e(__('Conversion Rate')); ?> </label>
                                            <div class="input-group mb-3">
                                                <span
                                                    class="input-group-text"><?php echo e('1 ' . get_currency_code() . ' = '); ?></span>
                                                <input type="number" step="any" min="0"
                                                       name="stripe_conversion_rate"
                                                       value="<?php echo e(get_option('stripe_conversion_rate') ? get_option('stripe_conversion_rate') : 1); ?>"
                                                       class="form-control">
                                                <span class="input-group-text stripe_append_currency append_currency"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Status')); ?> </label>
                                                <select name="stripe_status" class="form-control">
                                                    <option value="1"
                                                        <?php echo e(get_option('stripe_status') == 1 ? 'selected' : ''); ?>>
                                                        <?php echo e(__('Enable')); ?> </option>
                                                    <option value="0"
                                                        <?php echo e(get_option('stripe_status') == '0' ? 'selected' : ''); ?>>
                                                        <?php echo e(__('Disable')); ?> </option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Stripe Mode')); ?> </label>
                                                <select name="STRIPE_MODE" class="form-control">
                                                    <option value="sandbox"
                                                        <?php echo e(get_option('stripe_mode') == 'sandbox' ? 'selected' : ''); ?>>
                                                        <?php echo e(__('Sandbox')); ?> </option>
                                                    <option value="live"
                                                        <?php echo e(get_option('stripe_mode') == 'live' ? 'selected' : ''); ?>>
                                                        <?php echo e(__('Live')); ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Stripe Secret Key')); ?></label>
                                                <input type="text" name="STRIPE_PUBLIC_KEY"
                                                       value="<?php echo e(get_option('STRIPE_PUBLIC_KEY')); ?>" class="form-control">
                                            </div>
                                        </div>







                                    </div>
                                </div>


                            </div>

                            <div class="row justify-content-center p-3 pb-0">
                                <div
                                    class="admin-dashboard-payment-title-left col-6 border border-bottom-0 pr-4 text-center">
                                    <h5 class="p-2"><?php echo e(__('RAZORPAY')); ?></h5>
                                </div>
                                <div
                                    class="admin-dashboard-payment-title-right col-6 border border-bottom-0 pl-4 text-center">
                                    <h5 class="p-2"><?php echo e(__('SSLCOMMERZ')); ?></h5>
                                </div>
                            </div>

                            <div class="row justify-content-center px-3 pb-0 mb-3">
                                <div class="payment-getaway admin-dashboard-payment-content-box-left col-md-6 border p-3">
                                    <div class="row mb-3">

                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Currency ISO Code')); ?> </label>
                                                <select  name="razorpay_currency" class="form-control razorpay_currency currency">
                                                    <?php $__currentLoopData = getCurrency(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($code); ?>" <?php echo e(get_option('razorpay_currency') == $code ? 'selected' : ''); ?>><?php echo e($currency); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label><?php echo e(__('Conversion Rate')); ?> </label>
                                            <div class="input-group mb-3">
                                                <span
                                                    class="input-group-text"><?php echo e('1 ' . get_currency_code() . ' = '); ?></span>
                                                <input type="number" step="any" min="0"
                                                       name="razorpay_conversion_rate"
                                                       value="<?php echo e(get_option('razorpay_conversion_rate') ? get_option('razorpay_conversion_rate') : 1); ?>"
                                                       class="form-control">
                                                <span class="input-group-text razorpay_append_currency append_currency"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Status')); ?> </label>
                                                <select name="razorpay_status" class="form-control">
                                                    <option value=""> <?php echo e(__('Select Option')); ?></option>
                                                    <option value="1"
                                                        <?php echo e(get_option('razorpay_status') == 1 ? 'selected' : ''); ?>>
                                                        <?php echo e(__('Enable')); ?> </option>
                                                    <option value="0"
                                                        <?php echo e(get_option('razorpay_status') == '0' ? 'selected' : ''); ?>>
                                                        <?php echo e(__('Disable')); ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label> <?php echo e(__('RAZORPAY KEY')); ?> </label>
                                                <input type="text" name="RAZORPAY_KEY"
                                                       value="<?php echo e(get_option('RAZORPAY_KEY')); ?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">

                                            <div class="form-group text-black">
                                                <label> <?php echo e(__('RAZORPAY SECRET')); ?> </label>
                                                <input type="text" name="RAZORPAY_SECRET"
                                                       value="<?php echo e(get_option('RAZORPAY_SECRET')); ?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="payment-getaway admin-dashboard-payment-content-box-right col-md-6 border p-3">
                                    <div class="row mb-3">

                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Currency ISO Code')); ?> </label>
                                                <select  name="sslcommerz_currency" class="form-control sslcommerz_currency currency">
                                                    <?php $__currentLoopData = getCurrency(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($code); ?>" <?php echo e(get_option('sslcommerz_currency') == $code ? 'selected' : ''); ?>><?php echo e($currency); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label><?php echo e(__('Conversion Rate')); ?> </label>
                                            <div class="input-group mb-3">
                                                <span
                                                    class="input-group-text"><?php echo e('1 ' . get_currency_code() . ' = '); ?></span>
                                                <input type="number" step="any" min="0"
                                                       name="sslcommerz_conversion_rate"
                                                       value="<?php echo e(get_option('sslcommerz_conversion_rate') ? get_option('sslcommerz_conversion_rate') : 1); ?>"
                                                       class="form-control">
                                                <span class="input-group-text sslcommerz_append_currency append_currency"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Status')); ?> </label>
                                                <select name="sslcommerz_status" class="form-control">
                                                    <option value=""> <?php echo e(__('Select Option')); ?></option>
                                                    <option value="1"
                                                        <?php echo e(get_option('sslcommerz_status') == 1 ? 'selected' : ''); ?>>
                                                        <?php echo e(__('Enable')); ?> </option>
                                                    <option value="0"
                                                        <?php echo e(get_option('sslcommerz_status') == '0' ? 'selected' : ''); ?>>
                                                        <?php echo e(__('Disable')); ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label> <?php echo e(__('Sslcommerz Mode')); ?> </label>
                                                <select name="sslcommerz_mode" class="form-control">
                                                    <option value=""> <?php echo e(__('Select Option')); ?></option>
                                                    <option value="sandbox"
                                                        <?php echo e(get_option('sslcommerz_mode') == 'sandbox' ? 'selected' : ''); ?>>
                                                        <?php echo e(__('Sandbox')); ?> </option>
                                                    <option value="live"
                                                        <?php echo e(get_option('sslcommerz_mode') == 'live' ? 'selected' : ''); ?>>
                                                        <?php echo e(__('Live')); ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label> <?php echo e(__('Sslcommerz Store ID')); ?> </label>
                                                <input type="text" name="SSLCZ_STORE_ID"
                                                       value="<?php echo e(get_option('SSLCZ_STORE_ID')); ?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">

                                            <div class="form-group text-black">
                                                <label> <?php echo e(__('Sslcommerz store password')); ?> </label>
                                                <input type="text" name="SSLCZ_STORE_PASSWD"
                                                       value="<?php echo e(get_option('SSLCZ_STORE_PASSWD')); ?>"
                                                       class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row justify-content-center p-3 pb-0">
                                <div
                                    class="admin-dashboard-payment-title-left col-6 border border-bottom-0 pr-4 text-center">
                                    <h5 class="p-2"><?php echo e(__('Mollie')); ?></h5>
                                </div>
                                <div
                                    class="admin-dashboard-payment-title-right col-6 border border-bottom-0 pl-4 text-center">
                                    <h5 class="p-2"><?php echo e(__('Instamojo')); ?></h5>
                                </div>
                            </div>

                            <div class="row justify-content-center px-3 pb-0 mb-3">
                                <div class="payment-getaway admin-dashboard-payment-content-box-left col-md-6 border p-3">
                                    <div class="row mb-3">

                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Currency ISO Code')); ?> </label>
                                                <select  name="mollie_currency" class="form-control mollie_currency currency">
                                                    <?php $__currentLoopData = getCurrency(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($code); ?>" <?php echo e(get_option('mollie_currency') == $code ? 'selected' : ''); ?>><?php echo e($currency); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label><?php echo e(__('Conversion Rate')); ?> </label>
                                            <div class="input-group mb-3">
                                                <span
                                                    class="input-group-text"><?php echo e('1 ' . get_currency_code() . ' = '); ?></span>
                                                <input type="number" step="any" min="0"
                                                       name="mollie_conversion_rate"
                                                       value="<?php echo e(get_option('mollie_conversion_rate') ? get_option('mollie_conversion_rate') : 1); ?>"
                                                       class="form-control">
                                                <span class="input-group-text mollie_append_currency append_currency"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Status')); ?> </label>
                                                <select name="mollie_status" class="form-control">
                                                    <option value=""> <?php echo e(__('Select Option')); ?></option>
                                                    <option value="1"
                                                        <?php echo e(get_option('mollie_status') == 1 ? 'selected' : ''); ?>>
                                                        <?php echo e(__('Enable')); ?> </option>
                                                    <option value="0"
                                                        <?php echo e(get_option('mollie_status') == '0' ? 'selected' : ''); ?>>
                                                        <?php echo e(__('Disable')); ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label> <?php echo e(__('MOLLIE KEY')); ?> </label>
                                                <input type="text" name="MOLLIE_KEY"
                                                       value="<?php echo e(get_option('MOLLIE_KEY')); ?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="payment-getaway admin-dashboard-payment-content-box-right col-md-6 border p-3">
                                    <div class="row mb-3">

                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Currency ISO Code')); ?> </label>
                                                <select  name="im_currency" class="form-control im_currency currency">
                                                    <?php $__currentLoopData = getCurrency(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($code); ?>" <?php echo e(get_option('im_currency') == $code ? 'selected' : ''); ?>><?php echo e($currency); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label><?php echo e(__('Conversion Rate')); ?> </label>
                                            <div class="input-group mb-3">
                                                <span
                                                    class="input-group-text"><?php echo e('1 ' . get_currency_code() . ' = '); ?></span>
                                                <input type="number" step="any" min="0"
                                                       name="im_conversion_rate"
                                                       value="<?php echo e(get_option('im_conversion_rate') ? get_option('im_conversion_rate') : 1); ?>"
                                                       class="form-control">
                                                <span class="input-group-text im_append_currency append_currency"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Status')); ?> </label>
                                                <select name="im_status" class="form-control">
                                                    <option value=""> <?php echo e(__('Select Option')); ?></option>
                                                    <option value="1"
                                                        <?php echo e(get_option('im_status') == 1 ? 'selected' : ''); ?>>
                                                        <?php echo e(__('Enable')); ?> </option>
                                                    <option value="0"
                                                        <?php echo e(get_option('im_status') == '0' ? 'selected' : ''); ?>>
                                                        <?php echo e(__('Disable')); ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label> <?php echo e(__('API KEY')); ?> </label>
                                                <input type="text" name="IM_API_KEY" value="<?php echo e(get_option('IM_API_KEY')); ?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label> <?php echo e(__('AUTH TOKEN')); ?> </label>
                                                <input type="text" name="IM_AUTH_TOKEN" value="<?php echo e(get_option('IM_AUTH_TOKEN')); ?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label> <?php echo e(__('Payment Mode')); ?> </label>
                                                <select name="IM_URL" class="form-control">
                                                    <option value="https://test.instamojo.com/api/1.1/payment-requests/"
                                                        <?php echo e(get_option('IM_URL') == 'https://test.instamojo.com/api/1.1/payment-requests/' ? 'selected' : ''); ?>>
                                                        Sandbox
                                                    </option>
                                                    <option value="https://www.instamojo.com/api/1.1/payment-requests/"
                                                        <?php echo e(get_option('IM_URL') == 'https://www.instamojo.com/api/1.1/payment-requests/' ? 'selected' : ''); ?>>
                                                        Live
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row justify-content-center p-3 pb-0">
                                <div
                                    class="admin-dashboard-payment-title-left col-6 border border-bottom-0 pr-4 text-center">
                                    <h5 class="p-2"><?php echo e(__('Paystack')); ?></h5>
                                </div>
                                <div
                                    class="admin-dashboard-payment-title-left col-6 border border-bottom-0 pr-4 text-center">
                                    <h5 class="p-2"><?php echo e(__('Mercado PAGO')); ?></h5>
                                </div>
                            </div>
                            <div class="row justify-content-center px-3 pb-0 mb-3">
                                <div class="payment-getaway admin-dashboard-payment-content-box-left col-md-6 border p-3">
                                    <div class="row mb-3">

                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Currency ISO Code')); ?> </label>
                                                <select  name="paystack_currency" class="form-control paystack_currency currency">
                                                    <?php $__currentLoopData = getCurrency(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($code); ?>" <?php echo e(get_option('paystack_currency') == $code ? 'selected' : ''); ?>><?php echo e($currency); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label><?php echo e(__('Conversion Rate')); ?> </label>
                                            <div class="input-group mb-3">
                                                <span
                                                    class="input-group-text"><?php echo e('1 ' . get_currency_code() . ' = '); ?></span>
                                                <input type="number" step="any" min="0" name="paystack_conversion_rate"
                                                       value="<?php echo e(get_option('paystack_conversion_rate') ? get_option('paystack_conversion_rate') : 1); ?>" class="form-control">
                                                <span class="input-group-text paystack_append_currency append_currency"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Status')); ?> </label>
                                                <select name="paystack_status" class="form-control">
                                                    <option value=""> <?php echo e(__('Select Option')); ?></option>
                                                    <option value="1"<?php echo e(get_option('paystack_status') == 1 ? 'selected' : ''); ?>><?php echo e(__('Enable')); ?> </option>
                                                    <option value="0"<?php echo e(get_option('paystack_status') == '0' ? 'selected' : ''); ?>><?php echo e(__('Disable')); ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label> <?php echo e(__('Paystack Public Key')); ?> </label>
                                                <input type="text" name="PAYSTACK_PUBLIC_KEY" value="<?php echo e(get_option('PAYSTACK_PUBLIC_KEY')); ?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label> <?php echo e(__('Paystack Secret Key')); ?> </label>
                                                <input type="text" name="PAYSTACK_SECRET_KEY" value="<?php echo e(get_option('PAYSTACK_SECRET_KEY')); ?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="payment-getaway payment-getaway admin-dashboard-payment-content-box-right col-md-6 border p-3">
                                    <div class="row mb-3">

                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Currency ISO Code')); ?> </label>
                                                <select  name="mercado_currency" class="form-control mercado_currency currency">
                                                    <?php $__currentLoopData = getCurrency(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($code); ?>" <?php echo e(get_option('mercado_currency') == $code ? 'selected' : ''); ?>><?php echo e($currency); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label><?php echo e(__('Conversion Rate')); ?> </label>
                                            <div class="input-group mb-3">
                                                <span
                                                    class="input-group-text"><?php echo e('1 ' . get_currency_code() . ' = '); ?></span>
                                                <input type="number" step="any" min="0" name="mercado_conversion_rate"
                                                       value="<?php echo e(get_option('mercado_conversion_rate') ? get_option('mercado_conversion_rate') : 1); ?>" class="form-control">
                                                <span class="input-group-text mercado_append_currency append_currency"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Status')); ?> </label>
                                                <select name="mercado_status" class="form-control">
                                                    <option value=""> <?php echo e(__('Select Option')); ?></option>
                                                    <option value="1"<?php echo e(get_option('mercado_status') == 1 ? 'selected' : ''); ?>><?php echo e(__('Enable')); ?> </option>
                                                    <option value="0"<?php echo e(get_option('mercado_status') == '0' ? 'selected' : ''); ?>><?php echo e(__('Disable')); ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label> <?php echo e(__('Mercado Client ID')); ?> </label>
                                                <input type="text" name="MERCADO_PAGO_CLIENT_ID" value="<?php echo e(get_option('MERCADO_PAGO_CLIENT_ID')); ?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label> <?php echo e(__('Mercado Client Secret')); ?> </label>
                                                <input type="text" name="MERCADO_PAGO_CLIENT_SECRET" value="<?php echo e(get_option('MERCADO_PAGO_CLIENT_SECRET')); ?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <div class="row justify-content-center p-3 pb-0">
                                <div
                                    class="admin-dashboard-payment-title-left col-6 border border-bottom-0 pr-4 text-center">
                                    <h5 class="p-2"><?php echo e(__('Flutterwave')); ?></h5>
                                </div>
                                <div
                                    class="admin-dashboard-payment-title-left col-6 border border-bottom-0 pr-4 text-center">
                                    <h5 class="p-2"><?php echo e(__('Coinbase')); ?></h5>
                                </div>
                            </div>
                            <div class="row justify-content-center px-3 pb-0 mb-3">
                                <div class="payment-getaway admin-dashboard-payment-content-box-left col-md-6 border p-3">
                                    <div class="row mb-3">

                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Currency ISO Code')); ?> </label>
                                                <select  name="flutterwave_currency" class="form-control currency">
                                                    <?php $__currentLoopData = getCurrency(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($code); ?>" <?php echo e(get_option('flutterwave_currency') == $code ? 'selected' : ''); ?>><?php echo e($currency); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label><?php echo e(__('Conversion Rate')); ?> </label>
                                            <div class="input-group mb-3">
                                                <span
                                                    class="input-group-text"><?php echo e('1 ' . get_currency_code() . ' = '); ?></span>
                                                <input type="number" step="any" min="0" name="flutterwave_conversion_rate"
                                                       value="<?php echo e(get_option('flutterwave_conversion_rate') ? get_option('flutterwave_conversion_rate') : 1); ?>" class="form-control">
                                                <span class="input-group-text append_currency"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Status')); ?> </label>
                                                <select name="flutterwave_status" class="form-control">
                                                    <option value=""> <?php echo e(__('Select Option')); ?></option>
                                                    <option value="1"<?php echo e(get_option('flutterwave_status') == 1 ? 'selected' : ''); ?>><?php echo e(__('Enable')); ?> </option>
                                                    <option value="0"<?php echo e(get_option('flutterwave_status') == '0' ? 'selected' : ''); ?>><?php echo e(__('Disable')); ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label> <?php echo e(__('Flutterwave Public Key')); ?> </label>
                                                <input type="text" name="FLW_PUBLIC_KEY" value="<?php echo e(get_option('FLW_PUBLIC_KEY')); ?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label> <?php echo e(__('Flutterwave Secret Key')); ?> </label>
                                                <input type="text" name="FLW_SECRET_KEY" value="<?php echo e(get_option('FLW_SECRET_KEY')); ?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label> <?php echo e(__('Flutterwave Secret Hash')); ?> </label>
                                                <input type="text" name="FLW_SECRET_HASH" value="<?php echo e(get_option('FLW_SECRET_HASH')); ?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="payment-getaway admin-dashboard-payment-content-box-left col-md-6 border p-3">
                                    <div class="row mb-3">

                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Currency ISO Code')); ?> </label>
                                                <select  name="coinbase_currency" class="form-control currency">
                                                    <?php $__currentLoopData = getCurrency(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($code); ?>" <?php echo e(get_option('coinbase_currency') == $code ? 'selected' : ''); ?>><?php echo e($currency); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label><?php echo e(__('Conversion Rate')); ?> </label>
                                            <div class="input-group mb-3">
                                                <span
                                                    class="input-group-text"><?php echo e('1 ' . get_currency_code() . ' = '); ?></span>
                                                <input type="number" step="any" min="0" name="coinbase_conversion_rate"
                                                       value="<?php echo e(get_option('coinbase_conversion_rate') ? get_option('coinbase_conversion_rate') : 1); ?>" class="form-control">
                                                <span class="input-group-text append_currency"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Status')); ?> </label>
                                                <select name="coinbase_status" class="form-control">
                                                    <option value=""> <?php echo e(__('Select Option')); ?></option>
                                                    <option value="1"<?php echo e(get_option('coinbase_status') == 1 ? 'selected' : ''); ?>><?php echo e(__('Enable')); ?> </option>
                                                    <option value="0"<?php echo e(get_option('coinbase_status') == '0' ? 'selected' : ''); ?>><?php echo e(__('Disable')); ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Coinbase Mode')); ?> </label>
                                                <select name="coinbase_mode" class="form-control">
                                                    <option value="sandbox"
                                                        <?php echo e(get_option('coinbase_mode') == 'sandbox' ? 'selected' : ''); ?>>
                                                        <?php echo e(__('Sandbox')); ?> </option>
                                                    <option value="live"
                                                        <?php echo e(get_option('coinbase_mode') == 'live' ? 'selected' : ''); ?>>
                                                        <?php echo e(__('Live')); ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label> <?php echo e(__('Coinbase Key')); ?> </label>
                                                <input type="text" name="coinbase_key" value="<?php echo e(get_option('coinbase_key')); ?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center p-3 pb-0">
                                <div
                                    class="admin-dashboard-payment-title-left col-6 border border-bottom-0 pr-4 text-center">
                                    <h5 class="p-2"><?php echo e(__('Zitopay')); ?></h5>
                                </div>
                                <div class="admin-dashboard-payment-title-left col-6 border border-bottom-0 pr-4 text-center">
                                    <h5 class="p-2"><?php echo e(__('iyzico')); ?></h5>
                                </div>
                            </div>
                            <div class="row justify-content-center px-3 pb-0 mb-3">
                                <div class="payment-getaway admin-dashboard-payment-content-box-left col-md-6 border p-3">
                                    <div class="row mb-3">

                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Currency ISO Code')); ?> </label>
                                                <select  name="zitopay_currency" class="form-control currency">
                                                    <?php $__currentLoopData = getCurrency(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($code); ?>" <?php echo e(get_option('zitopay_currency') == $code ? 'selected' : ''); ?>><?php echo e($currency); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label><?php echo e(__('Conversion Rate')); ?> </label>
                                            <div class="input-group mb-3">
                                                <span
                                                    class="input-group-text"><?php echo e('1 ' . get_currency_code() . ' = '); ?></span>
                                                <input type="number" step="any" min="0" name="zitopay_conversion_rate"
                                                       value="<?php echo e(get_option('zitopay_conversion_rate') ? get_option('zitopay_conversion_rate') : 1); ?>" class="form-control">
                                                <span class="input-group-text append_currency"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Status')); ?> </label>
                                                <select name="zitopay_status" class="form-control">
                                                    <option value=""> <?php echo e(__('Select Option')); ?></option>
                                                    <option value="1"<?php echo e(get_option('zitopay_status') == 1 ? 'selected' : ''); ?>><?php echo e(__('Enable')); ?> </option>
                                                    <option value="0"<?php echo e(get_option('zitopay_status') == '0' ? 'selected' : ''); ?>><?php echo e(__('Disable')); ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label> <?php echo e(__('Zitopay Username')); ?> </label>
                                                <input type="text" name="zitopay_username" value="<?php echo e(get_option('zitopay_username')); ?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="payment-getaway admin-dashboard-payment-content-box-left col-md-6 border p-3">
                                    <div class="row mb-3">

                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Currency ISO Code')); ?> </label>
                                                <select  name="iyzipay_currency" class="form-control currency">
                                                    <?php $__currentLoopData = getCurrency(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($code); ?>" <?php echo e(get_option('iyzipay_currency') == $code ? 'selected' : ''); ?>><?php echo e($currency); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label><?php echo e(__('Conversion Rate')); ?> </label>
                                            <div class="input-group mb-3">
                                                <span
                                                    class="input-group-text"><?php echo e('1 ' . get_currency_code() . ' = '); ?></span>
                                                <input type="number" step="any" min="0" name="iyzipay_conversion_rate"
                                                       value="<?php echo e(get_option('iyzipay_conversion_rate') ? get_option('iyzipay_conversion_rate') : 1); ?>" class="form-control">
                                                <span class="input-group-text append_currency"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Status')); ?> </label>
                                                <select name="iyzipay_status" class="form-control">
                                                    <option value=""> <?php echo e(__('Select Option')); ?></option>
                                                    <option value="1"<?php echo e(get_option('iyzipay_status') == 1 ? 'selected' : ''); ?>><?php echo e(__('Enable')); ?> </option>
                                                    <option value="0"<?php echo e(get_option('iyzipay_status') == '0' ? 'selected' : ''); ?>><?php echo e(__('Disable')); ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Iyzico Mode')); ?> </label>
                                                <select name="iyzipay_mode" class="form-control">
                                                    <option value="sandbox"
                                                        <?php echo e(get_option('iyzipay_mode') == 'sandbox' ? 'selected' : ''); ?>>
                                                        <?php echo e(__('Sandbox')); ?> </option>
                                                    <option value="live"
                                                        <?php echo e(get_option('iyzipay_mode') == 'live' ? 'selected' : ''); ?>>
                                                        <?php echo e(__('Live')); ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label> <?php echo e(__('Iyzico Key')); ?> </label>
                                                <input type="text" name="iyzipay_key" value="<?php echo e(get_option('iyzipay_key')); ?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label> <?php echo e(__('Iyzico Secret')); ?> </label>
                                                <input type="text" name="iyzipay_secret" value="<?php echo e(get_option('iyzipay_secret')); ?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row justify-content-center p-3 pb-0">
                                <div
                                    class="admin-dashboard-payment-title-left col-6 border border-bottom-0 pr-4 text-center">
                                    <h5 class="p-2"><?php echo e(__('Bitpay')); ?></h5>
                                </div>
                                <div class="admin-dashboard-payment-title-left col-6 border border-bottom-0 pr-4 text-center">
                                    <h5 class="p-2"><?php echo e(__('iyzico')); ?></h5>
                                </div>
                            </div>

                            <div class="row justify-content-center px-3 pb-0 mb-3">
                                <div class="payment-getaway admin-dashboard-payment-content-box-left col-md-6 border p-3">
                                    <div class="row mb-3">

                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Currency ISO Code')); ?> </label>
                                                <select  name="bitpay_currency" class="form-control currency">
                                                    <?php $__currentLoopData = getCurrency(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($code); ?>" <?php echo e(get_option('bitpay_currency') == $code ? 'selected' : ''); ?>><?php echo e($currency); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label><?php echo e(__('Conversion Rate')); ?> </label>
                                            <div class="input-group mb-3">
                                                <span
                                                    class="input-group-text"><?php echo e('1 ' . get_currency_symbol() . ' = '); ?></span>
                                                <input type="number" step="any" min="0" name="bitpay_conversion_rate"
                                                       value="<?php echo e(get_option('bitpay_conversion_rate') ? get_option('bitpay_conversion_rate') : 1); ?>" class="form-control">
                                                <span class="input-group-text append_currency"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Status')); ?> </label>
                                                <select name="bitpay_status" class="form-control">
                                                    <option value=""> <?php echo e(__('Select Option')); ?></option>
                                                    <option value="1"<?php echo e(get_option('bitpay_status') == 1 ? 'selected' : ''); ?>><?php echo e(__('Enable')); ?> </option>
                                                    <option value="0"<?php echo e(get_option('bitpay_status') == '0' ? 'selected' : ''); ?>><?php echo e(__('Disable')); ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Bitpay Mode')); ?> </label>
                                                <select name="bitpay_mode" class="form-control">
                                                    <option value="testnet"
                                                        <?php echo e(get_option('bitpay_mode') == 'testnet' ? 'selected' : ''); ?>>
                                                        <?php echo e(__('Testnet')); ?> </option>
                                                    <option value="livenet"
                                                        <?php echo e(get_option('bitpay_mode') == 'livenet' ? 'selected' : ''); ?>>
                                                        <?php echo e(__('Livenet')); ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label> <?php echo e(__('Bitpay Key')); ?> </label>
                                                <input type="text" name="bitpay_key" value="<?php echo e(get_option('bitpay_key')); ?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="payment-getaway admin-dashboard-payment-content-box-left col-md-6 border p-3">
                                    <div class="row mb-3">

                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Currency ISO Code')); ?> </label>
                                                <select  name="braintree_currency" class="form-control currency">
                                                    <?php $__currentLoopData = getCurrency(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($code); ?>" <?php echo e(get_option('braintree_currency') == $code ? 'selected' : ''); ?>><?php echo e($currency); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label><?php echo e(__('Conversion Rate')); ?> </label>
                                            <div class="input-group mb-3">
                                                <span
                                                    class="input-group-text"><?php echo e('1 ' . get_currency_symbol() . ' = '); ?></span>
                                                <input type="number" step="any" min="0" name="braintree_conversion_rate"
                                                       value="<?php echo e(get_option('braintree_conversion_rate') ? get_option('braintree_conversion_rate') : 1); ?>" class="form-control">
                                                <span class="input-group-text append_currency"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Status')); ?> </label>
                                                <select name="braintree_status" class="form-control">
                                                    <option value=""> <?php echo e(__('Select Option')); ?></option>
                                                    <option value="1"<?php echo e(get_option('braintree_status') == 1 ? 'selected' : ''); ?>><?php echo e(__('Enable')); ?> </option>
                                                    <option value="0"<?php echo e(get_option('braintree_status') == '0' ? 'selected' : ''); ?>><?php echo e(__('Disable')); ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Braintree Test Mode')); ?> </label>
                                                <select name="braintree_test_mode" class="form-control">
                                                    <option value="1"
                                                        <?php echo e(get_option('braintree_test_mode') == 1 ? 'selected' : ''); ?>>
                                                        <?php echo e(__('True')); ?> </option>
                                                    <option value="0"
                                                        <?php echo e(get_option('braintree_test_mode') == 0 ? 'selected' : ''); ?>>
                                                        <?php echo e(__('False')); ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label> <?php echo e(__('Braintree Merchant ID')); ?> </label>
                                                <input type="text" name="braintree_merchant_id" value="<?php echo e(get_option('braintree_merchant_id')); ?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label> <?php echo e(__('Braintree Public Key')); ?> </label>
                                                <input type="text" name="braintree_public_key" value="<?php echo e(get_option('braintree_public_key')); ?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label> <?php echo e(__('Braintree Private Secret')); ?> </label>
                                                <input type="text" name="braintree_private_key" value="<?php echo e(get_option('braintree_private_key')); ?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>




                            <div class="row justify-content-center p-3 pb-0">
                                <div
                                    class="admin-dashboard-payment-title-left col-6 border border-bottom-0 pr-4 text-center">
                                    <h5 class="p-2"><?php echo e(__('Faysal Bank')); ?></h5>
                                </div>
                                <div
                                    class="admin-dashboard-payment-title-right col-6 border border-bottom-0 pl-4 text-center">
                                    <h5 class="p-2"><?php echo e(__('Meezan Bank')); ?></h5>
                                </div>
                            </div>
                            <div class="row justify-content-center px-3 pb-0 mb-3">
                                <div class="payment-getaway admin-dashboard-payment-content-box-left col-md-6 border p-3">
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Currency ISO Code')); ?> </label>
                                                <select  name="faysal_currency" class="form-control currency">
                                                    <?php $__currentLoopData = getCurrency(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($code); ?>" <?php echo e(get_option('faysal_currency') == $code ? 'selected' : ''); ?>><?php echo e($currency); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label><?php echo e(__('Conversion Rate')); ?> </label>
                                            <div class="input-group mb-3">
                                                <span
                                                    class="input-group-text"><?php echo e('1 ' . get_currency_code() . ' = '); ?></span>
                                                <input type="number" step="any" min="0" name="faysal_conversion_rate"
                                                       value="<?php echo e(get_option('faysal_conversion_rate') ? get_option('faysal_conversion_rate') : 1); ?>" class="form-control">
                                                <span class="input-group-text append_currency"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Status')); ?> </label>
                                                <select name="faysal_status" class="form-control">
                                                    <option value=""> <?php echo e(__('Select Option')); ?></option>
                                                    <option value="1"<?php echo e(get_option('faysal_status') == 1 ? 'selected' : ''); ?>><?php echo e(__('Enable')); ?> </option>
                                                    <option value="0"<?php echo e(get_option('faysal_status') == '0' ? 'selected' : ''); ?>><?php echo e(__('Disable')); ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Faysal Merchant ID')); ?> </label>
                                                <input type="text" name="faysal_merchant_id" value="<?php echo e(get_option('faysal_merchant_id')); ?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label> <?php echo e(__('Faysal Secure Key')); ?> </label>
                                                <input type="text" name="faysal_secure_key" value="<?php echo e(get_option('faysal_secure_key')); ?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label> <?php echo e(__('Faysal Base URL')); ?> </label>
                                                <input type="text" name="faysal_base_url" value="<?php echo e(get_option('faysal_base_url')); ?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label> <?php echo e(__('Faysal Access Token URL')); ?> </label>
                                                <input type="text" name="faysal_access_token_url" value="<?php echo e(get_option('faysal_access_token_url')); ?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                </div>




                                <div class="payment-getaway admin-dashboard-payment-content-box-left col-md-6 border p-3">
                                    <div class="row mb-3">

                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Currency ISO Code')); ?> </label>
                                                <select  name="meezan_currency" class="form-control currency">
                                                    <?php $__currentLoopData = getCurrency(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($code); ?>" <?php echo e(get_option('meezan_currency') == $code ? 'selected' : ''); ?>><?php echo e($currency); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label><?php echo e(__('Conversion Rate')); ?> </label>
                                            <div class="input-group mb-3">
                                                <span
                                                    class="input-group-text"><?php echo e('1 ' . get_currency_code() . ' = '); ?></span>
                                                <input type="number" step="any" min="0" name="meezan_conversion_rate"
                                                       value="<?php echo e(get_option('meezan_conversion_rate') ? get_option('meezan_conversion_rate') : 1); ?>" class="form-control">
                                                <span class="input-group-text append_currency"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Status')); ?> </label>
                                                <select name="meezan_status" class="form-control">
                                                    <option value=""> <?php echo e(__('Select Option')); ?></option>
                                                    <option value="1"<?php echo e(get_option('meezan_status') == 1 ? 'selected' : ''); ?>><?php echo e(__('Enable')); ?> </option>
                                                    <option value="0"<?php echo e(get_option('meezan_status') == '0' ? 'selected' : ''); ?>><?php echo e(__('Disable')); ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label> <?php echo e(__('Meezan User Name')); ?> </label>
                                                <input type="text" name="meezan_user_name" value="<?php echo e(get_option('meezan_user_name')); ?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label> <?php echo e(__('Meezan User Password')); ?> </label>
                                                <input type="text" name="meezan_user_password" value="<?php echo e(get_option('meezan_user_password')); ?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label> <?php echo e(__('Meezan Port Address')); ?> </label>
                                                <input type="text" name="meezan_port_address" value="<?php echo e(get_option('meezan_port_address')); ?>" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                  
                                </div>
                            </div>



                            <div class="row justify-content-center p-3 pb-0">
                                <div
                                    class="admin-dashboard-payment-title-left col-6 border border-bottom-0 pr-4 text-center">
                                    <h5 class="p-2"><?php echo e(__('Cash On Delivery')); ?></h5>
                                </div>
                            </div>
                            <div class="row justify-content-center px-3 pb-0 mb-3">
                                <div class="payment-getaway admin-dashboard-payment-content-box-left col-md-6 border p-3">
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-group text-black">
                                                <label><?php echo e(__('Status')); ?> </label>
                                                <select name="cash_on_status" class="form-control">
                                                    <option value=""> <?php echo e(__('Select Option')); ?></option>
                                                    <option value="1"<?php echo e(get_option('cash_on_status') == 1 ? 'selected' : ''); ?>><?php echo e(__('Enable')); ?> </option>
                                                    <option value="0"<?php echo e(get_option('cash_on_status') == '0' ? 'selected' : ''); ?>><?php echo e(__('Disable')); ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                  
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="input__group general-settings-btn">
                                        <button type="submit" class="btn btn-blue float-right"><?php echo e(__('Update')); ?></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page content area end -->
<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
    <script src="<?php echo e(asset('admin/js/custom/payment-method.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/payment-method.blade.php ENDPATH**/ ?>
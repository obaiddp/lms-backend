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
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
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
                    <div class="email-inbox__area bg-style form-horizontal__item bg-style admin-general-settings-page">
                        <div class="item-top mb-30"><h2><?php echo e(__(@$title)); ?></h2></div>
                        <form action="<?php echo e(route('settings.general_setting.cms.update')); ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>

                            <div class="row input__group mb-25">
                                <label class="col-lg-3"><?php echo e(__('App Name')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input type="text" name="app_name" value="<?php echo e(get_option('app_name')); ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="row input__group mb-25">
                                <label class="col-lg-3"><?php echo e(__('App Email')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input type="text" name="app_email" value="<?php echo e(get_option('app_email')); ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="row input__group mb-25">
                                <label class="col-lg-3"><?php echo e(__('App Contact Number')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input type="text" name="app_contact_number" value="<?php echo e(get_option('app_contact_number')); ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="row input__group mb-25">
                                <label class="col-lg-3"><?php echo e(__('App Location')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input type="text" name="app_location" value="<?php echo e(get_option('app_location')); ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="row input__group mb-25">
                                <label class="col-lg-3"><?php echo e(__('App Copyright')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input type="text" name="app_copyright" value="<?php echo e(get_option('app_copyright')); ?>" class="form-control" required>
                                </div>
                            </div>



                             <div class="row input__group mb-25">
                                <label class="col-lg-3"><?php echo e(__('Admin Email')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input type="text" name="admin_email" value="<?php echo e(get_option('admin_email')); ?>" class="form-control" required>
                                </div>
                            </div>


                             <div class="row input__group mb-25">
                                <label class="col-lg-3"><?php echo e(__('Financial Email')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input type="text" name="financial_email" value="<?php echo e(get_option('financial_email')); ?>" class="form-control" required>
                                </div>
                            </div>



                            <div class="row input__group mb-25">
                                <label class="col-lg-3"><?php echo e(__('Other Emails')); ?> </label>
                                <div class="col-lg-9">
                                    <?php
                                        $selectedEmails = json_decode(get_option('other_emails'), true) ?? [];
                                        // Static list of allowed emails:
                                        $staticEmails = [
                                            'quranedu.online@cef.org.pk' => 'quranedu.online@cef.org.pk',
                                        ];
                                    ?>

                                    <select name="other_emails[]" class="form-control select2" id="emails_selectpicker" multiple>
                                        <?php $__currentLoopData = $staticEmails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($value); ?>" <?php echo e(in_array($value, $selectedEmails) ? 'selected' : ''); ?>>
                                                <?php echo e($label); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <small class="form-text text-muted">
                                        <?php echo e(__('Choose one or more email addresses to receive notifications.')); ?>

                                    </small>
                                </div>
                            </div>


                            <div class="row input__group mb-25">
                                <label class="col-lg-3"><?php echo e(__('Developed By')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input type="text" name="app_developed" value="<?php echo e(get_option('app_developed')); ?>" class="form-control" required>
                                </div>
                            </div>

                            <div class="row input__group mb-25">
                                <label for="app_date_format" class="col-lg-3"><?php echo e(__('Date Format')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <select name="app_date_format" class="form-control">
                                        <option value="m/d/Y" <?php echo e(get_option('app_date_format') == 'm/d/Y' ? 'selected' : ''); ?> >
                                            m/d/Y (eg. <?php echo e(\Carbon\Carbon::now()->format("m/d/Y")); ?> )
                                        </option>
                                        <option value="d/m/Y" <?php echo e(get_option('app_date_format') == 'd/m/Y' ? 'selected' : ''); ?> >
                                            d/m/Y (eg. <?php echo e(\Carbon\Carbon::now()->format("d/m/Y")); ?> )
                                        </option>
                                        <option value="Y/m/d" <?php echo e(get_option('app_date_format') == 'Y/m/d' ? 'selected' : ''); ?> >
                                            Y/m/d (eg. <?php echo e(\Carbon\Carbon::now()->format("Y/m/d")); ?> )
                                        </option>
                                        <option value="Y/d/m" <?php echo e(get_option('app_date_format') == 'Y/d/m' ? 'selected' : ''); ?> >
                                            Y/d/m (eg. <?php echo e(\Carbon\Carbon::now()->format("Y/d/m")); ?> )
                                        </option>
                                        <option value="m-d-Y" <?php echo e(get_option('app_date_format') == 'm-d-Y' ? 'selected' : ''); ?> >
                                            m-d-Y (eg. <?php echo e(\Carbon\Carbon::now()->format("m-d-Y")); ?> )
                                        </option>
                                        <option value="d-m-Y" <?php echo e(get_option('app_date_format') == 'd-m-Y' ? 'selected' : ''); ?> >
                                            d-m-Y (eg. <?php echo e(\Carbon\Carbon::now()->format("d-m-Y")); ?> )
                                        </option>
                                        <option value="Y-m-d" <?php echo e(get_option('app_date_format') == 'Y-m-d' ? 'selected' : ''); ?> >
                                            Y-m-d (eg. <?php echo e(\Carbon\Carbon::now()->format("Y-m-d")); ?> )
                                        </option>
                                        <option value="Y-d-m" <?php echo e(get_option('app_date_format') == 'Y-d-m' ? 'selected' : ''); ?> >
                                            Y-d-m (eg. <?php echo e(\Carbon\Carbon::now()->format("Y-d-m")); ?> )
                                        </option>
                                        <option value="d M, Y" <?php echo e(get_option('app_date_format') == 'd M, Y' ? 'selected' : ''); ?> >
                                            d M, Y (eg. <?php echo e(\Carbon\Carbon::now()->format("d M, Y")); ?> )
                                        </option>
                                        <option value="M d, Y" <?php echo e(get_option('app_date_format') == 'M d, Y' ? 'selected' : ''); ?> >
                                            M d, Y (eg. <?php echo e(\Carbon\Carbon::now()->format("M d, Y")); ?> )
                                        </option>
                                        <option value="Y M, d" <?php echo e(get_option('app_date_format') == 'Y M, d' ? 'selected' : ''); ?> >
                                            Y M, d (eg. <?php echo e(\Carbon\Carbon::now()->format("Y M, d")); ?> )
                                        </option>
                                        <option value="d F, Y" <?php echo e(get_option('app_date_format') == 'd F, Y' ? 'selected' : ''); ?> >
                                            d F, Y (eg. <?php echo e(\Carbon\Carbon::now()->format("d F, Y")); ?> )
                                        </option>
                                        <option value="Y F, d" <?php echo e(get_option('app_date_format') == 'Y F, d' ? 'selected' : ''); ?> >
                                            Y F, d (eg. <?php echo e(\Carbon\Carbon::now()->format("Y F, d")); ?> )
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="row input__group mb-25">
                                <label for="app_date_format" class="col-lg-3"><?php echo e(__('Default Currency')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <select name="currency_id" class="form-control select2">
                                        <option value=""><?php echo e(__('Select Option')); ?></option>
                                        <?php $__currentLoopData = $currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($currency->id); ?>" <?php echo e($currency->id == @$current_currency->id ? 'selected' : ''); ?> > <?php echo e($currency->symbol); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row input__group mb-25">
                                <label for="app_date_format" class="col-lg-3"><?php echo e(__('Force HTTPS')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <select name="FORCE_HTTPS" class="form-control select2">
                                        <option value="true" <?php echo e(config('app.force_https') == true ? 'selected' : ''); ?> > <?php echo e(__('TRUE')); ?></option>
                                        <option value="false" <?php echo e(config('app.force_https') == false ? 'selected' : ''); ?> > <?php echo e(__('FALSE')); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="row input__group mb-25">
                                <label for="app_date_format" class="col-lg-3"><?php echo e(__('Default Language')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <select name="language_id" class="form-control select2">
                                        <option value=""><?php echo e(__('Select Option')); ?></option>
                                        <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($language->id); ?>" <?php echo e($language->id == @$default_language->id ? 'selected' : ''); ?> > <?php echo e($language->language); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row input__group mb-25">
                                <label for="app_date_format" class="col-lg-3"><?php echo e(__('Time Zone')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <select name="TIMEZONE" class="form-control select2">
                                        <?php $__currentLoopData = getTimeZone(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $timezone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($timezone); ?>" <?php echo e($timezone == env('TIMEZONE', 'UTC') ? 'selected' : ''); ?> > <?php echo e($timezone); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>


                            <div class="row input__group mb-25">
                                <label class="col-lg-3"><?php echo e(__('Admission Fee')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input type="number" name="admission_fee" value="<?php echo e(get_option('admission_fee')); ?>" class="form-control" required>
                                </div>
                            </div>


                            <div class="row input__group mb-25">
                                <label class="col-lg-3"><?php echo e(__('Platform Charge')); ?> (%) <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input type="number" min="0" max="100" name="platform_charge" value="<?php echo e(get_option('platform_charge')); ?>" class="form-control" required>
                                </div>
                            </div>

                            <div class="row input__group mb-25">
                                <label class="col-lg-3"> <?php echo e(__('Sell Commission')); ?> (%) <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input type="number" min="0" max="100" name="sell_commission" value="<?php echo e(get_option('sell_commission')); ?>" class="form-control" required>
                                </div>
                            </div>

                            <div class="row input__group mb-25">
                                <label for="allow_preloader" class="col-lg-3"><?php echo e(__('Allow Preloader')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <select name="allow_preloader" class="form-control">
                                        <option value=""><?php echo e(__('Select Option')); ?></option>
                                        <option value="1" <?php if(get_option('allow_preloader') == 1): ?> selected <?php endif; ?>><?php echo e(__('Active')); ?></option>
                                        <option value="0" <?php if(get_option('allow_preloader') != 1): ?> selected <?php endif; ?>><?php echo e(__('Disable')); ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="row input__group mb-25">
                                <label class="col-lg-3"><?php echo e(__('Preloader')); ?></label>
                                <div class="col-lg-4">
                                    <div class="upload-img-box">
                                        <?php if(get_option('app_preloader') != ''): ?>
                                            <img src="<?php echo e(getImageFile(get_option('app_preloader'))); ?>">
                                        <?php else: ?>
                                            <img src="">
                                        <?php endif; ?>
                                        <input type="file" name="app_preloader" id="app_preloader" accept="image/*" onchange="previewFile(this)">
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                            <p class="m-0"><?php echo e(__('Preloader')); ?></p>
                                        </div>
                                    </div>
                                    <?php if($errors->has('app_preloader')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('app_preloader')); ?></span>
                                    <?php endif; ?>
                                    <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> PNG, SVG <br> <span class="text-black"><?php echo e(__('Recommend Size')); ?>:</span> 118 x 40</p>
                                </div>
                            </div>
                            <div class="row input__group mb-25">
                                <label class="col-lg-3"><?php echo e(__('App Logo')); ?></label>
                                <div class="col-lg-4">
                                    <div class="upload-img-box">
                                        <?php if(get_option('app_logo') != ''): ?>
                                            <img src="<?php echo e(getImageFile(get_option('app_logo'))); ?>">
                                        <?php else: ?>
                                            <img src="">
                                        <?php endif; ?>
                                        <input type="file" name="app_logo" id="app_logo" accept="image/*" onchange="previewFile(this)">
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                            <p class="m-0"><?php echo e(__('App Logo')); ?></p>
                                        </div>
                                    </div>
                                    <?php if($errors->has('app_logo')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('app_logo')); ?></span>
                                    <?php endif; ?>
                                    <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> PNG, SVG <br> <span class="text-black"><?php echo e(__('Recommend Size')); ?>:</span> 140 x 40</p>
                                </div>
                            </div>

                            <div class="row input__group mb-25">
                                <label class="col-lg-3"><?php echo e(__('App Black Logo')); ?></label>
                                <div class="col-lg-4">
                                    <div class="upload-img-box">
                                        <?php if(get_option('app_black_logo') != ''): ?>
                                            <img src="<?php echo e(getImageFile(get_option('app_black_logo'))); ?>">
                                        <?php else: ?>
                                            <img src="">
                                        <?php endif; ?>
                                        <input type="file" name="app_black_logo" id="app_black_logo" accept="image/*" onchange="previewFile(this)">
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                            <p class="m-0"><?php echo e(__('App Black Logo')); ?></p>
                                        </div>
                                    </div>
                                    <?php if($errors->has('app_black_logo')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('app_black_logo')); ?></span>
                                    <?php endif; ?>
                                    <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> PNG, SVG <br> <span class="text-black"><?php echo e(__('Recommend Size')); ?>:</span> 140 x 40</p>
                                </div>
                            </div>


                            <div class="row input__group mb-25">
                                <label class="col-lg-3"><?php echo e(__('App Fav Icon')); ?> </label>
                                <div class="col-lg-4">
                                    <div class="upload-img-box">
                                        <?php if(get_option('app_fav_icon') != ''): ?>
                                            <img src="<?php echo e(getImageFile(get_option('app_fav_icon'))); ?>">
                                        <?php else: ?>
                                            <img src="<?php echo e(asset('uploads/default/no-image-found.png')); ?>">
                                        <?php endif; ?>
                                        <input type="file" name="app_fav_icon" id="app_fav_icon" accept="image/*" onchange="previewFile(this)">
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                            <p class="m-0"><?php echo e(__('App Fav Icon')); ?></p>
                                        </div>
                                    </div>
                                    <?php if($errors->has('app_fav_icon')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('app_fav_icon')); ?></span>
                                    <?php endif; ?>
                                    <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> PNG, SVG <br> <span class="text-black"><?php echo e(__('Recommend Size')); ?>:</span> 16 x 16</p>
                                </div>
                            </div>

                            <div class="row input__group mb-25">
                                <label class="col-lg-3"><?php echo e(__('App Footer Payment Banner')); ?> </label>
                                <div class="col-lg-4">
                                    <div class="upload-img-box">
                                        <?php if(get_option('app_footer_payment_image') != ''): ?>
                                            <img src="<?php echo e(getImageFile(get_option('app_footer_payment_image'))); ?>">
                                        <?php else: ?>
                                            <img src="<?php echo e(asset('uploads/default/no-image-found.png')); ?>">
                                        <?php endif; ?>
                                        <input type="file" name="app_footer_payment_image" id="app_footer_payment_image" accept="image/*" onchange="previewFile(this)">
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                            <p class="m-0"><?php echo e(__('App Footer Payment Banner')); ?></p>
                                        </div>
                                    </div>
                                    <?php if($errors->has('app_footer_payment_image')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('app_footer_payment_image')); ?></span>
                                    <?php endif; ?>
                                    <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> PNG, SVG <br> <span class="text-black"><?php echo e(__('Recommend Size')); ?>:</span>206 × 22 px</p>
                                </div>
                            </div>

                            <div class="row input__group mb-25">
                                <label class="col-lg-3"><?php echo e(__('App PWA Icon')); ?> </label>
                                <div class="col-lg-4">
                                    <div class="upload-img-box">
                                        <?php if(get_option('app_pwa_icon') != ''): ?>
                                            <img src="<?php echo e(getImageFile(get_option('app_pwa_icon'))); ?>">
                                        <?php else: ?>
                                            <img src="<?php echo e(asset('uploads/default/no-image-found.png')); ?>">
                                        <?php endif; ?>
                                        <input type="file" name="app_pwa_icon" id="app_pwa_icon" accept="image/*" onchange="previewFile(this)">
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                            <p class="m-0"><?php echo e(__('App Fav Icon')); ?></p>
                                        </div>
                                    </div>
                                    <?php if($errors->has('app_pwa_icon')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('app_pwa_icon')); ?></span>
                                    <?php endif; ?>
                                    <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> PNG <br> <span class="text-black"><?php echo e(__('Recommend Size')); ?>:</span> 512 x 512</p>
                                </div>
                            </div>

                            <div class="row input__group mb-25">
                                <label for="allow_preloader" class="col-lg-3"><?php echo e(__('PWA enable')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <select name="pwa_enable" class="form-control">
                                        <option value=""><?php echo e(__('Select Option')); ?></option>
                                        <option value="1" <?php if(get_option('pwa_enable') == 1): ?> selected <?php endif; ?>><?php echo e(__('Active')); ?></option>
                                        <option value="0" <?php if(get_option('pwa_enable') != 1): ?> selected <?php endif; ?>><?php echo e(__('Disable')); ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="row input__group mb-25">
                                <label class="col-lg-3"><?php echo e(__('Sign up Left Text')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input type="text" name="sign_up_left_text" value="<?php echo e(get_option('sign_up_left_text')); ?>" class="form-control" >
                                </div>
                            </div>


                            <div class="row input__group mb-25">
                                <label for="enable_email_notification" class="col-lg-3"><?php echo e(__('Enable Email Notification')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <select name="enable_email_notification" class="form-control">
                                        <option value=""><?php echo e(__('Select Option')); ?></option>
                                        <option value="1" <?php if(get_option('enable_email_notification') == 1): ?> selected <?php endif; ?>><?php echo e(__('Active')); ?></option>
                                        <option value="0" <?php if(get_option('enable_email_notification') != 1): ?> selected <?php endif; ?>><?php echo e(__('Disable')); ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="row input__group mb-25">
                                <label for="allow_preloader" class="col-lg-3"><?php echo e(__('Registration Email Verification')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <select name="registration_email_verification" class="form-control">
                                        <option value=""><?php echo e(__('Select Option')); ?></option>
                                        <option value="1" <?php if(get_option('registration_email_verification') == 1): ?> selected <?php endif; ?>><?php echo e(__('Active')); ?></option>
                                        <option value="0" <?php if(get_option('registration_email_verification') != 1): ?> selected <?php endif; ?>><?php echo e(__('Disable')); ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="row input__group mb-25">
                                <label class="col-lg-3"><?php echo e(__('Sign up Left Image')); ?> </label>
                                <div class="col-lg-4">
                                    <div class="upload-img-box">
                                        <?php if(get_option('sign_up_left_image') != ''): ?>
                                            <img src="<?php echo e(getImageFile(get_option('sign_up_left_image'))); ?>">
                                        <?php else: ?>
                                            <img src="<?php echo e(asset('uploads/default/no-image-found.png')); ?>">
                                        <?php endif; ?>
                                        <input type="file" name="sign_up_left_image" id="sign_up_left_image" accept="image/*" onchange="previewFile(this)">
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                        </div>
                                    </div>
                                    <?php if($errors->has('sign_up_left_image')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('sign_up_left_image')); ?></span>
                                    <?php endif; ?>
                                    <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> PNG, SVG <br> <span class="text-black"><?php echo e(__('Recommend Size')); ?>:</span> 800 x 540 (1MB)</p>
                                </div>
                            </div>

                            <div class="row input__group mb-25">
                                <label class="col-lg-3"><?php echo e(__('Forgot Title')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input type="text" name="forgot_title" value="<?php echo e(get_option('forgot_title')); ?>" class="form-control" >
                                </div>
                            </div>
                            <div class="row input__group mb-25">
                                <label class="col-lg-3"><?php echo e(__('Forgot Subtitle')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input type="text" name="forgot_subtitle" value="<?php echo e(get_option('forgot_subtitle')); ?>" class="form-control" >
                                </div>
                            </div>
                            <div class="row input__group mb-25">
                                <label class="col-lg-3"><?php echo e(__('Forgot Button Name')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input type="text" name="forgot_btn_name" value="<?php echo e(get_option('forgot_btn_name')); ?>" class="form-control" >
                                </div>
                            </div>
                            <div class="row input__group mb-25">
                                <label class="col-lg-3"><?php echo e(__('Footer Quote')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <textarea class="form-control" name="footer_quote" id="" rows="5"><?php echo e(get_option('footer_quote')); ?></textarea>
                                </div>
                            </div>

                            <hr>

                            <div class="item-top mb-30"><h2><?php echo e(__('Social Media Profile Link')); ?></h2></div>
                            <div class="row input__group mb-25">
                                <label class="col-lg-3"><?php echo e(__('Facebook URL')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input type="text" name="facebook_url" value="<?php echo e(get_option('facebook_url')); ?>" class="form-control">
                                </div>
                            </div>
                            <div class="row input__group mb-25">
                                <label class="col-lg-3"><?php echo e(__('Twitter URL')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input type="text" name="twitter_url" value="<?php echo e(get_option('twitter_url')); ?>" class="form-control">
                                </div>
                            </div>
                            <div class="row input__group mb-25">
                                <label class="col-lg-3"><?php echo e(__('LinkedIn URL')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input type="text" name="linkedin_url" value="<?php echo e(get_option('linkedin_url')); ?>" class="form-control">
                                </div>
                            </div>
                            <div class="row input__group mb-25">
                                <label class="col-lg-3"><?php echo e(__('Pinterest URL')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input type="text" name="pinterest_url" value="<?php echo e(get_option('pinterest_url')); ?>" class="form-control">
                                </div>
                            </div>
                            <div class="row input__group mb-25">
                                <label class="col-lg-3"><?php echo e(__('Instagram URL')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input type="text" name="instagram_url" value="<?php echo e(get_option('instagram_url')); ?>" class="form-control">
                                </div>
                            </div>

                            <div class="row input__group mb-25">
                                <label class="col-lg-3"><?php echo e(__('Tiktok URL')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input type="text" name="tiktok_url" value="<?php echo e(get_option('tiktok_url')); ?>" class="form-control">
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


<?php $__env->startPush('style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/custom/image-preview.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script src="<?php echo e(asset('admin/js/custom/image-preview.js')); ?>"></script>
      <script>
            
            $(document).ready(function () {
                $('#emails_selectpicker').select2({
                    width: '100%',
                    // allowClear: true,
                    placeholder: 'Select Email',
                    
                }); 
            });



            </script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/general/general-settings.blade.php ENDPATH**/ ?>
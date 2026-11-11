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
                    <div class="email-inbox__area bg-style">
                        <div class="item-top mb-30"><h2><?php echo e(__(@$title)); ?></h2></div>
                        <form action="<?php echo e(route('settings.social-login-settings.update')); ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <div class="item-top mb-30"><h6><?php echo e(__('Google Credentials')); ?></h6></div>
                            <div class="form-group text-black row mb-3">
                                <label class="col-lg-3"><?php echo e(__('Google Login Status')); ?></label>
                                <div class="col-lg-9">
                                    <select name="GOOGLE_LOGIN_STATUS" id="" class="form-control">
                                        <option value="">--<?php echo e(__('Select option')); ?>--</option>
                                        <option value="1" <?php if(env('GOOGLE_LOGIN_STATUS') == 1): ?> selected <?php endif; ?>><?php echo e(__('Active')); ?></option>
                                        <option value="0" <?php if(env('GOOGLE_LOGIN_STATUS') != 1): ?> selected <?php endif; ?>><?php echo e(__('Deactivated')); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label class="col-lg-3"><?php echo e(__('Google Client ID')); ?></label>
                                <div class="col-lg-9">
                                    <input type="text" name="GOOGLE_CLIENT_ID" value="<?php echo e(env('GOOGLE_CLIENT_ID')); ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label class="col-lg-3"><?php echo e(__('Google Client Secret')); ?> </label>
                                <div class="col-lg-9">
                                    <input type="text" name="GOOGLE_CLIENT_SECRET" value="<?php echo e(env('GOOGLE_CLIENT_SECRET')); ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label class="col-lg-3"><?php echo e(__('Google Redirect URL')); ?> </label>
                                <div class="col-lg-9">
                                    <input type="text" name="GOOGLE_REDIRECT_URL" value="<?php echo e(env('GOOGLE_REDIRECT_URL')); ?>" class="form-control">
                                </div>
                            </div>
                            <hr>
                            <div class="item-top mb-30"><h6><?php echo e(__('Facebook Credentials')); ?></h6></div>
                            <div class="form-group text-black row mb-3">
                                <label class="col-lg-3"><?php echo e(__('Facebook Login Status')); ?></label>
                                <div class="col-lg-9">
                                    <select name="FACEBOOK_LOGIN_STATUS" id="" class="form-control">
                                        <option value="">--<?php echo e(__('Select option')); ?>--</option>
                                        <option value="1" <?php if(env('FACEBOOK_LOGIN_STATUS') == 1): ?> selected <?php endif; ?>><?php echo e(__('Active')); ?></option>
                                        <option value="0" <?php if(env('FACEBOOK_LOGIN_STATUS') != 1): ?> selected <?php endif; ?>><?php echo e(__('Deactivated')); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label class="col-lg-3"><?php echo e(__('Facebook Client ID')); ?></label>
                                <div class="col-lg-9">
                                    <input type="text" name="FACEBOOK_CLIENT_ID" value="<?php echo e(env('FACEBOOK_CLIENT_ID')); ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label class="col-lg-3"><?php echo e(__('Facebook Client Secret')); ?> </label>
                                <div class="col-lg-9">
                                    <input type="text" name="FACEBOOK_CLIENT_SECRET" value="<?php echo e(env('FACEBOOK_CLIENT_SECRET')); ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label class="col-lg-3"><?php echo e(__('Facebook Redirect URL')); ?> </label>
                                <div class="col-lg-9">
                                    <input type="text" name="FACEBOOK_REDIRECT_URL" value="<?php echo e(env('FACEBOOK_REDIRECT_URL')); ?>" class="form-control">
                                </div>
                            </div>
                            <hr>
                            <div class="item-top mb-30"><h6><?php echo e(__('Twitter Credentials')); ?></h6></div>
                            <div class="form-group text-black row mb-3">
                                <label class="col-lg-3"><?php echo e(__('Twitter Login Status')); ?> </label>
                                <div class="col-lg-9">
                                    <select name="TWITTER_LOGIN_STATUS" id="" class="form-control">
                                        <option value="">--<?php echo e(__('Select option')); ?>--</option>
                                        <option value="1" <?php if(env('TWITTER_LOGIN_STATUS') == 1): ?> selected <?php endif; ?>><?php echo e(__('Active')); ?></option>
                                        <option value="0" <?php if(env('TWITTER_LOGIN_STATUS') != 1): ?> selected <?php endif; ?>><?php echo e(__('Deactivated')); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label class="col-lg-3"><?php echo e(__('Twitter Client ID')); ?></label>
                                <div class="col-lg-9">
                                    <input type="text" name="TWITTER_CLIENT_ID" value="<?php echo e(env('TWITTER_CLIENT_ID')); ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label class="col-lg-3"><?php echo e(__('Twitter Client Secret')); ?> </label>
                                <div class="col-lg-9">
                                    <input type="text" name="TWITTER_CLIENT_SECRET" value="<?php echo e(env('TWITTER_CLIENT_SECRET')); ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label class="col-lg-3"><?php echo e(__('Twitter Redirect URL')); ?> </label>
                                <div class="col-lg-9">
                                    <input type="text" name="TWITTER_REDIRECT_URL" value="<?php echo e(env('TWITTER_REDIRECT_URL')); ?>" class="form-control">
                                </div>
                            </div>

                            <div class="mb-20 row text-end">
                                <div class="col">
                                    <button type="submit" class="btn btn-blue float-right"><?php echo e(__('Update')); ?></button>
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


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/general/social-login-settings.blade.php ENDPATH**/ ?>
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
                    <div class="row">
                        <div class="col-md-12">
                            <div class="email-inbox__area bg-style">
                                <div class="item-top mb-30"><h2><?php echo e(__("System Font")); ?></h2></div>
                                <form action="<?php echo e(route('settings.general_setting.cms.update')); ?>" method="post" class="form-horizontal">
                                    <?php echo csrf_field(); ?>
        
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <label><?php echo e(__('Design')); ?> <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-lg-9 mb-15">
                                            <input type="radio" id="default" name="app_font_design_type" value="1"
                                                   <?php echo e((empty(get_option('app_font_design_type')) || get_option('app_font_design_type')) ? 'checked' : ''); ?> required>
                                            <label for="default"><?php echo e(__('Default')); ?></label><br>
                                            <input type="radio" id="custom" name="app_font_design_type" value="2" <?php echo e(get_option('app_font_design_type') == 2 ? 'checked' : ''); ?>>
                                            <label for="custom"><?php echo e(__('Custom')); ?></label><br>
                                        </div>
                                    </div>
                                    <div class="customDiv">
                                        <div class="bg-dark-primary-soft-varient p-3">
                                            <p><b>Note: </b>Please choose/Use the same type of font for the best performance. Because every font-weight is not the same, in that case, if you use other fonts (That font-weight is different) sometimes it causes some minor problems. </p>
                                            <p>When you add font. Please add every possible font-weight is available of this specific font. As if, your app will be looking cool.</p>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <label><?php echo e(__('Font Family')); ?> </label>
                                            </div>
                                            <div class="col-lg-9 mb-15">
                                                <input type="text" name="app_font_family" class="color-picker w-100" placeholder="'Jost', sans-serif"
                                                       value="<?php echo e(get_option('app_font_family')); ?>">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <label><?php echo e(__('Font Link')); ?> </label>
                                            </div>
                                            <div class="col-lg-9 mb-15">
                                                <input type="text" name="app_font_link" class="color-picker w-100"
                                                       value="<?php echo e(get_option('app_font_link')); ?>">
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
                        <div class="col-md-12">
                            <div class="email-inbox__area bg-style">
                                <div class="item-top mb-30"><h2><?php echo e(__("Certificate Font")); ?></h2></div>
                                <form action="<?php echo e(route('settings.general_setting.cms.update')); ?>" enctype="multipart/form-data" method="post" class="form-horizontal">
                                    <?php echo csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <label><?php echo e(__('Certificate font')); ?> <span class="text-danger">*</span> </label>
                                        </div>
                                        <div class="col-lg-9 mb-15">
                                            <input type="file" class="form-control" required name="certificate_font" value="<?php echo e(get_option('certificate_font')); ?>">
                                            <?php if(!empty(get_option('certificate_font'))): ?>
                                            <span><a target="_blank" href="<?php echo e(getVideoFile(get_option('certificate_font'))); ?>">View</a></span>
                                            <?php endif; ?>
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
        </div>
    </div>
    <!-- Page content area end -->
<?php $__env->stopSection(); ?>

<?php $__env->startPush('style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('frontend/assets/css/for-certificate.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>

    <script src="<?php echo e(asset('frontend/assets/js/color.js')); ?>"></script>

    <script>
        "use strict"

        $(function () {
            var app_font_design_type = "<?php echo e(empty(get_option('app_font_design_type')) ? 1 : get_option('app_font_design_type')); ?>";
            appDesignType(app_font_design_type)
        })
        $("input[name='app_font_design_type']").click(function () {
            var app_font_design_type = $("input[name='app_font_design_type']:checked").val();
            appDesignType(app_font_design_type)
        });

        function appDesignType(app_font_design_type) {
            if (app_font_design_type == 1) {
                $('.customDiv').addClass('d-none')
            } else {
                $('.customDiv').removeClass('d-none')
            }
        }

    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/general/font-settings.blade.php ENDPATH**/ ?>
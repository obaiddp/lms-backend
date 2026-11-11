<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Home Settings')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item"><a><?php echo e(__('Application Setting')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__(@$title)); ?></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-4">
                    <?php echo $__env->make('admin.application_settings.home-sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
                <div class="col-lg-9 col-md-8">
                    <div class="customers__area bg-style mb-30">
                        <div class="item-title d-flex justify-content-between">
                            <h2><?php echo e(__('Theme Settings')); ?></h2>
                        </div>
                        <form action="<?php echo e(route('settings.save.setting')); ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <div class="row">
                                <div class="bg-dark-secondary-soft-varient col-12 mb-24 py-2 rounded text-body text-center">
                                    <small><?php echo e(__('You need to adjust the homepage data from `Application Setting/Home Settings` to get the best view.')); ?></small>
                                </div>
                                <div class="col-lg-6 col-sm-12 mb-24">
                                    <div class="input__group mb-25">
                                        <label for="name"><?php echo e(__('Choose Theme')); ?></label>
                                        <select name="theme" id="theme" required>
                                            <?php $__currentLoopData = getThemes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $theme): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($index); ?>" <?php echo e($index == get_option('theme', THEME_DEFAULT) ? 'selected' : ''); ?>><?php echo e($theme); ?> </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <?php if($errors->has('theme')): ?>
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('theme')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php echo $__env->make('layouts.element.form.update-button', [], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </div>
                                <div class="col-lg-6 col-sm-12 h-500 overflow-auto overflow-y-auto">
                                    <img class="theme-img <?php echo e(get_option('theme', THEME_DEFAULT) == THEME_DEFAULT ? '' : 'd-none'); ?>" id="theme-<?php echo e(THEME_DEFAULT); ?>" src="<?php echo e(asset('admin/images/background/theme-1.jpg')); ?>" alt="">
                                    <img class="theme-img <?php echo e(get_option('theme', THEME_DEFAULT) == THEME_TWO ? '' : 'd-none'); ?>" id="theme-<?php echo e(THEME_TWO); ?>" src="<?php echo e(asset('admin/images/background/theme-2.jpg')); ?>" alt="">
                                    <img class="theme-img <?php echo e(get_option('theme', THEME_DEFAULT) == THEME_THREE ? '' : 'd-none'); ?>" id="theme-<?php echo e(THEME_THREE); ?>" src="<?php echo e(asset('admin/images/background/theme-3.jpg')); ?>" alt="">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12 text-right">
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
    <script>
        $(document).on('change', '#theme', function(){
            $('.theme-img').addClass('d-none');
            $('#theme-'+$(this).val()).removeClass('d-none');
        });
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/home/theme-settings.blade.php ENDPATH**/ ?>
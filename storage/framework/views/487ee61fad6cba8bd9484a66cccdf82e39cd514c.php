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
                    <div class="email-inbox__area bg-style admin-special-feature-section-page">
                        <div class="item-top mb-30"><h2><?php echo e(__(@$title)); ?></h2></div>
                        <form action="<?php echo e(route('settings.general_setting.cms.update')); ?>" method="post" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <?php if(get_option('theme', THEME_DEFAULT) != THEME_DEFAULT): ?>
                            <div class="row">
                                <div class="custom-form-group mb-3 col-md-12 col-lg-4 col-xl-4 col-xxl-4">
                                    <label for="home_special_feature_title" class="text-lg-right text-black"> <?php echo e(__('Feature Section Title')); ?></label>
                                    <input type="text" name="home_special_feature_title" id="home_special_feature_title"
                                           value="<?php echo e(get_option('home_special_feature_title')); ?>" class="form-control"
                                               placeholder="<?php echo e(__('Type title')); ?>" required>

                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="custom-form-group mb-3 col-md-12 col-lg-3 col-xl-3 col-xxl-2">
                                    <label for="home_special_feature_first_logo" class=" text-lg-right text-black"> <?php echo e(__('First Logo')); ?> </label>
                                    <div class="upload-img-box">
                                        <?php if(get_option('home_special_feature_first_logo') != ''): ?>
                                            <img src="<?php echo e(getImageFile(get_option('home_special_feature_first_logo'))); ?>">
                                        <?php else: ?>
                                            <img src="">
                                        <?php endif; ?>
                                        <input type="file" name="home_special_feature_first_logo" id="logo" accept="image/*" onchange="previewFile(this)">
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                            <p class="m-0"><?php echo e(__('Logo')); ?></p>
                                        </div>
                                    </div>
                                    <?php if($errors->has('home_special_feature_first_logo')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('home_special_feature_first_logo')); ?></span>
                                    <?php endif; ?>
                                    <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> PNG <br> <span class="text-black"><?php echo e(__('Accepted Size')); ?>:</span> 77 x 77</p>
                                </div>
                                <div class="custom-form-group mb-3 col-md-12 col-lg-4 col-xl-4 col-xxl-4">
                                    <label for="home_special_feature_first_title" class="text-lg-right text-black"> <?php echo e(__('First Title')); ?></label>
                                    <input type="text" name="home_special_feature_first_title" id="home_special_feature_first_title"
                                           value="<?php echo e(get_option('home_special_feature_first_title')); ?>" class="form-control"
                                               placeholder="<?php echo e(__('Type title')); ?>" required>

                                </div>
                                <div class="custom-form-group mb-3 col-md-12 col-lg-5 col-xl-5 col-xxl-6">
                                    <label for="home_special_feature_first_subtitle" class="text-lg-right text-black"> <?php echo e(__('First Subtitle')); ?></label>
                                    <input type="text" name="home_special_feature_first_subtitle" id="home_special_feature_first_subtitle"
                                           value="<?php echo e(get_option('home_special_feature_first_subtitle')); ?>" class="form-control"
                                               placeholder="<?php echo e(__('Type subtitle')); ?>" required>

                                </div>
                            </div>

                            <div class="row">
                                <div class="custom-form-group mb-3 col-md-12 col-lg-3 col-xl-3 col-xxl-2">
                                    <label for="home_special_feature_second_logo" class=" text-lg-right text-black"> <?php echo e(__('Second Logo')); ?> </label>
                                    <div class="upload-img-box">
                                        <?php if(get_option('home_special_feature_second_logo') != ''): ?>
                                            <img src="<?php echo e(getImageFile(get_option('home_special_feature_second_logo'))); ?>">
                                        <?php else: ?>
                                            <img src="">
                                        <?php endif; ?>
                                        <input type="file" name="home_special_feature_second_logo" id="home_special_feature_second_logo" accept="image/*" onchange="previewFile(this)">
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                            <p class="m-0">Logo</p>
                                        </div>
                                    </div>
                                    <?php if($errors->has('home_special_feature_second_logo')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('home_special_feature_second_logo')); ?></span>
                                    <?php endif; ?>
                                    <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> PNG <br> <span class="text-black"><?php echo e(__('Accepted Size')); ?>:</span> 77 x 77</p>
                                </div>
                                <div class="custom-form-group mb-3 col-md-12 col-lg-4 col-xl-4 col-xxl-4">
                                    <label for="home_special_feature_second_title" class="text-lg-right text-black"> <?php echo e(__('Second Title')); ?></label>
                                    <input type="text" name="home_special_feature_second_title" id="home_special_feature_second_title"
                                           value="<?php echo e(get_option('home_special_feature_second_title')); ?>" class="form-control"
                                               placeholder="<?php echo e(__('Type title')); ?>" required>

                                </div>
                                <div class="custom-form-group mb-3 col-md-12 col-lg-5 col-xl-5 col-xxl-6">
                                    <label for="home_special_feature_second_subtitle" class="text-lg-right text-black"> <?php echo e(__('Second Subtitle')); ?></label>
                                    <input type="text" name="home_special_feature_second_subtitle" id="home_special_feature_second_subtitle"
                                           value="<?php echo e(get_option('home_special_feature_second_subtitle')); ?>" class="form-control"
                                               placeholder="<?php echo e(__('Type subtitle')); ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="custom-form-group mb-3 col-md-12 col-lg-3 col-xl-3 col-xxl-2">
                                    <label for="home_special_feature_third_logo" class=" text-lg-right text-black"> <?php echo e(__('Third Logo')); ?> </label>
                                    <div class="upload-img-box">
                                        <?php if(get_option('home_special_feature_third_logo') != ''): ?>
                                            <img src="<?php echo e(getImageFile(get_option('home_special_feature_third_logo'))); ?>">
                                        <?php else: ?>
                                            <img src="">
                                        <?php endif; ?>
                                        <input type="file" name="home_special_feature_third_logo" id="home_special_feature_third_logo" accept="image/*" onchange="previewFile(this)">
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                            <p class="m-0"><?php echo e(__('Logo')); ?></p>
                                        </div>
                                    </div>
                                    <?php if($errors->has('home_special_feature_third_logo')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('home_special_feature_third_logo')); ?></span>
                                    <?php endif; ?>
                                    <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> PNG <br> <span class="text-black"><?php echo e(__('Accepted Size')); ?>:</span> 77 x 77</p>
                                </div>
                                <div class="custom-form-group mb-3 col-md-12 col-lg-4 col-xl-4 col-xxl-4">
                                    <label for="home_special_feature_third_title" class="text-lg-right text-black"> <?php echo e(__('Third Title')); ?></label>
                                    <input type="text" name="home_special_feature_third_title" id="home_special_feature_third_title"
                                           value="<?php echo e(get_option('home_special_feature_third_title')); ?>" class="form-control"
                                           placeholder="<?php echo e(__('Type title')); ?>" required>

                                </div>
                                <div class="custom-form-group mb-3 col-md-12 col-lg-5 col-xl-5 col-xxl-6">
                                    <label for="home_special_feature_thirdsubtitle" class="text-lg-right text-black"> <?php echo e(__('Third Subtitle')); ?></label>
                                    <input type="text" name="home_special_feature_third_subtitle" id="home_special_feature_third_subtitle"
                                           value="<?php echo e(get_option('home_special_feature_third_subtitle')); ?>" class="form-control"
                                           placeholder="<?php echo e(__('Type subtitle')); ?>" required>
                                </div>
                            </div>


                            <div class="row justify-content-end">
                                <div class="col-md-2 text-right ">
                                    <?php echo $__env->make('layouts.element.form.update-button', [], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/home/special-feature-section.blade.php ENDPATH**/ ?>
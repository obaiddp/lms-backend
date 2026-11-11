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
                    <div class="email-inbox__area bg-style">
                        <div class="item-top mb-30"><h2><?php echo e(__(@$title)); ?></h2></div>
                        <form action="<?php echo e(route('settings.general_setting.cms.update')); ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <div class="form-group text-black row mb-3">
                                <label for="course_logo" class="col-lg-3"><?php echo e(__('Logo')); ?></label>
                                <div class="col-lg-4">
                                    <div class="upload-img-box">
                                        <?php if(get_option('course_logo') != ''): ?>
                                            <img src="<?php echo e(getImageFile(get_option('course_logo'))); ?>">
                                        <?php else: ?>
                                            <img src="">
                                        <?php endif; ?>
                                        <input type="file" name="course_logo" id="course_logo" accept="image/*" onchange="previewFile(this)">
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                            <p class="m-0"><?php echo e(__('Logo')); ?></p>
                                        </div>
                                    </div>
                                    <?php if($errors->has('course_logo')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('course_logo')); ?></span>
                                    <?php endif; ?>
                                    <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> PNG <br> <span class="text-black"><?php echo e(__('Accepted Size')); ?>:</span> 60 x 60</p>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label for="course_title" class="col-lg-3"><?php echo e(__('Course Title')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input type="text" name="course_title" id="course_title" value="<?php echo e(get_option('course_title')); ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label for="course_subtitle" class="col-lg-3"><?php echo e(__('Course Subtitle')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input type="text" name="course_subtitle" id="course_subtitle" value="<?php echo e(get_option('course_subtitle')); ?>" class="form-control" required>
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/home/course-section.blade.php ENDPATH**/ ?>
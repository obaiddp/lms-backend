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
                    <div class="email-inbox__area bg-style admin-become-instructor-video">
                        <div class="item-top mb-30"><h2><?php echo e(__(@$title)); ?></h2></div>
                        <form action="<?php echo e(route('settings.general_setting.cms.update')); ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <div class="form-group text-black row mb-3">
                                <label for="become_instructor_video_logo" class="col-lg-3"><?php echo e(__('Video')); ?></label>
                                <div class="col-lg-4 col-xl-4 col-xxl-3">
                                    <input type="file" name="become_instructor_video" id="become_instructor_video" accept="video/mp4">
                                    <div class="video-area-left position-relative d-flex align-items-center justify-content-center mt-2">
                                        <img src="<?php echo e(asset(get_option('become_instructor_video_preview_image'))); ?>" alt="video" class="img-fluid">
                                        <a class="play-btn position-absolute venobox vbox-item" data-autoplay="true" data-maxwidth="800px"
                                           data-vbtype="video" href="<?php echo e(getVideoFile(get_option('become_instructor_video'))); ?>">
                                            <img src="<?php echo e(asset('frontend/assets/img/icons-svg/play.svg')); ?>" alt="play">
                                        </a>
                                    </div>

                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label for="become_instructor_video_preview_image" class="col-lg-3"><?php echo e(__('Video Preview Image')); ?></label>
                                <div class="col-lg-4 col-xl-4 col-xxl-3">
                                    <div class="upload-img-box">
                                        <?php if(get_option('become_instructor_video_preview_image') != ''): ?>
                                            <img src="<?php echo e(getImageFile(get_option('become_instructor_video_preview_image'))); ?>">
                                        <?php else: ?>
                                            <img src="" alt="img">
                                        <?php endif; ?>
                                        <input type="file" name="become_instructor_video_preview_image" id="become_instructor_video_preview_image" accept="image/*" onchange="previewFile(this)">
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                            <p class="m-0"><?php echo e(__('Preview Image')); ?></p>
                                        </div>
                                    </div>
                                    <?php if($errors->has('become_instructor_video_preview_image')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('become_instructor_video_preview_image')); ?></span>
                                    <?php endif; ?>
                                    <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> PNG <br> <span class="text-black"><?php echo e(__('Accepted Size')); ?>:</span> 835 x 630</p>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label for="become_instructor_video_logo" class="col-lg-3"><?php echo e(__('Logo')); ?></label>
                                <div class="col-lg-4 col-xl-4 col-xxl-3">
                                    <div class="upload-img-box">
                                        <?php if(get_option('become_instructor_video_logo') != ''): ?>
                                            <img src="<?php echo e(getImageFile(get_option('become_instructor_video_logo'))); ?>">
                                        <?php else: ?>
                                            <img src="" alt="img">
                                        <?php endif; ?>
                                        <input type="file" name="become_instructor_video_logo" id="become_instructor_video_logo" accept="image/*" onchange="previewFile(this)">
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                            <p class="m-0"><?php echo e(__('Logo')); ?></p>
                                        </div>
                                    </div>
                                    <?php if($errors->has('become_instructor_video_logo')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('become_instructor_video_logo')); ?></span>
                                    <?php endif; ?>
                                    <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> PNG <br> <span class="text-black"><?php echo e(__('Accepted Size')); ?>:</span> 70 x 70</p>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label for="become_instructor_video_title" class="col-lg-3"><?php echo e(__('Title')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input type="text" name="become_instructor_video_title" id="become_instructor_video_title" value="<?php echo e(get_option('become_instructor_video_title')); ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label for="become_instructor_video_subtitle" class="col-lg-3"><?php echo e(__('Subtitle')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <textarea name="become_instructor_video_subtitle" id="" rows="5" class="form-control"><?php echo e(get_option('become_instructor_video_subtitle')); ?></textarea>
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/home/become-instructor-video-section.blade.php ENDPATH**/ ?>
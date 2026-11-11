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
                        <form action="<?php echo e(route('settings.about.gallery-area.update')); ?>" method="post" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <div class="custom-form-group mb-3 row">
                                <label for="gallery_area_title" class="col-lg-3 text-lg-right text-black"> <?php echo e(__('Gallery Area Title')); ?> </label>
                                <div class="col-lg-9">
                                    <input type="text" name="gallery_area_title" id="gallery_area_title" value="<?php echo e(@$aboutUsGeneral->gallery_area_title); ?>"
                                           class="form-control" placeholder="<?php echo e(__('Type gallery area title')); ?>">
                                    <?php if($errors->has('gallery_area_title')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('gallery_area_title')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="custom-form-group mb-3 row">
                                <label for="gallery_area_subtitle" class="col-lg-3 text-lg-right text-black"> <?php echo e(__('Gallery Area Subtitle')); ?> </label>
                                <div class="col-lg-9">
                                    <textarea name="gallery_area_subtitle" class="form-control" rows="5" id="gallery_area_subtitle"><?php echo e(@$aboutUsGeneral->gallery_area_subtitle); ?></textarea>

                                    <?php if($errors->has('gallery_area_subtitle')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('gallery_area_subtitle')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="custom-form-group mb-3 row">
                                <label class="col-lg-3"><?php echo e(__('Gallery First Image')); ?></label>
                                <div class="col-lg-5">
                                    <div class="upload-img-box">
                                        <?php if( @$aboutUsGeneral->gallery_first_image): ?>
                                            <img src="<?php echo e(getImageFile(@$aboutUsGeneral->gallery_first_image)); ?>">
                                        <?php else: ?>
                                            <img src="" alt="img">
                                        <?php endif; ?>
                                        <input type="file" name="gallery_first_image" id="gallery_first_image" accept="image/*" onchange="previewFile(this)">
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                            <p class="m-0"><?php echo e(__('Image')); ?></p>
                                        </div>
                                    </div>
                                    <?php if($errors->has('gallery_first_image')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('gallery_first_image')); ?></span>
                                    <?php endif; ?>
                                    <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> JPG <br> <span class="text-black"><?php echo e(__('Accepted Size')); ?>:</span> 536 x 644 (1MB)</p>
                                </div>
                            </div>

                            <div class="custom-form-group mb-3 row">
                                <label class="col-lg-3"><?php echo e(__('Gallery Second Image')); ?></label>
                                <div class="col-lg-5">
                                    <div class="upload-img-box">
                                        <?php if( @$aboutUsGeneral->gallery_second_image): ?>
                                            <img src="<?php echo e(getImageFile(@$aboutUsGeneral->gallery_second_image)); ?>">
                                        <?php else: ?>
                                            <img src="" alt="img">
                                        <?php endif; ?>
                                        <input type="file" name="gallery_second_image" id="gallery_second_image" accept="image/*" onchange="previewFile(this)">
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                            <p class="m-0"><?php echo e(__('Image')); ?></p>
                                        </div>
                                    </div>
                                    <?php if($errors->has('gallery_second_image')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('gallery_second_image')); ?></span>
                                    <?php endif; ?>
                                    <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> JPG <br> <span class="text-black"><?php echo e(__('Accepted Size')); ?>:</span> 536 x 309 (1MB)</p>
                                </div>
                            </div>

                            <div class="custom-form-group mb-3 row">
                                <label class="col-lg-3"><?php echo e(__('Gallery Third Image')); ?></label>
                                <div class="col-lg-5">
                                    <div class="upload-img-box">
                                        <?php if( @$aboutUsGeneral->gallery_third_image): ?>
                                            <img src="<?php echo e(getImageFile(@$aboutUsGeneral->gallery_third_image)); ?>">
                                        <?php else: ?>
                                            <img src="" alt="img">
                                        <?php endif; ?>
                                        <input type="file" name="gallery_third_image" id="gallery_third_image" accept="image/*" onchange="previewFile(this)">
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                            <p class="m-0"><?php echo e(__('Image')); ?></p>
                                        </div>
                                    </div>
                                    <?php if($errors->has('gallery_third_image')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('gallery_third_image')); ?></span>
                                    <?php endif; ?>
                                    <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> JPG <br> <span class="text-black"><?php echo e(__('Accepted Size')); ?>:</span> 536 x 309 (1MB)</p>
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/about/gallery-area.blade.php ENDPATH**/ ?>
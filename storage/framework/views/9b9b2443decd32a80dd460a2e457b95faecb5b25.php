<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Add Language')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('language.index')); ?>"><?php echo e(__('Language Settings')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Add Language')); ?></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-vertical__item bg-style">
                        <div class="item-top mb-30">
                            <h2><?php echo e(__('Add Language')); ?></h2>
                        </div>
                        <form action="<?php echo e(route('language.store')); ?>" method="post" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <div class="input__group mb-25">
                                <label for="language"> <?php echo e(__('Name')); ?> </label>
                                <div>
                                    <input type="text" name="language" id="language" value="<?php echo e(old('language')); ?>" class="form-control flat-input" placeholder=" <?php echo e(__('Name')); ?> ">
                                    <?php if($errors->has('language ')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('language')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="input__group mb-25">
                                <label for="iso_code"> <?php echo e(__('ISO Code')); ?> </label>
                                <div>
                                    <input type="text" name="iso_code" id="iso_code" value="<?php echo e(old('iso_code')); ?>" class="form-control flat-input" placeholder=" <?php echo e(__('ISO Code')); ?> ">
                                    <?php if($errors->has('iso_code')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('iso_code')); ?></span>
                                    <?php endif; ?>
                                    <div class="mt-5">
                                        <span class="status blocked">Note: You can't update it.</span>
                                    </div>
                                    <div class="mt-3">
                                        <a href="https://en.wikipedia.org/wiki/ISO_3166-1#Current_codes" target="_blank"><b><i class="fa fa-list mr-1"></i> <?php echo e(__('ISO Code List')); ?></b></a>
                                    </div>

                                </div>
                            </div>


                            <div class="input__group mb-25">
                                <label for="flag" class="col-lg-3 text-lg-right text-black"> <?php echo e(__('Flag')); ?> </label>
                                <div class="col-lg-3">
                                    <div class="upload-img-box">
                                        <img src="<?php echo e(getImageFile('uploads/default/no-image-found.png')); ?>">
                                        <input type="file" name="flag" id="flag" accept="image/*" onchange="previewFile(this)">

                                    </div>
                                </div>
                            </div>

                            <div class="custom-form-group mb-3 row">
                                <label for="rtl" class="col-lg-1 text-lg-right text-black"> <?php echo e(__('RTL Support')); ?></label>
                                <div class="col-lg-1">
                                    <input type="checkbox" name="rtl" value="1" id="rtl">
                                </div>
                            </div>

                            <div class="input__group">
                                <div class="input__button ">
                                    <button type="submit" class="btn btn-blue"><?php echo e(__('Save')); ?></button>
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/language/create.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Profile')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Update Profile')); ?></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="customers__area bg-style mb-30">
                        <div class="item-title d-flex justify-content-between">
                            <h2><?php echo e(__('Update Profile')); ?></h2>
                        </div>
                        <form action="<?php echo e(route('admin.profile.update')); ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>

                            <div class="row">
                                <div class="col-md-9">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input__group mb-25">
                                                <label for="name"><?php echo e(__('Name')); ?> <span class="text-danger">*</span></label>
                                                <input type="text" name="name" id="name" value="<?php echo e(auth::user()->name); ?>" placeholder="<?php echo e(__('Name')); ?>" class="form-control">
                                                <?php if($errors->has('name')): ?>
                                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('name')); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input__group mb-25">
                                                <label for="phone_number"><?php echo e(__('Phone Number')); ?> <span class="text-danger">*</span></label>
                                                <input type="text" name="phone_number" id="phone_number" value="<?php echo e(auth::user()->phone_number); ?>" placeholder="<?php echo e(__('Phone Number')); ?>" class="form-control">
                                                <?php if($errors->has('phone_number')): ?>
                                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('phone_number')); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input__group mb-25">
                                                <label for="address"><?php echo e(__('Email')); ?> <span class="text-danger">*</span></label>
                                                <input type="text" name="email" id="email" value="<?php echo e(auth::user()->email); ?>" placeholder="<?php echo e(__('Email')); ?>" class="form-control">
                                                <?php if($errors->has('email')): ?>
                                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('email')); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input__group mb-25">
                                                <label for="address"><?php echo e(__('Address')); ?> <span class="text-danger">*</span></label>
                                                <textarea name="address" id="address" class="form-control" placeholder="<?php echo e(__('Address')); ?>"><?php echo e(auth::user()->address); ?></textarea>
                                                <?php if($errors->has('address')): ?>
                                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('address')); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="upload-img-box mb-25 overflow-hidden">
                                        <?php if(auth()->user()->image): ?>
                                            <img src="<?php echo e(getImageFile(auth()->user()->image)); ?>" alt="img" class="img-fluid">
                                        <?php else: ?>
                                            <img src="" alt="img">
                                        <?php endif; ?>
                                        <input type="file" name="image" id="image" accept="image/*" onchange="previewFile(this)">
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                            <p class="m-0"><?php echo e(__('Image')); ?></p>
                                        </div>
                                    </div>
                                    <p><?php echo e(__('Accepted Image Files')); ?>: JPEG, JPG, PNG <br> <?php echo e(__('Accepted Size')); ?>: 300 x 300 (1MB)</p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/profile/index.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
<!-- Page content area start -->
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb__content">
                    <div class="breadcrumb__content__left">
                        <div class="breadcrumb__title">
                            <h2><?php echo e(__('Add Subscription Package')); ?></h2>
                        </div>
                    </div>
                    <div class="breadcrumb__content__right">
                        <nav aria-label="breadcrumb">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a
                                        href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Add Subscription Package')); ?></li>
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
                        <h2><?php echo e(__('Add Subscription Package')); ?></h2>
                    </div>
                    <form action="<?php echo e(route('admin.subscriptions.store')); ?>" method="post" class="form-horizontal"
                        enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label><?php echo e(__('Title')); ?> <span class="text-danger">*</span></label>
                                    <input type="text" name="title" value="<?php echo e(old('title')); ?>"
                                        placeholder="<?php echo e(__('Title')); ?>" class="form-control" required>
                                    <?php if($errors->has('title')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('title')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label><?php echo e(__('Price Monthly')); ?> <span class="text-danger">*</span></label>
                                    <input type="number" min=0 name="monthly_price" value="<?php echo e(old('monthly_price')); ?>"
                                        placeholder="<?php echo e(__('Price Monthly')); ?>" class="form-control" required>
                                    <?php if($errors->has('monthly_price')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('monthly_price')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label><?php echo e(__('Discounted Price Monthly')); ?> <span class="text-danger">*</span></label>
                                    <input type="number" min=0 name="discounted_monthly_price"
                                        value="<?php echo e(old('discounted_monthly_price')); ?>"
                                        placeholder="<?php echo e(__('Discounted Price Monthly')); ?>" class="form-control"
                                        required>
                                    <?php if($errors->has('discounted_monthly_price')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('discounted_monthly_price')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label><?php echo e(__('Price Yearly')); ?> <span class="text-danger">*</span></label>
                                    <input type="number" min=0 name="yearly_price" value="<?php echo e(old('yearly_price')); ?>"
                                        placeholder="<?php echo e(__('Price Yearly')); ?>" class="form-control" required>
                                    <?php if($errors->has('yearly_price')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('yearly_price')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label><?php echo e(__('Discounted Price Yearly')); ?> <span class="text-danger">*</span></label>
                                    <input type="number" min=0 name="discounted_yearly_price"
                                        value="<?php echo e(old('discounted_yearly_price')); ?>"
                                        placeholder="<?php echo e(__('Discounted Price Yearly')); ?>" class="form-control" required>
                                    <?php if($errors->has('discounted_yearly_price')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('discounted_yearly_price')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label><?php echo e(__('Enroll Course Limit')); ?> <span class="text-danger">*</span></label>
                                    <input type="number" min=0 name="course" value="<?php echo e(old('course', 0)); ?>"
                                        placeholder="<?php echo e(__('Enroll Course Limit')); ?>" class="form-control" required>
                                    <?php if($errors->has('course')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('course')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label><?php echo e(__('Bundle Course Limit')); ?> <span class="text-danger">*</span></label>
                                    <input type="number" min=0 name="bundle_course" value="<?php echo e(old('bundle_course', 0)); ?>"
                                        placeholder="<?php echo e(__('Bundle Course Limit')); ?>" class="form-control" required>
                                    <?php if($errors->has('bundle_course')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('bundle_course')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label><?php echo e(__('Consulaltency Limit')); ?> <span class="text-danger">*</span></label>
                                    <input type="number" min=0 name="consultancy" value="<?php echo e(old('consultancy', 0)); ?>"
                                        placeholder="<?php echo e(__('Consulaltency Limit')); ?>" class="form-control" required>
                                    <?php if($errors->has('consultancy')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('consultancy')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label><?php echo e(__('Device Limit')); ?> <span class="text-danger">*</span></label>
                                    <input type="number" min=1 name="device" value="<?php echo e(old('device', 1)); ?>"
                                        placeholder="<?php echo e(__('Device Limit')); ?>" class="form-control" required>
                                    <?php if($errors->has('device')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('device')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label class="label-text-title color-heading font-medium font-16 mb-3"><?php echo e(__('Show
                                        in Home')); ?> <span class="text-danger">*</span></label>
                                    <select class="form-control" name="in_home">
                                        <option value="<?php echo e(PACKAGE_STATUS_ACTIVE); ?>"><?php echo e(__('YES')); ?></option>
                                        <option value="<?php echo e(PACKAGE_STATUS_DISABLED); ?>"><?php echo e(__('NO')); ?></option>
                                    </select>
                                    <?php if($errors->has('in_home')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('in_home')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label class="label-text-title color-heading font-medium font-16 mb-3"><?php echo e(__('Recomended')); ?> <span class="text-danger">*</span></label>
                                    <select class="form-control" name="recommended">
                                        <option value="<?php echo e(PACKAGE_STATUS_DISABLED); ?>"><?php echo e(__('NO')); ?></option>
                                        <option value="<?php echo e(PACKAGE_STATUS_ACTIVE); ?>"><?php echo e(__('YES')); ?></option>
                                    </select>
                                    <?php if($errors->has('recommended')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('recommended')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label class="label-text-title color-heading font-medium font-16 mb-3"><?php echo e(__('Order')); ?> <span class="text-danger">*</span></label>
                                    <input type="number" min=1 value="1" name="order" required>
                                    <?php if($errors->has('description')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('order')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="input__group mb-25">
                                    <label><?php echo e(__('Description')); ?></label>
                                    <textarea name="description" rows="3"><?php echo e(old('description')); ?></textarea>
                                    <?php if($errors->has('description')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('description')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <label><?php echo e(__('Icon')); ?> <span class="text-danger">*</span></label>
                                <div class="upload-img-box mb-25">
                                    <img src="">
                                    <input type="file" name="icon" id="icon" accept="image/*"
                                        onchange="previewFile(this)">
                                    <div class="upload-img-box-icon">
                                        <i class="fa fa-camera"></i>
                                        <p class="m-0"><?php echo e(__('Image')); ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php if($errors->has('icon')): ?>
                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('icon')); ?></span>
                            <?php endif; ?>
                            <p><?php echo e(__('Accepted Image Files')); ?>: JPEG, JPG, PNG <br> <?php echo e(__('Accepted Size')); ?>: 80 x
                                80 (300KB)</p>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12 text-right">
                                <button class="btn btn-primary" type="submit"><?php echo e(__('Save')); ?></button>
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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/subscriptions/create.blade.php ENDPATH**/ ?>
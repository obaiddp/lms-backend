<?php $__env->startSection('content'); ?>
<!-- Page content area start -->
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb__content">
                    <div class="breadcrumb__content__left">
                        <div class="breadcrumb__title">
                            <h2><?php echo e(__('Add Time Slot')); ?></h2>
                        </div>
                    </div>
                    <div class="breadcrumb__content__right">
                        <nav aria-label="breadcrumb">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a
                                        href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Add Time Slot')); ?></li>
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
                        <h2><?php echo e(__('Add Time Slot')); ?></h2>
                    </div>
                    <form action="<?php echo e(route('time_slots.store')); ?>" method="post" class="form-horizontal"
                        enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            
                            <div class="col-md-6">

                                <div class="input__group mb-25">
                                    <label for="start_time" class="form-label"><?php echo e(__('Start Time')); ?> <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="start_time" name="start_time" required>
                                    <?php if($errors->has('start_time')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('start_time')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-6">

                                <div class="input__group mb-25">
                                    <label for="end_time" class="form-label"><?php echo e(__('End Time')); ?> <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="end_time" name="end_time" required>
                                    <?php if($errors->has('end_time')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('end_time')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" <?php echo e(old('is_active', $timeSlot->is_active ?? true) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="is_active"><?php echo e(__('Active')); ?></label>
                                 
                                    <?php if($errors->has('is_active')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('is_active')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                         
                              
                        </div>
                     
                        <div class="row mb-3">
                            <div class="col-md-12 text-right">
                                <button class="btn btn-primary" type="submit"><?php echo e(__('Save')); ?></button>
                                <a href="<?php echo e(route('time_slots.index')); ?>" class="btn btn-secondary">Cancel</a>
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
<script src="<?php echo e(asset('admin/js/custom/admin-profile.js')); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/time_slots/create.blade.php ENDPATH**/ ?>
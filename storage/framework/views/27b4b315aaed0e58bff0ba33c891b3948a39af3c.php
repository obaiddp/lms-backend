<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Add Promotion')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('promotion.index')); ?>"><?php echo e(__('Promotion List')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Add Promotion')); ?></li>
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
                            <h2><?php echo e(__('Add Promotion')); ?></h2>
                        </div>
                        <form action="<?php echo e(route('promotion.store')); ?>" method="post" class="form-horizontal">
                            <?php echo csrf_field(); ?>
                            <div class="input__group mb-25">
                                <label><?php echo e(__('Name')); ?> <span class="text-danger">*</span></label>
                                <input type="text" name="name" value="<?php echo e(old('name')); ?>" placeholder="<?php echo e(__('Name')); ?>" class="form-control" required>
                                <?php if($errors->has('name')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('name')); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="input__group mb-25">
                                <label><?php echo e(__('Start Date')); ?> <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="start_date" value="<?php echo e(old('start_date')); ?>" placeholder="<?php echo e(__('Start Date')); ?>" class="form-control" required>
                                <?php if($errors->has('start_date')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('start_date')); ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="input__group mb-25">
                                <label><?php echo e(__('End Date')); ?> <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="end_date" value="<?php echo e(old('end_date')); ?>" placeholder="<?php echo e(__('End Date')); ?>" class="form-control" required>
                                <?php if($errors->has('end_date')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('end_date')); ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="input__group mb-25">
                                <label><?php echo e(__('Percentage')); ?> <span class="text-danger">*</span></label>
                                <input type="number" step="any" min="1" name="percentage" value="<?php echo e(old('percentage')); ?>" placeholder="<?php echo e(__('Percentage')); ?>" class="form-control" required>
                                <?php if($errors->has('percentage')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('percentage')); ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="input__group mb-25">
                                <label><?php echo e(__('Status')); ?> <span class="text-danger">*</span></label>
                                <select name="status" id="" required>
                                    <option value="">--<?php echo e(__('Select Option')); ?>--</option>
                                    <option value="1"><?php echo e(__('Active')); ?></option>
                                    <option value="0"><?php echo e(__('Deactivated')); ?></option>
                                </select>
                                <?php if($errors->has('status')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('status')); ?></span>
                                <?php endif; ?>
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/promotion/add.blade.php ENDPATH**/ ?>
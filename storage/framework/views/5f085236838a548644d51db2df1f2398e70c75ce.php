<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Add Coupon')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('coupon.index')); ?>"><?php echo e(__('Coupon List')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Add Coupon')); ?></li>
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
                            <h2><?php echo e(__('Add Coupon')); ?></h2>
                        </div>
                        <form action="<?php echo e(route('coupon.store')); ?>" method="post" class="form-horizontal">
                            <?php echo csrf_field(); ?>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="input__group text-black">
                                        <label><?php echo e(__('Coupon Code Name')); ?> <span class="text-danger">*</span></label>
                                        <input type="text" name="coupon_code_name" value="<?php echo e(old('coupon_code_name')); ?>" placeholder="<?php echo e(__('Type coupon code name')); ?>"
                                               class="form-control" required>
                                        <?php if($errors->has('coupon_code_name')): ?>
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('coupon_code_name')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="input__group text-black">
                                        <label><?php echo e(__('Start Date')); ?> <span class="text-danger">*</span></label>
                                        <input type="date" name="start_date" value="<?php echo e(old('start_date')); ?>" class="form-control" required>
                                        <?php if($errors->has('start_date')): ?>
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('start_date')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="input__group text-black">
                                        <label><?php echo e(__('End Date')); ?> <span class="text-danger">*</span></label>
                                        <input type="date" name="end_date" value="<?php echo e(old('end_date')); ?>" class="form-control" required>
                                        <?php if($errors->has('end_date')): ?>
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('end_date')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="input__group text-black">
                                        <label><?php echo e(__('Coupon Type')); ?> <span class="text-danger">*</span></label>
                                        <select name="coupon_type" id="coupon_type" class="form-control" required>
                                            <option value="">--<?php echo e(__('Select Option')); ?>--</option>
                                            <option value="1"><?php echo e(__('Global')); ?></option>
                                            <option value="2"><?php echo e(__('Instructor')); ?></option>
                                            <option value="3"><?php echo e(__('Course')); ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <div class="row mb-3 d-none" id="course_id_div">
                                <div class="col-md-12">
                                    <div class="input__group">
                                        <label><?php echo e(__('Course')); ?></label>
                                        <select name="course_ids[]" multiple id="course_ids" class="multiple-basic-single">
                                            <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($course->id); ?>"><?php echo e($course->title); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3 d-none" id="instructor_id_div">
                                <div class="col-md-12">
                                    <div class="input__group">
                                        <label for="instructor_ids"><?php echo e(__('Instructor')); ?></label>
                                        <select name="instructor_ids[]" multiple id="instructor_ids" class="multiple-basic-single">
                                            <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($instructor->id); ?>"><?php echo e($instructor->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="input__group text-black">
                                        <label>Discount (Percentage %)(ex: 15, 25, 30, 40 ...)<span class="text-danger">*</span></label>
                                        <input type="number" min="0" step="any" name="percentage" value="<?php echo e(old('percentage')); ?>" placeholder="%"
                                               class="form-control" required>
                                        <?php if($errors->has('percentage')): ?>
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('percentage')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="input__group text-black">
                                        <label><?php echo e(__('Minimum Amount to Apply Coupon')); ?><span class="text-danger">*</span></label>
                                        <input type="number" min="0" name="minimum_amount" value="<?php echo e(old('minimum_amount')); ?>" placeholder="<?php echo e(__('Type minimum amount')); ?>"
                                               class="form-control" required>
                                        <?php if($errors->has('minimum_amount')): ?>
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('minimum_amount')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="input__group text-black">
                                        <label><?php echo e(__('Status')); ?> <span class="text-danger">*</span></label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="">--<?php echo e(__('Select Option')); ?>--</option>
                                            <option value="1"><?php echo e(__('Active')); ?></option>
                                            <option value="0"><?php echo e(__('Deactivated')); ?></option>
                                        </select>
                                    </div>
                                </div>
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

<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script src="<?php echo e(asset('admin/js/custom/coupon-create.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/coupon/create.blade.php ENDPATH**/ ?>
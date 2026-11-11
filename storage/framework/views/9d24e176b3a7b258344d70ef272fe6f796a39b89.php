<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Update Password')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Update Password')); ?></li>
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
                            <h2><?php echo e(__('Update Password')); ?></h2>
                        </div>
                        <form action="<?php echo e(route('admin.change-password')); ?>" method="post" class="form-horizontal" >
                            <?php echo csrf_field(); ?>
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input__group mb-25">
                                                <label for="password"><?php echo e(__('New Password')); ?> <span class="text-danger">*</span></label>
                                                <input type="password" name="password" id="password" value="" placeholder="<?php echo e(__('Type your new password')); ?>" class="form-control" required>
                                                <?php if($errors->has('password')): ?>
                                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('password')); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input__group mb-25">
                                                <label for="password_confirmation"><?php echo e(__('Confirm Password')); ?> <span class="text-danger">*</span></label>
                                                <input type="password" name="password_confirmation" id="password_confirmation" value="" placeholder="<?php echo e(__('Type your confirm password')); ?>" class="form-control" required>
                                                <?php if($errors->has('password_confirmation')): ?>
                                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('password_confirmation')); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="row mb-3 text-end">
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/profile/change-password.blade.php ENDPATH**/ ?>
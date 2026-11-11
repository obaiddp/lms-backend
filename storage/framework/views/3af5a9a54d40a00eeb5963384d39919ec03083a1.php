<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Application Setting')); ?></h2>
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
                        <form action="<?php echo e(route('settings.save.setting')); ?>" method="post" class="form-horizontal">
                            <?php echo csrf_field(); ?>

                            <div class="row">
                                <div class="col-lg-4">
                                    <label><?php echo e(__('Cashback System')); ?> <span class="text-danger">*</span></label>
                                    <select name="cashback_system_mode" required class="form-control">
                                        <option value="0" <?php if(get_option('cashback_system_mode') != 1): ?> selected <?php endif; ?>><?php echo e(__('No')); ?></option>
                                        <option value="1" <?php if(get_option('cashback_system_mode') == 1): ?> selected <?php endif; ?>><?php echo e(__('Yes')); ?></option>
                                    </select>
                                    <?php if($errors->has('cashback_system_mode')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('cashback_system_mode')); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-lg-4">
                                    <label><?php echo e(__('Cashback Type')); ?> <span class="text-danger">*</span></label>
                                    <select name="cashback_type" required class="form-control">
                                        <option value="1" <?php if(get_option('cashback_type') == 1): ?> selected <?php endif; ?>><?php echo e(__('Percent')); ?></option>
                                        <option value="2" <?php if(get_option('cashback_type') == 2): ?> selected <?php endif; ?>><?php echo e(__('Fixed Amount')); ?></option>
                                    </select>
                                    <?php if($errors->has('cashback_type')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('cashback_type')); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-lg-4">
                                    <label><?php echo e(__('Amount')); ?> <span class="text-danger">*</span></label>
                                    <input type="number" value="<?php echo e(get_option('cashback_amount')); ?>" class="form-control" minimum=1 name="cashback_amount">
                                    <?php if($errors->has('cashback_amount')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('cashback_amount')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="row justify-content-end mt-3">
                                <div class="col-md-1">
                                    <button type="submit" class="btn btn-blue float-right"><?php echo e(__('Update')); ?></button>
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/cashbook_setting.blade.php ENDPATH**/ ?>
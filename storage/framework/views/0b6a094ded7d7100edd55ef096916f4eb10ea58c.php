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
                                <li class="breadcrumb-item"><a
                                        href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
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
                    <div class="item-top mb-30">
                        <h2><?php echo e(__(@$title)); ?></h2>
                    </div>
                    <div class="bg-dark-primary-soft-varient p-4 border-1">
                        <h2><?php echo e(__('Subscription Mode')); ?>: </h2>
                        <p><?php echo e(__('If subscription mode is active then by default all student have to use the subscription
                            package.')); ?></p>
                    </div>
                    <br>
                    <form action="<?php echo e(route('settings.subscription_mode.change')); ?>" method="post" class="form-horizontal">
                        <?php echo csrf_field(); ?>

                        <div class="form-group text-black row mb-3">
                            <label class="col-lg-3"><?php echo e(__('Subscription')); ?> <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <div class="col-lg-9">
                                    <select name="subscription_mode" required class="form-control subscription_mode">
                                        <option value="0" <?php if(get_option('subscription_mode') !=1): ?> selected <?php endif; ?>><?php echo e(__('No')); ?></option>
                                        <option value="1" <?php if(get_option('subscription_mode')==1): ?> selected <?php endif; ?>><?php echo e(__('Yes')); ?></option>
                                    </select>
                                    <?php if($errors->has('subscription_mode')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('subscription_mode')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-black row mb-3">
                            <label class="col-lg-3"><?php echo e(__('Default Subscription Type')); ?> <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <div class="col-lg-9">
                                    <select name="subscription_default_package_type" class="form-control saas_mode">
                                        <option value="monthly" <?php if(get_option('subscription_default_package_type') == 'monthly'): ?> selected <?php endif; ?>><?php echo e(__('Monthly')); ?></option>
                                        <option value="yearly" <?php if(get_option('subscription_default_package_type') == 'yearly'): ?> selected <?php endif; ?>><?php echo e(__('Yearly')); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-20 row text-end">
                            <div class="col">
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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/subscription.blade.php ENDPATH**/ ?>
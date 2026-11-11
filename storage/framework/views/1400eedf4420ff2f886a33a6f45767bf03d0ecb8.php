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
                    <div class="bg-dark-primary-soft-varient border-1 mb-4 p-4">
                        <h2>Instructions: </h2>
                        <p>Please use <span class="link-primary"><?php echo e(url('/instructor/gmeet-callback')); ?></span> in Authorized redirect URIs while creating OAuth Client IDs.</p>
                    </div>
                    <form action="<?php echo e(route('settings.gmeet_settings.update')); ?>" method="post" class="form-horizontal">
                        <?php echo csrf_field(); ?>
                        <div class="row input__group mb-25">
                            <label class="col-lg-3"><?php echo e(__('Gmeet Status')); ?> <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select name="gmeet_status" id="" class="form-control" required>
                                    <option value=""><?php echo e(__('Select Option')); ?></option>
                                    <option value="1" <?php if(get_option('gmeet_status')==1): ?> selected <?php endif; ?>><?php echo e(__('Active')); ?></option>
                                    <option value="0" <?php if(get_option('gmeet_status') !=1): ?> selected <?php endif; ?>><?php echo e(__('Disable')); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="row input__group mb-25">
                            <label class="col-lg-3"><?php echo e(__('Gmeet CLIENT ID')); ?><span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="text" name="gmeet_client_id" value="<?php echo e(get_option('gmeet_client_id')); ?>"
                                    class="form-control" required>
                            </div>
                        </div>
                        <div class="row input__group mb-25">
                            <label class="col-lg-3"><?php echo e(__('Gmeet CLIENT SECRET')); ?><span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="text" name="gmeet_client_secret"
                                    value="<?php echo e(get_option('gmeet_client_secret')); ?>" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="input__group general-settings-btn">
                                    <button type="submit" class="btn btn-blue float-right"><?php echo e(__('Update')); ?></button>
                                </div>
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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/general/gmeet_settings.blade.php ENDPATH**/ ?>
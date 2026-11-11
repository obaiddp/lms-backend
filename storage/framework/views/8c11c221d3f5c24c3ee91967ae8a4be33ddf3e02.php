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
                        <p>You can create the credentials from the official <a target="_blank" class="fw-bold text-link" href="https://console.agora.io/">Agora Site</a>.</p>
                    </div>
                    <form action="<?php echo e(route('settings.agora_settings.update')); ?>" method="post" class="form-horizontal">
                        <?php echo csrf_field(); ?>
                        <div class="row input__group mb-25">
                            <label class="col-lg-3"><?php echo e(__('Agora Status')); ?> <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <select name="agora_status" id="" class="form-control" required>
                                    <option value=""><?php echo e(__('Select Option')); ?></option>
                                    <option value="1" <?php if(get_option('agora_status')==1): ?> selected <?php endif; ?>><?php echo e(__('Active')); ?></option>
                                    <option value="0" <?php if(get_option('agora_status') !=1): ?> selected <?php endif; ?>><?php echo e(__('Disable')); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="row input__group mb-25">
                            <label class="col-lg-3"><?php echo e(__('Agora App ID')); ?><span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="text" name="agora_app_id" value="<?php echo e(get_option('agora_app_id')); ?>"
                                    class="form-control" required>
                            </div>
                        </div>
                        <div class="row input__group mb-25">
                            <label class="col-lg-3"><?php echo e(__('Agora App Certificate')); ?><span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="text" name="agora_app_certificate"
                                    value="<?php echo e(get_option('agora_app_certificate')); ?>" class="form-control" required>
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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/general/agora_settings.blade.php ENDPATH**/ ?>
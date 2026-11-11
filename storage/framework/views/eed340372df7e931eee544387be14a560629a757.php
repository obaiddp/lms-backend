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
                                    <label><?php echo e(__('Chat System Driver')); ?> <span class="text-danger">*</span></label>
                                    <select name="broadcast_default" required class="form-control">
                                        <option value="pusher" <?php if(get_option('broadcast_default') == 'pusher'): ?> selected <?php endif; ?>><?php echo e(__('Pusher')); ?></option>
                                        <option value="null" <?php if(get_option('broadcast_default') == 'null'): ?> selected <?php endif; ?>><?php echo e(__('Ajax')); ?></option>
                                    </select>
                                    <?php if($errors->has('broadcast_default')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('broadcast_default')); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-lg-4">
                                    <label><?php echo e(__('Pusher ID')); ?> <span class="text-danger">*</span></label>
                                    <input type="text" value="<?php echo e(get_option('pusher_app_id')); ?>" class="form-control" name="pusher_app_id">
                                    <?php if($errors->has('pusher_app_id')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('pusher_app_id')); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-lg-4">
                                    <label><?php echo e(__('Pusher Key')); ?> <span class="text-danger">*</span></label>
                                    <input type="text" value="<?php echo e(get_option('pusher_app_key')); ?>" class="form-control" name="pusher_app_key">
                                    <?php if($errors->has('pusher_app_key')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('pusher_app_key')); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-lg-4">
                                    <label><?php echo e(__('Pusher Secret')); ?> <span class="text-danger">*</span></label>
                                    <input type="text" value="<?php echo e(get_option('pusher_app_secret')); ?>" class="form-control" name="pusher_app_secret">
                                    <?php if($errors->has('pusher_app_secret')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('pusher_app_secret')); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-lg-4">
                                    <label><?php echo e(__('Pusher Cluster')); ?> <span class="text-danger">*</span></label>
                                    <input type="text" value="<?php echo e(get_option('pusher_app_cluster')); ?>" class="form-control" name="pusher_app_cluster">
                                    <?php if($errors->has('pusher_app_cluster')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('pusher_app_cluster')); ?></span>
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/chat_setting.blade.php ENDPATH**/ ?>
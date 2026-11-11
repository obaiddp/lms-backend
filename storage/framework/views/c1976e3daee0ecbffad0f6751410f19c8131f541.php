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
                        <div class="custom-form-group mb-3 row">
                            <label for="get_in_touch_title" class="col-lg-4 text-lg-right text-black"><?php echo e(__('Migrate Database (If needed for update database)')); ?> </label>
                            <div class="col-lg-4">
                                <a href="<?php echo e(route('settings.migrate-update')); ?>" class="btn btn-primary"><?php echo e(__('Click Here')); ?></a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page content area end -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/migrate-settings.blade.php ENDPATH**/ ?>
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
                        <div class="bg-dark-primary-soft-varient p-4 border-1">
                            <h2><?php echo e(__('Instructions')); ?>: </h2>
                            <p>You need to follow some instruction after maintenance mode changes. Instruction list given below-</p>
                            <div class="text-black">
                                <li>If you select maintenance mode <b>Maintenance On</b>,
                                    you need to input secret key for maintenance work. Otherwise you can't work this website. And your created secret key helps you to work under
                                    maintenance.
                                </li>
                                <li>After created maintenance key, you can use this website secretly through this url <span class="iconify"
                                                                                                                            data-icon="arcticons:url-forwarder"></span> <span
                                        class="text-primary"><?php echo e(url('')); ?>/(Your created secret key)</span></li>
                                <li>Only one time url is browsing with secret key and you can browse your site in maintenance mode. When maintenance mode on, any user can see
                                    maintenance mode error message.
                                </li>
                                <li>Unfortunately you forget your secret key and try to connect with your website. <br> Then you go to your project folder location
                                    <b>Main Files</b>(where your file in cpanel or your hosting)<span class="iconify" data-icon="arcticons:url-forwarder"></span><b>storage</b>
                                    <span class="iconify" data-icon="arcticons:url-forwarder"></span><b>framework</b>. You can see 2 files and need to delete 2 files. Files are:
                                    <br>
                                    1. down <br>
                                    2. maintenance.php
                                </li>
                            </div>
                        </div>
                        <br>
                        <form action="<?php echo e(route('settings.maintenance.change')); ?>" method="post" class="form-horizontal">
                            <?php echo csrf_field(); ?>

                            <div class="form-group text-black row mb-3">
                                <label class="col-lg-3"><?php echo e(__('Maintenance Mode')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <div class="col-lg-9">
                                        <select name="maintenance_mode" id="" class="form-control maintenance_mode">
                                            <option value="">--<?php echo e(__('Select Option')); ?>--</option>
                                            <option value="1" <?php if(get_option('maintenance_mode') == 1): ?> selected <?php endif; ?>><?php echo e(__('Maintenance On')); ?></option>
                                            <option value="2" <?php if(get_option('maintenance_mode') != 1): ?> selected <?php endif; ?>><?php echo e(__('Live')); ?></option>
                                        </select>
                                        <?php if($errors->has('maintenance_mode')): ?>
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('maintenance_mode')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label class="col-lg-3"><?php echo e(__('Maintenance Mode Secret Key')); ?></label>
                                <div class="col-lg-9">
                                    <input type="text" name="maintenance_secret_key" value="<?php echo e(get_option('maintenance_secret_key')); ?>" minlength="6"
                                           class="form-control maintenance_secret_key">
                                    <?php if($errors->has('maintenance_secret_key')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('maintenance_secret_key')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="form-group text-black row mb-3">
                                <label class="col-lg-3"><?php echo e(__('Maintenance Mode Url')); ?> </label>
                                <div class="col-lg-9">
                                    <input type="text" name="" value="" class="form-control maintenance_mode_url" disabled>
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

<?php $__env->startPush('script'); ?>
    <script>
        "use strict"
        $('.maintenance_secret_key').on('keyup', function () {
            var secret_value = $('.maintenance_secret_key').val()
            var url = "<?php echo e(url('')); ?>" + "/"
            $('.maintenance_mode_url').val(url + secret_value);
        })

        $(function () {
            var url = "<?php echo e(url('')); ?>" + "/" + "<?php echo e(get_option('maintenance_secret_key')); ?>"
            $('.maintenance_mode_url').val(url);

            var maintenance_mode = "<?php echo e(get_option('maintenance_mode')); ?>"
            maintenanceMode(maintenance_mode)
        })

        $('.maintenance_mode').change(function () {
            var maintenance_mode = this.value
            maintenanceMode(maintenance_mode)
        });

        function maintenanceMode(maintenance_mode) {
            if (maintenance_mode == 1) {
                $(".maintenance_secret_key").attr("required", "true");
            } else if (maintenance_mode == 2) {
                $(".maintenance_secret_key").removeAttr('required');
                $('.maintenance_secret_key').val('');
                $('.maintenance_mode_url').val("<?php echo e(url('')); ?>");
            }
        }

    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/maintenance-mode.blade.php ENDPATH**/ ?>
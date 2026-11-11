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
                        <form action="<?php echo e(route('settings.general_setting.cms.update')); ?>" method="post">
                            <?php echo csrf_field(); ?>

                            <div class="custom-form-group mb-3 row">
                                <label for="get_in_touch_title" class="col-lg-3 text-lg-right text-black"><?php echo e(__('Get In Touch Title')); ?> </label>
                                <div class="col-lg-9">
                                    <input type="text" name="get_in_touch_title" id="get_in_touch_title" value="<?php echo e(get_option('get_in_touch_title')); ?>"
                                           class="form-control" placeholder="<?php echo e(__('Title')); ?>">
                                </div>
                            </div>
                            <div class="custom-form-group mb-3 row">
                                <label for="send_us_msg_title" class="col-lg-3 text-lg-right text-black"><?php echo e(__('Send Us Msg Title')); ?> </label>
                                <div class="col-lg-9">
                                    <input type="text" name="send_us_msg_title" id="send_us_msg_title" value="<?php echo e(get_option('send_us_msg_title')); ?>"
                                           class="form-control" placeholder="<?php echo e(__('Title')); ?>">
                                </div>
                            </div>
                            <div class="custom-form-group mb-3 row">
                                <label for="contact_us_location" class="col-lg-3 text-lg-right text-black"><?php echo e(__('Location')); ?> </label>
                                <div class="col-lg-9">
                                    <input type="text" name="contact_us_location" id="contact_us_location" value="<?php echo e(get_option('contact_us_location')); ?>" class="form-control" placeholder="<?php echo e(__('Type location')); ?>">
                                </div>
                            </div>
                            <div class="custom-form-group mb-3 row">
                                <label for="contact_us_email_one" class="col-lg-3 text-lg-right text-black"><?php echo e(__('Email One')); ?> </label>
                                <div class="col-lg-9">
                                    <input type="email" name="contact_us_email_one" id="contact_us_email_one" value="<?php echo e(get_option('contact_us_email_one')); ?>" class="form-control" placeholder="<?php echo e(__('Email')); ?>">
                                </div>
                            </div>
                            <div class="custom-form-group mb-3 row">
                                <label for="contact_us_email_two" class="col-lg-3 text-lg-right text-black"><?php echo e(__('Email Two')); ?> </label>
                                <div class="col-lg-9">
                                    <input type="email" name="contact_us_email_two" id="contact_us_email_two" value="<?php echo e(get_option('contact_us_email_two')); ?>" class="form-control" placeholder="<?php echo e(__('Email')); ?>">
                                </div>
                            </div>
                            <div class="custom-form-group mb-3 row">
                                <label for="contact_us_phone_one" class="col-lg-3 text-lg-right text-black"><?php echo e(__('Contact Number One')); ?> </label>
                                <div class="col-lg-9">
                                    <input type="text" name="contact_us_phone_one" id="contact_us_phone_one" value="<?php echo e(get_option('contact_us_phone_one')); ?>" class="form-control"
                                           placeholder="<?php echo e(__('Type phone one')); ?>">
                                </div>
                            </div>
                            <div class="custom-form-group mb-3 row">
                                <label for="contact_us_phone_two" class="col-lg-3 text-lg-right text-black"><?php echo e(__('Contact Number Two')); ?> </label>
                                <div class="col-lg-9">
                                    <input type="text" name="contact_us_phone_two" id="contact_us_phone_two" value="<?php echo e(get_option('contact_us_phone_two')); ?>" class="form-control"
                                           placeholder="<?php echo e(__('Type phone two')); ?>">
                                </div>
                            </div>
                            <div class="custom-form-group mb-3 row">
                                <label for="contact_us_map_link" class="col-lg-3 text-lg-right text-black"><?php echo e(__('Location Map Link')); ?> </label>
                                <div class="col-lg-9">
                                    <input type="text" name="contact_us_map_link" id="contact_us_map_link" value="<?php echo e(get_option('contact_us_map_link')); ?>" class="form-control" placeholder="<?php echo e(__('Type map link')); ?>">
                                </div>
                            </div>
                            <div class="custom-form-group mb-3 row">
                                <label for="contact_us_description" class="col-lg-3 text-lg-right text-black"><?php echo e(__('Description')); ?> </label>
                                <div class="col-lg-9">
                                    <textarea name="contact_us_description" id="contact_us_description" rows="5" class="form-control"><?php echo e(get_option('contact_us_description')); ?></textarea>
                                </div>
                            </div>

                            <div class="row justify-content-end">
                                <div class="col-md-2 text-right ">
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/contact_us/cms.blade.php ENDPATH**/ ?>
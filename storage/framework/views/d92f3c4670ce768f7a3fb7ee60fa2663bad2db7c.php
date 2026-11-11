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
                        <div class="item-top mb-30"><h2><?php echo e(__('FAQ TAB')); ?></h2></div>
                        <form action="<?php echo e(route('settings.general_setting.cms.update')); ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>

                            <div class="form-group row">
                                <div class="custom-form-group mb-3 col-lg-5">
                                    <label for="question" class="text-lg-right text-black"><?php echo e(__('Title')); ?></label>
                                    <input type="text" name="faq_tab_first_title" id="faq_tab_first_title" value="<?php echo e(get_option('faq_tab_first_title')); ?>" class="form-control" placeholder="<?php echo e(__('Type title')); ?>" >
                                </div>
                                <div class="custom-form-group mb-3 col-lg-7">
                                    <label for="answer" class="text-lg-right text-black"><?php echo e(__('Subtitle')); ?></label>
                                    <input type="text" name="faq_tab_first_subtitle" id="faq_tab_first_subtitle" value="<?php echo e(get_option('faq_tab_first_subtitle')); ?>" class="form-control" placeholder="<?php echo e(__('Type subtitle')); ?>" >
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="custom-form-group mb-3 col-lg-5">
                                    <label for="question" class="text-lg-right text-black"><?php echo e(__('Title')); ?></label>
                                    <input type="text" name="faq_tab_sec_title" id="faq_tab_sec_title" value="<?php echo e(get_option('faq_tab_sec_title')); ?>" class="form-control" placeholder="<?php echo e(__('Type title')); ?>" >
                                </div>
                                <div class="custom-form-group mb-3 col-lg-7">
                                    <label for="answer" class="text-lg-right text-black"><?php echo e(__('Subtitle')); ?></label>
                                    <input type="text" name="faq_tab_sec_subtitle" id="faq_tab_sec_subtitle" value="<?php echo e(get_option('faq_tab_sec_subtitle')); ?>" class="form-control" placeholder="<?php echo e(__('Type subtitle')); ?>" >
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="custom-form-group mb-3 col-lg-5">
                                    <label for="question" class="text-lg-right text-black"><?php echo e(__('Title')); ?></label>
                                    <input type="text" name="faq_tab_third_title" id="faq_tab_third_title" value="<?php echo e(get_option('faq_tab_third_title')); ?>" class="form-control" placeholder="<?php echo e(__('Type title')); ?>" >
                                </div>
                                <div class="custom-form-group mb-3 col-lg-7">
                                    <label for="answer" class="text-lg-right text-black"><?php echo e(__('Subtitle')); ?></label>
                                    <input type="text" name="faq_tab_third_subtitle" id="faq_tab_third_subtitle" value="<?php echo e(get_option('faq_tab_third_subtitle')); ?>" class="form-control" placeholder="<?php echo e(__('Type subtitle')); ?>" >
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="custom-form-group mb-3 col-lg-5">
                                    <label for="question" class="text-lg-right text-black"><?php echo e(__('Title')); ?></label>
                                    <input type="text" name="faq_tab_four_title" id="faq_tab_four_title" value="<?php echo e(get_option('faq_tab_four_title')); ?>" class="form-control" placeholder="<?php echo e(__('Type title')); ?>" >
                                </div>
                                <div class="custom-form-group mb-3 col-lg-7">
                                    <label for="answer" class="text-lg-right text-black"><?php echo e(__('Subtitle')); ?></label>
                                    <input type="text" name="faq_tab_four_subtitle" id="faq_tab_four_subtitle" value="<?php echo e(get_option('faq_tab_four_subtitle')); ?>" class="form-control" placeholder="<?php echo e(__('Type subtitle')); ?>" >
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/faq/tab-service.blade.php ENDPATH**/ ?>
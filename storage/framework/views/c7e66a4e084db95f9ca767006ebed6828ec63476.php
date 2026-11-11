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
                    <div class="email-inbox__area  bg-style">
                        <div class="item-top mb-30"><h2><?php echo e(__(@$title)); ?></h2></div>
                        <form action="<?php echo e(route('settings.about.upgrade-skill.update')); ?>" method="post" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <div class="custom-form-group mb-3 row">
                                <label class="col-lg-3 text-lg-right text-black"> <?php echo e(__('Upgrade Skill Image')); ?> </label>
                                <div class="col-lg-3">
                                    <div class="upload-img-box">
                                        <?php if(@$aboutUsGeneral->upgrade_skill_logo): ?>
                                            <img src="<?php echo e(getImageFile(@$aboutUsGeneral->upgrade_skill_logo_path)); ?>">
                                        <?php else: ?>
                                            <img src="">
                                        <?php endif; ?>
                                        <input type="file" name="upgrade_skill_logo" id="upgrade_skill_logo" accept="image/*" onchange="previewFile(this)">
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                        </div>
                                    </div>
                                    <?php if($errors->has('upgrade_skill_logo')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('upgrade_skill_logo')); ?></span>
                                    <?php endif; ?>
                                    <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>: </span>JPG, JPEG, PNG <br> <span class="text-black"><?php echo e(__('Accepted Size')); ?>:</span> 505 x 540 (1MB)</p>
                                </div>
                            </div>
                            <div class="custom-form-group mb-3 row">
                                <label for="upgrade_skill_title" class="col-lg-3 text-lg-right text-black"><?php echo e(__('Title')); ?> </label>
                                <div class="col-lg-9">
                                    <input type="text" name="upgrade_skill_title" id="upgrade_skill_title" value="<?php echo e(@$aboutUsGeneral->upgrade_skill_title); ?>"
                                           class="form-control" placeholder="<?php echo e(__('Type upgrade skill title')); ?>">
                                </div>
                            </div>
                            <div class="custom-form-group mb-3 row">
                                <label for="upgrade_skill_title" class="col-lg-3 text-lg-right text-black"><?php echo e(__('Subtitle')); ?> </label>
                                <div class="col-lg-9">
                                    <textarea name="upgrade_skill_subtitle" class="form-control" rows="5" id="upgrade_skill_subtitle"
                                              required><?php echo e(@$aboutUsGeneral->upgrade_skill_subtitle); ?></textarea>
                                </div>
                            </div>
                            <div class="custom-form-group mb-3 row">
                                <label for="upgrade_skill_button_name" class="col-lg-3 text-lg-right text-black"><?php echo e(__('Button Text')); ?> </label>
                                <div class="col-lg-9">
                                    <input type="text" name="upgrade_skill_button_name" id="upgrade_skill_button_name" value="<?php echo e(@$aboutUsGeneral->upgrade_skill_button_name); ?>"
                                           class="form-control" placeholder="<?php echo e(__('Type upgrade skill button name')); ?>">
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


<?php $__env->startPush('style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/custom/image-preview.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script src="<?php echo e(asset('admin/js/custom/image-preview.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/about/upgrade-skill.blade.php ENDPATH**/ ?>
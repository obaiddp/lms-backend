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
                    <div class="email-inbox__area bg-style admin-instructor-feature-settings">
                        <div class="item-top mb-30"><h2><?php echo e(__(@$title)); ?></h2></div>
                        <form action="<?php echo e(route('settings.instructor-feature.update')); ?>" method="post" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <div id="add_repeater" class="mb-3">
                                <div data-repeater-list="instructor_features" class="">
                                    <?php if($instructorFeatures->count() > 0): ?>
                                        <?php $__currentLoopData = $instructorFeatures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div data-repeater-item="" class="form-group row">
                                                <input type="hidden" name="id" value="<?php echo e(@$feature['id']); ?>"/>
                                                <div class="custom-form-group mb-3 col-md-12 col-lg-3 col-xl-3 col-xxl-2">
                                                    <label for="logo_<?php echo e(@$feature['id']); ?>" class=" text-lg-right text-black"> <?php echo e(__('Logo')); ?> </label>

                                                    <div class="upload-img-box">
                                                        <?php if($feature->logo): ?>
                                                            <img src="<?php echo e(getImageFile($feature->image_path)); ?>">
                                                        <?php else: ?>
                                                            <img src="" alt="">
                                                        <?php endif; ?>
                                                        <input type="file" name="logo" id="logo_<?php echo e(@$feature['id']); ?>" accept="image/*" onchange="preview86DimensionsFile(this)">
                                                        <div class="upload-img-box-icon">
                                                            <i class="fa fa-camera"></i>
                                                            <p class="m-0"><?php echo e(__('Logo')); ?></p>
                                                        </div>
                                                    </div>
                                                    <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> PNG <br> <span class="text-black"><?php echo e(__('Recommended Size')); ?>:</span> 86 x 86</p>
                                                </div>
                                                <div class="custom-form-group mb-3 col-md-12 col-lg-4 col-xl-4 col-xxl-4">
                                                    <label for="title_<?php echo e(@$feature['id']); ?>" class="text-lg-right text-black"> <?php echo e(__('Title')); ?> </label>
                                                    <div class="">
                                                        <input type="text" name="title" id="title_<?php echo e(@$feature['id']); ?>" value="<?php echo e($feature->title); ?>" class="form-control" placeholder="<?php echo e(__('Type title')); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="custom-form-group mb-3 col-md-12 col-lg-4 col-xl-4 col-xxl-5">
                                                    <label for="subtitle_<?php echo e(@$feature['id']); ?>" class="text-lg-right text-black"> <?php echo e(__('Subtitle')); ?> </label>
                                                    <div class="">
                                                        <input type="text" name="subtitle" id="subtitle_<?php echo e(@$feature['id']); ?>" value="<?php echo e($feature->subtitle); ?>" class="form-control" placeholder="<?php echo e(__('Type subtitle')); ?>" required>
                                                    </div>
                                                </div>

                                                <div class="col-lg-1 mb-3 removeClass">
                                                    <label class="text-lg-right text-black opacity-0"><?php echo e(__('Remove')); ?></label>
                                                    <a href="javascript:;" data-repeater-delete=""
                                                       class="btn btn-icon-remove btn-danger">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <div data-repeater-item="" class="form-group row">
                                            <div class="custom-form-group mb-3 col-md-12 col-lg-3 col-xl-3 col-xxl-2">
                                                <label for="logo" class=" text-lg-right text-black"> <?php echo e(__('Logo')); ?> </label>

                                                <div class="upload-img-box">
                                                    <img src="" alt="">
                                                    <input type="file" name="logo" id="logo" accept="image/*" onchange="preview86DimensionsFile(this)" required>
                                                    <div class="upload-img-box-icon">
                                                        <i class="fa fa-camera"></i>
                                                        <p class="m-0"><?php echo e(__('Logo')); ?></p>
                                                    </div>
                                                </div>
                                                <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> PNG <br> <span class="text-black"><?php echo e(__('Recommended Size')); ?>:</span> 86 x 86</p>
                                            </div>
                                            <div class="custom-form-group mb-3 col-md-12 col-lg-4 col-xl-4 col-xxl-4">
                                                <label for="title" class="text-lg-right text-black"><?php echo e(__('Title')); ?></label>
                                                <div class="">
                                                    <input type="text" name="title" id="title" value="" class="form-control" placeholder="<?php echo e(__('Type title')); ?>" required>
                                                </div>
                                            </div>
                                            <div class="custom-form-group mb-3 col-md-12 col-lg-4 col-xl-4 col-xxl-5">
                                                <label for="subtitle" class="text-lg-right text-black"><?php echo e(__('Subtitle')); ?></label>
                                                <div class="">
                                                    <input type="text" name="subtitle" id="subtitle" value="" class="form-control" placeholder="<?php echo e(__('Type subtitle')); ?>" required>
                                                </div>
                                            </div>

                                            <div class="col-lg-1 mb-3 removeClass">
                                                <label class="text-lg-right text-black opacity-0"><?php echo e(__('Remove')); ?></label>
                                                <a href="javascript:;" data-repeater-delete=""
                                                   class="btn btn-icon-remove btn-danger">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-lg-2">
                                    <a id="add" href="javascript:;" data-repeater-create=""
                                       class="btn btn-blue">
                                        <i class="fas fa-plus"></i> <?php echo e(__('Add')); ?>

                                    </a>
                                </div>

                            </div>

                            <div class="row justify-content-end">
                                <div class="col-md-2 text-right">
                                    <?php echo $__env->make('layouts.element.form.update-button', [], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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
    <script src="<?php echo e(asset('common/js/jquery.repeater.min.js')); ?>"></script>
    <script src="<?php echo e(asset('common/js/add-repeater.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/instructor/instructor-feature.blade.php ENDPATH**/ ?>
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
                    <div class="email-inbox__area bg-style admin-about-us-instructor-support">
                        <div class="item-top mb-30"><h2><?php echo e(__(@$title)); ?></h2></div>
                        <form action="<?php echo e(route('settings.about.instructor-support.update')); ?>" method="post" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>

                            <div class=" custom-form-group mb-3 row">
                                <label for="instructor_support_title" class="col-lg-3 text-lg-right text-black"> <?php echo e(__('Instructor Support Title')); ?> </label>
                                <div class="col-lg-9">
                                    <input type="text" name="instructor_support_title" id="instructor_support_title" value="<?php echo e(@$aboutUsGeneral->instructor_support_title); ?>"
                                           class="form-control" placeholder="<?php echo e(__('Type team member title')); ?>">
                                    <?php if($errors->has('instructor_support_title')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('instructor_support_title')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-lg-12 custom-form-group mb-3 row">
                                <label for="instructor_support_subtitle" class="col-lg-3 text-lg-right text-black"> <?php echo e(__('Instructor Support Subtitle')); ?> </label>
                                <div class="col-lg-9">
                                    <textarea name="instructor_support_subtitle" class="form-control" rows="5" id="instructor_support_subtitle"
                                              required><?php echo e(@$aboutUsGeneral->instructor_support_subtitle); ?></textarea>
                                    <?php if($errors->has('instructor_support_subtitle')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('instructor_support_subtitle')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <hr>
                            <div id="add_repeater" class="mb-3">
                                <div data-repeater-list="instructor_supports" class="">
                                    <?php if(count($instructorSupports) > 0): ?>
                                        <?php $__currentLoopData = $instructorSupports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructorSupport): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div data-repeater-item="" class="form-group row ">
                                                <input type="hidden" name="id" value="<?php echo e(@$instructorSupport['id']); ?>"/>
                                                <div class="custom-form-group mb-3 col-lg-2">
                                                    <label for="image_<?php echo e($instructorSupport->id); ?>" class=" text-lg-right text-black"> Logo </label>
                                                    <div class="upload-img-box">
                                                        <?php if($instructorSupport->logo): ?>
                                                            <img src="<?php echo e(getImageFile($instructorSupport->image_path)); ?>">
                                                        <?php else: ?>
                                                            <img src="">
                                                        <?php endif; ?>
                                                        <input type="file" name="logo" id="image_<?php echo e($instructorSupport->id); ?>" accept="image/*" onchange="preview148DimensionsFile(this)">
                                                        <div class="upload-img-box-icon">
                                                            <i class="fa fa-camera"></i>
                                                        </div>
                                                    </div>
                                                    <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> PNG <br> <span class="text-black"><?php echo e(__('Accepted Size')); ?>:</span> 148 x 148</p>
                                                </div>

                                                <div class="custom-form-group mb-3 col-lg-5">
                                                    <label for="title_<?php echo e($instructorSupport->id); ?>" class="text-lg-right text-black"> <?php echo e(__('Title')); ?> </label>
                                                    <input type="text" name="title" id="title_<?php echo e($instructorSupport->id); ?>" value="<?php echo e($instructorSupport->title); ?>"
                                                           class="form-control" placeholder="<?php echo e(__('Type name')); ?>">

                                                </div>
                                                <div class="custom-form-group mb-3 col-lg-5">
                                                    <label for="subtitle_<?php echo e($instructorSupport->id); ?>" class="text-lg-right text-black"> <?php echo e(__('Subtitle')); ?> </label>
                                                    <textarea name="subtitle" class="form-control" rows="5" id="subtitle_<?php echo e($instructorSupport->id); ?>"
                                                              required><?php echo e($instructorSupport->subtitle); ?></textarea>
                                                </div>
                                                <div class="custom-form-group mb-3 col-lg-3">
                                                    <label for="button_name_<?php echo e($instructorSupport->id); ?>" class="text-lg-right text-black"> <?php echo e(__('Button Name')); ?> </label>
                                                    <input type="text" name="button_name" id="button_name_<?php echo e($instructorSupport->id); ?>"
                                                           value="<?php echo e($instructorSupport->button_name); ?>" class="form-control" placeholder="<?php echo e(__('Type button name')); ?>">
                                                </div>
                                                <div class="custom-form-group mb-3 col-lg-9">
                                                    <label for="button_link_<?php echo e($instructorSupport->id); ?>" class="text-lg-right text-black"> <?php echo e(__('Button Link')); ?> </label>
                                                    <input type="text" name="button_link" id="button_link_<?php echo e($instructorSupport->id); ?>"
                                                           value="<?php echo e($instructorSupport->button_link); ?>" class="form-control" placeholder="<?php echo e(__('Type button link')); ?>">
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
                                        <div data-repeater-item="" class="form-group row ">
                                            <div class="custom-form-group mb-3 col-lg-2">
                                                <label for="image" class=" text-lg-right text-black"> <?php echo e(__('Logo')); ?> </label>
                                                <div class="upload-img-box">
                                                    <img src="">
                                                    <input type="file" name="logo" id="image" accept="image/*" onchange="preview148DimensionsFile(this)">
                                                    <div class="upload-img-box-icon">
                                                        <i class="fa fa-camera"></i>
                                                    </div>
                                                </div>
                                                <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> <?php echo e(__('PNG')); ?> <br> <span class="text-black"><?php echo e(__('Accepted Size')); ?>:</span> 148 x 148</p>
                                            </div>

                                            <div class="custom-form-group mb-3 col-lg-5">
                                                <label for="title" class="text-lg-right text-black"> <?php echo e(__('Title')); ?> </label>
                                                <input type="text" name="title" id="title" value="" class="form-control" placeholder="<?php echo e(__('Type name')); ?>">

                                            </div>
                                            <div class="custom-form-group mb-3 col-lg-5">
                                                <label for="subtitle" class="text-lg-right text-black"> <?php echo e(__('Subtitle')); ?> </label>
                                                <textarea name="subtitle" class="form-control" rows="5" id="subtitle" required></textarea>
                                            </div>
                                            <div class="custom-form-group mb-3 col-lg-3">
                                                <label for="button_name" class="text-lg-right text-black"> <?php echo e(__('Button Name')); ?> </label>
                                                <input type="text" name="button_name" id="button_name" value="" class="form-control" placeholder="<?php echo e(__('Type button name')); ?>">
                                            </div>
                                            <div class="custom-form-group mb-3 col-lg-9">
                                                <label for="button_link" class="text-lg-right text-black"> <?php echo e(__('Button Link')); ?> </label>
                                                <input type="text" name="button_link" id="button_link" value="" class="form-control" placeholder="<?php echo e(__('Type button link')); ?>">
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/about/instructor-support.blade.php ENDPATH**/ ?>
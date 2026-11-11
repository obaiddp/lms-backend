<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Home Settings')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                   <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item"><a><?php echo e(__('Application Setting')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__(@$title)); ?></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-4">
                    <?php echo $__env->make('admin.application_settings.home-sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
                <div class="col-lg-9 col-md-8">
                    <div class="email-inbox__area bg-style">
                        <div class="item-top mb-30"><h2><?php echo e(__(@$title)); ?></h2></div>
                        <form action="<?php echo e(route('settings.banner-section.update')); ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <div class="input__group mb-25 row">
                                <label for="banner_mini_words_title" class="col-lg-3"> <?php echo e(__('Mini Title')); ?></label>
                                <div class="col-lg-9">
                                    <select class="form-control multiple-select-input" name="banner_mini_words_title[]" id="banner_mini_words_title" multiple="multiple">
                                        <?php $__currentLoopData = $home->banner_mini_words_title ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e(@$item); ?>" selected><?php echo e(@$item); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="input__group mb-25 row">
                                <label for="banner_first_line_title" class="col-lg-3"> <?php echo e(__('First Line Title')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input type="text" name="banner_first_line_title" id="banner_first_line_title"
                                           value="<?php echo e(@$home->banner_first_line_title); ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="input__group mb-25 row">
                                <label for="banner_second_line_title" class="col-lg-3"> <?php echo e(__('Second Line Title')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input type="text" name="banner_second_line_title" id="banner_second_line_title"
                                           value="<?php echo e(@$home->banner_second_line_title); ?>" class="form-control" required>
                                </div>
                            </div>
                            <?php if(get_option('theme', THEME_DEFAULT) == THEME_DEFAULT): ?>
                            <div class="input__group mb-25 row">
                                <label for="banner_second_line_changeable_words" class="col-lg-3"> <?php echo e(__('Second Line Changeable Word Title')); ?></label>
                                <div class="col-lg-9">
                                    <select class="form-control multiple-select-input" name="banner_second_line_changeable_words[]" id="banner_second_line_changeable_words" multiple="multiple">
                                        <?php $__currentLoopData = $home->banner_second_line_changeable_words ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e(@$item); ?>" selected><?php echo e(@$item); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="input__group mb-25 row">
                                <label for="banner_third_line_title" class="col-lg-3"> <?php echo e(__('Third Line Title')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input type="text" name="banner_third_line_title" id="banner_third_line_title" value="<?php echo e(@$home->banner_third_line_title); ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="input__group mb-25 row">
                                <label for="banner_subtitle" class="col-lg-3"> <?php echo e(__('Subtitle')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <input type="text" name="banner_subtitle" id="banner_subtitle" value="<?php echo e(@$home->banner_subtitle); ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="input__group mb-25 row">
                                <label for="banner_first_button_name" class="col-lg-3"><?php echo e(__('First Button Name')); ?> </label>
                                <div class="col-lg-9">
                                    <input type="text" name="banner_first_button_name" id="banner_first_button_name" value="<?php echo e(@$home->banner_first_button_name); ?>"
                                           class="form-control" >
                                </div>
                            </div>
                            <div class="input__group mb-25 row">
                                <label for="banner_first_button_link" class="col-lg-3"><?php echo e(__('First Button Link')); ?> </label>
                                <div class="col-lg-9">
                                    <input type="text" name="banner_first_button_link" id="banner_first_button_link" value="<?php echo e(@$home->banner_first_button_link); ?>"
                                           class="form-control" >
                                </div>
                            </div>
                            <div class="input__group mb-25 row">
                                <label for="banner_second_button_name" class="col-lg-3"><?php echo e(__('Second Button Name')); ?></label>
                                <div class="col-lg-9">
                                    <input type="text" name="banner_second_button_name" id="banner_second_button_name" value="<?php echo e(@$home->banner_second_button_name); ?>"
                                           class="form-control" >
                                </div>
                            </div>
                            <div class="input__group mb-25 row">
                                <label for="banner_second_button_link" class="col-lg-3"><?php echo e(__('Second Button Link')); ?> </label>
                                <div class="col-lg-9">
                                    <input type="text" name="banner_second_button_link" id="banner_second_button_link" value="<?php echo e(@$home->banner_second_button_link); ?>"
                                           class="form-control" >
                                </div>
                            </div>

                            <div class="input__group mb-25 row">
                                <label class="col-lg-3"><?php echo e(__('Banner Image')); ?></label>
                                <div class="col-lg-5">
                                    <div class="upload-img-box">
                                        <?php if( @$home->banner_image): ?>
                                            <img src="<?php echo e(getImageFile(@$home->banner_image_path)); ?>">
                                        <?php else: ?>
                                            <img src="" alt="img">
                                        <?php endif; ?>
                                        <input type="file" name="banner_image" id="banner_image" accept="image/*" onchange="previewFile(this)">
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                            <p class="m-0"><?php echo e(__('Banner Image')); ?></p>
                                        </div>
                                    </div>
                                    <?php if($errors->has('banner_image')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('banner_image')); ?></span>
                                    <?php endif; ?>
                                    <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> PNG,SVG <br> <span class="text-black"><?php echo e(__('Recommend Size')); ?>:</span> 800 x 540 (1MB)</p>
                                </div>
                            </div>

                            <?php if(get_option('theme', THEME_DEFAULT) == THEME_THREE): ?>
                            <div class="input__group mb-25 row">
                                <label for="banner_left_card_title" class="col-lg-3"><?php echo e(__('Left Card Title')); ?></label>
                                <div class="col-lg-9">
                                    <input type="text" name="banner_left_card_title" id="banner_left_card_title" value="<?php echo e(get_option('banner_left_card_title')); ?>"
                                           class="form-control" >
                                </div>
                            </div>
                            <div class="input__group mb-25 row">
                                <label for="banner_left_card_description" class="col-lg-3"><?php echo e(__('Left Card Description')); ?> </label>
                                <div class="col-lg-9">
                                    <input type="text" name="banner_left_card_description" id="banner_left_card_description" value="<?php echo e(get_option('banner_left_card_description')); ?>"
                                           class="form-control" >
                                </div>
                            </div>

                            <div class="input__group mb-25 row">
                                <label class="col-lg-3"><?php echo e(__('Left Card Icon')); ?></label>
                                <div class="col-lg-5">
                                    <div class="upload-img-box">
                                        <?php if(get_option('banner_left_card_icon') != ''): ?>
                                            <img src="<?php echo e(getImageFile(get_option('banner_left_card_icon'))); ?>">
                                        <?php else: ?>
                                            <img src="">
                                        <?php endif; ?>
                                        <input type="file" name="banner_left_card_icon" id="banner_left_card_icon" accept="image/*" onchange="previewFile(this)">
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                            <p class="m-0"><?php echo e(__('Right Card Icon')); ?></p>
                                        </div>
                                    </div>
                                    <?php if($errors->has('banner_left_card_icon')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('banner_left_card_icon')); ?></span>
                                    <?php endif; ?>
                                    <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> PNG,SVG <br> <span class="text-black"><?php echo e(__('Recommend Size')); ?>:</span> 50 x 50 (100KB)</p>
                                </div>
                            </div>
                            <div class="input__group mb-25 row">
                                <label for="banner_right_card_title" class="col-lg-3"><?php echo e(__('Right Card Title')); ?></label>
                                <div class="col-lg-9">
                                    <input type="text" name="banner_right_card_title" id="banner_right_card_title" value="<?php echo e(get_option('banner_right_card_title')); ?>"
                                           class="form-control" >
                                </div>
                            </div>
                            <div class="input__group mb-25 row">
                                <label for="banner_right_card_description" class="col-lg-3"><?php echo e(__('Right Card Description')); ?> </label>
                                <div class="col-lg-9">
                                    <input type="text" name="banner_right_card_description" id="banner_right_card_description" value="<?php echo e(get_option('banner_right_card_description')); ?>"
                                           class="form-control" >
                                </div>
                            </div>

                            <div class="input__group mb-25 row">
                                <label class="col-lg-3"><?php echo e(__('Right Card Icon')); ?></label>
                                <div class="col-lg-5">
                                    <div class="upload-img-box">
                                        <?php if(get_option('banner_right_card_icon') != ''): ?>
                                            <img src="<?php echo e(getImageFile(get_option('banner_right_card_icon'))); ?>">
                                        <?php else: ?>
                                            <img src="">
                                        <?php endif; ?>
                                        <input type="file" name="banner_right_card_icon" id="banner_right_card_icon" accept="image/*" onchange="previewFile(this)">
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                            <p class="m-0"><?php echo e(__('Right Card Icon')); ?></p>
                                        </div>
                                    </div>
                                    <?php if($errors->has('banner_right_card_icon')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('banner_right_card_icon')); ?></span>
                                    <?php endif; ?>
                                    <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> PNG,SVG <br> <span class="text-black"><?php echo e(__('Recommend Size')); ?>:</span> 50 x 50 (100KB)</p>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-12">
                                    <div class="input__group general-settings-btn">
                                        <button type="submit" class="btn btn-blue"><?php echo e(__('Update')); ?></button>
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


<?php $__env->startPush('style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/custom/image-preview.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script src="<?php echo e(asset('admin/js/custom/image-preview.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/home/banner-section.blade.php ENDPATH**/ ?>
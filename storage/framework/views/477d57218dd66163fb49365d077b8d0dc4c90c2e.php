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
                <div class="col-lg-4 col-md-4">
                    <?php echo $__env->make('admin.application_settings.home-sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
                <div class="col-lg-8 col-md-8">
                    <div class="email-inbox__area bg-style">
                        <div class="item-top mb-30"><h2><?php echo e(__(@$title)); ?></h2></div>
                        <form action="<?php echo e(route('settings.general_setting.cms.update')); ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <div class="form-group text-black row mb-3">
                                <label for="top_instructor_logo" class="col-lg-4"><?php echo e(__('Logo')); ?></label>
                                <div class="col-lg-3">
                                    <div class="upload-img-box">
                                        <?php if(get_option('customer_say_logo') != ''): ?>
                                            <img src="<?php echo e(getImageFile(get_option('customer_say_logo'))); ?>">
                                        <?php else: ?>
                                            <img src="">
                                        <?php endif; ?>
                                        <input type="file" name="customer_say_logo" id="customer_say_logo" accept="image/*" onchange="previewFile(this)">
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                            <p class="m-0"><?php echo e(__('Logo')); ?></p>
                                        </div>
                                    </div>
                                    <?php if($errors->has('customer_say_logo')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('customer_say_logo')); ?></span>
                                    <?php endif; ?>

                                    <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> PNG <br> <span class="text-black"><?php echo e(__('Accepted Size')); ?>:</span> 64 x 64</p>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label for="customer_say_title" class="col-lg-4"><?php echo e(__('Customer Say Title')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="text" name="customer_say_title" id="customer_say_title" value="<?php echo e(get_option('customer_say_title')); ?>" class="form-control" required>
                                </div>
                            </div>

                            <hr>
                            <div class="item-top mb-30"><h2><?php echo e(__('Customer Comment Section')); ?></h2></div>
                            <div class="form-group text-black row mb-3">
                                <label for="customer_say_first_image" class="col-lg-4"><?php echo e(__('Fisrt Image')); ?></label>
                                <div class="col-lg-3">
                                    <div class="upload-img-box">
                                        <?php if(get_option('customer_say_first_image') != ''): ?>
                                            <img src="<?php echo e(getImageFile(get_option('customer_say_first_image'))); ?>">
                                        <?php else: ?>
                                            <img src="">
                                        <?php endif; ?>
                                        <input type="file" name="customer_say_first_image" id="customer_say_first_image" accept="image/*" onchange="previewFile(this)">
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                            <p class="m-0"><?php echo e(__('Logo')); ?></p>
                                        </div>
                                    </div>
                                    <?php if($errors->has('customer_say_first_image')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('customer_say_first_image')); ?></span>
                                    <?php endif; ?>

                                    <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> PNG <br> <span class="text-black"><?php echo e(__('Accepted Size')); ?>:</span> 224 x 313</p>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label for="customer_say_first_name" class="col-lg-4"><?php echo e(__('First Customer Name')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="text" name="customer_say_first_name" id="customer_say_first_name" value="<?php echo e(get_option('customer_say_first_name')); ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label for="customer_say_first_position" class="col-lg-4"><?php echo e(__('First Customer Position')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="text" name="customer_say_first_position" id="customer_say_first_position" value="<?php echo e(get_option('customer_say_first_position')); ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label for="customer_say_first_comment_title" class="col-lg-4"><?php echo e(__('First Customer Comment title')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="text" name="customer_say_first_comment_title" id="customer_say_first_comment_title" value="<?php echo e(get_option('customer_say_first_comment_title')); ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label for="customer_say_first_comment_description" class="col-lg-4"><?php echo e(__('First Customer Comment Description')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="text" name="customer_say_first_comment_description" id="customer_say_first_comment_description" value="<?php echo e(get_option('customer_say_first_comment_description')); ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label for="customer_say_first_comment_rating_star" class="col-lg-4"><?php echo e(__('First Customer Rating Star (1-5)')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="number" min="1" max="5" step="any" name="customer_say_first_comment_rating_star" id="customer_say_first_comment_rating_star" value="<?php echo e(get_option('customer_say_first_comment_rating_star')); ?>" class="form-control" required>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group text-black row mb-3">
                                <label for="customer_say_second_image" class="col-lg-4"><?php echo e(__('Second Image')); ?></label>
                                <div class="col-lg-3">
                                    <div class="upload-img-box">
                                        <?php if(get_option('customer_say_second_image') != ''): ?>
                                            <img src="<?php echo e(getImageFile(get_option('customer_say_second_image'))); ?>">
                                        <?php else: ?>
                                            <img src="">
                                        <?php endif; ?>
                                        <input type="file" name="customer_say_second_image" id="customer_say_second_image" accept="image/*" onchange="previewFile(this)">
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                            <p class="m-0"><?php echo e(__('Logo')); ?></p>
                                        </div>
                                    </div>
                                    <?php if($errors->has('customer_say_second_image')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('customer_say_second_image')); ?></span>
                                    <?php endif; ?>

                                    <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> PNG <br> <span class="text-black"><?php echo e(__('Accepted Size')); ?>:</span> 224 x 313</p>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label for="customer_say_second_name" class="col-lg-4"><?php echo e(__('Second Customer Name')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="text" name="customer_say_second_name" id="customer_say_second_name" value="<?php echo e(get_option('customer_say_second_name')); ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label for="customer_say_second_position" class="col-lg-4"><?php echo e(__('Second Customer Position')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="text" name="customer_say_second_position" id="customer_say_second_position" value="<?php echo e(get_option('customer_say_second_position')); ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label for="customer_say_second_comment_title" class="col-lg-4"><?php echo e(__('Second Customer Comment title')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="text" name="customer_say_second_comment_title" id="customer_say_second_comment_title" value="<?php echo e(get_option('customer_say_second_comment_title')); ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label for="customer_say_second_comment_description" class="col-lg-4"><?php echo e(__('Second Customer Comment Description')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="text" name="customer_say_second_comment_description" id="customer_say_second_comment_description" value="<?php echo e(get_option('customer_say_second_comment_description')); ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label for="customer_say_second_comment_rating_star" class="col-lg-4"><?php echo e(__('Second Customer Rating Star (1-5)')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="number" min="1" max="5" step="any" name="customer_say_second_comment_rating_star" id="customer_say_second_comment_rating_star" value="<?php echo e(get_option('customer_say_second_comment_rating_star')); ?>" class="form-control" required>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group text-black row mb-3">
                                <label for="customer_say_third_image" class="col-lg-4"><?php echo e(__('Third Image')); ?></label>
                                <div class="col-lg-3">
                                    <div class="upload-img-box">
                                        <?php if(get_option('customer_say_third_image') != ''): ?>
                                            <img src="<?php echo e(getImageFile(get_option('customer_say_third_image'))); ?>">
                                        <?php else: ?>
                                            <img src="">
                                        <?php endif; ?>
                                        <input type="file" name="customer_say_third_image" id="customer_say_third_image" accept="image/*" onchange="previewFile(this)">
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                            <p class="m-0"><?php echo e(__('Logo')); ?></p>
                                        </div>
                                    </div>
                                    <?php if($errors->has('customer_say_third_image')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('customer_say_third_image')); ?></span>
                                    <?php endif; ?>

                                    <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> PNG <br> <span class="text-black"><?php echo e(__('Accepted Size')); ?>:</span> 224 x 313</p>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label for="customer_say_third_name" class="col-lg-4"><?php echo e(__('Third Customer Name')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="text" name="customer_say_third_name" id="customer_say_third_name" value="<?php echo e(get_option('customer_say_third_name')); ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label for="customer_say_third_position" class="col-lg-4"><?php echo e(__('Third Customer Position')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="text" name="customer_say_third_position" id="customer_say_third_position" value="<?php echo e(get_option('customer_say_third_position')); ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label for="customer_say_third_comment_title" class="col-lg-4"><?php echo e(__('Third Customer Comment title')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="text" name="customer_say_third_comment_title" id="customer_say_third_comment_title" value="<?php echo e(get_option('customer_say_third_comment_title')); ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label for="customer_say_third_comment_description" class="col-lg-4"><?php echo e(__('Third Customer Comment Description')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="text" name="customer_say_third_comment_description" id="customer_say_third_comment_description" value="<?php echo e(get_option('customer_say_third_comment_description')); ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label for="customer_say_third_comment_rating_star" class="col-lg-4"><?php echo e(__('Third Customer Rating Star (1-5)')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="number" min="1" max="5" step="any" name="customer_say_third_comment_rating_star" id="customer_say_third_comment_rating_star" value="<?php echo e(get_option('customer_say_third_comment_rating_star')); ?>" class="form-control" required>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group text-black row mb-3">
                                <label for="customer_say_fourth_image" class="col-lg-4"><?php echo e(__('Fourth Image')); ?></label>
                                <div class="col-lg-3">
                                    <div class="upload-img-box">
                                        <?php if(get_option('customer_say_fourth_image') != ''): ?>
                                            <img src="<?php echo e(getImageFile(get_option('customer_say_fourth_image'))); ?>">
                                        <?php else: ?>
                                            <img src="">
                                        <?php endif; ?>
                                        <input type="file" name="customer_say_fourth_image" id="customer_say_fourth_image" accept="image/*" onchange="previewFile(this)">
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                            <p class="m-0"><?php echo e(__('Logo')); ?></p>
                                        </div>
                                    </div>
                                    <?php if($errors->has('customer_say_fourth_image')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('customer_say_fourth_image')); ?></span>
                                    <?php endif; ?>

                                    <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> PNG <br> <span class="text-black"><?php echo e(__('Accepted Size')); ?>:</span> 224 x 313</p>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label for="customer_say_fourth_name" class="col-lg-4"><?php echo e(__('fourth Customer Name')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="text" name="customer_say_fourth_name" id="customer_say_fourth_name" value="<?php echo e(get_option('customer_say_fourth_name')); ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label for="customer_say_fourth_position" class="col-lg-4"><?php echo e(__('fourth Customer Position')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="text" name="customer_say_fourth_position" id="customer_say_fourth_position" value="<?php echo e(get_option('customer_say_fourth_position')); ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label for="customer_say_fourth_comment_title" class="col-lg-4"><?php echo e(__('fourth Customer Comment title')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="text" name="customer_say_fourth_comment_title" id="customer_say_fourth_comment_title" value="<?php echo e(get_option('customer_say_fourth_comment_title')); ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label for="customer_say_fourth_comment_description" class="col-lg-4"><?php echo e(__('fourth Customer Comment Description')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="text" name="customer_say_fourth_comment_description" id="customer_say_fourth_comment_description" value="<?php echo e(get_option('customer_say_fourth_comment_description')); ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label for="customer_say_fourth_comment_rating_star" class="col-lg-4"><?php echo e(__('fourth Customer Rating Star (1-5)')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="number" min="1" max="5" step="any" name="customer_say_fourth_comment_rating_star" id="customer_say_fourth_comment_rating_star" value="<?php echo e(get_option('customer_say_fourth_comment_rating_star')); ?>" class="form-control" required>
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


<?php $__env->startPush('style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/custom/image-preview.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script src="<?php echo e(asset('admin/js/custom/image-preview.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/home/customer-say-section.blade.php ENDPATH**/ ?>
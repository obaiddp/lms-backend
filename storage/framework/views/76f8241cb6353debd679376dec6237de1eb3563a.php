<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__($title)); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('lms_product.admin.category.index')); ?>"><?php echo e(__('Product Categories')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__($title)); ?></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-vertical__item bg-style">
                            <div class="item-top mb-30">
                                <h2><?php echo e(__('Add New Category')); ?></h2>
                            </div>
                            <form action="<?php echo e(route('lms_product.admin.category.store')); ?>" method="post" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>

                                <div class="input__group mb-25">
                                    <label for="name"> <?php echo e(__('Name')); ?> </label>
                                    <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>" class="form-control flat-input" placeholder="<?php echo e(__('Name')); ?>">
                                    <?php if($errors->has('name')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('name')); ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="input__group mb-25">
                                    <label for="is_feature"> <?php echo e(__('Feature')); ?> </label>
                                    <div>
                                        <label class="text-black"> <input type="checkbox" name="is_feature" id="is_feature" value="yes" <?php echo e(old('is_feature') == 'yes' ? 'checked' : ''); ?> > <?php echo e(__('Yes')); ?> </label>
                                    </div>
                                </div>

                                <div class="custom-form-group mb-25">
                                    <label for="image" class="text-lg-right text-black mb-2"> <?php echo e(__('Image')); ?> </label>
                                    <div class="upload-img-box mb-25">
                                        <img src="">
                                        <input type="file" name="image" id="image" accept="image/*" onchange="previewFileProductCategory(this)">
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                            <p class="m-0"><?php echo e(__('Image')); ?></p>
                                        </div>
                                    </div>
                                    <?php if($errors->has('image')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('image')); ?></span>
                                    <?php endif; ?>
                                    <p><?php echo e(__('Accepted Image Files')); ?>: PNG <br> <?php echo e(__('Recommend Size')); ?>: 300 x 300 (1MB)</p>
                                </div>

                                <div class="input__group mb-25">
                                    <label><?php echo e(__('Meta Title')); ?></label>
                                    <input type="text" name="meta_title" value="<?php echo e(old('meta_title')); ?>" placeholder="<?php echo e(__('Meta title')); ?>" class="form-control">
                                    <?php if($errors->has('meta_title')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('meta_title')); ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="input__group mb-25">
                                    <label><?php echo e(__('Meta Description')); ?></label>
                                    <input type="text" name="meta_description" value="<?php echo e(old('meta_description')); ?>" placeholder="<?php echo e(__('meta description')); ?>" class="form-control">
                                    <?php if($errors->has('meta_description')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('meta_description')); ?></span>
                                    <?php endif; ?>
                                </div>
    
                                <div class="input__group mb-25">
                                    <label><?php echo e(__('Meta Keywords')); ?></label>
                                    <input type="text" name="meta_keywords" value="<?php echo e(old('meta_keywords')); ?>" placeholder="<?php echo e(__('meta keywords')); ?>" class="form-control">
                                    <?php if($errors->has('meta_keywords')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('meta_keywords')); ?></span>
                                    <?php endif; ?>
                                </div>
    
                                <div class="input__group mb-25">
                                    <label><?php echo e(__('OG Image')); ?></label>
                                    <div class="upload-img-box">
                                        <img src="">
                                        <input type="file" name="og_image" id="og_image" accept="image/*" onchange="previewFile(this)">
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                            <p class="m-0"><?php echo e(__('OG Image')); ?></p>
                                        </div>
                                    </div>
                                    <?php if($errors->has('og_image')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('og_image')); ?></span>
                                    <?php endif; ?>
                                    <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> PNG, JPG <br> <span class="text-black"><?php echo e(__('Recommend Size')); ?>:</span> 1200 x 627</p>
                                </div>
                                <div class="input__group mb-25">
                                    <div class="">
                                        <?php echo $__env->make('layouts.element.form.save-with-another', [], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/addon/product/admin/category/create.blade.php ENDPATH**/ ?>
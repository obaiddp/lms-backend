<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Add Subcategory')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('subcategory.index')); ?>"><?php echo e(__('Subcategories')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Add Subcategory')); ?></li>
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
                                <h2><?php echo e(__('Add Subcategory')); ?></h2>
                            </div>
                            <form action="<?php echo e(route('subcategory.store')); ?>" method="post" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>

                                <div class="input__group mb-25">
                                    <label for="category_id"> <?php echo e(__('Category')); ?> </label>
                                    <select name="category_id" id="category_id" class="form-control">
                                        <option value=""><?php echo e(__('Select Category')); ?></option>
                                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($category->id); ?>" <?php echo e(old('category_id') == $category->id ? 'selected' : ''); ?>><?php echo e($category->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php if($errors->has('category_id')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('category_id')); ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="input__group mb-25">
                                    <label for="sort_order"><?php echo e(__('Sort order')); ?></label>
                                    <input type="number" name="sort_order" id="sort_order" value="<?php echo e(old('sort_order', 0)); ?>" class="form-control" min="0">
                                    <small class="text-muted"><?php echo e(__('Within the same category: lower = first on academy.')); ?></small>
                                    <?php if($errors->has('sort_order')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('sort_order')); ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="input__group mb-25">
                                    <label for="name"> <?php echo e(__('Name')); ?> </label>
                                    <div>
                                        <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>" class="form-control" placeholder="<?php echo e(__('Name')); ?> ">
                                        <?php if($errors->has('name')): ?>
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('name')); ?></span>
                                        <?php endif; ?>
                                    </div>
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
                                    <label><?php echo e(__('Cover Image')); ?></label>
                                    <div class="upload-img-box">
                                        <img src="">
                                        <input type="file" name="cover_image" id="cover_image" accept="image/*" onchange="previewCategoryFile(this)">
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                            <p class="m-0"><?php echo e(__('Cover Image')); ?></p>
                                        </div>
                                    </div>
                                    <?php if($errors->has('cover_image')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('cover_image')); ?></span>
                                    <?php endif; ?>
                                    <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> PNG, JPG <br> <span class="text-black"><?php echo e(__('Recommend Size')); ?>:</span> 1900 x 400</p>
                                </div>


                                <div class="input__group">
                                    <div>
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


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/subcategory/create.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Add Blog')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('blog.index')); ?>"><?php echo e(__('All Blog')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Add Blog')); ?></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="customers__area bg-style mb-30">
                        <div class="item-title d-flex justify-content-between">
                            <h2><?php echo e(__('Add Blog')); ?></h2>
                        </div>
                        <form action="<?php echo e(route('blog.store')); ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>

                            <div class="input__group mb-25">
                                <label><?php echo e(__('Title')); ?> <span class="text-danger">*</span></label>
                                <input type="text" name="title" value="<?php echo e(old('title')); ?>" placeholder="<?php echo e(__('Title')); ?>" class="form-control slugable"  onkeyup="slugable()">
                                <?php if($errors->has('title')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('title')); ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="input__group mb-25">
                                <label><?php echo e(__('Slug')); ?> <span class="text-danger">*</span></label>
                                <input type="text" name="slug" value="<?php echo e(old('slug')); ?>" placeholder="<?php echo e(__('Slug')); ?>" class="form-control slug" onkeyup="getMyself()">
                                <?php if($errors->has('slug')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('slug')); ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="input__group mb-25">
                                <label for="blog_category_id"> <?php echo e(__('Blog Category')); ?> </label>
                                <select name="blog_category_id" id="blog_category_id">
                                    <option value="">--<?php echo e(__('Select Option')); ?>--</option>
                                    <?php $__currentLoopData = $blogCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $blogCategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($blogCategory->id); ?>"><?php echo e($blogCategory->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="input__group mb-25">
                                <label for="tag_ids"> <?php echo e(__('Tag')); ?> </label>
                                <select name="tag_ids[]" multiple id="tag_ids" class="multiple-basic-single form-control">
                                    <?php $__currentLoopData = $tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($tag->id); ?>"><?php echo e($tag->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="input__group mb-25">
                                <label><?php echo e(__('Details')); ?> <span class="text-danger">*</span></label>
                                <textarea name="details" id="summernote"><?php echo e(old('details')); ?></textarea>

                                <?php if($errors->has('details')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('details')); ?></span>
                                <?php endif; ?>

                            </div>

                            <div class="row">
                                <label><?php echo e(__('Image')); ?></label>
                                <div class="col-md-3">
                                    <div class="upload-img-box mb-25">
                                        <img src="">
                                        <input type="file" name="image" id="image" accept="image/*" onchange="previewFile(this)" required>
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                            <p class="m-0"><?php echo e(__('Image')); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <?php if($errors->has('image')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('image')); ?></span>
                                <?php endif; ?>
                                <p><?php echo e(__('Accepted Files')); ?>: JPEG, JPG, PNG <br> <?php echo e(__('Recommend Size')); ?>: 870 x 500 (1MB)</p>
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

                            <div class="row mb-3">
                                <div class="col-md-12 text-right">
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

    <!-- Summernote CSS - CDN Link -->
    <link href="<?php echo e(asset('common/css/summernote/summernote.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('common/css/summernote/summernote-lite.min.css')); ?>" rel="stylesheet">
    <!-- //Summernote CSS - CDN Link -->
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script src="<?php echo e(asset('admin/js/custom/image-preview.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/custom/slug.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/custom/form-editor.js')); ?>"></script>

    <!-- Summernote JS - CDN Link -->
    <script src="<?php echo e(asset('common/js/summernote/summernote-lite.min.js')); ?>"></script>
    <script>
        $(document).ready(function() {
            $("#summernote").summernote({dialogsInBody: true});
            $('.dropdown-toggle').dropdown();
        });
    </script>
    <!-- //Summernote JS - CDN Link -->
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/blog/create.blade.php ENDPATH**/ ?>
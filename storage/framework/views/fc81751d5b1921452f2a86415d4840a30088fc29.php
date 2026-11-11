<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Add Live Course')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Add Live Course')); ?></li>
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
                            <h2><?php echo e(__('Add Live Course')); ?></h2>
                        </div>
                        <form action="<?php echo e(route('admin.course.store_standard')); ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label><?php echo e(__('Course Name')); ?> <span class="text-danger">*</span></label>
                                        <input type="text" name="title" value="<?php echo e(old('title')); ?>" placeholder="<?php echo e(__('Course Name')); ?>" class="form-control" required>
                                        <?php if($errors->has('title')): ?>
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('title')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label><?php echo e(__('Course SubTitle')); ?> <span class="text-danger">*</span></label>
                                        <input type="text" name="subtitle" value="<?php echo e(old('subtitle')); ?>" placeholder="<?php echo e(__('Course SubTitle')); ?>" class="form-control" required>
                                        <?php if($errors->has('subtitle')): ?>
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('subtitle')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label
                                        class="label-text-title color-heading font-medium font-14 mb-3"><?php echo e(__('Course Category')); ?> <span class="text-danger">*</span></label>
                                    <select name="category_id" id="category_id" class="form-select"
                                        required>
                                        <option value=""><?php echo e(__('Select Category')); ?></option>
                                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($category->id); ?>" <?php if(old('category_id')): ?>
                                            <?php echo e(old('category_id')==$category->id ? 'selected' : ''); ?> 
                                            <?php endif; ?> ><?php echo e($category->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>

                                    <?php if($errors->has('category_id')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('category_id')); ?></span>
                                    <?php endif; ?>
                                    </div>
                                </div>


                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label
                                        class="label-text-title color-heading font-medium font-16 mb-3"><?php echo e(__('Course Subcategory')); ?> <span class="text-danger">*</span></label>
                                    <select name="subcategory_id" id="subcategory_id" class="form-select"
                                        required>
                                        <option value=""><?php echo e(__('Select Subcategory')); ?></option>
                                        
                                    </select>

                                    <?php if($errors->has('subcategory_id')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('subcategory_id')); ?></span>
                                    <?php endif; ?>
                                </div>



                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label><?php echo e(__('Classes')); ?> <span class="text-danger"></span></label>
                                        <input type="number" name="classes" value="<?php echo e(old('classes')); ?>" placeholder="<?php echo e(__('Classes')); ?>" class="form-control">
                                        <?php if($errors->has('classes')): ?>
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('classes')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>




                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label><?php echo e(__('Duration')); ?> <span class="text-danger"></span></label>
                                        <input type="number" name="duration" value="<?php echo e(old('duration')); ?>" placeholder="<?php echo e(__('Duration')); ?>" class="form-control">
                                        <?php if($errors->has('duration')): ?>
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('duration')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>



                            <div class="row align-items-center">
                                <div class="col-md-12 mb-30">
                                 
                                        <label><?php echo e(__('Duration Type')); ?> <span class="text-danger"></span></label>
                                        <br>
                                        <input type="radio"
                                        checked
                                        
                                        id="1"
                                        name="duration_type"
                                        value="1">
                                        <label for="1"><?php echo e(__('Hours')); ?></label>



                                        <input style="margin-left: 5px" type="radio"
                                      
                                        
                                        id="2"
                                        name="duration_type"
                                        value="2">
                                        <label for="1"><?php echo e(__('Days')); ?></label>

                                      

                                        <input style="margin-left: 5px" type="radio"
                                     
                                        
                                        id="3"
                                        name="duration_type"
                                        value="3">
                                        <label for="1"><?php echo e(__('Months')); ?></label>

                                        <?php if($errors->has('duration')): ?>
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('duration')); ?></span>
                                        <?php endif; ?>
                                 
                                </div>
                            </div>


                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label><?php echo e(__('Short Description')); ?> </label>
                                        <textarea name="short_description" id="short_description" cols="30" rows="8"><?php echo e(old('short_description')); ?></textarea>
                                    
                                        <?php if($errors->has('short_description')): ?>
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('short_description')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>




                                <div class="col-md-12">
                                    <div class="upload-course-item-block-title mb-3">
                                        <h6 class="font-20"><?php echo e(__('Meta Details')); ?></h6>
                                    </div>
                                    <div class="row mb-30" style="margin-top: 1.4rem !important;">
                                        <div class="col-md-6">
                                            <label class="font-medium font-15 color-heading"><?php echo e(__('Meta Title')); ?></label>
                                            <input type="text" name="meta_title" class="form-control" placeholder="<?php echo e(__('Meta Title')); ?>">
                                            <?php if($errors->has('meta_title')): ?>
                                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('meta_title')); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="font-medium font-15 color-heading"><?php echo e(__('Meta Keywords')); ?></label>
                                            <input type="text" name="meta_keywords" class="form-control" placeholder="<?php echo e(__('Type meta keywords (comma separated)')); ?>">
                                            <?php if($errors->has('meta_keywords')): ?>
                                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('meta_keywords')); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="row mb-30" style="margin-top: 2.3rem !important;">
                                        <div class="col-md-12">
                                            <label class="font-medium font-15 color-heading"><?php echo e(__('Meta Description')); ?></label>
                                            <textarea class="form-control" name="meta_description" id="exampleFormControlTextarea1" rows="3" placeholder="<?php echo e(__('Type Meta Description')); ?>"></textarea>
                                            <?php if($errors->has('meta_description')): ?>
                                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('meta_description')); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="row mb-30" style="margin-top: 2rem !important;">
                                        <div class="col-md-12">
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
                                        </div>
                                      
                                    </div>
                                </div>



                                <div class="row align-items-center">
                                    <div class="col-12">
                                        <label
                                            class="label-text-title color-heading font-medium font-16 mb-3"><?php echo e(__('Home Image 1')); ?>

                                        </label>
                                    </div>
                                    <div class="col-md-6 mb-30">
                                        <div class="upload-img-box mt-3 height-200">
                                            <img src="">
                                            <input type="file" name="home_image_1" id="home_image_1" accept="image/*"
                                                onchange="previewFileHome1(this)">
                                            <div class="upload-img-box-icon">
                                                <i class="fa fa-camera"></i>
                                                <p class="m-0"><?php echo e(__('Image')); ?></p>
                                            </div>
                                        </div>
                                        <?php if($errors->has('home_image_1')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('home_image_1')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6 mb-30">
                                        <p class="font-14 color-gray"><?php echo e(__('Recomended image format & size')); ?>:
                                            220px X 360px (5MB)</p>
                                        <p class="font-14 color-gray"><?php echo e(__('Accepted image filetype')); ?>: jpg,
                                            jpeg, png</p>
                                    </div>
                                </div>




                                <div class="row align-items-center">
                                    <div class="col-12">
                                        <label
                                            class="label-text-title color-heading font-medium font-16 mb-3"><?php echo e(__('Home Image 2')); ?>

                                        </label>
                                    </div>
                                    <div class="col-md-6 mb-30">
                                        <div class="upload-img-box mt-3 height-200">
                                            <img src="">
                                            <input type="file" name="home_image_2" id="home_image_2" accept="image/*"
                                                onchange="previewFileHome2(this)">
                                            <div class="upload-img-box-icon">
                                                <i class="fa fa-camera"></i>
                                                <p class="m-0"><?php echo e(__('Image')); ?></p>
                                            </div>
                                        </div>
                                        <?php if($errors->has('home_image_2')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('home_image_2')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6 mb-30">
                                        <p class="font-14 color-gray"><?php echo e(__('Recomended image format & size')); ?>:
                                            260px X 420px (5MB)</p>
                                        <p class="font-14 color-gray"><?php echo e(__('Accepted image filetype')); ?>: jpg,
                                            jpeg, png</p>
                                    </div>
                                </div>




                                <div class="row align-items-center">
                                    <div class="col-12">
                                        <label
                                            class="label-text-title color-heading font-medium font-16 mb-3"><?php echo e(__('Course Image')); ?>

                                        </label>
                                    </div>
                                    <div class="col-md-6 mb-30">
                                        <div class="upload-img-box mt-3 height-200">
                                            <img src="">
                                            <input type="file" name="image" id="image" accept="image/*"
                                                onchange="previewFile(this)" required>
                                            <div class="upload-img-box-icon">
                                                <i class="fa fa-camera"></i>
                                                <p class="m-0"><?php echo e(__('Image')); ?></p>
                                            </div>
                                        </div>
                                        <?php if($errors->has('image')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('image')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6 mb-30">
                                        <p class="font-14 color-gray"><?php echo e(__('Recomended image format & size')); ?>:
                                            575px X 450px (1MB)</p>
                                        <p class="font-14 color-gray"><?php echo e(__('Accepted image filetype')); ?>: jpg,
                                            jpeg, png</p>
                                    </div>
                                </div>







                                <div class="row align-items-center">
                                    <div class="col-12">
                                        <label
                                            class="label-text-title color-heading font-medium font-16 mb-3"><?php echo e(__('Course Cover Image')); ?>

                                        </label>
                                    </div>
                                    <div class="col-md-6 mb-30">
                                        <div class="upload-img-box mt-3 height-200">
                                            <img src="">
                                            <input type="file" name="cover_image" id="cover_image" accept="image/*"
                                                onchange="previewCourseFile(this)">
                                            <div class="upload-img-box-icon">
                                                <i class="fa fa-camera"></i>
                                                <p class="m-0"><?php echo e(__('Cover Image')); ?></p>
                                            </div>
                                        </div>
                                        <?php if($errors->has('cover_image')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('cover_image')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6 mb-30">
                                        <p class="font-14 color-gray"><?php echo e(__('Recomended image format & size')); ?>:
                                            1900px X 400px (5MB)</p>
                                        <p class="font-14 color-gray"><?php echo e(__('Accepted image filetype')); ?>: jpg,
                                            jpeg, png</p>
                                    </div>
                                </div>

                              
                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label><?php echo e(__('Course Price')); ?> <span class="text-danger">*</span></label>
                                        <input type="number" name="price" value="<?php echo e(old('price')); ?>" placeholder="<?php echo e(__('Price')); ?>" class="form-control" required>
                                        <?php if($errors->has('price')): ?>
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('price')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                               
                            
                                <div class="col-md-6">
                                    <div class="input__group mb-25">
                                        <label><?php echo e(__('Course Language')); ?> <span class="text-danger">*</span></label>
                                        <select name="course_lang_id" id="course_lang_id" class="form-select" required>
                                            <option value=""><?php echo e(__('Select Language')); ?></option>
                                            <?php if(!$course_languages->isEmpty()): ?>
                                            <?php $__currentLoopData = $course_languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course_language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($course_language->id); ?>"
                                                <?php if(old('course_language_id')): ?>
                                                <?php echo e(old('course_language_id')==$course_language->id ? 'selected' :
                                                ''); ?>

                                                <?php endif; ?>
                                                ><?php echo e($course_language->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                             
                           
                                <div class="col-md-6">
                                    <div class="input__group mb-25">
                                        <label><?php echo e(__('Visibilty')); ?><span class="text-danger">*</span></label>
                                        <select name="learner_accessibility" id="gender" class="form-select" required>
                                            <option value=""><?php echo e(__('Select Option')); ?></option>
                                            <option value="paid" <?php echo e(old('learner_accessibility') == 'Paid' ? 'selected' : ''); ?> ><?php echo e(__('Paid')); ?></option>
                                            <option value="free" <?php echo e(old('learner_accessibility') == 'Free' ? 'selected' : ''); ?> ><?php echo e(__('Free')); ?></option>
                                          
                                        </select>
                                    </div>
                                </div>



                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label><?php echo e(__('Description')); ?> <span class="text-danger">*</span></label>
                                        <textarea name="description" id="summernote" required><?php echo e(old('description')); ?></textarea>
                                        <?php if($errors->has('description')): ?>
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('description')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                         

                            <div class="row mb-3">
                                <div class="col-md-12 text-right">
                                    <button class="btn btn-primary" type="submit"><?php echo e(__('Save')); ?></button>
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
    <link href="<?php echo e(asset('common/css/summernote/summernote.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('common/css/summernote/summernote-lite.min.css')); ?>" rel="stylesheet">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script src="<?php echo e(asset('admin/js/custom/image-preview.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/custom/admin-profile.js')); ?>"></script>
    <script src="<?php echo e(asset('common/js/summernote/summernote-lite.min.js')); ?>"></script>
    <script>
    $('#category_id').change(function (e) { 
        e.preventDefault();
        var category_id = $(this).val();
  
        $.ajax({
            type: "POST",
            headers:
                        {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
            url: '<?php echo e(route('classes.get_category_subcategories')); ?>',
            data: {category_id:category_id},
            dataType: "JSON",
            success: function (response) {
                var select = $('#subcategory_id');
                        select.empty();
                        select.append('<option value="">Select Subcategory</option>');
                        $.each(response, function(key, value) {
                            select.append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                console.log(response)
            }
        });
 
    });
    </script>

<script>
    $(document).ready(function() {
        $("#summernote").summernote({dialogsInBody: true});
        $('.dropdown-toggle').dropdown();
    });
</script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/course/add.blade.php ENDPATH**/ ?>
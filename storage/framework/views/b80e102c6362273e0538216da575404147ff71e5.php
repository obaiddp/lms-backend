<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Add Student')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Add Student')); ?></li>
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
                            <h2><?php echo e(__('Add Student')); ?></h2>
                        </div>
                        <form action="<?php echo e(route('student.store')); ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input__group mb-25">
                                        <label><?php echo e(__('First Name')); ?> <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" value="<?php echo e(old('first_name')); ?>" placeholder="<?php echo e(__('First Name')); ?>" class="form-control" required>
                                        <?php if($errors->has('first_name')): ?>
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('first_name')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input__group mb-25">
                                        <label><?php echo e(__('Last Name')); ?> <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" value="<?php echo e(old('last_name')); ?>" placeholder="<?php echo e(__('Last Name')); ?>" class="form-control" required>
                                        <?php if($errors->has('last_name')): ?>
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('last_name')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input__group mb-25">
                                        <label><?php echo e(__('Email')); ?> <span class="text-danger">*</span></label>
                                        <input type="email" name="email" value="<?php echo e(old('email')); ?>" placeholder="<?php echo e(__('Email')); ?>" class="form-control" required>
                                        <?php if($errors->has('email')): ?>
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('email')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input__group mb-25">
                                        <label><?php echo e(__('Password')); ?> <span class="text-danger">*</span></label>
                                        <input type="password" name="password" value="<?php echo e(old('password')); ?>" placeholder="<?php echo e(__('Password')); ?>" class="form-control" required>
                                        <?php if($errors->has('password')): ?>
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('password')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input__group mb-25">
                                        <label class="label-text-title color-heading font-medium font-16 mb-3"><?php echo e(__('Area Code')); ?> <span class="text-danger">*</span></label>
                                        <select class="form-control" name="area_code">
                                            <option value ><?php echo e(__("Select Code")); ?></option>
                                            <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($country->phonecode); ?>" <?php if(old('area_code')==$country->
                                                phonecode): ?> selected <?php endif; ?>><?php echo e($country->short_name.'('.$country->phonecode.')'); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <?php if($errors->has('area_code')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('area_code')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input__group mb-25">
                                        <label><?php echo e(__('Phone Number')); ?> <span class="text-danger">*</span></label>
                                        <input type="text" name="phone_number" value="<?php echo e(old('phone_number')); ?>" placeholder="<?php echo e(__('Phone number')); ?>" class="form-control" required>
                                        <?php if($errors->has('phone_number')): ?>
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('phone_number')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input__group mb-25">
                                        <label><?php echo e(__('Address')); ?> <span class="text-danger">*</span></label>
                                        <input type="text" name="address" value="<?php echo e(old('address')); ?>" placeholder="<?php echo e(__('Address')); ?>" class="form-control"
                                               required>
                                        <?php if($errors->has('address')): ?>
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('address')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input__group mb-25">
                                        <label><?php echo e(__('Postal Code')); ?> <span class="text-danger">*</span></label>
                                        <input type="text" name="postal_code" value="<?php echo e(old('postal_code')); ?>" placeholder="<?php echo e(__('Postal Code')); ?>" class="form-control"
                                               required>
                                        <?php if($errors->has('postal_code')): ?>
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('postal_code')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input__group mb-25">
                                        <label><?php echo e(__('Country')); ?></label>
                                        <select name="country_id" id="country_id" class="form-select">
                                            <option value=""><?php echo e(__('Select Country')); ?></option>
                                            <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($country->id); ?>" <?php if(old('country_id')): ?>
                                                    <?php echo e(old('country_id') == $country->id ? 'selected' : ''); ?>

                                                    <?php endif; ?> ><?php echo e($country->country_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input__group mb-25">
                                        <label><?php echo e(__('State')); ?></label>
                                        <select name="state_id" id="state_id" class="form-select">
                                            <option value=""><?php echo e(__('Select State')); ?></option>
                                            <?php if(old('country_id')): ?>
                                                <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($state->id); ?>" <?php echo e(old('state_id') == $state->id ? 'selected' : ''); ?> ><?php echo e($state->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input__group mb-25">
                                        <label><?php echo e(__('City')); ?></label>
                                        <select name="city_id" id="city_id" class="form-select">
                                            <option value=""><?php echo e(__('Select City')); ?></option>
                                            <?php if(old('state_id')): ?>
                                                <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($city->id); ?>" <?php echo e(old('city_id') == $city->id ? 'selected' : ''); ?> ><?php echo e($city->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input__group mb-25">
                                        <label><?php echo e(__('Gender')); ?><span class="text-danger">*</span></label>
                                        <select name="gender" id="gender" class="form-select" required>
                                            <option value=""><?php echo e(__('Select Option')); ?></option>
                                            <option value="Male" <?php echo e(old('gender') == 'Male' ? 'selected' : ''); ?> ><?php echo e(__('Male')); ?></option>
                                            <option value="Female" <?php echo e(old('gender') == 'Female' ? 'selected' : ''); ?> ><?php echo e(__('Female')); ?></option>
                                            <option value="Others" <?php echo e(old('gender') == 'Others' ? 'selected' : ''); ?> ><?php echo e(__('Others')); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label><?php echo e(__('Enrolled Course')); ?> (<?php echo e(__('Live')); ?>)</label>

                                        <select  multiple="multiple" name="course[]" id="course" class="form-select">
                                        <option value=""><?php echo e(__('Select Courses')); ?></option>
                                        <?php if(!$allCourses->isEmpty()): ?>
                                        <?php $__currentLoopData = $allCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($course->id); ?>" <?php echo e(old('course_id') == $course->id ? 'selected' : ''); ?> ><?php echo e($course->title); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </select>

                                        
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input__group mb-25">
                                        <label><?php echo e(__('Is Featured')); ?><span class="text-danger">*</span></label>
                                        <select name="is_featured" id="is_featured" class="form-select" required>
                                            <option value=""><?php echo e(__('Select Option')); ?></option>
                                            <option value="1"  ><?php echo e(__('Yes')); ?></option>
                                            <option value="0"  ><?php echo e(__('No')); ?></option>
                                         
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label><?php echo e(__('About Student')); ?> <span class="text-danger">*</span></label>
                                        <textarea name="about_me" id="" cols="15" rows="5" required><?php echo e(old('about_me')); ?></textarea>
                                        <?php if($errors->has('about_me')): ?>
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('about_me')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="upload-img-box mb-25">
                                        <img src="">
                                        <input type="file" name="image" id="image" accept="image/*" onchange="previewFile(this)"
                                        required>
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                            <p class="m-0"><?php echo e(__('Image')); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <?php if($errors->has('image')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('image')); ?></span>
                                <?php endif; ?>
                                <p><?php echo e(__('Accepted Image Files')); ?>: JPEG, JPG, PNG <br> <?php echo e(__('Accepted Size')); ?>: 300 x 300 (1MB)</p>
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
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script src="<?php echo e(asset('admin/js/custom/image-preview.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/custom/admin-profile.js')); ?>"></script>
    <script>
              $(document).ready(function () {
            $('#course').select2({
    // dropdownParent: $('#edit_drop_modal'),
    width: '100%',
    allowClear: true,
    placeholder: 'Select Courses',
 
   
});

        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/student/add.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
<!-- Page content area start -->
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb__content">
                    <div class="breadcrumb__content__left">
                        <div class="breadcrumb__title">
                            <h2><?php echo e(__('Add Group Class')); ?></h2>
                        </div>
                    </div>
                    <div class="breadcrumb__content__right">
                        <nav aria-label="breadcrumb">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a
                                        href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Add Group Class')); ?></li>
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
                        <h2><?php echo e(__('Add Group Class')); ?></h2>
                    </div>
                    <form action="<?php echo e(route('classes.store_group')); ?>" method="post" class="form-horizontal"
                        enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                          
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label class="label-text-title color-heading font-medium font-16 mb-3"><?php echo e(__('Student')); ?> <span class="text-danger">*</span></label>
                                    <select class="form-control" name="student_id[]" id="student_id" required multiple>
                                        
                                        <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($student->id); ?>" <?php if(old('student_id')==$student->id): ?> selected <?php endif; ?>><?php echo e($student->first_name.' '.$student->last_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php if($errors->has('student_id')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('student_id')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label class="label-text-title color-heading font-medium font-16 mb-3"><?php echo e(__('Instructor')); ?> <span class="text-danger">*</span></label>
                                    <select class="form-control" name="instructor_id" id="instructor_id" required>
                                        <option value><?php echo e(__("Select Instructor")); ?></option>
                                        <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($instructor->id); ?>" <?php if(old('instructor_id')==$instructor->id): ?> selected <?php endif; ?>><?php echo e($instructor->first_name.' '.$instructor->last_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php if($errors->has('instructor_id')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('instructor_id')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label class="label-text-title color-heading font-medium font-16 mb-3"><?php echo e(__('Course')); ?> <span class="text-danger">*</span></label>
                                    <select class="form-control" name="course_id" id="course_id" required>
                                        <option value=""><?php echo e(__("Select Course")); ?></option>
                                       
                                    </select>
                                    <?php if($errors->has('course_id')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('course_id')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                       
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label><?php echo e(__('Total Classes')); ?> <span class="text-danger">*</span></label>
                                    <input type="number" name="total_classes" value="<?php echo e(old('total_classes')); ?>"
                                        placeholder="<?php echo e(__('Total Classes')); ?>" class="form-control" required>
                                    <?php if($errors->has('total_classes')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('total_classes')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                          
                            <div class="col-md-12">
                                <div class="input__group mb-25">
                                    <label class="label-text-title color-heading font-medium font-16 mb-3"><?php echo e(__('Prefer Days')); ?> <span class="text-danger">*</span></label>
                                    <select id="select2" class="form-control" name="preferdays[]" required multiple>
                                        
                                        <option value="Monday">Monday</option>
                                        <option value="Tuesday">Tuesday</option>
                                        <option value="Wednesday">Wednesday</option>
                                        <option value="Thursday">Thursday</option>
                                        <option value="Friday">Friday</option>
                                        <option value="Saturday">Saturday</option>
                                        <option value="Sunday">Sunday</option>
                                    </select>
                               
                                </div>
                            </div>
                            
                           
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label><?php echo e(__('Prefer Time')); ?> <span class="text-danger">*</span></label>
                                    <input type="time" name="prefer_time" value="<?php echo e(old('prefer_time')); ?>"
                                        placeholder="<?php echo e(__('Prefer Time')); ?>" class="form-control" required>
                                    <?php if($errors->has('prefer_time')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('prefer_time')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                          
                          
                            
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label><?php echo e(__('Select Date')); ?> <span class="text-danger">*</span></label>
                                    <input type="date" name="prefer_date" value="<?php echo e(old('prefer_date')); ?>"
                                        placeholder="<?php echo e(__('Select Date')); ?>" class="form-control" required>
                                    <?php if($errors->has('prefer_date')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('prefer_date')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                          

                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label><?php echo e(__('Select Duration')); ?> <span class="text-danger">*</span></label>
                                    <input type="number" name="duration" value="<?php echo e(old('duration')); ?>"
                                        placeholder="<?php echo e(__('Select Duration')); ?>" class="form-control" required>
                                    <?php if($errors->has('duration')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('duration')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>



                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label class="label-text-title color-heading font-medium font-16 mb-3"><?php echo e(__('Learning Tool')); ?> <span class="text-danger">*</span></label>
                                    <select class="form-control" name="learning_tool" required>
                                        <option value =""><?php echo e(__("Select Learning Tool")); ?></option>
                                        
                                        <option value="BBB">BigBlueButton</option>
                                        <option value="Zoom">Zoom</option>
                                        <option value="Google_Meet">Google Meet</option>
                                        
                                    </select>
                               
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
<link rel="stylesheet" href="<?php echo e(asset('common/css/select2.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
<script src="<?php echo e(asset('admin/js/custom/image-preview.js')); ?>"></script>
<script src="<?php echo e(asset('admin/js/custom/admin-profile.js')); ?>"></script>
<script src="<?php echo e(asset('common/js/select2.min.js')); ?>"></script>
<script>
    $(document).ready(function () {


$("#student_id").select2({
     width: '100%',
  placeholder: "Select Students",
//   multiple: true,
//   allowClear: true
});


$("#instructor_id").select2({
     width: '100%',
  placeholder: "Select Instructor",
//   multiple: true,
//   allowClear: true
});



$("#select2").select2({
     width: '100%',
  placeholder: "Select Day",
//   multiple: true,
//   allowClear: true
});


    });
     

    $('#instructor_id').change(function (e) { 
        e.preventDefault();
        var instructor_id = $(this).val();
        $.ajax({
            type: "POST",
            headers:
                        {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
            url: '<?php echo e(route('classes.get_tutor_courses')); ?>',
            data: {instructor_id:instructor_id},
            dataType: "JSON",
            success: function (response) {
                var select = $('#course_id');
                        select.empty();
                        select.append('<option value="">Select Course</option>');
                        $.each(response, function(key, value) {
                            select.append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                console.log(response)
            }
        });
        // alert(instructor_id)
        
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/classes/add_group.blade.php ENDPATH**/ ?>
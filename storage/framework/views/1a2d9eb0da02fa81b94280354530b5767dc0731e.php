<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Add Student In Course')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Add Student In Course')); ?></li>
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
                            <h2><?php echo e(__('Enroll In Course')); ?></h2>
                        </div>
                        <form action="<?php echo e(route('admin.course.enroll.store')); ?>" method="post" class="form-horizontal" >
                            <?php echo csrf_field(); ?>

                            <div class="input__group mb-25">
                                <label><?php echo e(__('Student')); ?> <span class="text-danger">*</span></label>
                                <select name="user_id" id="user_id" required class="">
                                    <option value="">--<?php echo e(__('Select Student')); ?>--</option>
                                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($user->id); ?>" <?php if(old('user_id') == $user->id): ?> selected <?php endif; ?>><?php echo e(@$user->student->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php if($errors->has('user_id')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('user_id')); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="input__group mb-25">
                                <label><?php echo e(__('Course')); ?> <span class="text-danger">*</span></label>
                                <select name="course_id" required id="course_id">
                                    <option value="">--<?php echo e(__('Select Course')); ?>--</option>
                                    <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($course->id); ?>" <?php if(old('course_id') == $course->id): ?> selected <?php endif; ?>><?php echo e($course->title); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php if($errors->has('course_id')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('course_id')); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="input__group mb-25">
                                <label><?php echo e(__('Expired After Days')); ?> </label>
                               <input type="number" class="form-control" name="expired_after_days" min=1>
                                <?php if($errors->has('expired_after_days')): ?>
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('expired_after_days')); ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn btn-primary"><?php echo e(__('Add')); ?></button>
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


<?php $__env->startPush('script'); ?>
    <script src="<?php echo e(asset('admin/js/standalone/selectize.min.js')); ?>" ></script>
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/selectize.bootstrap3.min.css')); ?>" />
    <script>
        $(document).ready(function () {
            $('select').selectize({
                sortField: 'text'
            });
        })
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/course/enroll-student.blade.php ENDPATH**/ ?>
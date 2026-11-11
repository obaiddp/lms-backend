<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Course Upload Rules')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Upload Rules')); ?></li>
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
                            <h2><?php echo e(__('Course Upload Rules')); ?></h2>
                        </div>
                        <form action="<?php echo e(route('course-rules.store')); ?>" method="post" class="form-horizontal">
                            <?php echo csrf_field(); ?>
                            <div class="input__group mb-25 row">
                                <label for="title" class="col-lg-11"> <?php echo e(__('Title')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-11">
                                    <input type="text" name="courseUploadRuleTitle" id="courseUploadRuleTitle" value="<?php echo e(get_option('courseUploadRuleTitle')); ?>" class="form-control" required>
                                </div>
                            </div>

                            <div id="add_repeater" class="mb-3">
                                <div data-repeater-list="course_upload_rules" class="">
                                    <?php if($courseRules->count() > 0): ?>
                                        <?php $__currentLoopData = $courseRules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $courseRule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div data-repeater-item="" class="form-group row ">
                                                <input type="hidden" name="id" value="<?php echo e(@$courseRule['id']); ?>"/>

                                                <div class="custom-form-group mb-3 col-md-11 col-lg-11 col-xl-11 col-xxl-11">
                                                    <label for="description_<?php echo e(@$courseRule['id']); ?>" class="text-lg-right text-black"> <?php echo e(__('Description')); ?> </label>
                                                    <textarea name="description" id="description_<?php echo e(@$courseRule['id']); ?>" class="form-control" rows="5"
                                                              required><?php echo e($courseRule->description); ?></textarea>

                                                </div>

                                                <div class="col-lg-1 mt-4 removeClass">
                                                    <label class="text-lg-right text-black opacity-0"><?php echo e(__('Remove')); ?></label>
                                                    <a href="javascript:;" data-repeater-delete=""
                                                       class="btn btn-icon-remove btn-danger">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <div data-repeater-item="" class="form-group row">
                                            <div class="custom-form-group mb-3 col-md-11 col-lg-11 col-xl-11 col-xxl-11">
                                                <label for="description" class="text-lg-right text-black"> <?php echo e(__('Description')); ?> </label>
                                                <textarea name="description" id="description" class="form-control" rows="5" required></textarea>

                                            </div>

                                            <div class="col-lg-1 mt-4 removeClass">
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

                            <div class="row mb-3">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn btn-primary"><?php echo e(__('Save')); ?></button>
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
    <script src="<?php echo e(asset('common/js/jquery.repeater.min.js')); ?>"></script>
    <script src="<?php echo e(asset('common/js/add-repeater.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/course/upload-rules.blade.php ENDPATH**/ ?>
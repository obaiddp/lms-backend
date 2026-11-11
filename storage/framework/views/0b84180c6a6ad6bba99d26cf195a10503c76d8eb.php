<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Add Language')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('course-language.index')); ?>"><?php echo e(__('Languages')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Add Language')); ?></li>
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
                                <h2><?php echo e(__('Add Language')); ?></h2>
                            </div>
                            <form action="<?php echo e(route('course-language.store')); ?>" method="post" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>

                                <div class="input__group mb-25">
                                    <label for="name"> <?php echo e(__('Name')); ?><span class="text-danger">*</span></label>
                                    <div>
                                        <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>" class="form-control" placeholder=" <?php echo e(__('Name')); ?> ">
                                        <?php if($errors->has('name')): ?>
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('name')); ?></span>
                                        <?php endif; ?>
                                    </div>
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


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/course/language/create.blade.php ENDPATH**/ ?>
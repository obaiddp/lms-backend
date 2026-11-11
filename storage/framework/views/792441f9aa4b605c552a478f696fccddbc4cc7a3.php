<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Add Role')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('role.index')); ?>"><?php echo e(__('Roles')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__($title)); ?></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-horizontal__item bg-style">
                        <div class="item-top mb-30">
                            <h2><?php echo e(__('Add Role')); ?></h2>
                        </div>
                        <form action="<?php echo e(route('role.store')); ?>" method="post" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>

                            <div class="custom-form-group mb-3 row">
                                <label for="name" class="col-lg-3 text-lg-right text-black"> <?php echo e(__('Name')); ?> </label>
                                <div class="col-lg-9">
                                    <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>" class="form-control flat-input" placeholder="<?php echo e(__('Name')); ?>">
                                    <?php if($errors->has('name')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('name')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="custom-form-group mb-3 row">
                                <label for="name" class="col-lg-3 text-lg-right text-black"> <?php echo e(__('Select Permission')); ?> </label>
                                <div class="col-lg-9">
                                   <div class="row text-black">
                                       <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                       <div class="col-md-4 mb-2">
                                           <div class="form-check mb-0 d-flex align-items-center">
                                               <input class="form-check-input" type="checkbox" name="permissions[]" id="permission<?php echo e($permission->id); ?>" value="<?php echo e($permission->id); ?>" >
                                               <label class="form-check-label m-lg-1 mb-0 color-heading" for="permission<?php echo e($permission->id); ?>"><?php echo e(ucwords(str_ireplace("_", " ", $permission->name))); ?> </label>
                                           </div>
                                       </div>
                                       <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                   </div>
                                    <?php if($errors->has('permissions')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('permissions')); ?></span>
                                    <?php endif; ?>
                                </div>
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/user/role/create.blade.php ENDPATH**/ ?>
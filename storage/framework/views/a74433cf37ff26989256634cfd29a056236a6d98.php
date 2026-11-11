<?php $__env->startSection('content'); ?>
<!-- Page content area start -->
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="form-horizontal__item bg-style">
                    <div class="item-top mb-30">
                        <h2><?php echo e(__('Add User')); ?></h2>
                    </div>
                    <form action="<?php echo e(route('user.store')); ?>" method="post" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>

                        <div class="custom-form-group mb-3 row">
                            <label for="name" class="col-lg-3 text-lg-right text-black"> <?php echo e(__('Name')); ?> </label>
                            <div class="col-lg-9">
                                <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>"
                                    class="form-control flat-input" placeholder=" <?php echo e(__('Name')); ?> ">
                                <?php if($errors->has('name')): ?>
                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('name')); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="custom-form-group mb-3 row">
                            <label for="email" class="col-lg-3 text-lg-right text-black"> <?php echo e(__('Email')); ?> </label>
                            <div class="col-lg-9">
                                <input type="email" name="email" id="email" value="<?php echo e(old('email')); ?>"
                                    class="form-control flat-input" placeholder=" <?php echo e(__('Email')); ?> ">
                                <?php if($errors->has('email')): ?>
                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('email')); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>


                        <div class="custom-form-group mb-3 row">
                            <label class="col-lg-3 text-lg-right text-black"><?php echo e(__('Area
                                Code')); ?></label>
                            <div class="col-lg-9">
                                <div class="input__group">
                                    <select class="form-control" name="area_code">
                                        <option value><?php echo e(__("Select Code")); ?></option>
                                        <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($country->phonecode); ?>" <?php if(old('area_code')==$country->
                                            phonecode): ?> selected <?php endif; ?>><?php echo e($country->short_name.'('.$country->phonecode.')'); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <?php if($errors->has('area_code')): ?>
                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('area_code')); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="custom-form-group mb-3 row">
                            <label for="phone_number" class="col-lg-3 text-lg-right text-black"> <?php echo e(__('Phone')); ?>

                            </label>
                            <div class="col-lg-9">
                                <input type="text" name="phone_number" id="phone_number" value="<?php echo e(old('phone_number')); ?>"
                                    class="form-control flat-input" placeholder=" <?php echo e(__('Phone')); ?> ">
                                <?php if($errors->has('phone_number')): ?>
                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('phone_number')); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="custom-form-group mb-3 row">
                            <label for="address" class="col-lg-3 text-lg-right text-black"> <?php echo e(__('Address')); ?> </label>
                            <div class="col-lg-9">
                                <textarea name="address" id="address" class="form-control"
                                    placeholder="Address"><?php echo e(old('address')); ?></textarea>
                                <?php if($errors->has('address')): ?>
                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('address')); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="custom-form-group mb-3 row">
                            <label for="role_name" class="col-lg-3 text-lg-right text-black"> <?php echo e(__('Select Role')); ?>

                            </label>
                            <div class="col-lg-9">
                                <select name="role_name" id="role_name" class="form-control">
                                    <option value=""><?php echo e(__('Select Role')); ?></option>
                                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($role->name); ?>" <?php echo e(old('role_name')==$role->name ? 'selected' : ''); ?>

                                        ><?php echo e($role->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php if($errors->has('role_name')): ?>
                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('role_name')); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="custom-form-group mb-3 row">
                            <label for="password" class="col-lg-3 text-lg-right text-black"> <?php echo e(__('Password')); ?> </label>
                            <div class="col-lg-9">
                                <input type="password" name="password" id="password" class="form-control flat-input"
                                    placeholder=" <?php echo e(__('Password')); ?> ">
                                <?php if($errors->has('password')): ?>
                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('password')); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mb-3">
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/user/create.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Application Settings')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__(@$title)); ?></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-4">
                    <?php echo $__env->make('admin.application_settings.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
                <div class="col-lg-9 col-md-8">
                    <div class="customers__area bg-style mb-30">
                        <div class="item-title d-flex justify-content-between">
                            <h2><?php echo e(__('State')); ?></h2>
                            <button class="btn btn-success btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#add-todo-modal">
                                <i class="fa fa-plus"></i> <?php echo e(__('Add State')); ?>

                            </button>
                        </div>
                        <div class="customers__table">
                            <table id="customers-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('SL')); ?></th>
                                    <th><?php echo e(__('Name')); ?></th>
                                    <th><?php echo e(__('Country')); ?></th>
                                    <th><?php echo e(__('Action')); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="removable-item">
                                        <td><?php echo e($loop->iteration); ?></td>
                                        <td><?php echo e($state->name); ?></td>
                                        <td><?php echo e(@$state->country->country_name); ?></td>

                                        <td>
                                            <div class="action__buttons">
                                                <a href="<?php echo e(route('settings.location.state.edit', [$state->id, Str::slug($state->name)])); ?>" class=" btn-action mr-1 edit" data-toggle="tooltip" title="Edit">
                                                    <img src="<?php echo e(asset('admin/images/icons/edit-2.svg')); ?>" alt="edit">
                                                </a>
                                                <button class="btn-action ms-2 deleteItem" data-formid="delete_row_form_<?php echo e($state->id); ?>">
                                                    <img src="<?php echo e(asset('admin/images/icons/trash-2.svg')); ?>" alt="trash">
                                                </button>

                                                <form action="<?php echo e(route('settings.location.state.delete', $state->id)); ?>" method="post" id="delete_row_form_<?php echo e($state->id); ?>">
                                                    <?php echo e(method_field('DELETE')); ?>

                                                    <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <div class="mt-3">
                                <?php echo e($states->links()); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page content area end -->

    <!-- Add Modal section start -->
    <div class="modal fade" id="add-todo-modal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo e(__('Add State')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="input__group mb-25">
                                    <label for="name"><?php echo e(__('Country name')); ?></label>
                                    <select name="country_id" required>
                                        <option value="">--<?php echo e(__('Select Option')); ?>--</option>
                                        <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($country->id); ?>"><?php echo e($country->country_name); ?> </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php if($errors->has('country_id')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('country_id')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="input__group mb-25">
                                    <label for="name"><?php echo e(__('Name')); ?></label>
                                    <input type="text" name="name" id="name" placeholder="<?php echo e(__('Type name')); ?>" value="<?php echo e(old('name')); ?>" required>
                                    <?php if($errors->has('name')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('name')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-purple"><?php echo e(__('Save')); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add Modal section end -->
<?php $__env->stopSection(); ?>

<?php $__env->startPush('style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/jquery.dataTables.min.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script src="<?php echo e(asset('admin/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/custom/data-table-page.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/location/state.blade.php ENDPATH**/ ?>
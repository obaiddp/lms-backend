<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('All Users')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('All Users')); ?></li>
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
                            <h2><?php echo e(__('All Users')); ?></h2>
                            <a href="<?php echo e(route('user.create')); ?>" class="btn btn-success btn-sm"> <i class="fa fa-plus"></i> <?php echo e(__('Add User')); ?> </a>
                        </div>
                        <div class="customers__table">
                            <table id="customers-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('Name')); ?></th>
                                    <th><?php echo e(__('Email')); ?></th>
                                    <th><?php echo e(__('Address')); ?></th>
                                    <th><?php echo e(__('Phone')); ?></th>
                                    <th><?php echo e(__('Role')); ?></th>
                                    <th><?php echo e(__('Status')); ?></th>
                                    <th><?php echo e(__('Action')); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="removable-item">
                                        <td>
                                            <?php echo e($user->name); ?>

                                        </td>
                                        <td>
                                            <?php echo e($user->email); ?>

                                        </td>
                                        <td>
                                            <?php echo e($user->address); ?>

                                        </td>
                                        <td>
                                            <?php echo e($user->phone_number); ?>

                                        </td>
                                        <td>  <?php if(count($user->getRoleNames()) > 0): ?> <?php echo e($user->getRoleNames()[0]); ?><?php endif; ?> </td>
                                        <td>  <?php if($user->deleted_at == null): ?>
                                                Active
                                            <?php else: ?>
                                                Deleted
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($user->deleted_at == null): ?>
                                                <div class="action__buttons">
                                                    <a href="<?php echo e(route('user.edit', [$user->id])); ?>" class="btn-action" title="Edit User">
                                                        <img src="<?php echo e(asset('admin/images/icons/edit-2.svg')); ?>" alt="edit">
                                                    </a>
                                                    <a href="javascript:void(0);" data-url="<?php echo e(route('user.delete', [$user->id])); ?>" class="btn-action delete" title="Delete User">
                                                        <img src="<?php echo e(asset('admin/images/icons/trash-2.svg')); ?>" alt="trash">
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <div class="mt-3">
                                <?php echo e($users->links()); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Page content area end -->
<?php $__env->stopSection(); ?>

<?php $__env->startPush('style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/jquery.dataTables.min.css')); ?>" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script src="<?php echo e(asset('admin/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/custom/data-table-page.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/user/index.blade.php ENDPATH**/ ?>
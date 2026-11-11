<?php $__env->startSection('content'); ?>
<!-- Page content area start -->
<div class="page-content">
    <div class="container-fluid">
        <!-- Breadcrumb Start -->
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb__content">
                    <div class="breadcrumb__content__left">
                        <div class="breadcrumb__title">
                            <h2><?php echo e(__('Licenses')); ?></h2>
                        </div>
                    </div>
                    <div class="breadcrumb__content__right">
                        <nav aria-label="breadcrumb">
                            <ul class="breadcrumb">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['manage_dashboard','all_dashboard'])): ?>
                                    <li class="breadcrumb-item">
                                        <a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a>
                                    </li>
                                <?php endif; ?>
                                <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('All Licenses')); ?></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- Breadcrumb End -->

        <!-- License Table Start -->
        <div class="row">
            <div class="col-md-12">
                <div class="customers__area bg-style mb-30">
                    <div class="item-title d-flex justify-content-between align-items-center">
                        <h2><?php echo e(__('All Licenses')); ?></h2>
                        <a href="<?php echo e(route('admin.license.create')); ?>" class="btn btn-success">
                            <?php echo e(__('Create New License')); ?>

                        </a>
                    </div>

                    <div class="customers__table">
                        <table id="license-table" class="row-border data-table-filter table-style">
                            <thead>
                                <tr>
                                    <th><?php echo e(__('Sr No.')); ?></th>
                                    <th><?php echo e(__('Vault User')); ?></th>
                                    <th><?php echo e(__('License Key')); ?></th>
                                    <th><?php echo e(__('Courses Attached')); ?></th>
                                    <th><?php echo e(__('Number Of Devices')); ?></th>
                                    <th><?php echo e(__('Start Date')); ?></th>
                                    <th><?php echo e(__('Expiry Date')); ?></th>
                                    <th><?php echo e(__('Status')); ?></th>
                                    <th><?php echo e(__('Action')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                <?php $__currentLoopData = $licenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $license): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="removable-item">
                                        <td><?php echo e($i++); ?></td>

                                        <td>
                                            <?php if($license->vaultUser): ?>
                                                <?php echo e($license->vaultUser->name); ?>

                                                <?php if($license->vaultUser->email): ?>
                                                    <br><small><?php echo e($license->vaultUser->email); ?></small>
                                                <?php endif; ?>
                                            <?php elseif($license->user): ?>
                                                <?php echo e($license->user->name); ?>

                                                <br><small><?php echo e($license->user->email); ?></small>
                                            <?php endif; ?>
                                        </td>

                                        <td><code><?php echo e($license->key); ?></code></td>

                                        <td>
                                            <?php if($license->courses && count($license->courses) > 0): ?>
                                                <?php $__currentLoopData = $license->courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <span class="badge bg-info text-white mb-1"><?php echo e($course->title); ?></span><br>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?php echo e(__('No Courses')); ?></span>
                                            <?php endif; ?>
                                        </td>

                                        <td><?php echo e($license->licenseDevices()->count()); ?></td>

                                        <td><?php echo e(date('d-m-Y', strtotime($license->start_date))); ?></td>

                                        <td><?php echo e(date('d-m-Y', strtotime($license->end_date))); ?></td>

                                        <td>
                                            <?php if($license->status == 'active'): ?>
                                                <span class="badge bg-success"><?php echo e(__('Active')); ?></span>
                                            <?php elseif($license->status == 'expired'): ?>
                                                <span class="badge bg-danger"><?php echo e(__('Expired')); ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark"><?php echo e(__('Inactive')); ?></span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            

                                            <a href="<?php echo e(route('admin.license.usage-history', $license->id)); ?>" class="btn-action mr-10" title="<?php echo e(__('Usage history')); ?>">
                                                <img src="<?php echo e(asset('admin/images/icons/recent.svg')); ?>" alt="<?php echo e(__('Usage history')); ?>">
                                            </a>

                                            <a href="<?php echo e(route('admin.license.edit', $license->id)); ?>" class="btn-action mr-10" title="Edit License">
                                                <img src="<?php echo e(asset('admin/images/icons/edit-2.svg')); ?>" alt="edit">
                                            </a>

                                            <button class="ms-3">
                                                    <span data-formid="delete_row_form_<?php echo e($license->id); ?>" class="deleteItem">
                                                        <img src="<?php echo e(asset('admin/images/icons/trash-2.svg')); ?>" alt="trash">
                                                    </span>
                                            </button>
                                            <form action="<?php echo e(route('admin.license.delete', ['id'=>$license->id])); ?>" method="post" id="delete_row_form_<?php echo e($license->id); ?>">
                                                <?php echo e(method_field('DELETE')); ?>

                                                <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                                            </form>

                                        
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>

                        <div class="mt-3">
                            <?php echo e($licenses->links()); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- License Table End -->
    </div>
</div>
<!-- Page content area end -->
<?php $__env->stopSection(); ?>

<?php $__env->startPush('style'); ?>
<link rel="stylesheet" href="<?php echo e(asset('admin/css/jquery.dataTables.min.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
<script src="<?php echo e(asset('admin/js/jquery.dataTables.min.js')); ?>"></script>
<script src="<?php echo e(asset('admin/js/custom/data-table-page.js')); ?>"></script>

<script>
'use strict';

</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/license/index.blade.php ENDPATH**/ ?>
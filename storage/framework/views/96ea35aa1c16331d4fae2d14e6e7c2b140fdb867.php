<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Coupon')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Coupon')); ?></li>
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
                            <h2><?php echo e(__('Coupon List')); ?></h2>
                            <a href="<?php echo e(route('coupon.create')); ?>" class="btn btn-success btn-sm"> <i class="fa fa-plus"></i> <?php echo e(__('Add Coupon')); ?> </a>
                        </div>
                        <div class="customers__table">
                            <table id="customers-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('SL')); ?></th>
                                    <th><?php echo e(__('Coupon Code Name')); ?></th>
                                    <th><?php echo e(__('Duration')); ?></th>
                                    <th><?php echo e(__('Details')); ?></th>
                                    <th><?php echo e(__('Creator')); ?></th>
                                    <th><?php echo e(__('Status')); ?></th>
                                    <th><?php echo e(__('Action')); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $coupons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coupon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="removable-item">
                                        <td><?php echo e($loop->iteration); ?></td>
                                        <td><?php echo e($coupon->coupon_code_name); ?></td>
                                        <td>
                                            <div class="finance-table-inner-item my-2">
                                                <span class="fw-bold mr-1"><?php echo e(__('Start Date')); ?>:</span> <?php echo e($coupon->start_date); ?>

                                            </div>

                                            <div class="finance-table-inner-item my-2">
                                                <span class="fw-bold mr-1"><?php echo e(__('End Date')); ?>:</span><?php echo e($coupon->end_date); ?>

                                            </div>
                                        </td>

                                        <td>
                                            <span class="fw-bold mr-1"><?php echo e(__('Coupon Type')); ?>:</span>
                                            <?php if($coupon->coupon_type == 1): ?>
                                                <?php echo e(__('Global')); ?>

                                            <?php elseif($coupon->coupon_type == 2): ?>
                                                <?php echo e(__('Instructor')); ?>

                                            <?php elseif($coupon->coupon_type == 3): ?>
                                                <?php echo e(__('Course')); ?>

                                            <?php endif; ?>
                                            <br>
                                            <span class="fw-bold mr-1"><?php echo e(__('Minimum Amount to Use')); ?>: </span>
                                            <?php if(get_currency_placement() == 'after'): ?>
                                                <?php echo e($coupon->minimum_amount); ?> <?php echo e(get_currency_symbol()); ?>

                                            <?php else: ?>
                                                <?php echo e(get_currency_symbol()); ?> <?php echo e($coupon->minimum_amount); ?>

                                            <?php endif; ?>

                                            <br>
                                            <span class="fw-bold mr-1">Percentage: </span><?php echo e($coupon->percentage); ?>%
                                        </td>
                                        <td><?php echo e(@$coupon->creator->name); ?></td>

                                        <td>
                                            <?php if($coupon->status == 1): ?>
                                                <span class="status bg-green"><?php echo e(__('Active')); ?></span>
                                            <?php else: ?>
                                                <span class="status bg-red"><?php echo e(__('Deactivated')); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="action__buttons">
                                                <a href="<?php echo e(route('coupon.edit', [$coupon->uuid])); ?>" class="btn-action" title="Edit">
                                                    <img src="<?php echo e(asset('admin/images/icons/edit-2.svg')); ?>" alt="edit">
                                                </a>
                                                <a href="javascript:void(0);" data-url="<?php echo e(route('coupon.delete', [$coupon->uuid])); ?>" class="btn-action delete" title="Delete">
                                                    <img src="<?php echo e(asset('admin/images/icons/trash-2.svg')); ?>" alt="trash">
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <div class="mt-3">
                                <?php echo e($coupons->links()); ?>

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
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/jquery.dataTables.min.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script src="<?php echo e(asset('admin/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/custom/data-table-page.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/coupon/index.blade.php ENDPATH**/ ?>
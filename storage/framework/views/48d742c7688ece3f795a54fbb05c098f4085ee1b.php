<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Revenue Report')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Revenue report')); ?></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/shop.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-purple">
                                <?php if(get_currency_placement() == 'after'): ?>
                                    <?php echo e($grand_money_from_consultation - $cancel_consultation_money); ?> <?php echo e(get_currency_symbol()); ?>

                                <?php else: ?>
                                    <?php echo e(get_currency_symbol()); ?> <?php echo e($grand_money_from_consultation - $cancel_consultation_money); ?>

                                <?php endif; ?>
                            </h2>
                            <h3><?php echo e(__('Grand Total From Consultation')); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/money-loss.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-purple">
                                <?php if(get_currency_placement() == 'after'): ?>
                                    <?php echo e($cancel_consultation_money); ?> <?php echo e(get_currency_symbol()); ?>

                                <?php else: ?>
                                    <?php echo e(get_currency_symbol()); ?> <?php echo e($cancel_consultation_money); ?>

                                <?php endif; ?>
                            </h2>
                            <h3><?php echo e(__('Cancel Money From Consultation')); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/laptop.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-purple"><?php echo e($total_enrolment_in_consultation - $total_cancel_consultation); ?></h2>
                            <h3><?php echo e(__('Total Enrolled in Consultation')); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/principal.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-purple"><?php echo e($total_cancel_consultation); ?></h2>
                            <h3><?php echo e(__('Total Cancel Enrolled in Consultation')); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="customers__area bg-style mb-30">
                        <div class="item-title d-flex justify-content-between">
                            <h2><?php echo e(__('Revenue Report')); ?> (<?php echo e(__('Consultation')); ?>)</h2>
                            <div>
                                <a href="<?php echo e(route('course-report.revenue-report')); ?>" class="btn btn-success btn-sm">
                                    Course Report <span class="iconify" data-icon="akar-icons:arrow-right"></span>
                                </a>
                                <a href="<?php echo e(route('bundle-report.revenue-report')); ?>" class="btn btn-success btn-sm">
                                    Bundle Report <span class="iconify" data-icon="akar-icons:arrow-right"></span>
                                </a>
                            </div>
                        </div>
                        <div class="customers__table">
                            <table id="customers-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('SL')); ?></th>
                                    <th><?php echo e(__('Slot Details')); ?></th>
                                    <th><?php echo e(__('Instructor')); ?></th>
                                    <th><?php echo e(__('Total Admin Commission')); ?></th>
                                    <th><?php echo e(__('Total Instructor Commission')); ?></th>
                                    <th><?php echo e(__('Total Enroll')); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $consultationOrderItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $consultationOrderItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="removable-item">
                                        <td><?php echo e($loop->iteration); ?></td>
                                        <td>
                                            <strong><?php echo e(__('Date')); ?>: </strong><?php echo e($consultationOrderItem->consultation_date); ?> <br>
                                            <?php
                                                $booking = getBookingHistoryDetails($consultationOrderItem->consultation_slot_id);
                                            ?>
                                            <strong>Time: </strong><?php echo e(@$booking['time']); ?> <br>
                                        </td>
                                        <td><?php echo e(getInstructorName($consultationOrderItem->owner_user_id)); ?></td>
                                        <td>
                                            <?php if(get_currency_placement() == 'after'): ?>
                                                <?php echo e($consultationOrderItem->total_admin_commission); ?> <?php echo e(get_currency_symbol()); ?>

                                            <?php else: ?>
                                                <?php echo e(get_currency_symbol()); ?>  <?php echo e($consultationOrderItem->total_admin_commission); ?>

                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(get_currency_placement() == 'after'): ?>
                                                <?php echo e($consultationOrderItem->total_owner_balance); ?> <?php echo e(get_currency_symbol()); ?>

                                            <?php else: ?>
                                                <?php echo e(get_currency_symbol()); ?> <?php echo e($consultationOrderItem->total_owner_balance); ?>

                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($consultationOrderItem->total_enroll); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <div class="mt-3">
                                <?php echo e($consultationOrderItems->links()); ?>

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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/report/consultation-list.blade.php ENDPATH**/ ?>
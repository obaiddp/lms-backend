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
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Revenue Report')); ?></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-4">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/study.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-blue"><?php echo e($total_enrolment_in_bundle); ?></h2>
                            <h3><?php echo e(__('Total Enrolled in Bundle')); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/save-money.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-green">
                                <?php if(get_currency_placement() == 'after'): ?>
                                    <?php echo e($grand_admin_commission); ?> <?php echo e(get_currency_symbol()); ?>

                                <?php else: ?>
                                    <?php echo e(get_currency_symbol()); ?> <?php echo e($grand_admin_commission); ?>

                                <?php endif; ?>

                            </h2>
                            <h3><?php echo e(__('Total Admin Commission')); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/discount.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-purple">
                                <?php if(get_currency_placement() == 'after'): ?>
                                    <?php echo e($grand_instructor_commission); ?> <?php echo e(get_currency_symbol()); ?>

                                <?php else: ?>
                                    <?php echo e(get_currency_symbol()); ?> <?php echo e($grand_instructor_commission); ?>

                                <?php endif; ?>
                            </h2>
                            <h3><?php echo e(__('Total Instructor Commission')); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="customers__area bg-style mb-30">
                        <div class="item-title d-flex justify-content-between">
                            <h2><?php echo e(__('Revenue Report')); ?> (<?php echo e(__('Bundles')); ?>)</h2>
                            <div>
                                <a href="<?php echo e(route('course-report.revenue-report')); ?>" class="btn btn-success btn-sm">
                                    <?php echo e(__('Course Report')); ?> <span class="iconify" data-icon="akar-icons:arrow-right"></span>
                                </a>
                                <a href="<?php echo e(route('consultation-report.revenue-report')); ?>" class="btn btn-success btn-sm">
                                    <?php echo e(__('Consultation Report')); ?> <span class="iconify" data-icon="akar-icons:arrow-right"></span>
                                </a>
                            </div>
                        </div>
                        <div class="customers__table">
                            <table id="customers-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('Image')); ?></th>
                                    <th><?php echo e(__('Title')); ?></th>
                                    <th><?php echo e(__('Instructor')); ?></th>
                                    <th><?php echo e(__('Total Admin Commission')); ?></th>
                                    <th><?php echo e(__('Total Instructor Commission')); ?></th>
                                    <th><?php echo e(__('Total Enrolled')); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $bundleOrderItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $orderItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="removable-item">
                                        <?php
                                            $bundleDetail = getBundleDetails($orderItem->bundle_id);
                                        ?>
                                        <td>
                                            <a href="#"> <img src="<?php echo e(getImageFile(@$bundleDetail['image'])); ?>" width="80" alt="No Image"> </a>
                                        </td>
                                        <td><?php echo e(@$bundleDetail['name']); ?></td>
                                        <td><?php echo e(getInstructorName($orderItem->owner_user_id)); ?></td>
                                        <td>
                                            <?php if(get_currency_placement() == 'after'): ?>
                                                <?php echo e($orderItem->total_admin_commission); ?> <?php echo e(get_currency_symbol()); ?>

                                            <?php else: ?>
                                                <?php echo e(get_currency_symbol()); ?>  <?php echo e($orderItem->total_admin_commission); ?>

                                            <?php endif; ?>
                                           </td>
                                        <td>
                                            <?php if(get_currency_placement() == 'after'): ?>
                                                <?php echo e($orderItem->total_owner_balance); ?> <?php echo e(get_currency_symbol()); ?>

                                            <?php else: ?>
                                                <?php echo e(get_currency_symbol()); ?> <?php echo e($orderItem->total_owner_balance); ?>

                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($orderItem->total_enroll); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <div class="mt-3">
                                <?php echo e($bundleOrderItems->links()); ?>

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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/report/bundle-list.blade.php ENDPATH**/ ?>
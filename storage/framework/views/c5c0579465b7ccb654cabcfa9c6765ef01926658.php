<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Order Report')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Order Report')); ?></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/paying.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-red">
                                <?php if(get_currency_placement() == 'after'): ?>
                                    <?php echo e($grand_total - $cancel_consultation_money); ?> <?php echo e(get_currency_symbol()); ?>

                                <?php else: ?>
                                    <?php echo e(get_currency_symbol()); ?> <?php echo e($grand_total - $cancel_consultation_money); ?>

                                <?php endif; ?>
                            </h2>
                            <h3><?php echo e(__('Grand Total')); ?></h3>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/commission-1.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-purple">
                                <?php if(get_currency_placement() == 'after'): ?>
                                    <?php echo e($total_platform_charge); ?> <?php echo e(get_currency_symbol()); ?>

                                <?php else: ?>
                                    <?php echo e(get_currency_symbol()); ?> <?php echo e($total_platform_charge); ?>

                                <?php endif; ?>
                            </h2>
                            <h3><?php echo e(__('Total Platform Charge')); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/save-money.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-green">
                                <?php if(get_currency_placement() == 'after'): ?>
                                    <?php echo e($grand_admin_commission - $cancel_admin_commission_consultation_money); ?> <?php echo e(get_currency_symbol()); ?>

                                <?php else: ?>
                                    <?php echo e(get_currency_symbol()); ?> <?php echo e($grand_admin_commission - $cancel_admin_commission_consultation_money); ?>

                                <?php endif; ?>

                            </h2>
                            <h3><?php echo e(__('Total Admin Commission')); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/money.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-blue">
                                <?php if(get_currency_placement() == 'after'): ?>
                                    <?php echo e($total_revenue); ?> <?php echo e(get_currency_symbol()); ?>

                                <?php else: ?>
                                    <?php echo e(get_currency_symbol()); ?> <?php echo e($total_revenue); ?>

                                <?php endif; ?>
                            </h2>
                            <h3><?php echo e(__('Total Revenue')); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/discount.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-purple">
                                <?php if(get_currency_placement() == 'after'): ?>
                                    <?php echo e($grand_instructor_commission - $cancel_instructor_commission_consultation_money); ?> <?php echo e(get_currency_symbol()); ?>

                                <?php else: ?>
                                    <?php echo e(get_currency_symbol()); ?> <?php echo e($grand_instructor_commission - $cancel_instructor_commission_consultation_money); ?>

                                <?php endif; ?>
                            </h2>
                            <h3><?php echo e(__('Total Instructor Commission')); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/study.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-blue"><?php echo e($total_enrolment_in_course); ?></h2>
                            <h3><?php echo e(__('Total Enrolled in Courses')); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6">
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
                <div class="col-lg-2 col-md-6 col-sm-6">
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
                <div class="col-lg-2 col-md-6 col-sm-6">
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
                <div class="col-lg-2 col-md-6 col-sm-6">
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
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/discount.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-purple"> <?php echo e(@$total_bundle_Course_enrolled); ?></h2>
                            <h3><?php echo e(__('Total Bundle Course enrolled')); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/discount.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-purple">
                                <?php if(get_currency_placement() == 'after'): ?>
                                    <?php echo e($grand_money_from_bundle); ?> <?php echo e(get_currency_symbol()); ?>

                                <?php else: ?>
                                    <?php echo e(get_currency_symbol()); ?> <?php echo e($grand_money_from_bundle); ?>

                                <?php endif; ?>
                            </h2>
                            <h3><?php echo e(__('Grand Total Bundle Course')); ?></h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="customers__area bg-style mb-30">
                        <div class="item-title d-flex justify-content-between">
                            <h2><?php echo e(__('Order Report')); ?></h2>
                        </div>
                        <div class="customers__table">
                            <table id="customers-table" class="row-border data-table-filter table-style admin-order-report-table">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('Student Name')); ?></th>
                                    <th><?php echo e(__('Order Number')); ?></th>
                                    <th><?php echo e(__('Sub Total')); ?></th>
                                    <th><?php echo e(__('Discount')); ?></th>
                                    <th><?php echo e(__('Platform Charge')); ?></th>
                                    <th><?php echo e(__('Grand Total')); ?></th>
                                    <th class="admin-order-payment-method"><?php echo e(__('Payment Method & Details')); ?></th>
                                    <th><?php echo e(__('Total Admin Commission')); ?></th>
                                    <th><?php echo e(__('Total Instructor Commission')); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="removable-item">
                                        <td><?php echo e(@$order->user->student->name); ?></td>
                                        <td><?php echo e(@$order->order_number); ?></td>
                                        <td> <?php if(get_currency_placement() == 'after'): ?>
                                                <?php echo e(@$order->sub_total); ?> <?php echo e(get_currency_symbol()); ?>

                                            <?php else: ?>
                                                <?php echo e(get_currency_symbol()); ?> <?php echo e(@$order->sub_total); ?>

                                            <?php endif; ?></td>
                                        <td> <?php if(get_currency_placement() == 'after'): ?>
                                                <?php echo e(@$order->discount); ?> <?php echo e(get_currency_symbol()); ?>

                                            <?php else: ?>
                                                <?php echo e(get_currency_symbol()); ?> <?php echo e(@$order->discount); ?>

                                            <?php endif; ?></td>
                                        <td> <?php if(get_currency_placement() == 'after'): ?>
                                                <?php echo e(@$order->platform_charge); ?> <?php echo e(get_currency_symbol()); ?>

                                            <?php else: ?>
                                                <?php echo e(get_currency_symbol()); ?> <?php echo e(@$order->platform_charge); ?>

                                            <?php endif; ?></td>
                                        <td> <?php if(get_currency_placement() == 'after'): ?>
                                                <?php echo e(@$order->grand_total); ?> <?php echo e(get_currency_symbol()); ?>

                                            <?php else: ?>
                                                <?php echo e(get_currency_symbol()); ?> <?php echo e(@$order->grand_total); ?>

                                            <?php endif; ?></td>
                                        <td class="admin-order-payment-method">
                                            <?php echo e(ucfirst(@$order->payment_method)); ?>

                                            <?php if(@$order->payment_method): ?>
                                                <p class=" mb-0"><span class="fw-bold"><?php echo e(__('Payment Currency')); ?></span>: <?php echo e($order->payment_currency); ?></p>
                                                <p class="font-bold mb-0"><span class="fw-bold"><?php echo e(__('Conversion Rate')); ?></span> : <?php echo e(number_format($order->conversion_rate, 2)); ?></p>
                                                <p class="font-bold mb-0"><span class="fw-bold"><?php echo e(__('Payment')); ?></span>: <?php echo e(number_format($order->grand_total_with_conversation_rate, 2)); ?>

                                                </p>
                                                <?php if(@$order->payment_method == 'bank'): ?>
                                                    <p class="font-bold mb-0"><span class="fw-bold"><?php echo e(__('Deposit Bank Name')); ?></span>:
                                                        <?php echo e(@$order->bank->name); ?>

                                                    </p>
                                                    <p class="font-bold mb-0"><span class="fw-bold"><?php echo e(__('Deposit By')); ?></span>:
                                                        <?php echo e($order->deposit_by); ?>

                                                    </p>
                                                    <p class="font-bold mb-0"><span class="fw-bold"><?php echo e(__('Deposit Slip')); ?></span>:
                                                        <a class="color-blue" href="<?php echo e(getVideoFile($order->deposit_slip)); ?>" download><?php echo e(__('Download')); ?></a>
                                                    </p>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                        </td>
                                        <td>
                                            <?php if(get_currency_placement() == 'after'): ?>
                                                <?php echo e($order->total_admin_commission); ?> <?php echo e(get_currency_symbol()); ?>

                                            <?php else: ?>
                                                <?php echo e(get_currency_symbol()); ?> <?php echo e($order->total_admin_commission); ?>

                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(get_currency_placement() == 'after'): ?>
                                                <?php echo e($order->total_owner_balance); ?> <?php echo e(get_currency_symbol()); ?>

                                            <?php else: ?>
                                                <?php echo e(get_currency_symbol()); ?> <?php echo e($order->total_owner_balance); ?>

                                            <?php endif; ?>

                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <div class="mt-3">
                                <?php echo e($orders->links()); ?>

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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/report/order-report-list.blade.php ENDPATH**/ ?>
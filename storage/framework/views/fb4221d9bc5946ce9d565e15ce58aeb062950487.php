<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Order Report Pending')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a
                                            href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Order Report Pending')); ?></li>
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
                            <h2><?php echo e(__('Order Report Pending')); ?></h2>
                        </div>
                        <div class="customers__table">
                            <table id="customers-table"
                                   class="row-border data-table-filter table-style admin-order-report-table">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('Student Name')); ?></th>
                                    <th><?php echo e(__('Order Number')); ?></th>
                                    <th><?php echo e(__('Sub total')); ?></th>
                                    <th><?php echo e(__('Discount')); ?></th>
                                    <th><?php echo e(__('Platform Charge')); ?></th>
                                    <th><?php echo e(__('Grand Total')); ?></th>
                                    <th class="admin-order-payment-method"><?php echo e(__('Payment Method & Details')); ?></th>
                                    <th><?php echo e(__('Total Admin Commission')); ?></th>
                                    <th><?php echo e(__('Total Instructor Commission')); ?></th>
                                    <th><?php echo e(__('Action')); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="removable-item">
                                        <td><?php echo e(@$order->user->student->name); ?></td>
                                        <td><?php echo e(@$order->order_number); ?></td>
                                        <td>
                                            <?php if(get_currency_placement() == 'after'): ?>
                                                <?php echo e(@$order->sub_total); ?> <?php echo e(get_currency_symbol()); ?>

                                            <?php else: ?>
                                                <?php echo e(get_currency_symbol()); ?> <?php echo e(@$order->sub_total); ?>

                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(get_currency_placement() == 'after'): ?>
                                                <?php echo e(@$order->discount); ?> <?php echo e(get_currency_symbol()); ?>

                                            <?php else: ?>
                                                <?php echo e(get_currency_symbol()); ?> <?php echo e(@$order->discount); ?>

                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(get_currency_placement() == 'after'): ?>
                                                <?php echo e(@$order->platform_charge); ?> <?php echo e(get_currency_symbol()); ?>

                                            <?php else: ?>
                                                <?php echo e(get_currency_symbol()); ?> <?php echo e(@$order->platform_charge); ?>

                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(get_currency_placement() == 'after'): ?>
                                                <?php echo e(@$order->grand_total); ?> <?php echo e(get_currency_symbol()); ?>

                                            <?php else: ?>
                                                <?php echo e(get_currency_symbol()); ?> <?php echo e(@$order->grand_total); ?>

                                            <?php endif; ?>
                                        </td>
                                        <td class="admin-order-payment-method">
                                            <?php echo e(ucfirst(@$order->payment_method)); ?>

                                            <?php if(@$order->payment_method): ?>
                                                <p class=" mb-0"><span class="fw-bold"><?php echo e(__('Payment Currency')); ?></span>:
                                                    <?php echo e($order->payment_currency); ?></p>
                                                <p class="font-bold mb-0"><span class="fw-bold"><?php echo e(__('Conversion Rate')); ?></span> :
                                                    <?php echo e(number_format($order->conversion_rate, 2)); ?></p>
                                                <p class="font-bold mb-0"><span class="fw-bold"><?php echo e(__('Payment')); ?></span>:
                                                    <?php echo e(number_format($order->grand_total_with_conversation_rate, 2)); ?>

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
                                        <td>
                                            <div class="action__buttons">
                                                <a href="#"
                                                   data-url="<?php echo e(route('report.order-paid', [$order->uuid, 'paid'])); ?>"
                                                   class=" btn-success approve-btn p-2 btn-action mr-1 paid-order" data-toggle="tooltip"
                                                   title="Paid"><?php echo e(__('Paid')); ?>

                                                </a>
                                                <a href="#"
                                                   data-url="<?php echo e(route('report.order-paid', [$order->uuid, 'cancelled'])); ?>"
                                                   class=" btn-danger p-2 hold-btn btn-action mr-1 cancelled-order" data-toggle="tooltip"
                                                   title="Cancelled"><?php echo e(__('Cancelled')); ?>

                                                </a>
                                            </div>
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/report/order-report-pending.blade.php ENDPATH**/ ?>
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
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Cancel Consultation List')); ?></li>
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
                            <h2><?php echo e(__('Cancel List')); ?></h2>
                            <div>
                                <a href="<?php echo e(route('consultation-report.revenue-report')); ?>" class="btn btn-success btn-sm">
                                    <?php echo e(__('Consultation Revenue Report')); ?> <span class="iconify" data-icon="akar-icons:arrow-right"></span>
                                </a>
                            </div>
                        </div>
                        <div class="mb-5">
                            <caption><b>Note:</b> If any instructor cancel his/her booking consultation. Admin need to back money manually. After manually back money, admin can
                                send money status change <b>No</b> to <b>Yes</b></caption>
                        </div>
                        <div class="customers__table">

                            <table id="customers-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('SL')); ?>.</th>
                                    <th><?php echo e(__('Student Details')); ?></th>
                                    <th><?php echo e(__('Instructor')); ?></th>
                                    <th><?php echo e(__('Slot Details')); ?></th>
                                    <th><?php echo e(__('Admin Commission')); ?></th>
                                    <th><?php echo e(__('Instructor Commission')); ?></th>
                                    <th><?php echo e(__('Total Back Money')); ?></th>
                                    <th><?php echo e(__('Status')); ?></th>
                                    <th><?php echo e(__('Send Back Money')); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $bookingHistories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bookingHistory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="removable-item">
                                        <td><?php echo e($bookingHistory->id); ?></td>

                                        <td>
                                            <p><h6 class="font-15 d-inline"><?php echo e(__('Name')); ?></h6>: <?php echo e(@$bookingHistory->user->student->name); ?></p>
                                            <p><h6 class="font-15 d-inline"><?php echo e(__('Email')); ?></h6>: <?php echo e(@$bookingHistory->user->email); ?></p>
                                            <p><h6 class="font-15 d-inline"><?php echo e(__('Phone Number')); ?></h6>: <?php echo e(@$bookingHistory->user->student->phone_number); ?></p>
                                        </td>
                                        <td>
                                            <p><h6 class="font-15 d-inline"><?php echo e(__('Name')); ?></h6>: <?php echo e(@$bookingHistory->instructorUser->instructor->name); ?></p>
                                            <p><h6 class="font-15 d-inline"><?php echo e(__('Email')); ?></h6>: <?php echo e(@$bookingHistory->instructorUser->email); ?></p>
                                            <p><h6 class="font-15 d-inline"><?php echo e(__('Phone Number')); ?></h6>: <?php echo e(@$bookingHistory->instructorUser->instructor->phone_number); ?></p>
                                        </td>
                                        <td>
                                            <strong><?php echo e(__('Date')); ?>: </strong><?php echo e($bookingHistory->date); ?> <br>
                                            <strong><?php echo e(__('Time')); ?>: </strong><?php echo e(@$bookingHistory->time); ?> <br>
                                        </td>
                                        <td>
                                            <?php if($bookingHistory->send_back_money_status == 1): ?>
                                                <?php echo e($bookingHistory->back_admin_commission); ?>

                                            <?php else: ?>
                                                <?php if(get_currency_placement() == 'after'): ?>
                                                    <?php echo e(@$bookingHistory->order_item->admin_commission); ?> <?php echo e(get_currency_symbol()); ?>

                                                <?php else: ?>
                                                    <?php echo e(get_currency_symbol()); ?>  <?php echo e(@$bookingHistory->order_item->admin_commission); ?>

                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($bookingHistory->send_back_money_status == 1): ?>
                                                <?php echo e($bookingHistory->back_owner_balance); ?>

                                            <?php else: ?>
                                                <?php if(get_currency_placement() == 'after'): ?>
                                                    <?php echo e(@$bookingHistory->order_item->owner_balance); ?> <?php echo e(get_currency_symbol()); ?>

                                                <?php else: ?>
                                                    <?php echo e(get_currency_symbol()); ?> <?php echo e(@$bookingHistory->order_item->owner_balance); ?>

                                                <?php endif; ?>
                                            <?php endif; ?>


                                        </td>
                                        <td>
                                            <?php if($bookingHistory->send_back_money_status == 1): ?>
                                                <?php echo e(($bookingHistory->back_admin_commission ?? 0) + ($bookingHistory->back_owner_balance ?? 0)); ?>

                                            <?php else: ?>
                                            <?php $total = @$bookingHistory->order_item->admin_commission + @$bookingHistory->order_item->owner_balance ?>
                                                <?php if(get_currency_placement() == 'after'): ?>
                                                    <b><?php echo e($total); ?> <?php echo e(get_currency_symbol()); ?></b>
                                                <?php else: ?>
                                                    <b><?php echo e(get_currency_symbol()); ?> <?php echo e($total); ?></b>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($bookingHistory->status == BOOKING_HISTORY_STATUS_APPROVE): ?>
                                                <span class="status active"><?php echo e(__('Approved')); ?></span>
                                            <?php elseif($bookingHistory->status == BOOKING_HISTORY_STATUS_CANCELLED): ?>
                                                <span class="status blocked"><?php echo e(__('Cancelled')); ?></span>
                                            <?php elseif($bookingHistory->status == BOOKING_HISTORY_STATUS_COMPLETED): ?>
                                                <span class="status active"><?php echo e(__('Completed')); ?></span>
                                            <?php else: ?>
                                                <?php echo e(__('Pending')); ?>

                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($bookingHistory->send_back_money_status == SEND_BACK_MONEY_STATUS_YES): ?>
                                                <span class="status active"><?php echo e(__('Yes')); ?></span>
                                            <?php else: ?>
                                                <span id="hidden_id" style="display: none"><?php echo e($bookingHistory->id); ?></span>
                                                <select name="status" class="status label-inline font-weight-bolder mb-1 badge badge-info">
                                                    <option value="1" <?php if($bookingHistory->send_back_money_status == SEND_BACK_MONEY_STATUS_YES): ?> selected <?php endif; ?>><?php echo e(__('Yes')); ?></option>
                                                    <option value="0" <?php if($bookingHistory->send_back_money_status != SEND_BACK_MONEY_STATUS_YES): ?> selected <?php endif; ?>><?php echo e(__('No')); ?></option>
                                                </select>
                                            <?php endif; ?>

                                        </td>

                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <div class="mt-3">
                                <?php echo e($bookingHistories->links()); ?>

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
    <script>
        'use strict'
        $(".status").change(function () {
            var id = $(this).closest('tr').find('#hidden_id').html();
            var status_value = $(this).closest('tr').find('.status option:selected').val();
            Swal.fire({
                title: "<?php echo e(__('Are you sure to back money?')); ?>",
                text: "<?php echo e(__('You won`t be able to revert this!')); ?>",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "<?php echo e(__('Yes, Change it!')); ?>",
                cancelButtonText: "<?php echo e(__('No, cancel!')); ?>",
                reverseButtons: true
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo e(route('report.changeConsultationStatus')); ?>",
                        data: {"status": status_value, "id": id, "_token": "<?php echo e(csrf_token()); ?>",},
                        datatype: "json",
                        success: function (response) {
                            if(response.status == 403) {
                                toastr.options.positionClass = 'toast-bottom-right';
                                toastr.error('', "<?php echo e(__('Already money send')); ?>");
                            }else if(response.status == 200) {
                                toastr.options.positionClass = 'toast-bottom-right';
                                toastr.success('', "<?php echo e(__('Status updated successfully')); ?>");
                            }

                            location.reload()
                        },
                        error: function () {
                            alert("Error!");
                        },
                    });
                } else if (result.dismiss === "cancel") {
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/report/cancel-consultation-list.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
<!-- Page content area start -->
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb__content">
                    <div class="breadcrumb__content__left">
                        <div class="breadcrumb__title">
                            <h2><?php echo e(__('Wallet Recharge Pending')); ?></h2>
                        </div>
                    </div>
                    <div class="breadcrumb__content__right">
                        <nav aria-label="breadcrumb">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a
                                        href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Wallet Recharge Pending List')); ?></li>
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
                        <h2><?php echo e(__('Wallet Recharge Pending List')); ?></h2>
                    </div>
                    <div class="customers__table">
                        <table class="table-style">
                            <thead>
                                <tr>
                                    <th><?php echo e(__('Date')); ?></th>
                                    <th><?php echo e(__('Transaction ID')); ?></th>
                                    <th><?php echo e(__('Payment method')); ?></th>
                                    <th><?php echo e(__('Amount')); ?></th>
                                    <th><?php echo e(__('')); ?></th>
                                    <th><?php echo e(__('Status')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $walletRecharges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recharge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <span class="data-text"><?php echo e($recharge->created_at->format('Y-m-d H:i:s')); ?></span>
                                    </td>
                                    <td>
                                        <span class="data-text"><?php echo e($recharge->payment->payment_id); ?></span>
                                    </td>
                                    <td>
                                        <span class="data-text"><?php echo e($recharge->payment_method); ?></span></td>
                                    <td>
                                        <span class="data-text"><?php echo e($recharge->payment->grand_total); ?></span>
                                    </td>
                                    <td>
                                        <?php if($recharge->payment_method == 'bank'): ?>
                                            <p class="font-bold mb-0"><span class="fw-bold"><?php echo e(__('Deposit Bank Name')); ?></span>:
                                                <?php echo e($recharge->payment->bank->name); ?>

                                            </p>
                                            <p class="font-bold mb-0"><span class="fw-bold"><?php echo e(__('Deposit By')); ?></span>:
                                                <?php echo e($recharge->payment->deposit_by); ?>

                                            </p>
                                            <p class="font-bold mb-0"><span class="fw-bold"><?php echo e(__('Deposit Slip')); ?></span>:
                                                <a class="color-blue" href="<?php echo e(getVideoFile($recharge->payment->deposit_slip)); ?>" download><?php echo e(__('Download')); ?></a>
                                            </p>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span id="hidden_id" style="display: none"><?php echo e($recharge->id); ?></span>
                                        <select name="status" class="status label-inline font-weight-bolder mb-1 badge badge-info">
                                            <option value="<?php echo e(STATUS_ACCEPTED); ?>" <?php if($recharge->status == STATUS_ACCEPTED): ?> selected <?php endif; ?>><?php echo e(__("Accepted")); ?></option>
                                            <option value="<?php echo e(STATUS_PENDING); ?>" <?php if($recharge->status == STATUS_PENDING): ?> selected <?php endif; ?>><?php echo e(__("Pending")); ?></option>
                                            <option value="<?php echo e(STATUS_REJECTED); ?>" <?php if($recharge->status == STATUS_REJECTED): ?> selected <?php endif; ?>><?php echo e(__("Rejected")); ?></option>
                                        </select>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                        <div class="mt-3">
                            <?php echo e($walletRecharges->links()); ?>

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
                title: "<?php echo e(__('Are you sure to change status?')); ?>",
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
                        url: "<?php echo e(route('admin.wallet_recharge.changeStatus')); ?>",
                        data: {"status": status_value, "id": id, "_token": "<?php echo e(csrf_token()); ?>",},
                        datatype: "json",
                        success: function (data) {
                            toastr.options.positionClass = 'toast-bottom-right';
                            if(data.data == 'success'){
                                toastr.success('', "<?php echo e(__('Status has been updated')); ?>");
                                location.reload();
                            }
                            else{
                                toastr.error('', "<?php echo e(__('Status has cannot be changed')); ?>");
                            }
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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/wallet_recharge/pending_list.blade.php ENDPATH**/ ?>
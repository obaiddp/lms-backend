<?php $__env->startSection('content'); ?>
<!-- Page content area start -->
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb__content">
                    <div class="breadcrumb__content__left">
                        <div class="breadcrumb__title">
                            <h2><?php echo e(__('SaaS Purchase Pending')); ?></h2>
                        </div>
                    </div>
                    <div class="breadcrumb__content__right">
                        <nav aria-label="breadcrumb">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a
                                        href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('SaaS Purchase Pending List')); ?></li>
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
                        <h2><?php echo e(__('All SaaS Purchase Pending List')); ?></h2>
                    </div>
                    <div class="customers__table">
                        <table class="table-style">
                            <thead>
                                <tr>
                                    <th><?php echo e(__('Image')); ?></th>
                                    <th><?php echo e(__('Name')); ?></th>
                                    <th><?php echo e(__('Package Name')); ?></th>
                                    <th><?php echo e(__('Purchase date')); ?></th>
                                    <th><?php echo e(__('Expired at')); ?></th>
                                    <th><?php echo e(__('')); ?></th>
                                    <th><?php echo e(__('Status')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $userSaases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userSaas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <a  target="_blank" href="<?php echo e(route('student.view', [@$userSaas->user->student->uuid])); ?>"><img
                                                src="<?php echo e(asset(@$userSaas->user ? @$userSaas->user->image_path  : '')); ?>"
                                                alt="course" class="img-fluid" width="80"></a>
                                    </td>
                                    <td>
                                        <span  target="_blank" class="data-text"><a
                                                href="<?php echo e(route('student.view', [@$userSaas->user->student->uuid])); ?>"><?php echo e(@$userSaas->user->student->name); ?></a></span>
                                    </td>
                                    <td>
                                        <span class="data-text"><a target="_blank" href="<?php echo e(route('admin.subscriptions.show', [$userSaas->package_uuid])); ?>"><?php echo e($userSaas->title); ?></a></span>
                                    </td>
                                    <td>
                                        <span class="data-text"><?php echo e($userSaas->enroll_date); ?></span>
                                    </td>
                                    <td>
                                        <span class="data-text"><?php echo e($userSaas->expired_date); ?></span>
                                    </td>
                                    <td>
                                        <?php if($userSaas->payment->payment_method == 'bank'): ?>
                                            <p class="font-bold mb-0"><span class="fw-bold"><?php echo e(__('Deposit Bank Name')); ?></span>:
                                                <?php echo e($userSaas->payment->bank->name); ?>

                                            </p>
                                            <p class="font-bold mb-0"><span class="fw-bold"><?php echo e(__('Deposit By')); ?></span>:
                                                <?php echo e($userSaas->payment->deposit_by); ?>

                                            </p>
                                            <p class="font-bold mb-0"><span class="fw-bold"><?php echo e(__('Deposit Slip')); ?></span>:
                                                <a class="color-blue" href="<?php echo e(getVideoFile($userSaas->payment->deposit_slip)); ?>" download><?php echo e(__('Download')); ?></a>
                                            </p>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span id="hidden_id" style="display: none"><?php echo e($userSaas->id); ?></span>
                                        <select name="status" class="status label-inline font-weight-bolder mb-1 badge badge-info">
                                            <option value="<?php echo e(PACKAGE_STATUS_PENDING); ?>" <?php if($userSaas->status == PACKAGE_STATUS_PENDING): ?> selected <?php endif; ?>><?php echo e(__("Unpaid")); ?></option>
                                            <option value="<?php echo e(PACKAGE_STATUS_ACTIVE); ?>" <?php if($userSaas->status == PACKAGE_STATUS_ACTIVE): ?> selected <?php endif; ?>><?php echo e(__("Paid")); ?></option>
                                        </select>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                        <div class="mt-3">
                            <?php echo e($userSaases->links()); ?>

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
                        url: "<?php echo e(route('admin.saas.purchase.changeStatus')); ?>",
                        data: {"status": status_value, "id": id, "_token": "<?php echo e(csrf_token()); ?>",},
                        datatype: "json",
                        success: function (data) {
                            toastr.options.positionClass = 'toast-bottom-right';
                            toastr.success('', "<?php echo e(__('Purchase status has been updated')); ?>");
                            location.reload();
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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/saas/pending_purchase_list.blade.php ENDPATH**/ ?>
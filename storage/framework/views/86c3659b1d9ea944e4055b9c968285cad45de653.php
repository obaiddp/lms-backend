<?php $__env->startSection('content'); ?>
<!-- Page content area start -->
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb__content">
                    <div class="breadcrumb__content__left">
                        <div class="breadcrumb__title">
                            <h2><?php echo e(__('Subscription sale')); ?></h2>
                        </div>
                    </div>
                    <div class="breadcrumb__content__right">
                        <nav aria-label="breadcrumb">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a
                                        href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('All Subscription sale ')); ?></li>
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
                        <h2><?php echo e(__('All Subscription sale')); ?></h2>
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
                                    <th><?php echo e(__('Status')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $userSubscriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userSubscription): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <a  target="_blank" href="<?php echo e(route('student.view', [@$userSubscription->user->student->uuid])); ?>"><img
                                                src="<?php echo e(asset(@$userSubscription->user ? @$userSubscription->user->image_path  : '')); ?>"
                                                alt="course" class="img-fluid" width="80"></a>
                                    </td>
                                    <td>
                                        <span  target="_blank" class="data-text"><a
                                                href="<?php echo e(route('student.view', [@$userSubscription->user->student->uuid])); ?>"><?php echo e(@$userSubscription->user->student->name); ?></a></span>
                                    </td>
                                    <td>
                                        <span class="data-text"><a target="_blank" href="<?php echo e(route('admin.subscriptions.show', [$userSubscription->package_uuid])); ?>"><?php echo e($userSubscription->title); ?></a></span>
                                    </td>
                                    <td>
                                        <span class="data-text"><?php echo e($userSubscription->enroll_date); ?></span>
                                    </td>
                                    <td>
                                        <span class="data-text"><?php echo e($userSubscription->expired_date); ?></span>
                                    </td>
                                    <td>
                                        <span class="data-text"><?php echo e(getPackageStatus($userSubscription->status)); ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                        <div class="mt-3">
                            <?php echo e($userSubscriptions->links()); ?>

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
                        url: "<?php echo e(route('admin.subscriptions.changeStatus')); ?>",
                        data: {"status": status_value, "id": id, "_token": "<?php echo e(csrf_token()); ?>",},
                        datatype: "json",
                        success: function (data) {
                            toastr.options.positionClass = 'toast-bottom-right';
                            toastr.success('', "<?php echo e(__('Subscription status has been updated')); ?>");
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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/subscriptions/purchase_list.blade.php ENDPATH**/ ?>
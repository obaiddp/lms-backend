<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Promotion')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item"><?php echo e(__('Promotion')); ?></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Promotion List')); ?></li>
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
                            <h2><?php echo e(__('Promotion List')); ?></h2>
                            <a href="<?php echo e(route('promotion.create')); ?>" class="btn btn-success btn-sm">
                                <i class="fa fa-plus"></i> <?php echo e(__('Add Promotion')); ?>

                            </a>
                        </div>
                        <div class="customers__table">
                            <table id="customers-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('SL')); ?>.</th>
                                    <th><?php echo e(__('Name')); ?></th>
                                    <th><?php echo e(__('Duration')); ?></th>
                                    <th><?php echo e(__('Percentage')); ?> (%)</th>
                                    <th><?php echo e(__('Total Course')); ?></th>
                                    <th><?php echo e(__('Status')); ?></th>
                                    <th><?php echo e(__('Created By')); ?></th>
                                    <th><?php echo e(__('Action')); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $promotions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $promotion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="removable-item">
                                        <td><?php echo e(@$loop->iteration); ?></td>
                                        <td><?php echo e($promotion->name); ?></td>
                                        <td>
                                            <?php echo e(date('d M Y, H:i:s', strtotime(@$promotion->start_date))); ?> <span class="text-black">to</span> <?php echo e(date('d M Y, H:i:s', strtotime(@$promotion->end_date))); ?>

                                        </td>
                                        <td><?php echo e($promotion->percentage); ?></td>
                                        <td><?php echo e(@$promotion->promotionCourses->count()); ?></td>
                                        <td>
                                            <span id="hidden_id" style="display: none"><?php echo e($promotion->id); ?></span>
                                            <select name="status" class="status label-inline font-weight-bolder mb-1 badge badge-info">
                                                <option value="1" <?php if($promotion->status == 1): ?> selected <?php endif; ?>><?php echo e(__('Active')); ?></option>
                                                <option value="0" <?php if($promotion->status != 1): ?> selected <?php endif; ?>><?php echo e(__('Deactivated')); ?></option>
                                            </select>
                                        </td>
                                        <td><?php echo e($promotion->user->name); ?></td>
                                        <td>
                                            <div class="action__buttons">
                                                <a href="<?php echo e(route('promotion.editPromotionCourse', $promotion->uuid)); ?>" class="btn-action mr-1" title="<?php echo e(__('View promotion details')); ?>">
                                                    <button class="btn btn-primary btn-sm">+/- <?php echo e(__('Course')); ?></button>
                                                </a>
                                                <a href="<?php echo e(route('promotion.edit', $promotion->uuid)); ?>" class="btn-action mr-1 edit" data-toggle="tooltip" title="<?php echo e(__('Edit')); ?>">
                                                    <img src="<?php echo e(asset('admin/images/icons/edit-2.svg')); ?>" alt="edit">
                                                </a>
                                                <button class="btn-action ms-2 deleteItem" data-formid="delete_row_form_<?php echo e($promotion->uuid); ?>">
                                                    <img src="<?php echo e(asset('admin/images/icons/trash-2.svg')); ?>" alt="trash">
                                                </button>

                                                <form action="<?php echo e(route('promotion.delete', [$promotion->uuid])); ?>" method="post" id="delete_row_form_<?php echo e($promotion->uuid); ?>">
                                                    <?php echo e(method_field('DELETE')); ?>

                                                    <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <div class="mt-3">
                                <?php echo e($promotions->links()); ?>

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
            console.log(id, status_value)
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
                        url: "<?php echo e(route('promotion.changePromotionStatus')); ?>",
                        data: {"status": status_value, "id": id, "_token": "<?php echo e(csrf_token()); ?>",},
                        datatype: "json",
                        success: function (data) {
                            toastr.success('', '<?php echo e(__('Promotion status has been updated')); ?>');
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/promotion/index.blade.php ENDPATH**/ ?>
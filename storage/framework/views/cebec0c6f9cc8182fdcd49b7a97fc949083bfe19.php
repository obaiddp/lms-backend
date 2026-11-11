<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('SaaS Packages')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('All SaaS Package')); ?></li>
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
                            <h2><?php echo e(__('All SaaS Package')); ?></h2>
                        </div>
                        <div class="customers__table">
                            <table id="package-list-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('Icon')); ?></th>
                                    <th><?php echo e(__('Type')); ?></th>
                                    <th><?php echo e(__('Title')); ?></th>
                                    <th><?php echo e(__('Price')); ?></th>
                                    <th><?php echo e(__('Admin Comission')); ?>%</th>
                                    <th><?php echo e(__('Student')); ?></th>
                                    <th><?php echo e(__('Instructor')); ?></th>
                                    <th><?php echo e(__('Course')); ?></th>
                                    <th><?php echo e(__('Bundle Course')); ?></th>
                                    <th><?php echo e(__('Subscription Course')); ?></th>
                                    <th><?php echo e(__('Consultancy')); ?></th>
                                    <th><?php echo e(__('Status')); ?></th>
                                    <th><?php echo e(__('Action')); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $saases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $saas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="removable-item">
                                        <td>
                                             <img src="<?php echo e(getImageFile($saas->icon)); ?>" width="80">
                                        </td>
                                        <td><?php echo e(($saas->package_type == PACKAGE_TYPE_SAAS_INSTRUCTOR) ? __('Instructor') : __('Organization')); ?></td>
                                        <td><?php echo e($saas->title); ?></td>
                                        <td>
                                            <?php echo e(__('Monthly')); ?>: <?php echo e($saas->monthly_price); ?><br>
                                            <?php echo e(__('Yearly')); ?>: <?php echo e($saas->yearly_price); ?><br>
                                        </td>
                                        <td><?php echo e($saas->admin_commission); ?></td>
                                        <td><?php echo e(($saas->package_type == PACKAGE_TYPE_SAAS_ORGANIZATION) ? $saas->student : ''); ?></td>
                                        <td><?php echo e(($saas->package_type == PACKAGE_TYPE_SAAS_ORGANIZATION) ? $saas->instructor : ''); ?></td>
                                        <td><?php echo e($saas->course); ?></td>
                                        <td><?php echo e($saas->bundle_course); ?></td>
                                        <td><?php echo e($saas->subscription_course); ?></td>
                                        <td><?php echo e($saas->consultancy); ?></td>
                                        <td>
                                            <span id="hidden_id" style="display: none"><?php echo e($saas->id); ?></span>
                                            <select name="status" class="status label-inline font-weight-bolder mb-1 badge badge-info">
                                                <option value="<?php echo e(PACKAGE_STATUS_DISABLED); ?>" <?php if($saas->status == PACKAGE_STATUS_DISABLED): ?> selected <?php endif; ?>><?php echo e(__('Disable')); ?></option>
                                                <option value="<?php echo e(PACKAGE_STATUS_ACTIVE); ?>" <?php if($saas->status == PACKAGE_STATUS_ACTIVE): ?> selected <?php endif; ?>><?php echo e(__('Enable')); ?></option>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="action__buttons">
                                                <a href="<?php echo e(route('admin.saas.show', [$saas->uuid])); ?>" class="btn-action mr-30" title="View Details">
                                                    <img src="<?php echo e(asset('admin/images/icons/eye-2.svg')); ?>" alt="eye">
                                                </a>
                                                <a href="<?php echo e(route('admin.saas.edit', [$saas->uuid])); ?>" class="btn-action mr-30" title="Edit Details">
                                                    <img src="<?php echo e(asset('admin/images/icons/edit-2.svg')); ?>" alt="edit">
                                                </a>
                                                <button class="ms-3">
                                                    <span data-formid="delete_row_form_<?php echo e($saas->id); ?>" class="deleteItem">
                                                        <img src="<?php echo e(asset('admin/images/icons/trash-2.svg')); ?>" alt="trash">
                                                    </span>
                                                </button>

                                                <form action="<?php echo e(route('admin.saas.destroy', [$saas->uuid])); ?>" method="post" id="delete_row_form_<?php echo e($saas->id); ?>">
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
                                <?php echo e($saases->links()); ?>

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
                        url: "<?php echo e(route('admin.saas.changeStatus')); ?>",
                        data: {"status": status_value, "id": id, "_token": "<?php echo e(csrf_token()); ?>",},
                        datatype: "json",
                        success: function (data) {
                            toastr.options.positionClass = 'toast-bottom-right';
                            toastr.success('', "<?php echo e(__('SaaS status has been updated')); ?>");
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/saas/index.blade.php ENDPATH**/ ?>
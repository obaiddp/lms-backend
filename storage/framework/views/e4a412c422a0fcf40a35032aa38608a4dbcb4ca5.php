<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Instructors')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Blocked Instructors')); ?></li>
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
                            <h2><?php echo e(__('Blocked Instructors')); ?></h2>
                        </div>
                        <div class="customers__table">
                            <table id="customers-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('Image')); ?></th>
                                    <th><?php echo e(__('Name')); ?></th>
                                    <th><?php echo e(__('Phone Number')); ?></th>
                                    <th><?php echo e(__('Country')); ?></th>
                                    <th><?php echo e(__('State')); ?></th>
                                    <th><?php echo e(__('Status')); ?></th>
                                    <th><?php echo e(__('Action')); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="removable-item">
                                        <td>
                                            <a href="<?php echo e(route('instructor.view', [$instructor->uuid])); ?>"> <img src="<?php echo e(getImageFile($instructor->user ? $instructor->user->image_path : '')); ?>" width="80"> </a>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('instructor.view', [$instructor->uuid])); ?>"> 
                                            <?php echo e($instructor->name); ?>

                                        </a>
                                        </td>

                                        <td>
                                            <?php echo e($instructor->phone_number); ?>

                                        </td>
                                        <td>
                                            <?php echo e($instructor->country ? $instructor->country->country_name : ''); ?>

                                        </td>
                                        <td>
                                            <?php echo e($instructor->state ? $instructor->state->name : ''); ?>

                                        </td>
                                        <td>
                                            <span id="hidden_id" style="display: none"><?php echo e($instructor->id); ?></span>
                                            <select name="status" class="status label-inline font-weight-bolder mb-1 badge badge-info">
                                                <option value="1" <?php if($instructor->status == 1): ?> selected <?php endif; ?>><?php echo e(__('Approved')); ?></option>
                                                <option value="2" <?php if($instructor->status == 2): ?> selected <?php endif; ?>><?php echo e(__('Blocked')); ?></option>
                                                <option value="0" <?php if($instructor->status == 0): ?> selected <?php endif; ?>><?php echo e(__('Pending')); ?></option>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="action__buttons">
                                                <a href="<?php echo e(route('instructor.view', [$instructor->uuid])); ?>" class="btn-action mr-30" title="View Details">
                                                    <img src="<?php echo e(asset('admin/images/icons/eye-2.svg')); ?>" alt="eye">
                                                </a>
                                                <a href="<?php echo e(route('instructor.edit', [$instructor->uuid])); ?>" class="btn-action mr-30" title="Edit Details">
                                                    <img src="<?php echo e(asset('admin/images/icons/edit-2.svg')); ?>" alt="edit">
                                                </a>
                                                <a href="javascript:void(0);" data-url="<?php echo e(route('instructor.delete', [$instructor->uuid])); ?>" title="Delete" class="btn-action deleteBtn">
                                                    <img src="<?php echo e(asset('admin/images/icons/trash-2.svg')); ?>" alt="trash">
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <div class="mt-3">
                                <?php echo e($instructors->links()); ?>

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
    <script src="<?php echo e(asset('admin/js/custom/instructor-delete.js')); ?>"></script>
    <script>
        'use strict'
        $(".status").change(function () {
            var id = $(this).closest('tr').find('#hidden_id').html();
            var status_value = $(this).val();
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
                        url: "<?php echo e(route('admin.instructor.changeInstructorStatus')); ?>",
                        data: {"status": status_value, "id": id, "_token": "<?php echo e(csrf_token()); ?>",},
                        datatype: "json",
                        success: function (data) {
                            toastr.options.positionClass = 'toast-bottom-right';
                            toastr.success('', "<?php echo e(__('Instructor status has been updated')); ?>");
                            setTimeout(function(){
                                window.location.reload(1);
                            }, 1000);
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/instructor/blocked.blade.php ENDPATH**/ ?>
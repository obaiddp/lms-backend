<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('TimeSlots')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('All TimeSlots')); ?></li>
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
                            <h2><?php echo e(__('All TimeSlots')); ?></h2>
                            <a style="background-color: #1E417D" href="<?php echo e(route('time_slots.create')); ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create New Slot
                            </a>
                        </div>
                     
                        <div class="customers__table">
                            <table id="customers-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('Sr No.')); ?></th>
                                    
                                    <th><?php echo e(__('Time')); ?></th>
                                    <th><?php echo e(__('Status')); ?></th>
                                    <th><?php echo e(__('Bookings')); ?></th>
                                
                                    <th class="text-center"><?php echo e(__('Action')); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $timeSlots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="removable-item">
                                        <td>
                                            <?php echo e($key+1); ?>

                                        </td>
                                        
                                        <td><?php echo e($slot->start_time->format('h:i A')); ?> - <?php echo e($slot->end_time->format('h:i A')); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo e($slot->is_active ? 'success' : 'danger'); ?>">
                                                <?php echo e($slot->is_active ? 'Active' : 'Inactive'); ?>

                                            </span>
                                        </td>
                                        <td><?php echo e($slot->bookings->count()); ?></td>
                                     
                                        <td class="">
                                            <div class="action__buttons">
                                            <a style="margin-right: 10px" href="<?php echo e(route('time_slots.edit', $slot->id)); ?>" class="btn-action mr-50">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                                                 
                                          
                                            
                                            <button style="margin-right: 10px" class="btn-action mr-50 deleteItem" data-formid="delete_row_form_<?php echo e($slot->id); ?>">
                                                
                                                <i class="fas fa-trash"></i>
                                            </button>
                                          
                                            <form action="<?php echo e(route('time_slots.destroy', ['slot'=> $slot->id])); ?>" method="get" id="delete_row_form_<?php echo e($slot->id); ?>">

                                            </form>

                                            

                                        <form action="<?php echo e(route('time_slots.toggle_status',['slot'=> $slot->id])); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <button style="margin-right: 10px" type="submit" class="btn btn-sm btn-<?php echo e($slot->is_active ? 'warning' : 'success'); ?>">
                                                    <i class="fas fa-<?php echo e($slot->is_active ? 'times' : 'check'); ?>"></i>
                                                </button>
                                            </form>
                                            
                                          </div>
                                        </td>
                                        
                                        
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <div class="mt-3">
                                <?php echo e($timeSlots->links()); ?>

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
    
    <script src="<?php echo e(asset('admin/js/custom/coordinator-delete.js')); ?>"></script>
    <script>
        'use strict'
        $(".change-status").change(function () {
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
                        url: "<?php echo e(route('admin.coordinator.changeCoordinatorStatus')); ?>",
                        data: {"status": status_value, "id": id, "_token": "<?php echo e(csrf_token()); ?>",},
                        datatype: "json",
                        success: function (data) {
                            toastr.options.positionClass = 'toast-bottom-right';
                            toastr.success('', "<?php echo e(__('Coordinator status has been updated')); ?>");
                            location.reload();
                        },
                        error: function () {
                            alert("Error!");
                        },
                    });
                } else if (result.dismiss === "cancel") {
                    location.reload();
                }
            });
        });
        
        $(".change-auto-content").change(function () {
            var id = $(this).closest('tr').find('#hidden_id').html();
            var status_value = $(this).val();
            Swal.fire({
                title: "<?php echo e(__('Are you sure to change?')); ?>",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "<?php echo e(__('Yes, Change it!')); ?>",
                cancelButtonText: "<?php echo e(__('No, cancel!')); ?>",
                reverseButtons: true
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo e(route('admin.coordinator.changeAutoContentStatus')); ?>",
                        data: {"auto_content_approval": status_value, "id": id, "_token": "<?php echo e(csrf_token()); ?>",},
                        datatype: "json",
                        success: function (data) {
                            toastr.options.positionClass = 'toast-bottom-right';
                            toastr.success('', "<?php echo e(__('Auto content status has been updated')); ?>");
                            location.reload();
                        },
                        error: function () {
                            alert("Error!");
                        },
                    });
                } else if (result.dismiss === "cancel") {
                    location.reload();
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/time_slots/index.blade.php ENDPATH**/ ?>
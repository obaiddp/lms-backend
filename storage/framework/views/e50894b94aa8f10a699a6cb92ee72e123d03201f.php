<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('New Enrollment Requests')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('All New Enrollment Requests')); ?></li>
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
                            <h2><?php echo e(__('All New Enrollment Requests')); ?></h2>
                        </div>
                        <div class="customers__table">
                            <table id="free-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('Sr No.')); ?></th>
                                    <th><?php echo e(__('Student')); ?></th>
                                    <th><?php echo e(__('Course')); ?></th>
                                    <th><?php echo e(__('Preferred Date')); ?></th>
                                    <th><?php echo e(__('Preferred Time')); ?></th>
                                    <th><?php echo e(__('Enrollment Status')); ?></th>
                                    <th><?php echo e(__('Request')); ?></th> <!-- New Column -->
                                    <th><?php echo e(__('Action')); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                ?>
                                <?php $__currentLoopData = $freeClasses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $freeClass): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="removable-item">
                                        <td>
                                            <?php
                                                $response = checkfreeRecordTime($freeClass->created_at)
                                            ?>
                                              <?php echo e($i++); ?>

                                            <span style="color:white; font-size:12px;padding:4px" class="badge bg-primary"><?php echo e(__('New')); ?></span>
                                        </td>
                                        <td>
                                            <?php if($freeClass->student): ?>
                                            <a href="<?php echo e(route('student.view', [$freeClass->student->uuid])); ?>" class="btn-action mr-30" title="View Details">
                                                <?php echo e($freeClass->student?->name); ?><br>
                                            </a>
                                            <?php endif; ?>
                                         
                                        </td>
                                         <td>
                                            <?php echo e($freeClass->course?->title); ?><br>
                                        </td>
                                        <td>
                                            <?php echo e(date('d-m-Y',strtotime($freeClass->prefer_date))); ?><br>
                                        </td>
                                        <td>
                                            <?php echo e(date('h:i A',strtotime($freeClass->prefer_time))); ?><br>
                                        </td>

                                        <td>
                                            <?php if($freeClass->student_enrolled): ?>
                                                <span class="badge bg-success"><?php echo e(__('Enrolled')); ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark"><?php echo e(__('Pending')); ?></span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?php if($freeClass->approved): ?>
                                                <span class="badge bg-success"><?php echo e(__('Approved')); ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark"><?php echo e(__('Pending')); ?></span>
                                            <?php endif; ?>
                                        </td>

                                        <td>

                                       
                                            <?php if($freeClass->student): ?>
                                            <a href="<?php echo e(route('student.view', ['uuid'=>$freeClass->student?->uuid , 'trial_id' => $freeClass->id])); ?>" class="btn-action mr-30" title="View Free Class Details">
                                                <img src="<?php echo e(asset('admin/images/icons/eye-2.svg')); ?>" alt="eye">
                                            </a>
                                            <?php endif; ?>

                                             
                                                <?php if(!$freeClass->approved): ?>
                                                   
                                                 
                                                    <a href="<?php echo e(route('admin.trial.approve.form', $freeClass->id)); ?>" class="btn btn-sm btn-success mt-1">
                                                        <?php echo e(__('Approve')); ?>

                                                    </a>
                                                <?php else: ?>
                                                    <?php if(!$freeClass->student_enrolled): ?>
                                                        <button type="button"
                                                                class="btn btn-sm btn-warning text-dark resend-approval mt-1"
                                                                data-id="<?php echo e($freeClass->id); ?>">
                                                            <?php echo e(__('Resend Email')); ?>

                                                        </button>

                                                        <?php if($freeClass->invoice_pdf_path): ?>
                                                            <a href="<?php echo e(route('admin.trial.download.voucher', $freeClass->id)); ?>"
                                                               class="btn btn-sm btn-success mt-1" target="_blank">
                                                                <?php echo e(__('Download PDF')); ?>

                                                            </a>
                                                        <?php else: ?>
                                                            
                                                        <?php endif; ?>

                                                    <?php endif; ?>
                                                <?php endif; ?>


                                            <?php if($freeClass->approved): ?>
                                                <?php if(!$freeClass->status): ?>
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('add_classes')): ?>    
                                                        <a style="margin-left:5px" class="btn-action mr-30" href="<?php echo e(route('classes.create_free_class_individual',['id'=>$freeClass->id])); ?>" title="Schedule Free Class">
                                                            <img src="<?php echo e(asset('admin/images/icons/edit-2.svg')); ?>" alt="edit">
                                                        </a>   
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                     
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <div class="mt-3">
                                <?php echo e($freeClasses->links()); ?>

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
    'use strict';

    // Handle Approve Trial
    $(document).on('click', '.approve-trial', function () {
        const $button = $(this);
        const trialId = $button.data('id');

        Swal.fire({
            title: "<?php echo e(__('Approve Request Enrollment?')); ?>",
            text: "<?php echo e(__('This will mark the enrollment request as approved and send an email to the student.')); ?>",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "<?php echo e(__('Yes, Approve')); ?>",
            cancelButtonText: "<?php echo e(__('Cancel')); ?>",
            preConfirm: () => {
                // Start loading before AJAX
                $button.prop('disabled', true)
                       .text('<?php echo e(__('Approving...')); ?>')
                       .append(' <i class="spinner-border spinner-border-sm"></i>');
            }
        }).then((result) => {
            if (result.isConfirmed || result.value) {
                $.ajax({
                    url: "<?php echo e(route('admin.trial.approve')); ?>",
                    type: "POST",
                    data: {
                        id: trialId,
                        _token: "<?php echo e(csrf_token()); ?>"
                    },
                    success: function (response) {
                        $button.prop('disabled', false)
                               .html('<?php echo e(__('Approve')); ?>'); // Reset button

                        if (response.success) {
                            toastr.success(response.message || "<?php echo e(__('Enrollment Request approved and email sent!')); ?>");
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            toastr.error(response.message || "<?php echo e(__('Failed to approve enrollment request.')); ?>");
                        }
                    },
                    error: function () {
                        $button.prop('disabled', false)
                               .html('<?php echo e(__('Approve')); ?>'); // Reset button

                        toastr.error("<?php echo e(__('Something went wrong! Please try again.')); ?>");
                    }
                });
            } else {
                // If canceled, ensure button is re-enabled
                $button.prop('disabled', false);
            }
        });
    });

    // Handle Resend Approval Email
    $(document).on('click', '.resend-approval', function () {
        const $button = $(this);
        const trialId = $button.data('id');

        Swal.fire({
            title: "<?php echo e(__('Resend Approval Email?')); ?>",
            text: "<?php echo e(__('An approval email will be sent again to the student.')); ?>",
            icon: "info",
            showCancelButton: true,
            confirmButtonText: "<?php echo e(__('Yes, Resend')); ?>",
            cancelButtonText: "<?php echo e(__('Cancel')); ?>",
            preConfirm: () => {
                $button.prop('disabled', true)
                       .text('<?php echo e(__('Resending...')); ?>')
                       .append(' <i class="spinner-border spinner-border-sm"></i>');
            }
        }).then((result) => {
            if (result.isConfirmed || result.value) {
                $.ajax({
                    url: "<?php echo e(route('admin.trial.resend')); ?>",
                    type: "POST",
                    data: {
                        id: trialId,
                        _token: "<?php echo e(csrf_token()); ?>"
                    },
                    success: function (response) {
                        $button.prop('disabled', false)
                               .html('<?php echo e(__('Resend Email')); ?>'); // Reset button

                        if (response.success) {
                            toastr.success(response.message || "<?php echo e(__('Approval email resent successfully!')); ?>");
                        } else {
                            toastr.error(response.message || "<?php echo e(__('Failed to resend email.')); ?>");
                        }
                    },
                    error: function () {
                        $button.prop('disabled', false)
                               .html('<?php echo e(__('Resend Email')); ?>'); // Reset button

                        toastr.error("<?php echo e(__('Request failed. Please check your connection.')); ?>");
                    }
                });
            } else {
                $button.prop('disabled', false); // Ensure enabled if canceled
            }
        });
    });
</script>




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
                        url: "<?php echo e(route('admin.student.changeStudentStatus')); ?>",
                        data: {"status": status_value, "id": id, "_token": "<?php echo e(csrf_token()); ?>",},
                        datatype: "json",
                        success: function (data) {
                            toastr.options.positionClass = 'toast-bottom-right';
                            toastr.success('', "<?php echo e(__('Student status has been updated')); ?>");
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/student/freeClassList.blade.php ENDPATH**/ ?>
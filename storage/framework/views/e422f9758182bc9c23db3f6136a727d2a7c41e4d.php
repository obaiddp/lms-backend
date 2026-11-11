<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Affiliate')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Affiliate')); ?></li>
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
                            <h2><?php echo e(__('Affiliator list')); ?></h2>
                        </div>

                        <!-- Affiliate Tab List Start -->
                        <div class="orders__top affiliate-tab-list-top">
                            <div class="item">
                                <div class="sort-by">
                                    <ul class="nav nav-tabs affiliate-tab-list" id="nav-tab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="nav-one-tab" data-bs-toggle="tab" data-bs-target="#nav-one" type="button" role="tab" aria-controls="nav-one" aria-selected="true">
                                                <?php echo e(__('All')); ?>

                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="nav-two-tab" data-bs-toggle="tab" data-bs-target="#nav-two" type="button" role="tab" aria-controls="nav-two" aria-selected="false">
                                                <?php echo e(__('Pending Request')); ?>

                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="nav-three-tab" data-bs-toggle="tab" data-bs-target="#nav-three" type="button" role="tab" aria-controls="nav-three" aria-selected="false">
                                                <?php echo e(__('Suspend Request')); ?>

                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="nav-four-tab" data-bs-toggle="tab" data-bs-target="#nav-four" type="button" role="tab" aria-controls="nav-four" aria-selected="false">
                                                <?php echo e(__('Approved Request')); ?>

                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- Affiliate Tab List End -->

                        <!-- Affiliate Tab Content List Start -->
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-one" role="tabpanel" aria-labelledby="nav-one-tab">
                                <!-- Affiliate Tab Pane Table Start -->
                                <div class="customers__table affiliate-table">
                                    <table id="all-request-table" class="row-border data-table-filter table-style">
                                        <thead>
                                        <tr>
                                            <th><?php echo e(__('#ID')); ?></th>
                                            <th><?php echo e(__('Time')); ?></th>
                                            <th><?php echo e(__('Type')); ?></th>
                                            <th><?php echo e(__('Email')); ?></th>
                                            <th><?php echo e(__('Status')); ?></th>
                                            <th><?php echo e(__('View')); ?></th>
                                            <th><?php echo e(__('Action')); ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $__currentLoopData = $requestsAll; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($req->id); ?></td>
                                                <td><?php echo e($req->created_at); ?></td>
                                                <td><?php echo e(getUserType($req->user->role)); ?></td>
                                                <td><?php echo e($req->user->email); ?></td>
                                                <td>
                                                    <span class="status <?php echo e(statusClass($req->status)); ?>"><?php echo e(statusAction($req->status)); ?></span>
                                                </td>
                                                <td >
                                                    <button type="button" data-name="<?php echo e($req->user->name); ?>" data-email="<?php echo e($req->user->email); ?>" data-address="<?php echo e($req->address); ?>" data-letter="<?php echo e($req->letter); ?>" class="mx-2 view-request" title="View Details"  ><img src="<?php echo e(asset('admin/images/icons/eye-2.svg')); ?>" alt="eye"></button>
                                                </td>
                                                <td>
                                                    <ul class="action-list">
                                                        <li class="nav-item dropdown">
                                                            <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <img src="<?php echo e(asset('admin/images/icons/ellipsis-v.svg')); ?>" alt="icon">
                                                            </a>
                                                            <span id="hidden_id" style="display: none"><?php echo e($req->id); ?></span>
                                                            <ul class="dropdown-menu">
                                                                <?php if($req->status != STATUS_APPROVED ): ?>
                                                                    <li><a class="status dropdown-item" data-target="1" ><?php echo e(__('Approved')); ?></a></li>
                                                                <?php endif; ?>
                                                                <?php if($req->status != STATUS_REJECTED ): ?>
                                                                    <li><a class="status dropdown-item" data-target="2" ><?php echo e(__('Rejected')); ?></a></li>
                                                                <?php endif; ?>
                                                                <?php if($req->status != STATUS_PENDING ): ?>
                                                                    <li><a class="status dropdown-item" data-target="0"><?php echo e(__('Pending')); ?></a></li>
                                                                <?php endif; ?>
                                                            </ul>
                                                        </li>
                                                    </ul>
                                                </td>

                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="nav-two" role="tabpanel" aria-labelledby="nav-two-tab">
                                <!-- Affiliate Tab Pane Table Start -->
                                <div class="customers__table affiliate-table">
                                    <table id="pending-request-table" class="row-border data-table-filter table-style">
                                        <thead>
                                        <tr>
                                            <th><?php echo e(__('#ID')); ?></th>
                                            <th><?php echo e(__('Time')); ?></th>
                                            <th><?php echo e(__('Type')); ?></th>
                                            <th><?php echo e(__('Email')); ?></th>
                                            <th><?php echo e(__('Status')); ?></th>
                                            <th><?php echo e(__('View')); ?></th>
                                            <th><?php echo e(__('Action')); ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $__currentLoopData = $requestsPending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($req->id); ?></td>
                                                <td><?php echo e($req->created_at); ?></td>
                                                <td><?php echo e(getUserType($req->user->role)); ?></td>
                                                <td><?php echo e($req->user->email); ?></td>
                                                <td>
                                                    <span class="status <?php echo e(statusClass($req->status)); ?>"><?php echo e(statusAction($req->status)); ?></span>
                                                </td>
                                                <td >
                                                    <button type="button" data-name="<?php echo e($req->user->name); ?>" data-email="<?php echo e($req->user->email); ?>" data-address="<?php echo e($req->address); ?>" data-letter="<?php echo e($req->letter); ?>" class="mx-2 view-request" title="View Details"  ><img src="<?php echo e(asset('admin/images/icons/eye-2.svg')); ?>" alt="eye"></button>
                                                </td>
                                                <td>
                                                    <?php if(!$req->status): ?>
                                                        <ul class="action-list">
                                                            <li class="nav-item dropdown">
                                                                <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    <img src="<?php echo e(asset('admin/images/icons/ellipsis-v.svg')); ?>" alt="icon">
                                                                </a>
                                                                <span id="hidden_id" style="display: none"><?php echo e($req->id); ?></span>
                                                                <ul class="dropdown-menu">
                                                                    <?php if($req->status != STATUS_APPROVED ): ?>
                                                                        <li><a class="status dropdown-item" data-target="1" ><?php echo e(__('Approved')); ?></a></li>
                                                                    <?php endif; ?>
                                                                    <?php if($req->status != STATUS_REJECTED ): ?>
                                                                        <li><a class="status dropdown-item" data-target="2" ><?php echo e(__('Rejected')); ?></a></li>
                                                                    <?php endif; ?>
                                                                    <?php if($req->status != STATUS_PENDING ): ?>
                                                                        <li><a class="status dropdown-item" data-target="0"><?php echo e(__('Pending')); ?></a></li>
                                                                    <?php endif; ?>
                                                                </ul>
                                                            </li>
                                                        </ul>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                        </tbody>
                                    </table>
                                </div>
                                <!-- Affiliate Tab Pane Table End -->
                            </div>

                            <div class="tab-pane fade" id="nav-three" role="tabpanel" aria-labelledby="nav-three-tab">
                                <!-- Affiliate Tab Pane Table Start -->
                                <div class="customers__table affiliate-table">
                                    <table id="reject-request-table" class="row-border data-table-filter table-style">
                                        <thead>
                                        <tr>
                                            <th><?php echo e(__('#ID')); ?></th>
                                            <th><?php echo e(__('Time')); ?></th>
                                            <th><?php echo e(__('Type')); ?></th>
                                            <th><?php echo e(__('Email')); ?></th>
                                            <th><?php echo e(__('Status')); ?></th>
                                            <th><?php echo e(__('View')); ?></th>
                                            <th><?php echo e(__('Action')); ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $__currentLoopData = $requestsSuspend; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($req->id); ?></td>
                                                <td><?php echo e($req->created_at); ?></td>
                                                <td><?php echo e(getUserType($req->user->role)); ?></td>
                                                <td><?php echo e($req->user->email); ?></td>
                                                <td>
                                                    <span class="status <?php echo e(statusClass($req->status)); ?>"><?php echo e(statusAction($req->status)); ?></span>
                                                </td>
                                                <td >
                                                    <button type="button" data-name="<?php echo e($req->user->name); ?>" data-email="<?php echo e($req->user->email); ?>" data-address="<?php echo e($req->address); ?>" data-letter="<?php echo e($req->letter); ?>" class="mx-2 view-request" title="View Details"  ><img src="<?php echo e(asset('admin/images/icons/eye-2.svg')); ?>" alt="eye"></button>
                                                </td>
                                                <td>
                                                    <?php if(!$req->status): ?>
                                                        <ul class="action-list">
                                                            <li class="nav-item dropdown">
                                                                <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    <img src="<?php echo e(asset('admin/images/icons/ellipsis-v.svg')); ?>" alt="icon">
                                                                </a>
                                                                <span id="hidden_id" style="display: none"><?php echo e($req->id); ?></span>
                                                                <ul class="dropdown-menu">
                                                                    <?php if($req->status != STATUS_APPROVED ): ?>
                                                                        <li><a class="status dropdown-item" data-target="1" ><?php echo e(__('Approved')); ?></a></li>
                                                                    <?php endif; ?>
                                                                    <?php if($req->status != STATUS_REJECTED ): ?>
                                                                        <li><a class="status dropdown-item" data-target="2" ><?php echo e(__('Rejected')); ?></a></li>
                                                                    <?php endif; ?>
                                                                    <?php if($req->status != STATUS_PENDING ): ?>
                                                                        <li><a class="status dropdown-item" data-target="0"><?php echo e(__('Pending')); ?></a></li>
                                                                    <?php endif; ?>
                                                                </ul>
                                                            </li>
                                                        </ul>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                        </tbody>
                                    </table>
                                </div>
                                <!-- Affiliate Tab Pane Table End -->
                            </div>

                            <div class="tab-pane fade" id="nav-four" role="tabpanel" aria-labelledby="nav-four-tab">
                                <!-- Affiliate Tab Pane Table Start -->
                                <div class="customers__table affiliate-table">
                                    <table id="approved-request-table" class="row-border data-table-filter table-style">
                                        <thead>
                                        <tr>
                                            <th><?php echo e(__('#ID')); ?></th>
                                            <th><?php echo e(__('Time')); ?></th>
                                            <th><?php echo e(__('Type')); ?></th>
                                            <th><?php echo e(__('Email')); ?></th>
                                            <th><?php echo e(__('Status')); ?></th>
                                            <th><?php echo e(__('View')); ?></th>
                                            <th><?php echo e(__('Action')); ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $__currentLoopData = $requestsApproved; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($req->id); ?></td>
                                                <td><?php echo e($req->created_at); ?></td>
                                                <td><?php echo e(getUserType($req->user->role)); ?></td>
                                                <td><?php echo e($req->user->email); ?></td>
                                                <td>
                                                    <span class="status <?php echo e(statusClass($req->status)); ?>"><?php echo e(statusAction($req->status)); ?></span>
                                                </td>
                                                <td >
                                                    <button type="button" data-name="<?php echo e($req->user->name); ?>" data-email="<?php echo e($req->user->email); ?>" data-address="<?php echo e($req->address); ?>" data-letter="<?php echo e($req->letter); ?>" class="mx-2 view-request" title="<?php echo e(__('View Details')); ?>"  ><img src="<?php echo e(asset('admin/images/icons/eye-2.svg')); ?>" alt="eye"></button>
                                                </td>
                                                <td>
                                                    <?php if(!$req->status): ?>
                                                        <ul class="action-list">
                                                            <li class="nav-item dropdown">
                                                                <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    <img src="<?php echo e(asset('admin/images/icons/ellipsis-v.svg')); ?>" alt="icon">
                                                                </a>
                                                                <span id="hidden_id" style="display: none"><?php echo e($req->id); ?></span>
                                                                <ul class="dropdown-menu">
                                                                    <?php if($req->status != STATUS_APPROVED ): ?>
                                                                        <li><a class="status dropdown-item" data-target="1" ><?php echo e(__('Approved')); ?></a></li>
                                                                    <?php endif; ?>
                                                                    <?php if($req->status != STATUS_REJECTED ): ?>
                                                                        <li><a class="status dropdown-item" data-target="2" ><?php echo e(__('Rejected')); ?></a></li>
                                                                    <?php endif; ?>
                                                                    <?php if($req->status != STATUS_PENDING ): ?>
                                                                        <li><a class="status dropdown-item" data-target="0"><?php echo e(__('Pending')); ?></a></li>
                                                                    <?php endif; ?>
                                                                </ul>
                                                            </li>
                                                        </ul>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                        </tbody>
                                    </table>
                                </div>
                                <!-- Affiliate Tab Pane Table End -->
                            </div>

                        </div>
                        <!-- Affiliate Tab Content List End -->

                    </div>
                </div>
            </div>

        </div>
    </div>
    <!--RejectedNote Modal Start-->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" >
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h4><?php echo e(__('Write Feedback')); ?></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="status" id="status">
                    <div class="custom-form-group mb-3 row">
                        <div class="col-lg-12">
                            <textarea name="note" id="note" required class="form-control" placeholder="<?php echo e(__('Note')); ?>"></textarea>
                        </div>
                    </div>

                    <div class="custom-form-group mb-3 row">
                        <div class="col-lg-12 text-right">
                            <button type="button" class="submit-note btn btn-blue mr-30"><?php echo e(__('Submit')); ?></button>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <!--RejectedNote Modal End-->
    <!--ViewRequest Modal Start-->
    <div class="modal fade" id="viewRequestModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" >
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h4><?php echo e(__('Affiliate Request')); ?></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="custom-form-group mb-3 row">
                        <div class="col-lg-4">
                            <label for="name"><?php echo e(__('Name')); ?></label>
                            <input type="text" id="rqName" class="form-control" value="" readonly>
                        </div>
                        <div class="col-lg-8">
                            <label for="name"><?php echo e(__('Email')); ?></label>
                            <input type="text" id="rqEmail" class="form-control" value="" readonly>
                        </div>
                        <div class="col-lg-12">
                            <label for="name"><?php echo e(__('Address')); ?></label>
                            <input type="text" id="rqAddress" class="form-control" value="" readonly>
                        </div>
                    </div>
                    <div class="custom-form-group mb-3 row">
                        <div class="col-lg-12">
                            <label for="name"><?php echo e(__('Letter')); ?></label>
                            <textarea  class="form-control" id="rqLetter"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--ViewRequest Modal End-->
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
        $(".status").click(function () {
            var id = $(this).closest('tr').find('#hidden_id').html();
            var status_value = $(this).data('target');
            if(status_value === 2){
                $("#rejectModal #id").val(id);
                $("#rejectModal #status").val(status_value);
                $("#rejectModal").modal("show");
            }else{
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
                            url: "<?php echo e(route('affiliate.affiliate-request-status-change')); ?>",
                            data: {"status": status_value, "id": id,"note": false, "_token": "<?php echo e(csrf_token()); ?>",},
                            datatype: "json",
                            success: function (data) {
                                console.log(data);
                                if(data.data == 'success'){
                                    toastr.success('', "<?php echo e(__('Request status has been updated')); ?>");
                                    location.reload();
                                }else{
                                    toastr.error('',  data.message);
                                }

                            },
                            error: function () {
                                alert("Error!");
                            },
                        });
                    } else if (result.dismiss === "cancel") {
                    }
                });
            }
        });
        $(".submit-note").click(function () {
            var status_value = $("#status").val();
            var id = $("#id").val();
            var note = $("#note").val();
            $.ajax({
                type: "POST",
                url: "<?php echo e(route('affiliate.affiliate-request-status-change')); ?>",
                data: {"status": status_value, "id": id,"note": note, "_token": "<?php echo e(csrf_token()); ?>",},
                datatype: "json",
                success: function (data) {
                    console.log(data);
                    toastr.success('', "<?php echo e(__('Request status has been updated')); ?>");
                    location.reload();
                },
                error: function () {
                    alert("Error!");
                },
            });

        });

        $(".view-request").on("click", function () {
            $("#viewRequestModal .note").text($(this).data("note"));
            $("#rqName").val($(this).data("name"));
            $("#rqEmail").val($(this).data("email"));
            $("#rqAddress").val($(this).data("address"));
            $("#rqLetter").text($(this).data("letter"));
            $("#viewRequestModal").modal("show");
        });

    </script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/affiliate/affiliate-list.blade.php ENDPATH**/ ?>
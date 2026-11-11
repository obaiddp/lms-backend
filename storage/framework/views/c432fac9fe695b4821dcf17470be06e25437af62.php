<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Affliliator History')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Affliliator History')); ?></li>
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
                            <h2><?php echo e(__('Affliliator History')); ?></h2>
                        </div>

                        <!-- Affiliate History Table Start -->
                        <div class="table-responsive">
                            <table id="all-affiliate" class=" table row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th class="all"><?php echo e(__('Date')); ?></th>
                                    <th class="all"><?php echo e(__('User')); ?></th>
                                    <th class="all"><?php echo e(__('Type')); ?></th>
                                    <th class="none"><?php echo e(__('Course Name')); ?></th>
                                    <th class="all"><?php echo e(__('Actual Amount')); ?></th>
                                    <th class="all"><?php echo e(__('Earned Amount')); ?></th>
                                    <th class="all"><?php echo e(__('Commission %')); ?></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                        <!-- Affiliate History Table End -->

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page content area end -->
<?php $__env->stopSection(); ?>

<?php $__env->startPush('style'); ?>

<!-- DataTables -->
<link rel="stylesheet" href="<?php echo e(asset('frontend/assets/vendor/datatable/datatables-bs4/css/dataTables.bootstrap4.min.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('frontend/assets/vendor/datatable/datatables-responsive/css/responsive.bootstrap4.min.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('frontend/assets/vendor/datatable/datatables-buttons/css/buttons.bootstrap4.min.css')); ?>">

<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>


<!-- DataTables  & Plugins -->
<script src="<?php echo e(asset('frontend/assets/vendor/datatable/datatables/jquery.dataTables.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/assets/vendor/datatable/datatables-bs4/js/dataTables.bootstrap4.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/assets/vendor/datatable/datatables-responsive/js/dataTables.responsive.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/assets/vendor/datatable/datatables-responsive/js/responsive.bootstrap4.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/assets/vendor/datatable/datatables-buttons/js/dataTables.buttons.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/assets/vendor/datatable/datatables-buttons/js/buttons.bootstrap4.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/assets/vendor/datatable/jszip/jszip.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/assets/vendor/datatable/pdfmake/pdfmake.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/assets/vendor/datatable/pdfmake/vfs_fonts.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/assets/vendor/datatable/datatables-buttons/js/buttons.html5.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/assets/vendor/datatable/datatables-buttons/js/buttons.print.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/assets/vendor/datatable/datatables-buttons/js/buttons.colVis.min.js')); ?>"></script>

<script>
    (function($) {
        "use strict";
        $('#all-affiliate').DataTable({
            processing: true,
            serverSide: true,
            language: {
                'paginate': {
                    'previous': '<span class="iconify" data-icon="icons8:angle-left"></span>',
                    'next': '<span class="iconify" data-icon="icons8:angle-right"></span>'
                }
            },
            pageLength: 25,

            responsive: true,
            ajax: "<?php echo e(route('affiliate.affiliate-history-data')); ?>",
            order: [1, 'desc'],
            autoWidth:false,
            dom: '<"row"<"col-sm-4"l><"col-sm-4"B><"col-sm-4"f>>tr<"bottom"<"row"<"col-sm-6"i><"col-sm-6"p>>><"clear">',
            lengthMenu: [
                [ 10, 25, 50, -1 ],
                [ 10, 25, 50, 'all' ],
            ],
            buttons: [
                { extend: 'copy', className: 'theme-btn theme-button1 default-hover-btn' },
                { extend: 'excel', className: 'theme-btn theme-button1 default-hover-btn' },
                { extend: 'pdf', className: 'theme-btn theme-button1 default-hover-btn' }
            ],
            columns: [
                {"data": "date", "title":'Date'},
                {"name": "users.email","data": "email","title":"User"},
                {"name": "type","data": "type","title":"Type"},
                {"name": "courses.title","data": "title","title":"Course Name"},
                {"data": "actual_price","title":"Actual Amount"},
                {"data": "commission","title":"Earned Amount"},
                {"data": "commission_percentage","title":"Commission %"},

            ]
        });
    })(jQuery)
</script>
<?php $__env->stopPush(); ?>



<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/affiliate/affiliator-history.blade.php ENDPATH**/ ?>
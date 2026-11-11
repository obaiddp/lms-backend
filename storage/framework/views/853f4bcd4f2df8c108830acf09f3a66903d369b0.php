<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(__(@$title)); ?></title>

    <!-- Favicon included -->
    <link rel="shortcut icon" href="<?php echo e(getImageFile(get_option('app_fav_icon'))); ?>" type="image/x-icon">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <!-- Apple touch icon included -->
    <link rel="apple-touch-icon" href="<?php echo e(getImageFile(get_option('app_fav_icon'))); ?>">

    <link rel="stylesheet" href="<?php echo e(asset('admin/sweetalert2/sweetalert2.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('frontend/assets/css/navbar-styles.css')); ?>">

    <!-- All CSS files included here -->
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/all.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('frontend/assets/vendor/bootstrap/css/bootstrap.min.css')); ?>">
    <?php echo $__env->yieldPushContent('style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/metisMenu.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('admin/styles/main.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/admin-extra.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/time_slot.css')); ?>">
    <link href="<?php echo e(asset('common/css/select2.css')); ?>" rel="stylesheet">
    

    <?php if(isEnableOpenAI()): ?>
    <link rel="stylesheet" href="<?php echo e(asset('addon/AI/css/main.css')); ?>">
    <?php endif; ?>

    <?php echo toastr_css(); ?>

    <?php if(!empty(get_option('certificate_regular')) && get_option('certificate_regular') != '' ): ?>
    <style>
        .certificateFont {
            @font-face {
                font-family: "certificateFont";
                url: <?php echo e(asset(get_option('certificate_font'))); ?>,
            }
        }

       
        .url-cell {
            word-wrap: break-word; /* Ensures the URL wraps in the cell */
            word-break: break-word; /* For breaking at logical points */
            white-space: normal; /* Allows wrapping within the cell */
            max-width: 400px; /* Limits cell width for better readability */
        }
        
    </style>
    <?php endif; ?>

    <?php if(isset($zoomSliderPercentage)): ?>
    <style>
 .slider-container-zoom {
            width: 100%; /* Full width */
            background-color: #e0e0e0; /* Light gray background */
            border-radius: 10px;
            overflow: hidden;
            height: 30px; /* Adjust height as needed */
            margin-top: 20px;
            position: relative;
        }

        /* Colored bar style */
        .slider-progress-zoom {
            height: 100%;
            width: <?php echo e($zoomSliderPercentage); ?>%; /* Dynamic width based on percentage */
            background-color: <?php echo e($zoomSliderPercentage > 70 ? 'red' : ($zoomSliderPercentage > 30 ? 'yellow' : 'green')); ?>;
            transition: width 0.3s ease; /* Smooth transition */
        }

        /* Display percentage text */
        .slider-text-zoom {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: black;
            font-weight: bold;
            font-size: 16px;
        }
    </style>
       <?php endif; ?>
</head>
<body class=" <?php echo e(selectedLanguage()->rtl == 1 ? 'direction-rtl' : 'direction-ltr'); ?> ">

<?php if(get_option('allow_preloader') == 1): ?>
    <!-- Pre Loader Area start -->
    <div id="preloader">
        <div id="preloader_status"><img src="<?php echo e(getImageFile(get_option('app_preloader'))); ?>" alt="img" /></div>
    </div>
    <!-- Pre Loader Area End -->
<?php endif; ?>

<!-- Sidebar area start -->
<?php echo $__env->make('admin.common.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<!-- Sidebar area end -->

<!-- Main Content area start -->
<div class="main-content">

    <!-- Header section start -->
    <?php echo $__env->make('admin.common.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <!-- Header section end -->

    <!-- page content wrap start -->
    <div class="page-content-wrap">
        <!-- Page content area start -->
        <?php echo $__env->yieldContent('content'); ?>
        <!-- Page content area end -->

        <!-- Footer section start -->
        <?php echo $__env->make('admin.common.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <!-- Footer section end -->
    </div>
    <!-- page content wrap end -->

    <?php if(isEnableOpenAI()): ?> 
        <?php echo $__env->make('addon.AI.content-generation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>

</div>
<!-- Main Content area end -->
<script>
    var deleteTitle = '<?php echo e(__("Sure! You want to delete?")); ?>';
    var deleteText = '<?php echo e(__("You wont be able to revert this!")); ?>';
    var deleteConfirmButton = '<?php echo e(__("Yes, Delete It!")); ?>';
    var deleteSuccessText = '<?php echo e(__("Item has been deleted")); ?>';
    function getLanguage(){
        return {
            "sEmptyTable": "<?php echo e(__('No data available in table')); ?>",
            "sInfo": "<?php echo e(__('Showing _START_ To _END_ Of _TOTAL_ Entries')); ?>",
            "sInfoEmpty": "<?php echo e(__('Showing 0 to 0 of 0 entries')); ?>",
            "sInfoFiltered": "<?php echo e(__('(filtered from _MAX_ total entries)')); ?>",
            "sInfoPostFix": "",
            "sInfoThousands": ",",
            "sLengthMenu": "<?php echo e(__('Show _MENU_ entries')); ?>",
            "sLoadingRecords": "<?php echo e(__('Loading...')); ?>",
            "sProcessing": "<?php echo e(__('Processing...')); ?>",
            "sSearch": "<?php echo e(__('Search:')); ?>",
            "sZeroRecords": "<?php echo e(__('No matching records found')); ?>",
            "oPaginate": {
                "sFirst": "<?php echo e(__('First')); ?>",
                "sLast": "<?php echo e(__('Last')); ?>",
                "sNext": "<?php echo e(__('Next')); ?>",
                "sPrevious": "<?php echo e(__('Previous')); ?>"
            },
            "oAria": {
                "sSortAscending": ": <?php echo e(__('activate to sort column ascending')); ?>",
                "sSortDescending": ": <?php echo e(__('activate to sort column descending')); ?>"
            }
        };
    }
</script>
<input type="hidden" id="base_url" value="<?php echo e(url('/')); ?>">

<!-- All Javascript files included here -->
<script src="<?php echo e(asset('frontend/assets/vendor/jquery/jquery-3.6.0.min.js')); ?>"></script>
<script src="<?php echo e(asset('admin/js/popper.min.js')); ?>"></script>
<script src="<?php echo e(asset('frontend/assets/vendor/bootstrap/js/bootstrap.min.js')); ?>"></script>
<script src="<?php echo e(asset('common/js/apexcharts.min.js')); ?>"></script>
<script src="<?php echo e(asset('admin/sweetalert2/sweetalert2.all.js')); ?>"></script>
<script src="<?php echo e(asset('common/js/iconify.min.js')); ?>"></script>
<?php echo $__env->yieldPushContent('script'); ?>
<script src="<?php echo e(asset('admin/js/admin-custom.js')); ?>"></script>
<script src="<?php echo e(asset('admin/js/metisMenu.min.js')); ?>"></script>
<script src="<?php echo e(asset('admin/js/main.js')); ?>"></script>
<script src="<?php echo e(asset('common/js/select2.min.js')); ?>"></script>
<?php if(isEnableOpenAI()): ?>
<script src="<?php echo e(asset('addon/AI/js/main.js')); ?>"></script>
<?php endif; ?>


<?php echo toastr_js(); ?>
<?php echo app('toastr')->render(); ?>

<script>
    function confirmRemoveAuthorization(url) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, remove it!'
    }).then((result) => {
        console.log(result);
        if (result.value) {
          
            window.location.href = url;
        }
    });
}
</script>
<?php if(@$errors->any()): ?>
    <script>
        "use strict";
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            toastr.options.positionClass = 'toast-bottom-right';
            toastr.error("<?php echo e($error); ?>")
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </script>
<?php endif; ?>
</body>
</html>
<?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/layouts/admin.blade.php ENDPATH**/ ?>
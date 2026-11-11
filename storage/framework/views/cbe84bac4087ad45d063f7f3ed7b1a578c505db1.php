<?php $__env->startSection('content'); ?>
<div class="page-content">
    <div class="container-fluid">

        <!-- header / KPI area (use passed variables) -->
        <div class="row">
            <div class="col-md-12">
                <div class="top-products__area bg-style">
                    <!-- Search -->
                    <div class="search-container mb-2">
                        <input type="text" id="overdueSearch" class="form-control" placeholder="Search overdue...">
                    </div>

                    <div id="overdue-table-container">
                        <?php echo $__env->make('admin.student_payment._overdue_table', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
<script>
$(function(){
    let searchTimer;
    const $search = $('#overdueSearch');
    const $container = $('#overdue-table-container');

    function showLoader() {
        // you can insert a loader element or toggle a class
        $container.css('opacity', '0.5');
    }
    function hideLoader() {
        $container.css('opacity', '1');
    }

    function fetchPage(page = 1, search = '') {
        showLoader();
        $.ajax({
            url: '<?php echo e(route("student_payment.overdue")); ?>',
            data: { page: page, search: search },
            success: function(res) {
                // If controller returns html in `html` key (ajax request)
                if (res.html) {
                    $container.html(res.html);
                } else {
                    // fallback if controller directly returned blade HTML
                    $container.html(res);
                }
            },
            error: function(err) {
                console.error(err);
                toastr.error('Could not fetch data');
            },
            complete: function() {
                hideLoader();
                attachPaginationHandlers(); // re-bind
            }
        });
    }

    $search.on('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            fetchPage(1, $search.val());
        }, 500);
    });

    function attachPaginationHandlers() {
        $container.find('.pagination a').off('click').on('click', function(e){
            e.preventDefault();
            const page = $(this).attr('href').split('page=')[1] || 1;
            fetchPage(page, $search.val());
        });
    }

    // init
    attachPaginationHandlers();
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/student_payment/overdue_index.blade.php ENDPATH**/ ?>
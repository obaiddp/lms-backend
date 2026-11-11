<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__(@$pageTitle)); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item"><a href="#"><?php echo e(__('Payout')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__(@$title)); ?></li>
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
                            <h2 class="pageTitle"><?php echo e(__('Monthly Pay Commission To Instructor')); ?></h2>
                        </div>
                        <form action="<?php echo e(route('payout.store.distribute.subscription.calculation')); ?>" method="post" class="form-horizontal">
                            <?php echo csrf_field(); ?>
                            <div class="row">
                                <div class="input__group mb-25 col-md-3">
                                    <label><?php echo e(__('Month-Year')); ?> <span class="text-danger">*</span></label>
                                    <input type="text" id="datepickerMonthYear" name="month_year" value="" placeholder="<?php echo e(__('Month-Year')); ?>"
                                           class="form-control datepickerMonthYear">
                                </div>
                                <div class="input__group mb-25 col-md-3">
                                    <label><?php echo e(__('Current Subscription')); ?> <b><span class="monthly_year_level"></span></b><span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" min="0" step="any" name="current_subscription" value=""
                                               placeholder="<?php echo e(__('Current Subscription')); ?>" class="form-control current_subscription" readonly>
                                    </div>
                                </div>
                                <div class="input__group mb-25 col-md-3">
                                    <label><?php echo e(__('Enroll Course')); ?> <b><span class="monthly_year_level"></span></b><span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" min="0" step="any" name="total_enroll_course" value=""
                                               placeholder="<?php echo e(__('Enroll Course')); ?>" class="form-control total_enroll_course" readonly>
                                    </div>
                                </div>
                                <div class="input__group mb-25 col-md-3">
                                    <label><?php echo e(__('Total Income From Subscription')); ?> <b><span class="monthly_year_level"></span></b><span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" min="0" step="any" name="total_income_from_subscription" value="" placeholder="<?php echo e(__('Total Income From Plan')); ?>"
                                               class="form-control total_income_from_subscription" readonly>
                                        <span class="input-group-text"><?php echo e(get_currency_symbol()); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12 text-right">
                                    <button class="btn btn-primary" type="submit"><?php echo e(__('Send Money')); ?></button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="customers__area bg-style mb-30 admin-dashboard-blog-list-page">
                        <div class="item-title d-flex justify-content-between">
                            <h2 class="pageTitle"><?php echo e(__('History For Each Month')); ?></h2>
                            <form action="<?php echo e(route('payout.store.distribute.subscription.calculation')); ?>" class="d-flex">
                                <div class="input__group">
                                    <select name="search_string" id="search_string" class="form-control">
                                        <option value=""><?php echo e(__('Filter Month Year')); ?></option>
                                        <?php $__currentLoopData = $monthYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $monthYear): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($monthYear->month_year); ?>" <?php echo e(app('request')->search_string == $monthYear->month_year); ?>><?php echo e($monthYear->month_year); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="customers__table" id="appendList">
                            
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Page content area end -->
<?php $__env->stopSection(); ?>

<?php $__env->startPush('style'); ?>
    <link href="<?php echo e(asset('admin/css/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css')); ?>" rel="stylesheet"/>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>

    <script src="<?php echo e(asset('admin/js/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js')); ?>"></script>
    <script>
        $(function () {
            $("#datepickerMonthYear").datepicker({
                format: "MM-yyyy",
                viewMode: "months-years",
                minViewMode: "months",
            });
        });
    </script>

    <script>
        'use strict'
        $(".datepickerMonthYear").on('change', function () {
            var month_year = $('#datepickerMonthYear').val();
            if (month_year == null || !month_year) {
                return
            }
            
            $('.monthly_year_level').html('(' + month_year + ')');
            $.ajax({
                type: "GET",
                url: "<?php echo e(route('payout.distribute.earning.management.calculation')); ?>",
                data: {"month_year": month_year},
                datatype: "json",
                success: function (response) {
                    $('.current_subscription').val(response.current_subscription)
                    $('.total_income_from_subscription').val(response.total_income_from_subscription)
                    $('.total_enroll_course').val(response.total_enroll_course)
                },
                error: function () {
                    alert("Error!");
                },
            });
        });
    </script>

    <script>
        "use strict"
        $(document).on('click', '.pagination a', function (event) {
            event.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            var route = "<?php echo e(url()->current()); ?>" + "?page=" + page;
            var search_string = $('#search_string').val()
            var data = {'search_string':search_string}
            fetch_data(route, data);
        });

        $('#search_string').change(function (){
            var search_string = $('#search_string').val()
            var route = "<?php echo e(url()->current()); ?>";
            var data = {'search_string':search_string}
            fetch_data(route, data);
        })

        function fetch_data(route, data) {
            $.ajax({
                url: route,
                data: data,
                success: function (data) {
                    $('#appendList').html(data);
                }
            });
        }
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/payout/distribute-subscription.blade.php ENDPATH**/ ?>
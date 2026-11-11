<?php $__env->startSection('content'); ?>
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Dashboard')); ?> </h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#"><?php echo e(get_option('app_name')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Dashboard')); ?></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">

                 <!--  <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/principal.png" alt="icon">
                        </div>
                     <div class="status__box__text">
                            <h2 class="color-purple"><?php echo e($total_admins); ?></h2>
                            <h3><?php echo e(__('Total Admin')); ?></h3>
                        </div>
                    </div>
                </div>-->
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/laptop.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-purple"><?php echo e($total_instructors); ?></h2>
                            <h3><?php echo e(__('Total Instructors')); ?></h3>
                        </div>
                    </div>
                </div>

           

                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/study.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-blue"><?php echo e($total_students); ?></h2>
                            <h3><?php echo e(__('Total Students')); ?></h3>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/test.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-green"><?php echo e($total_general_courses); ?></h2>
                            <h3><?php echo e(__('Total General Courses')); ?></h3>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/checklist.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-red"><?php echo e($total_lessons); ?></h2>
                            <h3><?php echo e(__('Total Lessons')); ?></h3>
                        </div>
                    </div>
                </div>



                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class=" status__box__v3 bg-style">
                        <div class="">
                            <h4><?php echo e(__('Zoom')); ?></h4>
                        </div>
                        <?php if($zoomErrorMessage != null): ?>
                            <br>
                            <br>
                            <div class="">
                                <h3 class="color-red"><?php echo e($zoomErrorMessage); ?></h3>
                            </div>
                        <?php else: ?>
                               <div align="left" style="margin-top:5px" class="">
                                    <h3><?php echo e(__('Sessions')); ?>: <span class="color-red"><?php echo e($zoomResultArray['Total_Sessions']); ?></span></h3>   
                                </div>
                                <div align="left" style="margin-top:5px" class="">
                                    <h3><?php echo e(__('Minutes')); ?>: <span class="color-red"><?php echo e($zoomResultArray['Total_Minutes']); ?></span></h3>   
                                </div>

                                <div class="slider-container-zoom">
                                    <div class="slider-progress-zoom"></div>
                                    <div class="slider-text-zoom">
                                        <?php echo e(round($zoomSliderPercentage, 2)); ?>%
                                    </div>
                                </div>

                        <?php endif; ?>
                     
                    </div>
                </div>









             

                    <!-- START: Overdue Fee Tracker -->
            <div class="row">
                <div class="col-md-12">
                    <div class="top-products__area bg-style">
                        <!-- Header -->
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h2><?php echo e(__('Overdue Fee Tracker')); ?></h2>
                            <div>
                                <a href="<?php echo e(route('student_payment.donation')); ?>" class="btn btn-sm btn-outline-primary">
                                    <?php echo e(__('Pending Donations')); ?>

                                </a>
                                <a href="<?php echo e(route('student_payment.completed_donation')); ?>" class="btn btn-sm btn-outline-primary">
                                    <?php echo e(__('Paid Donations')); ?>

                                </a>
                                <a href="<?php echo e(route('student_payment.pending')); ?>" class="btn btn-sm btn-outline-primary">
                                    <?php echo e(__('Pending/Overdue')); ?>

                                </a>
                                <a href="<?php echo e(route('student_payment.completed')); ?>" class="btn btn-sm btn-outline-primary">
                                    <?php echo e(__('Paid')); ?>

                                </a>
                                <span class="muted mr-3"><?php echo e(__('Total Outstanding:')); ?></span>
                                <strong>
                                    <?php if(get_currency_placement() == 'after'): ?>
                                        <?php echo e(number_format($overdue_total_outstanding, 0)); ?> <?php echo e(get_currency_symbol()); ?>

                                    <?php else: ?>
                                        <?php echo e(get_currency_symbol()); ?> <?php echo e(number_format($overdue_total_outstanding, 0)); ?>

                                    <?php endif; ?>
                                </strong>
                            </div>
                        </div>
                    
                        <!-- KPI boxes + Chart -->
                        <div class="row mb-3 align-items-stretch">
                            
                            <!-- Students with Dues -->
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="status__box status__box__v3 bg-style h-100 d-flex align-items-center justify-content-center text-center">
                                    <div class="status__box__text">
                                        <h2 class="color-blue mb-1"><?php echo e($overdue_students_with_dues); ?></h2>
                                        <h3 class="mb-0"><?php echo e(__('Students with Dues')); ?></h3>
                                    </div>
                                </div>
                            </div>
                        
                            <!-- Critical Overdue -->
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="status__box status__box__v3 bg-style h-100 d-flex align-items-center justify-content-center text-center">
                                    <div class="status__box__text">
                                        <h2 class="color-red mb-1"><?php echo e($overdue_critical_count); ?></h2>
                                        <h3 class="mb-0"><?php echo e(__('Critical Overdue (30+ days)')); ?></h3>
                                        <div class="muted">
                                            <?php if(get_currency_placement() == 'after'): ?>
                                                <?php echo e(number_format($overdue_critical_amount, 0)); ?> <?php echo e(get_currency_symbol()); ?>

                                            <?php else: ?>
                                                <?php echo e(get_currency_symbol()); ?> <?php echo e(number_format($overdue_critical_amount, 0)); ?>

                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                            <!-- Donut Chart -->
                            <div class="col-lg-6 col-md-12 mb-3">
                                <div class="status__box status__box__v3 bg-style h-100 d-flex align-items-center justify-content-center">
                                    <div id="overdueBucketsChart" style="height:120px; width:100%;"></div>
                                </div>
                            </div>
                        
                        </div> <!-- /row -->

                        
                    </div>
                </div>
            </div>
              <!-- END: Overdue Fee Tracker -->












                <hr>
                <div class="breadcrumb__title">
                    
                    <h2 style="color:black">Quran Education Online</h2>
                    <br>
                </div>
       
            
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/laptop.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-purple"><?php echo e($total_coordinators); ?></h2>
                            <h3><?php echo e(__('Total Coordinators')); ?></h3>
                        </div>
                    </div>
                </div>


                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/test.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-green"><?php echo e($total_standard_courses); ?></h2>
                            <h3><?php echo e(__('Total Live Courses')); ?></h3>
                        </div>
                    </div>
                </div>


                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/checklist.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-green"><?php echo e(countActiveTodayClasses()); ?></h2>
                            <h3><?php echo e(__('Today Classes')); ?></h3>
                        </div>
                    </div>
                </div>


                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/checklist.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-green"><?php echo e(countActiveScheduledClasses()); ?></h2>
                            <h3><?php echo e(__('Scheduled Classes')); ?></h3>
                        </div>
                    </div>
                </div>


                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/checklist.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-green"><?php echo e(countActiveCompletedClasses()); ?></h2>
                            <h3><?php echo e(__('Completed Classes')); ?></h3>
                        </div>
                    </div>
                </div>


                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/checklist.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-green"><?php echo e(countActiveMissedClasses()); ?></h2>
                            <h3><?php echo e(__('Missed Classes')); ?></h3>
                        </div>
                    </div>
                </div>

                <hr>
                <!--
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">

                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/test-1.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-blue"><?php echo e($total_active_courses); ?></h2>
                            <h3><?php echo e(__('Active Courses')); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/download.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-purple"><?php echo e($total_pending_courses); ?></h2>
                            <h3><?php echo e(__('Pending Courses')); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/withdraw.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-purple"><?php echo e($total_free_courses); ?></h2>
                            <h3><?php echo e(__('Free Courses')); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/elearning.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-green"><?php echo e($total_paid_courses); ?></h2>
                            <h3><?php echo e(__('Paid Courses')); ?></h3>
                        </div>
                    </div>
                </div>
-->
                
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/website.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-red"><?php echo e($total_lectures); ?></h2>
                            <h3><?php echo e(__('Total Lectures')); ?></h3>
                        </div>
                    </div>
                </div>
<!--
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/blogger.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-yellow"><?php echo e($total_blogs); ?></h2>
                            <h3><?php echo e(__('Total Blogs')); ?></h3>
                        </div>
                    </div>
                </div>
-->
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/save-money.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-green">

                                <?php if(get_currency_placement() == 'after'): ?>
                                    <?php echo e($total_paid_sales); ?> <?php echo e(get_currency_symbol()); ?>

                                <?php else: ?>
                                    <?php echo e(get_currency_symbol()); ?> <?php echo e($total_paid_sales); ?>

                                <?php endif; ?>
                            </h2>
                            <h3><?php echo e(__('Total Paid Sales')); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/paying.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-red"><?php echo e($total_free_sales); ?></h2>
                            <h3><?php echo e(__('Total Free Sales')); ?></h3>
                        </div>
                    </div>
                </div>
                <!--
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="images/admin-dashboard-icons/commission-1.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-purple">

                                <?php if(get_currency_placement() == 'after'): ?>
                                    <?php echo e($total_platform_charge); ?> <?php echo e(get_currency_symbol()); ?>

                                <?php else: ?>
                                    <?php echo e(get_currency_symbol()); ?> <?php echo e($total_platform_charge); ?>

                                <?php endif; ?>
                            </h2>
                            <h3><?php echo e(__('Total Platform Charge')); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="images/admin-dashboard-icons/money-loss.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-blue">
                                <?php if(get_currency_placement() == 'after'): ?>
                                    <?php echo e($total_platform_charge_this_month); ?> <?php echo e(get_currency_symbol()); ?>

                                <?php else: ?>
                                    <?php echo e(get_currency_symbol()); ?> <?php echo e($total_platform_charge_this_month); ?>

                                <?php endif; ?>

                            </h2>
                            <h3><?php echo e(__('Total Platform Charge (Current Month)')); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/discount.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-purple">
                                <?php if(get_currency_placement() == 'after'): ?>
                                    <?php echo e($total_admin_commission); ?> <?php echo e(get_currency_symbol()); ?>

                                <?php else: ?>
                                    <?php echo e(get_currency_symbol()); ?> <?php echo e($total_admin_commission); ?>

                                <?php endif; ?>

                            </h2>
                            <h3><?php echo e(__('Total Sell Commission')); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/shop.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-red">
                                <?php if(get_currency_placement() == 'after'): ?>
                                    <?php echo e($total_admin_commission_this_month); ?> <?php echo e(get_currency_symbol()); ?>

                                <?php else: ?>
                                    <?php echo e(get_currency_symbol()); ?> <?php echo e($total_admin_commission_this_month); ?>

                                <?php endif; ?></h2>
                            <h3><?php echo e(__('Total Sell Commission (Current Month)')); ?></h3>
                        </div>
                    </div>
                </div>-->
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/money.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-blue"> <?php if(get_currency_placement() == 'after'): ?>
                                    <?php echo e($total_revenue); ?> <?php echo e(get_currency_symbol()); ?>

                                <?php else: ?>
                                    <?php echo e(get_currency_symbol()); ?> <?php echo e($total_revenue); ?>

                                <?php endif; ?></h2>
                            <h3><?php echo e(__('Total Revenue')); ?></h3>
                        </div>
                    </div>
                </div>
                <!--
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/economy.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-yellow">
                                <?php if(get_currency_placement() == 'after'): ?>
                                    <?php echo e($total_new_withdraws); ?> <?php echo e(get_currency_symbol()); ?>

                                <?php else: ?>
                                    <?php echo e(get_currency_symbol()); ?> <?php echo e($total_new_withdraws); ?>

                                <?php endif; ?>
                                </h2>
                            <h3><?php echo e(__('Total Request Withdraw')); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="<?php echo e(asset('admin')); ?>/images/admin-dashboard-icons/save-money.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-green"> <?php if(get_currency_placement() == 'after'): ?>
                                    <?php echo e($total_complete_withdraws); ?> <?php echo e(get_currency_symbol()); ?>

                                <?php else: ?>
                                    <?php echo e(get_currency_symbol()); ?> <?php echo e($total_complete_withdraws); ?>

                                <?php endif; ?></h2>
                            <h3><?php echo e(__('Total Complete Withdraw')); ?></h3>
                        </div>
                    </div>
                </div>
            </div>-->

          

            <div class="row">
                <div class="col-md-12">
                    <div class="revenue__chart-v2__area bg-style">
                        <div class="revenue__chart-v2__top">
                            <div class="revenue__chart-v2__top__left">
                                <div class="content-title">
                                    <h2><?php echo e(__('Enrollment')); ?></h2>
                                </div>
                            </div>
                            <div class="revenue__chart-v2__top__right">
                                <div class="revenue__chart-v2__list">
                                    <nav>
                                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                            <button class="nav-link" id="nav-two-tab" data-bs-toggle="tab" data-bs-target="#nav-two" type="button" role="tab"
                                                    aria-controls="nav-two" aria-selected="false">
                                                <?php echo e(__('Month')); ?>

                                            </button>
                                            <button class="nav-link active" id="nav-three-tab" data-bs-toggle="tab" data-bs-target="#nav-three" type="button" role="tab"
                                                    aria-controls="nav-three" aria-selected="false">
                                                <?php echo e(__('Year')); ?>

                                            </button>
                                        </div>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        <div class="total-profit">
                            <h2>
                                <?php echo e(__('Total Enrollment')); ?> <span><?php echo e($total_enrolments); ?></span>
                            </h2>
                        </div>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade" id="nav-two" role="tabpanel" aria-labelledby="nav-two-tab">
                                <div id="chartMonth"></div>
                            </div>
                            <div class="tab-pane fade show active" id="nav-three" role="tabpanel" aria-labelledby="nav-three-tab">
                                <div id="chartYear"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--<div class="col-md-4">
                    <div class="sales-location__area bg-style">
                        <div class="sales-location__title">
                            <h2><?php echo e(__('Top Seller')); ?></h2>
                        </div>
                        <div class="sales-location__map">
                            <div id="topSellerChart" ></div>
                        </div>
                    </div>
                </div>-->
            </div>


            
            <div class="row">
                <div class="col-md-12">
                    <div class="top-products__area bg-style">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h2><?php echo e(__('Top Courses')); ?></h2>
                            <a href="<?php echo e(route('admin.course.index')); ?>"><button class="btn bg-primary"><?php echo e(__('View All')); ?></button></a>
                        </div>
                        <div class="top-products__table">
                            <table class="table-style">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('Course')); ?></th>
                                    <th><?php echo e(__('Instructor Name')); ?></th>
                                    <th><?php echo e(__('Price')); ?></th>
                                    <th><?php echo e(__('Total')); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $total_ten_courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><a href="<?php echo e(route('admin.course.view', $course->uuid)); ?>"><?php echo e(Str::limit($course->title, 90)); ?></a></td>
                                        <td><?php echo e(@$course->instructor->name); ?></td>
                                        <td><?php echo e($course->price); ?></td>
                                        <td><?php echo e($course->totalOrder); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--
                <div class="col-md-6">
                    <div class="top-products__area bg-style">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h2><?php echo e(__('Requested Withdrawal')); ?></h2>
                            <a href="<?php echo e(route('payout.new-withdraw')); ?>"><button class="btn bg-primary"><?php echo e(__('View All')); ?></button></a>
                        </div>
                        <div class="top-products__table">
                            <table class="table-style">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('Instructor')); ?></th>
                                    <th><?php echo e(__('Payment Method')); ?></th>
                                    <th><?php echo e(__('Request Date')); ?></th>
                                    <th><?php echo e(__('Amount')); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $withdraws; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $withdraw): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td>
                                            <div class="finance-table-inner-item my-2">
                                                <span class="fw-bold mr-1"><?php echo e(__('Name')); ?></span>: <?php echo e($withdraw->user->student->name ?? $withdraw->user->instructor->name); ?>

                                            </div>
                                            <div class="finance-table-inner-item my-2">
                                                <span class="fw-bold mr-1"><?php echo e(__('Phone')); ?></span>: <?php echo e($withdraw->user->student->phone_number ?? $withdraw->user->instructor->phone_number); ?>

                                            </div>
                                        </td>
                                        <td><?php if($withdraw->payment_method == 'paypal'): ?>
                                                <div class="finance-table-inner-item my-2">
                                                    <span class="fw-bold mr-1"><?php echo e(__('Payment Method')); ?></span>: PayPal
                                                </div>
                                                <div class="finance-table-inner-item my-2">
                                                    <span class="fw-bold mr-1"><?php echo e(__('Email')); ?></span>: <?php echo e($withdraw->user->paypal ? $withdraw->user->paypal->email : ''); ?>

                                                </div>
                                            <?php endif; ?>

                                            <?php if($withdraw->payment_method == 'card'): ?>
                                                <div class="finance-table-inner-item my-2">
                                                    <span class="fw-bold mr-1"><?php echo e(__('Payment Method')); ?></span>: Card
                                                </div>
                                                <?php if($withdraw->user->card): ?>
                                                    <div class="finance-table-inner-item my-2">
                                                        <span class="fw-bold mr-1"><?php echo e(__('Card Number')); ?></span>: <?php echo e($withdraw->user->card->card_number); ?>

                                                    </div>
                                                    <div class="finance-table-inner-item my-2">
                                                        <span class="fw-bold mr-1"><?php echo e(__('Car Holder')); ?></span>: <?php echo e($withdraw->user->card->card_holder_name); ?>

                                                    </div>
                                                    <div class="finance-table-inner-item my-2">
                                                        <span class="fw-bold mr-1"><?php echo e(__('Date')); ?></span>: <?php echo e($withdraw->user->card->month); ?>/<?php echo e($withdraw->user->card->year); ?>

                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($withdraw->created_at->format('d M Y')); ?></td>
                                        <td> <?php if(get_currency_placement() == 'after'): ?>
                                                <?php echo e($withdraw->amount); ?> <?php echo e(get_currency_symbol()); ?>

                                            <?php else: ?>
                                                <?php echo e(get_currency_symbol()); ?> <?php echo e($withdraw->amount); ?>

                                            <?php endif; ?> </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="2"><?php echo e(__('No Requested Found')); ?></td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                -->
            </div>

            
          
        </div>
    </div>






                  







<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jsrsasign/10.5.24/jsrsasign-all-min.js"></script>

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
                url: '<?php echo e(route("admin.dashboard")); ?>',
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


    <script>

        'use strict'



    //     function generateSignature(apiKey, sessionName, sessionKey = null, role= null,apiSecret = null) {
    //     const iat = Math.floor(Date.now() / 1000)
    //     const exp = iat + 60 * 60 * 2
    //     const oHeader = { alg: 'HS256', typ: 'JWT' }
    //     const oPayload = {
    //         app_key: apiKey,
    //         role_type: role,
    //         tpc: sessionName,
    //         version : 1,
    //         iat: iat,
    //         exp: exp,
    //         session_key: sessionKey
    //     };
    //     const sHeader = JSON.stringify(oHeader);
    //     const sPayload = JSON.stringify(oPayload);
    //     const sJWT = KJUR.jws.JWS.sign('HS256', sHeader, sPayload, apiSecret);
    //     return sJWT;
    // }



        // Month
        var options = {
            series: [{
                name: 'Total Enroll students',
                data: <?php echo json_encode($totalMonthlyEnroll, 15, 512) ?>
            }],
            chart: {
                height: 350,
                type: 'area'
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },
            xaxis: {
                type: 'year',
                categories: <?php echo json_encode($totalMonths, 15, 512) ?>
            },
            tooltip: {
                x: {
                    format: 'dd/MM/yy HH:mm'
                },
            },
        };

        var chart = new ApexCharts(document.querySelector("#chartMonth"), options);
        chart.render();

        // Year
        var options = {
            series: [{
                name: 'Total enroll students',
                data: <?php echo json_encode($totalYearlyEnroll, 15, 512) ?>
            }],
            chart: {
                height: 350,
                type: 'area'
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },
            xaxis: {
                type: 'year',
                categories: <?php echo json_encode($totalYears, 15, 512) ?>
            },
            tooltip: {
                x: {
                    format: 'dd/MM/yy HH:mm'
                },
            },
        };

        var chart = new ApexCharts(document.querySelector("#chartYear"), options);
        chart.render();

        // var options = {
        //     series: <?php echo json_encode(@$allPercentage, 15, 512) ?>,
        //     chart: {
        //         type: 'donut',
        //     },
        //     responsive: [{
        //         breakpoint: 480,
        //         options: {
        //             chart: {
        //                 width: 200
        //             },
        //             legend: {
        //                 position: 'bottom'
        //             }
        //         }
        //     },
        //     ],
        //     labels: <?php echo json_encode(@$allName, 15, 512) ?>
        // };

        // var chart = new ApexCharts(document.querySelector("#topSellerChart"), options);
        // chart.render();






  // Overdue buckets (ApexCharts donut)
(function() {
    
    const overdueBuckets = <?php echo json_encode($overdue_buckets, 15, 512) ?>;
     console.log(overdueBuckets)
    const overdueLabels = ['1-7 days', '8-15 days', '16-30 days', '30+ days'];
    const overdueData = [
        overdueBuckets['1_7']?.count || 0,
        overdueBuckets['8_15']?.count || 0,
        overdueBuckets['16_30']?.count || 0,
        overdueBuckets['30_plus']?.count || 0
    ];

    const overdueOptions = {
        series: overdueData,
        chart: {
            type: 'donut',
            height: 250
        },
        labels: overdueLabels,
        colors: ['#4e73df', '#1cc88a', '#f6c23e', '#e74a3b'],
        legend: {
            position: 'bottom'
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val.toFixed(1) + "%";
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + " students";
                }
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: { width: 200 },
                legend: { position: 'bottom' }
            }
        }]
    };

   
    // ✅ Correct rendering
    const overdueChartEl = document.querySelector("#overdueBucketsChart");
  
    if (overdueChartEl) {
        
        const overdueChart = new ApexCharts(overdueChartEl, overdueOptions);
        overdueChart.render();
    } else {
      
        console.warn("Overdue chart element not found!");
    }
})();
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/dashboard.blade.php ENDPATH**/ ?>
@extends('layouts.admin')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2>{{ __('Dashboard') }} </h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">{{ get_option('app_name') }}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ __('Dashboard') }}</li>
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
                            <img src="{{ asset('admin') }}/images/admin-dashboard-icons/principal.png" alt="icon">
                        </div>
                     <div class="status__box__text">
                            <h2 class="color-purple">{{ $total_admins }}</h2>
                            <h3>{{ __('Total Admin') }}</h3>
                        </div>
                    </div>
                </div>-->
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="{{ asset('admin') }}/images/admin-dashboard-icons/laptop.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-purple">{{ $total_instructors }}</h2>
                            <h3>{{ __('Total Instructors') }}</h3>
                        </div>
                    </div>
                </div>

           

                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="{{ asset('admin') }}/images/admin-dashboard-icons/study.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-blue">{{ $total_students }}</h2>
                            <h3>{{ __('Total Students') }}</h3>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="{{ asset('admin') }}/images/admin-dashboard-icons/test.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-green">{{ $total_general_courses }}</h2>
                            <h3>{{ __('Total General Courses') }}</h3>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="{{ asset('admin') }}/images/admin-dashboard-icons/checklist.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-red">{{ $total_lessons }}</h2>
                            <h3>{{ __('Total Lessons') }}</h3>
                        </div>
                    </div>
                </div>



                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class=" status__box__v3 bg-style">
                        <div class="">
                            <h4>{{__('Zoom')}}</h4>
                        </div>
                        @if ($zoomErrorMessage != null)
                            <br>
                            <br>
                            <div class="">
                                <h3 class="color-red">{{ $zoomErrorMessage }}</h3>
                            </div>
                        @else
                               <div align="left" style="margin-top:5px" class="">
                                    <h3>{{ __('Sessions') }}: <span class="color-red">{{ $zoomResultArray['Total_Sessions'] }}</span></h3>   
                                </div>
                                <div align="left" style="margin-top:5px" class="">
                                    <h3>{{ __('Minutes') }}: <span class="color-red">{{ $zoomResultArray['Total_Minutes'] }}</span></h3>   
                                </div>

                                <div class="slider-container-zoom">
                                    <div class="slider-progress-zoom"></div>
                                    <div class="slider-text-zoom">
                                        {{ round($zoomSliderPercentage, 2) }}%
                                    </div>
                                </div>

                        @endif
                     
                    </div>
                </div>









             

                    <!-- START: Overdue Fee Tracker -->
            <div class="row">
                <div class="col-md-12">
                    <div class="top-products__area bg-style">
                        <!-- Header -->
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h2>{{ __('Overdue Fee Tracker') }}</h2>
                            <div>
                                <a href="{{ route('student_payment.donation') }}" class="btn btn-sm btn-outline-primary">
                                    {{ __('Pending Donations') }}
                                </a>
                                <a href="{{ route('student_payment.completed_donation') }}" class="btn btn-sm btn-outline-primary">
                                    {{ __('Paid Donations') }}
                                </a>
                                <a href="{{ route('student_payment.pending') }}" class="btn btn-sm btn-outline-primary">
                                    {{ __('Pending/Overdue') }}
                                </a>
                                <a href="{{ route('student_payment.completed') }}" class="btn btn-sm btn-outline-primary">
                                    {{ __('Paid') }}
                                </a>
                                <span class="muted mr-3">{{ __('Total Outstanding:') }}</span>
                                <strong>
                                    @if(get_currency_placement() == 'after')
                                        {{ number_format($overdue_total_outstanding, 0) }} {{ get_currency_symbol() }}
                                    @else
                                        {{ get_currency_symbol() }} {{ number_format($overdue_total_outstanding, 0) }}
                                    @endif
                                </strong>
                            </div>
                        </div>
                    
                        <!-- KPI boxes + Chart -->
                        <div class="row mb-3 align-items-stretch">
                            
                            <!-- Students with Dues -->
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="status__box status__box__v3 bg-style h-100 d-flex align-items-center justify-content-center text-center">
                                    <div class="status__box__text">
                                        <h2 class="color-blue mb-1">{{ $overdue_students_with_dues }}</h2>
                                        <h3 class="mb-0">{{ __('Students with Dues') }}</h3>
                                    </div>
                                </div>
                            </div>
                        
                            <!-- Critical Overdue -->
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="status__box status__box__v3 bg-style h-100 d-flex align-items-center justify-content-center text-center">
                                    <div class="status__box__text">
                                        <h2 class="color-red mb-1">{{ $overdue_critical_count }}</h2>
                                        <h3 class="mb-0">{{ __('Critical Overdue (30+ days)') }}</h3>
                                        <div class="muted">
                                            @if(get_currency_placement() == 'after')
                                                {{ number_format($overdue_critical_amount, 0) }} {{ get_currency_symbol() }}
                                            @else
                                                {{ get_currency_symbol() }} {{ number_format($overdue_critical_amount, 0) }}
                                            @endif
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

                        {{-- <table class="table-style w-100">
                            <thead>
                                <tr>
                                    <th>{{ __('Student') }}</th>
                                    <th>{{ __('Course') }}</th>
                                    <th>{{ __('Issue Date') }}</th>
                                    <th>{{ __('Days Overdue') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($overdue_rows as $row)
                                    <tr>
                                        <td>{{ $row['student_name'] }}</td>
                                        <td>{{ $row['course_name'] }}</td>
                                        <td>{{ $row['issue_date'] }}</td>
                                        <td>{{ $row['days_overdue'] }}</td>
                                        <td>
                                            @if(get_currency_placement() == 'after')
                                                {{ number_format($row['amount'], 0) }} {{ get_currency_symbol() }}
                                            @else
                                                {{ get_currency_symbol() }} {{ number_format($row['amount'], 0) }}
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('student.view', $row['student_uuid']) }}" class="btn btn-sm btn-outline-primary">
                                                {{ __('View') }}
                                            </a>
                                            <a href="{{route('student_payment.edit_payment_2', $row['id'])}}" class="btn-action mr-30" title="Edit Details">
                                                <img src="{{asset('admin/images/icons/edit-2.svg')}}" alt="edit">
                                            </a>
                                            <button class="ms-3">
                                                <span data-formid="delete_row_form_{{$row['id']}}" class="deleteItem">
                                                    <img src="{{asset('admin/images/icons/trash-2.svg')}}" alt="trash">
                                                </span>
                                            </button>
                                        
                                            <form action="{{route('student_payment.delete_payment', $row['id'])}}" method="post" id="delete_row_form_{{ $row['id'] }}">
                                                {{ method_field('DELETE') }}
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            </form>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">{{ __('No overdue payments found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table> --}}
                    </div>
                </div>
            </div>
              <!-- END: Overdue Fee Tracker -->












                <hr>
                <div class="breadcrumb__title">
                    {{-- <h2>{{ __('Quran Education Online') }} </h2> --}}
                    <h2 style="color:black">Quran Education Online</h2>
                    <br>
                </div>
       
            
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="{{ asset('admin') }}/images/admin-dashboard-icons/laptop.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-purple">{{ $total_coordinators }}</h2>
                            <h3>{{ __('Total Coordinators') }}</h3>
                        </div>
                    </div>
                </div>


                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="{{ asset('admin') }}/images/admin-dashboard-icons/test.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-green">{{ $total_standard_courses }}</h2>
                            <h3>{{ __('Total Live Courses') }}</h3>
                        </div>
                    </div>
                </div>


                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="{{ asset('admin') }}/images/admin-dashboard-icons/checklist.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-green">{{ countActiveTodayClasses() }}</h2>
                            <h3>{{ __('Today Classes') }}</h3>
                        </div>
                    </div>
                </div>


                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="{{ asset('admin') }}/images/admin-dashboard-icons/checklist.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-green">{{ countActiveScheduledClasses() }}</h2>
                            <h3>{{ __('Scheduled Classes') }}</h3>
                        </div>
                    </div>
                </div>


                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="{{ asset('admin') }}/images/admin-dashboard-icons/checklist.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-green">{{ countActiveCompletedClasses() }}</h2>
                            <h3>{{ __('Completed Classes') }}</h3>
                        </div>
                    </div>
                </div>


                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="{{ asset('admin') }}/images/admin-dashboard-icons/checklist.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-green">{{ countActiveMissedClasses() }}</h2>
                            <h3>{{ __('Missed Classes') }}</h3>
                        </div>
                    </div>
                </div>

                <hr>
                <!--
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">

                        <div class="status__box__img">
                            <img src="{{ asset('admin') }}/images/admin-dashboard-icons/test-1.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-blue">{{ $total_active_courses }}</h2>
                            <h3>{{ __('Active Courses') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="{{ asset('admin') }}/images/admin-dashboard-icons/download.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-purple">{{ $total_pending_courses }}</h2>
                            <h3>{{ __('Pending Courses') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="{{ asset('admin') }}/images/admin-dashboard-icons/withdraw.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-purple">{{ $total_free_courses }}</h2>
                            <h3>{{ __('Free Courses') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="{{ asset('admin') }}/images/admin-dashboard-icons/elearning.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-green">{{ $total_paid_courses }}</h2>
                            <h3>{{ __('Paid Courses') }}</h3>
                        </div>
                    </div>
                </div>
-->
                
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="{{ asset('admin') }}/images/admin-dashboard-icons/website.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-red">{{ $total_lectures }}</h2>
                            <h3>{{ __('Total Lectures') }}</h3>
                        </div>
                    </div>
                </div>
<!--
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="{{ asset('admin') }}/images/admin-dashboard-icons/blogger.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-yellow">{{ $total_blogs }}</h2>
                            <h3>{{ __('Total Blogs') }}</h3>
                        </div>
                    </div>
                </div>
-->
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="{{ asset('admin') }}/images/admin-dashboard-icons/save-money.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-green">

                                @if(get_currency_placement() == 'after')
                                    {{ $total_paid_sales }} {{ get_currency_symbol() }}
                                @else
                                    {{ get_currency_symbol() }} {{ $total_paid_sales }}
                                @endif
                            </h2>
                            <h3>{{ __('Total Paid Sales') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="{{ asset('admin') }}/images/admin-dashboard-icons/paying.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-red">{{ $total_free_sales }}</h2>
                            <h3>{{ __('Total Free Sales') }}</h3>
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

                                @if(get_currency_placement() == 'after')
                                    {{ $total_platform_charge }} {{ get_currency_symbol() }}
                                @else
                                    {{ get_currency_symbol() }} {{ $total_platform_charge }}
                                @endif
                            </h2>
                            <h3>{{ __('Total Platform Charge') }}</h3>
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
                                @if(get_currency_placement() == 'after')
                                    {{ $total_platform_charge_this_month }} {{ get_currency_symbol() }}
                                @else
                                    {{ get_currency_symbol() }} {{ $total_platform_charge_this_month }}
                                @endif

                            </h2>
                            <h3>{{ __('Total Platform Charge (Current Month)') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="{{ asset('admin') }}/images/admin-dashboard-icons/discount.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-purple">
                                @if(get_currency_placement() == 'after')
                                    {{ $total_admin_commission }} {{ get_currency_symbol() }}
                                @else
                                    {{ get_currency_symbol() }} {{ $total_admin_commission }}
                                @endif

                            </h2>
                            <h3>{{ __('Total Sell Commission') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="{{ asset('admin') }}/images/admin-dashboard-icons/shop.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-red">
                                @if(get_currency_placement() == 'after')
                                    {{ $total_admin_commission_this_month }} {{ get_currency_symbol() }}
                                @else
                                    {{ get_currency_symbol() }} {{ $total_admin_commission_this_month }}
                                @endif</h2>
                            <h3>{{ __('Total Sell Commission (Current Month)') }}</h3>
                        </div>
                    </div>
                </div>-->
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="{{ asset('admin') }}/images/admin-dashboard-icons/money.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-blue"> @if(get_currency_placement() == 'after')
                                    {{ $total_revenue }} {{ get_currency_symbol() }}
                                @else
                                    {{ get_currency_symbol() }} {{ $total_revenue }}
                                @endif</h2>
                            <h3>{{ __('Total Revenue') }}</h3>
                        </div>
                    </div>
                </div>
                <!--
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="{{ asset('admin') }}/images/admin-dashboard-icons/economy.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-yellow">
                                @if(get_currency_placement() == 'after')
                                    {{ $total_new_withdraws }} {{ get_currency_symbol() }}
                                @else
                                    {{ get_currency_symbol() }} {{ $total_new_withdraws }}
                                @endif
                                </h2>
                            <h3>{{ __('Total Request Withdraw') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="status__box status__box__v3 bg-style">
                        <div class="status__box__img">
                            <img src="{{ asset('admin') }}/images/admin-dashboard-icons/save-money.png" alt="icon">
                        </div>
                        <div class="status__box__text">
                            <h2 class="color-green"> @if(get_currency_placement() == 'after')
                                    {{ $total_complete_withdraws }} {{ get_currency_symbol() }}
                                @else
                                    {{ get_currency_symbol() }} {{ $total_complete_withdraws }}
                                @endif</h2>
                            <h3>{{ __('Total Complete Withdraw') }}</h3>
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
                                    <h2>{{ __('Enrollment') }}</h2>
                                </div>
                            </div>
                            <div class="revenue__chart-v2__top__right">
                                <div class="revenue__chart-v2__list">
                                    <nav>
                                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                            <button class="nav-link" id="nav-two-tab" data-bs-toggle="tab" data-bs-target="#nav-two" type="button" role="tab"
                                                    aria-controls="nav-two" aria-selected="false">
                                                {{ __('Month') }}
                                            </button>
                                            <button class="nav-link active" id="nav-three-tab" data-bs-toggle="tab" data-bs-target="#nav-three" type="button" role="tab"
                                                    aria-controls="nav-three" aria-selected="false">
                                                {{ __('Year') }}
                                            </button>
                                        </div>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        <div class="total-profit">
                            <h2>
                                {{ __('Total Enrollment') }} <span>{{ $total_enrolments }}</span>
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
                            <h2>{{ __('Top Seller') }}</h2>
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
                            <h2>{{ __('Top Courses') }}</h2>
                            <a href="{{ route('admin.course.index') }}"><button class="btn bg-primary">{{ __('View All') }}</button></a>
                        </div>
                        <div class="top-products__table">
                            <table class="table-style">
                                <thead>
                                <tr>
                                    <th>{{ __('Course') }}</th>
                                    <th>{{ __('Instructor Name') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Total') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($total_ten_courses as $course)
                                    <tr>
                                        <td><a href="{{ route('admin.course.view', $course->uuid) }}">{{ Str::limit($course->title, 90) }}</a></td>
                                        <td>{{ @$course->instructor->name }}</td>
                                        <td>{{ $course->price }}</td>
                                        <td>{{ $course->totalOrder }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--
                <div class="col-md-6">
                    <div class="top-products__area bg-style">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h2>{{ __('Requested Withdrawal') }}</h2>
                            <a href="{{ route('payout.new-withdraw') }}"><button class="btn bg-primary">{{ __('View All') }}</button></a>
                        </div>
                        <div class="top-products__table">
                            <table class="table-style">
                                <thead>
                                <tr>
                                    <th>{{__('Instructor')}}</th>
                                    <th>{{__('Payment Method')}}</th>
                                    <th>{{__('Request Date')}}</th>
                                    <th>{{__('Amount')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($withdraws as $withdraw)
                                    <tr>
                                        <td>
                                            <div class="finance-table-inner-item my-2">
                                                <span class="fw-bold mr-1">{{__('Name')}}</span>: {{$withdraw->user->student->name ?? $withdraw->user->instructor->name}}
                                            </div>
                                            <div class="finance-table-inner-item my-2">
                                                <span class="fw-bold mr-1">{{__('Phone')}}</span>: {{$withdraw->user->student->phone_number ?? $withdraw->user->instructor->phone_number}}
                                            </div>
                                        </td>
                                        <td>@if($withdraw->payment_method == 'paypal')
                                                <div class="finance-table-inner-item my-2">
                                                    <span class="fw-bold mr-1">{{__('Payment Method')}}</span>: PayPal
                                                </div>
                                                <div class="finance-table-inner-item my-2">
                                                    <span class="fw-bold mr-1">{{__('Email')}}</span>: {{$withdraw->user->paypal ? $withdraw->user->paypal->email : '' }}
                                                </div>
                                            @endif

                                            @if($withdraw->payment_method == 'card')
                                                <div class="finance-table-inner-item my-2">
                                                    <span class="fw-bold mr-1">{{__('Payment Method')}}</span>: Card
                                                </div>
                                                @if($withdraw->user->card)
                                                    <div class="finance-table-inner-item my-2">
                                                        <span class="fw-bold mr-1">{{__('Card Number')}}</span>: {{$withdraw->user->card->card_number }}
                                                    </div>
                                                    <div class="finance-table-inner-item my-2">
                                                        <span class="fw-bold mr-1">{{__('Car Holder')}}</span>: {{$withdraw->user->card->card_holder_name }}
                                                    </div>
                                                    <div class="finance-table-inner-item my-2">
                                                        <span class="fw-bold mr-1">{{__('Date')}}</span>: {{$withdraw->user->card->month }}/{{$withdraw->user->card->year }}
                                                    </div>
                                                @endif
                                            @endif
                                        </td>
                                        <td>{{$withdraw->created_at->format('d M Y')}}</td>
                                        <td> @if(get_currency_placement() == 'after')
                                                {{$withdraw->amount}} {{ get_currency_symbol() }}
                                            @else
                                                {{ get_currency_symbol() }} {{$withdraw->amount}}
                                            @endif </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2">{{ __('No Requested Found') }}</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                -->
            </div>

            
          
        </div>
    </div>






                  







@endsection

@push('script')
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
                url: '{{ route("admin.dashboard") }}',
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
                data: @json($totalMonthlyEnroll)
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
                categories: @json($totalMonths)
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
                data: @json($totalYearlyEnroll)
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
                categories: @json($totalYears)
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
        //     series: @json(@$allPercentage),
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
        //     labels: @json(@$allName)
        // };

        // var chart = new ApexCharts(document.querySelector("#topSellerChart"), options);
        // chart.render();






  // Overdue buckets (ApexCharts donut)
(function() {
    
    const overdueBuckets = @json($overdue_buckets);
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
@endpush

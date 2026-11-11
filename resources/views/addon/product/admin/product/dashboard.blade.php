@extends('layouts.instructor')
<link rel="stylesheet" href="{{ asset('addon/product/css/ecommerce-product.css') }}">
@section('breadcrumb')
<div class="page-banner-content text-center">
    <h3 class="page-banner-heading text-white pb-15"> {{__('Product Dashboard')}} </h3>

    <!-- Breadcrumb Start-->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb justify-content-center">
            <li class="breadcrumb-item font-14"><a href="{{route('instructor.dashboard')}}">{{__('Dashboard')}}</a></li>
            <li class="breadcrumb-item font-14 active" aria-current="page">{{__('Manage Product')}}</li>
        </ol>
    </nav>
</div>
@endsection

@section('content2')
<div class="dashboard-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h6 class="dashboard-title"> {{ __('Dashboard') }}</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                <div class="sel-analisis">
                    <div class="content-sel">
                        <p>{{ __('Pending Order') }}</p>
                        <h4>{{ $pendingOrder }}</h4>
                        <input type="hidden" id="pendingOrderCount" value="{{ $pendingOrder }}">
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                            <path
                                d="M34.6875 30.9375H7.8125V6.5625C7.8125 6.39062 7.67188 6.25 7.5 6.25H5.3125C5.14062 6.25 5 6.39062 5 6.5625V33.4375C5 33.6094 5.14062 33.75 5.3125 33.75H34.6875C34.8594 33.75 35 33.6094 35 33.4375V31.25C35 31.0781 34.8594 30.9375 34.6875 30.9375ZM11.25 27.8125H13.4375C13.6094 27.8125 13.75 27.6719 13.75 27.5V21.875C13.75 21.7031 13.6094 21.5625 13.4375 21.5625H11.25C11.0781 21.5625 10.9375 21.7031 10.9375 21.875V27.5C10.9375 27.6719 11.0781 27.8125 11.25 27.8125ZM17.1875 27.8125H19.375C19.5469 27.8125 19.6875 27.6719 19.6875 27.5V15C19.6875 14.8281 19.5469 14.6875 19.375 14.6875H17.1875C17.0156 14.6875 16.875 14.8281 16.875 15V27.5C16.875 27.6719 17.0156 27.8125 17.1875 27.8125ZM23.125 27.8125H25.3125C25.4844 27.8125 25.625 27.6719 25.625 27.5V18.0469C25.625 17.875 25.4844 17.7344 25.3125 17.7344H23.125C22.9531 17.7344 22.8125 17.875 22.8125 18.0469V27.5C22.8125 27.6719 22.9531 27.8125 23.125 27.8125ZM29.0625 27.8125H31.25C31.4219 27.8125 31.5625 27.6719 31.5625 27.5V11.875C31.5625 11.7031 31.4219 11.5625 31.25 11.5625H29.0625C28.8906 11.5625 28.75 11.7031 28.75 11.875V27.5C28.75 27.6719 28.8906 27.8125 29.0625 27.8125Z"
                                fill="#754FFE" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="sel-analisis">
                    <div class="content-sel">
                        <p>{{ __('Processing/Shipped') }}</p>
                        <h4>{{ $processingOrder }}</h4>
                        <input type="hidden" id="processingOrderCount" value="{{ $processingOrder }}">
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                            <path
                                d="M34.6875 30.9375H7.8125V6.5625C7.8125 6.39062 7.67188 6.25 7.5 6.25H5.3125C5.14062 6.25 5 6.39062 5 6.5625V33.4375C5 33.6094 5.14062 33.75 5.3125 33.75H34.6875C34.8594 33.75 35 33.6094 35 33.4375V31.25C35 31.0781 34.8594 30.9375 34.6875 30.9375ZM11.25 27.8125H13.4375C13.6094 27.8125 13.75 27.6719 13.75 27.5V21.875C13.75 21.7031 13.6094 21.5625 13.4375 21.5625H11.25C11.0781 21.5625 10.9375 21.7031 10.9375 21.875V27.5C10.9375 27.6719 11.0781 27.8125 11.25 27.8125ZM17.1875 27.8125H19.375C19.5469 27.8125 19.6875 27.6719 19.6875 27.5V15C19.6875 14.8281 19.5469 14.6875 19.375 14.6875H17.1875C17.0156 14.6875 16.875 14.8281 16.875 15V27.5C16.875 27.6719 17.0156 27.8125 17.1875 27.8125ZM23.125 27.8125H25.3125C25.4844 27.8125 25.625 27.6719 25.625 27.5V18.0469C25.625 17.875 25.4844 17.7344 25.3125 17.7344H23.125C22.9531 17.7344 22.8125 17.875 22.8125 18.0469V27.5C22.8125 27.6719 22.9531 27.8125 23.125 27.8125ZM29.0625 27.8125H31.25C31.4219 27.8125 31.5625 27.6719 31.5625 27.5V11.875C31.5625 11.7031 31.4219 11.5625 31.25 11.5625H29.0625C28.8906 11.5625 28.75 11.7031 28.75 11.875V27.5C28.75 27.6719 28.8906 27.8125 29.0625 27.8125Z"
                                fill="#754FFE" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="sel-analisis">
                    <div class="content-sel">
                        <p>{{ __('Delevered Order') }}</p>
                        <h4>{{ $deliveredOrder }}</h4>
                        <input type="hidden" id="deliveredOrderCount" value="{{ $deliveredOrder }}">
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                            <path
                                d="M34.6875 30.9375H7.8125V6.5625C7.8125 6.39062 7.67188 6.25 7.5 6.25H5.3125C5.14062 6.25 5 6.39062 5 6.5625V33.4375C5 33.6094 5.14062 33.75 5.3125 33.75H34.6875C34.8594 33.75 35 33.6094 35 33.4375V31.25C35 31.0781 34.8594 30.9375 34.6875 30.9375ZM11.25 27.8125H13.4375C13.6094 27.8125 13.75 27.6719 13.75 27.5V21.875C13.75 21.7031 13.6094 21.5625 13.4375 21.5625H11.25C11.0781 21.5625 10.9375 21.7031 10.9375 21.875V27.5C10.9375 27.6719 11.0781 27.8125 11.25 27.8125ZM17.1875 27.8125H19.375C19.5469 27.8125 19.6875 27.6719 19.6875 27.5V15C19.6875 14.8281 19.5469 14.6875 19.375 14.6875H17.1875C17.0156 14.6875 16.875 14.8281 16.875 15V27.5C16.875 27.6719 17.0156 27.8125 17.1875 27.8125ZM23.125 27.8125H25.3125C25.4844 27.8125 25.625 27.6719 25.625 27.5V18.0469C25.625 17.875 25.4844 17.7344 25.3125 17.7344H23.125C22.9531 17.7344 22.8125 17.875 22.8125 18.0469V27.5C22.8125 27.6719 22.9531 27.8125 23.125 27.8125ZM29.0625 27.8125H31.25C31.4219 27.8125 31.5625 27.6719 31.5625 27.5V11.875C31.5625 11.7031 31.4219 11.5625 31.25 11.5625H29.0625C28.8906 11.5625 28.75 11.7031 28.75 11.875V27.5C28.75 27.6719 28.8906 27.8125 29.0625 27.8125Z"
                                fill="#754FFE" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="sel-analisis">
                    <div class="content-sel">
                        <p>{{ __('Cancelled Order') }}</p>
                        <h4>{{ $cancelledOrder }}</h4>
                        <input type="hidden" id="cancelledOrderCount" value="{{ $cancelledOrder }}">
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                            <path
                                d="M34.6875 30.9375H7.8125V6.5625C7.8125 6.39062 7.67188 6.25 7.5 6.25H5.3125C5.14062 6.25 5 6.39062 5 6.5625V33.4375C5 33.6094 5.14062 33.75 5.3125 33.75H34.6875C34.8594 33.75 35 33.6094 35 33.4375V31.25C35 31.0781 34.8594 30.9375 34.6875 30.9375ZM11.25 27.8125H13.4375C13.6094 27.8125 13.75 27.6719 13.75 27.5V21.875C13.75 21.7031 13.6094 21.5625 13.4375 21.5625H11.25C11.0781 21.5625 10.9375 21.7031 10.9375 21.875V27.5C10.9375 27.6719 11.0781 27.8125 11.25 27.8125ZM17.1875 27.8125H19.375C19.5469 27.8125 19.6875 27.6719 19.6875 27.5V15C19.6875 14.8281 19.5469 14.6875 19.375 14.6875H17.1875C17.0156 14.6875 16.875 14.8281 16.875 15V27.5C16.875 27.6719 17.0156 27.8125 17.1875 27.8125ZM23.125 27.8125H25.3125C25.4844 27.8125 25.625 27.6719 25.625 27.5V18.0469C25.625 17.875 25.4844 17.7344 25.3125 17.7344H23.125C22.9531 17.7344 22.8125 17.875 22.8125 18.0469V27.5C22.8125 27.6719 22.9531 27.8125 23.125 27.8125ZM29.0625 27.8125H31.25C31.4219 27.8125 31.5625 27.6719 31.5625 27.5V11.875C31.5625 11.7031 31.4219 11.5625 31.25 11.5625H29.0625C28.8906 11.5625 28.75 11.7031 28.75 11.875V27.5C28.75 27.6719 28.8906 27.8125 29.0625 27.8125Z"
                                fill="#754FFE" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="sel-analisis">
                    <div class="content-sel">
                        <p>{{ now()->format('F') }} {{ __('Sale') }}</p>
                        <h4>{{ $thisMonthSale }}</h4>
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                            <path
                                d="M34.6875 30.9375H7.8125V6.5625C7.8125 6.39062 7.67188 6.25 7.5 6.25H5.3125C5.14062 6.25 5 6.39062 5 6.5625V33.4375C5 33.6094 5.14062 33.75 5.3125 33.75H34.6875C34.8594 33.75 35 33.6094 35 33.4375V31.25C35 31.0781 34.8594 30.9375 34.6875 30.9375ZM11.25 27.8125H13.4375C13.6094 27.8125 13.75 27.6719 13.75 27.5V21.875C13.75 21.7031 13.6094 21.5625 13.4375 21.5625H11.25C11.0781 21.5625 10.9375 21.7031 10.9375 21.875V27.5C10.9375 27.6719 11.0781 27.8125 11.25 27.8125ZM17.1875 27.8125H19.375C19.5469 27.8125 19.6875 27.6719 19.6875 27.5V15C19.6875 14.8281 19.5469 14.6875 19.375 14.6875H17.1875C17.0156 14.6875 16.875 14.8281 16.875 15V27.5C16.875 27.6719 17.0156 27.8125 17.1875 27.8125ZM23.125 27.8125H25.3125C25.4844 27.8125 25.625 27.6719 25.625 27.5V18.0469C25.625 17.875 25.4844 17.7344 25.3125 17.7344H23.125C22.9531 17.7344 22.8125 17.875 22.8125 18.0469V27.5C22.8125 27.6719 22.9531 27.8125 23.125 27.8125ZM29.0625 27.8125H31.25C31.4219 27.8125 31.5625 27.6719 31.5625 27.5V11.875C31.5625 11.7031 31.4219 11.5625 31.25 11.5625H29.0625C28.8906 11.5625 28.75 11.7031 28.75 11.875V27.5C28.75 27.6719 28.8906 27.8125 29.0625 27.8125Z"
                                fill="#754FFE" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="sel-analisis">
                    <div class="content-sel">
                        <p>{{ now()->format('F') }} {{ __('Earning') }}</p>
                        <h4>{{ $thisMonthEarning }}</h4>
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                            <path
                                d="M34.6875 30.9375H7.8125V6.5625C7.8125 6.39062 7.67188 6.25 7.5 6.25H5.3125C5.14062 6.25 5 6.39062 5 6.5625V33.4375C5 33.6094 5.14062 33.75 5.3125 33.75H34.6875C34.8594 33.75 35 33.6094 35 33.4375V31.25C35 31.0781 34.8594 30.9375 34.6875 30.9375ZM11.25 27.8125H13.4375C13.6094 27.8125 13.75 27.6719 13.75 27.5V21.875C13.75 21.7031 13.6094 21.5625 13.4375 21.5625H11.25C11.0781 21.5625 10.9375 21.7031 10.9375 21.875V27.5C10.9375 27.6719 11.0781 27.8125 11.25 27.8125ZM17.1875 27.8125H19.375C19.5469 27.8125 19.6875 27.6719 19.6875 27.5V15C19.6875 14.8281 19.5469 14.6875 19.375 14.6875H17.1875C17.0156 14.6875 16.875 14.8281 16.875 15V27.5C16.875 27.6719 17.0156 27.8125 17.1875 27.8125ZM23.125 27.8125H25.3125C25.4844 27.8125 25.625 27.6719 25.625 27.5V18.0469C25.625 17.875 25.4844 17.7344 25.3125 17.7344H23.125C22.9531 17.7344 22.8125 17.875 22.8125 18.0469V27.5C22.8125 27.6719 22.9531 27.8125 23.125 27.8125ZM29.0625 27.8125H31.25C31.4219 27.8125 31.5625 27.6719 31.5625 27.5V11.875C31.5625 11.7031 31.4219 11.5625 31.25 11.5625H29.0625C28.8906 11.5625 28.75 11.7031 28.75 11.875V27.5C28.75 27.6719 28.8906 27.8125 29.0625 27.8125Z"
                                fill="#754FFE" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="sel-analisis">
                    <div class="content-sel">
                        <p>{{ __('This Year Sale') }}</p>
                        <h4>{{ $thisYearSale }}</h4>
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                            <path
                                d="M34.6875 30.9375H7.8125V6.5625C7.8125 6.39062 7.67188 6.25 7.5 6.25H5.3125C5.14062 6.25 5 6.39062 5 6.5625V33.4375C5 33.6094 5.14062 33.75 5.3125 33.75H34.6875C34.8594 33.75 35 33.6094 35 33.4375V31.25C35 31.0781 34.8594 30.9375 34.6875 30.9375ZM11.25 27.8125H13.4375C13.6094 27.8125 13.75 27.6719 13.75 27.5V21.875C13.75 21.7031 13.6094 21.5625 13.4375 21.5625H11.25C11.0781 21.5625 10.9375 21.7031 10.9375 21.875V27.5C10.9375 27.6719 11.0781 27.8125 11.25 27.8125ZM17.1875 27.8125H19.375C19.5469 27.8125 19.6875 27.6719 19.6875 27.5V15C19.6875 14.8281 19.5469 14.6875 19.375 14.6875H17.1875C17.0156 14.6875 16.875 14.8281 16.875 15V27.5C16.875 27.6719 17.0156 27.8125 17.1875 27.8125ZM23.125 27.8125H25.3125C25.4844 27.8125 25.625 27.6719 25.625 27.5V18.0469C25.625 17.875 25.4844 17.7344 25.3125 17.7344H23.125C22.9531 17.7344 22.8125 17.875 22.8125 18.0469V27.5C22.8125 27.6719 22.9531 27.8125 23.125 27.8125ZM29.0625 27.8125H31.25C31.4219 27.8125 31.5625 27.6719 31.5625 27.5V11.875C31.5625 11.7031 31.4219 11.5625 31.25 11.5625H29.0625C28.8906 11.5625 28.75 11.7031 28.75 11.875V27.5C28.75 27.6719 28.8906 27.8125 29.0625 27.8125Z"
                                fill="#754FFE" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="sel-analisis">
                    <div class="content-sel">
                        <p>{{ __('This Year Earning') }}</p>
                        <h4>{{ $thisYearEarning }}</h4>
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                            <path
                                d="M34.6875 30.9375H7.8125V6.5625C7.8125 6.39062 7.67188 6.25 7.5 6.25H5.3125C5.14062 6.25 5 6.39062 5 6.5625V33.4375C5 33.6094 5.14062 33.75 5.3125 33.75H34.6875C34.8594 33.75 35 33.6094 35 33.4375V31.25C35 31.0781 34.8594 30.9375 34.6875 30.9375ZM11.25 27.8125H13.4375C13.6094 27.8125 13.75 27.6719 13.75 27.5V21.875C13.75 21.7031 13.6094 21.5625 13.4375 21.5625H11.25C11.0781 21.5625 10.9375 21.7031 10.9375 21.875V27.5C10.9375 27.6719 11.0781 27.8125 11.25 27.8125ZM17.1875 27.8125H19.375C19.5469 27.8125 19.6875 27.6719 19.6875 27.5V15C19.6875 14.8281 19.5469 14.6875 19.375 14.6875H17.1875C17.0156 14.6875 16.875 14.8281 16.875 15V27.5C16.875 27.6719 17.0156 27.8125 17.1875 27.8125ZM23.125 27.8125H25.3125C25.4844 27.8125 25.625 27.6719 25.625 27.5V18.0469C25.625 17.875 25.4844 17.7344 25.3125 17.7344H23.125C22.9531 17.7344 22.8125 17.875 22.8125 18.0469V27.5C22.8125 27.6719 22.9531 27.8125 23.125 27.8125ZM29.0625 27.8125H31.25C31.4219 27.8125 31.5625 27.6719 31.5625 27.5V11.875C31.5625 11.7031 31.4219 11.5625 31.25 11.5625H29.0625C28.8906 11.5625 28.75 11.7031 28.75 11.875V27.5C28.75 27.6719 28.8906 27.8125 29.0625 27.8125Z"
                                fill="#754FFE" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        {{-- chart area start --}}

        <div class="row">
            <div class="col-xl-6 col-lg-12">
                <div class="single-chart">
                    <p class="chart-ratio">{{ __("Top 5 Product") }}</p>
                    <div id="productChart"></div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-12">
                <div class="single-chart">
                    <p class="chart-ratio">{{ __("Status") }}</p>
                    <div id="deliveryStatusChart"></div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-12">
                <div class="single-chart-sel">
                    <p class="chart-ratio-sel">{{ now()->format('F') }} {{ __('Sale') }}</p>

                    <div class="chart-area">
                        <div id="thisMonthSale"></div>
                    </div>
                </div>

            </div>
            <div class="col-xl-6 col-lg-12">
                <div class="single-chart-sel">
                    <p class="chart-ratio-sel">{{ now()->format('F') }} {{ __('Earning') }}</p>

                    <div class="chart-area">
                        <div id="thisMonthEarning"></div>
                    </div>

                </div>
            </div>
        </div>

        {{-- chart area end --}}

        {{-- Product Inventory area start --}}

        <div class="row">
            <div class="col-md-12">
                <h5 class="inventory-title">{{ __('Product Inventory') }}</h5>
                <div class="menu-products table-responsive">
                    <table class="table table-size">
                        <thead>
                            <tr>
                                <th scope="col">{{ __("Image") }}</th>
                                <th scope="col">{{ __("Product") }}</th>
                                <th scope="col">{{ __("Category") }}</th>
                                <th scope="col">{{ __("Price") }}</th>
                                <th scope="col">{{ __("Type") }}</th>
                                <th scope="col">{{ __("Status") }}</th>
                                <th scope="col">{{ __("Action") }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                            <tr>
                                <td scope="row">
                                    <img class="products-img-list" src="{{getImageFile($product->thumbnail_path)}}" alt="{{ $product->title }}">
                                </td>
                                <td>{{ $product->title }}</td>
                                <td>{{ $product->category->name }}</td>
                                <td>
                                    @if(get_currency_placement() == 'after')
                                        {{$product->current_price}} {{ get_currency_symbol() }}
                                    @else
                                        {{ get_currency_symbol() }} {{$product->current_price}}
                                    @endif
                                </td>
                                <td>{{ getProductByType($product->type) }}</td>
                                <td>
                                    ({{ $product->quantity >=0 ? $product->quantity : 0 }})
                                    <button class="product-btu {{ $product->quantity < 1  ? 'out-stock-btu' : 'stock-btu' }} stock-modal-btn" data-slug="{{ $product->slug }}" >
                                        {{ $product->quantity < 1 ? __('Out Of Stock') : __('In Stock') }}
                                    </button>
                                </td>
                                <td>
                                    <div class="mean-all-icon">
                                        <div class="icon-view-list">
                                            <a href="{{ route('lms_product.admin.product.edit', [$product->slug]) }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                    viewBox="0 0 20 20" fill="none">
                                                    <g clip-path="url(#clip0_2734_130627)">
                                                        <path
                                                            d="M9.16797 3.33203H3.33464C2.89261 3.33203 2.46868 3.50763 2.15612 3.82019C1.84356 4.13275 1.66797 4.55667 1.66797 4.9987V16.6654C1.66797 17.1074 1.84356 17.5313 2.15612 17.8439C2.46868 18.1564 2.89261 18.332 3.33464 18.332H15.0013C15.4433 18.332 15.8673 18.1564 16.1798 17.8439C16.4924 17.5313 16.668 17.1074 16.668 16.6654V10.832"
                                                            stroke="#52526C" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M15.418 2.08417C15.7495 1.75265 16.1991 1.56641 16.668 1.56641C17.1368 1.56641 17.5864 1.75265 17.918 2.08417C18.2495 2.41569 18.4357 2.86533 18.4357 3.33417C18.4357 3.80301 18.2495 4.25265 17.918 4.58417L10.0013 12.5008L6.66797 13.3342L7.5013 10.0008L15.418 2.08417Z"
                                                            stroke="#52526C" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_2734_130627">
                                                            <rect width="20" height="20" fill="white" />
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                            </a>
                                        </div>

                                        <div class="icon-view-list">
                                            <button class="deleteItem" data-formid="delete_row_form_{{$product->slug}}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                    viewBox="0 0 20 20" fill="none">
                                                    <path d="M2.5 5H4.16667H17.5" stroke="#52526C" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                    <path
                                                        d="M6.66797 5.0013V3.33464C6.66797 2.89261 6.84356 2.46868 7.15612 2.15612C7.46869 1.84356 7.89261 1.66797 8.33464 1.66797H11.668C12.11 1.66797 12.5339 1.84356 12.8465 2.15612C13.159 2.46868 13.3346 2.89261 13.3346 3.33464V5.0013M15.8346 5.0013V16.668C15.8346 17.11 15.659 17.5339 15.3465 17.8465C15.0339 18.159 14.61 18.3346 14.168 18.3346H5.83464C5.39261 18.3346 4.96868 18.159 4.65612 17.8465C4.34356 17.5339 4.16797 17.11 4.16797 16.668V5.0013H15.8346Z"
                                                        stroke="#52526C" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path d="M8.33203 9.16797V14.168" stroke="#52526C"
                                                        stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path d="M11.668 9.16797V14.168" stroke="#52526C" stroke-width="1.5"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </button>

                                            <form action="{{ route('lms_product.admin.product.delete', [$product->slug]) }}" method="get" id="delete_row_form_{{ $product->slug }}">
                                                {{-- {{ method_field('DELETE') }}
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}"> --}}
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-center" colspan="7">
                                    {{ __('No Data Found') }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-12">
                @if(@$products->hasPages())
                    {{ @$products->links('frontend.paginate.paginate') }}
                @endif
            </div>
        </div>

        {{-- Product Inventory area end --}}
    </div>
</div>

{{-- model area start --}}

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="model-area-box">
                <h4 class="model-title">Stock Request</h4>
                <div class="quantity-area">
                    <h6>Product Quantity</h6>
                    <input type="text" name="" id="">
                </div>
                <div class="massage-area">
                    <h6>Massage</h6>
                    <textarea name="" id="" cols="30" rows="10" placeholder="Write your text"></textarea>
                </div>
                <button id="loadMoreBtn" type="button" class="theme-btn theme-button2 load-more-btn"
                    data-bs-dismiss="modal">Send Request</button>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modalStock" tabindex="-1" aria-labelledby="modalStockLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="model-area-box">
                <h4 class="model-title">{{ __('Add new Stock') }}</h4>
                <form method="POST" action="{{route('lms_product.admin.product.add-stock')}}" enctype="multipart/form-data" data-handler="getStockResponse" class="ajax row g-3 needs-validation">
                    @csrf
                    <div class="mb-3 quantity-area">
                        <h6>{{ __('Number of Quantity') }}</h6>
                        <input type="hidden" name="slug" value="">
                        <input type="number" min=1 name="quantity" required>
                    </div>
                    <button type="submit" class="theme-btn theme-button2 load-more-btn">{{ __('Update') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script src="{{ asset('common/js/apexcharts.min.js') }}"></script>

<script>
    var top5ProductLabels = @json($top5ProductLabels);    
    var top5ProductSeries = @json($top5ProductSeries);   
    var chartThisMonthSaleData = @json($chartThisMonthSaleData);
    var chartThisMonthSaleCategories = @json($chartThisMonthSaleCategories);
    var chartThisMonthEarningData = @json($chartThisMonthEarningData);
    var chartThisMonthEarningCategories = @json($chartThisMonthEarningCategories);
</script>

<script src="{{asset('common/js/select2.min.js')}}"></script>
<script src="{{ asset('common/js/summernote/summernote-lite.min.js') }}"></script>
<script src="{{asset('addon/product/js/product-upload.js')}}"></script>
<script src="{{ asset('addon\product\js\instructor-dashboard.js') }}"></script>

@endpush
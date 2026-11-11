@extends('layouts.instructor')

@push('style')
<link rel="stylesheet" href="{{ asset('addon/product/css/ecommerce-product.css') }}">
@endpush

@section('breadcrumb')
    <div class="page-banner-content text-center">
        <h3 class="page-banner-heading text-white pb-15"> {{__($title)}} </h3>

        <!-- Breadcrumb Start-->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item font-14"><a href="{{route('instructor.dashboard')}}">{{__('Dashboard')}}</a></li>
                <li class="breadcrumb-item font-14 active" aria-current="page">{{__($title)}}</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')

<div>
    <div class="upload-product">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h6 class="dashboard-title"> {{ __('Upload Product') }}</h6>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <a href="{{ route('lms_product.instructor.product.upload', ['step' => 'step-2', 'type' => PHYSICAL_PRODUCT ]) }} ">
                        <div class="single-product-upload">
                            <img src="{{ asset('addon/product/image/product (1).png') }}" alt="{{ __('Physical Product') }}">
                            <h4>{{ __('Add Physical Product') }}</h4>
                        </div>
                     </a>
                </div>
                <div class="col-lg-6">
                    <a href="{{ route('lms_product.instructor.product.upload', ['step' => 'step-2', 'type' => DIGITAL_PRODUCT ]) }} ">
                        <div class="single-product-upload">
                            <img src="{{ asset('addon/product/image/product (2).png') }}" alt="{{ __('Digital Product') }}">
                            <h4>{{ __('Add Digital Product') }}</h4>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
    
@endsection



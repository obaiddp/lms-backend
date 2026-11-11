@extends('frontend.layouts.app')
@section('meta')
    <meta name="description" content="{{ __($product->meta_description) }}">
    <meta name="keywords" content="{{ __($product->meta_keywords) }}">

    <!-- Open Graph meta tags for social sharing -->
    <meta property="og:type" content="Learning">
    <meta property="og:title" content="{{ __($product->meta_title) }}">
    <meta property="og:description" content="{{ __($product->meta_description) }}">
    <meta property="og:image" content="{{ getImageFile($product->og_image) }}">
    <meta property="og:url" content="{{ url()->current() }}">

    <meta property="og:site_name" content="{{ __(get_option('app_name')) }}">

    <!-- Twitter Card meta tags for Twitter sharing -->
    <meta name="twitter:card" content="Learning">
    <meta name="twitter:title" content="{{ __($product->meta_title) }}">
    <meta name="twitter:description" content="{{ __($product->meta_description) }}">
    <meta name="twitter:image" content="{{ getImageFile($product->og_image) }}">
    <link rel="stylesheet" href="{{ asset('addon/product/css/ecommerce-product.css') }}">
@endsection
@section('content')
    <!-- Page Header Start -->
    <header class="page-banner-header gradient-bg position-relative">
        <div class="section-overlay">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-12">
                        <div class="page-banner-content text-center">
                            <h3 class="page-banner-heading text-white pb-15">{{ __($pageTitle) }}</h3>

                            <!-- Breadcrumb Start-->
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb justify-content-center">
                                    <li class="breadcrumb-item font-14"><a
                                            href="{{ url('/') }}">{{ __('Home') }}</a></li>
                                    <li class="breadcrumb-item font-14 active" aria-current="page">{{ __($pageTitle) }}
                                    </li>
                                </ol>
                            </nav>
                            <!-- Breadcrumb End-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Page Header End -->

    @php

        if($product->user->role == USER_ROLE_INSTRUCTOR){
            $userType = 'instructor';
        }
        else if($product->user->role == USER_ROLE_ORGANIZATION){
            $userType = 'organization';
        }
        else if($product->user->role == USER_ROLE_STUDENT){
           
            $userType = 'student';
        }
        else if($product->user->role == USER_ROLE_ADMIN){
            $userType = 'admin';
        }
      
    @endphp

    {{-- slide area --}}
    <div class="single-product-deities">

        <div class="container">
            <div class="border-area">

                <div class="row space-section">
                    <div class="col-lg-7">
                        <div class="slider-product tab-img-part">
                            <div class="d-flex align-items-start">
                                <div class="nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist"
                                    aria-orientation="vertical">
                                    <button class="nav-link pt-0 active" id="v-pills-home-tab" data-bs-toggle="pill"
                                        data-bs-target="#v-pills-home" type="button" role="tab" aria-controls="v-pills-home"
                                        aria-selected="true">
                                        <img src="{{ getImageFile($product->image_1) }}" alt="">
                                    </button>
                                    <button class="nav-link " id="v-pills-profile-tab" data-bs-toggle="pill"
                                        data-bs-target="#v-pills-profile" type="button" role="tab"
                                        aria-controls="v-pills-profile" aria-selected="false">
                                        <img src="{{ getImageFile($product->image_2) }}" alt="">
                                    </button>
                                    
                                    <button class="nav-link" id="v-pills-messages-tab" data-bs-toggle="pill"
                                        data-bs-target="#v-pills-messages" type="button" role="tab"
                                        aria-controls="v-pills-messages" aria-selected="false">
                                        <img src="{{ getImageFile($product->image_3) }}" alt="">
                                    </button>
                                    <button class="nav-link" id="v-pills-settings-tab" data-bs-toggle="pill"
                                        data-bs-target="#v-pills-settings" type="button" role="tab"
                                        aria-controls="v-pills-settings" aria-selected="false">
                                        <img src="{{ getImageFile($product->image_4) }}" alt="">
                                    </button>
                                </div>

                                <div class="tab-content tab-img-slider" id="v-pills-tabContent">
                                    <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel"
                                        aria-labelledby="v-pills-home-tab" tabindex="0">
                                    <img src="{{ getImageFile($product->image_1) }}" alt="">
                                    </div>
                                    <div class="tab-pane fade" id="v-pills-profile" role="tabpanel"
                                        aria-labelledby="v-pills-profile-tab" tabindex="0">
                                        <img src="{{ getImageFile($product->image_2) }}" alt="">
                                    </div>
                                    
                                    <div class="tab-pane fade" id="v-pills-messages" role="tabpanel"
                                        aria-labelledby="v-pills-messages-tab" tabindex="0">
                                        <img src="{{ getImageFile($product->image_3) }}" alt="">
                                    </div>
                                    <div class="tab-pane fade" id="v-pills-settings" role="tabpanel"
                                        aria-labelledby="v-pills-settings-tab" tabindex="0">
                                        <img src="{{ getImageFile($product->image_4) }}" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="product-text-info">
                            <h6 class="product-name">{{__($product->title) }}</h6>
                            {{-- <p class="created-by">{{__('Created by')}} <a href="{{ route('userProfile', $product->user->id) }}" class="created-by-name"> {{ $product->user->name }} </a> </p> --}}
                            <p class="created-by">{{__('Created by')}} {{ $product->user->name }} </p>
                            @php
                                $reviewCount = $product->reviews()->count();
                                $averate_percent = $product->average_review * 20;
                            @endphp
                             @if (@$product->average_review > 0)
                                <div class="product-reating">
                                    <h6>
                                        {{ number_format(@$product->average_review, 1) }}
                                    </h6>
                                    <div class="rating-part-star">
                                        <div class="course-rating search-instructor-rating w-100 mb-0 d-inline-flex align-items-center justify-content-center">
                                            <div class="star-ratings">
                                                <div class="fill-ratings" style="width: {{ $averate_percent }}%">
                                                    <span>★★★★★</span>
                                                </div>
                                                <div class="empty-ratings">
                                                    <span>★★★★★</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <span>
                                        ({{ $reviewCount }})
                                    </span>
                                </div>
                            @endif
                            <div class="product-price">
                                <p>{{ __('Price') }}:</p>

                                @if($product->old_price > $product->current_price)
                                <span class="product-old-price">
                                    @if($currencyPlacement ?? get_currency_placement() == 'after')
                                        {{ $product->old_price }} {{ $currencySymbol ?? get_currency_symbol() }}
                                    @else
                                        {{ $currencySymbol ?? get_currency_symbol() }} {{ $product->old_price }}
                                    @endif
                                </span>
                                <span class="product-new-price">
                                    @if($currencyPlacement ?? get_currency_placement() == 'after')
                                        {{ $product->current_price }} {{ $currencySymbol ?? get_currency_symbol() }}
                                    @else
                                        {{ $currencySymbol ?? get_currency_symbol() }} {{ $product->current_price }}
                                    @endif
                                </span>
                                @else
                                <span class="product-new-price">
                                    @if($currencyPlacement ?? get_currency_placement() == 'after')
                                        {{ $product->current_price }} {{ $currencySymbol ?? get_currency_symbol() }}
                                    @else
                                        {{ $currencySymbol ?? get_currency_symbol() }} {{ $product->current_price }}
                                    @endif
                                </span>
                                @endif
                            </div>
                
                            @if($product->quantity > 0)
                            <div class="stock-product">
                                <span><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <circle cx="10" cy="10" r="10" fill="#45C881"/>
                                    <path d="M14.6693 6.5L8.2526 12.9167L5.33594 10" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg></span>
                                <span>In Stock ({{ $product->quantity }})</span>
                            </div>
                                <!-- @if (auth()->user())
                                    @if (auth()->user()->role == 3)
                                        <div>
                                            <button type="button" class="theme-btn theme-button1 theme-button3 w-75 addToCartProduct" data-product_id="{{ $product->id }}" data-quantity=1 data-route="{{ route('student.addToCart') }}">{{ __('Add To Cart') }}</button>
                                        </div>
                                    @endif
                                @else
                                    <div>
                                        <button type="button" class="theme-btn theme-button1 theme-button3 w-75 addToCartProduct" data-product_id="{{ $product->id }}" data-quantity=1 data-route="{{ route('student.addToCart') }}">{{ __('Add To Cart') }}</button>
                                    </div>
                                @endif    -->
                            @else
                                <div class="mt-3">
                                    <button type="button" class="bg-warning p-2 rounded text-white w-75">{{ __('Out of stock') }}</button>
                                </div>  
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="">
        
                            <!-- Tab panel nav list -->
                            <div class="course-tab-nav-wrap course-details-tab-nav-wrap d-flex justify-content-between ecommerce-tab-review">
                                <ul class="nav product-ecommerce nav-tabs tab-nav-list border-0" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active p-0 m-0" id="Overview-tab" data-bs-toggle="tab" href="#Overview"
                                            role="tab" aria-controls="Overview" aria-selected="true"> {{ __('Description') }}</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="Curriculum-tab" data-bs-toggle="tab" href="#Curriculum"
                                            role="tab" aria-controls="Curriculum" aria-selected="false"
                                            tabindex="-1">{{ __('Additional Information') }}</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="Discussion-tab" data-bs-toggle="tab" href="#Discussion"
                                            role="tab" aria-controls="Discussion" aria-selected="false"
                                            tabindex="-1">{{__('Shipping Return')}}</a>
                                    </li>
                                    {{-- <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="Review-tab" data-bs-toggle="tab" href="#Review" role="tab"
                                            aria-controls="Review" aria-selected="false" tabindex="-1">{{ __('Author') }}</a>
                                    </li> --}}
                                </ul>
                            </div>
                            <!-- Tab panel nav list -->
        
                            <!-- Tab Content-->
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade active show" id="Overview" role="tabpanel"
                                    aria-labelledby="Overview-tab">
                                    <div class="overview-content">
        
                                        <p class="mt-2">
                                            {!! $product->description !!}
                                        </p>
                                    </div>
                                </div>
                                @if($product->type == PHYSICAL_PRODUCT)
                                <div class="tab-pane fade" id="Curriculum" role="tabpanel" aria-labelledby="Curriculum-tab">
                                    <div class="curriculum-content">
                                        <p class="mt-2"> 
                                            {!! $product->shipping_return !!}
                                        </p>
                                    </div>
                                </div>
                                @endif
                                <div class="tab-pane fade" id="Discussion" role="tabpanel" aria-labelledby="Discussion-tab">
                                    <div class="curriculum-content">
                                        <p class="mt-2">
                                            {!! $product->additional_information !!}
                                        </p>
                                    </div>
                                </div>
                                {{-- <div class="tab-pane fade" id="Review" role="tabpanel" aria-labelledby="Review-tab">
                                    <div class="author-content">
                                        <div class="author-content-info">
                                            <div class="author-img-content">
                                                <img src="{{ getImageFile($product->user->image_path)  }}" alt="">
                                            </div>
                                            <h6 class="font-20">{{ $product->user->name }}</h6>
                                            <p class="author-position"> {{ $product->user->professional_title }}</p>
                                        </div>
                                        <div class="author-detiles">
                                    
                                            <p>
                                                {{ $product->user->$userType->about_me }}
                                            </p>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="certification">
                                                    <h6 class="mb-4 col-12">{{ __('Certifications') }}</h6>
                                                    @forelse($product->user->$userType->certificates as $certificate)
                                                    <div class="certificat-year-time">
                                                        <span class="year">{{ $certificate->passing_year }}</span>
                                                        <span class="year-info">{{ $certificate->name }}</span>
                                                    </div>
                                                    @empty
                                                    <div class="certificat-year-time">
                                                        <span>{{ __('No certificate to show') }}</span>
                                                    </div>
                                                    @endforelse
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="certification">
                                                    <h6 class="mb-3 col-12">{{ __('Awards') }}</h6>
                                                    @forelse($product->user->$userType->awards as $award)
                                                    <div class="certificat-year-time">
                                                        <span class="year">{{ $award->winning_year }}</span>
                                                        <span class="year-info">{{ $award->name }}</span>
                                                    </div>
                                                    @empty
                                                    <div class="certificat-year-time">
                                                        <span>{{ __('No awards to show') }}</span>
                                                    </div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
        
                                    </div>
                                </div> --}}
        
                            </div>
                        </div>
        
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- slide area --}}
@endsection

@push('script')
<script src="{{ asset('frontend/assets/js/course/addToCart.js') }}"></script>
@endpush
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
@endsection
@section('content')
    <!-- Page Header Start -->
    <header class="page-banner-header gradient-bg position-relative pt-28">
    </header>
    <div class="courses-crumb course-details-crumb">
        <div class="home-page-container">
            <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="me-2"><svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M11.875 16.25V12.5C11.875 12.3343 11.8092 12.1753 11.6919 12.0581C11.5747 11.9409 11.4158 11.875 11.25 11.875H8.75C8.58424 11.875 8.42527 11.9409 8.30806 12.0581C8.19085 12.1753 8.125 12.3343 8.125 12.5V16.25C8.125 16.4158 8.05915 16.5747 7.94194 16.692C7.82473 16.8092 7.66576 16.875 7.5 16.875H3.75C3.58424 16.875 3.42527 16.8092 3.30806 16.692C3.19085 16.5747 3.125 16.4158 3.125 16.25V9.02345C3.1264 8.93696 3.14509 8.85162 3.17998 8.77245C3.21486 8.69329 3.26523 8.62191 3.32812 8.56252L9.57812 2.88283C9.69334 2.77742 9.84384 2.71896 10 2.71896C10.1562 2.71896 10.3067 2.77742 10.4219 2.88283L16.6719 8.56252C16.7348 8.62191 16.7851 8.69329 16.82 8.77245C16.8549 8.85162 16.8736 8.93696 16.875 9.02345V16.25C16.875 16.4158 16.8092 16.5747 16.6919 16.692C16.5747 16.8092 16.4158 16.875 16.25 16.875H12.5C12.3342 16.875 12.1753 16.8092 12.0581 16.692C11.9408 16.5747 11.875 16.4158 11.875 16.25Z"
                            stroke="#5F6C72" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </li>
                <li class="breadcrumb-item font-14"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item font-14" aria-current="page">{{ __('Products') }}</li>
                <li class="breadcrumb-item font-14 active" aria-current="page">{{ __($pageTitle) }}</li>
                </ol>
            </nav>
        </div>
    </div>
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

    <div class="d-flex product-details-container justify-content-center align-items-start mt-10 home-page-container mx-auto">
        <div class="products-page">
            <div class="d-flex !flex-wrap lg:!flex-nowrap justify-content-center align-items-center position-relative">
                <div style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff" class="swiper mySwiper2 product-swiper">
                    <div class="swiper-wrapper product-wrapper">
                        <div class="swiper-slide">
                                        <img src="{{ getImageFile($product->image_1) }}" alt="">
                        </div>
                        <div class="swiper-slide">
                            <img src="{{ getImageFile($product->image_2) }}" />
                        </div>
                        <div class="swiper-slide">
                            <img src="{{ getImageFile($product->image_3) }}" />
                        </div>
                        <div class="swiper-slide">
                            <img src="{{ getImageFile($product->image_4) }}" />
                        </div>
                    </div>
                </div>
                <div class="product-swiper-buttons">
                    <div class="product-btn-container position-relative like-btns">
                    </div>
                    <div class="product-btn-container position-relative">
                        <button class="quantity-btn swiper-button-prev w-12 h-12" >
                            <svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 1.02905C6 1.02905 1.00001 4.71147 1 6.02905C0.999992 7.34672 6 11.0291 6 11.0291" stroke="#141414" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <button class="quantity-btn swiper-button-next w-12 h-12">
                            <svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1.02905C1 1.02905 5.99999 4.71147 6 6.02905C6.00001 7.34672 1 11.0291 1 11.0291" stroke="#141414" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div thumbsSlider="" class="swiper mySwiper product-thumbs-swiper">
                <div class="swiper-wrapper">
                <div class="swiper-slide">
                        <img src="{{ getImageFile($product->image_1) }}" alt="">
                        </div>
                        <div class="swiper-slide">
                            <img src="{{ getImageFile($product->image_2) }}" />
                        </div>
                        <div class="swiper-slide">
                            <img src="{{ getImageFile($product->image_3) }}" />
                        </div>
                        <div class="swiper-slide">
                            <img src="{{ getImageFile($product->image_4) }}" />
                        </div>
                </div>
            </div> 
        </div>
        <div class="products-page-right">
            <h1 class="product-detail-title aileron2">
                {{__($product->title) }}
            </h1>
            @if (@$product->average_review > 0)
                <div class="course-rating-wrap justify-content-start align-items-center my-2 border-none">
                    <!-- <div class="products-sold aileron">1,238 Sold</div> -->
                    <div class="course-rating !mb-0">
                        <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.04894 0.92705C7.3483 0.00573921 8.6517 0.00573969 8.95106 0.92705L10.0206 4.21885C10.1545 4.63087 10.5385 4.90983 10.9717 4.90983H14.4329C15.4016 4.90983 15.8044 6.14945 15.0207 6.71885L12.2205 8.75329C11.87 9.00793 11.7234 9.4593 11.8572 9.87132L12.9268 13.1631C13.2261 14.0844 12.1717 14.8506 11.388 14.2812L8.58778 12.2467C8.2373 11.9921 7.7627 11.9921 7.41221 12.2467L4.61204 14.2812C3.82833 14.8506 2.77385 14.0844 3.0732 13.1631L4.14277 9.87132C4.27665 9.4593 4.12999 9.00793 3.7795 8.75329L0.979333 6.71885C0.195619 6.14945 0.598395 4.90983 1.56712 4.90983H5.02832C5.46154 4.90983 5.8455 4.63087 5.97937 4.21885L7.04894 0.92705Z" fill="#FFA439"></path>
                        </svg>
                        @php
                                    $reviewCount = $product->reviews()->count();
                                    $averate_percent = $product->average_review * 20;
                                @endphp
                        <span>{{ number_format(@$product->average_review, 1) }}</span>
                    </div>
                </div>
            @endif
            
            <div class="rating-banner">
                <!-- <div class="rating aileron">#5 Top Rated</div> -->
                <div class="creator aileron">Created by {{ $product->user->name }}</div>
            </div>
            <div class="price-container my-2">
                <div class="price-wrapper">
                    @if($product->old_price > $product->current_price)
                                <span  class="original-price aileron">
                                    @if($currencyPlacement ?? get_currency_placement() == 'after')
                                        {{ $product->old_price }} {{ $currencySymbol ?? get_currency_symbol() }}
                                    @else
                                        {{ $currencySymbol ?? get_currency_symbol() }} {{ $product->old_price }}
                                    @endif
                                </span>
                                <span  class="original-price aileron">
                                    @if($currencyPlacement ?? get_currency_placement() == 'after')
                                        {{ $product->current_price }} {{ $currencySymbol ?? get_currency_symbol() }}
                                    @else
                                        {{ $currencySymbol ?? get_currency_symbol() }} {{ $product->current_price }}
                                    @endif
                                </span>
                                @else
                                <span class="current-price aileron">
                                    @if($currencyPlacement ?? get_currency_placement() == 'after')
                                        {{ $product->current_price }} {{ $currencySymbol ?? get_currency_symbol() }}
                                    @else
                                        {{ $currencySymbol ?? get_currency_symbol() }} {{ $product->current_price }}
                                    @endif
                                </span>
                                @endif
                </div>
                <!-- <div class="discount-badge aileron">15% OFF - Limited Time</div> -->
            </div>
         
            <div class="product-descriptions">
                <h1>Description:</h1>
                {!! $product->description !!}
            </div>
            <div class="product-quantity-container">
                <div class="product-quantity">
                    <h1>Quantity</h1>
                    <div class="quantity-wrapper">
                    <button class="quantity-btn pt-1" id="decrease-quantity">-</button>
                    <input type="text" class="quantity-input" id="quantity" value="1" readonly>
                    <button class="quantity-btn" id="increase-quantity">+</button>
                </div>
            </div>
            <div class="product-add-to-cart">
                @if($product->quantity > 0)
                    @if(auth()->check() && auth()->user()->role == 3)
                        <button class="add-to-cart-btn addToCartProduct" data-product_id="{{ $product->id }}" data-quantity=1 data-route="{{ route('student.addToCart') }}">Add To Cart</button>
                    @else
                        <a href="{{ route('login') }}" class="add-to-cart-btn">Login to Add to Cart</a>
                    @endif
                @else
                    <button class="add-to-cart-btn" disabled>Out of Stock</button>
                @endif
            </div>
            </div>
         
        </div>
    </div>

    <!-- <div class="product-review home-page-container mx-auto">
        <div class="review-content">
            <div class="review-tab-top-part mt-10">
                <div class="d-flex justify-content-between align-items-center w-100 products-reviews-section">
                    <div class="filters">
                        <h2 class="section-title">Product Reviews</h2>
                        <svg class="block lg:hidden mobile-filter-select" width="24" height="18" viewBox="0 0 24 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22.5 7.49998H1.5C0.671578 7.49998 0 8.17156 0 8.99998C0 9.82841 0.671578 10.5 1.5 10.5H22.5C23.3284 10.5 24 9.82841 24 8.99998C24 8.17156 23.3284 7.49998 22.5 7.49998ZM1.5 3.5H22.5C23.3284 3.5 24 2.82842 24 2C24 1.17158 23.3284 0.5 22.5 0.5H1.5C0.671578 0.5 0 1.17158 0 2C0 2.82842 0.671578 3.5 1.5 3.5ZM22.5 14.5H1.5C0.671578 14.5 0 15.1715 0 16C0 16.8284 0.671578 17.5 1.5 17.5H22.5C23.3284 17.5 24 16.8284 24 16C24 15.1715 23.3284 14.5 22.5 14.5Z" fill="black"/>
                        </svg>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.0814 4.26378L13.2546 3.12003C13.3381 3.0386 13.3972 2.9354 13.4252 2.82212C13.4532 2.70883 13.4488 2.58998 13.4127 2.47902C13.3766 2.36806 13.3102 2.26942 13.2209 2.19427C13.1317 2.11911 13.0232 2.07045 12.9077 2.05378L11.2858 1.81816L10.5608 0.34878C10.5032 0.251346 10.4212 0.170605 10.3229 0.114517C10.2246 0.0584288 10.1134 0.0289307 10.0002 0.0289307C9.887 0.0289307 9.77577 0.0584288 9.67746 0.114517C9.57915 0.170605 9.49715 0.251346 9.43956 0.34878L8.71456 1.81816L7.09268 2.05378C6.97724 2.07054 6.8688 2.11928 6.77962 2.19447C6.69044 2.26966 6.62409 2.36831 6.58806 2.47926C6.55203 2.5902 6.54777 2.70901 6.57576 2.82225C6.60375 2.9355 6.66287 3.03864 6.74643 3.12003L7.91956 4.26378L7.64268 5.87941C7.62307 5.99426 7.63596 6.11231 7.67989 6.22023C7.72381 6.32815 7.79704 6.42164 7.89128 6.49015C7.98553 6.55865 8.09705 6.59945 8.21326 6.60794C8.32947 6.61642 8.44573 6.59225 8.54893 6.53816L10.0002 5.77566L11.4508 6.53816C11.554 6.59242 11.6704 6.61673 11.7867 6.60833C11.903 6.59993 12.0146 6.55916 12.109 6.49063C12.2033 6.42209 12.2766 6.32853 12.3206 6.22051C12.3645 6.1125 12.3774 5.99434 12.3577 5.87941L12.0814 4.26378ZM19.9696 4.32691C19.9335 4.21608 19.8671 4.11756 19.778 4.04246C19.6889 3.96736 19.5805 3.91867 19.4652 3.90191L17.8433 3.66566L17.1177 2.19628C17.0602 2.09869 16.9783 2.0178 16.8799 1.96159C16.7816 1.90539 16.6703 1.87583 16.5571 1.87583C16.4438 1.87583 16.3325 1.90539 16.2342 1.96159C16.1359 2.0178 16.0539 2.09869 15.9964 2.19628L15.2714 3.66566L13.6496 3.90128C13.5341 3.91804 13.4257 3.96678 13.3365 4.04197C13.2473 4.11716 13.181 4.21581 13.1449 4.32676C13.1089 4.4377 13.1046 4.55651 13.1326 4.66975C13.1606 4.783 13.2197 4.88614 13.3033 4.96753L14.4771 6.11128L14.1996 7.7269C14.1797 7.84189 14.1925 7.96013 14.2364 8.06825C14.2803 8.17636 14.3535 8.27003 14.4479 8.33865C14.5423 8.40727 14.654 8.4481 14.7704 8.45651C14.8868 8.46493 15.0032 8.4406 15.1064 8.38628L16.5564 7.62378L18.0077 8.38628C18.1109 8.4406 18.2274 8.46493 18.3437 8.45651C18.4601 8.4481 18.5718 8.40727 18.6662 8.33865C18.7606 8.27003 18.8339 8.17636 18.8777 8.06825C18.9216 7.96013 18.9344 7.84189 18.9146 7.7269L18.6377 6.11128L19.8108 4.96753C19.8944 4.8862 19.9536 4.78309 19.9816 4.66988C20.0097 4.55667 20.0055 4.43786 19.9696 4.32691ZM6.35018 3.90128L4.72893 3.66566L4.00393 2.19628C3.95235 2.09161 3.8725 2.00348 3.77342 1.94185C3.67435 1.88021 3.55999 1.84755 3.44331 1.84755C3.32662 1.84755 3.21227 1.88021 3.11319 1.94185C3.01411 2.00348 2.93426 2.09161 2.88268 2.19628L2.15768 3.66566L0.535181 3.90128C0.419744 3.91804 0.311301 3.96678 0.222122 4.04197C0.132943 4.11716 0.0665855 4.21581 0.0305577 4.32676C-0.00547019 4.4377 -0.00973088 4.55651 0.0182576 4.66975C0.0462461 4.783 0.105367 4.88614 0.188931 4.96753L1.36268 6.11128L1.08581 7.7269C1.0662 7.84176 1.07908 7.95981 1.12301 8.06773C1.16694 8.17565 1.24016 8.26914 1.33441 8.33765C1.42866 8.40615 1.54018 8.44695 1.65639 8.45544C1.77259 8.46392 1.88886 8.43975 1.99206 8.38566L3.44331 7.62315L4.89331 8.38566C4.99654 8.4401 5.11296 8.46454 5.22937 8.45623C5.34579 8.44791 5.45755 8.40717 5.552 8.33861C5.64645 8.27005 5.71981 8.17641 5.76379 8.0683C5.80776 7.96019 5.82058 7.84193 5.80081 7.7269L5.52393 6.11128L6.69768 4.96753C6.78107 4.88608 6.84003 4.78293 6.86788 4.66973C6.89572 4.55654 6.89136 4.43781 6.85528 4.32696C6.8192 4.21611 6.75284 4.11757 6.66369 4.04246C6.57454 3.96734 6.46617 3.91866 6.35081 3.90191L6.35018 3.90128ZM16.2502 9.37503H3.75018C3.25305 9.37553 2.77643 9.57323 2.4249 9.92475C2.07338 10.2763 1.87568 10.7529 1.87518 11.25V16.25C1.87568 16.7472 2.07338 17.2238 2.4249 17.5753C2.77643 17.9268 3.25305 18.1245 3.75018 18.125H7.86643L9.55831 19.8169C9.67551 19.9341 9.83445 19.9999 10.0002 19.9999C10.1659 19.9999 10.3249 19.9341 10.4421 19.8169L12.1339 18.125H16.2502C16.7473 18.1245 17.2239 17.9268 17.5755 17.5753C17.927 17.2238 18.1247 16.7472 18.1252 16.25V11.25C18.1247 10.7529 17.927 10.2763 17.5755 9.92475C17.2239 9.57323 16.7473 9.37553 16.2502 9.37503ZM15.0002 15.625H5.00018C4.83442 15.625 4.67545 15.5592 4.55824 15.442C4.44103 15.3248 4.37518 15.1658 4.37518 15C4.37518 14.8343 4.44103 14.6753 4.55824 14.5581C4.67545 14.4409 4.83442 14.375 5.00018 14.375H15.0002C15.1659 14.375 15.3249 14.4409 15.4421 14.5581C15.5593 14.6753 15.6252 14.8343 15.6252 15C15.6252 15.1658 15.5593 15.3248 15.4421 15.442C15.3249 15.5592 15.1659 15.625 15.0002 15.625ZM15.0002 13.125H5.00018C4.83442 13.125 4.67545 13.0592 4.55824 12.942C4.44103 12.8248 4.37518 12.6658 4.37518 12.5C4.37518 12.3343 4.44103 12.1753 4.55824 12.0581C4.67545 11.9409 4.83442 11.875 5.00018 11.875H15.0002C15.1659 11.875 15.3249 11.9409 15.4421 12.0581C15.5593 12.1753 15.6252 12.3343 15.6252 12.5C15.6252 12.6658 15.5593 12.8248 15.4421 12.942C15.3249 13.0592 15.1659 13.125 15.0002 13.125Z" fill="#1d9e34"/>
                    </svg>
                    <span class="delivery-text">All reviews are from verified purchases</span>
                    </div>
                </div>

                
        
        <div class="rating-summary-container">
            <div class="average-rating-circle">
                <div class="circle-wrapper">
                    <div class="circle-value">4.5</div>
                </div>
                <div class="rating-meta">
                    <div class="rating-stars">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="star {{ $i <= 4.5 ? 'filled' : '' }}">
                                <span class="iconify" data-icon="bi:star-fill"></span>
                            </span>
                        @endfor
                    </div>
                    <p class="rating-count">from 150 reviews</p>
                </div>
            </div>

            <div class="review-progress-bar-wrap">
                <div class="barras">
                    <div class="progress-bar-box d-flex align-items-center">
                        <div class="progress-hint">
                            <div class="progress-star d-flex align-items-center me-2">
                                <div class="hint-number me-3 font-medium color-heading">5</div>
                                <ul class="rating-list d-flex align-items-center">
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="barra">
                            <div class="barra-nivel" data-nivel="70%"></div>
                        </div>
                        <div class="progress-hint-value font-14 color-heading">105</div>
                    </div>
                </div>
                <div class="barras">
                    <div class="progress-bar-box d-flex align-items-center">
                        <div class="progress-hint">
                            <div class="progress-star d-flex align-items-center me-2">
                                <div class="hint-number me-3 font-medium color-heading">4</div>
                                <ul class="rating-list d-flex align-items-center">
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li><span class="iconify" data-icon="bi:star-fill"></span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="barra">
                            <div class="barra-nivel" data-nivel="20%"></div>
                        </div>
                        <div class="progress-hint-value font-14 color-heading">30</div>
                    </div>
                </div>
                <div class="barras">
                    <div class="progress-bar-box d-flex align-items-center">
                        <div class="progress-hint">
                            <div class="progress-star d-flex align-items-center me-2">
                                <div class="hint-number me-3 font-medium color-heading">3</div>
                                <ul class="rating-list d-flex align-items-center">
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li><span class="iconify" data-icon="bi:star-fill"></span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="barra">
                            <div class="barra-nivel" data-nivel="5%"></div>
                        </div>
                        <div class="progress-hint-value font-14 color-heading">7</div>
                    </div>
                </div>
                <div class="barras">
                    <div class="progress-bar-box d-flex align-items-center">
                        <div class="progress-hint">
                            <div class="progress-star d-flex align-items-center me-2">
                                <div class="hint-number me-3 font-medium color-heading">2</div>
                                <ul class="rating-list d-flex align-items-center">
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li><span class="iconify" data-icon="bi:star-fill"></span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="barra">
                            <div class="barra-nivel" data-nivel="3%"></div>
                        </div>
                        <div class="progress-hint-value font-14 color-heading">5</div>
                    </div>
                </div>
                <div class="barras">
                    <div class="progress-bar-box d-flex align-items-center">
                        <div class="progress-hint">
                            <div class="progress-star d-flex align-items-center me-2">
                                <div class="hint-number me-3 font-medium color-heading">1</div>
                                <ul class="rating-list d-flex align-items-center">
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li><span class="iconify" data-icon="bi:star-fill"></span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="barra">
                            <div class="barra-nivel" data-nivel="2%"></div>
                        </div>
                        <div class="progress-hint-value font-14 color-heading">3</div>
                    </div>
                </div>

            </div>
        </div>

        <div class="product-reviews-container">
            <div class="d-flex justify-content-between gap-4">
                <div class="product-reviews-filter">
                <div class="reviews-filter">
                    <div class="justify-content-between align-items-center mobile-view">
                        <a href="{{ url('/') }}" class="navbar-img-mobile">
                            <img src="{{ getImageFile(get_option('app_logo')) }}" alt="Logo">
                        </a>
                        <svg class="filter-close-mobile" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g opacity="0.3" clip-path="url(#clip0_651_1092)">
                            <path d="M21.3334 10.6667C21.0834 10.4167 20.7443 10.2763 20.3908 10.2763C20.0372 10.2763 19.6981 10.4167 19.4481 10.6667L16.0001 14.1147L12.5521 10.6667C12.3006 10.4238 11.9638 10.2894 11.6142 10.2924C11.2646 10.2955 10.9302 10.4357 10.683 10.6829C10.4358 10.9301 10.2956 11.2645 10.2925 11.6141C10.2895 11.9637 10.4239 12.3005 10.6668 12.552L14.1148 16L10.6668 19.448C10.4239 19.6995 10.2895 20.0363 10.2925 20.3859C10.2956 20.7355 10.4358 21.0699 10.683 21.3171C10.9302 21.5643 11.2646 21.7045 11.6142 21.7076C11.9638 21.7106 12.3006 21.5762 12.5521 21.3333L16.0001 17.8853L19.4481 21.3333C19.6996 21.5762 20.0364 21.7106 20.386 21.7076C20.7356 21.7045 21.07 21.5643 21.3172 21.3171C21.5644 21.0699 21.7046 20.7355 21.7077 20.3859C21.7107 20.0363 21.5763 19.6995 21.3334 19.448L17.8854 16L21.3334 12.552C21.5834 12.302 21.7238 11.9629 21.7238 11.6093C21.7238 11.2558 21.5834 10.9167 21.3334 10.6667Z" fill="black"/>
                            <path d="M16 0C12.8355 0 9.74207 0.938384 7.11088 2.69649C4.4797 4.45459 2.42894 6.95345 1.21793 9.87707C0.00693258 12.8007 -0.309921 16.0177 0.307443 19.1214C0.924806 22.2251 2.44866 25.0761 4.6863 27.3137C6.92394 29.5514 9.77487 31.0752 12.8786 31.6926C15.9823 32.3099 19.1993 31.9931 22.1229 30.7821C25.0466 29.5711 27.5454 27.5203 29.3035 24.8891C31.0616 22.2579 32 19.1645 32 16C31.9954 11.7579 30.3082 7.69095 27.3087 4.69136C24.3091 1.69177 20.2421 0.00458811 16 0ZM16 29.3333C13.3629 29.3333 10.7851 28.5513 8.5924 27.0863C6.39975 25.6212 4.69078 23.5388 3.68161 21.1024C2.67245 18.6661 2.4084 15.9852 2.92287 13.3988C3.43734 10.8124 4.70722 8.43661 6.57192 6.57191C8.43662 4.70721 10.8124 3.43733 13.3988 2.92286C15.9852 2.40839 18.6661 2.67244 21.1025 3.68161C23.5388 4.69078 25.6212 6.39974 27.0863 8.5924C28.5514 10.7851 29.3333 13.3629 29.3333 16C29.3295 19.535 27.9235 22.9242 25.4238 25.4238C22.9242 27.9235 19.535 29.3295 16 29.3333Z" fill="black"/>
                            </g>
                            <defs>
                            <clipPath id="clip0_651_1092">
                            <rect width="32" height="32" fill="white"/>
                            </clipPath>
                            </defs>
                        </svg>
                    </div>
                    <form action="#" class="form-search-mobile">
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon1">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g opacity="0.3" clip-path="url(#clip0_651_1078)">
                                    <path d="M17.6268 13.8499C20.7909 10.6858 20.7909 5.53727 17.6268 2.37311C14.4627 -0.791056 9.31424 -0.791056 6.15014 2.37311C3.46578 5.05747 3.05923 9.0498 4.92941 12.1674C4.92941 12.1674 5.06374 12.3928 4.88231 12.5741L0.741918 16.7145C-0.0821909 17.5385 -0.278377 18.6908 0.45244 19.4218L0.578269 19.5475C1.30909 20.2784 2.46146 20.0823 3.2855 19.2582L7.4172 15.1265C7.60733 14.9363 7.83263 15.0707 7.83263 15.0707C10.9502 16.9408 14.9425 16.5343 17.6268 13.8499ZM7.64837 12.3517C5.31037 10.0137 5.31044 6.20952 7.64843 3.87153C9.98643 1.5336 13.7906 1.53354 16.1285 3.87153C18.4665 6.20946 18.4665 10.0137 16.1285 12.3517C13.7905 14.6895 9.98643 14.6895 7.64837 12.3517Z" fill="black"/>
                                    <path d="M8.1663 7.62992C8.05748 7.62992 7.94686 7.60853 7.84023 7.56349C7.41429 7.38322 7.21501 6.89176 7.39528 6.46576C8.52833 3.78816 11.6285 2.5316 14.306 3.66465C14.7319 3.84492 14.9312 4.33638 14.7509 4.76239C14.5706 5.18839 14.0793 5.38754 13.6531 5.20733C11.8263 4.43431 9.71099 5.29167 8.93803 7.11848C8.8028 7.43799 8.49264 7.62992 8.1663 7.62992Z" fill="black"/>
                                    </g>
                                    <defs>
                                    <clipPath id="clip0_651_1078">
                                    <rect width="20" height="20" fill="white"/>
                                    </clipPath>
                                    </defs>
                                    </svg>
                            </span>
                            <input type="text" id="search-product" class="search-product2" type="search" name="keyword" value="{{request('keyword')}}" placeholder="{{__('Search Product')}}..." aria-label="Search">
                        </div>
                    </form>
                    <h5 class="filter-heading">Reviews Filter</h5>
                    <hr>
                    <div class="filter-section">
                        <h6 class="filter-subheading">Rating</h6>
                        <ul class="rating-filter-list">
                            <li>
                                <input type="checkbox" id="rating-5">
                                <label for="rating-5">
                                    <span class="iconify" data-icon="bi:star-fill"></span> 5
                                </label>
                            </li>
                            <li>
                                <input type="checkbox" id="rating-4">
                                <label for="rating-4">
                                    <span class="iconify" data-icon="bi:star-fill"></span> 4
                                </label>
                            </li>
                            <li>
                                <input type="checkbox" id="rating-3">
                                <label for="rating-3">
                                    <span class="iconify" data-icon="bi:star-fill"></span> 3
                                </label>
                            </li>
                            <li>
                                <input type="checkbox" id="rating-2">
                                <label for="rating-2">
                                    <span class="iconify" data-icon="bi:star-fill"></span> 2
                                </label>
                            </li>
                            <li>
                                <input type="checkbox" id="rating-1">
                                <label for="rating-1">
                                    <span class="iconify" data-icon="bi:star-fill"></span> 1
                                </label>
                            </li>
                        </ul>
                    </div>
                    <hr>
                    <div class="filter-section">
                        <h6 class="filter-subheading">Review Topics</h6>
                        <ul class="topics-filter-list">
                            <li>
                                <input type="checkbox" id="topic-quality">
                                <label for="topic-quality">Product Quality</label>
                            </li>
                            <li>
                                <input type="checkbox" id="topic-services">
                                <label for="topic-services">Seller Services</label>
                            </li>
                            <li>
                                <input type="checkbox" id="topic-price">
                                <label for="topic-price">Product Price</label>
                            </li>
                            <li>
                                <input type="checkbox" id="topic-shipment">
                                <label for="topic-shipment">Shipment</label>
                            </li>
                            <li>
                                <input type="checkbox" id="topic-description">
                                <label for="topic-description">Match with Description</label>
                            </li>
                            <li>
                                <input type="checkbox" id="topic-quality">
                                <label for="topic-quality">Product Quality</label>
                            </li>
                            <li>
                                <input type="checkbox" id="topic-services">
                                <label for="topic-services">Seller Services</label>
                            </li>
                            <li>
                                <input type="checkbox" id="topic-price">
                                <label for="topic-price">Product Price</label>
                            </li>
                            <li>
                                <input type="checkbox" id="topic-shipment">
                                <label for="topic-shipment">Shipment</label>
                            </li>
                            <li>
                                <input type="checkbox" id="topic-description">
                                <label for="topic-description">Match with Description</label>
                            </li>
                        </ul>
                    </div>
                    <button class="apply">Apply</button> 
                </div>
                </div>
                <div class="product-reviews-filter-tabs">
                    <h5 class="filterlist-heading">Review List</h5>
                    <div class="review-filter-tabs">
                        <button class="filter-tab active">All Reviews</button>
                        <button class="filter-tab">With Photo & Video</button>
                        <button class="filter-tab">With Description</button>
                    </div>
                    <div class="customer-review-item d-flex">
                    <div class="review-container">
                        <ul class="rating-list d-flex align-items-center me-2">
                            <li class="star-full"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--bi" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 16 16" data-icon="bi:star-fill"><path fill="currentColor" d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327l4.898.696c.441.062.612.636.282.95l-3.522 3.356l.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"></path></svg></li>
                            <li class="star-full"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--bi" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 16 16" data-icon="bi:star-fill"><path fill="currentColor" d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327l4.898.696c.441.062.612.636.282.95l-3.522 3.356l.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"></path></svg></li>
                            <li class="star-full"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--bi" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 16 16" data-icon="bi:star-fill"><path fill="currentColor" d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327l4.898.696c.441.062.612.636.282.95l-3.522 3.356l.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"></path></svg></li>
                            <li class=""><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--bi" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 16 16" data-icon="bi:star-fill"><path fill="currentColor" d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327l4.898.696c.441.062.612.636.282.95l-3.522 3.356l.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"></path></svg></li>
                            <li class=""><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--bi" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 16 16" data-icon="bi:star-fill"><path fill="currentColor" d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327l4.898.696c.441.062.612.636.282.95l-3.522 3.356l.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"></path></svg></li>
                        </ul>

                        <p class="review-text">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.
                        </p>
                        
                        <p class="review-date">1 month ago</p>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="reviewer-info">
                                <div class="reviewer-avatar">
                                    <img src="http://127.0.0.1:8000/uploads/default/instructor-default.png" alt="Malaika Baig">
                                </div>
                                <div class="reviewer-name-role">
                                    <p class="reviewer-name">John Doe - Student</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="review-list">
                        <div class="load-more-container" id="loadMoreBtn">
                            <button class="load-more-btn loadMore">{{ __('Load More') }} <span class="iconify" data-icon="icon-park-outline:loading-one"></span></button>
                        </div>
                    </div>
                </div>
                </div>
        </div>
    </div> -->
</div>


</div>

    {{-- <div class="product-image-grid-container home-page-container mt-4">
        <div class="image-grid">
            <div class="grid-item">
                <img src="{{ getImageFile($product->image_1) }}" alt="Product Image 1" class="grid-image">
            </div>
            <div class="grid-item">
                <img src="{{ getImageFile($product->image_2) }}" alt="Product Image 2" class="grid-image">
            </div>
            <div class="grid-item">
                <img src="{{ getImageFile($product->image_3) }}" alt="Product Image 3" class="grid-image">
            </div>
            <div class="grid-item">
                <img src="{{ getImageFile($product->image_4) }}" alt="Product Image 4" class="grid-image">
            </div>
        </div>
    </div> --}}

    <!-- <div class="home-page-container">
        <section class="products-section">
            <div class="home-page-container">
                <p
                    class="section-tag">
                    <span class="section-tag-icon">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M1.68226 4.33298L6.42758 1.96032C6.59649 1.87586 6.79532 1.87586 6.96423 1.96032L11.7096 4.33298C12.1518 4.55409 12.1518 5.18518 11.7096 5.40629L6.96423 7.77895C6.79532 7.86341 6.59649 7.86341 6.42758 7.77895L1.68226 5.40629C1.24003 5.18518 1.24003 4.55409 1.68226 4.33298Z"
                                stroke="#013370" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M13.3912 7.30432V5.17389L12.1738 4.56519" stroke="#013370" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path
                                d="M2.43555 5.78259V8.58215C2.43555 9.35439 2.88014 10.0577 3.57774 10.3889L5.83861 11.4623C6.38144 11.7201 7.01139 11.7201 7.55422 11.4623L9.81509 10.3889C10.5127 10.0577 10.9573 9.35439 10.9573 8.58215V5.78259"
                                stroke="#013370" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <span class="section-tag-title">Recommended</span>
                </p>
                <h1 class="section-heading-home">You Might Also Like</h1>
                <div class="product-section-wrap">
                    @php
                        $dummyProducts = [
                            [
                                'title' => 'Smartphone X1',
                                'slug' => 'smartphone-x1',
                                'thumbnail_path' => 'uploads/products/phone1.jpg',
                                'old_price' => 999,
                                'current_price' => 799,
                                'category' => ['name' => 'Electronics'],
                                'average_review' => 4.5,
                                'quantity' => 10,
                                'id' => 1
                            ],
                            [
                                'title' => 'Laptop Pro',
                                'slug' => 'laptop-pro', 
                                'thumbnail_path' => 'uploads/products/laptop1.jpg',
                                'old_price' => 1499,
                                'current_price' => 1299,
                                'category' => ['name' => 'Computers'],
                                'average_review' => 4.8,
                                'quantity' => 5,
                                'id' => 2
                            ],
                            [
                                'title' => 'Wireless Earbuds',
                                'slug' => 'wireless-earbuds',
                                'thumbnail_path' => 'uploads/products/earbuds1.jpg', 
                                'old_price' => 199,
                                'current_price' => 149,
                                'category' => ['name' => 'Audio'],
                                'average_review' => 4.3,
                                'quantity' => 20,
                                'id' => 3
                            ],
                        ];
                    @endphp

                    @foreach ($dummyProducts as $product)
                        <a href="{{ route('lms_product.frontend.view', $product['slug']) }}"
                            class="product-flex">
                            <span class="sale-tag">Sale</span>
                            <img loading="lazy" src="{{ $product['thumbnail_path'] }}" alt="{{ $product['title'] }}"
                                class="product-img">
                            <h4 class="product-title">{{ $product['title'] }}</h4>

                            @if ($product['old_price'] > $product['current_price'])
                                <span class="new-price">
                                    <span class="new-price-text">${{ $product['current_price'] }}</span>
                                </span>
                                <span>/</span>
                                <span class="old-price">
                                    <span class="gray-text">${{ $product['old_price'] }}</span>
                                </span>
                            @else
                                <span class="new-price">
                                    <span class="new-price-text">${{ $product['current_price'] }}</span>
                                </span>
                            @endif

                            <hr class="line">
                            <div class="product-description">
                                <h6 class="product-category">{{ $product['category']['name'] }}</h6>
                                <div class="product-reviews">
                                    <span>
                                        <svg width="16" height="15" viewBox="0 0 16 15" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M7.60845 0L10.0066 4.69921L15.2169 5.52786L11.4888 9.26079L12.3107 14.4721L7.60845 12.08L2.90617 14.4721L3.72814 9.26079L0 5.52786L5.21029 4.69921L7.60845 0Z"
                                                fill="#FEC801" />
                                        </svg>
                                    </span>
                                    <span class="product-total-reviews">{{ number_format($product['average_review'], 1) }}
                                        ({{ rand(10,100) }} reviews)</span>
                                </div>
                            </div>
                            @if (auth()->user())
                                @if (auth()->user()->role == 3)
                                @if ($product['quantity'] > 0)
                                    <div class="product-overlay">
                                        <button type="button" class="addToCartProduct" data-product_id="{{ $product['id'] }}" data-quantity=1
                                            data-route="{{ route('student.addToCart') }}"
                                            class="product-overlay-button">
                                            {{ __('Add to cart') }}
                                        </button>
                                    </div>
                                    @endif
                                    @endif
                            @else
                                <div class="product-overlay">
                                    <button type="button"
                                        class="product-overlay-button">
                                        {{ __('Out of stock') }}
                                    </button>
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>

                <div class="product-section-slider">
                    <div class="swiper products-slider">
                        <div class="swiper-wrapper">
                            @foreach ($dummyProducts as $product)
                                <div class="swiper-slide">
                                    <a href="{{ route('lms_product.frontend.view', $product['slug']) }}"
                                        class="product-flex">
                                        <span class="sale-tag">Sale</span>
                                        <img loading="lazy" src="{{ $product['thumbnail_path'] }}" alt="{{ $product['title'] }}"
                                            class="product-img">
                                        <h4 class="product-title">{{ $product['title'] }}</h4>

                                        @if ($product['old_price'] > $product['current_price'])
                                            <span class="new-price">
                                                <span class="new-price-text">${{ $product['current_price'] }}</span>
                                            </span>
                                            <span>/</span>
                                            <span class="old-price">
                                                <span class="gray-text">${{ $product['old_price'] }}</span>
                                            </span>
                                        @else
                                            <span class="new-price">
                                                <span class="new-price-text">${{ $product['current_price'] }}</span>
                                            </span>
                                        @endif

                                        <hr class="line">
                                        <div class="product-description">
                                            
                                                <h6 class="product-category">{{ $product['category']['name'] }}</h6>
                                            
                                            <div class="product-reviews">
                                                <span>
                                                    <svg width="16" height="15" viewBox="0 0 16 15" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M7.60845 0L10.0066 4.69921L15.2169 5.52786L11.4888 9.26079L12.3107 14.4721L7.60845 12.08L2.90617 14.4721L3.72814 9.26079L0 5.52786L5.21029 4.69921L7.60845 0Z"
                                                            fill="#FEC801" />
                                                    </svg>
                                                </span>
                                                <span class="product-total-reviews">{{ number_format($product['average_review'], 1) }}
                                                    ({{ rand(10,100) }} reviews)</span>
                                            </div>
                                        </div>

                                        @if ($product['quantity'] > 0)
                                            <div class="product-overlay">
                                                <button type="button" data-product_id="{{ $product['id'] }}" data-quantity=1
                                                    data-route="{{ route('student.addToCart') }}"
                                                    class="product-overlay-button">
                                                    {{ __('Add to cart') }}
                                                </button>
                                            </div>
                                        @else
                                            <div class="product-overlay">
                                                <button type="button"
                                                    class="product-overlay-button">
                                                    {{ __('Out of stock') }}
                                                </button>
                                            </div>
                                        @endif
                                    </a>
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>

                <div class="view-all-btn-home">
                    <a href="{{ route('lms_product.frontend.list') }}"
                        class="view-all-btn-home-btn"
                        aria-current="page">
                        {{ __('View All Products') }}
                    </a>
                </div>
            </div>
        </section>
    </div> -->



    {{-- slide area --}}
    {{-- <div class="single-product-deities">

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
                            <p class="created-by">{{__('Created by')}} {{ $product->user->name }} </p>
                            @php
                                $reviewCount = $product->reviews()->count();
                                $averate_percent = $product->average_review * 20;
                            @endphp
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
                                @if (auth()->user())
                                    @if (auth()->user()->role == 3)
                                        @if($product->quantity > 0)
                                        <div>
                                            <button type="button" class="theme-btn theme-button1 theme-button3 w-75 addToCartProduct" data-product_id="{{ $product->id }}" data-quantity=1 data-route="{{ route('student.addToCart') }}">{{ __('Add To Cart') }}</button>
                                        </div>
                                        @endif
                                    @endif
                                @else
                                
                                @endif   
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
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="Review-tab" data-bs-toggle="tab" href="#Review" role="tab"
                                            aria-controls="Review" aria-selected="false" tabindex="-1">{{ __('Author') }}</a>
                                    </li> 
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
                                            {!! $product->additional_information !!}
                                        </p>
                                    </div>
                                </div>
                                @endif
                                <div class="tab-pane fade" id="Discussion" role="tabpanel" aria-labelledby="Discussion-tab">
                                    <div class="discussion-content">
                                        <p class="mt-2">
                                            {!! $product->shipping_return !!}
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
                                </div> 
        
                            </div>
                        </div>
        
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
@endsection

@push('script')
<script src="{{ asset('frontend/assets/js/course/addToCart.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function startCountdown(hours) {
            // Convert hours to seconds for countdown
            const totalSeconds = hours * 3600;
            const startTime = totalSeconds;
            let timer = totalSeconds;
            
            // Check if elements exist
            const countdownElement = document.getElementById('countdown');
            const progressBar = document.getElementById('progress-bar');
            const timerIcon = document.querySelector('.timer-icon');
            const progressBarContainer = document.querySelector('.progress-bar-container');
            
            if (!countdownElement || !progressBar || !timerIcon || !progressBarContainer) {
                console.error('Required elements not found');
                return;
            }
            
            const countdown = setInterval(() => {
                // Calculate remaining hours, minutes, seconds
                const remainingHours = Math.floor(timer / 3600);
                const minutes = Math.floor((timer % 3600) / 60);
                const seconds = timer % 60;
                
                // Format time display
                const timeString = `${remainingHours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                countdownElement.textContent = timeString;
                
                // Calculate progress percentage based on hours (100% divided by total hours)
                const hoursProgress = (remainingHours / hours) * 100;
                progressBar.style.width = `${hoursProgress}%`;
                
                // Calculate the actual position for the timer icon
                const containerWidth = progressBarContainer.offsetWidth;
                const iconPosition = (hoursProgress / 100) * containerWidth;
                timerIcon.style.left = `${iconPosition}px`;
                
                if (--timer < 0) {
                    clearInterval(countdown);
                    countdownElement.textContent = "DEAL ENDED";
                    progressBar.style.width = '0%';
                    timerIcon.style.left = '0px';
                }
            }, 1000);
        }

        // Set total hours (you can change this value or make it dynamic from your backend)
        const totalHours = 6; // 6 hours in this example
        startCountdown(totalHours);
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileFilterSelect = document.querySelector('.mobile-filter-select');
        const coursesLeftSidebar = document.querySelector('.product-reviews-filter');
        const filterCloseMobile = document.querySelector('.filter-close-mobile');
        
        if (mobileFilterSelect) {
            mobileFilterSelect.addEventListener('click', function() {
                if (coursesLeftSidebar) {
                    coursesLeftSidebar.style.display = 'block';
                    coursesLeftSidebar.style.zIndex = '999';
                    coursesLeftSidebar.style.background = 'white';
                    coursesLeftSidebar.style.width = '100%';
                    coursesLeftSidebar.style.height = '100vh';
                    coursesLeftSidebar.style.overflow = 'auto';
                    document.body.style.overflow = 'hidden';
                }
            });
        }
        
        if (filterCloseMobile) {
            filterCloseMobile.addEventListener('click', function() {
                if (coursesLeftSidebar) {
                    coursesLeftSidebar.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            });
        }
    });
</script>

<!-- Initialize Swiper -->
<script>
    var swiper = new Swiper(".mySwiper", {
        spaceBetween: 10,
        slidesPerView: 5,
        freeMode: true,
        watchSlidesProgress: true,
        breakpoints: {
            300: {
                slidesPerView: 3,
            },
            531: {
                slidesPerView: 3,
            },
            768: {
                slidesPerView: 4,
            },
            993: {
                slidesPerView: 5,
            },
        }
    });
    var swiper2 = new Swiper(".mySwiper2", {
        spaceBetween: 10,
        navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
        },
        thumbs: {
        swiper: swiper,
        },
    });
</script>

<script>
    document.getElementById('decrease-quantity').addEventListener('click', function() {
        var quantity = document.getElementById('quantity');
        if (parseInt(quantity.value) > 1) {
            quantity.value = parseInt(quantity.value) - 1;
        }
    });

    document.getElementById('increase-quantity').addEventListener('click', function() {
        var quantity = document.getElementById('quantity');
        quantity.value = parseInt(quantity.value) + 1;
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var swiper4 = new Swiper(".products-slider", {
            spaceBetween: 10,
            loop: true,
            centeredSlides: true,
            pagination: {
                el: ".swiper-pagination",
            },
            breakpoints: {
                320: {
                    slidesPerView: 1,
                },
                576: {
                    slidesPerView: 1,
                },
                    767: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 5,
                },
            },
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInput = document.getElementById('quantity');
        const decreaseBtn = document.getElementById('decrease-quantity');
        const increaseBtn = document.getElementById('increase-quantity');
        const addToCartBtn = document.querySelector('.addToCartProduct');

        function updateQuantity(value) {
            quantityInput.value = value;
            if(addToCartBtn) {
                addToCartBtn.setAttribute('data-quantity', value);
            }
        }

        decreaseBtn.addEventListener('click', function() {
            const currentValue = parseInt(quantityInput.value);
            if(currentValue > 1) {
                updateQuantity(currentValue - 1);
            }
        });

        increaseBtn.addEventListener('click', function() {
            const currentValue = parseInt(quantityInput.value);
            updateQuantity(currentValue + 1);
        });

        // Initialize add to cart button with current quantity
        if(addToCartBtn) {
            addToCartBtn.setAttribute('data-quantity', quantityInput.value);
        }
    });
</script>
@push('script')
{{-- <script src="{{ asset('frontend/assets/js/course/addToCart.js') }}"></script> --}}
@endpush
@endpush


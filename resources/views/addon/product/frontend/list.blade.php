@extends('frontend.layouts.app')
@section('meta')
@php
$metaData = getMeta('product');
@endphp

<meta name="description" content="{{ __($metaData['meta_description']) }}">
<meta name="keywords" content="{{ __($metaData['meta_keyword']) }}">

<!-- Open Graph meta tags for social sharing -->
<meta property="og:type" content="Learning">
<meta property="og:title" content="{{ __($metaData['meta_title']) }}">
<meta property="og:description" content="{{ __($metaData['meta_description']) }}">
<meta property="og:image" content="{{ __($metaData['og_image']) }}">
<meta property="og:url" content="{{ url()->current() }}">

<meta property="og:site_name" content="{{ __(get_option('app_name')) }}">

<!-- Twitter Card meta tags for Twitter sharing -->
<meta name="twitter:card" content="Learning">
<meta name="twitter:title" content="{{ __($metaData['meta_title']) }}">
<meta name="twitter:description" content="{{ __($metaData['meta_description']) }}">
<meta name="twitter:image" content="{{ __($metaData['og_image']) }}">
<link rel="stylesheet" href="{{ asset('addon/product/css/ecommerce-product.css') }}">
<link rel="stylesheet" href="{{ asset('addon/product/css/price_range_style.css') }}">
@endsection
@section('content')
<div class="course-list">

    <x-frontend.partials.header
        title="Our Featured Products"
        subtitle="We found {{ $total_products }} products available for you"
        :breadcrumbs="[
        ['text' => 'Home', 'url' => url('/')],
        ['text' => 'Products']
    ]"
        margin-top="lg-mt-10" 
        height="h-24 md-h-30 lg-h-35"
        height2="h-15 sm-h-21 md-h-19"
    />
    <!-- Page Header End -->

    <!-- All Products Area Start -->
    <div class="all-products-area pb-0">
        <div class="home-page-container">

            <div class="row" style="width: 100%;">
                {{-- left list area --}}
                <div class="col-md-4 col-lg-3 col-xl-3 coursesLeftSidebar">
                    <div class="justify-content-between align-items-center mobile-view">
                        <a href="{{ url('/') }}" class="navbar-img-mobile">
                            <img src="{{ getImageFile(get_option('app_logo')) }}" alt="Logo">
                        </a>
                        <svg class="filter-close-mobile" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g opacity="0.3" clip-path="url(#clip0_651_1092)">
                                <path d="M21.3334 10.6667C21.0834 10.4167 20.7443 10.2763 20.3908 10.2763C20.0372 10.2763 19.6981 10.4167 19.4481 10.6667L16.0001 14.1147L12.5521 10.6667C12.3006 10.4238 11.9638 10.2894 11.6142 10.2924C11.2646 10.2955 10.9302 10.4357 10.683 10.6829C10.4358 10.9301 10.2956 11.2645 10.2925 11.6141C10.2895 11.9637 10.4239 12.3005 10.6668 12.552L14.1148 16L10.6668 19.448C10.4239 19.6995 10.2895 20.0363 10.2925 20.3859C10.2956 20.7355 10.4358 21.0699 10.683 21.3171C10.9302 21.5643 11.2646 21.7045 11.6142 21.7076C11.9638 21.7106 12.3006 21.5762 12.5521 21.3333L16.0001 17.8853L19.4481 21.3333C19.6996 21.5762 20.0364 21.7106 20.386 21.7076C20.7356 21.7045 21.07 21.5643 21.3172 21.3171C21.5644 21.0699 21.7046 20.7355 21.7077 20.3859C21.7107 20.0363 21.5763 19.6995 21.3334 19.448L17.8854 16L21.3334 12.552C21.5834 12.302 21.7238 11.9629 21.7238 11.6093C21.7238 11.2558 21.5834 10.9167 21.3334 10.6667Z" fill="black" />
                                <path d="M16 0C12.8355 0 9.74207 0.938384 7.11088 2.69649C4.4797 4.45459 2.42894 6.95345 1.21793 9.87707C0.00693258 12.8007 -0.309921 16.0177 0.307443 19.1214C0.924806 22.2251 2.44866 25.0761 4.6863 27.3137C6.92394 29.5514 9.77487 31.0752 12.8786 31.6926C15.9823 32.3099 19.1993 31.9931 22.1229 30.7821C25.0466 29.5711 27.5454 27.5203 29.3035 24.8891C31.0616 22.2579 32 19.1645 32 16C31.9954 11.7579 30.3082 7.69095 27.3087 4.69136C24.3091 1.69177 20.2421 0.00458811 16 0ZM16 29.3333C13.3629 29.3333 10.7851 28.5513 8.5924 27.0863C6.39975 25.6212 4.69078 23.5388 3.68161 21.1024C2.67245 18.6661 2.4084 15.9852 2.92287 13.3988C3.43734 10.8124 4.70722 8.43661 6.57192 6.57191C8.43662 4.70721 10.8124 3.43733 13.3988 2.92286C15.9852 2.40839 18.6661 2.67244 21.1025 3.68161C23.5388 4.69078 25.6212 6.39974 27.0863 8.5924C28.5514 10.7851 29.3333 13.3629 29.3333 16C29.3295 19.535 27.9235 22.9242 25.4238 25.4238C22.9242 27.9235 19.535 29.3295 16 29.3333Z" fill="black" />
                            </g>
                            <defs>
                                <clipPath id="clip0_651_1092">
                                    <rect width="32" height="32" fill="white" />
                                </clipPath>
                            </defs>
                        </svg>
                    </div>
                    <form action="#" class="form-search-mobile">
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon1">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g opacity="0.3" clip-path="url(#clip0_651_1078)">
                                        <path d="M17.6268 13.8499C20.7909 10.6858 20.7909 5.53727 17.6268 2.37311C14.4627 -0.791056 9.31424 -0.791056 6.15014 2.37311C3.46578 5.05747 3.05923 9.0498 4.92941 12.1674C4.92941 12.1674 5.06374 12.3928 4.88231 12.5741L0.741918 16.7145C-0.0821909 17.5385 -0.278377 18.6908 0.45244 19.4218L0.578269 19.5475C1.30909 20.2784 2.46146 20.0823 3.2855 19.2582L7.4172 15.1265C7.60733 14.9363 7.83263 15.0707 7.83263 15.0707C10.9502 16.9408 14.9425 16.5343 17.6268 13.8499ZM7.64837 12.3517C5.31037 10.0137 5.31044 6.20952 7.64843 3.87153C9.98643 1.5336 13.7906 1.53354 16.1285 3.87153C18.4665 6.20946 18.4665 10.0137 16.1285 12.3517C13.7905 14.6895 9.98643 14.6895 7.64837 12.3517Z" fill="black" />
                                        <path d="M8.1663 7.62992C8.05748 7.62992 7.94686 7.60853 7.84023 7.56349C7.41429 7.38322 7.21501 6.89176 7.39528 6.46576C8.52833 3.78816 11.6285 2.5316 14.306 3.66465C14.7319 3.84492 14.9312 4.33638 14.7509 4.76239C14.5706 5.18839 14.0793 5.38754 13.6531 5.20733C11.8263 4.43431 9.71099 5.29167 8.93803 7.11848C8.8028 7.43799 8.49264 7.62992 8.1663 7.62992Z" fill="black" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_651_1078">
                                            <rect width="20" height="20" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                            <input type="text" id="search-product" class="search-product2" type="search" name="keyword" value="{{request('keyword')}}" placeholder="{{__('Search Product')}}..." aria-label="Search">
                        </div>
                    </form>
                    <div class="courses-sidebar-area">
                        <div class="courses-sidebar-section">
                            <h2 class="course-sidebar-title">
                                {{ __(' Product Categories') }}
                            </h2>
                            <ul class="course-category-filter">
                                @foreach($categories as $key => $category)
                                <li>
                                    <label for="category-{{ $category->id }}">
                                        <input class="filterCategory" type="radio" name="filterCategory" id="category-{{ $category->id }}" value="{{ $category->id }}">
                                        <span class="checkmark"></span>
                                        {{ $category->name }}
                                    </label>
                                </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="courses-sidebar-section">
                            <h2 class="course-sidebar-title">
                                {{ __('Price') }}
                            </h2>
                            <div class="time-range">
                                <div class="sliders_step1">
                                    <div id="slider-range" class="price-filter-range" name="rangeInput">
                                    </div>
                                </div>
                                <div class="range-value-box">
                                    <div class="range-value-wrap">
                                        <label for="min_price">{{ __('Min') }}:</label>

                                        <input type="number" min=0 max="10000"
                                            oninput="validity.valid||(value='0');" value="0" id="min_price"
                                            class="price-range-field border-0 p-0 w-100" />
                                    </div>
                                    <div class="range-value-wrap">
                                        <label for="max_price">{{ __('Max') }}:</label>
                                        <input type="number" min=0 max="10000"
                                            oninput="validity.valid||(value='10000');" value="10000" id="max_price"
                                            class="price-range-field border-0 p-0 w-100" />
                                    </div>

                                </div>
                            </div>
                            {{-- <ul class="course-category-filter">
                                    <li>
                                        <label for="price-all">
                                            <input class="filterTag" type="radio" name="price" id="price-all" value="price-all">
                                            <span class="checkmark"></span>
                                            All
                                        </label>
                                    </li>
                                    <li>
                                        <label for="price-50">
                                            <input class="filterTag" type="radio" name="price" id="price-50" value="price-50">
                                            <span class="checkmark"></span>
                                            Under 50 PKR
                                        </label>
                                    </li>
                                    <li>
                                        <label for="price-100">
                                            <input class="filterTag" type="radio" name="price" id="price-100" value="price-100">
                                            <span class="checkmark"></span>
                                            50 to 100 PKR
                                        </label>
                                    </li>
                                    <li>
                                        <label for="price-200">
                                            <input class="filterTag" type="radio" name="price" id="price-200" value="price-200">
                                            <span class="checkmark"></span>
                                            100 to 200 PKR
                                        </label>
                                    </li>
                                    <li>
                                        <label for="price-500">
                                            <input class="filterTag" type="radio" name="price" id="price-500" value="price-500">
                                            <span class="checkmark"></span>
                                            200 to 500 PKR
                                        </label>
                                    </li>
                                    <li>
                                        <label for="price-1000">
                                            <input class="filterTag" type="radio" name="price" id="price-1000" value="price-1000">
                                            <span class="checkmark"></span>
                                            500 to 1000 PKR
                                        </label>
                                    </li>
                                    <li>
                                        <label for="price-1001">
                                            <input class="filterTag" type="radio" name="price" id="price-1001" value="price-1001">
                                            <span class="checkmark"></span>
                                            Above 1000 PKR
                                        </label>
                                    </li>
                                </ul> --}}
                        </div>
                        <div class="courses-sidebar-section">
                            <h2 class="course-sidebar-title">
                                {{ __('Discount') }}
                            </h2>
                            <ul class="course-category-filter">
                                <li>
                                    <label for="0%-off">
                                        <input class="filterDiscount" type="radio" name="discount" id="0%-off" value="0">
                                        <span class="checkmark"></span>
                                        {{ __('0% off or more') }}
                                    </label>
                                </li>
                                <li>
                                    <label for="10%-off">
                                        <input class="filterDiscount" type="radio" name="discount" id="10%-off" value="10">
                                        <span class="checkmark"></span>
                                        {{ __('10% off or more') }}
                                    </label>
                                </li>
                                <li>
                                    <label for="25%-off">
                                        <input class="filterDiscount" type="radio" name="discount" id="25%-off" value="25">
                                        <span class="checkmark"></span>
                                        {{ __('25% off or more') }}
                                    </label>
                                </li>
                                <li>
                                    <label for="50%-off">
                                        <input class="filterDiscount" type="radio" name="discount" id="50%-off" value="50">
                                        <span class="checkmark"></span>
                                        {{ __('50% off or more') }}
                                    </label>
                                </li>
                                <li>
                                    <label for="70%-off">
                                        <input class="filterDiscount" type="radio" name="discount" id="70%-off" value="70">
                                        <span class="checkmark"></span>
                                        {{ __('70% off or more') }}
                                    </label>
                                </li>
                            </ul>
                        </div>
                        <div class="courses-sidebar-section">
                            <h2 class="course-sidebar-title">
                                {{ __('Rating') }}
                            </h2>
                            <ul class="course-category-filter">
                                <li>
                                    <label for="5start">
                                        <input class="filterRating" type="radio" name="rating" id="5start" value="5">
                                        <span class="checkmark"></span>
                                        <span class="iconify" data-icon="bi:star-fill"></span> 5 star
                                    </label>
                                </li>
                                <li>
                                    <label for="4start">
                                        <input class="filterRating" type="radio" name="rating" id="4start" value="4">
                                        <span class="checkmark"></span>
                                        <span class="iconify" data-icon="bi:star-fill"></span> 4 star or above
                                    </label>
                                </li>
                                <li>
                                    <label for="3start">
                                        <input class="filterRating" type="radio" name="rating" id="3start" value="3">
                                        <span class="checkmark"></span>
                                        <span class="iconify" data-icon="bi:star-fill"></span> 3 star or above
                                    </label>
                                </li>
                                <li>
                                    <label for="2start">
                                        <input class="filterRating" type="radio" name="rating" id="2start" value="2">
                                        <span class="checkmark"></span>
                                        <span class="iconify" data-icon="bi:star-fill"></span> 2 star or above
                                    </label>
                                </li>
                                <li>
                                    <label for="1start">
                                        <input class="filterRating" type="radio" name="rating" id="1start" value="1">
                                        <span class="checkmark"></span>
                                        <span class="iconify" data-icon="bi:star-fill"></span> 1 star or above
                                    </label>
                                </li>

                                <li>
                                    <label for="0start">
                                        <input class="filterRating" type="radio" name="rating" id="0start" value="0">
                                        <span class="checkmark"></span>
                                        <span class="iconify" data-icon="bi:star-fill"></span> 0 star or above
                                    </label>
                                </li>

                            </ul>
                        </div>

                        @if (!$tags->isEmpty())
                        <div class="courses-sidebar-section">
                            <h2 class="course-sidebar-title">
                                {{ __('Tags') }}
                            </h2>
                            <ul class="course-category-filter tag-filters">
                                @foreach($tags as $tag)
                                <li>
                                    <label for="tag-{{ $tag->id }}">
                                        <input class="filterTag" type="radio" name="filterTag" id="tag-{{ $tag->id }}" value="{{ $tag->id }}">
                                        {{ $tag->name }}
                                    </label>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <button class="apply">Apply</button>

                    </div>
                </div>


                {{-- left list area --}}

                {{-- product table area  --}}
                <div class="col-md-12 col-lg-9 col-xl-9 show-all-course-area-wrap pe-0 !ps-6" id="appendCourse">
                    <div class="courses-filter-wrap">
                        <form action="#" class="mx-2">
                            <div class="input-group">

                                <input class="form-control me-2 searchCourse search-product2" id="search-product" type="search" name="keyword"
                                    value="{{request('keyword')}}" placeholder="{{__('Search Product')}}..."
                                    aria-label="Search">
                                <span class="input-group-text" id="basic-addon1">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.0625 15.625C12.6869 15.625 15.625 12.6869 15.625 9.0625C15.625 5.43813 12.6869 2.5 9.0625 2.5C5.43813 2.5 2.5 5.43813 2.5 9.0625C2.5 12.6869 5.43813 15.625 9.0625 15.625Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M13.7031 13.7031L17.5 17.5" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                            </div>
                        </form>

                        <div class="filter-select">
                            <label for="filterSortBy"> Filter by: </label>
                            <select id="filterSortBy" name="filterSortBy" class="form-control filterSortBy" aria-label="Default select example">
                                <option value="1" selected>All</option>
                                <option value="2">Newest</option>
                                <option value="3">Oldet</option>
                            </select>
                        </div>
                        <div class="mobile-filter-select d-flex">
                            <svg class="sidebar-svg" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22.5 10.5H1.5C0.671578 10.5 0 11.1716 0 12C0 12.8284 0.671578 13.5 1.5 13.5H22.5C23.3284 13.5 24 12.8284 24 12C24 11.1716 23.3284 10.5 22.5 10.5ZM1.5 6.50003H22.5C23.3284 6.50003 24 5.82845 24 5.00003C24 4.17161 23.3284 3.50003 22.5 3.50003H1.5C0.671578 3.50003 0 4.17161 0 5.00003C0 5.82845 0.671578 6.50003 1.5 6.50003ZM22.5 17.5H1.5C0.671578 17.5 0 18.1716 0 19C0 19.8284 0.671578 20.5 1.5 20.5H22.5C23.3284 20.5 24 19.8284 24 19C24 18.1716 23.3284 17.5 22.5 17.5Z" fill="black" />
                            </svg>
                        </div>
                    </div>
                    <div class="row product-section-wrap !px-0" id="appendProduct">
                        {{-- single product area --}}
                        {{-- @foreach ($products as $product)
                            <div class="col-sm-12 col-md-6 !mx-0 col-lg-4 col-xl-4 col-lg-6">
                                @include('addon.product.partials.product-card')
                            </div>
                            @endforeach --}}


                        @forelse($products as $product)
                        <div class="col-sm-12 col-md-6 !mx-0 col-xl-4 col-lg-5 ">
                            @include('addon.product.partials.product-card')
                        </div>
                        <!-- Course item end -->
                        @empty
                        <div class="no-course-found text-center">
                            <img src="{{ asset('frontend/assets/img/empty-data-img.png') }}" alt="img" class="img-fluid">
                            <h5 class="mt-3">{{ __('Products Not Found') }}</h5>
                        </div>
                        @endforelse


                    </div>
                    <div class="row">
                        {{$products->links()}}
                    </div>
                </div>
                {{-- product table area  --}}
            </div>

        </div>
    </div>
</div>
<!-- All Products Area End -->
<input type="hidden" id="search-route" value="{{ route('lms_product.frontend.list') }}">

@endsection
@push('script')
{{-- <script src="{{ asset('addon/product/js/price_range_script.js') }}"></script> --}}
<script src="{{ asset('frontend/assets/js/course/addToCart.js') }}"></script>
<script src="{{ asset('addon/product/js/filter.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileFilterSelect = document.querySelector('.mobile-filter-select');
        const coursesLeftSidebar = document.querySelector('.coursesLeftSidebar');
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
@endpush
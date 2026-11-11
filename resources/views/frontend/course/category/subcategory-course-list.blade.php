@extends('frontend.layouts.app')

@section('meta')
<meta name="description" content="{{ __($subcategory->meta_description) }}">
<meta name="keywords" content="{{ __($subcategory->meta_keywords) }}">

<!-- Open Graph meta tags for social sharing -->
<meta property="og:type" content="Learning">
<meta property="og:title" content="{{ __($subcategory->meta_title) }}">
<meta property="og:description" content="{{ __($subcategory->meta_description) }}">
<meta property="og:image" content="{{ getImageFile($subcategory->og_image) }}">
<meta property="og:url" content="{{ url()->current() }}">

<meta property="og:site_name" content="{{ __(get_option('app_name')) }}">

<!-- Twitter Card meta tags for Twitter sharing -->
<meta name="twitter:card" content="Learning">
<meta name="twitter:title" content="{{ __($subcategory->meta_title) }}">
<meta name="twitter:description" content="{{ __($subcategory->meta_description) }}">
<meta name="twitter:image" content="{{ getImageFile($subcategory->og_image) }}">
@endsection

@section('content')

<div class="course-list">

    <x-frontend.partials.header
        title="{!! __($subcategory->name) !!}"
        subtitle="We found {{ $courses->count() }} courses available for you"
        :breadcrumbs="[
        ['text' => 'Home', 'url' => url('/')],
        ['text' => 'Courses'],
        ['text' => __($subcategory->name)]
    ]"
        margin-top="lg-mt-10"
        height="h-25 md-h-26 lg-h-35"
        height2="h-17 sm-h-21 md-h-19" />

    <!-- Course Single Page Header Start -->
    {{-- <header class="page-banner-header gradient-bg position-relative">
            <div class="section-overlay">
                <div class="container">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12">
                            <div class="page-banner-content text-center">
                                <h3 class="page-banner-heading text-white pb-15 cat-text">{{ __($subcategory->name) }}</h3>

    <!-- Breadcrumb Start-->
    <nav aria-label="breadcrumb" class="cat-text">
        <ol class="breadcrumb justify-content-center">
            <li class="breadcrumb-item font-14"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
            <li class="breadcrumb-item font-14"><a href="#">{{ __('Subcategory') }}</a></li>
            <li class="breadcrumb-item font-14 active" aria-current="page">{{ __($subcategory->name) }}</li>
        </ol>
    </nav>
    <!-- Breadcrumb End-->
</div>
</div>
</div>
</div>
</div>
</header> --}}
<!-- Course Single Page Header End -->

<!-- Courses Page Area Start -->
{{-- <section class="courses-page-area section-t-space bg-white">
            <div class="home-page-container">
                <div class="row shop-content" style="width: 100%">

                    <!-- Courses Sidebar start-->
                @include('frontend.course.render-sidebar-filter-category-part')
                <!-- Courses Sidebar End-->

                    <!-- Show all course area start-->
                    <div class="col-md-8 col-lg-9 col-xl-9 show-all-course-area-wrap">
                        <div class="show-all-course-area mt-0">

                            <!-- all courses grid Start-->
                            <div id="loading" class="no-course-found text-center d-none">
                                <div id="inner-status"><img src="{{ asset('frontend/assets/img/loader.svg') }}" alt="img" /></div>
</div>
@include('frontend.course.render-course-list')
<!-- all courses grid End-->

</div>
</div>
<!-- Show all course area End-->
</div>
</div>
</section> --}}



<section class="courses-page-area section-t-space bg-white">
    <div class="home-page-container">
        <div class="row shop-content" style="width: 100%">
            <!-- Courses Sidebar start-->
            @include('frontend.course.render-sidebar-filter-category-part')
            <!-- Courses Sidebar End-->
            <!-- Show all course area start-->
            <div class="col-md-12 col-lg-9 col-xl-9 show-all-course-area-wrap">
                <!-- <div class="show-all-course-area"> -->
                <!-- all courses grid Start-->
                <!-- <div id="loading" class="no-course-found text-center d-none">
                                <div id="inner-status"><img src="{{ asset('frontend/assets/img/loader.svg') }}" alt="img" /></div>
                            </div> -->

                <div id="course-container">
                    @include('frontend.course.render-course-list')
                </div>
                <!-- all courses grid End-->

                <!-- </div> -->
            </div>
            <!-- Show all course area End-->
        </div>
    </div>
</section>
<!-- Courses Page Area End -->

</div>

<!-- some important hidden id for filter.js -->
<input type="hidden" class="sub_category_id" value="{{ $subcategory->id }}">
<input type="hidden" class="route" value="{{ route('getFilterCourse') }}">
<input type="hidden" class="fetch-data-route" value="{{ route('course.fetch-data') }}">
@endsection
@push('script')
<script>
    var paginateRoute = "{{ route('courses') }}"
</script>
<script src="{{ asset('frontend/assets/js/course/filter.js') }}"></script>
<script src="{{ asset('frontend/assets/js/course/addToCart.js') }}"></script>
<script src="{{ asset('frontend/assets/js/course/addToWishlist.js') }}"></script>
@endpush
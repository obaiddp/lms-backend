@extends('frontend.layouts.app')

@section('meta')
<meta name="description" content="{{ __($category->meta_description) }}">
<meta name="keywords" content="{{ __($category->meta_keywords) }}">

<!-- Open Graph meta tags for social sharing -->
<meta property="og:type" content="Learning">
<meta property="og:title" content="{{ __($category->meta_title) }}">
<meta property="og:description" content="{{ __($category->meta_description) }}">
<meta property="og:image" content="{{ getImageFile($category->og_image) }}">
<meta property="og:url" content="{{ url()->current() }}">

<meta property="og:site_name" content="{{ __(get_option('app_name')) }}">

<!-- Twitter Card meta tags for Twitter sharing -->
<meta name="twitter:card" content="Learning">
<meta name="twitter:title" content="{{ __($category->meta_title) }}">
<meta name="twitter:description" content="{{ __($category->meta_description) }}">
<meta name="twitter:image" content="{{ getImageFile($category->og_image) }}">

@endsection
@section('content')

<div class="course-list">
    <x-frontend.partials.header
        title="{!! __($category->name) !!}"
        subtitle="We found {{ $courses->count() }} courses available for you"
        :breadcrumbs="[
        ['text' => 'Home', 'url' => url('/')],
        ['text' => 'Courses'],
        ['text' => __($category->name)]
    ]"
        margin-top="lg-mt-10"
        height="h-24 md-h-28 lg-h-35"
        height2="h-15 sm-h-21 md-h-19" />

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
<input type="hidden" class="category_id" value="{{ $category->id }}">
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
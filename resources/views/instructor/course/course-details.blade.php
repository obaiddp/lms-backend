<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
@extends('frontend.layouts.app') @php $relation = getUserRoleRelation($course->user); @endphp @section('meta')
<meta name="description" content="{{ __($course->meta_description) }}">
<meta name="keywords" content="{{ __($course->meta_keywords) }}">
<!-- Open Graph meta tags for social sharing -->
<meta property="og:type" content="Learning">
<meta property="og:title" content="{{ __($course->meta_title) }}">
<meta property="og:description" content="{{ __($course->meta_description) }}">
<meta property="og:image" content="{{ getImageFile($course->og_image) }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="{{ __(get_option('app_name')) }}">
<!-- Twitter Card meta tags for Twitter sharing -->
<meta name="twitter:card" content="Learning">
<meta name="twitter:title" content="{{ __($course->meta_title) }}">
<meta name="twitter:description" content="{{ __($course->meta_description) }}">
<meta name="twitter:image" content="{{ getImageFile($course->og_image) }}">
<style>
    th,
    td {
        max-width: 3rem !important;
        min-width: 3rem !important;
        text-align: center !important;
        height: 3rem;
    }

    #next,
    #previous,
    #back {
        font-size: 40px;
    }

    table {
        caption-side: bottom;
        border-collapse: collapse;
        margin: 0 auto !important;
    }

    table tr:hover {
        background-color: #ffffff !important;
    }

    .form-control:focus {
        border-color: var(--border-color) !important;
    }

    .input-group-text {
        background-color: #ffffff !important;
        border: 1px solid var(--border-color) !important;
        border-left: 0;
    }

    .form-svg.form-control,
    .form-select {
        border-right: 0 !important;
    }

    .form-country.form-control,
    .form-select {
        padding: 0 !important;
    }

    .selectize-input {
        border: 0 !important;
        min-height: 3rem !important;
        padding: 14.6px 12px !important;
        box-shadow: none !important;
    }

    .selectize-input>input {
        padding: 0.4rem !important;
        line-height: 1.5 !important;
        font-size: 1rem !important;
    }

    .selectize-dropdown {
        height: 8rem !important;
        overflow-y: scroll;
        box-shadow: none !important;
        border: 1px solid var(--border-color) !important;
    }

    .selectize-dropdown-content {
        max-height: 8rem !important;
    }

    input::placeholder {
        color: #a0a3aa;
    }

    .selectize-control.single .selectize-input {
        background: white !important;
    }

    .selectize-dropdown [data-selectable].option {
        font-size: 14px !important;
    }

    .selectize-control.single .selectize-input,
    .selectize-dropdown.single {
        overflow: hidden !important;
        font-size: 15px;
        border-bottom: 1px solid var(--border-color) !important;
    }

    .selectize-dropdown .selected {
        background-color: #ffffff !important;
        color: #000000 !important;
    }

    .iti {
        width: 100% !important;
    }

    #phone {
        width: 100% !important;
    }

    .btn:disabled {
        display: none !important;
    }

    .selectize-control.plugin-clear_button.single .clear {
        display: none !important;
    }

    .multiple-select {
        list-style-type: none;
        margin: 1rem auto !important;
        padding: 0;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
    }

    .multiple-select li {
        float: left;
        margin: 0 5px 0 0;
        width: 48%;
        height: 50px;
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
    }

    .multiple-select label,
    .multiple-select input {
        display: block;
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
    }

    .multiple-select input[type="checkbox"] {
        opacity: 0.011;
        z-index: 100;
        cursor: pointer;
    }

    .multiple-select input[type="checkbox"]:checked+label {
        background: var(--theme-color);
        color: white;
    }

    .multiple-select input[type="radio"] {
        opacity: 0.011;
        z-index: 100;
        cursor: pointer;
    }

    .multiple-select input[type="radio"]:checked+label {
        background: var(--theme-color);
        color: white;
    }

    .multiple-select label {
        padding: 5px;
        border: 1px solid #CCC;
        cursor: pointer;
        z-index: 90;
        font-size: 16px;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        border-radius: 2px;
    }

    .multiple-select label:hover {
        background: #DDD;
    }

    .date-picker {
        cursor: pointer;
        text-align: center;
        padding: 10px;
        border-radius: 50%;
        transition: background-color 0.3s, border 0.3s;
    }

    .slot {
        border: 1px solid var(--theme-color);
        width: 100%;
        height: 3rem;
        margin: 0.2rem auto;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 1rem;
        font-weight: 500;
        color: var(--theme-color);
        transition: background-color 0.3s;
    }

    .slot:hover {
        cursor: pointer;
        background-color: var(--theme-color);
        color: white;
    }

    .date-picker.selected {
        background-color: var(--theme-color);
        color: white;
        border-radius: 50%;
    }

    .date-picker:hover {
        background-color: #e9ecef;
    }

    #back {
        position: absolute;
        font-size: 50px;
        top: -1rem;
    }

    #slots {
        position: relative;
    }

    .course-details-header-img{
        /* height: 30rem !important; */
    }

    .course-details-header-img2{
        margin-top: 20px !important;
        padding: 0 !important;
        /* height: 34rem !important;
        min-height: 34rem !important; */
    }

</style> @endsection 
@section('content3') 

<div class="bg-page">
    <!-- Course Single Page Header Start -->
    <header class="course-single-page-header gradient-bg position-relative course-details-header-img">
        <div class="section-overlay align-items-end course-details-header-img2" style="background: transparent !important;" > 
            <picture>
                <!-- For small screens -->
                <source media="(max-width: 991px)" srcset="{{asset($course->cover_image)}}">
                <!-- For tablets and desktops -->
                <source media="(min-width: 992px)" srcset="{{asset($course->cover_image)}}">
                <!-- Default for desktops -->
                <img class="bg-image-cat" src="{{asset($course->cover_image)}}" alt="Course Cover">
            </picture>

            <div class="container">
                <div class="row" style="height: 16.8rem !important;">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-8 d-flex align-items-start">
                    <div style="width: 100% !important">
                            {{-- <div class="course-rating d-flex align-items-center text-white cat-text">
                                <span
                                    class="font-medium font-14 me-2">{{ number_format($course->average_rating, 1) }}</span>
                                <ul class="rating-list d-flex align-items-center me-2">
                                    @include('frontend.course.render-course-rating') </ul>
                                <span class="rating-count font-14 me-3">({{ $total_user_review }})</span>
                                <span class="rating-count font-14">{{ @$course->orderItems->count() }}
                                    {{ __('Students') }}</span>
                            </div> --}}
                                
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Course Single Page Header End -->
    <!-- Course Single Details Area Start -->
    <section class="course-single-details-area before-login-purchase-course-details">
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-12 col-lg-8">
                    <div class="course-single-details-left-content bg-white">
                        <h3 class="page-banner-heading pb-20 cat-text" style="padding: 16px 30px">{{ __($course->title) }}</h3>
                            <p class="instructor-name-certificate font-medium font-14 cat-text" style="padding: 16px 30px">
                                <span class="text-decoration-underline">{{ @$course->user->$relation->name }}</span>
                                @if(get_instructor_ranking_level(@$course->user->badges)) |
                                {{ get_instructor_ranking_level(@$course->user->badges) }} @endif
                            </p>
                        <!-- Tab panel nav list -->
                        <div class="course-tab-nav-wrap course-details-tab-nav-wrap d-flex justify-content-between">
                            <ul class="nav nav-tabs tab-nav-list border-0" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="Overview-tab" data-bs-toggle="tab" href="#Overview"
                                        role="tab" aria-controls="Overview"
                                        aria-selected="true">{{ __('Overview') }}</a>
                                </li> @if($course->course_type == COURSE_TYPE_GENERAL) <li class="nav-item"
                                    role="presentation">
                                    <a class="nav-link" id="Curriculum-tab" data-bs-toggle="tab" href="#Curriculum"
                                        role="tab" aria-controls="Curriculum"
                                        aria-selected="false">{{ __('Curriculum') }}</a>
                                </li> @endif <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="Discussion-tab" data-bs-toggle="tab" href="#Discussion"
                                        role="tab" aria-controls="Discussion"
                                        aria-selected="false">{{ __('Discussion') }}</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="Review-tab" data-bs-toggle="tab" href="#Review" role="tab"
                                        aria-controls="Review" aria-selected="false">{{ __('Review') }}</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="Instructor-tab" data-bs-toggle="tab" href="#Instructor"
                                        role="tab" aria-controls="Review"
                                        aria-selected="false">{{ (count($course->course_instructors->where('status', STATUS_APPROVED)) > 1) ? __('Instructors') : __('Instructor') }}</a>
                                </li>
                            </ul>
                        </div>
                        <!-- Tab panel nav list -->
                        <!-- Tab Content-->
                        <div class="tab-content" id="myTabContent">
                            @include('frontend.course.partial.partial-overview-tab')
                            @include('frontend.course.partial.partial-curriculum-tab')
                            @include('frontend.course.partial.partial-discussion-tab')
                            @include('frontend.course.partial.partial-review-tab')
                            @include('frontend.course.partial.partial-instructor-tab') </div>
                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-4">
                    <div class="course-single-details-right-content">
                        <div class="course-info-box bg-white">
                            <div
                                class="video-area-left position-relative d-flex align-items-center justify-content-center">
                                <div class="course-info-video-img"><img src="{{ getImageFile($course->image) }}"
                                        alt="video" class="img-fluid"></div> @if($course->intro_video_check == 1 &&
                                getVideoFile($course->video)) <button type="button" class="play-btn position-absolute"
                                    data-bs-toggle="modal" data-bs-target="#newVideoPlayerModal">
                                    <img src="{{ asset('frontend/assets/img/icons-svg/play.svg') }}" alt="play">
                                </button> @endif @if($course->intro_video_check == 2 && $course->youtube_video_id)
                                <button type="button" class="play-btn position-absolute" data-bs-toggle="modal"
                                    data-bs-target="#newVideoPlayerModal">
                                    <img src="{{ asset('frontend/assets/img/icons-svg/play.svg') }}" alt="play">
                                </button> @endif
                            </div> @if($course->learner_accessibility == 'paid') <div
                                class="course-price-box d-flex justify-content-between align-items-center mt-30 mb-30">
                                <?php
                                $startDate = date('d-m-Y H:i:s', strtotime(@$course->promotionCourse->promotion->start_date));
                                $endDate = date('d-m-Y H:i:s', strtotime(@$course->promotionCourse->promotion->end_date));
                                $percentage = @$course->promotionCourse->promotion->percentage;
                                $discount_price = number_format($course->price - (($course->price * $percentage) / 100), 2);
                            ?> @if(now()->gt($startDate) && now()->lt($endDate)) <div>
                                    <h4 class="d-flex align-items-center mb-1"> @if(get_currency_placement() == 'after')
                                        {{ $discount_price }} {{ get_currency_symbol() }} @else
                                        {{ get_currency_symbol() }} {{ $discount_price }} @endif <span
                                            class="text-decoration-line-through fw-normal font-16 color-gray ps-3">
                                            @if(get_currency_placement() == 'after') {{ $course->price }}
                                            {{ get_currency_symbol() }} @else {{ get_currency_symbol() }}
                                            {{ $course->price }} @endif </span>
                                    </h4>
                                    <span class="course-left-duration color-deep-orange font-14"> <span
                                            class="iconify me-2 font-18" data-icon="clarity:alarm-clock-line"></span>
                                        {{ getLeftDuration($startDate, $endDate) }}
                                        {{ __('Left at this Price') }}!</span>
                                </div>
                                <div
                                    class="price-off font-12 font-medium color-hover d-flex align-items-center justify-content-center">
                                    {{ @$percentage }}% {{ __("off") }}</div> @elseif($course->price <= $course->
                                    old_price) <div>
                                        <h4 class="d-flex align-items-center mb-1"> @if(get_currency_placement() ==
                                            'after') {{ $course->price }} {{ get_currency_symbol() }} @else
                                            {{ get_currency_symbol() }} {{ $course->price }} @endif <span
                                                class="text-decoration-line-through fw-normal font-16 color-gray ps-3">
                                                @if(get_currency_placement() == 'after') {{ $course->old_price }}
                                                {{ get_currency_symbol() }} @else {{ get_currency_symbol() }}
                                                {{ $course->old_price }} @endif </span>
                                        </h4>
                                    </div> @php $percentage = number_format(((($course->old_price - $course->price) /
                                    $course->old_price) * 100), 2); @endphp <div
                                        class="price-off font-12 font-medium color-hover d-flex align-items-center justify-content-center">
                                        {{ $percentage }}% {{ __("off") }}</div> @else <div>
                                        <h4 class="d-flex align-items-center mb-1"> @if(get_currency_placement() ==
                                            'after') {{ $course->price }} {{ get_currency_symbol() }} @else
                                            {{ get_currency_symbol() }} {{ $course->price }} @endif </h4>
                                    </div> @endif
                            </div> @elseif($course->learner_accessibility == 'free') <div
                                class="course-price-box d-flex justify-content-between align-items-center mt-30 mb-30">
                                <div>
                                    <h4 class="d-flex align-items-center mb-1"> {{ __('Free') }} </h4>
                                </div>
                            </div> @endif <div class="course-includes-box course-includes-box-top">
                                <ul class="pb-30">
                                    <li class="d-flex justify-content-between">
                                        <div>
                                            <span class="iconify" data-icon="bytesize:clock"></span>
                                            <span>{{ __('Course Duration') }}</span>
                                        </div> @if($course->course_type == COURSE_TYPE_GENERAL) <div>
                                            {{ @$course->VideoDuration }}</div> @else <div>
                                            {{ @$course->scorm_course->duration }}</div> @endif
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        <div>
                                            <span class="iconify" data-icon="carbon:increase-level"></span>
                                            <span>{{ __('Course Level') }}</span>
                                        </div>
                                        <div>{{ @$course->difficultyLevel->name }}</div>
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        <div>
                                            <span class="iconify" data-icon="heroicons-outline:users"></span>
                                            <span>{{ __('Student Enrolled') }}</span>
                                        </div>
                                        <div>{{ @$total_course_students }}</div>
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        <div>
                                            <span class="iconify" data-icon="cil:language"></span>
                                            <span>{{ __('Language') }}</span>
                                        </div>
                                        <div>{{ @$course->language->name }}</div>
                                    </li>
                                </ul>
                            </div> 
                            @if($course_exits == 'enrolled') 
                                {{-- <a
                                href="{{ route('student.my-course.show', $course->slug) }}"
                                class="theme-btn theme-button1 theme-button3 w-100 mb-30 ">
                                {{ __('Go to Course') }}
                                <i data-feather="arrow-right"></i>
                                </a>  --}}
                                <a href="https://portal.cefonlineacademy.com/mycourses/{{$course->slug}}"
                                    class="theme-btn theme-button1 theme-button3 w-100 mb-30 ">
                                    {{ __('Go to Course') }}
                                </a>
                            @elseif($course_exits == 'cartList') 
                            <button
                                class="theme-btn theme-button1 theme-button3 w-100 mb-30">
                                {{ __('Added to Cart') }}
                            </button> 
                            @else 
                                @if($course->status == STATUS_APPROVED)
                                    @if ($authRole == 3 || $authRole == null) 
                                        @if ($course->course_type == 1 || $course->course_type == 2)    <!--Self Learning -->
                                            <a data-placement="left" href="javascript:void(0);"
                                            class="theme-btn theme-button1 theme-button3 w-100 mb-30 addToCart"
                                            data-course_id="{{ $course->id }}" data-route="{{ route('student.addToCart') }}"
                                            data-coursetype="2" data-login_route="{{ route('login') }}/2/{{$course->slug}}">
                                            <span class="msgInfoChange">{{ __('Enroll the Course') }} <i
                                                    data-feather="arrow-right"></i></span>
                                            </a>
                                        @else       <!--Live -->
                                              <a  data-placement="left" href="javascript:void(0);" style="width: 100% !important;"  class="theme-btn theme-button1 theme-button3 w-100 mb-30 enrollCourse" data-course_id="{{ $course->id }}"
                                            data-route="{{ route('student.enrollCourse') }}"
                                            data-coursetype="1"
                                            data-login_route="{{ route('login') }}/1/{{$course->slug}}">
                                            <span class="msgInfoChange">{{ __('Enroll Live Course') }} <i data-feather="arrow-right"></i></span> 
                                            </a>
                                        @endif
                                        
                            {{-- <button class="theme-btn theme-button1 theme-button3 w-100 mb-30 addToCart" data-course_id="{{ $course->id }}"
                            data-route="{{ route('student.addToCart') }}" data-login_route="{{ route('login')
                            }}/2/{{$course->slug}}"> <span class="msgInfoChange">{{ __('Enroll the Course') }} <i
                                    data-feather="arrow-right"></i></span>
                            </button> --}}

                            <!-- Modal -->
                            <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog"> 
                                    <div style=" max-height: 90vh; overflow-y: auto;" class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">{{__('Sign In')}}</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="col-md-12">
                                                <form id="loginForm2" method="POST" action="{{ route('login-modal') }}">
                                                    @csrf <h5 class="mb-1 text-center">{{__('Sign In')}}</h5>
                                                    <p class="font-14 mb-30 text-center">{{__('New User')}} ? <a
                                                            data-placement="left" href="javascript:void(0);"
                                                            class="fw-500 w-100 mb-30 signUp"
                                                            data-course_id="{{ $course->id }}"
                                                            data-route="{{ route('sign-up-as-student-modal') }}/2/{{$course->slug}}"
                                                            data-coursetype="2" data-courseslug="{{$course->slug}}"
                                                            data-login_route="{{ route('sign-up-as-student-modal') }}/2/{{$course->slug}}">
                                                            <span class="msgInfoChange">{{__('Sign Up')}} <i
                                                                    style="width: 1rem; height: 1rem;"
                                                                    data-feather="arrow-right"></i></span>
                                                        </a>
                                                        {{-- <a href="{{route('sign-up-as-student')}}" class="theme-btn
                                                        theme-button1 theme-button3 font-12 fw-bold w-40">{{__('Sign
                                                        Up')}}</a> --}}
                                                    </p>
                                                    <input type="hidden" id="coursetype" name="coursetype">
                                                    <div class="row mb-30">
                                                        <div class="col-md-12">
                                                            <label
                                                                class="label-text-title color-heading font-medium font-16 mb-3">{{__('Email or Phone')}}</label>
                                                            <input required type="text" name="email"
                                                                value="{{old('email')}}" class="form-control email"
                                                                placeholder="{{ __('Type your email or phone number') }}">
                                                            @if ($errors->has('email')) <span class="text-danger"><i
                                                                    class="fas fa-exclamation-triangle"></i>
                                                                {{ $errors->first('email') }}</span> @endif
                                                        </div>
                                                    </div>
                                                    <div class="row mb-30">
                                                        <div class="col-md-12">
                                                            <label
                                                                class="label-text-title color-heading font-medium font-16 mb-3">{{__('Password')}}</label>
                                                            <div class="form-group mb-0 position-relative">
                                                                <input required class="form-control password"
                                                                    name="password" value="{{old('password')}}"
                                                                    placeholder="*********" type="password">
                                                                <span class="toggle cursor fas fa-eye pass-icon" style="position: absolute;top: 1.1rem;right: 0.8rem;"></span>
                                                            </div> @if ($errors->has('password')) <span
                                                                class="text-danger"><i
                                                                    class="fas fa-exclamation-triangle"></i>
                                                                {{ $errors->first('password') }}</span> @endif
                                                        </div>
                                                    </div>
                                                    <div id="div-response">
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-md-12">
                                                            <button id="login_now" type="submit"
                                                                class="theme-btn theme-button1 theme-button3 font-15 fw-bold w-100">{{__('Sign In')}}</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Modal -->












                            <div class="modal fade" id="signUpModal" tabindex="-1" aria-labelledby="signUpModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div style=" max-height: 90vh; overflow-y: auto;" class="modal-content">
                                        <div class="modal-body p-0">
                                            <!-- Registration Area Start -->
                                            <section class="register-page p-0 bg-gray">
                                                <div
                                                    class="container-fluid p-0 min-h-full bg-white w-90 mx-auto shadow-md flex flex-col justify-content-center align-items-center px-4 py-3">
                                                    <h4 class="text-center">Let's get your Journey Started</h4>
                                                    <div class="main">
                                                        <ul class="progressBar my-2">
                                                            <li>
                                                                <div class="step st first active">
                                                                    <p>1</p>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                        height="16" fill="currentColor"
                                                                        class="bi bi-check-lg" viewBox="0 0 16 16">
                                                                        <path
                                                                            d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z" />
                                                                    </svg>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="step st second">
                                                                    <p>2</p>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                        height="16" fill="currentColor"
                                                                        class="bi bi-check-lg" viewBox="0 0 16 16">
                                                                        <path
                                                                            d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z" />
                                                                    </svg>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="step st third">
                                                                    <p>3</p>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                        height="16" fill="currentColor"
                                                                        class="ms-1 mt-1 bi bi-check-lg" viewBox="0 0 16 16">
                                                                        <path
                                                                            d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z" />
                                                                    </svg>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="step st fourth">
                                                                    <p>4</p>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                        height="16" fill="currentColor"
                                                                        class="ms-1 mt-1 bi bi-check-lg" viewBox="0 0 16 16">
                                                                        <path
                                                                            d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z" />
                                                                    </svg>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                        <form id="multiStepForm" class="w-100 px-4">
                                                            <input type="hidden" name="token" id="token"
                                                                value="{{ csrf_token() }}">
                                                            <input type="hidden" name="new_student_id"
                                                                id="new_student_id" value="">
                                                            <input type="hidden" name="new_trial_id" id="new_trial_id"
                                                                value="">
                                                            <input type="hidden" name="check_status" id="check_status"
                                                                value="step_1_create">
                                                            <input type="hidden" name="type_st" id="type_st"
                                                                value="{{$typeCourse}}">
                                                            <input type="hidden" name="slug" id="slug" value="">
                                                            <div class="step-content mt-3 active" id="step-1">
                                                                <h6 class="text-center mb-4">Your personal details</h6>
                                                                <div class="input-group mb-3">
                                                                    <input name="fname" id="fname" type="text"
                                                                        class="form-control form-svg"
                                                                        placeholder="First Name" aria-label="First Name"
                                                                        aria-describedby="FirstName" required>
                                                                    <span class="input-group-text" id="FirstName">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            width="24" height="24" fill="#a3a6ad"
                                                                            class="bi bi-person" viewBox="0 0 16 16">
                                                                            <path
                                                                                d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z" />
                                                                        </svg>
                                                                    </span>
                                                                    <span class="error-message" id="fname_error"></span>
                                                                </div>
                                                                <div class="input-group mb-3">
                                                                    <input name="lname" id="lname" type="text"
                                                                        class="form-control form-svg"
                                                                        placeholder="Last Name" aria-label="Last Name"
                                                                        aria-describedby="LastName" required>
                                                                    <span class="input-group-text" id="LastName">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            width="24" height="24" fill="#a3a6ad"
                                                                            class="bi bi-person" viewBox="0 0 16 16">
                                                                            <path
                                                                                d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z" />
                                                                        </svg>
                                                                    </span>
                                                                    <span class="error-message" id="lname_error"></span>
                                                                </div>
                                                                <div class="input-group mb-3">
                                                                    <input type="email" name="email" id="email"
                                                                        class="form-control form-svg"
                                                                        placeholder="Email" aria-label="Email"
                                                                        aria-describedby="Email">
                                                                    <span class="input-group-text" id="Email">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            width="22" height="22" fill="#a3a6ad"
                                                                            class="bi bi-envelope" viewBox="0 0 16 16">
                                                                            <path
                                                                                d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z" />
                                                                        </svg>
                                                                    </span>
                                                                    <span class="error-message" id="email_error"></span>
                                                                </div>
                                                                <div class="input-group mb-3">
                                                                    <input type="password" name="password" id="password"
                                                                        class="form-control password"
                                                                        placeholder="*********">
                                                                    <span class="toggle cursor fas fa-eye pass-icon" style="position: absolute;top: 1.1rem;right: 0.8rem;"></span>
                                                                    <span class="error-message"
                                                                        id="password_error"></span>
                                                                </div>
                                                                <div class="input-group mb-3">
                                                                    <select class="form-control time-zone" name="time_zone"
                                                                        id="time_zone" required>
                                                                        <option value="">{{ __("Select Time Zone") }}
                                                                        </option>
                                                                        @if (!empty($timeZonesArray))
                                                                        @foreach ($timeZonesArray as $key => $timezone)
                                                                            <option value="{{$key}}"
                                                                            @if(@$student->user->time_zone == $key) 
                                                                                selected 
                                                                            @endif
                                                                            >
                                                                            {{$timezone}}
                                                                            </option>
                                                                        @endforeach
                                                                    @endif

                                                                    </select>
                                                                    <span class="error-message"
                                                                        id="time_zone_error"></span>
                                                                </div>
                                                                <div class="input-group mb-3">
                                                                    <input id="phone" class="form-control" type="tel"
                                                                        name="phone" />
                                                                    <span class="error-message" id="phone_error"></span>
                                                                </div>
                                                                <input type="hidden" name="area_code" id="area_code"
                                                                    value="">
                                                                <div class="input-group mb-3">
                                                                    <select id="country-id" name="country_id"
                                                                        class="form-control"
                                                                        placeholder="Select Country">
                                                                        <option value="">Select Country</option>     
                                                                        @if (!$countries->isEmpty())
                                                                        @foreach ($countries as $country)
                                                                            <option value="{{$country->id}}">{{$country->country_name}}</option>
                                                                        @endforeach
                                                                    @endif
                                                                    </select>
                                                                    <span class="error-message"
                                                                        id="country_id_error"></span>
                                                                </div>
                                                                <div class="input-group mb-3">
                                                                    <input id="referral_code" name="referral_code"
                                                                        type="text" class="form-control"
                                                                        placeholder="Referral/Promo Code"
                                                                        aria-label="Referral/Promo Code"
                                                                        aria-describedby="Code">
                                                                    <span class="error-message"
                                                                        id="referral_code_error"></span>
                                                                </div>
                                                                <div style="display: flex;width: 100%;justify-content: space-around;align-items: baseline;">
                                                                    <div>
                                                                        <label style="font-size:15px" for="1">Live</label>
                                                                    <input class="st_type" name="st_type"
                                                                        type="checkbox" id="1" value="1"
                                                                        @if($typeCourse==1) checked @endif>
                                                                    </div>
                                                                    <div>
                                                                    <label style="margin-left:15px ; font-size:15px"
                                                                        for="2">Self Learning</label>
                                                                    <input class="st_type" name="st_type"
                                                                        type="checkbox" id="2" value="2"
                                                                        @if($typeCourse==2) checked @endif>
                                                                    <span class="error-message"
                                                                        id="st_type_error"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="step-content mt-3" id="step-2">
                                                                {{-- <h6>What will you use CEF Online for?</h6>
                    <ul class="multiple-select">
                        <li>
                            <input class="lms_uses" type="checkbox" id="Quran" value="Quran" name="lms_uses" />
                            <label for="Quran">Quran</label>
                        </li>
                        <li>
                            <input class="lms_uses" type="checkbox" id="Islamic Studies" value="Islamic Studies" name="lms_uses" />
                            <label for="Islamic Studies">Islamic Studies</label>
                        </li>
                        <li>
                            <input class="lms_uses" type="checkbox" id="Arabic" value="Arabic" name="lms_uses" />
                            <label for="Arabic">Arabic</label>
                        </li>
                        <li>
                            <input class="lms_uses" type="checkbox" id="Other" value="Other" name="lms_uses" />
                            <label for="Other">Other</label>
                        </li>
                        <span class="error-message" id="lms_uses_error"></span>
                    </ul> --}}
                                                                <div class="" id="step-2-field-1">
                                                                    <h6>How many students will join?</h6>
                                                                    <ul class="multiple-select">
                                                                        <li class="w-10">
                                                                            <input type="radio" id="1" value="1"
                                                                                name="no_of_joined_students" />
                                                                            <label for="1"
                                                                                class="rounded-full">1</label>
                                                                        </li>
                                                                        <li class="w-10">
                                                                            <input type="radio" id="2" value="2"
                                                                                name="no_of_joined_students" />
                                                                            <label for="2"
                                                                                class="rounded-full">2</label>
                                                                        </li>
                                                                        <li class="w-10">
                                                                            <input type="radio" id="3" value="3"
                                                                                name="no_of_joined_students" />
                                                                            <label for="3"
                                                                                class="rounded-full">3</label>
                                                                        </li>
                                                                        <li class="w-10">
                                                                            <input type="radio" id="4" value="4"
                                                                                name="no_of_joined_students" />
                                                                            <label for="4"
                                                                                class="rounded-full">4</label>
                                                                        </li>
                                                                        <li class="w-10">
                                                                            <input type="radio" id="5" value="5"
                                                                                name="no_of_joined_students" />
                                                                            <label for="5"
                                                                                class="rounded-full">5</label>
                                                                        </li>
                                                                        <span class="error-message"
                                                                            id="no_of_joined_students_error"></span>
                                                                    </ul>
                                                                </div>
                                                                <div class="" id="step-2-field-2">
                                                                    <h6>For your evaluation, do you prefer</h6>
                                                                    <ul class="multiple-select">
                                                                        <li class="w-100">
                                                                            <input type="radio" id="Male" value="Male"
                                                                                name="preferred_teacher" />
                                                                            <label for="Male">Male Teacher</label>
                                                                        </li>
                                                                        <li class="w-100">
                                                                            <input type="radio" id="Female"
                                                                                value="Female"
                                                                                name="preferred_teacher" />
                                                                            <label for="Female">Female Teacher</label>
                                                                        </li>
                                                                        <li class="w-100">
                                                                            <input type="radio" id="Either"
                                                                                value="Either"
                                                                                name="preferred_teacher" />
                                                                            <label for="Either">Either</label>
                                                                        </li>
                                                                        <span class="error-message"
                                                                            id="preferred_teacher_error"></span>
                                                                    </ul>
                                                                </div>
                                                                <h6>How did you learn about CEF Online?</h6>
                                                                <ul class="multiple-select">
                                                                    <li>
                                                                        <input type="radio" id="A Friend"
                                                                            value="A Friend" name="learn_about_lms" />
                                                                        <label for="A Friend">A Friend</label>
                                                                    </li>
                                                                    <li>
                                                                        <input type="radio" id="Social Media"
                                                                            value="Social Media"
                                                                            name="learn_about_lms" />
                                                                        <label for="Social Media">Social Media</label>
                                                                    </li>
                                                                    <li>
                                                                        <input type="radio" id="E-Mail" value="E-Mail"
                                                                            name="learn_about_lms" />
                                                                        <label for="E-Mail">E-Mail</label>
                                                                    </li>
                                                                    <li>
                                                                        <input type="radio" id="Other" value="Other"
                                                                            name="learn_about_lms" />
                                                                        <label for="Other">Other</label>
                                                                    </li>
                                                                </ul>
                                                                <span class="error-message"
                                                                        id="learn_about_lms_error"></span>
                                                            </div>
                                                            <div class="step-content" id="step-3">
                                                                <h6 class="text-center mt-3 mb-4">Pick a date and time
                                                                    for your free trial lesson (1 hour)</h6>
                                                                <div id="wrapper" class="wrapper mb-2">
                                                                    <div class="container-calendar">
                                                                        <div
                                                                            class="d-flex justify-content-between align-items-center px-4">
                                                                            <h6 class="w-50" id="monthAndYear"></h6>
                                                                            <div class="button-container-calendar">
                                                                                {{-- <button id="previous" type="button" onclick="previous()">&#8249;</button>
                                    <button id="next" type="button" onclick="next()">&#8250;</button> --}}
                                                                                <button id="previous" type="button"
                                                                                    disabled>&#8249;</button>
                                                                                <button id="next"
                                                                                    type="button">&#8250;</button>
                                                                            </div>
                                                                        </div>
                                                                        <table class="table-calendar" id="calendar"
                                                                            data-lang="en">
                                                                            <thead id="thead-month"></thead>
                                                                            <tbody id="calendar-body"></tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" name="preferDate" id="preferDate"
                                                                value="">
                                                            <input type="hidden" name="selectedDate" id="selectedDate"
                                                                value="">
                                                            <input type="hidden" name="selectedMonth" id="selectedMonth"
                                                                value="">
                                                            <input type="hidden" name="selectedYear" id="selectedYear"
                                                                value="">
                                                            <div class="step-content" id="step-4">
                                                                <h6 class="text-center mt-3 mb-2">Select Slot</h6>
                                                                <div id="slots" class="slots mb-2 mt-4">
                                                                    <div class="w-80 mx-auto d-flex flex-col flex-wrap">
                                                                        <div class="slot">
                                                                            <label for="1:00 AM">
                                                                                <input type="radio" name="preferTime"
                                                                                    id="1:00 AM" value="1:00 AM"
                                                                                    checked> 1:00 AM </label>
                                                                        </div>
                                                                        <div class="slot">
                                                                            <label for="12:00 PM">
                                                                                <input type="radio" name="preferTime"
                                                                                    id="12:00 PM" value="12:00 PM">
                                                                                12:00 PM </label>
                                                                        </div>
                                                                        <div class="slot">
                                                                            <label for="12:15 PM">
                                                                                <input type="radio" name="preferTime"
                                                                                    id="12:15 PM" value="12:15 PM">
                                                                                12:15 PM </label>
                                                                        </div>
                                                                        <div class="slot">
                                                                            <label for="12:30 PM">
                                                                                <input type="radio" name="preferTime"
                                                                                    id="12:30 PM" value="12:30 PM">
                                                                                12:30 PM </label>
                                                                        </div>
                                                                        <div class="slot">
                                                                            <label for="11:30 PM">
                                                                                <input type="radio" name="preferTime"
                                                                                    id="11:30 PM" value="11:30 PM">
                                                                                11:30 PM </label>
                                                                        </div>
                                                                        {{-- <div class="slot">11:30 PM</div> --}}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                        <div class="button-container w-100 px-4">
                                                            <button id="nextBtn"
                                                                class="btn btn-primary w-100 my-2">Next</button>
                                                            <button id="prevBtn" class="btn btn-secondary w-100"
                                                                disabled>Previous</button>
                                                            <span id="message" style="display:none"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                            <!-- Registration Area End -->
                                        </div>
                                        {{-- <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary">Login</button>
            </div> --}}
                                    </div>
                                </div>
                            </div> 
                            @endif
                            
                            @if($course->learner_accessibility != 'free' &&
                            get_option('cashback_system_mode', 0)) <div class="alert alert-success d-flex mb-15">
                                <div class="align-items-center d-flex mr-15">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="feather feather-credit-card">
                                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                        <line x1="1" y1="10" x2="23" y2="10"></line>
                                    </svg>
                                </div>
                                <div class="ml-10">
                                    <div class="font-14 font-weight-bold ">{{ __("Get Cashback") }}</div>
                                    <div class="font-12 ">{{ __("By purchasing this product, you will get")}}
                                        @if($currencyPlacement ?? get_currency_placement() == 'after')
                                        {{calculateCashback($course->price) }}
                                        {{ $currencySymbol ?? get_currency_symbol() }} @else
                                        {{ $currencySymbol ?? get_currency_symbol() }}
                                        {{calculateCashback($course->price) }} @endif {{ __("cashback") }}.</div>
                                </div>
                            </div> @endif @else <button class="bg-warning mb-30 theme-btn w-100">
                                <span class="msgInfoChange">{{ __('Upcoming') }}</span>
                            </button> @endif @endif @if(get_option('course_gift_mode', 0)) <a
                                href="{{ route('student.course-gift', $course->uuid) }}"
                                class="alert alert-warning align-items-center border border-dark d-flex mb-30 p-2">
                                <div class="mr-15">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="feather feather-gift text-gray">
                                        <polyline points="20 12 20 22 4 22 4 12"></polyline>
                                        <rect x="2" y="7" width="20" height="5"></rect>
                                        <line x1="12" y1="22" x2="12" y2="7"></line>
                                        <path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path>
                                        <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path>
                                    </svg>
                                </div>
                                <div class="ml-5">
                                    <h4 class="font-14 font-weight-bold text-gray">{{ __("Gift this course") }}</h4>
                                    <p class="font-12 text-gray">{{ __("Send this course as a gift to your friends") }}
                                    </p>
                                </div>
                            </a> @endif <div class="course-info-box-wishlist-btns d-flex mb-30">
                                <button class="theme-btn para-color font-medium mx-2 addToWishlist"
                                    title="Add to wishlist" data-course_id="{{ $course->id }}"
                                    data-route="{{ route('student.addToWishlist') }}"><span class="iconify me-2"
                                        data-icon="bytesize:heart"></span>{{ __('Add to Wishlist') }}</button>
                                <button class="theme-btn para-color font-medium mx-2" title="Share this course"
                                    data-bs-toggle="modal" data-bs-target="#shareThisCourseModal"><span
                                        class="iconify me-2"
                                        data-icon="ci:share-outline"></span>{{ __('Share Course') }}</button>
                            </div>
                            <div class="course-includes-box mb-30">
                                <h6 class="pb-30">{{ __('This Course Includes') }}</h6>
                                <ul> @if($course->course_type == COURSE_TYPE_GENERAL) 
                                    {{-- <li>
                                        <span class="iconify" data-icon="bi:camera-video"></span>
                                        <span>{{@$course->lectures->count() > 0 ? @$course->lectures->count() : '0'}}
                                            {{ __('Total Lectures') }}</span>
                                    </li>  --}}
                                    <li>
                                        <span class="iconify" data-icon="bi:camera-video"></span>
                                        <span>{{@$course->lectures->count() > 0 ? @$course->video_duration : '0'}}
                                            {{ __('Video Lectures') }}</span>
                                    </li> 
                                    @endif
                                     <li>
                                        <span class="iconify"
                                            data-icon="healthicons:i-exam-multiple-choice-outline"></span>
                                        <span>{{ @$course->quizzes->count() }} {{ __('Quizzes') }}</span>
                                    </li>
                                    <li>
                                        <span class="iconify" data-icon="bi:book"></span>
                                        <span>{{ @$course->assignments->count() }} {{ __('Assignments') }}</span>
                                    </li>
                                    <li>
                                        <span class="iconify" data-icon="akar-icons:download"></span>
                                        <span>{{ @$course->resources->count() }}
                                            {{ __('Downloadable Resources') }}</span>
                                    </li>
                                    <li>
                                        <span class="iconify" data-icon="bytesize:clock"></span>
                                        <span>{{ (!$course->access_period) ? __('Full Lifetime Access') : $course->access_period.' '.__('days after the enrollment') }}</span>
                                    </li>
                                    <li>
                                        <span class="iconify" data-icon="lucide:award"></span>
                                        <span>{{ __('Certificate of Completion') }}</span>
                                    </li>
                                </ul>
                            </div> @if(@Auth::user() && Auth::user()->is_affiliator == AFFILIATOR &&
                            get_option('referral_status')) <div
                                class="course-info-box-affiliate-link-copy position-relative">
                                <h6 class="pb-20">Affiliate Link:</h6>
                                <div class="input-group">
                                    <input id="ref_link" class="form-control w-100" value="{{ $ref_link }}" />
                                    <button type="button" class="btn affiliate-copy-btn border-0 position-absolute"
                                        onclick="copyToClipboard('ref_link')" title="{{ __('Copy to Clipboard') }}">
                                        <i class="fa fa-copy"></i>
                                    </button>
                                </div>
                            </div> @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Course Single Details Area End -->
</div>




<!--Share This Course Modal Start-->
<div class="modal fade" id="shareThisCourseModal" tabindex="-1" aria-labelledby="shareThisCourseModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="shareThisCourseModalLabel">{{ __('Share This Course') }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="share-course-list">
                    <li><a href="http://www.facebook.com/sharer.php?u={{ route('course-details', $course->slug) }}"><span
                                class="iconify" data-icon="cib:facebook-f"></span></a></li>
                    <li><a href="https://twitter.com/share?url={{ route('course-details', $course->slug) }}"><span
                                class="iconify" data-icon="el:twitter"></span></a></li>
                    <li><a
                            href="https://www.linkedin.com/shareArticle?url={{ route('course-details', $course->slug) }}"><span
                                class="iconify" data-icon="cib:linkedin-in"></span></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!--Share This Course Modal End-->
<input type="hidden" class="courseReviewPaginateRoute" value="{{ route('frontend.reviewPaginate', $course->id) }}">
<!-- New Video Player Modal Start-->
<div class="modal fade VideoTypeModal" id="newVideoPlayerModal" tabindex="-1" aria-labelledby="newVideoPlayerModal"
    aria-hidden="true">
    <div class="modal-header border-bottom-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span class="iconify"
                data-icon="akar-icons:cross"></span></button>
    </div>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="video-player-area"> @if($course->intro_video_check == 1 && getVideoFile($course->video))
                    <!-- HTML 5 Video -->
                    <video id="player2" playsinline controls data-poster="{{ getImageFile($course->image) }}"
                        controlsList="nodownload">
                        <source src="{{ getVideoFile($course->video) }}" type="video/mp4">
                    </video> @endif @if($course->intro_video_check == 2 && $course->youtube_video_id) <div
                        class="plyr__video-embed" id="youtubePlayer2">
                        <iframe src="https://www.youtube.com/embed/{{ @$course->youtube_video_id }}" allowfullscreen
                            allowtransparency allow="autoplay">
                        </iframe>
                    </div> @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!-- New Video Player Modal End-->
<!--  Text Upload Modal Start -->
<div class="modal fade textUploadModal venoBoxTypeModal" id="textUploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-header border-bottom-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span class="iconify"
                data-icon="akar-icons:cross"></span></button>
    </div>
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-body">
                <p class="getLectureText"></p>
            </div>
        </div>
    </div>
</div>
<!-- Text Upload Modal End -->
<!-- Image Upload Modal Start -->
<div class="modal fade textUploadModal venoBoxTypeModal" id="imageUploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-header border-bottom-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span class="iconify"
                data-icon="akar-icons:cross"></span></button>
    </div>
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-body">
                <img src="" alt="" class="img-fluid getLectureImage">
            </div>
        </div>
    </div>
</div>
<!-- Image Upload Modal End -->
<!-- Slide Show Upload Modal Start-->
<div class="modal fade venoBoxTypeModal" id="slideModal" tabindex="-1" aria-hidden="true">
    <div class="modal-header border-bottom-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span class="iconify"
                data-icon="akar-icons:cross"></span></button>
    </div>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <iframe class="getLectureSlide" src="" width="100%" height="400" frameborder="0"
                    scrolling="no"></iframe>
            </div>
        </div>
    </div>
</div>
<!-- Slide Show Upload Modal End-->
<!-- Audio Player Modal Start-->
<div class="modal fade venoBoxTypeModal" id="audioPlayerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-header border-bottom-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span class="iconify"
                data-icon="akar-icons:cross"></span></button>
    </div>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <!--Audio -->
                <audio class="getLectureAudio" src="" type="audio/mp3" style="width: 550px" controls
                    controlsList="nodownload">
                </audio>
            </div>
        </div>
    </div>
</div>
<!-- Audio Player Modal End--> @endsection @push('style')
<!-- Video Player css -->
<link rel="stylesheet" href="{{ asset('frontend/assets/vendor/video-player/plyr.css') }}"> @endpush @push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/js/selectize.min.js"
    integrity="sha512-IOebNkvA/HZjMM7MxL0NYeLYEalloZ8ckak+NDtOViP7oiYzG5vn6WVXyrJDiJPhl4yRdmNAG49iuLmhkUdVsQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    $(document).ready(function () {
        $('#country-id').selectize({ create: false,sortField: 'text', searchField: 'text', onDropdownOpen: function($dropdown) { this.clear(); } });
    });
</script>

<script>
    $(document).ready(function () {
        var $select = $('#time_zone').selectize({
            create: false,
            searchField: 'text',
            onDropdownOpen: function($dropdown) {
                this.clear();
            }
        });
    });
</script>

<script>
    var currentMonth, currentYear, monthAndYear;
var months = [];
var days = [];


        function next() {
    currentYear = (currentMonth === 11) ? currentYear + 1 : currentYear;
    currentMonth = (currentMonth + 1) % 12;
    showCalendar(currentMonth, currentYear);
    $('#previous').removeAttr('disabled');
}

function previous() {
    currentYear = (currentMonth === 0) ? currentYear - 1 : currentYear;
    currentMonth = (currentMonth === 0) ? 11 : currentMonth - 1;
    showCalendar(currentMonth, currentYear);
}

            document.addEventListener("DOMContentLoaded", function() {
    function generate_year_range(start, end) {
        var years = "";
        for (var year = start; year <= end; year++) {
            years += "<option value='" + year + "'>" + year + "</option>";
        }
        return years;
    }

    today = new Date();

    currentMonth = today.getMonth();
    currentYear = today.getFullYear();
    monthAndYear = document.getElementById("monthAndYear");

    var calendar = document.getElementById("calendar");
    var lang = calendar.getAttribute('data-lang');

    var monthDefault = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    var dayDefault = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

    if (lang == "en") {
        months = monthDefault;
        days = dayDefault;
    } else if (lang == "id") {
        months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        days = ["Ming", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"];
    } else if (lang == "fr") {
        months = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
        days = ["dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi"];
    } else {
        months = monthDefault;
        days = dayDefault;
    }

    var $dataHead = "<tr>";
    for (dhead in days) {
        $dataHead += "<th data-days='" + days[dhead] + "'>" + days[dhead] + "</th>";
    }
    $dataHead += "</tr>";

    document.getElementById("thead-month").innerHTML = $dataHead;

    showCalendar(currentMonth, currentYear);

    document.getElementById("next").addEventListener("click", next);
    document.getElementById("previous").addEventListener("click", previous);

});





function showCalendar(month, year) {
          
          var selectedDate=$('#selectedDate').val();
          var selectedMonth=$('#selectedMonth').val();
          // selectedMonth = (parseInt(selectedMonth) + parseInt(1))
          var selectedYear=$('#selectedYear').val();
          var firstDay = (new Date(year, month)).getDay();
          var tbl = document.getElementById("calendar-body");
          tbl.innerHTML = "";
      
          monthAndYear.innerHTML = months[month] + " " + year;
      
          var date = 1;
          for (var i = 0; i < 6; i++) {
              var row = document.createElement("tr");
      
              for (var j = 0; j < 7; j++) {
                  if (i === 0 && j < firstDay) {
                      var cell = document.createElement("td");
                      cell.appendChild(document.createTextNode(""));
                      cell.setAttribute("data-date", "");
                      cell.setAttribute('disabled', 'true');
                      cell.className = "disabled-td";
                      row.appendChild(cell);
                  } else if (date > daysInMonth(month, year)) {
                      break;
                  } else {
                      var cell = document.createElement("td");
                      cell.setAttribute("data-date", date);
                      cell.setAttribute("data-month", month + 1);
                      cell.setAttribute("data-year", year);
                      cell.setAttribute("data-month_name", months[month]);
                
                      cell.className = "date-picker";
                      cell.innerHTML = "<span>" + date + "</span>";
                    
                      if (selectedDate === date  && selectedYear === year && selectedMonth === month) {
                     
                          // alert(selectedDate+'-----'+date)
                          // alert(selectedYear+'-----'+year)
                          // alert(selectedMonth+'-----'+month)
                          var dateValue = year+'-'+ (parseInt(month)+ parseInt(1))+'-'+date;
                          $('#preferDate').val(dateValue)
                          cell.className = "date-picker selected";
                      }
                      else if (date === today.getDate() && year === today.getFullYear() && month === today.getMonth()) {
                          // alert(selectedDate+'-----'+date)
                          // alert(selectedYear+'-----'+year)
                          // alert(selectedMonth+'-----'+month)
                          var dateValue = year+'-'+ (parseInt(month)+ parseInt(1))+'-'+date;
                          $('#preferDate').val(dateValue)
                          cell.className = "date-picker selected";
                      }
                      else if(year < today.getFullYear()){
                          cell.setAttribute('disabled', 'true');
                          cell.className = "disabled-td";
                      }
                      else if(year === today.getFullYear() && month < today.getMonth()){
                          cell.setAttribute('disabled', 'true');
                          cell.className = "disabled-td";
                      }
                      else if(month === today.getMonth() && date < today.getDate()){
                          cell.setAttribute('disabled', 'true');
                          cell.className = "disabled-td";
                      }
      
                      row.appendChild(cell);
                      date++;
                  }
              }
      
              tbl.appendChild(row);
          }
      }
      


      function daysInMonth(month, year) {
        // Create a date object for the 1st of the next month and get the last day of the previous month
        const date = new Date(year, month + 1, 0);
        // Return the day of the month for the last day of the month
        return date.getDate();
    }





    $(document).ready(function () {
    $("#calendar-body").on('click', 'tr td', function() {
//Use .text() as td doesn't have method .val()
//Empty first time as the td:first has no text until clicked.

$("#calendar-body tr td").removeClass('selected')

var date = $(this).attr('data-date');
var month = $(this).attr('data-month');
var year = $(this).attr('data-year');

if(date === ""){

$(this).attr('disabled', 'true');
$(this).addClass('disabled-td')
}
else if(year < today.getFullYear()){
$(this).attr('disabled', 'true');
$(this).addClass('disabled-td')
}
else if(year === today.getFullYear() && month < today.getMonth()){
$(this).attr('disabled', 'true');
$(this).addClass('disabled-td')
}
else if(month === today.getMonth() && date < today.getDate()){
$(this).attr('disabled', 'true');
$(this).addClass('disabled-td')
}
else{
var dateValue = year+'-'+ (parseInt(month))+'-'+date;
$('#preferDate').val(dateValue)
$('#selectedDate').val(date)
$('#selectedMonth').val(month)
$('#selectedYear').val(year)
$(this).addClass('date-picker selected');
}




});



$.ajax({
    url: 'https://restcountries.com/v3.1/all',
    type: 'GET',
    dataType: 'json',
    success: function(data) {
        var options = data.map(function(country) {
            return {
                name: country.name.common,
                code: country.cca2
            };
        });

        options.sort(function(a, b) {
            return a.name.localeCompare(b.name);
        });

        $('#country-selector').selectize({
            plugins: ['restore_on_backspace', 'clear_button'],
            delimiter: ' - ',
            persist: false,
            maxItems: 1,
            valueField: 'code',
            labelField: 'name',
            searchField: ['name'],
            options: options,
            create: false
        });
    },
    error: function(xhr, status, error) {
        console.error('Error fetching country data:', status, error);
    }
});
});






















document.addEventListener("DOMContentLoaded", () => {
        const phoneInputField = document.querySelector("#phone");
        var countryCodeInput = document.querySelector("#area_code");
        const phoneInput = window.intlTelInput(phoneInputField, {
            utilsScript:
            "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        });
        phoneInputField.addEventListener('countrychange', function(e) {
            var countryData = phoneInput.getSelectedCountryData();
            countryCodeInput.value = countryData.dialCode;
        });

        // Step navigation logic
        const first = document.querySelector(".first");
        const second = document.querySelector(".second");
        const third = document.querySelector(".third");
        const fourth = document.querySelector(".fourth");
        const steps = [first, second, third,fourth];
        const stepContents = document.querySelectorAll(".step-content");
        let currentStep = 0;

        function updateButtons() {
            document.getElementById('prevBtn').disabled = currentStep === 0;
            document.getElementById('nextBtn').innerText = currentStep === steps.length - 1 ? 'Finish' : 'Next';
        }

        function updateStep() {
            steps.forEach((step, index) => {
                if (index <= currentStep) {
                    step.classList.add("active");
                } else {
                    step.classList.remove("active");
                }
            });
            stepContents.forEach((content, index) => {
                if (index === currentStep) {
                    content.classList.add("active");
                } else {
                    content.classList.remove("active");
                }
            });
            updateButtons();
        }

        function nextStep() {
        


        
            var checkStatus =  $('#check_status').val();
       
           if(checkStatus == 'step_1_create'){
                var form = document.getElementById('multiStepForm');
                var formFields = form.querySelectorAll('input[name],select[name]');
                var fieldsToSubmit = ['st_type','fname','lname','email', 'password','time_zone','country_id','area_code','phone','referral_code','token']; // List the specific fields to validate
                var phoneValue = document.getElementById('phone');
             
                if(phoneValue != ""){
                    var fieldsToValidate = ['st_type','fname','lname','email', 'password','time_zone','country_id','phone']; // List the specific fields to validate
                }
                else{
                    var fieldsToValidate = ['st_type','fname','lname','email', 'password','time_zone','country_id']; // List the specific fields to validate
                }
            
                let isValid = true;
                fieldsToValidate.forEach(fieldName => {
            var field = form.querySelector(`[name="${fieldName}"]`);
            if (field) {
                field.classList.remove('error');
                var errorElement = document.getElementById(`${fieldName}_error`);
                if (errorElement) {
                    errorElement.textContent = '';
                }
            }
            });

        
            fieldsToValidate.forEach(fieldName => {var field = form.querySelector(`[name="${fieldName}"]`);
            if (field && !field.value.trim()) {
                field.classList.add('error');
                var errorElement = document.getElementById(`${fieldName}_error`);
                if (errorElement) {
                    errorElement.textContent = `${fieldName.replace('_', ' ')} is required`;
                }
                isValid = false;
            }
            else if (fieldName === 'email' && field.value.trim()) {
                // Email format validation
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(field.value.trim())) {
                    field.classList.add('error');
                    var errorElement = document.getElementById('email_error');
                    if (errorElement) {
                        errorElement.textContent = 'Invalid email format';
                    }
                    isValid = false;
                }
            }
            });

            fieldsToValidate.forEach(fieldName => {
                var fields = form.querySelectorAll(`[name="${fieldName}"]`);
                if (fieldName === 'st_type') {
                var checked = Array.from(fields).some(field => field.checked);
             
                if (!checked) {
                    fields.forEach(field => field.classList.add('error'));
                    var errorElement = document.getElementById(`${fieldName}_error`);
                    if (errorElement) {
                        errorElement.textContent = 'At least one type must be checked';
                    }
                    isValid = false;
                }
            }
            });

        if(isValid){
            var email = $('input[name="email"]').val()

            $("#nextBtn").attr("disabled", "disabled");
$("#message").css('color','green');
$("#message").css('font-size','20px');
$("#message").css('text-align','center');
$("#message").text("Please Wait!!!!");
$("#message").css('display','block');

            $.ajax({
                    type: "POST",
                    url: "{{route('check-student-email')}}",
                    data: {
                        email : email,
                    },
                    dataType: "JSON",
                    headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "Accept-Language": 'en',
                    },
                    success: function (response) {
                        if (!response.unique) {
                            $('input[name="email"]').addClass('error');
                                var errorElement = document.getElementById('email_error');
                                if (errorElement) {
                                    errorElement.textContent = 'Email is already taken';
                                }
                                isValid = false;

                              $("#nextBtn").removeAttr("disabled");
                              $("#message").text("");
                              $("#message").css('display','none');
                        }
                        else{
                            var phone_value = $('input[name="phone"]').val()

                            $("#nextBtn").attr("disabled", "disabled");
$("#message").css('color','green');
$("#message").css('font-size','20px');
$("#message").css('text-align','center');
$("#message").text("Please Wait!!!!");
$("#message").css('display','block');

                            $.ajax({
                    type: "POST",
                    url: "{{route('check-student-phone')}}",
                    data: {
                        phone : phone_value,
                    },
                    dataType: "JSON",
                    headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "Accept-Language": 'en',
                    },
                    success: function (response) {
                        if (!response.unique) {
                            $('input[name="phone"]').addClass('error');
                                var errorElement = document.getElementById('phone_error');
                                if (errorElement) {
                                    errorElement.textContent = 'Phone is already taken';
                                }
                                isValid = false;
                            $("#nextBtn").removeAttr("disabled");
                              $("#message").text("");
                              $("#message").css('display','none');
                        }
                        else{
            if (isValid) {

$("#nextBtn").attr("disabled", "disabled");
$("#message").css('color','green');
$("#message").css('font-size','20px');
$("#message").css('text-align','center');
$("#message").text("Please Wait!!!!");
$("#message").css('display','block');
                let formData = {};
                fieldsToSubmit.forEach(fieldName => {
                var field = form.querySelector(`[name="${fieldName}"]`);
                if (field) {
                    formData[fieldName] = field.value;
                }
                });
                var st_values = [];
                $('.st_type').each(function(){
                    var $this = $(this);
                    if ($this.is(':checked')) {
                    st_values.push($this.val());
                    }
                    });
                formData['st_type'] = st_values;
                console.log(formData)
                $.ajax({
                    type: "POST",
                    url: "{{route('store.sign-up-as-student-step1')}}",
                    data: formData,
                    dataType: "JSON",
                    headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "Accept-Language": 'en',
                    },
                    success: function (response) {
                        if(response.status){
                            console.log(formData);
                              $('#check_status').val("step_2_update");
                              $('#new_student_id').val(response.student_id);
                              $('#type_st').val(response.stType);
                              $("#nextBtn").removeAttr("disabled");
                              $("#message").text("");
                              $("#message").css('display','none');
                         
                            if (currentStep < steps.length - 1) {
                                if(response.stType == 2){ //Only Self Learning Selected
                                    $("#step-2-field-1").css('display','none');
                                    $("#step-2-field-2").css('display','none');
                                    $(".third").css('display','none');
                                    $(".fourth").css('display','none');
                                }   
                                else{
                                    $("#step-2-field-1").css('display','block');
                                    $("#step-2-field-2").css('display','block');
                                    $(".third").css('display','block');
                                    $(".fourth").css('display','block');
                                }
                                 currentStep++;
                                 updateStep();
                            }
                        }
                    }
                });
            
            }
            else {
            console.log('Specific fields have errors');
                                $("#nextBtn").removeAttr("disabled");
                              $("#message").text("");
                              $("#message").css('display','none');
            }
                        }
                    }
                });
                        }
                    }
                });
            } 
            else{
                console.log('Specific fields have errors'); 
                                $("#nextBtn").removeAttr("disabled");
                              $("#message").text("");
                              $("#message").css('display','none');
            }

        }
        else if(checkStatus == 'step_1_edit'){
            var studentID =  $('#new_student_id').val();
                var form = document.getElementById('multiStepForm');
                var fieldsToSubmit = ['st_type','fname','lname','email', 'password','time_zone','country_id','area_code','phone','referral_code','token','new_student_id']; // List the specific fields to validate
                var phoneValue = document.getElementById('phone');
             
                if(phoneValue != ""){
                    var fieldsToValidate = ['st_type','fname','lname','email', 'password','time_zone','country_id','phone']; // List the specific fields to validate
                }
                else{
                    var fieldsToValidate = ['st_type','fname','lname','email', 'password','time_zone','country_id']; // List the specific fields to validate
                }
            
                let isValid = true;
                fieldsToValidate.forEach(fieldName => {
            var field = form.querySelector(`[name="${fieldName}"]`);
            if (field) {
                field.classList.remove('error');
                const errorElement = document.getElementById(`${fieldName}_error`);
                if (errorElement) {
                    errorElement.textContent = '';
                }
            }
            });

            fieldsToValidate.forEach(fieldName => {
            var field = form.querySelector(`[name="${fieldName}"]`);
            if (field && !field.value.trim()) {
                field.classList.add('error');
                var errorElement = document.getElementById(`${fieldName}_error`);
                if (errorElement) {
                    errorElement.textContent = `${fieldName.replace('_', ' ')} is required`;
                }
                isValid = false;
            }
            else if (fieldName === 'email' && field.value.trim()) {
                // Email format validation
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(field.value.trim())) {
                    field.classList.add('error');
                    var errorElement = document.getElementById('email_error');
                    if (errorElement) {
                        errorElement.textContent = 'Invalid email format';
                    }
                    isValid = false;
                }
               
            }
            });

            fieldsToValidate.forEach(fieldName => {
                var fields = form.querySelectorAll(`[name="${fieldName}"]`);
                if (fieldName === 'st_type') {
                var checked = Array.from(fields).some(field => field.checked);
             
                if (!checked) {
                    fields.forEach(field => field.classList.add('error'));
                    var errorElement = document.getElementById(`${fieldName}_error`);
                    if (errorElement) {
                        errorElement.textContent = 'At least one type must be checked';
                    }
                    isValid = false;
                }
            }
            });

            if (isValid) { 

                $("#nextBtn").attr("disabled", "disabled");
$("#message").css('color','green');
$("#message").css('font-size','20px');
$("#message").css('text-align','center');
$("#message").text("Please Wait!!!!");
$("#message").css('display','block');

            var email = $('input[name="email"]').val();
                $.ajax({
                    type: "POST",
                    url: "{{route('check-student-email-update')}}",
                    data: {
                        email : email,
                        given_student_id : studentID,
                    },
                    dataType: "JSON",
                    headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "Accept-Language": 'en',
                    },
                    success: function (response) {
                        if (!response.unique) {
                            $('input[name="email"]').addClass('error');
                                var errorElement = document.getElementById('email_error');
                                if (errorElement) {
                                    errorElement.textContent = 'Email is already taken';
                                }
                                isValid = false;
                                $("#nextBtn").removeAttr("disabled");
                              $("#message").text("");
                              $("#message").css('display','none');
                        }
                        else{

                            $("#nextBtn").attr("disabled", "disabled");
$("#message").css('color','green');
$("#message").css('font-size','20px');
$("#message").css('text-align','center');
$("#message").text("Please Wait!!!!");
$("#message").css('display','block');


                            var phone_value = $('input[name="phone"]').val();
                $.ajax({
                    type: "POST",
                    url: "{{route('check-student-phone-update')}}",
                    data: {
                        phone : phone_value,
                        given_student_id : studentID,
                    },
                    dataType: "JSON",
                    headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "Accept-Language": 'en',
                    },
                    success: function (response) {
                        if (!response.unique) {
                            $('input[name="phone"]').addClass('error');
                                var errorElement = document.getElementById('phone_error');
                                if (errorElement) {
                                    errorElement.textContent = 'Phone is already taken';
                                }
                                isValid = false;
                                $("#nextBtn").removeAttr("disabled");
                              $("#message").text("");
                              $("#message").css('display','none');
                        }
                        else{
                            if (isValid) {
$("#nextBtn").attr("disabled", "disabled");
$("#message").css('color','green');
$("#message").css('font-size','20px');
$("#message").css('text-align','center');
$("#message").text("Please Wait!!!!");
$("#message").css('display','block');
                let formData = {};
                fieldsToSubmit.forEach(fieldName => {
                var field = form.querySelector(`[name="${fieldName}"]`);
                if (field) {
                    formData[fieldName] = field.value;
                }
                });
                var st_values = [];
                $('.st_type').each(function(){
                    var $this = $(this);
                    if ($this.is(':checked')) {
                    st_values.push($this.val());
                    }
                    });
                formData['st_type'] = st_values;
                console.log(formData)
                $.ajax({
                    type: "POST",
                    url: "{{route('update.sign-up-as-student-step1')}}",
                    data: formData,
                    dataType: "JSON",
                    headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "Accept-Language": 'en',
                    },
                    success: function (response) {
                        if(response.status){
                            console.log(formData);
                              $('#check_status').val("step_2_update");
                              $('#new_student_id').val(response.student_id);
                              $('#type_st').val(response.stType);
                              $("#nextBtn").removeAttr("disabled");
                              $("#message").text("");
                              $("#message").css('display','none');
                         
                            if (currentStep < steps.length - 1) {
                                if(response.stType == 2){ //Only Self Learning Selected
                                 
                                    $("#step-2-field-1").css('display','none');
                                    $("#step-2-field-2").css('display','none');
                                    $(".third").css('display','none');
                                    $(".fourth").css('display','none');
                                }   
                                else{
                                  
                                    $("#step-2-field-1").css('display','block');
                                    $("#step-2-field-2").css('display','block');
                                    $(".third").css('display','block');
                                    $(".fourth").css('display','block');
                                }
                                 currentStep++;
                                 updateStep();
                            }
                        }
                    }
                });
            
            }
            else {
            console.log('Specific fields have errors');
                                $("#nextBtn").removeAttr("disabled");
                              $("#message").text("");
                              $("#message").css('display','none');
            }
                        }
                    }
                });
                        }
                    }
                });
            }
            else{
                console.log('Specific fields have errors');
                                $("#nextBtn").removeAttr("disabled");
                              $("#message").text("");
                              $("#message").css('display','none');
            }

        }
        else if(checkStatus == 'step_2_update'){
            var updated_st_type = $('#type_st').val();
            var updated_slug = $('#slug').val();
                var form = document.getElementById('multiStepForm');
                var fieldsToSubmit = ['no_of_joined_students','preferred_teacher', 'learn_about_lms','token','new_student_id']; // List the specific fields to validate
                // var fieldsToValidate = ['lms_uses','no_of_joined_students','preferred_teacher', 'learn_about_lms']; // List the specific fields to validate
                var fieldsToValidate = ['no_of_joined_students','preferred_teacher', 'learn_about_lms']; // List the specific fields to validate
        
                let isValid = true;
                fieldsToValidate.forEach(fieldName => {
            var field = form.querySelector(`[name="${fieldName}"]`);
            if (field) {
                field.classList.remove('error');
                const errorElement = document.getElementById(`${fieldName}_error`);
                if (errorElement) {
                    errorElement.textContent = '';
                }
            }
            });

            fieldsToValidate.forEach(fieldName => {
            var fields = form.querySelectorAll(`[name="${fieldName}"]`);
         
            // if (fieldName === 'lms_uses') {
            //     var checked = Array.from(fields).some(field => field.checked);
             
            //     if (!checked) {
            //         fields.forEach(field => field.classList.add('error'));
            //         var errorElement = document.getElementById(`${fieldName}_error`);
            //         if (errorElement) {
            //             errorElement.textContent = 'At least one use must be selected';
            //         }
            //         isValid = false;
            //     }
            // }
            // else if(fieldName === 'no_of_joined_students'){
            if(fieldName === 'no_of_joined_students' && updated_st_type != 2){
                var isChecked = Array.from(fields).some(radio => radio.checked);
                if (!isChecked) {
                    var errorElement = document.getElementById(`${fieldName}_error`);
                    if (errorElement) {
                        errorElement.textContent = 'Select no of students';
                    }
                    isValid = false;
                }
            }
            // else if(fieldName === 'preferred_teacher'){
            else if(fieldName === 'preferred_teacher' && updated_st_type != 2){
                var isChecked = Array.from(fields).some(radio => radio.checked);
    
                if (!isChecked) {
                    var errorElement = document.getElementById(`${fieldName}_error`);
                    if (errorElement) {
                        errorElement.textContent = 'Select preferred teacher type';
                    }
                    isValid = false;
                }
            }
            else if(fieldName === 'learn_about_lms'){
                var isChecked = Array.from(fields).some(radio => radio.checked);
                if (!isChecked) {
                    var errorElement = document.getElementById(`${fieldName}_error`);
                    if (errorElement) {
                        errorElement.textContent = 'Select one reason';
                    }
                    isValid = false;
                }
            }
            });


            if (isValid) {
$("#nextBtn").attr("disabled", "disabled");
$("#prevBtn").attr("disabled", "disabled");
$("#message").css('color','green');
$("#message").css('font-size','20px');
$("#message").css('text-align','center');
$("#message").text("Please Wait!!!!");
$("#message").css('display','block');
                let formData = {};
                var values = [];
                // $('.lms_uses').each(function(){
                //     var $this = $(this);
                //     if ($this.is(':checked')) {
                //     values.push($this.val());
                //     }
                //     });
                // formData['lms_uses'] = values;
                // formData['lms_uses'] = $('input[name="lms_uses[]"]').val()
                if(updated_st_type != 2){
                formData['no_of_joined_students'] = $('input[name="no_of_joined_students"]:checked').val()
                formData['preferred_teacher'] = $('input[name="preferred_teacher"]:checked').val()
                formData['st_type_updated'] = updated_st_type
                }
                else{
                    formData['no_of_joined_students'] = null
                    formData['preferred_teacher'] = null
                    formData['st_type_updated'] = updated_st_type
                }

                formData['learn_about_lms'] = $('input[name="learn_about_lms"]:checked').val()
                formData['new_student_id'] = $('input[name="new_student_id"]').val()
                formData['slug_updated'] = updated_slug
              
                console.log(formData)
                $.ajax({
                    type: "POST",
                    url: "{{route('update.sign-up-as-student-step2')}}",
                    data: formData,
                    dataType: "JSON",
                    headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "Accept-Language": 'en',
                    },
                    success: function (response) {
                        if(response.status){
                            if(response.stTypeUpdated == 2){
                                $("#nextBtn").attr("disabled", "disabled");
                                $("#prevBtn").attr("disabled", "disabled");
                                $("#message").css('color','green');
                                $("#message").css('font-size','20px');
                                $("#message").css('text-align','center');
                                $("#message").text(response.message);
                                $("#message").css('display','block'); 
                                setTimeout(() => {
                                    $('#signUpModal').modal('hide');
                                    // $('#loginModal').modal('show');
                                    window.location.href = response.url;
                                }, 3000);
                            }
                            else{
                                var studentTrial =  $('#new_trial_id').val();
                                if(studentTrial){
                                    $('#check_status').val("step_3_update_free_trial");
                                    $('#new_trial_id').val(studentTrial);
                                }
                                else{
                                    $('#check_status').val("step_3_create_free_trial");
                                }
                                $('#new_student_id').val(response.student_id);

                                $("#nextBtn").removeAttr("disabled");
                                $("#prevBtn").removeAttr("disabled");
                                $("#message").text("");
                                $("#message").css('display','none');
                                if (currentStep < steps.length - 1) {
                                    currentStep++;
                                    updateStep();
                                }
                                } 
                        }
                    }
                });
            
            }
            else {
              
            console.log('Specific fields have errors');
            $("#nextBtn").removeAttr("disabled");
            $("#prevBtn").removeAttr("disabled");
            $("#message").text("");
            $("#message").css('display','none');
            }

        }
        else if(checkStatus == 'step_3_create_free_trial'){
                var studentID =  $('#new_student_id').val();
                var form = document.getElementById('multiStepForm');
                var fieldsToSubmit = ['no_of_joined_students','preferred_teacher', 'learn_about_lms','token','new_student_id']; // List the specific fields to validate
                var fieldsToValidate = ['no_of_joined_students','preferred_teacher', 'learn_about_lms']; // List the specific fields to validate
                let isValid = true;
             
                $('#check_status').val("step_4_update_free_trial");
                if (currentStep < steps.length - 1) {
                                 currentStep++;
                                 updateStep();
                }    

        }
        else if(checkStatus == 'step_3_update_free_trial'){
                var form = document.getElementById('multiStepForm');
                var fieldsToSubmit = ['no_of_joined_students','preferred_teacher', 'learn_about_lms','token','new_student_id','new_trial_id']; // List the specific fields to validate
                var fieldsToValidate = ['no_of_joined_students','preferred_teacher', 'learn_about_lms']; // List the specific fields to validate
                let isValid = true;
                $('#check_status').val("step_4_update_free_trial");
                if (currentStep < steps.length - 1) {
                                 currentStep++;
                                 updateStep();
                }    
        }
        else if(checkStatus == 'step_4_update_free_trial'){
        
            var updated_slug = $('#slug').val();
            $("#nextBtn").attr("disabled", "disabled");
$("#prevBtn").attr("disabled", "disabled");
$("#message").css('color','green');
$("#message").css('font-size','20px');
$("#message").css('text-align','center');
$("#message").text("Please Wait!!!!");
$("#message").css('display','block');

            console.log($('input[name="new_student_id"]').val())
            console.log($('input[name="preferDate"]').val())
            console.log($('input[name="preferTime"]:checked').val())
            var formData = new FormData();
            formData.set('new_student_id', $('input[name="new_student_id"]').val());
            formData.set('preferred_date', $('input[name="preferDate"]').val());
            formData.set('preferred_time', $('input[name="preferTime"]:checked').val());
            formData.set('slug_updated', updated_slug);
            // console.log(formData)
            $.ajax({
                    method: "POST",
                    processData: false,
    contentType: false,
    cache: false,
                    url: "{{route('update.sign-up-as-student-step4')}}",
                    data: formData,
                    dataType: "JSON",
                    headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "Accept-Language": 'en',
                    },
                    success: function (response) {
                        if(response.status){
                            $("#nextBtn").attr("disabled", "disabled");
                            $("#prevBtn").attr("disabled", "disabled");
$("#message").css('color','green');
$("#message").css('font-size','20px');
$("#message").css('text-align','center');
$("#message").text(response.message);
$("#message").css('display','block'); 
setTimeout(() => {
    $('#signUpModal').modal('hide');
    // $('#loginModal').modal('show');
    window.location.href = response.url;
}, 3000);

                        }
                        else{
                           $("#nextBtn").attr("disabled", "disabled");
                           $("#prevBtn").attr("disabled", "disabled");
$("#message").css('color','red');
$("#message").css('font-size','20px');
$("#message").css('text-align','center');
$("#message").text(response.message);
$("#message").css('display','block'); 
setTimeout(() => {
    $('#signUpModal').modal('hide');
    // $('#loginModal').modal('show');
    window.location.href = response.url;
}, 3000);

                        }
                    }
                });
        }
   
        }

        function prevStep() {
            var checkStatus =  $('#check_status').val();
            if(checkStatus == 'step_2_update'){
                // $.ajax({
                //     type: "POST",
                //     url: "url",
                //     data: {
                //         student_id : givenStudentID,
                //     },
                //     dataType: "JSON",
                //     headers: {
                // "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                // "Accept-Language": 'en',
                //     },
                //     success: function (response) {
                //         console.log(response)
                //     }
                // });
                $("#prevBtn").attr("disabled", "disabled");
                $('#check_status').val("step_1_edit");
                if (currentStep > 0) {
                currentStep--;
                updateStep();
            }
            }
            else if(checkStatus == 'step_3_create_free_trial'){

                $('#check_status').val("step_2_update");
                if (currentStep > 0) {
                currentStep--;
                updateStep();
                }
            }
            else if(checkStatus == 'step_4_update_free_trial'){

                $('#check_status').val("step_3_update_free_trial");
                if (currentStep > 0) {
                currentStep--;
                updateStep();
                }
            }
              else if(checkStatus == 'step_3_update_free_trial'){

                $('#check_status').val("step_2_update");
                if (currentStep > 0) {
                currentStep--;
                updateStep();
                }
            }
        
        }

        document.getElementById('nextBtn').addEventListener("click", nextStep);
        document.getElementById('prevBtn').addEventListener("click", prevStep);

        // steps.forEach((step, index) => {
        //     step.addEventListener("click", () => {
        //         currentStep = index;
        //         updateStep();
        //     });
        // });

        updateButtons();
    });
</script>
<script src="{{ asset('frontend/assets/js/course/addToCart.js') }}"></script>
<script src="{{ asset('frontend/assets/js/course/addToWishlist.js') }}"></script>
<script src="{{ asset('frontend/assets/js/course/course-review-paginate.js') }}"></script>
<!--Tinymce js-->
<script src="{{ asset('frontend/assets/js/tinymce.min.js') }}"></script>
<script src="{{ asset('frontend/assets/js/tinymce-script.js') }}"></script>
<!-- Video Player js -->
<script src="{{ asset('frontend/assets/vendor/video-player/plyr.js') }}"></script>
<script>
    // const zai_player = new Plyr('#player');
        // const zai_player2 = new Plyr('#youtubePlayer');
        const zai_player1 = new Plyr('#player2');
        const zai_player3 = new Plyr('#youtubePlayer2');
        // const zai_player4 = new Plyr('#vimeoPlayer');
</script>
<script>
    const players = Array.from(document.querySelectorAll('.js-player')).map((p) => new Plyr(p));
</script>
<!-- Video Player js -->
<script>
    "use strict"
        $('.lectureText').on('click', function () {
            var text = $(this).data("lecture_text")
            $('.getLectureText').html(text)
        })

        $('.lectureImage').on('click', function () {
            var image = $(this).data("lecture_image")
            $('.getLectureImage').attr('src', image)
        })

        $('.lectureSlide').on('click', function () {
            var slide = $(this).data("lecture_slide")
            $('.getLectureSlide').attr('src', slide)
        })

        $('.normalVideo').on('click', function () {
            var video = $(this).data("normal_video")
            console.log(video)
            $('.getNormalVideo').attr('src', video)
        })

        $('.lectureAudio').on('click', function () {
            var audio = $(this).data("lecture_audio")
            console.log(audio)
            $('.getLectureAudio').attr('src', audio)
        })

        function copyToClipboard(elem) {
            // Get the text field
            var copyText = document.getElementById(elem);

            // Select the text field
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices

            // Copy the text inside the text field
            navigator.clipboard.writeText(copyText.value);

        }
        var url = new URL(window.location.href);
        var code = url.searchParams.get("code");
        if(code.length>0){
            saveItem(code);
        }
        function saveItem(val) {
            if(val === null){
                return;
            }
            var all_ref = localStorage.getItem('ref')

            if (all_ref === '' || all_ref == null){
                localStorage.setItem('ref', JSON.stringify([code]));
                return;
            }
            all_ref = JSON.parse(all_ref);
            if(!all_ref.includes(val)){
                all_ref.push(val)
                localStorage.setItem('ref', JSON.stringify(all_ref));
            }
        }

</script> @endpush
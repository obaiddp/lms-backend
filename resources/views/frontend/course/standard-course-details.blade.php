
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
@extends('frontend.layouts.app')
@php 
$relation = getUserRoleRelation($course->user);
@endphp
@section('meta')
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
    #previous, #back {
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

.slot:hover{
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
</style>
@endsection
@section('content')
<div class="bg-page">
<!-- Course Single Page Header Start -->
<header class="course-single-page-header gradient-bg position-relative">
    <div class="section-overlay">
        <div class="container">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                    <div class="course-single-banner-content">
                        <h3 class="page-banner-heading text-white pb-30">{{ __($course->title) }}</h3>
                        @php
                            if($courseType == 3){
                                $type = 'Standard';
                            }
                            else{
                                $type = null;
                            }
                        @endphp
                        <p class="page-banner-sub-heading pb-30">{{ __($course->subtitle) }} ({{ __($type) }})</p>
                        <p class="instructor-name-certificate font-medium font-12 text-white">
                            <span class="text-decoration-underline">{{ @$course->user->$relation->name }}</span>
                            @if(get_instructor_ranking_level(@$course->user->badges))
                                | {{ get_instructor_ranking_level(@$course->user->badges) }}
                            @endif
                        </p>

                        <div class="course-rating d-flex align-items-center text-white">
                            <span class="font-medium font-14 me-2">{{ number_format($course->average_rating, 1) }}</span>
                            <ul class="rating-list d-flex align-items-center me-2">
                                @include('frontend.course.render-course-rating')
                            </ul>
                            <span class="rating-count font-14 me-3">({{ $total_user_review }})</span>
                            @php
                                if($total_course_students == 1 || $total_course_students == 0){
                                    $text = 'Student';
                                }
                                else{
                                    $text = 'Students';
                                }
                            @endphp
                            <span class="rating-count font-14">  {{ @$total_course_students }} {{ __($text) }}</span>

                          
                        </div>
                        <img src="{{asset($course->cover_image)}}" alt="{{$course->cover_image}}">
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

                    <!-- Tab panel nav list -->
                    <div class="course-tab-nav-wrap course-details-tab-nav-wrap d-flex justify-content-between">
                        <ul class="nav nav-tabs tab-nav-list border-0" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="Overview-tab" data-bs-toggle="tab" href="#Overview" role="tab" aria-controls="Overview" aria-selected="true">{{ __('Overview') }}</a>
                            </li>
                            {{-- @if($course->course_type == COURSE_TYPE_GENERAL)
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="Curriculum-tab" data-bs-toggle="tab" href="#Curriculum" role="tab" aria-controls="Curriculum" aria-selected="false">{{ __('Curriculum') }}</a>
                            </li>
                            @endif
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="Discussion-tab" data-bs-toggle="tab" href="#Discussion" role="tab" aria-controls="Discussion" aria-selected="false">{{ __('Discussion') }}</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="Review-tab" data-bs-toggle="tab" href="#Review" role="tab" aria-controls="Review" aria-selected="false">{{ __('Review') }}</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="Instructor-tab" data-bs-toggle="tab" href="#Instructor" role="tab" aria-controls="Review" aria-selected="false">{{ (count($course->course_instructors->where('status', STATUS_APPROVED)) > 1) ? __('Instructors') : __('Instructor') }}</a>
                            </li> --}}
                        </ul>
                    </div>
                    <!-- Tab panel nav list -->

                    <!-- Tab Content-->
                    <div class="tab-content" id="myTabContent">
                        @include('frontend.course.partial.partial-overview-tab')
                        {{-- @include('frontend.course.partial.partial-curriculum-tab')
                        @include('frontend.course.partial.partial-discussion-tab')
                        @include('frontend.course.partial.partial-review-tab')
                        @include('frontend.course.partial.partial-instructor-tab') --}}
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-4">
                <div class="course-single-details-right-content">
                    <div class="course-info-box bg-white">

                        <div class="video-area-left position-relative d-flex align-items-center justify-content-center">
                            <div class="course-info-video-img"><img src="{{ getImageFile($course->image) }}" alt="video" class="img-fluid"></div>
                            @if($course->intro_video_check == 1 && getVideoFile($course->video))
                            <button type="button" class="play-btn position-absolute" data-bs-toggle="modal" data-bs-target="#newVideoPlayerModal">
                                <img src="{{ asset('frontend/assets/img/icons-svg/play.svg') }}" alt="play">
                            </button>

                            @endif
                            @if($course->intro_video_check == 2 && $course->youtube_video_id)
                            <button type="button" class="play-btn position-absolute" data-bs-toggle="modal" data-bs-target="#newVideoPlayerModal">
                                <img src="{{ asset('frontend/assets/img/icons-svg/play.svg') }}" alt="play">
                            </button>
                            @endif
                        </div>
                        @if($course->learner_accessibility == 'paid')
                            <div class="course-price-box d-flex justify-content-between align-items-center mt-30 mb-30">
                            <?php
                                $startDate = date('d-m-Y H:i:s', strtotime(@$course->promotionCourse->promotion->start_date));
                                $endDate = date('d-m-Y H:i:s', strtotime(@$course->promotionCourse->promotion->end_date));
                                $percentage = @$course->promotionCourse->promotion->percentage;
                                $discount_price = number_format($course->price - (($course->price * $percentage) / 100), 2);
                            ?>

                            @if(now()->gt($startDate) && now()->lt($endDate))
                                <div>
                                    <h4 class="d-flex align-items-center mb-1">
                                        @if(get_currency_placement() == 'after')
                                            {{ $discount_price }} {{ get_currency_symbol() }}
                                        @else
                                            {{ get_currency_symbol() }} {{ $discount_price }}
                                        @endif

                                        <span class="text-decoration-line-through fw-normal font-16 color-gray ps-3">
                                        @if(get_currency_placement() == 'after')
                                                {{ $course->price }} {{ get_currency_symbol() }}
                                            @else
                                                {{ get_currency_symbol() }} {{ $course->price }}
                                            @endif
                                        </span>
                                    </h4>
                                    <span class="course-left-duration color-deep-orange font-14"> <span class="iconify me-2 font-18" data-icon="clarity:alarm-clock-line"></span>

                                         {{ getLeftDuration($startDate, $endDate) }} {{ __('Left at this Price') }}!</span>
                                </div>
                                <div class="price-off font-12 font-medium color-hover d-flex align-items-center justify-content-center">{{ @$percentage }}% {{ __("off") }}</div>
                            @elseif($course->price <= $course->old_price)
                                <div>
                                    <h4 class="d-flex align-items-center mb-1">
                                        @if(get_currency_placement() == 'after')
                                            {{ $course->price }} {{ get_currency_symbol() }}
                                        @else
                                            {{ get_currency_symbol() }} {{ $course->price }}
                                        @endif

                                        <span class="text-decoration-line-through fw-normal font-16 color-gray ps-3">
                                        @if(get_currency_placement() == 'after')
                                                {{ $course->old_price }} {{ get_currency_symbol() }}
                                            @else
                                                {{ get_currency_symbol() }} {{ $course->old_price }}
                                            @endif
                                        </span>
                                    </h4>
                                </div>
                                @php 
                                    $percentage = number_format(((($course->old_price - $course->price) / $course->old_price) * 100), 2);
                                @endphp
                                <div class="price-off font-12 font-medium color-hover d-flex align-items-center justify-content-center">{{ $percentage }}% {{ __("off") }}</div>
                            @else
                                <div>
                                    <h4 class="d-flex align-items-center mb-1">
                                        @if(get_currency_placement() == 'after')
                                            {{ $course->price }} {{ get_currency_symbol() }}
                                        @else
                                            {{ get_currency_symbol() }} {{ $course->price }}
                                        @endif
                                    </h4>
                                </div>
                            @endif
                        </div>
                        @elseif($course->learner_accessibility == 'free')
                        @php
                        if($courseType == 3){
                            $type2 = 'Standard';
                        }
                        else{
                            $type2 = null;
                        }
                        @endphp
                            <div class="course-price-box d-flex justify-content-between align-items-center mt-30 mb-30">
                                <div>
                                    <h4 class="d-flex align-items-center mb-1">  {{ __('Free') }}({{ __($type2)}}) </h4>
                                </div>
                            </div>
                        @endif

                        <div class="course-includes-box course-includes-box-top">
                            <ul class="pb-30">
                                {{-- <li class="d-flex justify-content-between">
                                    <div>
                                        <span class="iconify" data-icon="bytesize:clock"></span>
                                        <span>{{ __('Course Duration') }}</span>
                                    </div>
                                    @if($course->course_type == COURSE_TYPE_GENERAL)
                                    <div>{{ @$course->VideoDuration }}</div>
                                    @else
                                    <div>{{ @$course->scorm_course->duration }}</div>
                                    @endif
                                </li> --}}
                                {{-- <li class="d-flex justify-content-between">
                                    <div>
                                        <span class="iconify" data-icon="carbon:increase-level"></span>
                                        <span>{{ __('Course Level') }}</span>
                                    </div>
                                    <div>{{ @$course->difficultyLevel->name }}</div>
                                </li> --}}
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
                            {{-- <a href="{{ route('student.my-course.show', $course->slug) }}" class="theme-btn theme-button1 theme-button3 w-100 mb-30 ">
                                {{ __('Go to Course') }}
                                <i data-feather="arrow-right"></i>
                            </a> --}}
                            {{-- <a href="https://portal.cefonlineacademy.com/mycourses/{{$course->slug}}"
                                class="theme-btn theme-button1 theme-button3 w-100 mb-30 ">
                                {{ __('Go to Course') }}
                            </a> --}}
                            <button class="theme-btn theme-button1 theme-button3 w-100 mb-30" >
                                {{ __('Course Enrolled') }}
                            </button>
                        @elseif($course_exits == 'cartList')
                            <button class="theme-btn theme-button1 theme-button3 w-100 mb-30" >
                                {{ __('Added to Cart') }}
                            </button>
                        @else
                            @if($course->status == STATUS_APPROVED)
                            @if ($authRole == 3 || $authRole == null)
                                   
                                        <a  data-placement="left" href="javascript:void(0);" style="width: 100% !important;"  class="theme-btn theme-button1 theme-button3 w-100 mb-30 enrollCourse" data-course_id="{{ $course->id }}"
                                            data-route="{{ route('student.enrollCourse') }}"
                                            data-coursetype="1"
                                            data-login_route="{{ route('login') }}/1/{{$course->slug}}">
                                            <span class="msgInfoChange">{{ __('Enroll Live Course') }} <i data-feather="arrow-right"></i></span> 
                                        </a>
                                        {{-- <button class="theme-btn theme-button1 theme-button3 w-100 mb-30 enrollCourse" data-course_id="{{ $course->id }}"
                                                    data-route="{{ route('student.enrollCourse') }}"
                                                    data-login_route="{{ route('login') }}/1/{{$course->slug}}">
                                                <span class="msgInfoChange">{{ __('Enroll the Course') }} <i data-feather="arrow-right"></i></span>
                                        </button> --}}
                           
  
  <!-- Modal -->
  <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog w-40">
      <div style="max-height: 90vh; overflow-y: auto;" class="modal-content">
        <div class="modal-body">
            <div class="col-md-12">
                <form id="loginForm" method="POST" action="{{ route('login-modal') }}">
                    @csrf
                    <h4 class="mb-1 text-center">{{__('Sign In')}}</h4>
                    <p class="font-14 mb-30 text-center">{{__('New User')}} ?
                        <a  data-placement="left" href="javascript:void(0);"  class="w-100 mb-30 signUp" data-course_id="{{ $course->id }}"
                            data-route="{{ route('sign-up-as-student-modal') }}/1/{{$course->slug}}"
                            data-coursetype="1"
                            data-courseslug="{{$course->slug}}"
                            data-login_route="{{ route('sign-up-as-student-modal') }}/1/{{$course->slug}}">
                            <span class="msgInfoChange">{{__('Sign Up')}} <i style="width: 1rem; height: 1rem;" data-feather="arrow-right"></i></span> 
                        </a>
                        {{-- <a href="{{route('sign-up-as-student')}}" class="theme-btn theme-button1 theme-button3 font-12 fw-bold w-40">{{__('Sign Up')}}</a> --}}
                    </p>
                    <input type="hidden" id="coursetype" name="coursetype">
                    <div class="row mb-30">
                        <div class="col-md-12">
                            <label class="label-text-title color-heading font-medium font-16 mb-0">{{__('Email or Phone')}}</label>
                            <input required type="text" name="email" value="{{old('email')}}" class="form-control email" placeholder="{{ __('Type your email or phone number') }}">
                            @if ($errors->has('email'))
                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('email') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-30">
                        <div class="col-md-12">
                            <label class="label-text-title color-heading font-medium font-16 mb-0">{{__('Password')}}</label>
                            <div class="form-group mb-0 position-relative">
                                <input required class="form-control password" name="password" value="{{old('password')}}" placeholder="*********" type="password">
                                <span class="toggle cursor fas fa-eye pass-icon" style="position:absolute; top:1.2rem; right:1rem;"></span>
                            </div>

                            @if ($errors->has('password'))
                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('password') }}</span>
                            @endif
                        </div>
                    </div>

                    <div id="div-response">

                    </div>
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <button id="login_now" style="width:100% !important;" type="submit" class="theme-btn theme-button1 theme-button3 font-15 fw-bold w-100">{{__('Sign In')}}</button>
                        </div>
                    </div>

                </form>    
            </div>
          
        </div>
        {{-- <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Login</button>
        </div> --}}
      </div>
    </div>
  </div>
                                        







    <!-- Modal -->
    <div class="modal fade" id="signUpModal" tabindex="-1" aria-labelledby="signUpModalLabel" aria-hidden="true">
        <div class="modal-dialog w-40">
          <div style=" max-height: 90vh; overflow-y: auto;" class="modal-content">
            
            <div class="modal-bodyp-0">
       <!-- Registration Area Start -->
<section class="register-page reg-page p-0">
    <div
        class="container-fluid p-0 min-h-full bg-white w-90 mx-auto shadow-md flex flex-col justify-content-center align-items-center px-4 py-3">
        <h4 class="text-center">Let's get your Journey Started</h4>
        <div class="main">
            <ul class="progressBar my-2">
                <li>
                    <div class="step first active">
                        <p>1</p>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="transparent" class="bi bi-check-lg"
                            viewBox="0 0 16 16">
                            <path
                                d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z" />
                        </svg>
                    </div>
                </li>
                <li>
                    <div class="step second">
                        <p>2</p>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="transparent" class="bi bi-check-lg"
                            viewBox="0 0 16 16">
                            <path
                                d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z" />
                        </svg>
                    </div>
                </li>
                <li>
                    <div class="step third">
                        <p>3</p>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="transparent" class="bi bi-check-lg"
                            viewBox="0 0 16 16">
                            <path
                                d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z" />
                        </svg>
                    </div>
                </li>
                <li>
                    <div class="step fourth">
                        <p>4</p>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="transparent" class="bi bi-check-lg"
                            viewBox="0 0 16 16">
                            <path
                                d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z" />
                        </svg>
                    </div>
                </li>
            </ul>
            <form id="multiStepForm" class="w-100 px-4">
                <input type="hidden" name="token" id="token" value="{{ csrf_token() }}">
                <input type="hidden" name="new_student_id" id="new_student_id" value="">
                <input type="hidden" name="new_trial_id" id="new_trial_id" value="">
                <input type="hidden" name="check_status" id="check_status" value="step_1_create">
                <input type="hidden" name="type_st" id="type_st" value="{{$typeCourse}}">
                <input type="hidden" name="slug" id="slug" value="">
                <div class="step-content mt-3 active" id="step-1">
                    <h6 class="text-center mb-4">Your personal details</h6>

                    <div class="input-group mb-3">
                        <input name="fname" id="fname" type="text" class="form-control form-svg" placeholder="First Name"
                            aria-label="First Name" aria-describedby="FirstName" required>
                        <span class="input-group-text" id="FirstName">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#a3a6ad"
                                class="bi bi-person" viewBox="0 0 16 16">
                                <path
                                    d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z" />
                            </svg>
                        </span>
                        <span class="error-message" id="fname_error"></span>
                    </div>

                    <div class="input-group mb-3">
                        <input name="lname" id="lname" type="text" class="form-control form-svg" placeholder="Last Name" aria-label="Last Name"
                            aria-describedby="LastName" required>
                        <span class="input-group-text" id="LastName">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#a3a6ad"
                                class="bi bi-person" viewBox="0 0 16 16">
                                <path
                                    d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z" />
                            </svg>
                        </span>
                        <span class="error-message" id="lname_error"></span>
                    </div>
                    <div class="input-group mb-3">
                        <input type="email" name="email" id="email" class="form-control form-svg" placeholder="Email" aria-label="Email"
                            aria-describedby="Email">
                        <span class="input-group-text" id="Email">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="#a3a6ad"
                                class="bi bi-envelope" viewBox="0 0 16 16">
                                <path
                                    d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z" />
                            </svg>
                        </span>
                        <span class="error-message" id="email_error"></span>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="password" id="password" class="form-control password" placeholder="*********">
                        <span width="24" height="24" id="password" class="input-group-text toggle cursor fas fa-eye pass-icon pt-3"></span>
                        <span class="error-message" id="password_error"></span>
                    </div>

                    <div class="input-group mb-3">
                        <select class="form-control" name="time_zone" id="time_zone" required>
                            <option value ="">{{ __("Select Time Zone") }}</option>
                            <option value="America/Adak"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Adak") selected @endif>(GMT-10:00) America/Adak (Hawaii-Aleutian Standard Time)</option>
                            <option value="America/Atka"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Atka") selected @endif>(GMT-10:00) America/Atka (Hawaii-Aleutian Standard Time)</option>
                            <option value="America/Anchorage"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Anchorage") selected @endif>(GMT-9:00) America/Anchorage (Alaska Standard Time)</option>
                            <option value="America/Juneau"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Juneau") selected @endif>(GMT-9:00) America/Juneau (Alaska Standard Time)</option>
                            <option value="America/Nome"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Nome") selected @endif>(GMT-9:00) America/Nome (Alaska Standard Time)</option>
                            <option value="America/Yakutat"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Yakutat") selected @endif>(GMT-9:00) America/Yakutat (Alaska Standard Time)</option>
                            <option value="America/Dawson"@if(old('time_zone', @$student->
                                user->area_code)=="America/Dawson") selected @endif>(GMT-8:00) America/Dawson (Pacific Standard Time)</option>
                            <option value="America/Ensenada"@if(old('time_zone', @$student->
                                user->area_code)=="America/Ensenada") selected @endif>(GMT-8:00) America/Ensenada (Pacific Standard Time)</option>
                            <option value="America/Los_Angeles"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Los_Angeles") selected @endif>(GMT-8:00) America/Los_Angeles (Pacific Standard Time)</option>
                            <option value="America/Tijuana"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Tijuana") selected @endif>(GMT-8:00) America/Tijuana (Pacific Standard Time)</option>
                            <option value="America/Vancouver"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Vancouver") selected @endif>(GMT-8:00) America/Vancouver (Pacific Standard Time)</option>
                            <option value="America/Whitehorse"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Whitehorse") selected @endif>(GMT-8:00) America/Whitehorse (Pacific Standard Time)</option>
                            <option value="Canada/Pacific"@if(old('time_zone', @$student->
                                user->time_zone)=="Canada/Pacific") selected @endif>(GMT-8:00) Canada/Pacific (Pacific Standard Time)</option>
                            <option value="Canada/Yukon"@if(old('time_zone', @$student->
                                user->time_zone)=="Canada/Yukon") selected @endif>(GMT-8:00) Canada/Yukon (Pacific Standard Time)</option>
                            <option value="Mexico/BajaNorte"@if(old('time_zone', @$student->
                                user->time_zone)=="Mexico/BajaNorte") selected @endif>(GMT-8:00) Mexico/BajaNorte (Pacific Standard Time)</option>
                            <option value="America/Boise"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Boise") selected @endif>(GMT-7:00) America/Boise (Mountain Standard Time)</option>
                            <option value="America/Cambridge_Bay"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Cambridge_Bay") selected @endif>(GMT-7:00) America/Cambridge_Bay (Mountain Standard Time)</option>
                            <option value="America/Chihuahua"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Chihuahua") selected @endif>(GMT-7:00) America/Chihuahua (Mountain Standard Time)</option>
                            <option value="America/Dawson_Creek"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Dawson_Creek") selected @endif>(GMT-7:00) America/Dawson_Creek (Mountain Standard Time)</option>
                            <option value="America/Denver"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Denver") selected @endif>(GMT-7:00) America/Denver (Mountain Standard Time)</option>
                            <option value="America/Edmonton"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Edmonton") selected @endif>(GMT-7:00) America/Edmonton (Mountain Standard Time)</option>
                            <option value="America/Hermosillo"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Hermosillo") selected @endif>(GMT-7:00) America/Hermosillo (Mountain Standard Time)</option>
                            <option value="America/Inuvik"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Inuvik") selected @endif>(GMT-7:00) America/Inuvik (Mountain Standard Time)</option>
                            <option value="America/Mazatlan"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Mazatlan") selected @endif>(GMT-7:00) America/Mazatlan (Mountain Standard Time)</option>
                            <option value="America/Phoenix"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Phoenix") selected @endif>(GMT-7:00) America/Phoenix (Mountain Standard Time)</option>
                            <option value="America/Shiprock"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Shiprock") selected @endif>(GMT-7:00) America/Shiprock (Mountain Standard Time)</option>
                            <option value="America/Yellowknife"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Yellowknife") selected @endif>(GMT-7:00) America/Yellowknife (Mountain Standard Time)</option>
                            <option value="Canada/Mountain"@if(old('time_zone', @$student->
                                user->time_zone)=="Canada/Mountain") selected @endif>(GMT-7:00) Canada/Mountain (Mountain Standard Time)</option>
                            <option value="Mexico/BajaSur"@if(old('time_zone', @$student->
                                user->time_zone)=="Mexico/BajaSur") selected @endif>(GMT-7:00) Mexico/BajaSur (Mountain Standard Time)</option>
                            <option value="America/Belize"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Belize") selected @endif>(GMT-6:00) America/Belize (Central Standard Time)</option>
                            <option value="America/Cancun"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Cancun") selected @endif>(GMT-6:00) America/Cancun (Central Standard Time)</option>
                            <option value="America/Chicago"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Chicago") selected @endif>(GMT-6:00) America/Chicago (Central Standard Time)</option>
                            <option value="America/Costa_Rica"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Costa_Rica") selected @endif>(GMT-6:00) America/Costa_Rica (Central Standard Time)</option>
                            <option value="America/El_Salvador"@if(old('time_zone', @$student->
                                user->time_zone)=="America/El_Salvador") selected @endif>(GMT-6:00) America/El_Salvador (Central Standard Time)</option>
                            <option value="America/Guatemala"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Guatemala") selected @endif>(GMT-6:00) America/Guatemala (Central Standard Time)</option>
                            <option value="America/Knox_IN"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Knox_IN") selected @endif>(GMT-6:00) America/Knox_IN (Central Standard Time)</option>
                            <option value="America/Managua"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Managua") selected @endif>(GMT-6:00) America/Managua (Central Standard Time)</option>
                            <option value="America/Menominee"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Menominee") selected @endif>(GMT-6:00) America/Menominee (Central Standard Time)</option>
                            <option value="America/Merida"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Merida") selected @endif>(GMT-6:00) America/Merida (Central Standard Time)</option>
                            <option value="America/Mexico_City"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Mexico_City") selected @endif>(GMT-6:00) America/Mexico_City (Central Standard Time)</option>
                            <option value="America/Monterrey"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Monterrey") selected @endif>(GMT-6:00) America/Monterrey (Central Standard Time)</option>
                            <option value="America/Rainy_River"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Rainy_River") selected @endif>(GMT-6:00) America/Rainy_River (Central Standard Time)</option>
                            <option value="America/Rankin_Inlet"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Rankin_Inlet") selected @endif>(GMT-6:00) America/Rankin_Inlet (Central Standard Time)</option>
                            <option value="America/Regina"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Regina") selected @endif>(GMT-6:00) America/Regina (Central Standard Time)</option>
                            <option value="America/Swift_Current"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Swift_Current") selected @endif>(GMT-6:00) America/Swift_Current (Central Standard Time)</option>
                            <option value="America/Tegucigalpa"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Yakutat") selected @endif>(GMT-6:00) America/Tegucigalpa (Central Standard Time)</option>
                            <option value="America/Winnipeg"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Yakutat") selected @endif>(GMT-6:00) America/Winnipeg (Central Standard Time)</option>
                            <option value="Canada/Central"@if(old('time_zone', @$student->
                                user->time_zone)=="Canada/Central") selected @endif>(GMT-6:00) Canada/Central (Central Standard Time)</option>
                            <option value="Canada/East-Saskatchewan"@if(old('time_zone', @$student->
                                user->time_zone)=="Canada/East-Saskatchewan") selected @endif>(GMT-6:00) Canada/East-Saskatchewan (Central Standard Time)</option>
                            <option value="Canada/Saskatchewan"@if(old('time_zone', @$student->
                                user->time_zone)=="Canada/Saskatchewan") selected @endif>(GMT-6:00) Canada/Saskatchewan (Central Standard Time)</option>
                            <option value="Chile/EasterIsland"@if(old('time_zone', @$student->
                                user->time_zone)=="Chile/EasterIsland") selected @endif>(GMT-6:00) Chile/EasterIsland (Easter Is. Time)</option>
                            <option value="Mexico/General"@if(old('time_zone', @$student->
                                user->time_zone)=="Mexico/General") selected @endif>(GMT-6:00) Mexico/General (Central Standard Time)</option>
                            <option value="America/Atikokan"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Atikokan") selected @endif>(GMT-5:00) America/Atikokan (Eastern Standard Time)</option>
                            <option value="America/Bogota"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Bogota") selected @endif>(GMT-5:00) America/Bogota (Colombia Time)</option>
                            <option value="America/Cayman"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Cayman") selected @endif>(GMT-5:00) America/Cayman (Eastern Standard Time)</option>
                            <option value="America/Coral_Harbour"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Coral_Harbour") selected @endif>(GMT-5:00) America/Coral_Harbour (Eastern Standard Time)</option>
                            <option value="America/Detroit"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Detroit") selected @endif>(GMT-5:00) America/Detroit (Eastern Standard Time)</option>
                            <option value="America/Fort_Wayne"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Fort_Wayne") selected @endif>(GMT-5:00) America/Fort_Wayne (Eastern Standard Time)</option>
                            <option value="America/Grand_Turk"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Grand_Turk") selected @endif>(GMT-5:00) America/Grand_Turk (Eastern Standard Time)</option>
                            <option value="America/Guayaquil"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Guayaquil") selected @endif>(GMT-5:00) America/Guayaquil (Ecuador Time)</option>
                            <option value="America/Havana"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Havana") selected @endif>(GMT-5:00) America/Havana (Cuba Standard Time)</option>
                            <option value="America/Indianapolis"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Indianapolis") selected @endif>(GMT-5:00) America/Indianapolis (Eastern Standard Time)</option>
                            <option value="America/Iqaluit"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Iqaluit") selected @endif>(GMT-5:00) America/Iqaluit (Eastern Standard Time)</option>
                            <option value="America/Jamaica"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Jamaica") selected @endif>(GMT-5:00) America/Jamaica (Eastern Standard Time)</option>
                            <option value="America/Lima"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Lima") selected @endif>(GMT-5:00) America/Lima (Peru Time)</option>
                            <option value="America/Louisville"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Louisville") selected @endif>(GMT-5:00) America/Louisville (Eastern Standard Time)</option>
                            <option value="America/Montreal"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Montreal") selected @endif>(GMT-5:00) America/Montreal (Eastern Standard Time)</option>
                            <option value="America/Nassau"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Nassau") selected @endif>(GMT-5:00) America/Nassau (Eastern Standard Time)</option>
                            <option value="America/New_York"@if(old('time_zone', @$student->
                                user->time_zone)=="America/New_York") selected @endif>(GMT-5:00) America/New_York (Eastern Standard Time)</option>
                            <option value="America/Nipigon"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Nipigon") selected @endif>(GMT-5:00) America/Nipigon (Eastern Standard Time)</option>
                            <option value="America/Panama"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Panama") selected @endif>(GMT-5:00) America/Panama (Eastern Standard Time)</option>
                            <option value="America/Pangnirtung"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Pangnirtung") selected @endif>(GMT-5:00) America/Pangnirtung (Eastern Standard Time)</option>
                            <option value="America/Port-au-Prince"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Port-au-Prince") selected @endif>(GMT-5:00) America/Port-au-Prince (Eastern Standard Time)</option>
                            <option value="America/Resolute"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Resolute") selected @endif>(GMT-5:00) America/Resolute (Eastern Standard Time)</option>
                            <option value="America/Thunder_Bay"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Thunder_Bay") selected @endif>(GMT-5:00) America/Thunder_Bay (Eastern Standard Time)</option>
                            <option value="America/Toronto"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Toronto") selected @endif>(GMT-5:00) America/Toronto (Eastern Standard Time)</option>
                            <option value="Canada/Eastern"@if(old('time_zone', @$student->
                                user->time_zone)=="Canada/Eastern") selected @endif>(GMT-5:00) Canada/Eastern (Eastern Standard Time)</option>
                            <option value="America/Caracas"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Caracas") selected @endif>(GMT-4:-30) America/Caracas (Venezuela Time)</option>
                            <option value="America/Anguilla"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Anguilla") selected @endif>(GMT-4:00) America/Anguilla (Atlantic Standard Time)</option>
                            <option value="America/Antigua"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Antigua") selected @endif>(GMT-4:00) America/Antigua (Atlantic Standard Time)</option>
                            <option value="America/Aruba"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Aruba") selected @endif>(GMT-4:00) America/Aruba (Atlantic Standard Time)</option>
                            <option value="America/Asuncion"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Asuncion") selected @endif>(GMT-4:00) America/Asuncion (Paraguay Time)</option>
                            <option value="America/Barbados"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Barbados") selected @endif>(GMT-4:00) America/Barbados (Atlantic Standard Time)</option>
                            <option value="America/Blanc-Sablon"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Blanc-Sablon") selected @endif>(GMT-4:00) America/Blanc-Sablon (Atlantic Standard Time)</option>
                            <option value="America/Boa_Vista"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Boa_Vista") selected @endif>(GMT-4:00) America/Boa_Vista (Amazon Time)</option>
                            <option value="America/Campo_Grande"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Campo_Grande") selected @endif>(GMT-4:00) America/Campo_Grande (Amazon Time)</option>
                            <option value="America/Cuiaba"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Cuiaba") selected @endif>(GMT-4:00) America/Cuiaba (Amazon Time)</option>
                            <option value="America/Curacao"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Curacao") selected @endif>(GMT-4:00) America/Curacao (Atlantic Standard Time)</option>
                            <option value="America/Dominica"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Dominica") selected @endif>(GMT-4:00) America/Dominica (Atlantic Standard Time)</option>
                            <option value="America/Eirunepe"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Eirunepe") selected @endif>(GMT-4:00) America/Eirunepe (Amazon Time)</option>
                            <option value="America/Glace_Bay"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Glace_Bay") selected @endif>(GMT-4:00) America/Glace_Bay (Atlantic Standard Time)</option>
                            <option value="America/Goose_Bay"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Goose_Bay") selected @endif>(GMT-4:00) America/Goose_Bay (Atlantic Standard Time)</option>
                            <option value="America/Grenada"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Grenada") selected @endif>(GMT-4:00) America/Grenada (Atlantic Standard Time)</option>
                            <option value="America/Guadeloupe"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Guadeloupe") selected @endif>(GMT-4:00) America/Guadeloupe (Atlantic Standard Time)</option>
                            <option value="America/Guyana"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Guyana") selected @endif>(GMT-4:00) America/Guyana (Guyana Time)</option>
                            <option value="America/Halifax"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Halifax") selected @endif>(GMT-4:00) America/Halifax (Atlantic Standard Time)</option>
                            <option value="America/La_Paz"@if(old('time_zone', @$student->
                                user->time_zone)=="America/La_Paz") selected @endif>(GMT-4:00) America/La_Paz (Bolivia Time)</option>
                            <option value="America/Manaus"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Manaus") selected @endif>(GMT-4:00) America/Manaus (Amazon Time)</option>
                            <option value="America/Marigot"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Marigot") selected @endif>(GMT-4:00) America/Marigot (Atlantic Standard Time)</option>
                            
                            
                            
                          
                            
                            
                            <option value="Asia/Nicosia"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Nicosia") selected @endif>(GMT+2:00) Asia/Nicosia (Eastern European Time)</option>
                            <option value="Asia/Tel_Aviv"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Tel_Aviv") selected @endif>(GMT+2:00) Asia/Tel_Aviv (Israel Standard Time)</option>
                            <option value="Europe/Athens"@if(old('time_zone', @$student->
                                user->time_zone)=="Europe/Athens") selected @endif>(GMT+2:00) Europe/Athens (Eastern European Time)</option>
                            <option value="Europe/Bucharest"@if(old('time_zone', @$student->
                                user->time_zone)=="Europe/Bucharest") selected @endif>(GMT+2:00) Europe/Bucharest (Eastern European Time)</option>
                            <option value="Europe/Chisinau"@if(old('time_zone', @$student->
                                user->time_zone)=="Europe/Chisinau") selected @endif>(GMT+2:00) Europe/Chisinau (Eastern European Time)</option>
                            <option value="Europe/Helsinki"@if(old('time_zone', @$student->
                                user->time_zone)=="Europe/Helsinki") selected @endif>(GMT+2:00) Europe/Helsinki (Eastern European Time)</option>
                            <option value="Europe/Istanbul"@if(old('time_zone', @$student->
                                user->time_zone)=="Europe/Istanbul") selected @endif>(GMT+2:00) Europe/Istanbul (Eastern European Time)</option>
                            <option value="Europe/Kaliningrad"@if(old('time_zone', @$student->
                                user->time_zone)=="Europe/Kaliningrad") selected @endif>(GMT+2:00) Europe/Kaliningrad (Eastern European Time)</option>
                            <option value="Europe/Kiev"@if(old('time_zone', @$student->
                                user->time_zone)=="Europe/Kiev") selected @endif>(GMT+2:00) Europe/Kiev (Eastern European Time)</option>
                            <option value="Europe/Mariehamn"@if(old('time_zone', @$student->
                                user->time_zone)=="Europe/Mariehamn") selected @endif>(GMT+2:00) Europe/Mariehamn (Eastern European Time)</option>
                            <option value="Europe/Minsk"@if(old('time_zone', @$student->
                                user->time_zone)=="Europe/Minsk") selected @endif>(GMT+2:00) Europe/Minsk (Eastern European Time)</option>
                            <option value="Europe/Nicosia"@if(old('time_zone', @$student->
                                user->time_zone)=="Europe/Nicosia") selected @endif>(GMT+2:00) Europe/Nicosia (Eastern European Time)</option>
                            <option value="Europe/Riga"@if(old('time_zone', @$student->
                                user->time_zone)=="Europe/Riga") selected @endif>(GMT+2:00) Europe/Riga (Eastern European Time)</option>
                            <option value="Europe/Simferopol"@if(old('time_zone', @$student->
                                user->time_zone)=="Europe/Simferopol") selected @endif>(GMT+2:00) Europe/Simferopol (Eastern European Time)</option>
                            <option value="Europe/Sofia"@if(old('time_zone', @$student->
                                user->time_zone)=="Europe/Sofia") selected @endif>(GMT+2:00) Europe/Sofia (Eastern European Time)</option>
                            <option value="Europe/Tallinn"@if(old('time_zone', @$student->
                                user->time_zone)=="Europe/Tallinn") selected @endif>(GMT+2:00) Europe/Tallinn (Eastern European Time)</option>
                            <option value="Europe/Tiraspol"@if(old('time_zone', @$student->
                                user->time_zone)=="Europe/Tiraspol") selected @endif>(GMT+2:00) Europe/Tiraspol (Eastern European Time)</option>
                            <option value="Europe/Uzhgorod"@if(old('time_zone', @$student->
                                user->time_zone)=="Europe/Uzhgorod") selected @endif>(GMT+2:00) Europe/Uzhgorod (Eastern European Time)</option>
                            <option value="Europe/Vilnius"@if(old('time_zone', @$student->
                                user->time_zone)=="Europe/Vilnius") selected @endif>(GMT+2:00) Europe/Vilnius (Eastern European Time)</option>
                            <option value="Europe/Zaporozhye"@if(old('time_zone', @$student->
                                user->time_zone)=="Europe/Zaporozhye") selected @endif>(GMT+2:00) Europe/Zaporozhye (Eastern European Time)</option>
                            <option value="Africa/Addis_Ababa"@if(old('time_zone', @$student->
                                user->time_zone)=="Africa/Addis_Ababa") selected @endif>(GMT+3:00) Africa/Addis_Ababa (Eastern African Time)</option>
                            <option value="Africa/Asmara"@if(old('time_zone', @$student->
                                user->time_zone)=="Africa/Asmara") selected @endif>(GMT+3:00) Africa/Asmara (Eastern African Time)</option>
                            <option value="Africa/Asmera"@if(old('time_zone', @$student->
                                user->time_zone)=="Africa/Asmera") selected @endif>(GMT+3:00) Africa/Asmera (Eastern African Time)</option>
                            <option value="Africa/Dar_es_Salaam"@if(old('time_zone', @$student->
                                user->time_zone)=="Africa/Dar_es_Salaam") selected @endif>(GMT+3:00) Africa/Dar_es_Salaam (Eastern African Time)</option>
                            <option value="Africa/Djibouti"@if(old('time_zone', @$student->
                                user->time_zone)=="Africa/Djibouti") selected @endif>(GMT+3:00) Africa/Djibouti (Eastern African Time)</option>
                            <option value="Africa/Kampala"@if(old('time_zone', @$student->
                                user->time_zone)=="Africa/Kampala") selected @endif>(GMT+3:00) Africa/Kampala (Eastern African Time)</option>
                            <option value="Africa/Khartoum"@if(old('time_zone', @$student->
                                user->time_zone)=="Africa/Khartoum") selected @endif>(GMT+3:00) Africa/Khartoum (Eastern African Time)</option>
                            <option value="Africa/Mogadishu"@if(old('time_zone', @$student->
                                user->time_zone)=="Africa/Mogadishu") selected @endif>(GMT+3:00) Africa/Mogadishu (Eastern African Time)</option>
                            <option value="Africa/Nairobi"@if(old('time_zone', @$student->
                                user->time_zone)=="Africa/Nairobi") selected @endif>(GMT+3:00) Africa/Nairobi (Eastern African Time)</option>
                            <option value="Antarctica/Syowa"@if(old('time_zone', @$student->
                                user->time_zone)=="Antarctica/Syowa") selected @endif>(GMT+3:00) Antarctica/Syowa (Syowa Time)</option>
                            <option value="Asia/Aden"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Aden") selected @endif>(GMT+3:00) Asia/Aden (Arabia Standard Time)</option>
                            <option value="Asia/Baghdad"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Baghdad") selected @endif>(GMT+3:00) Asia/Baghdad (Arabia Standard Time)</option>
                            <option value="Asia/Bahrain"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Bahrain") selected @endif>(GMT+3:00) Asia/Bahrain (Arabia Standard Time)</option>
                            <option value="Asia/Kuwait"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Kuwait") selected @endif>(GMT+3:00) Asia/Kuwait (Arabia Standard Time)</option>
                            <option value="Asia/Qatar"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Qatar") selected @endif>(GMT+3:00) Asia/Qatar (Arabia Standard Time)</option>
                            <option value="Europe/Moscow"@if(old('time_zone', @$student->
                                user->time_zone)=="Europe/Moscow") selected @endif>(GMT+3:00) Europe/Moscow (Moscow Standard Time)</option>
                            <option value="Europe/Volgograd"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Yakutat") selected @endif>(GMT+3:00) Europe/Volgograd (Volgograd Time)</option>
                            <option value="Indian/Antananarivo"@if(old('time_zone', @$student->
                                user->time_zone)=="Indian/Antananarivo") selected @endif>(GMT+3:00) Indian/Antananarivo (Eastern African Time)</option>
                            <option value="Indian/Comoro"@if(old('time_zone', @$student->
                                user->time_zone)=="Indian/Comoro") selected @endif>(GMT+3:00) Indian/Comoro (Eastern African Time)</option>
                            <option value="Indian/Mayotte"@if(old('time_zone', @$student->
                                user->time_zone)=="Indian/Mayotte") selected @endif>(GMT+3:00) Indian/Mayotte (Eastern African Time)</option>
                            <option value="Asia/Tehran"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Tehran") selected @endif>(GMT+3:30) Asia/Tehran (Iran Standard Time)</option>
                            <option value="Asia/Baku"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Baku") selected @endif>(GMT+4:00) Asia/Baku (Azerbaijan Time)</option>
                            <option value="Asia/Dubai"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Dubai") selected @endif>(GMT+4:00) Asia/Dubai (Gulf Standard Time)</option>
                            <option value="Asia/Muscat"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Muscat") selected @endif>(GMT+4:00) Asia/Muscat (Gulf Standard Time)</option>
                            <option value="Asia/Tbilisi"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Tbilisi") selected @endif>(GMT+4:00) Asia/Tbilisi (Georgia Time)</option>
                            <option value="Asia/Yerevan"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Yerevan") selected @endif>(GMT+4:00) Asia/Yerevan (Armenia Time)</option>
                            <option value="Europe/Samara"@if(old('time_zone', @$student->
                                user->time_zone)=="Europe/Samara") selected @endif>(GMT+4:00) Europe/Samara (Samara Time)</option>
                            <option value="Indian/Mahe"@if(old('time_zone', @$student->
                                user->time_zone)=="Indian/Mahe") selected @endif>(GMT+4:00) Indian/Mahe (Seychelles Time)</option>
                            <option value="Indian/Mauritius"@if(old('time_zone', @$student->
                                user->time_zone)=="Indian/Mauritius") selected @endif>(GMT+4:00) Indian/Mauritius (Mauritius Time)</option>
                            <option value="Indian/Reunion"@if(old('time_zone', @$student->
                                user->time_zone)=="Indian/Reunion") selected @endif>(GMT+4:00) Indian/Reunion (Reunion Time)</option>
                            <option value="Asia/Kabul"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Kabul") selected @endif>(GMT+4:30) Asia/Kabul (Afghanistan Time)</option>
                            <option value="Asia/Aqtau"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Aqtau") selected @endif>(GMT+5:00) Asia/Aqtau (Aqtau Time)</option>
                            <option value="Asia/Aqtobe"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Aqtobe") selected @endif>(GMT+5:00) Asia/Aqtobe (Aqtobe Time)</option>
                            <option value="Asia/Ashgabat"@if(old('time_zone', @$student->
                                user->time_zone)=="America/Yakutat") selected @endif>(GMT+5:00) Asia/Ashgabat (Turkmenistan Time)</option>
                            <option value="Asia/Ashkhabad"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Ashkhabad") selected @endif>(GMT+5:00) Asia/Ashkhabad (Turkmenistan Time)</option>
                            <option value="Asia/Dushanbe"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Dushanbe") selected @endif>(GMT+5:00) Asia/Dushanbe (Tajikistan Time)</option>
                            <option value="Asia/Karachi"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Karachi") selected @endif>(GMT+5:00) Asia/Karachi (Pakistan Time)</option>
                            <option value="Asia/Oral"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Oral") selected @endif>(GMT+5:00) Asia/Oral (Oral Time)</option>
                            <option value="Asia/Samarkand"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Samarkand") selected @endif>(GMT+5:00) Asia/Samarkand (Uzbekistan Time)</option>
                            <option value="Asia/Tashkent"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Tashkent") selected @endif>(GMT+5:00) Asia/Tashkent (Uzbekistan Time)</option>
                            <option value="Asia/Yekaterinburg"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Yekaterinburg") selected @endif>(GMT+5:00) Asia/Yekaterinburg (Yekaterinburg Time)</option>
                            <option value="Indian/Kerguelen"@if(old('time_zone', @$student->
                                user->time_zone)=="Indian/Kerguelen") selected @endif>(GMT+5:00) Indian/Kerguelen (French Southern &amp; Antarctic Lands Time)</option>
                            <option value="Indian/Maldives"@if(old('time_zone', @$student->
                                user->time_zone)=="Indian/Maldives") selected @endif>(GMT+5:00) Indian/Maldives (Maldives Time)</option>
                            <option value="Asia/Calcutta"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Calcutta") selected @endif>(GMT+5:30) Asia/Calcutta (India Standard Time)</option>
                            <option value="Asia/Colombo"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Colombo") selected @endif>(GMT+5:30) Asia/Colombo (India Standard Time)</option>
                            <option value="Asia/Kolkata"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Kolkata") selected @endif>(GMT+5:30) Asia/Kolkata (India Standard Time)</option>
                            <option value="Asia/Katmandu"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Katmandu") selected @endif>(GMT+5:45) Asia/Katmandu (Nepal Time)</option>
                            <option value="Antarctica/Mawson"@if(old('time_zone', @$student->
                                user->time_zone)=="Antarctica/Mawson") selected @endif>(GMT+6:00) Antarctica/Mawson (Mawson Time)</option>
                            <option value="Antarctica/Vostok"@if(old('time_zone', @$student->
                                user->time_zone)=="Antarctica/Vostok") selected @endif>(GMT+6:00) Antarctica/Vostok (Vostok Time)</option>
                            <option value="Asia/Almaty"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Almaty") selected @endif>(GMT+6:00) Asia/Almaty (Alma-Ata Time)</option>
                            <option value="Asia/Bishkek"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Bishkek") selected @endif>(GMT+6:00) Asia/Bishkek (Kirgizstan Time)</option>
                            <option value="Asia/Dacca"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Dacca") selected @endif>(GMT+6:00) Asia/Dacca (Bangladesh Time)</option>
                            <option value="Asia/Dhaka"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Dhaka") selected @endif>(GMT+6:00) Asia/Dhaka (Bangladesh Time)</option>
                            <option value="Asia/Novosibirsk"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Novosibirsk") selected @endif>(GMT+6:00) Asia/Novosibirsk (Novosibirsk Time)</option>
                            <option value="Asia/Omsk"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Omsk") selected @endif>(GMT+6:00) Asia/Omsk (Omsk Time)</option>
                            <option value="Asia/Qyzylorda"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Qyzylorda") selected @endif>(GMT+6:00) Asia/Qyzylorda (Qyzylorda Time)</option>
                            <option value="Asia/Thimbu"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Thimbu") selected @endif>(GMT+6:00) Asia/Thimbu (Bhutan Time)</option>
                            <option value="Asia/Thimphu"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Thimphu") selected @endif>(GMT+6:00) Asia/Thimphu (Bhutan Time)</option>
                            <option value="Indian/Chagos"@if(old('time_zone', @$student->
                                user->time_zone)=="Indian/Chagos") selected @endif>(GMT+6:00) Indian/Chagos (Indian Ocean Territory Time)</option>
                            <option value="Asia/Rangoon"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Rangoon") selected @endif>(GMT+6:30) Asia/Rangoon (Myanmar Time)</option>
                            <option value="Indian/Cocos"@if(old('time_zone', @$student->
                                user->time_zone)=="Indian/Cocos") selected @endif>(GMT+6:30) Indian/Cocos (Cocos Islands Time)</option>
                            <option value="Antarctica/Davis"@if(old('time_zone', @$student->
                                user->time_zone)=="Antarctica/Davis") selected @endif>(GMT+7:00) Antarctica/Davis (Davis Time)</option>
                            <option value="Asia/Bangkok"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Bangkok") selected @endif>(GMT+7:00) Asia/Bangkok (Indochina Time)</option>
                            <option value="Asia/Ho_Chi_Minh"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Ho_Chi_Minh") selected @endif>(GMT+7:00) Asia/Ho_Chi_Minh (Indochina Time)</option>
                            <option value="Asia/Hovd"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Hovd") selected @endif>(GMT+7:00) Asia/Hovd (Hovd Time)</option>
                            <option value="Asia/Jakarta"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Jakarta") selected @endif>(GMT+7:00) Asia/Jakarta (West Indonesia Time)</option>
                            <option value="Asia/Krasnoyarsk"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Krasnoyarsk") selected @endif>(GMT+7:00) Asia/Krasnoyarsk (Krasnoyarsk Time)</option>
                            <option value="Asia/Phnom_Penh"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Phnom_Penh") selected @endif>(GMT+7:00) Asia/Phnom_Penh (Indochina Time)</option>
                            <option value="Asia/Pontianak"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Pontianak") selected @endif>(GMT+7:00) Asia/Pontianak (West Indonesia Time)</option>
                            <option value="Asia/Saigon"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Saigon") selected @endif>(GMT+7:00) Asia/Saigon (Indochina Time)</option>
                            <option value="Asia/Vientiane"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Vientiane") selected @endif>(GMT+7:00) Asia/Vientiane (Indochina Time)</option>
                            <option value="Indian/Christmas"@if(old('time_zone', @$student->
                                user->time_zone)=="Indian/Christmas") selected @endif>(GMT+7:00) Indian/Christmas (Christmas Island Time)</option>
                            <option value="Antarctica/Casey"@if(old('time_zone', @$student->
                                user->time_zone)=="Antarctica/Casey") selected @endif>(GMT+8:00) Antarctica/Casey (Western Standard Time (Australia))</option>
                            <option value="Asia/Brunei"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Brunei") selected @endif>(GMT+8:00) Asia/Brunei (Brunei Time)</option>
                            <option value="Asia/Choibalsan"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Choibalsan") selected @endif>(GMT+8:00) Asia/Choibalsan (Choibalsan Time)</option>
                            <option value="Asia/Chongqing"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Chongqing") selected @endif>(GMT+8:00) Asia/Chongqing (China Standard Time)</option>
                            <option value="Asia/Chungking"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Chungking") selected @endif>(GMT+8:00) Asia/Chungking (China Standard Time)</option>
                            <option value="Asia/Harbin"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Harbin") selected @endif>(GMT+8:00) Asia/Harbin (China Standard Time)</option>
                            <option value="Asia/Hong_Kong"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Hong_Kong") selected @endif>(GMT+8:00) Asia/Hong_Kong (Hong Kong Time)</option>
                            <option value="Asia/Irkutsk"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Irkutsk") selected @endif>(GMT+8:00) Asia/Irkutsk (Irkutsk Time)</option>
                            <option value="Asia/Kashgar"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Kashgar") selected @endif>(GMT+8:00) Asia/Kashgar (China Standard Time)</option>
                            <option value="Asia/Kuala_Lumpur"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Kuala_Lumpur") selected @endif>(GMT+8:00) Asia/Kuala_Lumpur (Malaysia Time)</option>
                            <option value="Asia/Kuching"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Kuching") selected @endif>(GMT+8:00) Asia/Kuching (Malaysia Time)</option>
                            <option value="Asia/Macao"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Macao") selected @endif>(GMT+8:00) Asia/Macao (China Standard Time)</option>
                            <option value="Asia/Macau"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Macau") selected @endif>(GMT+8:00) Asia/Macau (China Standard Time)</option>
                            <option value="Asia/Makassar"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Makassar") selected @endif>(GMT+8:00) Asia/Makassar (Central Indonesia Time)</option>
                            <option value="Asia/Manila"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Manila") selected @endif>(GMT+8:00) Asia/Manila (Philippines Time)</option>
                            <option value="Asia/Shanghai"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Shanghai") selected @endif>(GMT+8:00) Asia/Shanghai (China Standard Time)</option>
                            <option value="Asia/Singapore"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Singapore") selected @endif>(GMT+8:00) Asia/Singapore (Singapore Time)</option>
                            <option value="Asia/Taipei"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Taipei") selected @endif>(GMT+8:00) Asia/Taipei (China Standard Time)</option>
                            <option value="Asia/Ujung_Pandang"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Ujung_Pandang") selected @endif>(GMT+8:00) Asia/Ujung_Pandang (Central Indonesia Time)</option>
                            <option value="Asia/Ulaanbaatar"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Ulaanbaatar") selected @endif>(GMT+8:00) Asia/Ulaanbaatar (Ulaanbaatar Time)</option>
                            <option value="Asia/Ulan_Bator"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Ulan_Bator") selected @endif>(GMT+8:00) Asia/Ulan_Bator (Ulaanbaatar Time)</option>
                            <option value="Asia/Urumqi"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Urumqi") selected @endif>(GMT+8:00) Asia/Urumqi (China Standard Time)</option>
                            <option value="Australia/Perth"@if(old('time_zone', @$student->
                                user->time_zone)=="Australia/Perth") selected @endif>(GMT+8:00) Australia/Perth (Western Standard Time (Australia))</option>
                            <option value="Australia/West"@if(old('time_zone', @$student->
                                user->time_zone)=="Australia/West") selected @endif>(GMT+8:00) Australia/West (Western Standard Time (Australia))</option>
                            <option value="Australia/Eucla"@if(old('time_zone', @$student->
                                user->time_zone)=="Australia/Eucla") selected @endif>(GMT+8:45) Australia/Eucla (Central Western Standard Time (Australia))</option>
                            <option value="Asia/Dili"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Dili") selected @endif>(GMT+9:00) Asia/Dili (Timor-Leste Time)</option>
                            <option value="Asia/Jayapura"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Jayapura") selected @endif>(GMT+9:00) Asia/Jayapura (East Indonesia Time)</option>
                            <option value="Asia/Pyongyang"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Pyongyang") selected @endif>(GMT+9:00) Asia/Pyongyang (Korea Standard Time)</option>
                            <option value="Asia/Seoul"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Seoul") selected @endif>(GMT+9:00) Asia/Seoul (Korea Standard Time)</option>
                            <option value="Asia/Tokyo"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Tokyo") selected @endif>(GMT+9:00) Asia/Tokyo (Japan Standard Time)</option>
                            <option value="Asia/Yakutsk"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Yakutsk") selected @endif>(GMT+9:00) Asia/Yakutsk (Yakutsk Time)</option>
                            <option value="Australia/Adelaide"@if(old('time_zone', @$student->
                                user->time_zone)=="Australia/Adelaide") selected @endif>(GMT+9:30) Australia/Adelaide (Central Standard Time (South Australia))</option>
                            <option value="Australia/Broken_Hill"@if(old('time_zone', @$student->
                                user->time_zone)=="Australia/Broken_Hill") selected @endif>(GMT+9:30) Australia/Broken_Hill (Central Standard Time (South Australia/New South Wales))
                            </option>
                            <option value="Australia/Darwin"@if(old('time_zone', @$student->
                                user->time_zone)=="Australia/Darwin") selected @endif>(GMT+9:30) Australia/Darwin (Central Standard Time (Northern Territory))</option>
                            <option value="Australia/North"@if(old('time_zone', @$student->
                                user->time_zone)=="Australia/North") selected @endif>(GMT+9:30) Australia/North (Central Standard Time (Northern Territory))</option>
                            <option value="Australia/South"@if(old('time_zone', @$student->
                                user->time_zone)=="Australia/South") selected @endif>(GMT+9:30) Australia/South (Central Standard Time (South Australia))</option>
                            <option value="Australia/Yancowinna"@if(old('time_zone', @$student->
                                user->time_zone)=="Australia/Yancowinna") selected @endif>(GMT+9:30) Australia/Yancowinna (Central Standard Time (South Australia/New South Wales))</option>
                            <option value="Antarctica/DumontDUrville"@if(old('time_zone', @$student->
                                user->time_zone)=="Antarctica/DumontDUrville") selected @endif>(GMT+10:00) Antarctica/DumontDUrville (Dumont-d'Urville Time)</option>
                            <option value="Asia/Sakhalin"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Sakhalin") selected @endif>(GMT+10:00) Asia/Sakhalin (Sakhalin Time)</option>
                            <option value="Asia/Vladivostok"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Vladivostok") selected @endif>(GMT+10:00) Asia/Vladivostok (Vladivostok Time)</option>
                            <option value="Australia/ACT"@if(old('time_zone', @$student->
                                user->time_zone)=="Australia/ACT") selected @endif>(GMT+10:00) Australia/ACT (Eastern Standard Time (New South Wales))</option>
                            <option value="Australia/Brisbane"@if(old('time_zone', @$student->
                                user->time_zone)=="Australia/Brisbane") selected @endif>(GMT+10:00) Australia/Brisbane (Eastern Standard Time (Queensland))</option>
                            <option value="Australia/Canberra"@if(old('time_zone', @$student->
                                user->time_zone)=="Australia/Canberra") selected @endif>(GMT+10:00) Australia/Canberra (Eastern Standard Time (New South Wales))</option>
                            <option value="Australia/Currie"@if(old('time_zone', @$student->
                                user->time_zone)=="Australia/Currie") selected @endif>(GMT+10:00) Australia/Currie (Eastern Standard Time (New South Wales))</option>
                            <option value="Australia/Hobart"@if(old('time_zone', @$student->
                                user->time_zone)=="Australia/Hobart") selected @endif>(GMT+10:00) Australia/Hobart (Eastern Standard Time (Tasmania))</option>
                            <option value="Australia/Lindeman"@if(old('time_zone', @$student->
                                user->time_zone)=="Australia/Lindeman") selected @endif>(GMT+10:00) Australia/Lindeman (Eastern Standard Time (Queensland))</option>
                            <option value="Australia/Melbourne"@if(old('time_zone', @$student->
                                user->time_zone)=="Australia/Melbourne") selected @endif>(GMT+10:00) Australia/Melbourne (Eastern Standard Time (Victoria))</option>
                            <option value="Australia/NSW"@if(old('time_zone', @$student->
                                user->time_zone)=="Australia/NSW") selected @endif>(GMT+10:00) Australia/NSW (Eastern Standard Time (New South Wales))</option>
                            <option value="Australia/Queensland"@if(old('time_zone', @$student->
                                user->time_zone)=="Australia/Queensland") selected @endif>(GMT+10:00) Australia/Queensland (Eastern Standard Time (Queensland))</option>
                            <option value="Australia/Sydney"@if(old('time_zone', @$student->
                                user->time_zone)=="Australia/Sydney") selected @endif>(GMT+10:00) Australia/Sydney (Eastern Standard Time (New South Wales))</option>
                            <option value="Australia/Tasmania"@if(old('time_zone', @$student->
                                user->time_zone)=="Australia/Tasmania") selected @endif>(GMT+10:00) Australia/Tasmania (Eastern Standard Time (Tasmania))</option>
                            <option value="Australia/Victoria"@if(old('time_zone', @$student->
                                user->time_zone)=="Australia/Victoria") selected @endif>(GMT+10:00) Australia/Victoria (Eastern Standard Time (Victoria))</option>
                            <option value="Australia/LHI"@if(old('time_zone', @$student->
                                user->time_zone)=="Australia/LHI") selected @endif>(GMT+10:30) Australia/LHI (Lord Howe Standard Time)</option>
                            <option value="Australia/Lord_Howe"@if(old('time_zone', @$student->
                                user->time_zone)=="Australia/Lord_Howe") selected @endif>(GMT+10:30) Australia/Lord_Howe (Lord Howe Standard Time)</option>
                            <option value="Asia/Magadan"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Magadan") selected @endif>(GMT+11:00) Asia/Magadan (Magadan Time)</option>
                            <option value="Antarctica/McMurdo"@if(old('time_zone', @$student->
                                user->time_zone)=="Antarctica/McMurdo") selected @endif>(GMT+12:00) Antarctica/McMurdo (New Zealand Standard Time)</option>
                            <option value="Antarctica/South_Pole"@if(old('time_zone', @$student->
                                user->time_zone)=="Antarctica/South_Pole") selected @endif>(GMT+12:00) Antarctica/South_Pole (New Zealand Standard Time)</option>
                            <option value="Asia/Anadyr"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Anadyr") selected @endif>(GMT+12:00) Asia/Anadyr (Anadyr Time)</option>
                            <option value="Asia/Kamchatka"@if(old('time_zone', @$student->
                                user->time_zone)=="Asia/Kamchatka") selected @endif>(GMT+12:00) Asia/Kamchatka (Petropavlovsk-Kamchatski Time)</option>
                        </select>
                        <span class="error-message" id="time_zone_error"></span>
                    </div>

                    <div class="input-group mb-3">
                        <input id="phone" class="form-control" type="tel" name="phone" />
                        <span class="error-message" id="phone_error"></span>
                    </div>
                    <input type="hidden" name="area_code" id="area_code" value="">
                    <div class="input-group mb-3">
                        <select id="country-id" name="country_id" class="form-control"
                            placeholder="Select Country">
                            <option value="">Select Country</option>
                        @if (!$countries->isEmpty())
                            @foreach ($countries as $country)
                                <option value="{{$country->id}}">{{$country->country_name}}</option>
                            @endforeach
                        @endif
                        </select>
                        <span class="error-message" id="country_id_error"></span>
                    </div>
                    <div class="input-group mb-3">
                        <input id="referral_code" name="referral_code" type="text" class="form-control" placeholder="Referral/Promo Code"
                            aria-label="Referral/Promo Code" aria-describedby="Code">
                            <span class="error-message" id="referral_code_error"></span>
                    </div>
                       
                <div class="learning">
                    <label style="font-size:15px" for="1">Live</label>
                    <input class="st_type" name="st_type" type="checkbox" id="1"  value="1" @if($typeCourse == 1) checked @endif>
               
                    <label style="margin-left:15px ; font-size:15px" for="2">Self Learning</label>
                    <input class="st_type" name="st_type" type="checkbox" id="2"  value="2" @if($typeCourse == 2) checked @endif>
                    <span class="error-message" id="st_type_error"></span>
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
                            <input type="radio" id="1" value="1" name="no_of_joined_students" />
                            <label for="1" class="rounded-full">1</label>
                        </li>
                        <li class="w-10">
                            <input type="radio" id="2" value="2" name="no_of_joined_students" />
                            <label for="2" class="rounded-full">2</label>
                        </li>
                        <li class="w-10">
                            <input type="radio" id="3" value="3" name="no_of_joined_students" />
                            <label for="3" class="rounded-full">3</label>
                        </li>
                        <li class="w-10">
                            <input type="radio" id="4" value="4" name="no_of_joined_students" />
                            <label for="4" class="rounded-full">4</label>
                        </li>
                        <li class="w-10">
                            <input type="radio" id="5" value="5" name="no_of_joined_students" />
                            <label for="5" class="rounded-full">5</label>
                        </li>
                        <span class="error-message" id="no_of_joined_students_error"></span>
                    </ul>
                </div>

                <div class="" id="step-2-field-2">
                    <h6>For your evaluation, do you prefer</h6>
                    <ul class="multiple-select">
                        <li class="w-100">
                            <input type="radio" id="Male" value="Male" name="preferred_teacher" />
                            <label for="Male">Male Teacher</label>
                        </li>
                        <li class="w-100">
                            <input type="radio" id="Female" value="Female" name="preferred_teacher" />
                            <label for="Female">Female Teacher</label>
                        </li>
                        <li class="w-100">
                            <input type="radio" id="Either" value="Either" name="preferred_teacher" />
                            <label for="Either">Either</label>
                        </li>
                        <span class="error-message" id="preferred_teacher_error"></span>
                    </ul>
                </div>

                    <h6>How did you learn about CEF Online?</h6>
                    <ul class="multiple-select">
                        <li>
                            <input type="radio" id="A Friend" value="A Friend" name="learn_about_lms" />
                            <label for="A Friend">A Friend</label>
                        </li>
                        <li>
                            <input type="radio" id="Social Media" value="Social Media" name="learn_about_lms" />
                            <label for="Social Media">Social Media</label>
                        </li>
                        <li>
                            <input type="radio" id="E-Mail" value="E-Mail" name="learn_about_lms" />
                            <label for="E-Mail">E-Mail</label>
                        </li>
                        <li>
                            <input type="radio" id="Other" value="Other" name="learn_about_lms" />
                            <label for="Other">Other</label>
                        </li>
                        <span class="error-message" id="learn_about_lms_error"></span>
                    </ul>
                </div>
                <div class="step-content" id="step-3">
                    <h6 class="text-center mt-3 mb-4">Pick a date and time for your free trial lesson (1 hour)</h6>
                    <div id="wrapper" class="wrapper mb-2">
                        <div class="container-calendar">
                            <div class="d-flex justify-content-between align-items-center px-4">
                                <h6 class="w-50" id="monthAndYear"></h6>
                                <div class="button-container-calendar">
                                    {{-- <button id="previous" type="button" onclick="previous()">&#8249;</button>
                                    <button id="next" type="button" onclick="next()">&#8250;</button> --}}
                                    <button id="previous" type="button" disabled>&#8249;</button>
                                    <button id="next" type="button">&#8250;</button>
                                </div>
                            </div>
                            <table class="table-calendar" id="calendar" data-lang="en">
                                <thead id="thead-month"></thead>
                                <tbody id="calendar-body"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="preferDate" id="preferDate" value="">
                <input type="hidden" name="selectedDate" id="selectedDate" value="">
                <input type="hidden" name="selectedMonth" id="selectedMonth" value="">
                <input type="hidden" name="selectedYear" id="selectedYear" value="">
           
                <div class="step-content" id="step-4">
                    <h6 class="text-center mt-3 mb-2">Select Slot</h6>
                    <div id="slots" class="slots mb-2 mt-4">
                        <div class="w-80 mx-auto d-flex flex-col flex-wrap">
                        <div class="slot">
                            <label for="1:00 AM">
                                <input type="radio" name="preferTime" id="1:00 AM" value="1:00 AM" checked>
                            1:00 AM
                            </label>
                        </div>
                       
                          <div class="slot">
                            <label for="12:00 PM">
                                <input type="radio" name="preferTime" id="12:00 PM" value="12:00 PM">
                                12:00 PM
                            </label>
                        </div>
                        <div class="slot">
                            <label for="12:15 PM">
                                <input type="radio" name="preferTime" id="12:15 PM" value="12:15 PM">
                                12:15 PM
                            </label>
                        </div>
                          
                        <div class="slot">
                            <label for="12:30 PM">
                                <input type="radio" name="preferTime" id="12:30 PM" value="12:30 PM">
                                12:30 PM
                          
                            </label>
                        </div>
                        <div class="slot">
                            <label for="11:30 PM">
                                <input type="radio" name="preferTime" id="11:30 PM" value="11:30 PM">
                                11:30 PM
                            </label>
                        </div>
                            {{-- <div class="slot">11:30 PM</div> --}}
                        </div>
                    </div>
                </div>
            </form>
            <div class="button-container w-100 px-4">
                <button id="nextBtn" class="btn btn-primary w-100 my-2">Next</button>
                <button id="prevBtn" class="btn btn-secondary w-100" disabled>Previous</button>
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
                            @if($course->learner_accessibility != 'free' && get_option('cashback_system_mode', 0))
                            <div class="alert alert-success d-flex mb-15">
                                <div class="align-items-center d-flex mr-15">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-credit-card"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                                </div>
                    
                                <div class="ml-10">
                                    <div class="font-14 font-weight-bold ">{{ __("Get Cashback") }}</div>
                                    <div class="font-12 ">{{ __("By purchasing this product, you will get")}}
                                    @if($currencyPlacement ?? get_currency_placement() == 'after')
                                        {{calculateCashback($course->price) }} {{ $currencySymbol ?? get_currency_symbol() }}
                                    @else
                                        {{ $currencySymbol ?? get_currency_symbol() }} {{calculateCashback($course->price) }}
                                    @endif
                                    {{ __("cashback") }}.</div>
                                </div>
                            </div>
                            @endif
                    
                            @else
                                <button class="bg-warning mb-30 theme-btn w-100">
                                    <span class="msgInfoChange">{{ __('Upcoming') }}</span>
                                </button>
                            @endif
                        @endif

                        {{-- @if(get_option('course_gift_mode', 0))
                        <a href="{{ route('student.course-gift', $course->uuid) }}" class="alert alert-warning align-items-center border border-dark d-flex mb-30 p-2">
                            <div class="mr-15">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-gift text-gray"><polyline points="20 12 20 22 4 22 4 12"></polyline><rect x="2" y="7" width="20" height="5"></rect><line x1="12" y1="22" x2="12" y2="7"></line><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path></svg>
                            </div>
                            <div class="ml-5">
                                <h4 class="font-14 font-weight-bold text-gray">{{ __("Gift this course") }}</h4>
                                <p class="font-12 text-gray">{{ __("Send this course as a gift to your friends") }}</p>
                            </div>
                        </a>
                        @endif --}}


                        {{-- <div class="course-info-box-wishlist-btns d-flex mb-30">
                            <button class="theme-btn para-color font-medium mx-2 addToWishlist" title="Add to wishlist" data-course_id="{{ $course->id }}" data-route="{{ route('student.addToWishlist') }}"><span class="iconify me-2" data-icon="bytesize:heart"></span>{{ __('Add to Wishlist') }}</button>
                            <button class="theme-btn para-color font-medium mx-2" title="Share this course" data-bs-toggle="modal" data-bs-target="#shareThisCourseModal"><span class="iconify me-2" data-icon="ci:share-outline"></span>{{ __('Share Course') }}</button>
                        </div> --}}

                        {{-- <div class="course-includes-box mb-30">
                            <h6 class="pb-30">{{ __('This Course Includes') }}</h6>
                            <ul>
                                @if($course->course_type == COURSE_TYPE_GENERAL)
                                <li>
                                    <span class="iconify" data-icon="bi:camera-video"></span>
                                    <span>{{@$course->lectures->count() > 0 ? @$course->video_duration : '0'}} {{ __('Video Lectures') }}</span>
                                </li>
                                @endif
                                <li>
                                    <span class="iconify" data-icon="healthicons:i-exam-multiple-choice-outline"></span>
                                    <span>{{ @$course->quizzes->count() }} {{ __('Quizzes') }}</span>
                                </li>
                                <li>
                                    <span class="iconify" data-icon="bi:book"></span>
                                    <span>{{ @$course->assignments->count() }} {{ __('Assignments') }}</span>
                                </li>
                                <li>
                                    <span class="iconify" data-icon="akar-icons:download"></span>
                                    <span>{{ @$course->resources->count() }} {{ __('Downloadable Resources') }}</span>
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
                        </div> --}}

                        {{-- @if(@Auth::user() && Auth::user()->is_affiliator == AFFILIATOR && get_option('referral_status'))
                        <div class="course-info-box-affiliate-link-copy position-relative">
                            <h6 class="pb-20">Affiliate Link:</h6>
                            <div class="input-group">
                                <input id="ref_link" class="form-control w-100" value="{{ $ref_link }}"/>
                                <button type="button" class="btn affiliate-copy-btn border-0 position-absolute" onclick="copyToClipboard('ref_link')" title="{{ __('Copy to Clipboard') }}">
                                    <i class="fa fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        @endif --}}

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Course Single Details Area End -->
</div>

<!--Share This Course Modal Start-->
<div class="modal fade" id="shareThisCourseModal" tabindex="-1" aria-labelledby="shareThisCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="shareThisCourseModalLabel">{{ __('Share This Course') }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="share-course-list">
                    <li><a href="http://www.facebook.com/sharer.php?u={{ route('course-details', $course->slug) }}"><span class="iconify" data-icon="cib:facebook-f"></span></a></li>
                    <li><a href="https://twitter.com/share?url={{ route('course-details', $course->slug) }}"><span class="iconify" data-icon="el:twitter"></span></a></li>
                    <li><a href="https://www.linkedin.com/shareArticle?url={{ route('course-details', $course->slug) }}"><span class="iconify" data-icon="cib:linkedin-in"></span></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!--Share This Course Modal End-->

<input type="hidden" class="courseReviewPaginateRoute" value="{{ route('frontend.reviewPaginate', $course->id) }}">

<!-- New Video Player Modal Start-->
<div class="modal fade VideoTypeModal" id="newVideoPlayerModal" tabindex="-1" aria-labelledby="newVideoPlayerModal" aria-hidden="true">

    <div class="modal-header border-bottom-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span class="iconify" data-icon="akar-icons:cross"></span></button>
    </div>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="video-player-area">
                    @if($course->intro_video_check == 1 && getVideoFile($course->video))
                    <!-- HTML 5 Video -->
                    <video id="player2" playsinline controls data-poster="{{ getImageFile($course->image) }}" controlsList="nodownload">
                        <source src="{{ getVideoFile($course->video) }}" type="video/mp4" >
                    </video>
                    @endif
                    @if($course->intro_video_check == 2 && $course->youtube_video_id)
                        <div class="plyr__video-embed" id="youtubePlayer2">
                            <iframe
                                src="https://www.youtube.com/embed/{{ @$course->youtube_video_id }}"
                                allowfullscreen
                                allowtransparency
                                allow="autoplay">
                            </iframe>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!-- New Video Player Modal End-->

<!--  Text Upload Modal Start -->
<div class="modal fade textUploadModal venoBoxTypeModal" id="textUploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-header border-bottom-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span class="iconify" data-icon="akar-icons:cross"></span></button>
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
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span class="iconify" data-icon="akar-icons:cross"></span></button>
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
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span class="iconify" data-icon="akar-icons:cross"></span></button>
    </div>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <iframe class="getLectureSlide" src="" width="100%" height="400" frameborder="0" scrolling="no"></iframe>
            </div>
        </div>
    </div>
</div>
<!-- Slide Show Upload Modal End-->

<!-- Audio Player Modal Start-->
<div class="modal fade venoBoxTypeModal" id="audioPlayerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-header border-bottom-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span class="iconify" data-icon="akar-icons:cross"></span></button>
    </div>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <!--Audio -->
                <audio class="getLectureAudio" src="" type="audio/mp3" style="width: 550px" controls controlsList="nodownload">
                </audio>
            </div>
        </div>
    </div>
</div>
<!-- Audio Player Modal End-->
@endsection

@push('style')
    <!-- Video Player css -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/vendor/video-player/plyr.css') }}">
@endpush

@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/js/selectize.min.js"
    integrity="sha512-IOebNkvA/HZjMM7MxL0NYeLYEalloZ8ckak+NDtOViP7oiYzG5vn6WVXyrJDiJPhl4yRdmNAG49iuLmhkUdVsQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>


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



    </script>

    <script>
        $(document).ready(function () {
            $('#country-id').selectize({ create: false, searchField: 'value', onDropdownOpen: function($dropdown) { this.clear(); } });
        });
    </script>
    <script>
        $(document).ready(function () {
                var $select = $('#time_zone').selectize({
                    create: false,
                    searchField: 'value',
                    onDropdownOpen: function($dropdown) {
                        this.clear();
                    }
                });
            });
    </script>
<style>
    th,
    td {
        max-width: 3rem !important;
        min-width: 3rem !important;
        text-align: center !important;
        height: 3rem;
        color: white !important;
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
        background-color: transparent !important;
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
        max-height: 7rem !important;
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

    .selectize-input.items.has-options.full.has-items {
        border-radius: 4rem;
        padding: 6px 4px 6px 14px ! IMPORTANT;
        text-align: start;
        display: flex;
        height: 2rem !important;
        margin-top: -0.5rem;
    margin-left: -1rem !important;
    }

    ::placeholder{
        color:black !important;
    }

    .selectize-input.items.has-options.full.has-items input#country-id-selectized,
    .selectize-input.items.has-options.full.has-items input#time_zone-selectized {
        display: none !important
    }

    .single.selectize-dropdown.form-control {
    position: absolute; z-index: 9999; width: 100% !important; top: 2.4rem !important; left: 0 !important; overflow-y: auto !important;
}

    .selectize-input.items.has-options.full.has-items .item {
        margin-top: 0.5rem;
        overflow: hidden !important;
        height: 1.2rem;
        text-overflow: ellipsis;
        white-space: nowrap;
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

    .iti__country-list {
        overflow-x: scroll !important;
        width: 28vw !important;
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
        color: var(--theme-color) !important;
    }

    #back {
        position: absolute;
        font-size: 50px;
        top: -1rem;
    }

    #slots {
        position: relative;
    }

    span.iti__country-name,
    span.iti__dial-code {
        color: black !important;
        font-size: 0.8rem !important;
    }

    .selectize-control.single .selectize-input {
        background: transparent !important;
    }

    input#country-id-selectized,
    input#time_zone-selectized {
        width: calc(100% - 27px) !important;
        padding: 0 12px !important;
        margin-top: 0.4rem !important;
    margin-left: -0.8rem !important;
    }

    .selectize-control {
        position: relative;
        width: 100% !important;
    }

    .selectize-input.items.not-full.has-options {
        padding: 0 !important;
        width: 100% !important;
    }

    .selectize-dropdown-content {
        text-align: start !important;
    }

    .learning {
        display: flex;
        width: 100%;
        flex-direction: row;
        justify-content: space-evenly;
        align-items: baseline;
        height: 2rem !important;
        position: relative !important;
    }

    span.error-message {
        position: absolute !important;
        text-align: start !important;
        font-size: 0.6rem !important;
        left: 0 !important;
        top: 3.1rem !important;
    }

    button#prevBtn {
        margin-top: 0 !important;
    }

    .reg-page .step::after {
    top: 1rem !important;
}

.reg-page svg.bi.bi-check-lg {
    position: absolute !important;
    top: 0.44rem !important;
}

.reg-page th, td{
    color: black !important;
}

.reg-page ul.progressBar li .step {
    display: flex !important;
    place-items: center !important;
    justify-content: center !important;
}
.reg-page .step::after {
width: 0 !important;
}
}
</style>
@endpush

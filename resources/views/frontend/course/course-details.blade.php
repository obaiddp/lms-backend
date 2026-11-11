<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
@extends('frontend.layouts.app') @php $relation = getUserRoleRelation($course->user); @endphp

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

    .course-details-header-img {
        /* height: 30rem !important; */
    }

    .course-details-header-img2 {
        margin-top: 20px !important;
        padding: 0 !important;
        /* height: 34rem !important;
                min-height: 34rem !important; */
    }

    /* New */
    .course-header-bg {
        position: relative;
        width: 100%;
        height: auto;
    }

    .svg-mask-container {
        position: relative;
        width: 100%;
        height: 388px;
    }

    .svg-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: auto;
        z-index: 2;
        /* Ensure it appears above the image */
    }

    .course-image-masked {
        width: 100vw;
        height: 100%;
        object-fit: fill;
    }

    .svg-mask {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
    }
</style>
@endsection

@section('content')
<div>
    <header
        class="course-single-page-header gradient-bg position-relative course-details-header-img position-relative">
        <div class="course-header">
            <svg width="1440" height="581" viewBox="0 0 1440 581" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <g opacity="0.1">
                    <path d="M-0.5 125H1439.5" stroke="white" />
                    <path d="M-0.5 112H1439.5" stroke="white" />
                    <path d="M-0.5 99H1439.5" stroke="white" />
                    <path d="M-0.5 86H1439.5" stroke="white" />
                    <path d="M-0.5 73H1439.5" stroke="white" />
                    <path d="M-0.5 60H1439.5" stroke="white" />
                    <path d="M-0.5 47H1439.5" stroke="white" />
                    <path d="M-0.5 34H1439.5" stroke="white" />
                    <path d="M-0.5 21H1439.5" stroke="white" />
                    <path d="M-0.5 8H1439.5" stroke="white" />
                    <path d="M-1 138H1439" stroke="white" />
                    <path d="M-1 151H1439" stroke="white" />
                    <path d="M-1 164H1439" stroke="white" />
                    <path d="M-1 177H1439" stroke="white" />
                    <path d="M-1 190H1439" stroke="white" />
                    <path d="M-1 203H1439" stroke="white" />
                    <path d="M-1 216H1439" stroke="white" />
                    <path d="M-1 229H1439" stroke="white" />
                    <path d="M-1 242H1439" stroke="white" />
                    <path d="M-1 255H1439" stroke="white" />
                    <path d="M-1 268H1439" stroke="white" />
                    <path d="M-1 281H1439" stroke="white" />
                    <path d="M-1 294H1439" stroke="white" />
                    <path d="M-1 307H1439" stroke="white" />
                    <path d="M-1 320H1439" stroke="white" />
                    <path d="M-1 333H1439" stroke="white" />
                    <path d="M-1 346H1439" stroke="white" />
                    <path d="M-1 359H1439" stroke="white" />
                    <path d="M-1 372H1439" stroke="white" />
                    <path d="M-1 385H1439" stroke="white" />
                    <path d="M-1 398H1439" stroke="white" />
                    <path d="M-1 411H1439" stroke="white" />
                    <path d="M-1 424H1439" stroke="white" />
                    <path d="M-1 437H1439" stroke="white" />
                    <path d="M-1 450H1439" stroke="white" />
                    <path d="M-1 463H1439" stroke="white" />
                    <path d="M-1 476H1439" stroke="white" />
                    <path d="M-1 489H1439" stroke="white" />
                    <path d="M-1 502H1439" stroke="white" />
                    <path d="M-1 515H1439" stroke="white" />
                    <path d="M-1 528H1439" stroke="white" />
                    <path d="M-1 541H1439" stroke="white" />
                    <path d="M-1 554H1439" stroke="white" />
                    <path d="M-1 567H1439" stroke="white" />
                    <path d="M-1 580H1439" stroke="white" />
                    <path d="M12 -5V580" stroke="white" />
                    <path d="M25 -5V580" stroke="white" />
                    <path d="M38 -5V580" stroke="white" />
                    <path d="M51 -5V580" stroke="white" />
                    <path d="M64 -5V580" stroke="white" />
                    <path d="M77 -5V580" stroke="white" />
                    <path d="M90 -5V580" stroke="white" />
                    <path d="M103 -5V580" stroke="white" />
                    <path d="M116 -5V580" stroke="white" />
                    <path d="M129 -5V580" stroke="white" />
                    <path d="M142 -5V580" stroke="white" />
                    <path d="M155 -5V580" stroke="white" />
                    <path d="M168 -5V580" stroke="white" />
                    <path d="M181 -5V580" stroke="white" />
                    <path d="M194 -5V580" stroke="white" />
                    <path d="M207 -5V580" stroke="white" />
                    <path d="M220 -5V580" stroke="white" />
                    <path d="M233 -5V580" stroke="white" />
                    <path d="M246 -5V580" stroke="white" />
                    <path d="M259 -5V580" stroke="white" />
                    <path d="M272 -5V580" stroke="white" />
                    <path d="M285 -5V580" stroke="white" />
                    <path d="M298 -5V580" stroke="white" />
                    <path d="M311 -5V580" stroke="white" />
                    <path d="M324 -5V580" stroke="white" />
                    <path d="M337 -5V580" stroke="white" />
                    <path d="M350 -5V580" stroke="white" />
                    <path d="M363 -5V580" stroke="white" />
                    <path d="M376 -5V580" stroke="white" />
                    <path d="M389 -5V580" stroke="white" />
                    <path d="M402 -5V580" stroke="white" />
                    <path d="M415 -5V580" stroke="white" />
                    <path d="M428 -5V580" stroke="white" />
                    <path d="M441 -5V580" stroke="white" />
                    <path d="M454 -5V580" stroke="white" />
                    <path d="M467 -5V580" stroke="white" />
                    <path d="M480 -5V580" stroke="white" />
                    <path d="M493 -5V580" stroke="white" />
                    <path d="M506 -5V580" stroke="white" />
                    <path d="M519 -5V580" stroke="white" />
                    <path d="M532 -5V580" stroke="white" />
                    <path d="M545 -5V580" stroke="white" />
                    <path d="M558 -5V580" stroke="white" />
                    <path d="M571 -5V580" stroke="white" />
                    <path d="M584 -5V580" stroke="white" />
                    <path d="M597 -5V580" stroke="white" />
                    <path d="M610 -5V580" stroke="white" />
                    <path d="M623 -5V580" stroke="white" />
                    <path d="M636 -5V580" stroke="white" />
                    <path d="M649 -5V580" stroke="white" />
                    <path d="M662 -5V580" stroke="white" />
                    <path d="M675 -5V580" stroke="white" />
                    <path d="M688 -5V580" stroke="white" />
                    <path d="M701 -5V580" stroke="white" />
                    <path d="M714 -5V580" stroke="white" />
                    <path d="M727 -5V580" stroke="white" />
                    <path d="M740 -5V580" stroke="white" />
                    <path d="M753 -5V580" stroke="white" />
                    <path d="M766 -5V580" stroke="white" />
                    <path d="M779 -5V580" stroke="white" />
                    <path d="M792 -5V580" stroke="white" />
                    <path d="M805 -5V580" stroke="white" />
                    <path d="M818 -5V580" stroke="white" />
                    <path d="M831 -5V580" stroke="white" />
                    <path d="M844 -5V580" stroke="white" />
                    <path d="M857 -5V580" stroke="white" />
                    <path d="M870 -5V580" stroke="white" />
                    <path d="M883 -5V580" stroke="white" />
                    <path d="M896 -5V580" stroke="white" />
                    <path d="M909 -5V580" stroke="white" />
                    <path d="M922 -5V580" stroke="white" />
                    <path d="M935 -5V580" stroke="white" />
                    <path d="M948 -5V580" stroke="white" />
                    <path d="M961 -5V580" stroke="white" />
                    <path d="M974 -5V580" stroke="white" />
                    <path d="M987 -5V580" stroke="white" />
                    <path d="M1000 -5V580" stroke="white" />
                    <path d="M1013 -5V580" stroke="white" />
                    <path d="M1026 -5V580" stroke="white" />
                    <path d="M1039 -5V580" stroke="white" />
                    <path d="M1052 -5V580" stroke="white" />
                    <path d="M1065 -5V580" stroke="white" />
                    <path d="M1078 -5V580" stroke="white" />
                    <path d="M1091 -5V580" stroke="white" />
                    <path d="M1104 -5V580" stroke="white" />
                    <path d="M1117 -5V580" stroke="white" />
                    <path d="M1130 -5V580" stroke="white" />
                    <path d="M1143 -5V580" stroke="white" />
                    <path d="M1156 -5V580" stroke="white" />
                    <path d="M1169 -5V580" stroke="white" />
                    <path d="M1182 -5V580" stroke="white" />
                    <path d="M1195 -5V580" stroke="white" />
                    <path d="M1208 -5V580" stroke="white" />
                    <path d="M1221 -5V580" stroke="white" />
                    <path d="M1234 -5V580" stroke="white" />
                    <path d="M1247 -5V580" stroke="white" />
                    <path d="M1260 -5V580" stroke="white" />
                    <path d="M1273 -5V580" stroke="white" />
                    <path d="M1286 -5V580" stroke="white" />
                    <path d="M1299 -5V580" stroke="white" />
                    <path d="M1312 -5V580" stroke="white" />
                    <path d="M1325 -5V580" stroke="white" />
                    <path d="M1338 -5V580" stroke="white" />
                    <path d="M1351 -5V580" stroke="white" />
                    <path d="M1364 -5V580" stroke="white" />
                    <path d="M1377 -5V580" stroke="white" />
                    <path d="M1390 -5V580" stroke="white" />
                    <path d="M1403 -5V580" stroke="white" />
                    <path d="M1416 -5V580" stroke="white" />
                    <path d="M1429 -5V580" stroke="white" />
                </g>
            </svg>
        </div>

        <div class="course-header-bg mx-auto">
            <img class="course-image-masked" src="{{ $course->cover_image ? asset($course->cover_image) : getImageFile($course->image) }}">
        </div>
    </header>

    <div class="courses-crumb course-details-crumb">
        <nav aria-label="breadcrumb" class="home-page-container">
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
                <li class="breadcrumb-item font-14" aria-current="page">{{ __('Courses') }}</li>
                <li class="breadcrumb-item font-14 active" aria-current="page">{{ __($course->title) }}</li>
            </ol>
        </nav>
    </div>

    <section class="course-single-details-area before-login-purchase-course-details">
        <div class="home-page-container">
            <div class="row ms-0.5 xl:ms-0" style="width:98%;">
                <div class="col-12 col-md-12 col-lg-8 order-1 first-div-course-detail">
                    <h3 class="page-banner-heading pb-20 cats-text ps-0 mt-0" style="padding: 16px 30px">
                        {{ __($course->title) }}
                    </h3>
                   
                    @if($course->course_type == 3)
                        <div
                            class="course-price-box d-flex justify-content-between align-items-center mt-30 mb-30">
                            <h4 class="d-flex align-items-center mb-1"> <i>{{__('Live sessions with assigned tutor. Apply to join.')}}</i> </h4>
                        </div>
                    @endif
                    <div class="course-rating-wrap mt-2 ">
                        @if ($course->average_rating > 0)
                            <div class="course-rating">
                                <svg width="16" height="15" viewBox="0 0 16 15" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M7.04894 0.92705C7.3483 0.00573921 8.6517 0.00573969 8.95106 0.92705L10.0206 4.21885C10.1545 4.63087 10.5385 4.90983 10.9717 4.90983H14.4329C15.4016 4.90983 15.8044 6.14945 15.0207 6.71885L12.2205 8.75329C11.87 9.00793 11.7234 9.4593 11.8572 9.87132L12.9268 13.1631C13.2261 14.0844 12.1717 14.8506 11.388 14.2812L8.58778 12.2467C8.2373 11.9921 7.7627 11.9921 7.41221 12.2467L4.61204 14.2812C3.82833 14.8506 2.77385 14.0844 3.0732 13.1631L4.14277 9.87132C4.27665 9.4593 4.12999 9.00793 3.7795 8.75329L0.979333 6.71885C0.195619 6.14945 0.598395 4.90983 1.56712 4.90983H5.02832C5.46154 4.90983 5.8455 4.63087 5.97937 4.21885L7.04894 0.92705Z"
                                        fill="#FFA439" />
                                </svg>
                                <span>{{ number_format($course->average_rating, 1) }}
                                    ({{ number_format($total_user_review) }} reviews)</span>
                            </div>
                        @endif
                        
                        <button class="share-course" data-bs-toggle="modal"
                            data-bs-target="#shareThisCourseModal">
                            <span>Share Course</span>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M15 5.83331C15.6454 5.96713 16.1323 6.19047 16.5237 6.56322C17.5 7.49315 17.5 8.9899 17.5 11.9833C17.5 14.9767 17.5 16.4734 16.5237 17.4034C15.5474 18.3333 13.976 18.3333 10.8333 18.3333H9.16667C6.02397 18.3333 4.45262 18.3333 3.47631 17.4034C2.5 16.4734 2.5 14.9767 2.5 11.9833C2.5 8.9899 2.5 7.49315 3.47631 6.56322C3.86765 6.19047 4.35458 5.96713 5 5.83331"
                                    stroke="black" stroke-width="1.25" stroke-linecap="round" />
                                <path
                                    d="M10.0211 1.66712L10 11.6667M10.0211 1.66712C9.88558 1.66152 9.74925 1.70995 9.62775 1.81246C8.87242 2.45007 7.5 4.10741 7.5 4.10741M10.0211 1.66712C10.1426 1.67217 10.2635 1.72066 10.3723 1.81259C11.1276 2.45033 12.5 4.10741 12.5 4.10741"
                                    stroke="black" stroke-width="1.25" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-8 second-div-course-detail">
                    <div class="course-single-details-left-content bg-white">
                        <div class="instructor-profile-header">
                            <div class="instructor-title">
                                <div class="ins-image">
                                    {{-- <img src="https://cefonlineacademy.com/uploads/default/instructor-default.png"
                                        alt=""> --}}
                                          <img src=" {{ @$course->instructor?->user?->image_url}}"
                                        alt="">
                                </div>
                                <div class="ins-info">
                                    <h6>Instructor/Teacher</h6>
                                    <h3 class="instructor-name">
                                        {{-- {{ @$course->course_instructors->first()->user->name }} --}}
                                        {{ @$course->instructor?->first_name.' '.  @$course->instructor?->last_name}}
                                    </h3>
                                </div>
                            </div>
                            @if ($course->classes > 0)
                            <div class="ins-info instructor-new instructor-new-1">
                                <h6>Classes</h6>
                                <h3>
                                    {{-- {{ @$course->lectures->count() > 0 ? @$course->video_duration : '0' }} --}}
                                    {{ @$course->classes > 0 ? @$course->classes : '0' }}
                                </h3>
                            </div>
                            @endif

                            <div class="ins-info instructor-new">
                                <h6>Category</h6>
                                <h3>{{ @$course->category->name }}</h3>
                            </div>
                        </div>
                        <h1 class="course-description-title">Description</h1>

                        @include('frontend.course.partial.partial-overview-tab')
                        @include('frontend.course.partial.partial-curriculum-tab')
                        @include('frontend.course.partial.partial-instructor-tab')
                        @include('frontend.course.partial.partial-review-tab')

                    </div>

                    <div class="col-12 col-md-12 col-lg-4 third-div-course-detail">
                        <div class="course-single-details-right-content">

                            <div class="bg-white">
                                <div
                                    class="video-area-left position-relative d-flex align-items-center justify-content-center">
                                    <div class="course-info-video-img">
                                        <img src="{{ getImageFile($course->image) }}" alt="video"
                                            class="img-fluid">
                                    </div>

                                    @if ($course->intro_video_check == 1 && getVideoFile($course->video))
                                    <button type="button" class="play-btn position-absolute"
                                        data-bs-toggle="modal" data-bs-target="#newVideoPlayerModal">
                                        <img src="{{ asset('frontend/assets/img/icons-svg/play.svg') }}"
                                            alt="play">
                                    </button>
                                    @endif @if ($course->intro_video_check == 2 && $course->youtube_video_id)
                                    <button type="button" class="play-btn position-absolute"
                                        data-bs-toggle="modal" data-bs-target="#newVideoPlayerModal">
                                        <img src="{{ asset('frontend/assets/img/icons-svg/play.svg') }}"
                                            alt="play">
                                    </button>
                                    @endif
                                </div>
                            </div>

                            @if ($course->learner_accessibility == 'paid')
                            @if ($course->status == STATUS_APPROVED)
                            @if ($authRole == 3 || $authRole == null)
                            @if ($course->course_type == 3)
                            @else
                            <div
                                class="course-price-box d-flex justify-content-between align-items-start mt-30 mb-30">
                                <?php
                                $startDate = date('d-m-Y H:i:s', strtotime(@$course->promotionCourse->promotion->start_date));
                                $endDate = date('d-m-Y H:i:s', strtotime(@$course->promotionCourse->promotion->end_date));
                                $percentage = @$course->promotionCourse->promotion->percentage;
                                $discount_price = number_format($course->price - ($course->price * $percentage) / 100, 2);
                                ?>
                                @if (now()->gt($startDate) && now()->lt($endDate))
                                <div class="w-100">
                                    <h4 class="d-flex align-items-center mb-1">
                                        @if (get_currency_placement() == 'after')
                                        {{ $discount_price }}
                                        {{ get_currency_symbol() }}
                                        @else
                                        {{ get_currency_symbol() }}
                                        {{ $discount_price }}
                                        @endif <span
                                            class="text-decoration-line-through fw-normal font-16 color-gray ps-3">
                                            @if (get_currency_placement() == 'after')
                                            {{ $course->price }}
                                            {{ get_currency_symbol() }}
                                            @else
                                            {{ get_currency_symbol() }}
                                            {{ $course->price }}
                                            @endif
                                        </span>
                                    </h4>
                                    <span
                                        class="course-left-duration color-deep-orange font-14">
                                        <span class="iconify me-2 font-18"
                                            data-icon="clarity:alarm-clock-line"></span>
                                        {{ getLeftDuration($startDate, $endDate) }}
                                        {{ __('Left at this Price') }}!
                                    </span>
                                    <div
                                        class="price-off font-12 font-medium color-hover d-flex align-items-center justify-content-center">
                                        {{ @$percentage }}% {{ __('off') }}
                                    </div>
                                    @elseif($course->price <= $course->old_price)
                                        <div class="w-100">
                                            <h4 class="d-flex align-items-center mb-1">
                                                @if (get_currency_placement() == 'after')
                                                {{ $course->price }}
                                                {{ get_currency_symbol() }}
                                                @else
                                                {{ get_currency_symbol() }}
                                                {{ $course->price }}
                                                @endif <span
                                                    class="text-decoration-line-through fw-normal font-16 color-gray ps-3">
                                                    @if (get_currency_placement() == 'after')
                                                    {{ $course->old_price }}
                                                    {{ get_currency_symbol() }}
                                                    @else
                                                    {{ get_currency_symbol() }}
                                                    {{ $course->old_price }}
                                                    @endif
                                                </span>
                                            </h4>
                                        </div>
                                        @php $percentage = number_format((($course->old_price - $course->price) / $course->old_price) * 100, 2); @endphp
                                        <div
                                            class="price-off font-12 font-medium color-hover d-flex align-items-center justify-content-center">
                                            {{ $percentage }}% {{ __('off') }}
                                        </div>
                                        @else
                                        <div class="w-100">
                                            <h4 class="d-flex align-items-center mb-1">
                                                @if (get_currency_placement() == 'after')
                                                {{ $course->price }}
                                                {{ get_currency_symbol() }}
                                                @else
                                                {{ get_currency_symbol() }}
                                                {{ $course->price }}
                                                @endif
                                            </h4>
                                        </div>
                                        @endif
                                        @endif
                                        @endif
                                        @endif
                                        @elseif($course->course_type == 1 && $course->learner_accessibility == 'free')
                                        <div
                                            class="course-price-box d-flex justify-content-between align-items-center mt-30 mb-30">
                                            <h4 class="d-flex align-items-center mb-1"> {{ __('Free') }} </h4>
                                        </div>
                                        @endif


                                        <ul class="pb-30 w-100 mt-2">
                                            @if ($course->course_type == COURSE_TYPE_GENERAL && $course->difficultyLevel)
                                            <li class="d-flex justify-content-between features-course">
                                                <div class="d-flex align-items-center gap-2">
                                                    <svg width="16" height="16" viewBox="0 0 16 16"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g clip-path="url(#clip0_125_1335)">
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M3.65544 14.8752H1.4715C1.26529 14.8752 1.09657 14.7065 1.09657 14.5002V11.9601H0.374835C0.234237 11.9601 0.103013 11.8851 0.0374009 11.7539C-0.0188381 11.6227 -0.00946495 11.4727 0.0748936 11.3602L2.26822 8.44517C2.33383 8.35144 2.44631 8.2952 2.56816 8.2952C2.68063 8.2952 2.79311 8.35144 2.8681 8.44517L5.02393 11.3321C5.08954 11.3977 5.12703 11.4915 5.12703 11.5852C5.12703 11.7914 4.95831 11.9601 4.75211 11.9601H4.03037V14.5002C4.03037 14.7065 3.86165 14.8752 3.65544 14.8752ZM9.09188 14.8752H6.89856C6.69235 14.8752 6.53301 14.7065 6.53301 14.5002V10.8635H5.81127C5.67068 10.8635 5.53945 10.7791 5.47384 10.6572C5.40823 10.526 5.42697 10.376 5.51133 10.2636L7.69528 7.34851C7.77027 7.25478 7.88274 7.19854 7.99522 7.19854C8.11707 7.19854 8.22955 7.25478 8.29516 7.34851L10.4604 10.2354C10.526 10.3011 10.5635 10.3854 10.5635 10.4885C10.5635 10.6947 10.3948 10.8635 10.1885 10.8635H9.46681V14.5002C9.46681 14.7065 9.29809 14.8752 9.09188 14.8752ZM14.5283 14.8752H12.335C12.1288 14.8752 11.9601 14.7065 11.9601 14.5002V9.76679H11.2383C11.0977 9.76679 10.9665 9.68243 10.9103 9.56058C10.8447 9.42936 10.854 9.27939 10.9384 9.16691L13.1317 6.25185C13.1973 6.15812 13.3098 6.10188 13.4317 6.10188C13.5441 6.10188 13.6566 6.15812 13.7316 6.25185L15.8968 9.13879C15.953 9.2044 15.9999 9.28876 15.9999 9.39186C15.9999 9.59807 15.8312 9.76679 15.625 9.76679H14.9032V14.5002C14.9032 14.7065 14.7345 14.8752 14.5283 14.8752ZM1.42463 8.02338C1.34027 8.02338 1.26529 7.99526 1.19967 7.9484C1.0872 7.86404 1.02158 7.71407 1.0497 7.57347L1.25591 6.49556L0.459194 5.7457C0.356089 5.6426 0.309223 5.49263 0.356089 5.35203C0.402954 5.2208 0.524806 5.1177 0.665403 5.09895L1.76206 4.95836L2.22135 3.9648C2.28696 3.83358 2.41819 3.74922 2.56816 3.74922C2.70875 3.74922 2.83998 3.83358 2.90559 3.9648L3.37425 4.95836L4.46154 5.09895C4.60213 5.1177 4.72399 5.2208 4.77085 5.35203C4.81772 5.49263 4.77085 5.6426 4.66775 5.7457L3.87103 6.49556L4.07724 7.57347C4.10536 7.71407 4.03974 7.86404 3.92727 7.9484C3.80541 8.03276 3.65544 8.04213 3.52422 7.97652L2.56816 7.44225L1.60272 7.97652C1.54648 8.00464 1.48087 8.02338 1.42463 8.02338ZM6.8517 6.7955C6.77671 6.7955 6.70172 6.76738 6.63611 6.72051C6.51426 6.63615 6.45802 6.48618 6.48614 6.34558L6.69235 5.26767L5.89563 4.51782C5.78315 4.41471 5.74566 4.26474 5.79253 4.12414C5.83939 3.99292 5.96124 3.88981 6.10184 3.87107L7.18913 3.73984L7.65779 2.73691C7.7234 2.60569 7.85462 2.52133 7.99522 2.52133C8.14519 2.52133 8.27642 2.60569 8.33266 2.73691L8.80131 3.73984L9.8886 3.87107C10.0386 3.88981 10.1604 3.99292 10.2073 4.12414C10.2448 4.26474 10.2073 4.41471 10.1042 4.51782L9.29809 5.26767L9.5043 6.34558C9.53242 6.48618 9.47618 6.63615 9.3637 6.72051C9.24185 6.80487 9.08251 6.81424 8.96066 6.74863L7.99522 6.21436L7.03916 6.74863C6.98292 6.77675 6.91731 6.7955 6.8517 6.7955ZM14.5752 5.38952C14.5096 5.38952 14.4533 5.37078 14.3971 5.34266L13.4317 4.80839L12.4662 5.34266C12.3444 5.40827 12.185 5.39889 12.0632 5.31454C11.9507 5.23018 11.8945 5.08021 11.9226 4.93961L12.1288 3.8617L11.3227 3.11184C11.2196 3.00874 11.1821 2.85877 11.229 2.71817C11.2665 2.58694 11.3883 2.48384 11.5383 2.46509L12.6256 2.3245L13.0942 1.33094C13.1505 1.19971 13.2817 1.11536 13.4317 1.11536C13.5723 1.11536 13.7035 1.19971 13.7691 1.33094L14.2378 2.3245L15.325 2.46509C15.475 2.48384 15.5875 2.58694 15.6344 2.71817C15.6812 2.85877 15.6437 3.00874 15.5406 3.11184L14.7345 3.8617L14.9407 4.93961C14.9689 5.08021 14.9126 5.23018 14.7908 5.31454C14.7252 5.3614 14.6502 5.38952 14.5752 5.38952Z"
                                                                fill="#818B9C" />
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_125_1335">
                                                                <rect width="16" height="16" fill="white" />
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                    <span>{{ __('Level') }}</span>
                                                </div>
                                                <div class="text-align-right" style="width: 60%">{{ @$course->difficultyLevel->name }}</div>
                                            </li>
                                            @endif
                                            @if ($course->classes > 0)
                                            <li class="d-flex justify-content-between features-course">
                                                <div class="d-flex align-items-center gap-2">
                                                    <svg width="16" height="16" viewBox="0 0 16 16"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M15.0625 0.78125H0.9375C0.420625 0.78125 0 1.20187 0 1.71875V11.0312C0 11.5481 0.420625 11.9688 0.9375 11.9688H15.0625C15.5794 11.9688 16 11.5481 16 11.0312V1.71875C16 1.20187 15.5794 0.78125 15.0625 0.78125ZM13 14.2812H10.4688V12.9062H5.53125V14.2812H3C2.74125 14.2812 2.53125 14.4913 2.53125 14.75C2.53125 15.0087 2.74125 15.2188 3 15.2188H13C13.2587 15.2188 13.4688 15.0087 13.4688 14.75C13.4688 14.4913 13.2587 14.2812 13 14.2812Z"
                                                            fill="#818B9C" />
                                                    </svg>
                                                    <span>{{ __('Classes') }}</span>
                                                </div>
                                                <div class="text-align-right" style="width: 60%">
                                                    {{-- {{ @$course->lectures->count() > 0 ? @$course->video_duration : '0' }} --}}
                                                    {{ @$course->classes > 0 ? @$course->classes : '0' }}
                                                </div>
                                            </li>
                                            @endif

                                            @if ($course->duration)
                                            <li class="d-flex justify-content-between features-course">
                                                <div class="d-flex align-items-center gap-2">
                                                    <svg width="16" height="16" viewBox="0 0 16 16"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M8.00033 0.666748C6.54993 0.666748 5.13211 1.09684 3.92615 1.90264C2.72019 2.70843 1.78025 3.85374 1.22521 5.19374C0.67017 6.53373 0.524945 8.00822 0.807903 9.43074C1.09086 10.8533 1.78929 12.1599 2.81488 13.1855C3.84046 14.2111 5.14714 14.9095 6.56967 15.1925C7.99219 15.4755 9.46668 15.3302 10.8067 14.7752C12.1467 14.2202 13.292 13.2802 14.0978 12.0743C14.9036 10.8683 15.3337 9.45048 15.3337 8.00008C15.3314 6.05586 14.558 4.19194 13.1832 2.81717C11.8085 1.4424 9.94455 0.669042 8.00033 0.666748ZM10.4717 10.4714C10.3466 10.5964 10.1771 10.6666 10.0003 10.6666C9.82355 10.6666 9.65401 10.5964 9.529 10.4714L7.529 8.47141C7.40396 8.34642 7.3337 8.17688 7.33366 8.00008V4.00008C7.33366 3.82327 7.4039 3.6537 7.52892 3.52868C7.65395 3.40365 7.82352 3.33341 8.00033 3.33341C8.17714 3.33341 8.34671 3.40365 8.47173 3.52868C8.59676 3.6537 8.667 3.82327 8.667 4.00008V7.72408L10.4717 9.52875C10.5966 9.65377 10.6669 9.82331 10.6669 10.0001C10.6669 10.1769 10.5966 10.3464 10.4717 10.4714Z"
                                                            fill="#818B9C" />
                                                    </svg>

                                                    <span>{{ __('Duration') }}</span>
                                                </div>

                                                @php
                                                if(@$course->duration_type == 1){
                                                $durationType = 'hrs';
                                                }
                                                else if(@$course->duration_type == 2){
                                                $durationType = 'd';
                                                }
                                                else if(@$course->duration_type == 3){
                                                $durationType = 'mo';
                                                }
                                                else{
                                                $durationType = 'hrs';
                                                }
                                                @endphp
                                                @if ($course->course_type == COURSE_TYPE_GENERAL)
                                                <div class="text-align-right" style="width: 60%">
                                                    {{-- {{ @$course->VideoDuration }} --}}
                                                    {{ @$course->duration.''.$durationType }}
                                                </div>
                                                @else
                                                <div class="text-align-right" style="width: 60%">
                                                    {{-- {{ @$course->scorm_course->duration }} --}}
                                                    {{ @$course->duration.''.$durationType  }}
                                                </div>
                                                @endif
                                            </li>
                                            @endif


                                            <li class="d-flex justify-content-between features-course">
                                                <div class="d-flex align-items-center gap-2">
                                                    <svg width="16" height="12" viewBox="0 0 16 12"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M14.5276 0.00830078H5.48906C4.67719 0.00830078 4.01666 0.668832 4.01666 1.48171C4.01666 2.29358 4.67719 2.95411 5.48906 2.95411H14.5276C15.3394 2.95411 16 2.29358 16 1.48171C16 0.668801 15.3395 0.00830078 14.5276 0.00830078ZM1.47294 0.00830078C0.660781 0.00830078 0 0.669051 0 1.48121C0 2.29336 0.660781 2.95411 1.47294 2.95411C2.28509 2.95411 2.94584 2.29336 2.94584 1.48121C2.94584 0.669051 2.28509 0.00830078 1.47294 0.00830078ZM1.47294 4.52705C0.660781 4.52705 0 5.1878 0 5.99996C0 6.81211 0.660781 7.47286 1.47294 7.47286C2.28509 7.47286 2.94584 6.81211 2.94584 5.99996C2.94584 5.1878 2.28509 4.52705 1.47294 4.52705ZM1.47294 9.0458C0.660781 9.0458 0 9.70655 0 10.5187C0 11.3309 0.660781 11.9916 1.47294 11.9916C2.28509 11.9916 2.94584 11.3309 2.94584 10.5187C2.94584 9.70655 2.28509 9.0458 1.47294 9.0458ZM14.5276 4.52705H5.48906C4.67719 4.52705 4.01666 5.18758 4.01666 6.00046C4.01666 6.81233 4.67719 7.47286 5.48906 7.47286H14.5276C15.3394 7.47286 16 6.81233 16 6.00046C16 5.18755 15.3395 4.52705 14.5276 4.52705ZM14.5276 9.0458H5.48906C4.67719 9.0458 4.01666 9.70633 4.01666 10.5192C4.01666 11.3311 4.67719 11.9916 5.48906 11.9916H14.5276C15.3394 11.9916 16 11.3311 16 10.5192C16 9.70633 15.3395 9.0458 14.5276 9.0458Z"
                                                            fill="#818B9C" />
                                                    </svg>

                                                    <span>{{ __('Category') }}</span>
                                                </div>

                                                <div class="text-align-right" style="width: 60%">{{ @$course->category->name }}</div>
                                            </li>

                                            <li class="d-flex justify-content-between features-course">
                                                <div class="d-flex align-items-center gap-2">
                                                    <svg width="16" height="16" viewBox="0 0 16 16"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M4.33432 4.28125H4.1337L3.75879 6.15625H4.70923L4.33432 4.28125ZM11.9176 8.03125C12.12 8.57497 12.3892 9.01509 12.6871 9.38959C12.9851 9.01509 13.2855 8.57494 13.4879 8.03125H11.9176Z"
                                                            fill="#818B9C" />
                                                        <path
                                                            d="M14.5935 2.84375H8.7722L9.98601 12.5837C10.0075 12.9821 9.89857 13.3572 9.63857 13.6508L7.58301 16H14.5935C15.369 16 15.9998 15.3692 15.9998 14.5938V4.28125C15.9998 3.50581 15.369 2.84375 14.5935 2.84375ZM14.5935 8.03125H14.4684C14.2016 8.88697 13.7787 9.55656 13.3391 10.0844C13.6834 10.3993 14.0517 10.6575 14.4178 10.9467C14.6196 11.1083 14.6526 11.4031 14.4905 11.6055C14.3292 11.8075 14.0333 11.8403 13.8318 11.6783C13.434 11.3643 13.0607 11.1015 12.6873 10.7588C12.3139 11.1015 11.9718 11.3643 11.574 11.6783C11.3725 11.8403 11.0766 11.8075 10.9153 11.6055C10.7532 11.4031 10.7862 11.1083 10.9881 10.9467C11.3542 10.6575 11.6911 10.3993 12.0355 10.0844C11.5959 9.55659 11.2042 8.887 10.9375 8.03125H10.8123C10.5532 8.03125 10.3435 7.82159 10.3435 7.5625C10.3435 7.30341 10.5532 7.09375 10.8123 7.09375H12.2185V6.625C12.2185 6.36591 12.4282 6.15625 12.6873 6.15625C12.9464 6.15625 13.156 6.36591 13.156 6.625V7.09375H14.5935C14.8526 7.09375 15.0623 7.30341 15.0623 7.5625C15.0623 7.82159 14.8526 8.03125 14.5935 8.03125Z"
                                                            fill="#818B9C" />
                                                        <path
                                                            d="M7.63013 1.23184C7.54269 0.529625 6.94256 0 6.23488 0H1.40625C0.630813 0 0 0.630813 0 1.40625V11.7812C0 12.5567 0.630813 13.1875 1.40625 13.1875H8.79847C8.93522 13.0312 9.04984 12.9333 9.05469 12.7293C9.05587 12.6781 7.63647 1.28259 7.63013 1.23184ZM5.74825 8.95959C5.49969 9.01075 5.24791 8.84937 5.19666 8.592L4.89706 7.09375H3.57172L3.27213 8.592C3.22178 8.84559 2.97731 9.01222 2.72053 8.95959C2.46694 8.90878 2.30212 8.66206 2.35294 8.408L3.29041 3.7205C3.33434 3.50169 3.52662 3.34375 3.75 3.34375H4.71875C4.94212 3.34375 5.13441 3.50169 5.17834 3.7205L6.11584 8.408C6.16666 8.66206 6.00187 8.90878 5.74825 8.95959ZM5.47691 14.125L5.55734 14.7682C5.61097 15.1989 5.89716 15.6381 6.36791 15.8574L7.94163 14.125H5.47691Z"
                                                            fill="#818B9C" />
                                                    </svg>

                                                    <span>{{ __('Language') }}</span>
                                                </div>

                                                <div class="text-align-right" style="width: 60%">{{ @$course->language->name }}</div>
                                            </li>

                                            <li class="d-flex justify-content-between features-course">
                                                <div class="d-flex align-items-center gap-2">
                                                    <svg width="9" height="13" viewBox="0 0 9 13" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M2.36657 12.2899C2.36657 12.6814 2.6851 12.9999 3.07657 12.9999H6.21883C6.61034 12.9999 6.92883 12.6814 6.92883 12.2899V11.4049H2.36657V12.2899ZM8.41804 8.81974C8.60697 8.57804 8.7098 8.28019 8.71023 7.97341V6.86386C8.71023 6.25798 8.30399 5.71639 7.72233 5.54685L5.0865 4.77863V1.69834C5.0865 1.07448 4.57895 0.566895 3.95506 0.566895C3.33117 0.566895 2.82361 1.07448 2.82361 1.69834V6.64242L2.01188 5.66847C1.66676 5.25441 1.05275 5.1753 0.613949 5.48833C0.220002 5.76941 0.0733903 6.27838 0.257445 6.72594C0.25951 6.73101 0.261762 6.73598 0.264202 6.74089L2.10988 10.4665H7.29435L8.41804 8.81974Z"
                                                            fill="#818B9C" />
                                                    </svg>

                                                    <span>{{ __('Access') }}</span>
                                                </div>

                                                <div class="text-align-right" style="width: 60%">
                                                    {{ !$course->access_period ? __('Full Lifetime Access') : $course->access_period . ' ' . __('days after the enrollment') }}
                                                </div>
                                            </li>

                                            <li class="d-flex justify-content-between features-course">
                                                <div class="d-flex align-items-center gap-2">
                                                    <svg width="14" height="15" viewBox="0 0 14 15"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M6.60952 5.015L3.41452 0.225C3.36923 0.15622 3.30764 0.0996921 3.23524 0.0604398C3.16285 0.0211876 3.08187 0.000426059 2.99952 0H0.499519C0.410921 0.000152678 0.323954 0.0238444 0.247521 0.0686502C0.171087 0.113456 0.107932 0.177767 0.0645191 0.255C0.0205933 0.332287 -0.00166315 0.419995 9.68493e-05 0.508875C0.00185685 0.597755 0.0275681 0.684513 0.0745191 0.76L3.53952 6.395C4.37053 5.59094 5.45653 5.10277 6.60952 5.015ZM6.99952 6C6.0756 5.99653 5.17876 6.31181 4.46027 6.89267C3.74177 7.47352 3.24559 8.28441 3.05542 9.18854C2.86524 10.0927 2.99271 11.0347 3.41632 11.8558C3.83993 12.6769 4.53376 13.3268 5.38079 13.6958C6.22781 14.0648 7.17619 14.1304 8.06596 13.8816C8.95573 13.6327 9.73244 13.0846 10.2651 12.3296C10.7977 11.5747 11.0537 10.6592 10.9898 9.73746C10.926 8.81574 10.5462 7.94426 9.91452 7.27C9.54194 6.86943 9.09081 6.54993 8.58928 6.33142C8.08775 6.11292 7.54658 6.0001 6.99952 6ZM13.9345 0.255C13.8911 0.177767 13.828 0.113456 13.7515 0.0686502C13.6751 0.0238444 13.5881 0.000152678 13.4995 0H10.9995C10.9172 0.000426059 10.8362 0.0211876 10.7638 0.0604398C10.6914 0.0996921 10.6298 0.15622 10.5845 0.225L7.38952 5.015C8.54251 5.10277 9.62851 5.59094 10.4595 6.395L13.9245 0.76C13.9715 0.684513 13.9972 0.597755 13.9989 0.508875C14.0007 0.419995 13.9784 0.332287 13.9345 0.255Z"
                                                            fill="#818B9C" />
                                                    </svg>

                                                    <span>{{ __('Certficate') }}</span>
                                                </div>

                                                <div class="text-align-right" style="width: 60%">{{ __('Yes') }}</div>
                                            </li>

                                            <li class="d-flex justify-content-between features-course">
                                                <div class="d-flex align-items-center gap-2">
                                                    <svg width="14" height="16" viewBox="0 0 14 16"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M2.3125 16H11.6875C12.4629 16 13.0938 15.3692 13.0938 14.5938V4.6875H9.8125C9.03706 4.6875 8.40625 4.05669 8.40625 3.28125V0H2.3125C1.53706 0 0.90625 0.630813 0.90625 1.40625V14.5938C0.90625 15.3692 1.53706 16 2.3125 16ZM4.1875 6.59375H9.8125C10.0716 6.59375 10.2812 6.80341 10.2812 7.0625C10.2812 7.32159 10.0716 7.53125 9.8125 7.53125H4.1875C3.92841 7.53125 3.71875 7.32159 3.71875 7.0625C3.71875 6.80341 3.92841 6.59375 4.1875 6.59375ZM4.1875 8.46875H9.8125C10.0716 8.46875 10.2812 8.67841 10.2812 8.9375C10.2812 9.19659 10.0716 9.40625 9.8125 9.40625H4.1875C3.92841 9.40625 3.71875 9.19659 3.71875 8.9375C3.71875 8.67841 3.92841 8.46875 4.1875 8.46875ZM4.1875 10.3438H9.8125C10.0716 10.3438 10.2812 10.5534 10.2812 10.8125C10.2812 11.0716 10.0716 11.2812 9.8125 11.2812H4.1875C3.92841 11.2812 3.71875 11.0716 3.71875 10.8125C3.71875 10.5534 3.92841 10.3438 4.1875 10.3438ZM4.1875 12.2188H7.9375C8.19659 12.2188 8.40625 12.4284 8.40625 12.6875C8.40625 12.9466 8.19659 13.1562 7.9375 13.1562H4.1875C3.92841 13.1562 3.71875 12.9466 3.71875 12.6875C3.71875 12.4284 3.92841 12.2188 4.1875 12.2188Z"
                                                            fill="#818B9C" />
                                                        <path
                                                            d="M9.8125 3.75H12.8191L9.34375 0.274658V3.28125C9.34375 3.53988 9.55387 3.75 9.8125 3.75Z"
                                                            fill="#818B9C" />
                                                    </svg>

                                                    <span>{{ __('Resource') }}</span>
                                                </div>

                                                <div class="text-align-right" style="width: 60%">{{ @$course->resources->count() }}
                                                    {{ __('Downloadable Resources') }}
                                                </div>
                                            </li>

                                        </ul>


                                    
                                        @if ($course_exits == 'enrolled')
                                        {{-- <a href="/student/dashboard?course_url={{ urlencode(route('student.my-course.show', $course->slug)) }}"
                                            class="theme-btn theme-button1 theme-button3 w-100 mb-30 ">
                                            {{ __('Go to Course') }}
                                        </a> --}}
                                            <a href="https://portal.cefonlineacademy.com/mycourses/{{$course->slug}}"
                                                class="theme-btn theme-button1 theme-button3 w-100 mb-30 ">
                                                {{ __('Go to Course') }}
                                            </a>
                                        @elseif($course_exits == 'cartList')
                                            @if ($course->course_type == 3)
                                                @if (check_request_enrollment($authUserStudent,$course->id))
                                                        <a data-placement="left" href="javascript:void(0);"
                                                            class="theme-btn theme-button1 theme-button3 w-100 !mb-2">
                                                            <span class="msgInfoChange">{{ __('Already Requested') }}
                                                            </span>
                                                        </a>
                                                @else
                                                    <a class="theme-btn theme-button1 theme-button3 w-100 !mb-2 enroll-btn" data-placement="left" href="{{route('live-course-enrollment-form',['type'=>1, 'slug'=> $course->slug, 'user_id'=>$authUserStudent->user_id])}}">
                                                        <span class="msgInfoChange">{{ __('Request Enrollment') }}
                                                        </span>
                                                      
                                                    </a>
                                                @endif
                                            @else
                                                <button class="theme-btn theme-button1 theme-button3 w-100 mb-30">
                                                    {{ __('Added to Cart') }}
                                                </button>
                                            @endif
                                            
                                           
                                        @else
                                        @if ($course->status == STATUS_APPROVED)
                                        @if ($authRole == 3 || $authRole == null)
                                        <div class="w-full mobile-fixed-btns-course">
                                            @if (($course->course_type == 1 || $course->course_type == 2) && $course->learner_accessibility == 'free')
                                                <!--Free Self Learning -->
                                                @if ($authRole == null)
                                                {{-- <a class="theme-btn theme-button1 theme-button3 w-100 mb-30 enroll-btn" data-placement="left" href="{{ route('sign-up-as-student') }}/2/{{ $course->slug }}" aria-current="page">
                                                    <span class="msgInfoChange">{{ __('Enroll Now') }}
                                                    </span>
                                                </a> --}}
                                                <a class="theme-btn theme-button1 theme-button3 w-100 mb-30 enroll-btn" data-placement="left" href="{{ route('student-login') }}/2/{{ $course->slug }}" aria-current="page">
                                                    <span class="msgInfoChange">{{ __('Enroll Now') }}
                                                    </span>
                                                </a>
                                                @else
                                                <a data-placement="left" href="javascript:void(0);"
                                                    class="theme-btn theme-button1 theme-button3 w-100 mb-30 addToCart enroll-btn"
                                                    data-course_id="{{ $course->id }}"
                                                    data-route="{{ route('student.addToCart') }}"
                                                    data-coursetype="2"
                                                    data-login_route="{{ route('login') }}/2/{{ $course->slug }}">
                                                    <span class="msgInfoChange">{{ __('Enroll Now') }}
                                                    </span>
                                                </a>
                                                @endif
                                            @elseif (($course->course_type == 1 || $course->course_type == 2) && $course->learner_accessibility == 'paid')
                                                <!--Paid Self Learning -->
                                                @if ($authRole == null)
                                                <a class="theme-btn theme-button1 theme-button3 w-100 mb-30 enroll-btn" data-placement="left" href="{{ route('sign-up-as-student') }}/2/{{ $course->slug }}" aria-current="page">
                                                    <span class="msgInfoChange">{{ __('Buy Now') }}
                                                    </span>
                                                </a>
                                                @else
                                                <a data-placement="left" href="javascript:void(0);"
                                                    class="theme-btn theme-button1 theme-button3 w-100 mb-30 addToCartWithCheckout enroll-btn"
                                                    data-course_id="{{ $course->id }}"
                                                    data-route="{{ route('student.addToCartWithCheckout') }}"
                                                    data-coursetype="2"
                                                    data-location="1"
                                                    data-login_route="{{ route('login') }}/2/{{ $course->slug }}">
                                                    <span class="msgInfoChange">{{ __('Buy Now') }}
                                                    </span>
                                                </a>
                                                @endif
                                            @else
                                                @if ($authRole == null)
                                                <a data-placement="left"
                                                    class="theme-btn theme-button1 theme-button3 w-100 !mb-2 enroll-btn" href="{{ route('sign-up-as-student') }}/1/{{ $course->slug }}" aria-current="page">
                                                    <span class="msgInfoChange">{{ __('Request Enrollment') }}
                                                    </span>
                                                </a>
                                                @else
                                                    @if (check_request_enrollment($authUserStudent,$course->id))
                                                        <a data-placement="left" href="javascript:void(0);"
                                                            class="theme-btn theme-button1 theme-button3 w-100 !mb-2">
                                                            <span class="msgInfoChange">{{ __('Already Requested') }}
                                                            </span>
                                                        </a>
                                                    @else
                                                        <a class="theme-btn theme-button1 theme-button3 w-100 !mb-2 enroll-btn" data-placement="left" href="{{route('live-course-enrollment-form',['type'=>1, 'slug'=> $course->slug, 'user_id'=>$authUserStudent->user_id])}}">
                                                            <span class="msgInfoChange">{{ __('Request Enrollment') }}
                                                            </span>
                                                        </a>
                                                    @endif
                                                
                                                @endif
                                            @endif

                                            @if ($course->learner_accessibility == 'paid')
                                            @if ($course->status == STATUS_APPROVED)
                                            {{-- @if ($authRole == 3 || $authRole == null) --}}
                                            @if ($authRole == null)
                                            @if ($course->course_type == 3)
                                            <br>
                                            <a data-placement="left"
                                                href="{{ route('sign-up-as-student-live') }}/1/{{ $course->slug }}"
                                                style="width: 100% !important;"
                                                class="theme-btn theme-button1 theme-button3 w-100 mb-30 demo-btn"
                                                data-course_id="{{ $course->id }}"
                                                data-route="{{ route('student.enrollCourse') }}"
                                                data-coursetype="1"
                                                data-login_route="{{ route('login') }}/1/{{ $course->slug }}">
                                                <span class="msgInfoChange">{{ __('Book a Demo Class') }}</span>
                                            </a>
                                            @endif
                                            @endif
                                            @endif

                                        </div>
                                        @endif




                                        <div class="modal fade" id="loginModal" tabindex="-1"
                                            aria-labelledby="loginModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div style=" max-height: 90vh; overflow-y: auto;"
                                                    class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="exampleModalLabel">
                                                            {{ __('Sign In') }}
                                                        </h1>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="col-md-12">
                                                            <form id="loginForm2" method="POST"
                                                                action="{{ route('login-modal') }}">
                                                                @csrf <h5 class="mb-1 text-center">
                                                                    {{ __('Sign In') }}
                                                                </h5>
                                                                <p class="font-14 mb-30 text-center">
                                                                    {{ __('New User') }} ? <a
                                                                        data-placement="left"
                                                                        href="javascript:void(0);"
                                                                        class="fw-500 w-100 mb-30 signUp"
                                                                        data-course_id="{{ $course->id }}"
                                                                        data-route="{{ route('sign-up-as-student-modal') }}/2/{{ $course->slug }}"
                                                                        data-coursetype="2"
                                                                        data-courseslug="{{ $course->slug }}"
                                                                        data-login_route="{{ route('sign-up-as-student-modal') }}/2/{{ $course->slug }}">
                                                                        <span
                                                                            class="msgInfoChange">{{ __('Sign Up') }}
                                                                            <i style="width: 1rem; height: 1rem;"
                                                                                data-feather="arrow-right"></i></span>
                                                                    </a>

                                                                </p>
                                                                <input type="hidden" id="coursetype"
                                                                    name="coursetype">
                                                                <div class="row mb-30">
                                                                    <div class="col-md-12">
                                                                        <label
                                                                            class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Email or Phone') }}</label>
                                                                        <input required type="text"
                                                                            name="email"
                                                                            value="{{ old('email') }}"
                                                                            class="form-control email"
                                                                            placeholder="{{ __('Type your email or phone number') }}">
                                                                        @if ($errors->has('email'))
                                                                        <span class="text-danger"><i
                                                                                class="fas fa-exclamation-triangle"></i>
                                                                            {{ $errors->first('email') }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-30">
                                                                    <div class="col-md-12">
                                                                        <label
                                                                            class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Password') }}</label>
                                                                        <div
                                                                            class="form-group mb-0 position-relative">
                                                                            <input required
                                                                                class="form-control password"
                                                                                name="password"
                                                                                value="{{ old('password') }}"
                                                                                placeholder="*********"
                                                                                type="password">
                                                                            <span
                                                                                class="toggle cursor fas fa-eye pass-icon"
                                                                                style="position: absolute;top: 1.1rem;right: 0.8rem;"></span>
                                                                        </div>
                                                                        @if ($errors->has('password'))
                                                                        <span class="text-danger"><i
                                                                                class="fas fa-exclamation-triangle"></i>
                                                                            {{ $errors->first('password') }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div id="div-response">
                                                                </div>
                                                                <div class="row mb-2">
                                                                    <div class="col-md-12">
                                                                        <button id="login_now" type="submit"
                                                                            class="theme-btn theme-button1 theme-button3 font-15 fw-bold w-100">{{ __('Sign In') }}</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Modal -->












                                        <div class="modal fade" id="signUpModal" tabindex="-1"
                                            aria-labelledby="signUpModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div style=" max-height: 90vh; overflow-y: auto;"
                                                    class="modal-content">
                                                    <div class="modal-body p-0">

                                                        <section class="register-page p-0 bg-gray">
                                                            <div
                                                                class="container-fluid p-0 min-h-full bg-white w-90 mx-auto shadow-md flex flex-col justify-content-center align-items-center px-4 py-3">
                                                                <h4 class="text-center">Let's get your Journey
                                                                    Started</h4>
                                                                <div class="main">
                                                                    <ul class="progressBar my-2">
                                                                        <li>
                                                                            <div class="step st first active">
                                                                                <p>1</p>
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    width="16"
                                                                                    height="16"
                                                                                    fill="currentColor"
                                                                                    class="bi bi-check-lg"
                                                                                    viewBox="0 0 16 16">
                                                                                    <path
                                                                                        d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z" />
                                                                                </svg>
                                                                            </div>
                                                                        </li>
                                                                        <li>
                                                                            <div class="step st second">
                                                                                <p>2</p>
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    width="16"
                                                                                    height="16"
                                                                                    fill="currentColor"
                                                                                    class="bi bi-check-lg"
                                                                                    viewBox="0 0 16 16">
                                                                                    <path
                                                                                        d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z" />
                                                                                </svg>
                                                                            </div>
                                                                        </li>
                                                                        <li>
                                                                            <div class="step st third">
                                                                                <p>3</p>
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    width="16"
                                                                                    height="16"
                                                                                    fill="currentColor"
                                                                                    class="ms-1 mt-1 bi bi-check-lg"
                                                                                    viewBox="0 0 16 16">
                                                                                    <path
                                                                                        d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z" />
                                                                                </svg>
                                                                            </div>
                                                                        </li>
                                                                        <li>
                                                                            <div class="step st fourth">
                                                                                <p>4</p>
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    width="16"
                                                                                    height="16"
                                                                                    fill="currentColor"
                                                                                    class="ms-1 mt-1 bi bi-check-lg"
                                                                                    viewBox="0 0 16 16">
                                                                                    <path
                                                                                        d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z" />
                                                                                </svg>
                                                                            </div>
                                                                        </li>
                                                                    </ul>
                                                                    <form id="multiStepForm" class="w-100 px-4">
                                                                        <input type="hidden" name="token"
                                                                            id="token"
                                                                            value="{{ csrf_token() }}">
                                                                        <input type="hidden"
                                                                            name="new_student_id"
                                                                            id="new_student_id" value="">
                                                                        <input type="hidden"
                                                                            name="new_trial_id"
                                                                            id="new_trial_id" value="">
                                                                        <input type="hidden"
                                                                            name="check_status"
                                                                            id="check_status"
                                                                            value="step_1_create">
                                                                        <input type="hidden" name="type_st"
                                                                            id="type_st"
                                                                            value="{{ $typeCourse }}">
                                                                        <input type="hidden" name="slug"
                                                                            id="slug" value="">
                                                                        <div class="step-content mt-3 active"
                                                                            id="step-1">
                                                                            <h6 class="text-center mb-4">Your
                                                                                personal details</h6>
                                                                            <div class="input-group mb-3">
                                                                                <input name="fname"
                                                                                    id="fname"
                                                                                    type="text"
                                                                                    class="form-control form-svg"
                                                                                    placeholder="First Name"
                                                                                    aria-label="First Name"
                                                                                    aria-describedby="FirstName"
                                                                                    required>
                                                                                <span class="input-group-text"
                                                                                    id="FirstName">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                                        width="24"
                                                                                        height="24"
                                                                                        fill="#a3a6ad"
                                                                                        class="bi bi-person"
                                                                                        viewBox="0 0 16 16">
                                                                                        <path
                                                                                            d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z" />
                                                                                    </svg>
                                                                                </span>
                                                                                <span class="error-message"
                                                                                    id="fname_error"></span>
                                                                            </div>
                                                                            <div class="input-group mb-3">
                                                                                <input name="lname"
                                                                                    id="lname"
                                                                                    type="text"
                                                                                    class="form-control form-svg"
                                                                                    placeholder="Last Name"
                                                                                    aria-label="Last Name"
                                                                                    aria-describedby="LastName"
                                                                                    required>
                                                                                <span class="input-group-text"
                                                                                    id="LastName">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                                        width="24"
                                                                                        height="24"
                                                                                        fill="#a3a6ad"
                                                                                        class="bi bi-person"
                                                                                        viewBox="0 0 16 16">
                                                                                        <path
                                                                                            d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z" />
                                                                                    </svg>
                                                                                </span>
                                                                                <span class="error-message"
                                                                                    id="lname_error"></span>
                                                                            </div>
                                                                            <div class="input-group mb-3">
                                                                                <input type="email"
                                                                                    name="email"
                                                                                    id="email"
                                                                                    class="form-control form-svg"
                                                                                    placeholder="Email"
                                                                                    aria-label="Email"
                                                                                    aria-describedby="Email">
                                                                                <span class="input-group-text"
                                                                                    id="Email">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                                        width="22"
                                                                                        height="22"
                                                                                        fill="#a3a6ad"
                                                                                        class="bi bi-envelope"
                                                                                        viewBox="0 0 16 16">
                                                                                        <path
                                                                                            d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z" />
                                                                                    </svg>
                                                                                </span>
                                                                                <span class="error-message"
                                                                                    id="email_error"></span>
                                                                            </div>
                                                                            <div class="input-group mb-3">
                                                                                <input type="password"
                                                                                    name="password"
                                                                                    id="password"
                                                                                    class="form-control password"
                                                                                    placeholder="*********">
                                                                                <span
                                                                                    class="toggle cursor fas fa-eye pass-icon"
                                                                                    style="position: absolute;top: 1.1rem;right: 0.8rem;"></span>
                                                                                <span class="error-message"
                                                                                    id="password_error"></span>
                                                                            </div>
                                                                            <div class="input-group mb-3">
                                                                                <select
                                                                                    class="form-control time-zone"
                                                                                    name="time_zone"
                                                                                    id="time_zone" required>
                                                                                    <option value="">
                                                                                        {{ __('Select Time Zone') }}
                                                                                    </option>
                                                                                    @if (!empty($timeZonesArray))
                                                                                    @foreach ($timeZonesArray as $key => $timezone)
                                                                                    <option
                                                                                        value="{{$key}}"
                                                                                        @if (@$student->user->time_zone == $key) selected @endif>
                                                                                        {{ $timezone }}
                                                                                    </option>
                                                                                    @endforeach
                                                                                    @endif

                                                                                </select>
                                                                                <span class="error-message"
                                                                                    id="time_zone_error"></span>
                                                                            </div>
                                                                            <div class="input-group mb-3">
                                                                                <input id="phone"
                                                                                    class="form-control"
                                                                                    type="tel"
                                                                                    name="phone" />
                                                                                <span class="error-message"
                                                                                    id="phone_error"></span>
                                                                            </div>
                                                                            <input type="hidden"
                                                                                name="area_code" id="area_code"
                                                                                value="">
                                                                            <div class="input-group mb-3">
                                                                                <select id="country-id"
                                                                                    name="country_id"
                                                                                    class="form-control"
                                                                                    placeholder="Select Country">
                                                                                    <option value="">Select
                                                                                        Country</option>
                                                                                    @if (!$countries->isEmpty())
                                                                                    @foreach ($countries as $country)
                                                                                    <option
                                                                                        value="{{ $country->id }}">
                                                                                        {{ $country->country_name }}
                                                                                    </option>
                                                                                    @endforeach
                                                                                    @endif
                                                                                </select>
                                                                                <span class="error-message"
                                                                                    id="country_id_error"></span>
                                                                            </div>
                                                                            <div class="input-group mb-3">
                                                                                <input id="referral_code"
                                                                                    name="referral_code"
                                                                                    type="text"
                                                                                    class="form-control"
                                                                                    placeholder="Referral/Promo Code"
                                                                                    aria-label="Referral/Promo Code"
                                                                                    aria-describedby="Code">
                                                                                <span class="error-message"
                                                                                    id="referral_code_error"></span>
                                                                            </div>
                                                                            <div
                                                                                style="display: flex;width: 100%;justify-content: space-around;align-items: baseline;">
                                                                                <div>
                                                                                    <label style="font-size:15px"
                                                                                        for="1">Live</label>
                                                                                    <input class="st_type"
                                                                                        name="st_type"
                                                                                        type="checkbox"
                                                                                        id="1"
                                                                                        value="1"
                                                                                        @if ($typeCourse==1) checked @endif>
                                                                                </div>
                                                                                <div>
                                                                                    <label
                                                                                        style="margin-left:15px ; font-size:15px"
                                                                                        for="2">Self
                                                                                        Learning</label>
                                                                                    <input class="st_type"
                                                                                        name="st_type"
                                                                                        type="checkbox"
                                                                                        id="2"
                                                                                        value="2"
                                                                                        @if ($typeCourse==2) checked @endif>
                                                                                    <span class="error-message"
                                                                                        id="st_type_error"></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="step-content mt-3"
                                                                            id="step-2">

                                                                            <div class=""
                                                                                id="step-2-field-1">
                                                                                <h6>How many students will join?
                                                                                </h6>
                                                                                <ul class="multiple-select">
                                                                                    <li class="w-10">
                                                                                        <input type="radio"
                                                                                            id="1"
                                                                                            value="1"
                                                                                            name="no_of_joined_students" />
                                                                                        <label for="1"
                                                                                            class="rounded-full">1</label>
                                                                                    </li>
                                                                                    <li class="w-10">
                                                                                        <input type="radio"
                                                                                            id="2"
                                                                                            value="2"
                                                                                            name="no_of_joined_students" />
                                                                                        <label for="2"
                                                                                            class="rounded-full">2</label>
                                                                                    </li>
                                                                                    <li class="w-10">
                                                                                        <input type="radio"
                                                                                            id="3"
                                                                                            value="3"
                                                                                            name="no_of_joined_students" />
                                                                                        <label for="3"
                                                                                            class="rounded-full">3</label>
                                                                                    </li>
                                                                                    <li class="w-10">
                                                                                        <input type="radio"
                                                                                            id="4"
                                                                                            value="4"
                                                                                            name="no_of_joined_students" />
                                                                                        <label for="4"
                                                                                            class="rounded-full">4</label>
                                                                                    </li>
                                                                                    <li class="w-10">
                                                                                        <input type="radio"
                                                                                            id="5"
                                                                                            value="5"
                                                                                            name="no_of_joined_students" />
                                                                                        <label for="5"
                                                                                            class="rounded-full">5</label>
                                                                                    </li>
                                                                                    <span class="error-message"
                                                                                        id="no_of_joined_students_error"></span>
                                                                                </ul>
                                                                            </div>
                                                                            <div class=""
                                                                                id="step-2-field-2">
                                                                                <h6>For your evaluation, do you
                                                                                    prefer</h6>
                                                                                <ul class="multiple-select">
                                                                                    <li class="w-100">
                                                                                        <input type="radio"
                                                                                            id="Male"
                                                                                            value="Male"
                                                                                            name="preferred_teacher" />
                                                                                        <label for="Male">Male
                                                                                            Teacher</label>
                                                                                    </li>
                                                                                    <li class="w-100">
                                                                                        <input type="radio"
                                                                                            id="Female"
                                                                                            value="Female"
                                                                                            name="preferred_teacher" />
                                                                                        <label
                                                                                            for="Female">Female
                                                                                            Teacher</label>
                                                                                    </li>
                                                                                    <li class="w-100">
                                                                                        <input type="radio"
                                                                                            id="Either"
                                                                                            value="Either"
                                                                                            name="preferred_teacher" />
                                                                                        <label
                                                                                            for="Either">Either</label>
                                                                                    </li>
                                                                                    <span class="error-message"
                                                                                        id="preferred_teacher_error"></span>
                                                                                </ul>
                                                                            </div>
                                                                            <h6>How did you learn about CEF Online?
                                                                            </h6>
                                                                            <ul class="multiple-select">
                                                                                <li>
                                                                                    <input type="radio"
                                                                                        id="A Friend"
                                                                                        value="A Friend"
                                                                                        name="learn_about_lms" />
                                                                                    <label for="A Friend">A
                                                                                        Friend</label>
                                                                                </li>
                                                                                <li>
                                                                                    <input type="radio"
                                                                                        id="Social Media"
                                                                                        value="Social Media"
                                                                                        name="learn_about_lms" />
                                                                                    <label
                                                                                        for="Social Media">Social
                                                                                        Media</label>
                                                                                </li>
                                                                                <li>
                                                                                    <input type="radio"
                                                                                        id="E-Mail"
                                                                                        value="E-Mail"
                                                                                        name="learn_about_lms" />
                                                                                    <label
                                                                                        for="E-Mail">E-Mail</label>
                                                                                </li>
                                                                                <li>
                                                                                    <input type="radio"
                                                                                        id="Other"
                                                                                        value="Other"
                                                                                        name="learn_about_lms" />
                                                                                    <label
                                                                                        for="Other">Other</label>
                                                                                </li>
                                                                            </ul>
                                                                            <span class="error-message"
                                                                                id="learn_about_lms_error"></span>
                                                                        </div>
                                                                        <div class="step-content"
                                                                            id="step-3">
                                                                            <h6 class="text-center mt-3 mb-4">Pick
                                                                                a date and time
                                                                                for your free trial lesson (1 hour)
                                                                            </h6>
                                                                            <div id="wrapper"
                                                                                class="wrapper mb-2">
                                                                                <div class="container-calendar">
                                                                                    <div
                                                                                        class="d-flex justify-content-between align-items-center px-4">
                                                                                        <h6 class="w-50"
                                                                                            id="monthAndYear">
                                                                                        </h6>
                                                                                        <div
                                                                                            class="button-container-calendar">

                                                                                            <button id="previous"
                                                                                                type="button"
                                                                                                disabled>&#8249;</button>
                                                                                            <button id="next"
                                                                                                type="button">&#8250;</button>
                                                                                        </div>
                                                                                    </div>
                                                                                    <table class="table-calendar"
                                                                                        id="calendar"
                                                                                        data-lang="en">
                                                                                        <thead id="thead-month">
                                                                                        </thead>
                                                                                        <tbody id="calendar-body">
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <input type="hidden" name="preferDate"
                                                                            id="preferDate" value="">
                                                                        <input type="hidden"
                                                                            name="selectedDate"
                                                                            id="selectedDate" value="">
                                                                        <input type="hidden"
                                                                            name="selectedMonth"
                                                                            id="selectedMonth" value="">
                                                                        <input type="hidden"
                                                                            name="selectedYear"
                                                                            id="selectedYear" value="">
                                                                        <div class="step-content"
                                                                            id="step-4">
                                                                            <h6 class="text-center mt-3 mb-2">
                                                                                Select Slot</h6>
                                                                            <div id="slots"
                                                                                class="slots mb-2 mt-4">
                                                                                <div
                                                                                    class="w-80 mx-auto d-flex flex-col flex-wrap">
                                                                                    <div class="slot">
                                                                                        <label for="1:00 AM">
                                                                                            <input type="radio"
                                                                                                name="preferTime"
                                                                                                id="1:00 AM"
                                                                                                value="1:00 AM"
                                                                                                checked> 1:00 AM
                                                                                        </label>
                                                                                    </div>
                                                                                    <div class="slot">
                                                                                        <label for="12:00 PM">
                                                                                            <input type="radio"
                                                                                                name="preferTime"
                                                                                                id="12:00 PM"
                                                                                                value="12:00 PM">
                                                                                            12:00 PM </label>
                                                                                    </div>
                                                                                    <div class="slot">
                                                                                        <label for="12:15 PM">
                                                                                            <input type="radio"
                                                                                                name="preferTime"
                                                                                                id="12:15 PM"
                                                                                                value="12:15 PM">
                                                                                            12:15 PM </label>
                                                                                    </div>
                                                                                    <div class="slot">
                                                                                        <label for="12:30 PM">
                                                                                            <input type="radio"
                                                                                                name="preferTime"
                                                                                                id="12:30 PM"
                                                                                                value="12:30 PM">
                                                                                            12:30 PM </label>
                                                                                    </div>
                                                                                    <div class="slot">
                                                                                        <label for="11:30 PM">
                                                                                            <input type="radio"
                                                                                                name="preferTime"
                                                                                                id="11:30 PM"
                                                                                                value="11:30 PM">
                                                                                            11:30 PM </label>
                                                                                    </div>

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                    <div class="button-container w-100 px-4">
                                                                        <button id="nextBtn"
                                                                            class="btn btn-primary w-100 my-2">Next</button>
                                                                        <button id="prevBtn"
                                                                            class="btn btn-secondary w-100"
                                                                            disabled>Previous</button>
                                                                        <span id="message"
                                                                            style="display:none"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </section>

                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        @if ($course->learner_accessibility != 'free' && get_option('cashback_system_mode', 0))
                                        <div class="alert alert-success d-flex mb-15">
                                            <div class="align-items-center d-flex mr-15">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18"
                                                    height="18" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="feather feather-credit-card">
                                                    <rect x="1" y="4" width="22" height="16"
                                                        rx="2" ry="2"></rect>
                                                    <line x1="1" y1="10" x2="23"
                                                        y2="10"></line>
                                                </svg>
                                            </div>
                                            <div class="ml-10">
                                                <div class="font-14 font-weight-bold ">{{ __('Get Cashback') }}
                                                </div>
                                                <div class="font-12 ">
                                                    {{ __('By purchasing this product, you will get') }}
                                                    @if ($currencyPlacement ?? get_currency_placement() == 'after')
                                                    {{ calculateCashback($course->price) }}
                                                    {{ $currencySymbol ?? get_currency_symbol() }}
                                                    @else
                                                    {{ $currencySymbol ?? get_currency_symbol() }}
                                                    {{ calculateCashback($course->price) }}
                                                    @endif {{ __('cashback') }}.
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        @else
                                        <button class="bg-warning mb-30 theme-btn w-100">
                                            <span class="msgInfoChange">{{ __('Upcoming') }}</span>
                                        </button> @endif @endif @if (get_option('course_gift_mode', 0))
                                        <a href="{{ route('student.course-gift', $course->uuid) }}"
                                            class="alert alert-warning align-items-center border border-dark d-flex mb-30 p-2">
                                            <div class="mr-15">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20"
                                                    height="20" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="feather feather-gift text-gray">
                                                    <polyline points="20 12 20 22 4 22 4 12"></polyline>
                                                    <rect x="2" y="7" width="20" height="5"></rect>
                                                    <line x1="12" y1="22" x2="12"
                                                        y2="7"></line>
                                                    <path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path>
                                                    <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path>
                                                </svg>
                                            </div>
                                            <div class="ml-5">
                                                <h4 class="font-14 font-weight-bold text-gray">
                                                    {{ __('Gift this course') }}
                                                </h4>
                                                <p class="font-12 text-gray">
                                                    {{ __('Send this course as a gift to your friends') }}
                                                </p>
                                            </div>
                                        </a>
                                        @endif
                                        <div class="course-info-box-wishlist-btns d-flex">
                                            @if ($authRole == 3)
                                            <button class="theme-btn para-color font-medium mx-2 addToWishlist mb-30"
                                                title="Add to wishlist" data-course_id="{{ $course->id }}"
                                                data-route="{{ route('student.addToWishlist') }}"><span
                                                    class="iconify me-2"
                                                    data-icon="bytesize:heart"></span>{{ __('Add to Wishlist') }}</button>
                                            @endif
                                        </div>
                                        @if (@Auth::user() && Auth::user()->is_affiliator == AFFILIATOR && get_option('referral_status'))
                                        <div class="course-info-box-affiliate-link-copy position-relative">
                                            <h6 class="pb-20">Affiliate Link:</h6>
                                            <div class="input-group">
                                                <input id="ref_link" class="form-control w-100"
                                                    value="{{ $ref_link }}" />
                                                <button type="button"
                                                    class="btn affiliate-copy-btn border-0 position-absolute"
                                                    onclick="copyToClipboard('ref_link')"
                                                    title="{{ __('Copy to Clipboard') }}">
                                                    <i class="fa fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @endif



                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    </section>
    <div>
        <div class="home-page-container mb-5">
            <p class="section-tag">
                <span class="section-tag-icon">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M1.68226 4.33298L6.42758 1.96032C6.59649 1.87586 6.79532 1.87586 6.96423 1.96032L11.7096 4.33298C12.1518 4.55409 12.1518 5.18518 11.7096 5.40629L6.96423 7.77895C6.79532 7.86341 6.59649 7.86341 6.42758 7.77895L1.68226 5.40629C1.24003 5.18518 1.24003 4.55409 1.68226 4.33298Z"
                            stroke="#013370" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M13.3912 7.30432V5.17389L12.1738 4.56519" stroke="#013370"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <path
                            d="M2.43555 5.78259V8.58215C2.43555 9.35439 2.88014 10.0577 3.57774 10.3889L5.83861 11.4623C6.38144 11.7201 7.01139 11.7201 7.55422 11.4623L9.81509 10.3889C10.5127 10.0577 10.9573 9.35439 10.9573 8.58215V5.78259"
                            stroke="#013370" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
                <span class="section-tag-title">Recommended</span>
            </p>
            <h1 class="section-heading-home">You May Also Like</h1>
            <div class="course-container mx-auto w-90">
                @foreach ($courses as $key => $course1)
                <div onclick="window.location.href='{{ route('course-details', $course1->slug) }}'"
                    class="course-item-home" style="border: 1px solid #e5e8ec !important; border-radius: 0.5rem;">
                    <p class="course-tag-home">
                        {{ $course1->language->name }}
                    </p>
                    <a href="{{ route('course-details', $course1->slug) }}"
                        class="course-name-home">{{ Str::limit($course1->title, 40) }}</a>
                    <p class="course-rating-home">
                        <span>
                            <svg width="16" height="15" viewBox="0 0 16 15" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.60845 0L10.0066 4.69921L15.2169 5.52786L11.4888 9.26079L12.3107 14.4721L7.60845 12.08L2.90617 14.4721L3.72814 9.26079L0 5.52786L5.21029 4.69921L7.60845 0Z"
                                    fill="#FEC801" />
                            </svg>
                        </span>
                        <span class="">{{ $course1->average_rating }}</span>
                    </p>
                    <img src="{{ getImageFile($course1->image_path) }}" alt="Course"
                        class="course-image-home">
                    <p class="course-description-home"> {{ $course1->subtitle}}</p>
                    <hr class="my-2">
                    <div class="course-price-home-container">
                        <h2 class="course-price-home">{{ $course1->price }}</h2>
                        @if ($course1->course_type == COURSE_TYPE_GENERAL)
                        <div class="course-price-home-container-icon">
                            <span>
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect x="0.5" y="0.5" width="19" height="19" rx="1.5"
                                        fill="white" />
                                    <rect x="0.5" y="0.5" width="19" height="19" rx="1.5"
                                        stroke="black" />
                                    <path
                                        d="M11.9288 9.59143C12.2111 9.79063 12.2111 10.2093 11.9288 10.4085L7.91328 13.2421C7.58206 13.4758 7.125 13.2389 7.125 12.8335L7.125 7.16637C7.125 6.761 7.58207 6.52412 7.91328 6.75784L11.9288 9.59143Z"
                                        fill="white" stroke="black" />
                                </svg>
                            </span>
                            {{-- <span class="classes">{{ @$course1->lectures->count() > 0 ? @$course1->video_duration : '0' }} Classes</span> --}}
                            <span class="classes">{{ @$course1->classes > 0 ? @$course1->classes : '0' }} Classes</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            <div class="courses-slider">
                <div class="swiper course-slider">
                    <div class="swiper-wrapper">
                        @foreach ($courses as $key => $course2)
                        <div class="swiper-slide">
                            <div onclick="window.location.href='{{ route('course-details', $course2->slug) }}'"
                                class="course-item-home" style="border: 1px solid #e5e8ec !important; border-radius: 0.5rem;">
                                <p class="course-tag-home">
                                    {{ $course2->language->name }}
                                </p>
                                <a href="{{ route('course-details', $course2->slug) }}"
                                    class="course-name-home">{{ Str::limit($course2->title, 40) }}</a>
                                <p class="course-rating-home">
                                    <span>
                                        <svg width="16" height="15" viewBox="0 0 16 15" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M7.60845 0L10.0066 4.69921L15.2169 5.52786L11.4888 9.26079L12.3107 14.4721L7.60845 12.08L2.90617 14.4721L3.72814 9.26079L0 5.52786L5.21029 4.69921L7.60845 0Z"
                                                fill="#FEC801" />
                                        </svg>
                                    </span>
                                    <span class="">{{ $course2->average_rating }}</span>
                                </p>
                                <img src="{{ getImageFile($course2->image_path) }}" alt="Course"
                                    class="course-image-home">
                                <p class="course-description-home"> {{ $course2->subtitle}}</p>
                                <hr class="my-2">
                                <div class="course-price-home-container">
                                    <h2 class="course-price-home">{{ $course2->price }}</h2>
                                    @if ($course2->course_type == COURSE_TYPE_GENERAL)
                                    <div class="course-price-home-container-icon">
                                        <span>
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <rect x="0.5" y="0.5" width="19" height="19" rx="1.5"
                                                    fill="white" />
                                                <rect x="0.5" y="0.5" width="19" height="19" rx="1.5"
                                                    stroke="black" />
                                                <path
                                                    d="M11.9288 9.59143C12.2111 9.79063 12.2111 10.2093 11.9288 10.4085L7.91328 13.2421C7.58206 13.4758 7.125 13.2389 7.125 12.8335L7.125 7.16637C7.125 6.761 7.58207 6.52412 7.91328 6.75784L11.9288 9.59143Z"
                                                    fill="white" stroke="black" />
                                            </svg>
                                        </span>
                                        {{-- <span class="classes">{{ @$course2->lectures->count() > 0 ? @$course2->video_duration : '0' }} Classes</span> --}}
                                        <span class="classes">{{ @$course2->classes > 0 ? @$course2->classes : '0' }} Classes</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="modal fade" id="shareThisCourseModal" tabindex="-1" aria-labelledby="shareThisCourseModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="shareThisCourseModalLabel">{{ __('Share This Course') }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="share-course-list">
                    <li><a
                            href="http://www.facebook.com/sharer.php?u={{ route('course-details', $course->slug) }}"><span
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

<input type="hidden" class="courseReviewPaginateRoute"
    value="{{ route('frontend.reviewPaginate', $course->id) }}">

<input type="hidden" class="studentReviewCreateRoute" value="{{ route('student.review.create') }}">
<input type="hidden" class="studentReviewPaginateRoute" value="{{ route('student.reviewPaginate', $course->id) }}">
<input type="hidden" class="course_id" value="{{ $course->id}}">

<div class="modal fade VideoTypeModal" id="newVideoPlayerModal" tabindex="-1"
    aria-labelledby="newVideoPlayerModal" aria-hidden="true">
    <div class="modal-header border-bottom-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span
                class="iconify" data-icon="akar-icons:cross"></span></button>
    </div>
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="video-player-area" style="width: 100%; height: 80vh !important;">
                    @if ($course->intro_video_check == 1 && getVideoFile($course->video))
                    <video id="player2" playsinline controls style="width: 100%; height: 80vh !important; object-fit: contain;"
                        data-poster="{{ getImageFile($course->image) }}" controlsList="nodownload">
                        <source src="{{ getVideoFile($course->video) }}" type="video/mp4">
                    </video>
                    @endif

                    @if ($course->intro_video_check == 2 && $course->youtube_video_id)
                    <div class="plyr__video-embed" id="youtubePlayer2" style="width: 100%; height: 80vh !important;">
                        <iframe src="https://www.youtube.com/embed/{{ @$course->youtube_video_id }}"
                            style="width: 100%; height: 100%;"
                            allowfullscreen allowtransparency allow="autoplay">
                        </iframe>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade textUploadModal venoBoxTypeModal" id="textUploadModal" tabindex="-1"
    aria-hidden="true">
    <div class="modal-header border-bottom-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span
                class="iconify" data-icon="akar-icons:cross"></span></button>
    </div>
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-body">
                <p class="getLectureText"></p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade textUploadModal venoBoxTypeModal" id="imageUploadModal" tabindex="-1"
    aria-hidden="true">
    <div class="modal-header border-bottom-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span
                class="iconify" data-icon="akar-icons:cross"></span></button>
    </div>
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-body">
                <img src="" alt="" class="img-fluid getLectureImage">
            </div>
        </div>
    </div>
</div>


<div class="modal fade venoBoxTypeModal" id="slideModal" tabindex="-1" aria-hidden="true">
    <div class="modal-header border-bottom-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span
                class="iconify" data-icon="akar-icons:cross"></span></button>
    </div>
    <div style="height: 50%" class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-body">
                <iframe class="getLectureSlide" src="" width="100%" height="600"
                    frameborder="0" scrolling="no"></iframe>
            </div>
        </div>
    </div>
</div>


<div class="modal fade venoBoxTypeModal" id="audioPlayerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-header border-bottom-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span
                class="iconify" data-icon="akar-icons:cross"></span></button>
    </div>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">

                <audio class="getLectureAudio" src="" type="audio/mp3" style="width: 550px" controls
                    controlsList="nodownload">
                </audio>
            </div>
        </div>
    </div>
</div>

<div class="modal fade venoBoxTypeModal" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="video-player-area" style="width: 100%; height: 80vh !important;">
                    @if ($course->intro_video_check == 1 && getVideoFile($course->video))
                    <video id="player2" playsinline controls style="width: 100%; height: 80vh !important; object-fit: contain;"
                        data-poster="{{ getImageFile($course->image) }}" controlsList="nodownload">
                        <source src="{{ getVideoFile($course->video) }}" type="video/mp4">
                    </video>
                    @endif

                    @if ($course->intro_video_check == 2 && $course->youtube_video_id)
                    <div class="plyr__video-embed" id="youtubePlayer2" style="width: 100%; height: 80vh !important;">
                        <iframe src="https://www.youtube.com/embed/{{ @$course->youtube_video_id }}"
                            style="width: 100%; height: 100%;"
                            allowfullscreen allowtransparency allow="autoplay">
                        </iframe>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('style')
<!-- Video Player css -->
<link rel="stylesheet" href="{{ asset('frontend/assets/vendor/video-player/plyr.css') }}">

@endpush @push('script')

<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/js/selectize.min.js"
    integrity="sha512-IOebNkvA/HZjMM7MxL0NYeLYEalloZ8ckak+NDtOViP7oiYzG5vn6WVXyrJDiJPhl4yRdmNAG49iuLmhkUdVsQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>


<script src="{{ asset('frontend/assets/js/custom/rating.js') }}"></script>
<script src="{{ asset('frontend/assets/js/course/student-review-paginate.js') }}"></script>


<script>
    "use strict"
    $(".intro_video_check").click(function() {
        var intro_video_check = $("input[name='intro_video_check']:checked").val();
        introVideoCheck(intro_video_check);
    });


    function introVideoCheck(intro_video_check) {
        if (intro_video_check == 1) {
            $('#video_review').removeClass('d-none');
            $('.videoSource').removeClass('d-none');
            $('.videoSourceYoutube').addClass('d-none');
            $('#youtube_video_id').addClass('d-none');
        }
        if (intro_video_check == 2) {
            $('#video_review').addClass('d-none');
            $('.videoSource').addClass('d-none');
            $('.videoSourceYoutube').removeClass('d-none');
            $('#youtube_video_id').removeClass('d-none');
        }
    }
</script>

<script>
    $(document).ready(function() {
        $('#country-id').selectize({
            create: false,
            sortField: 'text',
            searchField: 'text',
            onDropdownOpen: function($dropdown) {
                this.clear();
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
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

        var monthDefault = ["January", "February", "March", "April", "May", "June", "July", "August",
            "September", "October", "November", "December"
        ];
        var dayDefault = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

        if (lang == "en") {
            months = monthDefault;
            days = dayDefault;
        } else if (lang == "id") {
            months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September",
                "Oktober", "November", "Desember"
            ];
            days = ["Ming", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"];
        } else if (lang == "fr") {
            months = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre",
                "Octobre", "Novembre", "Décembre"
            ];
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

        var selectedDate = $('#selectedDate').val();
        var selectedMonth = $('#selectedMonth').val();
        // selectedMonth = (parseInt(selectedMonth) + parseInt(1))
        var selectedYear = $('#selectedYear').val();
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

                    if (selectedDate === date && selectedYear === year && selectedMonth === month) {

                        // alert(selectedDate+'-----'+date)
                        // alert(selectedYear+'-----'+year)
                        // alert(selectedMonth+'-----'+month)
                        var dateValue = year + '-' + (parseInt(month) + parseInt(1)) + '-' + date;
                        $('#preferDate').val(dateValue)
                        cell.className = "date-picker selected";
                    } else if (date === today.getDate() && year === today.getFullYear() && month === today.getMonth()) {
                        // alert(selectedDate+'-----'+date)
                        // alert(selectedYear+'-----'+year)
                        // alert(selectedMonth+'-----'+month)
                        var dateValue = year + '-' + (parseInt(month) + parseInt(1)) + '-' + date;
                        $('#preferDate').val(dateValue)
                        cell.className = "date-picker selected";
                    } else if (year < today.getFullYear()) {
                        cell.setAttribute('disabled', 'true');
                        cell.className = "disabled-td";
                    } else if (year === today.getFullYear() && month < today.getMonth()) {
                        cell.setAttribute('disabled', 'true');
                        cell.className = "disabled-td";
                    } else if (month === today.getMonth() && date < today.getDate()) {
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





    $(document).ready(function() {
        $("#calendar-body").on('click', 'tr td', function() {
            //Use .text() as td doesn't have method .val()
            //Empty first time as the td:first has no text until clicked.

            $("#calendar-body tr td").removeClass('selected')

            var date = $(this).attr('data-date');
            var month = $(this).attr('data-month');
            var year = $(this).attr('data-year');

            if (date === "") {

                $(this).attr('disabled', 'true');
                $(this).addClass('disabled-td')
            } else if (year < today.getFullYear()) {
                $(this).attr('disabled', 'true');
                $(this).addClass('disabled-td')
            } else if (year === today.getFullYear() && month < today.getMonth()) {
                $(this).attr('disabled', 'true');
                $(this).addClass('disabled-td')
            } else if (month === today.getMonth() && date < today.getDate()) {
                $(this).attr('disabled', 'true');
                $(this).addClass('disabled-td')
            } else {
                var dateValue = year + '-' + (parseInt(month)) + '-' + date;
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
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
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
        const steps = [first, second, third, fourth];
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




            var checkStatus = $('#check_status').val();

            if (checkStatus == 'step_1_create') {
                var form = document.getElementById('multiStepForm');
                var formFields = form.querySelectorAll('input[name],select[name]');
                var fieldsToSubmit = ['st_type', 'fname', 'lname', 'email', 'password', 'time_zone',
                    'country_id', 'area_code', 'phone', 'referral_code', 'token'
                ]; // List the specific fields to validate
                var phoneValue = document.getElementById('phone');

                if (phoneValue != "") {
                    var fieldsToValidate = ['st_type', 'fname', 'lname', 'email', 'password', 'time_zone',
                        'country_id', 'phone'
                    ]; // List the specific fields to validate
                } else {
                    var fieldsToValidate = ['st_type', 'fname', 'lname', 'email', 'password', 'time_zone',
                        'country_id'
                    ]; // List the specific fields to validate
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


                fieldsToValidate.forEach(fieldName => {
                    var field = form.querySelector(`[name="${fieldName}"]`);
                    if (field && !field.value.trim()) {
                        field.classList.add('error');
                        var errorElement = document.getElementById(`${fieldName}_error`);
                        if (errorElement) {
                            errorElement.textContent = `${fieldName.replace('_', ' ')} is required`;
                        }
                        isValid = false;
                    } else if (fieldName === 'email' && field.value.trim()) {
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
                    var email = $('input[name="email"]').val()

                    $("#nextBtn").attr("disabled", "disabled");
                    $("#message").css('color', 'green');
                    $("#message").css('font-size', '20px');
                    $("#message").css('text-align', 'center');
                    $("#message").text("Please Wait!!!!");
                    $("#message").css('display', 'block');

                    $.ajax({
                        type: "POST",
                        url: "{{ route('check-student-email') }}",
                        data: {
                            email: email,
                        },
                        dataType: "JSON",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                            "Accept-Language": 'en',
                        },
                        success: function(response) {
                            if (!response.unique) {
                                $('input[name="email"]').addClass('error');
                                var errorElement = document.getElementById('email_error');
                                if (errorElement) {
                                    errorElement.textContent = 'Email is already taken';
                                }
                                isValid = false;

                                $("#nextBtn").removeAttr("disabled");
                                $("#message").text("");
                                $("#message").css('display', 'none');
                            } else {
                                var phone_value = $('input[name="phone"]').val()

                                $("#nextBtn").attr("disabled", "disabled");
                                $("#message").css('color', 'green');
                                $("#message").css('font-size', '20px');
                                $("#message").css('text-align', 'center');
                                $("#message").text("Please Wait!!!!");
                                $("#message").css('display', 'block');

                                $.ajax({
                                    type: "POST",
                                    url: "{{ route('check-student-phone') }}",
                                    data: {
                                        phone: phone_value,
                                    },
                                    dataType: "JSON",
                                    headers: {
                                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                            "content"),
                                        "Accept-Language": 'en',
                                    },
                                    success: function(response) {
                                        if (!response.unique) {
                                            $('input[name="phone"]').addClass('error');
                                            var errorElement = document.getElementById(
                                                'phone_error');
                                            if (errorElement) {
                                                errorElement.textContent =
                                                    'Phone is already taken';
                                            }
                                            isValid = false;
                                            $("#nextBtn").removeAttr("disabled");
                                            $("#message").text("");
                                            $("#message").css('display', 'none');
                                        } else {
                                            if (isValid) {

                                                $("#nextBtn").attr("disabled",
                                                    "disabled");
                                                $("#message").css('color', 'green');
                                                $("#message").css('font-size', '20px');
                                                $("#message").css('text-align',
                                                    'center');
                                                $("#message").text("Please Wait!!!!");
                                                $("#message").css('display', 'block');
                                                let formData = {};
                                                fieldsToSubmit.forEach(fieldName => {
                                                    var field = form
                                                        .querySelector(
                                                            `[name="${fieldName}"]`
                                                        );
                                                    if (field) {
                                                        formData[fieldName] =
                                                            field.value;
                                                    }
                                                });
                                                var st_values = [];
                                                $('.st_type').each(function() {
                                                    var $this = $(this);
                                                    if ($this.is(':checked')) {
                                                        st_values.push($this
                                                            .val());
                                                    }
                                                });
                                                formData['st_type'] = st_values;
                                                console.log(formData)
                                                $.ajax({
                                                    type: "POST",
                                                    url: "{{ route('store.sign-up-as-student-step1') }}",
                                                    data: formData,
                                                    dataType: "JSON",
                                                    headers: {
                                                        "X-CSRF-TOKEN": $(
                                                            'meta[name="csrf-token"]'
                                                        ).attr(
                                                            "content"),
                                                        "Accept-Language": 'en',
                                                    },
                                                    success: function(
                                                        response) {
                                                        if (response
                                                            .status) {
                                                            console.log(
                                                                formData
                                                            );
                                                            $('#check_status')
                                                                .val(
                                                                    "step_2_update"
                                                                );
                                                            $('#new_student_id')
                                                                .val(
                                                                    response
                                                                    .student_id
                                                                );
                                                            $('#type_st')
                                                                .val(
                                                                    response
                                                                    .stType
                                                                );
                                                            $("#nextBtn")
                                                                .removeAttr(
                                                                    "disabled"
                                                                );
                                                            $("#message")
                                                                .text("");
                                                            $("#message")
                                                                .css(
                                                                    'display',
                                                                    'none');

                                                            if (currentStep <
                                                                steps
                                                                .length - 1
                                                            ) {
                                                                if (response
                                                                    .stType ==
                                                                    2
                                                                ) { //Only Self Learning Selected
                                                                    $("#step-2-field-1")
                                                                        .css(
                                                                            'display',
                                                                            'none'
                                                                        );
                                                                    $("#step-2-field-2")
                                                                        .css(
                                                                            'display',
                                                                            'none'
                                                                        );
                                                                    $(".third")
                                                                        .css(
                                                                            'display',
                                                                            'none'
                                                                        );
                                                                    $(".fourth")
                                                                        .css(
                                                                            'display',
                                                                            'none'
                                                                        );
                                                                } else {
                                                                    $("#step-2-field-1")
                                                                        .css(
                                                                            'display',
                                                                            'block'
                                                                        );
                                                                    $("#step-2-field-2")
                                                                        .css(
                                                                            'display',
                                                                            'block'
                                                                        );
                                                                    $(".third")
                                                                        .css(
                                                                            'display',
                                                                            'block'
                                                                        );
                                                                    $(".fourth")
                                                                        .css(
                                                                            'display',
                                                                            'block'
                                                                        );
                                                                }
                                                                currentStep++;
                                                                updateStep
                                                                    ();
                                                            }
                                                        }
                                                    }
                                                });

                                            } else {
                                                console.log(
                                                    'Specific fields have errors');
                                                $("#nextBtn").removeAttr("disabled");
                                                $("#message").text("");
                                                $("#message").css('display', 'none');
                                            }
                                        }
                                    }
                                });
                            }
                        }
                    });
                } else {
                    console.log('Specific fields have errors');
                    $("#nextBtn").removeAttr("disabled");
                    $("#message").text("");
                    $("#message").css('display', 'none');
                }

            } else if (checkStatus == 'step_1_edit') {
                var studentID = $('#new_student_id').val();
                var form = document.getElementById('multiStepForm');
                var fieldsToSubmit = ['st_type', 'fname', 'lname', 'email', 'password', 'time_zone',
                    'country_id', 'area_code', 'phone', 'referral_code', 'token', 'new_student_id'
                ]; // List the specific fields to validate
                var phoneValue = document.getElementById('phone');

                if (phoneValue != "") {
                    var fieldsToValidate = ['st_type', 'fname', 'lname', 'email', 'password', 'time_zone',
                        'country_id', 'phone'
                    ]; // List the specific fields to validate
                } else {
                    var fieldsToValidate = ['st_type', 'fname', 'lname', 'email', 'password', 'time_zone',
                        'country_id'
                    ]; // List the specific fields to validate
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
                    } else if (fieldName === 'email' && field.value.trim()) {
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
                    $("#message").css('color', 'green');
                    $("#message").css('font-size', '20px');
                    $("#message").css('text-align', 'center');
                    $("#message").text("Please Wait!!!!");
                    $("#message").css('display', 'block');

                    var email = $('input[name="email"]').val();
                    $.ajax({
                        type: "POST",
                        url: "{{ route('check-student-email-update') }}",
                        data: {
                            email: email,
                            given_student_id: studentID,
                        },
                        dataType: "JSON",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                            "Accept-Language": 'en',
                        },
                        success: function(response) {
                            if (!response.unique) {
                                $('input[name="email"]').addClass('error');
                                var errorElement = document.getElementById('email_error');
                                if (errorElement) {
                                    errorElement.textContent = 'Email is already taken';
                                }
                                isValid = false;
                                $("#nextBtn").removeAttr("disabled");
                                $("#message").text("");
                                $("#message").css('display', 'none');
                            } else {

                                $("#nextBtn").attr("disabled", "disabled");
                                $("#message").css('color', 'green');
                                $("#message").css('font-size', '20px');
                                $("#message").css('text-align', 'center');
                                $("#message").text("Please Wait!!!!");
                                $("#message").css('display', 'block');


                                var phone_value = $('input[name="phone"]').val();
                                $.ajax({
                                    type: "POST",
                                    url: "{{ route('check-student-phone-update') }}",
                                    data: {
                                        phone: phone_value,
                                        given_student_id: studentID,
                                    },
                                    dataType: "JSON",
                                    headers: {
                                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                            "content"),
                                        "Accept-Language": 'en',
                                    },
                                    success: function(response) {
                                        if (!response.unique) {
                                            $('input[name="phone"]').addClass('error');
                                            var errorElement = document.getElementById(
                                                'phone_error');
                                            if (errorElement) {
                                                errorElement.textContent =
                                                    'Phone is already taken';
                                            }
                                            isValid = false;
                                            $("#nextBtn").removeAttr("disabled");
                                            $("#message").text("");
                                            $("#message").css('display', 'none');
                                        } else {
                                            if (isValid) {
                                                $("#nextBtn").attr("disabled",
                                                    "disabled");
                                                $("#message").css('color', 'green');
                                                $("#message").css('font-size', '20px');
                                                $("#message").css('text-align',
                                                    'center');
                                                $("#message").text("Please Wait!!!!");
                                                $("#message").css('display', 'block');
                                                let formData = {};
                                                fieldsToSubmit.forEach(fieldName => {
                                                    var field = form
                                                        .querySelector(
                                                            `[name="${fieldName}"]`
                                                        );
                                                    if (field) {
                                                        formData[fieldName] =
                                                            field.value;
                                                    }
                                                });
                                                var st_values = [];
                                                $('.st_type').each(function() {
                                                    var $this = $(this);
                                                    if ($this.is(':checked')) {
                                                        st_values.push($this
                                                            .val());
                                                    }
                                                });
                                                formData['st_type'] = st_values;
                                                console.log(formData)
                                                $.ajax({
                                                    type: "POST",
                                                    url: "{{ route('update.sign-up-as-student-step1') }}",
                                                    data: formData,
                                                    dataType: "JSON",
                                                    headers: {
                                                        "X-CSRF-TOKEN": $(
                                                            'meta[name="csrf-token"]'
                                                        ).attr(
                                                            "content"),
                                                        "Accept-Language": 'en',
                                                    },
                                                    success: function(
                                                        response) {
                                                        if (response
                                                            .status) {
                                                            console.log(
                                                                formData
                                                            );
                                                            $('#check_status')
                                                                .val(
                                                                    "step_2_update"
                                                                );
                                                            $('#new_student_id')
                                                                .val(
                                                                    response
                                                                    .student_id
                                                                );
                                                            $('#type_st')
                                                                .val(
                                                                    response
                                                                    .stType
                                                                );
                                                            $("#nextBtn")
                                                                .removeAttr(
                                                                    "disabled"
                                                                );
                                                            $("#message")
                                                                .text("");
                                                            $("#message")
                                                                .css(
                                                                    'display',
                                                                    'none');

                                                            if (currentStep <
                                                                steps
                                                                .length - 1
                                                            ) {
                                                                if (response
                                                                    .stType ==
                                                                    2
                                                                ) { //Only Self Learning Selected

                                                                    $("#step-2-field-1")
                                                                        .css(
                                                                            'display',
                                                                            'none'
                                                                        );
                                                                    $("#step-2-field-2")
                                                                        .css(
                                                                            'display',
                                                                            'none'
                                                                        );
                                                                    $(".third")
                                                                        .css(
                                                                            'display',
                                                                            'none'
                                                                        );
                                                                    $(".fourth")
                                                                        .css(
                                                                            'display',
                                                                            'none'
                                                                        );
                                                                } else {

                                                                    $("#step-2-field-1")
                                                                        .css(
                                                                            'display',
                                                                            'block'
                                                                        );
                                                                    $("#step-2-field-2")
                                                                        .css(
                                                                            'display',
                                                                            'block'
                                                                        );
                                                                    $(".third")
                                                                        .css(
                                                                            'display',
                                                                            'block'
                                                                        );
                                                                    $(".fourth")
                                                                        .css(
                                                                            'display',
                                                                            'block'
                                                                        );
                                                                }
                                                                currentStep++;
                                                                updateStep
                                                                    ();
                                                            }
                                                        }
                                                    }
                                                });

                                            } else {
                                                console.log(
                                                    'Specific fields have errors');
                                                $("#nextBtn").removeAttr("disabled");
                                                $("#message").text("");
                                                $("#message").css('display', 'none');
                                            }
                                        }
                                    }
                                });
                            }
                        }
                    });
                } else {
                    console.log('Specific fields have errors');
                    $("#nextBtn").removeAttr("disabled");
                    $("#message").text("");
                    $("#message").css('display', 'none');
                }

            } else if (checkStatus == 'step_2_update') {
                var updated_st_type = $('#type_st').val();
                var updated_slug = $('#slug').val();
                var form = document.getElementById('multiStepForm');
                var fieldsToSubmit = ['no_of_joined_students', 'preferred_teacher', 'learn_about_lms', 'token',
                    'new_student_id'
                ]; // List the specific fields to validate
                // var fieldsToValidate = ['lms_uses','no_of_joined_students','preferred_teacher', 'learn_about_lms']; // List the specific fields to validate
                var fieldsToValidate = ['no_of_joined_students', 'preferred_teacher',
                    'learn_about_lms'
                ]; // List the specific fields to validate

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
                    if (fieldName === 'no_of_joined_students' && updated_st_type != 2) {
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
                    else if (fieldName === 'preferred_teacher' && updated_st_type != 2) {
                        var isChecked = Array.from(fields).some(radio => radio.checked);

                        if (!isChecked) {
                            var errorElement = document.getElementById(`${fieldName}_error`);
                            if (errorElement) {
                                errorElement.textContent = 'Select preferred teacher type';
                            }
                            isValid = false;
                        }
                    } else if (fieldName === 'learn_about_lms') {
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
                    $("#message").css('color', 'green');
                    $("#message").css('font-size', '20px');
                    $("#message").css('text-align', 'center');
                    $("#message").text("Please Wait!!!!");
                    $("#message").css('display', 'block');
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
                    if (updated_st_type != 2) {
                        formData['no_of_joined_students'] = $('input[name="no_of_joined_students"]:checked')
                            .val()
                        formData['preferred_teacher'] = $('input[name="preferred_teacher"]:checked').val()
                        formData['st_type_updated'] = updated_st_type
                    } else {
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
                        url: "{{ route('update.sign-up-as-student-step2') }}",
                        data: formData,
                        dataType: "JSON",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                            "Accept-Language": 'en',
                        },
                        success: function(response) {
                            if (response.status) {
                                if (response.stTypeUpdated == 2) {
                                    $("#nextBtn").attr("disabled", "disabled");
                                    $("#prevBtn").attr("disabled", "disabled");
                                    $("#message").css('color', 'green');
                                    $("#message").css('font-size', '20px');
                                    $("#message").css('text-align', 'center');
                                    $("#message").text(response.message);
                                    $("#message").css('display', 'block');
                                    setTimeout(() => {
                                        $('#signUpModal').modal('hide');
                                        // $('#loginModal').modal('show');
                                        window.location.href = response.url;
                                    }, 3000);
                                } else {
                                    var studentTrial = $('#new_trial_id').val();
                                    if (studentTrial) {
                                        $('#check_status').val("step_3_update_free_trial");
                                        $('#new_trial_id').val(studentTrial);
                                    } else {
                                        $('#check_status').val("step_3_create_free_trial");
                                    }
                                    $('#new_student_id').val(response.student_id);

                                    $("#nextBtn").removeAttr("disabled");
                                    $("#prevBtn").removeAttr("disabled");
                                    $("#message").text("");
                                    $("#message").css('display', 'none');
                                    if (currentStep < steps.length - 1) {
                                        currentStep++;
                                        updateStep();
                                    }
                                }
                            }
                        }
                    });

                } else {

                    console.log('Specific fields have errors');
                    $("#nextBtn").removeAttr("disabled");
                    $("#prevBtn").removeAttr("disabled");
                    $("#message").text("");
                    $("#message").css('display', 'none');
                }

            } else if (checkStatus == 'step_3_create_free_trial') {
                var studentID = $('#new_student_id').val();
                var form = document.getElementById('multiStepForm');
                var fieldsToSubmit = ['no_of_joined_students', 'preferred_teacher', 'learn_about_lms', 'token',
                    'new_student_id'
                ]; // List the specific fields to validate
                var fieldsToValidate = ['no_of_joined_students', 'preferred_teacher',
                    'learn_about_lms'
                ]; // List the specific fields to validate
                let isValid = true;

                $('#check_status').val("step_4_update_free_trial");
                if (currentStep < steps.length - 1) {
                    currentStep++;
                    updateStep();
                }

            } else if (checkStatus == 'step_3_update_free_trial') {
                var form = document.getElementById('multiStepForm');
                var fieldsToSubmit = ['no_of_joined_students', 'preferred_teacher', 'learn_about_lms', 'token',
                    'new_student_id', 'new_trial_id'
                ]; // List the specific fields to validate
                var fieldsToValidate = ['no_of_joined_students', 'preferred_teacher',
                    'learn_about_lms'
                ]; // List the specific fields to validate
                let isValid = true;
                $('#check_status').val("step_4_update_free_trial");
                if (currentStep < steps.length - 1) {
                    currentStep++;
                    updateStep();
                }
            } else if (checkStatus == 'step_4_update_free_trial') {

                var updated_slug = $('#slug').val();
                $("#nextBtn").attr("disabled", "disabled");
                $("#prevBtn").attr("disabled", "disabled");
                $("#message").css('color', 'green');
                $("#message").css('font-size', '20px');
                $("#message").css('text-align', 'center');
                $("#message").text("Please Wait!!!!");
                $("#message").css('display', 'block');

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
                    url: "{{ route('update.sign-up-as-student-step4') }}",
                    data: formData,
                    dataType: "JSON",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        "Accept-Language": 'en',
                    },
                    success: function(response) {
                        if (response.status) {
                            $("#nextBtn").attr("disabled", "disabled");
                            $("#prevBtn").attr("disabled", "disabled");
                            $("#message").css('color', 'green');
                            $("#message").css('font-size', '20px');
                            $("#message").css('text-align', 'center');
                            $("#message").text(response.message);
                            $("#message").css('display', 'block');
                            setTimeout(() => {
                                $('#signUpModal').modal('hide');
                                // $('#loginModal').modal('show');
                                window.location.href = response.url;
                            }, 3000);

                        } else {
                            $("#nextBtn").attr("disabled", "disabled");
                            $("#prevBtn").attr("disabled", "disabled");
                            $("#message").css('color', 'red');
                            $("#message").css('font-size', '20px');
                            $("#message").css('text-align', 'center');
                            $("#message").text(response.message);
                            $("#message").css('display', 'block');
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
            var checkStatus = $('#check_status').val();
            if (checkStatus == 'step_2_update') {
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
            } else if (checkStatus == 'step_3_create_free_trial') {

                $('#check_status').val("step_2_update");
                if (currentStep > 0) {
                    currentStep--;
                    updateStep();
                }
            } else if (checkStatus == 'step_4_update_free_trial') {

                $('#check_status').val("step_3_update_free_trial");
                if (currentStep > 0) {
                    currentStep--;
                    updateStep();
                }
            } else if (checkStatus == 'step_3_update_free_trial') {

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
    // Add this script after your existing video player initialization
$(document).ready(function() {
    
    // Handle modal close events for the video player modal
    $('#newVideoPlayerModal').on('hidden.bs.modal', function () {
        // Pause HTML5 video if it exists
        const video = document.querySelector('#player2');
        if (video) {
            video.pause();
        }
        
        // Pause Plyr video player if it exists
        if (typeof zai_player1 !== 'undefined' && zai_player1) {
            zai_player1.pause();
        }
        
        // Stop YouTube video by reloading the iframe src
        const youtubePlayer = document.querySelector('#youtubePlayer2 iframe');
        if (youtubePlayer) {
            const src = youtubePlayer.src;
            youtubePlayer.src = '';
            youtubePlayer.src = src;
        }
        
        // Alternative method for YouTube - if you have access to YouTube API
        if (typeof zai_player3 !== 'undefined' && zai_player3) {
            zai_player3.pause();
        }
    });

    // Handle modal close events for the preview modal
    $('#previewModal').on('hidden.bs.modal', function () {
        // Pause HTML5 video if it exists
        const video = document.querySelector('#previewModal #player2');
        if (video) {
            video.pause();
        }
        
        // Stop YouTube video by reloading the iframe src
        const youtubePlayer = document.querySelector('#previewModal #youtubePlayer2 iframe');
        if (youtubePlayer) {
            const src = youtubePlayer.src;
            youtubePlayer.src = '';
            youtubePlayer.src = src;
        }
    });

    // Handle audio modal close
    $('#audioPlayerModal').on('hidden.bs.modal', function () {
        const audio = document.querySelector('.getLectureAudio');
        if (audio) {
            audio.pause();
            audio.currentTime = 0; // Reset to beginning
        }
    });

    // Also handle when clicking the close button or clicking outside modal
    $('.btn-close').on('click', function() {
        const modal = $(this).closest('.modal');
        modal.modal('hide');
    });

    // Additional safety - pause all videos when any modal is hidden
    $('.modal').on('hidden.bs.modal', function () {
        // Pause all HTML5 videos
        $('video').each(function() {
            this.pause();
        });
        
        // Pause all audio elements
        $('audio').each(function() {
            this.pause();
        });
        
        // Stop all YouTube iframes by reloading their src
        $('iframe[src*="youtube.com"]').each(function() {
            const src = this.src;
            this.src = '';
            this.src = src;
        });
    });
});

// Alternative approach using vanilla JavaScript if you prefer
document.addEventListener('DOMContentLoaded', function() {
    // Get modal elements
    const videoModal = document.getElementById('newVideoPlayerModal');
    const previewModal = document.getElementById('previewModal');
    const audioModal = document.getElementById('audioPlayerModal');
    
    // Function to stop all media in a modal
    function stopMediaInModal(modal) {
        // Pause videos
        const videos = modal.querySelectorAll('video');
        videos.forEach(video => {
            video.pause();
        });
        
        // Pause audio
        const audios = modal.querySelectorAll('audio');
        audios.forEach(audio => {
            audio.pause();
            audio.currentTime = 0;
        });
        
        // Stop YouTube iframes
        const youtubeIframes = modal.querySelectorAll('iframe[src*="youtube.com"]');
        youtubeIframes.forEach(iframe => {
            const src = iframe.src;
            iframe.src = '';
            iframe.src = src;
        });
    }
    
    // Add event listeners for modal close
    if (videoModal) {
        videoModal.addEventListener('hidden.bs.modal', () => stopMediaInModal(videoModal));
    }
    
    if (previewModal) {
        previewModal.addEventListener('hidden.bs.modal', () => stopMediaInModal(previewModal));
    }
    
    if (audioModal) {
        audioModal.addEventListener('hidden.bs.modal', () => stopMediaInModal(audioModal));
    }
});

</script>
<script>
    const players = Array.from(document.querySelectorAll('.js-player')).map((p) => new Plyr(p));
</script>
<!-- Video Player js -->
<script>
    "use strict"
    $('.lectureText').on('click', function() {
        var text = $(this).data("lecture_text")
        $('.getLectureText').html(text)
    })

    $('.lectureImage').on('click', function() {
        var image = $(this).data("lecture_image")
        $('.getLectureImage').attr('src', image)
    })

    $('.lectureSlide').on('click', function() {
        var slide = $(this).data("lecture_slide")
        $('.getLectureSlide').attr('src', slide)
    })

    $('.normalVideo').on('click', function() {
        var video = $(this).data("normal_video")
        console.log(video)
        $('.getNormalVideo').attr('src', video)
    })

    $('.lectureAudio').on('click', function() {
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
    if (code.length > 0) {
        saveItem(code);
    }

    function saveItem(val) {
        if (val === null) {
            return;
        }
        var all_ref = localStorage.getItem('ref')

        if (all_ref === '' || all_ref == null) {
            localStorage.setItem('ref', JSON.stringify([code]));
            return;
        }
        all_ref = JSON.parse(all_ref);
        if (!all_ref.includes(val)) {
            all_ref.push(val)
            localStorage.setItem('ref', JSON.stringify(all_ref));
        }
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var swiper5 = new Swiper(".course-slider", {
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
                531: {
                    slidesPerView: 1,
                },
                767: {
                    slidesPerView: 2,
                },
                992: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 5,
                },
            },
        });
    });
</script>


@endpush
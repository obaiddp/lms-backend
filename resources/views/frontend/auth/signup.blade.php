@extends('frontend.layouts.app')

@section('meta')
    @php
        $metaData = getMeta('signup');
    @endphp

    <meta name="description" content="{{ $metaData['meta_description'] }}">
    <meta name="keywords" content="{{ $metaData['meta_keyword'] }}">

    <!-- Open Graph meta tags for social sharing -->
    <meta property="og:type" content="Learning">
    <meta property="og:title" content="{{ $metaData['meta_title'] }}">
    <meta property="og:description" content="{{ $metaData['meta_description'] }}">
    <meta property="og:image" content="{{ $metaData['og_image'] }}">
    <meta property="og:url" content="{{ url()->current() }}">

    <meta property="og:site_name" content="{{ get_option('app_name') }}">

    <!-- Twitter Card meta tags for Twitter sharing -->
    <meta name="twitter:card" content="Learning">
    <meta name="twitter:title" content="{{ $metaData['meta_title'] }}">
    <meta name="twitter:description" content="{{ $metaData['meta_description'] }}">
    <meta name="twitter:image" content="{{ $metaData['og_image'] }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css  " rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css  " />
    {{--
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/css/intlTelInput.css  "> --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@17.0.8/build/css/intlTelInput.css  ">

@endsection

@section('content')
    <div class="signupPage">
        <!-- Page Header Start - Updated with Login Button -->
        <header class="breadcrumb-section px-3 xl:px-0">
            <div class="home-page-container d-flex justify-content-between align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}" class="aileron">
                                <i class="fas fa-home"></i>
                                Home
                            </a>
                        </li>
                        <li class="breadcrumb-separator">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.5 2.25L8.25 6L4.5 9.75" stroke="#77878F" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </li>
                        <li class="breadcrumb-item active aileron" aria-current="page">Sign Up</li>
                    </ol>
                </nav>
                
                <!-- Login Button for existing users -->
                @guest
                    @if ($type == null)
                        <a href="{{ route('student-login') }}" class="btn btn-sm btn-login aileron d-flex align-items-center">
                            <i class="fas fa-sign-in-alt me-2"></i> Already have an account? Login
                        </a>
                    @else
                        <a href="{{ route('student-login',['id'=> $type, 'slug'=> $slug]) }}" class="btn btn-sm btn-login aileron d-flex align-items-center">
                            <i class="fas fa-sign-in-alt me-2"></i> Already have an account? Login
                        </a>
                    @endif
                @endguest
            </div>
        </header>
        <!-- Page Header End -->

        <section class="bg-white pt-20">
            <div class="home-page-container d-flex justify-content-center align-items-center">
                <div class="row bg-white justify-content-start mt-4 px-3 xl:px-0" style="width: 100%;">
                    <div class="col-md-6 p-0 ps-2.5">
                      
                        <div class="mb-1">
                            <h1 class="fs-5 text-black font-bold aileron">Let's get your Journey Started!</h1>
                            <p style="font-size: 14px; margin-block: 0.2rem 1rem;" class="aileron grayNew">Please enter your credentials</p>
                        </div>
                        <!-- Signup Type Button Group -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="btn-group signup-group w-100" role="group" aria-label="Signup options">
                                    <button type="button" class="signup-btn active aileron p-0" data-value="student">
                                        <input type="radio" name="signup_type" value="student" checked class="d-none">
                                        Signup as Student
                                    </button>

                                    @if ($type == null)
                                        <button type="button" class="signup-btn aileron p-0" data-value="teacher">
                                            <input type="radio" name="signup_type" value="teacher" class="d-none">
                                            Signup as Instructor
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <style>
                            .signup-group {
                                display: flex;
                                border-radius: 8px;
                                overflow: hidden;
                            }

                            .signup-btn {
                                border: 1px solid #0a3a75;
                                background: transparent;
                                color: #0a3a75;
                                font-weight: 500;
                                transition: all 0.3s ease;
                                flex: 1;
                                position: relative;
                                margin: 0 !important;
                                height: 30px;
                            }

                            .signup-btn:first-child {
                                border-top-left-radius: 8px;
                                border-bottom-left-radius: 8px;
                            }

                            .signup-btn:last-child {
                                border-top-right-radius: 8px;
                                border-bottom-right-radius: 8px;
                            }

                            .signup-btn:not(:last-child) {
                                border-right: none;
                            }

                            .signup-btn:hover {
                                background: rgba(10, 58, 117, 0.1);
                                color: #0a3a75;
                            }

                            .signup-btn.active {
                                background: #0a3a75;
                                color: #fff;
                                border-color: #0a3a75;
                            }

                            /* Login Button Styles */
                            .btn-login {
                                background: transparent;
                                border: 1px solid #0a3a75;
                                color: #0a3a75;
                                border-radius: 8px;
                                padding: 6px 15px;
                                font-size: 14px;
                                transition: all 0.3s ease;
                                font-weight: 500;
                            }
                            
                            .btn-login:hover {
                                background: rgba(10, 58, 117, 0.1);
                                color: #0a3a75;
                            }
                            
                            @media (max-width: 768px) {
                                .btn-login {
                                    font-size: 12px;
                                    padding: 4px 10px;
                                }
                            }

                            /* Ensure buttons stay connected on mobile */
                            @media (max-width: 768px) {
                                .signup-group {
                                    flex-direction: row !important;
                                }
                                
                                .signup-btn {
                                    white-space: nowrap;
                                    font-size: 14px;
                                    padding: 12px 16px;
                                }
                            }

                            @media (max-width: 400px) {
                                .signup-btn {
                                    font-size: 12px;
                                    padding: 10px 12px;
                                }
                            }
                        </style>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const buttons = document.querySelectorAll('.signup-btn');
                                
                                buttons.forEach(button => {
                                    button.addEventListener('click', function() {
                                        // Remove active class from all buttons
                                        buttons.forEach(btn => btn.classList.remove('active'));
                                        
                                        // Add active class to clicked button
                                        this.classList.add('active');
                                        
                                        // Check the radio input
                                        const radio = this.querySelector('input[type="radio"]');
                                        radio.checked = true;
                                        
                                        // Trigger the change event for the radio
                                        const event = new Event('change');
                                        radio.dispatchEvent(event);
                                        
                                        // Show/hide appropriate form
                                        const formType = this.dataset.value;
                                        document.getElementById('student-form-container').style.display = formType === 'student' ? 'block' : 'none';
                                        document.getElementById('teacher-form-container').style.display = formType === 'teacher' ? 'block' : 'none';
                                        
                                        // Show/hide error containers
                                        document.getElementById('student-errors').style.display = formType === 'student' ? 'block' : 'none';
                                        document.getElementById('teacher-errors').style.display = formType === 'teacher' ? 'block' : 'none';
                                    });
                                });
                            });
                        </script>

                        <!-- Error containers for each form -->
                        <div id="student-errors" class="error-container">
                            @if(session('form_type') === 'student' || old('signup_type', 'student') === 'student')
                                @include('frontend.auth.signup.partials.errors')
                            @endif
                        </div>

                        <div id="teacher-errors" class="error-container" style="display: none;">
                            @if(session('form_type') === 'teacher')
                                @include('frontend.auth.signup.partials.errors')
                            @endif
                        </div>

                        <!-- Form Containers -->
                        <div id="student-form-container">
                            @include('frontend.auth.signup.student-form', ['form1Data' => $form1Data])
                        </div>

                        <div id="teacher-form-container" style="display: none;">
                            @include('frontend.auth.signup.teacher-form')
                        </div>

                        <div class="d-flex justify-content-center gap-3">
                        </div>
                    </div>

                    <div class="col-md-6 rounded-4 p-0 md:!ps-5 pe-2.5">
                        <div class="swiper mySwiper rounded-4">
                            <div class="swiper-wrapper rounded-4">

                                <div class="swiper-slide">
                                    <img src="{{ asset('Images/signin-1.webp') }}" class="rounded-4 w-100 h-90" />
                                </div>
                                <div class="swiper-slide">
                                    <img src="{{ asset('Images/signin-2.webp') }}" class="rounded-4 w-100 h-90" />
                                </div>
                                <div class="swiper-slide">
                                    <img src="{{ asset('Images/signin-3.webp') }}" class="rounded-4 w-100 h-90" />
                                </div>
                                <div class="swiper-slide">
                                    <img src="{{ asset('Images/signin-4.webp') }}" class="rounded-4 w-100 h-90" />
                                </div>
                            </div>
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js  "></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js  " defer></script>
{{--
<script src="{{ asset('frontend/assets/js/time-slots.js') }}"></script> --}}
<script src="{{ asset('frontend/assets/js/time-slots2.js') }}"></script>

<script>
    $(document).ready(function () {
        $('input[name="signup_type"]').on('change', function () {
            const selectedForm = $(this).val();
            // Hide all form containers and error containers
            $('#student-form-container, #teacher-form-container').hide();
            $('.error-container').hide();

            // Show the selected form and its errors
            $(`#${selectedForm}-form-container`).show();
            $(`#${selectedForm}-errors`).show();
        });

        const formType = "{{ old('signup_type', 'student') }}";
        if (formType === 'teacher') {
            $('input[name="signup_type"][value="teacher"]').prop('checked', true);
            $('#student-form-container, #student-errors').hide();
            $('#teacher-form-container, #teacher-errors').show();
        }
    });

    $(document).ready(function () {
        $('.select2').select2({
            placeholder: "Select Timezone",
            allowClear: true,
            width: '100%'
        });
    });

    $(document).ready(function () {
        $('#country').select2({
            placeholder: "Select a Country",
            allowClear: true,  // Allow clearing the selection
            width: '100%'      // Set width to fit the container
        });
    });

    $(document).ready(function () {
        $('#area_code').select2({
            placeholder: "Select Code",
            allowClear: true,  // Allow clearing the selection
            width: '100%'      // Set width to fit the container
        });
    });
</script>

<style>
    .calendar-text {
        font-size: 16px !important;
        font-weight: 700 !important;
        color: #000000 !important;
    }

    #step-4 {
        position: relative;
        z-index: 1000;
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-top: 15px;
    }

    .container-calendar {
        width: 80% !important;
        margin: 0 auto;
    }

    .month-year-display {
        font-size: 20px;
        font-weight: 700;
        color: #000000;
        width: 100%;
        text-align: center;
    }

    .button-container-calendar {
        width: 100%;
        display: flex;
        justify-content: space-between;
    }

    .nav-btn {
        background: none;
        border: none;
        font-size: 16px;
        color: #000000;
        cursor: pointer;
        padding: 2px 8px;
    }

    .nav-btn:hover {
        color: #0056b3;
    }

    .table-calendar {
        width: 100%;
        border-collapse: collapse;
    }

    .table-calendar th,
    .table-calendar td {
        text-align: center;
        padding: 8px 0;
        border: none;
    }

    .table-calendar th {
        color: #7a7a7a !important;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem !important;
    }

    .calendar-cell {
        cursor: pointer;
    }

    .calendar-cell span {
        display: inline-block;
        width: 30px;
        height: 30px;
        line-height: 30px;
        border-radius: 50%;
        transition: all 0.3s;
    }

    .calendar-cell:hover span,
    .calendar-cell.selected span {
        background-color: #0a3a75;
        color: #fff;
    }

    .disabled-cell {
        color: #adb5bd;
        pointer-events: none;
    }

    .empty-cell {
        visibility: hidden;
    }

    /* Time Slot Styling */
    .time-slot-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
        gap: 10px;
        margin-top: 15px;
    }

    .no-slots-message {
        width: 100%;
        background-color: #f8f9fa;
        border-radius: 8px;
        border: 1px dashed #dee2e6;
        text-align: center;
    }

    .no-slots-message svg {
        opacity: 0.7;
    }

    .no-slots-message p {
        color: #6c757d !important;
        font-weight: 500;
    }

    .slot-indicator {
        position: absolute;
        bottom: 4px;
        left: 50%;
        transform: translateX(-50%);
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: #4CAF50;
    }

    /* Time Slot Container */
    .time-slot-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 10px;
        margin-top: 15px;
    }

    /* Slot Styling */
    .slot {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
        background-color: #f9f9f9;
        position: relative;
    }

    .slot input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    .slot:hover {
        border-color: #0a3a75;
        background-color: #f0f7ff;
    }

    .slot input:checked+span {
        background: linear-gradient(135deg, #6ba0e0, #0c61e2);
        color: white;
        position: relative;
    }

    .slot input:checked+span::after {
        content: "✓";
        margin-left: 5px;
        font-weight: bold;
    }

    /* Make the entire slot clickable */
    .slot span {
        display: block;
        width: 100%;
        text-align: center;
        padding: 5px;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .signupPage {
        background: #f8f9fa;
        padding-top: 70px !important;
    }

    .signup-radio:checked {
        accent-color: #0a3a75;
        /* Blue color when checked */
    }

    .error-container {
        margin-bottom: 1rem;
    }

    #student-form-container,
    #teacher-form-container {
        transition: opacity 0.3s ease;
    }

    .breadcrumb-section {
        background: #F0F5F2 !important;
        padding-top: 25px !important;
        padding-bottom: 5px !important;
        width: 100%;
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        padding: 0;
        margin: 0;
        list-style: none;
    }

    .breadcrumb-item {
        display: flex;
        align-items: center;
    }

    .breadcrumb-item a {
        text-decoration: none;
        color: inherit;
    }

    .breadcrumb-separator {
        margin: 0 8px;
        color: #6c757d;
    }

    .breadcrumb-item.active {
        color: #6c757d;
    }

    .fas.fa-home {
        margin-right: 5px;
    }

    .reduced-height {
        height: 550px !important;
        /* Lower height for the left section */
    }

    .btn-group .btn {
        border: 1px solid #d3d3d3;
        /* Light gray border */
        padding: 10px 0;
    }

    .active-tab {
        background-color: white;
        color: black;
        border: 2px solid #f0eff2 !important;
        z-index: 1;
        /* Bring the active tab to the front if necessary */
    }

    .inactive-tab {
        background-color: #f0eff2 !important;
        color: black !important;
        border: 1px solid #f0eff2 !important;
        opacity: 40%;
    }

    .btn-group .btn:first-child {
        border-right: none;
        /* Remove the border between the two buttons */
    }

    select.form-control {
        -webkit-appearance: menulist;
        /* Ensures dropdown arrow appears */
        -moz-appearance: menulist;
        appearance: menulist;
    }

    .table-calendar td:hover span,
    .selected span {
        background-color: #0a3a75;
        /* Blue on selection */
        color: #fff;
        border: 1px solid #0a3a75;
        border-radius: 50%;
        padding: 0.4rem !important;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }

    .selected span {
        background-color: #0a3a75;
        /* Blue for selected date */
        color: #fff;
        border: 1px solid #0a3a75;
        border-radius: 50%;
        padding: 4px;
        margin: 0 auto;
    }

    .disabled-cell {
        color: #adb5bd;
        background-color: #f8f9fa;
        pointer-events: none;
    }

    /* Navigation buttons alignment */
    .nav-btn {
        background: none;
        border: none;
        font-size: 16px;
        color: #000000;
        cursor: pointer;
        padding: 2px 8px;
    }

    .nav-btn:hover {
        color: #0056b3;
    }

    .month-year-display {
        font-size: 20px;
        font-weight: 700;
        color: #000000;
    }

    /* Compact table structure */
    table {
        caption-side: bottom;
        border-collapse: collapse;
        margin: 0 auto !important;
        width: 100%;
    }

    th,
    td {
        max-width: 4rem !important;
        min-width: 4rem !important;
        text-align: center;
        height: 3rem !important;
        /* Compact height */
        padding: 4px !important;
        /* Minimal padding */
        font-size: 16px !important;
        /* Smaller font */
        border: 1px solid #dee2e6;
        color: #000000;
    }

    /* Style for the selected and hover states */
    .date-picker {
        cursor: pointer;
        text-align: center;
        padding: 5px;
        /* Small padding to keep circular structure */
        border-radius: 50%;
        transition: background-color 0.3s, border 0.3s;
    }

    .date-picker:hover {
        background-color: #f0f8ff;
    }

    .date-picker.selected {
        background-color: var(--theme-color);
        color: white;
    }

    /* Smaller navigation buttons for month switching */
    #next,
    #previous {
        font-size: 3rem !important;
        padding: 4px 8px;
    }

    /* Time Slot Container: Full Width with 4 slots per row */
    .time-slot-container {
        display: grid;
        /* Use Grid layout */
        grid-template-columns: repeat(4, 1fr);
        /* 4 slots in a row */
        gap: 5px;
        /* Add some space between slots */
        width: 100%;
        margin-top: 5px;
        /* Full width of the container */
    }

    /* Slot Styling */
    .slot {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px;
        border: 1px solid #ccc;
        /* Default gray border */
        border-radius: 5px;
        cursor: pointer;
        font-size: 0.9rem;
        font-weight: 500;
        transition: background-color 0.3s, border-color 0.3s;
    }

    /* Hide default radio button */
    .slot input {
        margin-right: 2px !important;
        margin-left: 2px !important;
    }

    /* Style for selected slots (blue border when checked) */
    .slot input:checked+span {
        border-color: #0a3a75;
        color: #0a3a75;
    }

    /* Slot hover effect */
    .slot:hover {
        border-color: #0a3a75;
        /* Blue border on hover */
    }

    .form-control::placeholder {
        color: #999999 !important;
        font-size: 0.9rem !important;
        height: 2.8rem !important;
    }

    .form-control {
        color: #999999 !important;
        font-size: 0.9rem !important;
        height: 2.8rem !important;
    }

    h6#monthAndYear {
        text-align: center;
        width: 100%;
    }

    .button-container-calendar {
        width: 100%;
    }

    button#next {
        right: 0 !important;
        position: absolute;
    }

    #thead-month th {
        color: #7a7a7a !important;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem !important;
    }

    td.calendar-cell,
    #thead-month th {
        border: none !important;
        min-width: 1rem !important;
        padding: 0 !important;
        max-width: 1rem !important;
    }

    .my-tab {
        height: 2.2rem !important;
        padding: 0 !important;
        font-size: 0.8rem !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        box-shadow: none !important;
    }

    td.calendar-cell:hover {
        cursor: pointer !important;
    }

    tbody#calendar-body {
        border-top: none !important;
    }

    h6.mt-3.mb-4.calendar-text.aileron {
        font-size: 0.9rem !important;
    }

    .container-calendar {
        width: 80% !important;
    }

    .swiper.mySwiper.rounded-4.swiper-initialized.swiper-horizontal.swiper-backface-hidden {
        height: 35rem;
    }

    @media (min-width: 300px) and (max-width: 530px) {
        .signupPage {
            padding-top: 4.4rem !important;
        }

        .swiper.mySwiper.rounded-4.swiper-initialized.swiper-horizontal.swiper-backface-hidden {
            height: 20rem;
            margin-top: 4rem;
        }
    }

    @media (min-width: 531px) and (max-width: 767px) {
        .signupPage {
            padding-top: 5.4rem !important;
        }

        .swiper.mySwiper.rounded-4.swiper-initialized.swiper-horizontal.swiper-backface-hidden {
            height: 34rem;
            margin-top: 4rem;
        }
    }

    @media (min-width: 768px) and (max-width: 992px) {
        .swiper.mySwiper.rounded-4.swiper-initialized.swiper-horizontal.swiper-backface-hidden {
            height: 22rem;
        }
    }

    @media (min-width: 993px) and (max-width: 1199px) {
        .swiper.mySwiper.rounded-4.swiper-initialized.swiper-horizontal.swiper-backface-hidden {
            height: 28rem;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js  "></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var swiper = new Swiper(".mySwiper", {
            pagination: {
                el: ".swiper-pagination",
            },
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 2500,
                disableOnInteraction: false,
            }
        });
    });
</script>
{{--
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/intlTelInput.min.js  "></script> --}}
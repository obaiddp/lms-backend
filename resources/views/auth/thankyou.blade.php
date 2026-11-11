<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="author" content="{{ get_option('app_copyright') }}">
        <meta name="robots" content="index, follow">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="msapplication-TileImage" content="{{ getImageFile(get_option('app_logo')) }}">
        <meta name="msapplication-TileColor" content="rgba(103, 20, 222,.55)">
        <meta name="theme-color" content="#754FFE"> @hasSection('meta') @yield('meta') @else @php $defaultMeta =
        getMeta('default'); @endphp
        <meta name="description" content="{{ $defaultMeta['meta_description'] }}">
        <meta name="keywords" content="{{ $defaultMeta['meta_keyword'] }}">
        <!-- Open Graph meta tags for social sharing -->
        <meta property="og:type" content="Learning">
        <meta property="og:title" content="{{ $defaultMeta['meta_title'] }}">
        <meta property="og:description" content="{{ $defaultMeta['meta_description'] }}">
        <meta property="og:image" content="{{ $defaultMeta['og_image'] }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:site_name" content="{{ get_option('app_name') }}">
        <!-- Twitter Card meta tags for Twitter sharing -->
        <meta name="twitter:card" content="Learning">
        <meta name="twitter:title" content="{{ $defaultMeta['meta_title'] }}">
        <meta name="twitter:description" content="{{ $defaultMeta['meta_description'] }}">
        <meta name="twitter:image" content="{{ $defaultMeta['og_image'] }}"> @endif <title>{{ get_option('app_name') }}
            - {{ __(@$pageTitle) }}</title>
        <!--======================================= All Css Style link ===========================================-->
        <!-- Bootstrap core CSS -->
        <link href="{{ asset('frontend/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('frontend/assets/css/jquery-ui.min.css') }}" rel="stylesheet">
        <!-- Font Awesome for this template -->
        <link href="{{ asset('frontend/assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet"
            type="text/css">
        <!-- Custom fonts for this template -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin> @if(get_option('app_font_design_type') ==
        2) @if(empty(get_option('app_font_link')))
        <link
            href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
            rel="stylesheet"> @else {!! get_option('app_font_link') !!} @endif @else
        <link
            href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
            rel="stylesheet"> @endif
        <link rel="stylesheet" href="{{ asset('frontend/assets/fonts/feather/feather.css') }}">
        <!-- Animate Css-->
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/animate.min.css') }}">
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/owl.theme.default.min.css') }}">
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/venobox.min.css') }}">
        <!-- Sweet Alert css -->
        <link rel="stylesheet" href="{{asset('admin/sweetalert2/sweetalert2.css')}}">
        <!-- Custom styles for this template -->
        <link href="{{ asset('frontend/assets/css/style.css') }}" rel="stylesheet"> @stack('theme-style')
        <link href="{{ asset('frontend/assets/css/extra.css') }}" rel="stylesheet">
        <!-- Responsive Css-->
        <link rel="stylesheet" href="{{ asset('frontend/assets/css/responsive.css') }}">
        <!-- FAVICONS -->
        <link rel="icon" href="{{ getImageFile(get_option('app_fav_icon')) }}" type="image/png" sizes="16x16">
        <link rel="shortcut icon" href="{{ getImageFile(get_option('app_fav_icon')) }}" type="image/x-icon">
        <link rel="shortcut icon" href="{{ getImageFile(get_option('app_fav_icon')) }}">
        <link rel="apple-touch-icon-precomposed" type="image/x-icon"
            href="{{ getImageFile(get_option('app_fav_icon')) }}" sizes="72x72">
        <link rel="apple-touch-icon-precomposed" type="image/x-icon"
            href="{{ getImageFile(get_option('app_fav_icon')) }}" sizes="114x114">
        <link rel="apple-touch-icon-precomposed" type="image/x-icon"
            href="{{ getImageFile(get_option('app_fav_icon')) }}" sizes="144x144">
        <link rel="apple-touch-icon-precomposed" type="image/x-icon"
            href="{{ getImageFile(get_option('app_fav_icon')) }}">
        <!-- DataTables -->
        <link rel="stylesheet"
            href="{{asset('frontend/assets/vendor/datatable/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
        <link rel="stylesheet"
            href="{{asset('frontend/assets/vendor/datatable/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
        <link rel="stylesheet"
            href="{{asset('frontend/assets/vendor/datatable/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]--> @stack('style') @toastr_css @include(getThemePath().'.layouts.dynamic-style')
        @if(get_option('pwa_enable'))
        <!-- PWA  -->
        <meta name="theme-color"
            content="{{ empty(get_option('app_theme_color')) ? '#5e3fd7' : get_option('app_theme_color') }}" />
        <link rel="apple-touch-icon" href="{{ getImageFile(get_option('app_fav_icon')) }}">
        <link rel="manifest" href="{{ asset('manifest.json') }}"> @endif @if(isEnableOpenAI())
        <link rel="stylesheet" href="{{asset('addon/AI/css/main.css')}}"> @endif <script async
            src="https://www.googletagmanager.com/gtag/js?id={{ get_option('measurement_id') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', "{{ get_option('measurement_id') }}");
        </script>
    </head> @php $selectedLanguage = selectedLanguage(); @endphp

    <body class="thanks {{$selectedLanguage->rtl == 1 ? 'direction-rtl' : 'direction-ltr' }}">
        @if(get_option('allow_preloader') == 1)
        <!-- Pre Loader Area start -->
        <div id="preloader">
            <div id="preloader_status"><img src="{{getImageFile(get_option('app_preloader'))}}" alt="img" /></div>
        </div>
        <!-- Pre Loader Area End --> @endif
        <!--Main Menu/Navbar Area Start --> @include(getThemePath().'.layouts.navbar')
        <!--Main Menu/Navbar Area Start -->
        <!-- Main Content Start--> @yield('content') <header class="hero-area gradient-bg position-relative h-38">
            <div class="section-overlay h-38">
                <div class="container">
                    <div class="row mx-5 mt-15">
                        <div class="col-md-3">
                            <svg xmlns="http://www.w3.org/2000/svg" id="Component_88_1"
                                data-name="Component 88 &#x2013; 1" width="200" height="200" viewBox="0 0 241.705 226">
                                <ellipse id="Ellipse_63" data-name="Ellipse 63" cx="113.5" cy="113" rx="113.5" ry="113"
                                    fill="#ebf8dc"></ellipse>
                                <g id="Group_55" data-name="Group 55" transform="translate(45.184 25.477)">
                                    <path id="Path_68" data-name="Path 68"
                                        d="M77.193-214.361a19.742,19.742,0,0,1,16.232,8.343,12,12,0,0,0,9.735,5,11.868,11.868,0,0,0,4.935-1.07,19.834,19.834,0,0,1,8.243-1.8,19.9,19.9,0,0,1,19.872,18.029,11.99,11.99,0,0,0,10.738,10.739,19.743,19.743,0,0,1,15.341,9.886A19.744,19.744,0,0,1,163.181-147a11.992,11.992,0,0,0,3.931,14.669,19.744,19.744,0,0,1,8.342,16.232,19.744,19.744,0,0,1-8.342,16.232,11.993,11.993,0,0,0-3.93,14.669,19.743,19.743,0,0,1-.891,18.228,19.743,19.743,0,0,1-15.341,9.887A11.99,11.99,0,0,0,136.21-46.344a19.9,19.9,0,0,1-19.872,18.03,19.832,19.832,0,0,1-8.243-1.8,11.869,11.869,0,0,0-4.936-1.07,12,12,0,0,0-9.735,5,19.741,19.741,0,0,1-16.232,8.342,19.742,19.742,0,0,1-16.232-8.343,12,12,0,0,0-9.735-5,11.869,11.869,0,0,0-4.935,1.07,19.832,19.832,0,0,1-8.243,1.8A19.9,19.9,0,0,1,18.175-46.344,11.99,11.99,0,0,0,7.437-57.083,19.743,19.743,0,0,1-7.9-66.97,19.743,19.743,0,0,1-8.8-85.2a11.991,11.991,0,0,0-3.931-14.669A19.744,19.744,0,0,1-21.068-116.1a19.744,19.744,0,0,1,8.342-16.232A11.991,11.991,0,0,0-8.8-147a19.744,19.744,0,0,1,.891-18.23,19.743,19.743,0,0,1,15.341-9.886,11.99,11.99,0,0,0,10.738-10.738,19.9,19.9,0,0,1,19.872-18.03,19.829,19.829,0,0,1,8.244,1.8,11.869,11.869,0,0,0,4.936,1.07,12,12,0,0,0,9.735-5A19.741,19.741,0,0,1,77.193-214.361Zm25.967,21.343a20.015,20.015,0,0,1-16.24-8.343,11.83,11.83,0,0,0-9.727-5,11.83,11.83,0,0,0-9.727,5,20.015,20.015,0,0,1-16.24,8.344,19.815,19.815,0,0,1-8.241-1.785,11.883,11.883,0,0,0-4.938-1.082,11.932,11.932,0,0,0-11.91,10.811,20.008,20.008,0,0,1-17.92,17.92,11.831,11.831,0,0,0-9.193,5.924,11.832,11.832,0,0,0-.534,10.924,20.009,20.009,0,0,1-6.559,24.479,11.832,11.832,0,0,0-5,9.728,11.832,11.832,0,0,0,5,9.728A20.009,20.009,0,0,1-1.51-81.893,11.831,11.831,0,0,0-.976-70.969a11.831,11.831,0,0,0,9.193,5.925,20.008,20.008,0,0,1,17.92,17.92,11.932,11.932,0,0,0,11.91,10.81A11.885,11.885,0,0,0,42.986-37.4a19.815,19.815,0,0,1,8.24-1.785,20.015,20.015,0,0,1,16.24,8.343,11.83,11.83,0,0,0,9.727,5,11.83,11.83,0,0,0,9.727-5,20.015,20.015,0,0,1,16.24-8.344A19.815,19.815,0,0,1,111.4-37.4a11.885,11.885,0,0,0,4.938,1.082,11.932,11.932,0,0,0,11.91-10.811,20.008,20.008,0,0,1,17.92-17.92,11.831,11.831,0,0,0,9.193-5.925,11.831,11.831,0,0,0,.534-10.924,20.012,20.012,0,0,1,6.559-24.479,11.832,11.832,0,0,0,5-9.728,11.832,11.832,0,0,0-5-9.728,20.012,20.012,0,0,1-6.559-24.479,11.832,11.832,0,0,0-.534-10.925,11.831,11.831,0,0,0-9.193-5.924,20.008,20.008,0,0,1-17.92-17.92,11.931,11.931,0,0,0-11.91-10.81A11.887,11.887,0,0,0,111.4-194.8,19.815,19.815,0,0,1,103.159-193.018Z"
                                        transform="translate(21.068 214.361)" fill="#008e37"></path>
                                    <path id="Path_69" data-name="Path 69"
                                        d="M68.018-58.7A66.653,66.653,0,0,1,1.441-125.275a66.652,66.652,0,0,1,66.577-66.576,66.551,66.551,0,0,1,55.2,29.349,4,4,0,0,1-1.075,5.554,4,4,0,0,1-5.554-1.075,58.561,58.561,0,0,0-48.575-25.828A58.643,58.643,0,0,0,9.441-125.275,58.643,58.643,0,0,0,68.018-66.7a58.813,58.813,0,0,0,57.313-46.417,4,4,0,0,1,4.74-3.088,4,4,0,0,1,3.088,4.74A66.85,66.85,0,0,1,68.018-58.7Z"
                                        transform="translate(30.243 223.536)" fill="#008e37"></path>
                                    <path id="Path_70" data-name="Path 70"
                                        d="M94.564-136.636a4,4,0,0,1-4-4,59.351,59.351,0,0,0-.675-8.926,4,4,0,0,1,3.351-4.558,4,4,0,0,1,4.558,3.351,67.377,67.377,0,0,1,.766,10.133A4,4,0,0,1,94.564-136.636Z"
                                        transform="translate(66.274 238.896)" fill="#008e37"></path>
                                    <path id="Path_71" data-name="Path 71"
                                        d="M58.3-100.616a4.02,4.02,0,0,1-2.828-1.172l-27.74-27.74a4,4,0,0,1,0-5.657,4,4,0,0,1,5.657,0L58.3-110.273l87.273-87.273a4,4,0,0,1,5.657,0,4,4,0,0,1,0,5.657l-90.1,90.1A4,4,0,0,1,58.3-100.616Z"
                                        transform="translate(40.482 220.737)" fill="#008e37"></path>
                                </g>
                            </svg>
                        </div>
                        <div class="col-md-9 d-flex flex-column justify-content-center">
                            <h1 class="text-white text-3xl"> Welcome, you're in! </h1>
                            <p class="text-white fs-5 fw-light mt-2"> You just joined a dedicated community of Quran and
                                Arabic learners. Check your e-mail to take your first step. </p>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div class="container-fluid bg-gray">
            <div class="container">
                <div class="row mx-auto d-flex justify-content-center p-4">
                    <h1 class="text-center fw-normal">What to Expect Next</h1>
                    <div class="features">
                        <div class="feature">
                            <svg xmlns="http://www.w3.org/2000/svg" width="198" height="198" viewBox="0 0 198 198">
                                <g id="Group_413" data-name="Group 413" transform="translate(-528 -866)">
                                    <circle id="Ellipse_57" data-name="Ellipse 57" cx="99" cy="99" r="99"
                                        transform="translate(528 866)" fill="#e5deff"></circle>
                                    <g id="Group_128" data-name="Group 128" transform="translate(579.76 887.25)">
                                        <rect id="Rectangle_166" data-name="Rectangle 166" width="90.979"
                                            height="56.369" rx="9.616" transform="translate(1.75 59.875)"
                                            fill="#5e3fd7"></rect>
                                        <path id="Rectangle_166-2" data-name="Rectangle 166"
                                            d="M0,10a1.75,1.75,0,0,1-1.75-1.75V0A1.75,1.75,0,0,1,0-1.75,1.75,1.75,0,0,1,1.75,0V8.253A1.75,1.75,0,0,1,0,10Z"
                                            transform="translate(92.73 90.707)" fill="#030060"></path>
                                        <path id="Rectangle_166-3" data-name="Rectangle 166"
                                            d="M716.4,775.858H644.648a11.366,11.366,0,0,1-11.366-11.366V727.355a11.366,11.366,0,0,1,11.366-11.366H716.4a11.366,11.366,0,0,1,11.366,11.366v8.928a1.75,1.75,0,0,1-3.5,0v-8.928a7.875,7.875,0,0,0-7.866-7.866H644.648a7.875,7.875,0,0,0-7.866,7.866v37.137a7.875,7.875,0,0,0,7.866,7.866H716.4a7.875,7.875,0,0,0,7.866-7.866,1.75,1.75,0,0,1,3.5,0A11.379,11.379,0,0,1,716.4,775.858Z"
                                            transform="translate(-633.282 -657.864)" fill="#030060"></path>
                                        <path id="Path_104_-_Outline" data-name="Path 104 - Outline"
                                            d="M680.521,747.674a1.75,1.75,0,0,1-.683-.139l-45.489-19.282a1.75,1.75,0,0,1,1.366-3.222l44.806,18.992,44.807-18.992a1.75,1.75,0,1,1,1.366,3.222L681.2,747.535A1.749,1.749,0,0,1,680.521,747.674Z"
                                            transform="translate(-633.282 -657.864)" fill="#030060"></path>
                                        <g id="Group_127" data-name="Group 127"
                                            transform="translate(-633.282 -657.864)">
                                            <path id="Line_20" data-name="Line 20"
                                                d="M0,26.212a1.75,1.75,0,0,1-1.75-1.75V0A1.75,1.75,0,0,1,0-1.75,1.75,1.75,0,0,1,1.75,0V24.462A1.75,1.75,0,0,1,0,26.212Z"
                                                transform="translate(692.321 684.076)" fill="#030060"></path>
                                            <path id="Line_21" data-name="Line 21"
                                                d="M0,50.675a1.75,1.75,0,0,1-1.75-1.75V0A1.75,1.75,0,0,1,0-1.75,1.75,1.75,0,0,1,1.75,0V48.925A1.75,1.75,0,0,1,0,50.675Z"
                                                transform="translate(680.521 659.614)" fill="#030060"></path>
                                            <path id="Line_22" data-name="Line 22"
                                                d="M0,42.426a1.75,1.75,0,0,1-1.75-1.75V0A1.75,1.75,0,0,1,0-1.75,1.75,1.75,0,0,1,1.75,0V40.676A1.75,1.75,0,0,1,0,42.426Z"
                                                transform="translate(668.722 667.863)" fill="#030060"></path>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                            <h1 class="feature-text">Receive An Email</h1>
                        </div>
                        <div class="feature">
                            <svg xmlns="http://www.w3.org/2000/svg" width="198" height="198" viewBox="0 0 198 198">
                                <g id="Group_414" data-name="Group 414" transform="translate(-854 -750)">
                                    <circle id="Ellipse_55" data-name="Ellipse 55" cx="99" cy="99" r="99"
                                        transform="translate(854 750)" fill="#e5deff"></circle>
                                    <g id="Group_132" data-name="Group 132" transform="translate(885.389 796.25)">
                                        <path id="Path_105" data-name="Path 105"
                                            d="M711.643,850.271V827.088a12.49,12.49,0,0,0-12.49-12.49H657.519a12.49,12.49,0,0,0-12.49,12.49v41.634a12.491,12.491,0,0,0,12.49,12.49h41.634a12.49,12.49,0,0,0,12.49-12.49h18.451Z"
                                            transform="translate(-596.619 -812.848)" fill="#030060"></path>
                                        <path id="Path_105_-_Outline" data-name="Path 105 - Outline"
                                            d="M699.153,882.962H657.519a14.241,14.241,0,0,1-14.24-14.24V827.088a14.24,14.24,0,0,1,14.24-14.24h41.634a14.24,14.24,0,0,1,14.24,14.24v22.458l17.938,17.938a1.75,1.75,0,0,1-1.237,2.987H713.287a14.245,14.245,0,0,1-14.134,12.49Zm-41.634-66.614a10.74,10.74,0,0,0-10.74,10.74v41.634a10.741,10.741,0,0,0,10.74,10.74h41.634a10.74,10.74,0,0,0,10.74-10.74,1.75,1.75,0,0,1,1.75-1.75h14.226l-15.464-15.464a1.75,1.75,0,0,1-.513-1.237V827.088a10.74,10.74,0,0,0-10.74-10.74Z"
                                            transform="translate(-596.619 -812.848)" fill="#030060"></path>
                                        <path id="Path_106" data-name="Path 106"
                                            d="M650.874,911.1H629.309a12.491,12.491,0,0,1-12.49-12.49h-18.45l18.45-18.451V856.974a12.49,12.49,0,0,1,12.49-12.49h41.635a12.49,12.49,0,0,1,12.49,12.49v41.634a12.49,12.49,0,0,1-12.49,12.49"
                                            transform="translate(-596.619 -812.848)" fill="#5e3fd7"></path>
                                        <path id="Path_106_-_Outline" data-name="Path 106 - Outline"
                                            d="M670.944,912.848a1.75,1.75,0,0,1,0-3.5,10.752,10.752,0,0,0,10.74-10.74V856.974a10.74,10.74,0,0,0-10.74-10.74H629.309a10.74,10.74,0,0,0-10.74,10.74v23.183a1.75,1.75,0,0,1-.513,1.237l-15.463,15.464h14.225a1.75,1.75,0,0,1,1.75,1.75,10.741,10.741,0,0,0,10.74,10.74h21.565a1.75,1.75,0,0,1,0,3.5H629.309a14.245,14.245,0,0,1-14.134-12.49H598.369a1.75,1.75,0,0,1-1.237-2.987l17.937-17.938V856.974a14.24,14.24,0,0,1,14.24-14.24h41.635a14.24,14.24,0,0,1,14.24,14.24v41.634a14.24,14.24,0,0,1-14.24,14.24Z"
                                            transform="translate(-596.619 -812.848)" fill="#030060"></path>
                                    </g>
                                </g>
                            </svg>
                            <h1 class="feature-text">Attend Trial Lesson</h1>
                        </div>
                        <div class="feature">
                            <svg xmlns="http://www.w3.org/2000/svg" width="198" height="198" viewBox="0 0 198 198">
                                <g id="Group_415" data-name="Group 415" transform="translate(-40 -866)">
                                    <circle id="Ellipse_56" data-name="Ellipse 56" cx="99" cy="99" r="99"
                                        transform="translate(40 866)" fill="#e5deff"></circle>
                                    <g id="Group_134" data-name="Group 134" transform="translate(97.828 905.871)">
                                        <g id="Group_134-2" data-name="Group 134"
                                            transform="translate(2590.218 -13.122)">
                                            <path id="Rectangle_168" data-name="Rectangle 168"
                                                d="M-2509.625,107.957v3.6a8.069,8.069,0,0,1-8.069,8.069h-62.7a8.068,8.068,0,0,1-8.069-8.069V38.111a8.069,8.069,0,0,1,8.069-8.069h62.7a8.07,8.07,0,0,1,8.069,8.069v16.9"
                                                fill="#5e3fd7"></path>
                                            <path id="Rectangle_168_-_Outline" data-name="Rectangle 168 - Outline"
                                                d="M-2517.694,121.38h-62.7a9.83,9.83,0,0,1-9.819-9.819V38.111a9.83,9.83,0,0,1,9.819-9.819h62.7a9.83,9.83,0,0,1,9.819,9.819v16.9a1.75,1.75,0,0,1-1.75,1.75,1.75,1.75,0,0,1-1.75-1.75v-16.9a6.326,6.326,0,0,0-6.319-6.319h-62.7a6.326,6.326,0,0,0-6.319,6.319v73.45a6.326,6.326,0,0,0,6.319,6.319h62.7a6.326,6.326,0,0,0,6.319-6.319v-3.6a1.75,1.75,0,0,1,1.75-1.75,1.75,1.75,0,0,1,1.75,1.75v3.6A9.83,9.83,0,0,1-2517.694,121.38Z"
                                                fill="#030060"></path>
                                            <path id="Rectangle_168-2" data-name="Rectangle 168"
                                                d="M0,28.115a1.75,1.75,0,0,1-1.75-1.75V0A1.75,1.75,0,0,1,0-1.75,1.75,1.75,0,0,1,1.75,0V26.365A1.75,1.75,0,0,1,0,28.115Z"
                                                transform="translate(-2509.625 64.886)" fill="#030060"></path>
                                            <g id="Group_133" data-name="Group 133">
                                                <path id="Line_23" data-name="Line 23"
                                                    d="M0,34.657a1.75,1.75,0,0,1-1.75-1.75V0A1.75,1.75,0,0,1,0-1.75,1.75,1.75,0,0,1,1.75,0V32.907A1.75,1.75,0,0,1,0,34.657Z"
                                                    transform="translate(-2564.163 14.872)" fill="#030060"></path>
                                                <path id="Line_24" data-name="Line 24"
                                                    d="M0,34.657a1.75,1.75,0,0,1-1.75-1.75V0A1.75,1.75,0,0,1,0-1.75,1.75,1.75,0,0,1,1.75,0V32.907A1.75,1.75,0,0,1,0,34.657Z"
                                                    transform="translate(-2533.931 14.872)" fill="#030060"></path>
                                            </g>
                                            <rect id="Rectangle_169" data-name="Rectangle 169" width="16.025"
                                                height="14.487" transform="translate(-2557.06 76.763)" fill="#030060">
                                            </rect>
                                            <rect id="Rectangle_170" data-name="Rectangle 170" width="16.025"
                                                height="14.486" transform="translate(-2557.059 95.883)" fill="#030060">
                                            </rect>
                                            <rect id="Rectangle_171" data-name="Rectangle 171" width="16.025"
                                                height="14.487" transform="translate(-2577.718 76.763)" fill="#030060">
                                            </rect>
                                            <rect id="Rectangle_172" data-name="Rectangle 172" width="16.025"
                                                height="14.487" transform="translate(-2557.06 57.642)" fill="#030060">
                                            </rect>
                                            <rect id="Rectangle_173" data-name="Rectangle 173" width="16.025"
                                                height="14.487" transform="translate(-2536.402 76.763)" fill="#030060">
                                            </rect>
                                            <rect id="Rectangle_174" data-name="Rectangle 174" width="16.025"
                                                height="14.487" transform="translate(-2536.402 57.642)" fill="#030060">
                                            </rect>
                                            <rect id="Rectangle_175" data-name="Rectangle 175" width="16.025"
                                                height="14.487" transform="translate(-2577.718 57.642)" fill="#030060">
                                            </rect>
                                            <rect id="Rectangle_176" data-name="Rectangle 176" width="16.025"
                                                height="14.486" transform="translate(-2577.718 95.883)" fill="#030060">
                                            </rect>
                                            <rect id="Rectangle_177" data-name="Rectangle 177" width="16.025"
                                                height="14.486" transform="translate(-2536.402 95.883)" fill="#030060">
                                            </rect>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                            <h1 class="feature-text">Receive Your Class Schedule</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid bg-white h-30 d-flex align-items-center">
            <div class="container my-4">
                <div class="row mx-auto d-flex justify-content-center p-4">
                    <h1 class="text-center fw-normal">What CEF Online Achieved</h1>
                </div>
                <div class="achievements">
                    <div class="achievement">
                        <div class="w-100 h-10">
                            <img src="teachers.png" alt="">
                        </div>
                        <h4 class="my-1">300+</h4>
                        <p class="fw-normal">Teachere expert</p>
                    </div>
                    <div class="achievement">
                        <div class="w-100 h-10">
                            <img src="enroll.png" alt="">
                        </div>
                        <h4 class="my-1">2500+</h4>
                        <p class="fw-normal">Students enrolled</p>
                    </div>
                    <div class="achievement">
                        <div class="w-100 h-10">
                            <img src="Stopwatch.png" alt="">
                        </div>
                        <h4 class="my-1">1000000+</h4>
                        <p class="fw-normal">Hours taught</p>
                    </div>
                    <div class="achievement">
                        <div class="w-100 h-10">
                            <img src="Map.png" alt="">
                        </div>
                        <h4 class="my-1">46+</h4>
                        <p class="fw-normal">Countries</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid bg-white">
            <div class="container">
                <div class="row mx-auto d-flex justify-content-center p-4">
                    <h1 class="text-center fw-normal">You ask, we answer</h1>
                    <p class="text-center fw-normal secondary my-2">Here are responses to some frequently-asked
                        questions</p>
                </div>
                <div class="accordion" id="accordionExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne"> When did
                                you begin your business? </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body"> CEF Online was founded in 2017 by three fathers, an MIT research
                                scientist, a Tufts University educator, and Al-Azhar University Sheikh. With each
                                founder bringing a unique skillset, they saw a need to bring proper Quran, Tajweed and
                                Islamic Studies to families living in the West. All founders are now Board Members and
                                work to secure the legacy of CEF Online for generations to come. </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo"> What
                                makes CEF Online different? </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body"> There are many online programs for Quran and Islamic Education,
                                but none match the resume of CEF Online. We hold the most prestigious accreditation of Quranic
                                recitation. Sh. Masarawi, scholar and founder of the largest, and most accomplished
                                Quran Institute awarded us the official seal of approval. </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">How
                                much experience do you have? </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body"> We have taught over 1 million hours of Quran, Islamic Studies
                                and Arabic. With over 3,000 active students in 46 countries, 300 teachers and 35 staff
                                members, CEF Online is run more like a Fortune 500 company than a local Sunday school, but
                                with the personal touch of a family member. Feel confident knowing we attract the best
                                in the industry and don’t stop innovating to stay ahead. </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">Where
                                are you located and do you have service in my area? </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body"> Our headquarters is in Massachusetts, USA, and we have a
                                satellite branch in Cairo, Egypt. Our staff and teachers are located all over the world.
                                The school is online only, and so, we are able to offer lessons to anyone, anywhere in
                                the world. All you need is a computer, phone, or tablet and an internet connection.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-25 mx-auto my-2">
                    <button type="submit" class="theme-btn theme-button1 theme-button3 font-15 fw-bold w-100">TRY FOR
                        FREE</button>
                </div>
            </div>
        </div>
        <div class="container-fluid bg-white subscription bg-thanks h-30 d-flex align-items-center">
            <div class="container my-2">
                <div class="row mx-auto d-flex justify-content-center p-4">
                    <h1 class="text-center fw-normal my-2">Get our best advice, every 2 weeks</h1>
                    <h5 class="text-center fw-normal secondary my-2 text-gray w-80">Every other week we send out our
                        best advice from our blog . Subscribe below or connect with us on Facebook , Instagram , YouTube
                        and LinkedIn</h5>
                    <div class="w-30 mx-auto my-3">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Your Email Address"
                                aria-label="Recipient's username" aria-describedby="button-addon2">
                            <button class="theme-btn theme-button1 theme-button3 font-15 fw-bold" type="button"
                                id="button-addon2">Button</button>
                        </div>
                        <p class="text-gray text-center">Thanks for subscribing us!</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Main Content End-->
        <!-- Footer Start --> @include(getThemePath().'.layouts.footer')
        <!-- Footer End -->
        <!-- PWA Install Button Start -->
        <button class="d-none pwa-install-btn bg-white position-fixed radius-4" id="installApp">
            <span class="iconify" data-icon="logos:pwa"></span>
        </button>
        <!-- PWA Install Button End -->
        <script>
            var deleteTitle = '{{ __("Sure! You want to delete?") }}';
            var deleteText = '{{ __("You wont be able to revert this!") }}';
            var deleteConfirmButton = '{{ __("Yes, Delete It!") }}';
            var deleteSuccessText = '{{ __("Item has been deleted") }}';
        </script>
        <!--======================================= All Jquery Script link ===========================================-->
        <!-- Bootstrap core JavaScript -->
        <script src="{{ asset('frontend/assets/vendor/jquery/jquery-3.6.0.min.js') }}"></script>
        <script src="{{ asset('frontend/assets/vendor/jquery/popper.min.js') }}"></script>
        <script src="{{ asset('frontend/assets/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
        <!-- ==== Plugin JavaScript ==== -->
        <script src="{{ asset('frontend/assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
        <script src="{{ asset('frontend/assets/js/jquery-ui.min.js') }}"></script>
        <!--WayPoints JS Script-->
        <script src="{{ asset('frontend/assets/js/waypoints.min.js') }}"></script>
        <!--Counter Up JS Script-->
        <script src="{{ asset('frontend/assets/js/jquery.counterup.min.js') }}"></script>
        <script src="{{ asset('frontend/assets/js/owl.carousel.min.js') }}"></script>
        <!-- Range Slider -->
        <script src="{{ asset('frontend/assets/js/price_range_script.js') }}"></script>
        <!--Feather Icon-->
        <script src="{{ asset('frontend/assets/js/feather.min.js') }}"></script>
        <!--Iconify Icon-->
        <script src="{{ asset('common/js/iconify.min.js') }}"></script>
        <!--Venobox-->
        <script src="{{ asset('frontend/assets/js/venobox.min.js') }}"></script>
        <!-- Menu js -->
        <script src="{{ asset('frontend/assets/js/menu.js') }}"></script>
        <!-- Custom scripts for this template -->
        <script src="{{ asset('frontend/assets/js/frontend-custom.js') }}"></script>
        <script src="{{asset('admin/sweetalert2/sweetalert2.all.js')}}"></script>
        <input type="hidden" id="base_url" value="{{url('/')}}">
        <!-- Start:: Navbar Search  -->
        <input type="hidden" class="search_route" value="{{ route('search-course.list') }}">
        <script src="{{ asset('frontend/assets/js/custom/search-course.js') }}"></script>
        <!-- End:: Navbar Search  -->
        <script>
            function getLanguage(){
        return {
            "sEmptyTable": "{{ __('No data available in table') }}",
            "sInfo": "{{__('Showing _START_ To _END_ Of _TOTAL_ Entries')}}",
            "sInfoEmpty": "{{__('Showing 0 to 0 of 0 entries')}}",
            "sInfoFiltered": "{{__('(filtered from _MAX_ total entries)')}}",
            "sInfoPostFix": "",
            "sInfoThousands": ",",
            "sLengthMenu": "{{__('Show _MENU_ entries')}}",
            "sLoadingRecords": "{{__('Loading...')}}",
            "sProcessing": "{{__('Processing...')}}",
            "sSearch": "{{__('Search:')}}",
            "sZeroRecords": "{{__('No matching records found')}}",
            "oPaginate": {
                "sFirst": "{{__('First')}}",
                "sLast": "{{__('Last')}}",
                "sNext": "{{__('Next')}}",
                "sPrevious": "{{__('Previous')}}"
            },
            "oAria": {
                "sSortAscending": ": {{__('activate to sort column ascending')}}",
                "sSortDescending": ": {{__('activate to sort column descending')}}"
            }
        };
    }
        </script>
        <!-- DataTables  & Plugins -->
        <script src="{{asset('frontend/assets/vendor/datatable/datatables/jquery.dataTables.min.js')}}"></script>
        <script src="{{asset('frontend/assets/vendor/datatable/datatables-bs4/js/dataTables.bootstrap4.min.js')}}">
        </script>
        <script
            src="{{asset('frontend/assets/vendor/datatable/datatables-responsive/js/dataTables.responsive.min.js')}}">
        </script>
        <script
            src="{{asset('frontend/assets/vendor/datatable/datatables-responsive/js/responsive.bootstrap4.min.js')}}">
        </script>
        <script src="{{asset('frontend/assets/vendor/datatable/datatables-buttons/js/dataTables.buttons.min.js')}}">
        </script>
        <script src="{{asset('frontend/assets/vendor/datatable/datatables-buttons/js/buttons.bootstrap4.min.js')}}">
        </script>
        <script src="{{asset('frontend/assets/vendor/datatable/jszip/jszip.min.js')}}"></script>
        <script src="{{asset('frontend/assets/vendor/datatable/pdfmake/pdfmake.min.js')}}"></script>
        <script src="{{asset('frontend/assets/vendor/datatable/pdfmake/vfs_fonts.js')}}"></script>
        <script src="{{asset('frontend/assets/vendor/datatable/datatables-buttons/js/buttons.html5.min.js')}}"></script>
        <script src="{{asset('frontend/assets/vendor/datatable/datatables-buttons/js/buttons.print.min.js')}}"></script>
        <script src="{{asset('frontend/assets/vendor/datatable/datatables-buttons/js/buttons.colVis.min.js')}}">
        </script> @if(isEnableOpenAI()) <script src="{{asset('addon/AI/js/main.js')}}"></script> @endif @stack('script')
        @toastr_js @toastr_render @if (@$errors->any()) <script>
            "use strict";
        @foreach ($errors->all() as $error)
        toastr.options.positionClass = 'toast-bottom-right';
        toastr.error("{{ $error }}")
        @endforeach
        </script> @endif @if(get_option('pwa_enable')) <script src="{{ asset('sw.js') }}"></script>
        <script>
            if (!navigator.serviceWorker.controller) {
        navigator.serviceWorker.register("/sw.js").then(function (reg) {
            console.log("Service worker has been registered for scope: " + reg.scope);
        });
    }

    let deferredPrompt;
    window.addEventListener('beforeinstallprompt', (e) => {
        $('#installApp').removeClass('d-none');
        deferredPrompt = e;
    });

    const installApp = document.getElementById('installApp');
    installApp.addEventListener('click', async () => {
        if (deferredPrompt !== null) {
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            if (outcome === 'accepted') {
                deferredPrompt = null;
            }
        }
    });




        </script> @endif
    </body>

</html>
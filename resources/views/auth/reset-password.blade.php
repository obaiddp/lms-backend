@extends('frontend.layouts.app')

@section('meta')
    @php
        $metaData = getMeta('forgot');
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

@endsection

@section('content')

<div class="loginPage" >
    <!-- Page Header Start -->
    <header class="breadcrumb-section px-3 xl:px-0">
        <nav aria-label="breadcrumb" class="home-page-container">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" class="aileron">
                        <i class="fas fa-home"></i> <!-- Home icon -->
                        Home
                    </a>
                </li>
                <li class="breadcrumb-separator">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4.5 2.25L8.25 6L4.5 9.75" stroke="#77878F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </li> <!-- Custom separator -->
                <li class="breadcrumb-item active aileron" aria-current="page" class="">Reset Password</li>
            </ol>
        </nav>
    </header>
    <section class="bg-white">
    <div class="home-page-container d-flex justify-content-center align-items-center">
        <div class="row bg-white justify-content-start align-items-center mt-4 px-3 xl:px-0" style="width: 100%;">
          <div class="col-md-6 p-0 ps-2.5">
            <div class="mb-3">
                <h1 class="fs-5 text-black font-bold aileron">{{ __('Reset Password') }}</h1>
                 <p style="font-size: 14px" class="aileron grayNew">{{ __('Verification code sent your email. Please check.') }}</p>
            </div>
      
            <form method="POST" action="{{ route('reset-password') }}">
                @csrf
                <div class="mb-3">
                    <label class="mb-1 text-sm text-black font-bold aileron">{{ __('Verification Code') }}</label>
                    <input type="number" name="verification_code" class="form-control aileron" id="email" 
                        placeholder="{{ __('Type your verification code') }}" required">
                    @if ($errors->has('verification_code'))
                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('verification_code') }}</span>
                    @endif
                </div>
                <div class="mb-3">
                    <label class="mb-1 text-sm text-black font-bold aileron">{{ __('New Password') }}</label>
                    <div class="position-relative">
                        <input class="form-control password aileron" name="password" placeholder="*********" type="password" required>
                        <span class="toggle cursor fas fa-eye pass-icon"></span>
                    </div>
                    @if ($errors->has('password'))
                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('password') }}</span>
                    @endif
                </div>
                <div class="mb-3">
                    <label class="mb-1 text-sm text-black font-bold aileron">{{ __('Confirm Password') }}</label>
                    <div class="form-group mb-0 position-relative">
                        <input class="form-control password aileron" name="password_confirmation" placeholder="*********" type="password" required>
                        <span class="toggle cursor fas fa-eye pass-icon"></span>
                    </div>
                    @if ($errors->has('password_confirmation'))
                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('password_confirmation') }}</span>
                    @endif
                </div>
                <button type="submit" class="theme-btn aileron theme-button1 theme-button3 w-100 font-14 !rounded !fw-light">{{ __('Change Password') }}</button>
                <a href="{{ route('login') }}" class="aileron text-decoration-underline d-block text-center mb-3">{{ __('Back to Login?') }}</a>
            </form>
          </div>
          <div class="col-md-6 rounded-4 p-0 md:!ps-5 pe-2.5">
                <div class="swiper mySwiper rounded-4">
                    <div class="swiper-wrapper rounded-4">
                    <div class="swiper-slide">
                        <img src="Images/signin-1.png" class="rounded-4 w-100 h-90"/>
                    </div>
                    <div class="swiper-slide">
                        <img src="Images/signin-2.png" class="rounded-4 w-100 h-90" />
                    </div>
                    <div class="swiper-slide">
                        <img src="Images/signin-3.png" class="rounded-4 w-100 h-90" />
                    </div>
                    <div class="swiper-slide">
                        <img src="Images/signin-4.png" class="rounded-4 w-100 h-90" />
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
<style>
    .loginPage {
        background: #f8f9fa;
        padding-top: 70px !important;
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
        margin: 0 8px; /* Adjust spacing as needed */
        color: #6c757d; /* Adjust color as needed */
    }

    .breadcrumb-item.active {
        color: #6c757d; /* Adjust color for the active item */
    }

    .fas.fa-home {
        margin-right: 5px; /* Space between icon and text */
    }
    .btn-group .btn {
        border: 1px solid #d3d3d3; /* Light gray border */
        padding: 10px 0;
    }

    .btn-group .btn:first-child {
        border-right: none; /* Remove the border between the two buttons */
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
    .swiper.mySwiper.rounded-4.swiper-initialized.swiper-horizontal.swiper-backface-hidden {
    height: 35rem;
}



@media (min-width: 300px) and (max-width: 640px){
    .loginPage {
    padding-top: 4.4rem !important;
    }
    .swiper.mySwiper.rounded-4.swiper-initialized.swiper-horizontal.swiper-backface-hidden{
        height: 20rem;
        margin-top: 4rem;
    }
}

@media (min-width: 531px) and (max-width: 767px){
    .loginPage {
        padding-top: 5.4rem !important;
    }
    .swiper.mySwiper.rounded-4.swiper-initialized.swiper-horizontal.swiper-backface-hidden{
        height: 34rem;
        margin-top: 4rem;
    }
}

@media (min-width: 768px) and (max-width: 992px){
    .swiper.mySwiper.rounded-4.swiper-initialized.swiper-horizontal.swiper-backface-hidden {
        height: 22rem;
    }
}

@media (min-width: 993px) and (max-width: 1199px){
    .swiper.mySwiper.rounded-4.swiper-initialized.swiper-horizontal.swiper-backface-hidden {
        height: 28rem;
    }
}
</style>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
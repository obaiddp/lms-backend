@extends('frontend.layouts.app')

@section('meta')
    @php
        $metaData = getMeta('login');
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
<div class="loginPage @if(isset($fromAdmin) && $fromAdmin) loginPage--admin @endif">
    @if(!isset($fromAdmin) || !$fromAdmin)
    <!-- Page Header Start -->
    <header class="breadcrumb-section px-3 xl:px-0">
        <div class="home-page-container d-flex justify-content-between align-items-center">
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
                    </li> 
                    <!-- Custom separator -->
                    <li class="breadcrumb-item active aileron" aria-current="page">Login</li>
                </ol>
             </nav>
            @guest
                @if ($type == null)
                    <a href="{{ route('sign-up-as-student') }}" class="btn btn-sm btn-login aileron d-flex align-items-center">
                        <i class="fas fa-sign-in-alt me-2"></i> Don,t have an account? Sign Up
                    </a>
                @else
                    <a href="{{ route('sign-up-as-student',['type'=> $type, 'slug'=> $slug]) }}" class="btn btn-sm btn-login aileron d-flex align-items-center">
                        <i class="fas fa-sign-in-alt me-2"></i> Don,t have an account? Sign Up
                    </a>
                @endif
             @endguest
        </div>
    </header>
    <!-- Page Header End -->
    @endif
    <section class="bg-white">
        <div class="home-page-container d-flex justify-content-center align-items-center">
        <div class="row bg-white justify-content-start align-items-center mt-4 px-3 xl:px-0" style="width: 100%;">
            <div class="col-md-6 p-0 ps-2.5">
                <div class="mb-1">
                    <h1 class="fs-5 text-black font-bold aileron">Welcome to CEF</h1>
                    <p style="font-size: 14px" class="aileron grayNew">Please enter your credentials</p>
                </div>

                @if ($type == null)
                    {{-- <div class="btn-group w-100 mt-2 mb-4" role="group">
                        <a href="{{ route('login') }}" class="btn w-50 aileron my-tab {{ Request::is('login') ? 'active-tab' : 'inactive-tab' }}">
                            Sign In
                        </a>
                        <a href="{{ route('sign-up-as-student') }}" class="btn w-50 aileron my-tab {{ Request::is('sign-up-as-student') ? 'active-tab' : 'inactive-tab' }}">
                            Sign Up
                        </a>
                    </div> --}}
                @endif
                <br>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <input type="hidden" name="fromAdmin" id="fromAdmin" value={{$fromAdmin}}>
                 
                     <input type="hidden" name="slug_updated" id="slug_updated" value={{$slug}}>
                       <input type="hidden" name="st_type_updated" id="st_type_updated" value={{$type}}>
                    <div class="mb-3">
                        <label for="email" class="mb-1 text-sm text-black font-bold aileron">Email or Phone</label>
                        <input type="text" name="email" class="form-control aileron" id="email" 
                            placeholder="Enter your email or phone" required value="{{ old('email') }}">
                        @if ($errors->has('email'))
                            <span class="text-danger aileron"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('email') }}</span>
                        @endif
                    </div>
                    

                    <div class="mb-3">
                        <label for="password" class="mb-1 text-sm text-black font-bold aileron">Password</label>
                        <div class="position-relative">
                            <input type="password" name="password" class="form-control aileron pe-5 password" id="password" placeholder="*********" required>
                            <i class="position-absolute end-0 top-50 translate-middle-y me-3" id="togglePassword" style="cursor: pointer;">
                                <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_800_1128)">
                                <path d="M15.8667 8.15202C15.78 7.91936 13.6667 2.46002 8 2.46002C2.33333 2.46002 0.217333 7.91936 0.133333 8.15202L0 8.50002L0.133333 8.84802C0.22 9.08069 2.33333 14.54 8 14.54C13.6667 14.54 15.7827 9.08069 15.8667 8.84802L16 8.50002L15.8667 8.15202ZM8 12.5567C4.388 12.5567 2.63933 9.54736 2.14 8.50002C2.64067 7.45002 4.39 4.44336 8 4.44336C11.61 4.44336 13.3587 7.44936 13.86 8.50002C13.3587 9.55069 11.61 12.5567 8 12.5567Z" fill="#013370"/>
                                <path d="M7.99967 11.1667C9.47243 11.1667 10.6663 9.9728 10.6663 8.50004C10.6663 7.02728 9.47243 5.83337 7.99967 5.83337C6.52692 5.83337 5.33301 7.02728 5.33301 8.50004C5.33301 9.9728 6.52692 11.1667 7.99967 11.1667Z" fill="#013370"/>
                                </g>
                                <defs>
                                <clipPath id="clip0_800_1128">
                                <rect width="16" height="16" fill="white" transform="translate(0 0.5)"/>
                                </clipPath>
                                </defs>
                                </svg>
                            </i>
                        </div>
                        @if ($errors->has('password'))
                            <span class="text-danger aileron"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('password') }}</span>
                        @endif
                    </div>
                    

                    <button type="submit" class="theme-btn aileron theme-button1 theme-button3 w-100 font-14 !rounded !fw-light">Login</button>
                    <a href="{{ route('forget-password') }}" class="aileron text-decoration-underline d-block text-center mb-3">Forgot Password</a>
                </form>
                
                {{-- <div class="divider d-flex align-items-center my-1">
                    <div class="divider-line flex-grow-1" style="border-top: 1px solid #D9D9D9;"></div>
                    <p class="text-center mx-3 mb-0 aileron grayNew" style="font-size: 0.8rem !important">Or continue with</p>
                    <div class="divider-line flex-grow-1" style="border-top: 1px solid #D9D9D9;"></div>
                </div> --}}
                <div class="d-flex justify-content-center gap-3">
                            {{-- <a href="{{ route('google_login') }}" class="btn btn-outline-light">
                                <svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="0.5" y="0.5" width="55" height="55" rx="27.5" fill="white"/>
                                    <rect x="0.5" y="0.5" width="55" height="55" rx="27.5" stroke="#D9D9D9"/>
                                    <g clip-path="url(#clip0_800_1056)">
                                    <path d="M21.3189 30.5035L20.4835 33.6222L17.4301 33.6868C16.5176 31.9943 16 30.0578 16 28C16 26.0101 16.4839 24.1336 17.3418 22.4813H17.3424L20.0608 22.9797L21.2516 25.6818C21.0024 26.4084 20.8665 27.1884 20.8665 28C20.8666 28.8809 21.0262 29.7249 21.3189 30.5035Z" fill="#FBBB00"/>
                                    <path d="M39.7902 25.7583C39.928 26.4842 39.9999 27.2339 39.9999 28.0001C39.9999 28.8592 39.9095 29.6972 39.7375 30.5056C39.1533 33.2563 37.6269 35.6583 35.5124 37.3581L35.5118 37.3574L32.0878 37.1827L31.6032 34.1576C33.0063 33.3348 34.1028 32.0471 34.6804 30.5056H28.2637V25.7583H39.7902Z" fill="#518EF8"/>
                                    <path d="M35.5114 37.3574L35.5121 37.358C33.4556 39.011 30.8433 40 27.9996 40C23.4297 40 19.4565 37.4457 17.4297 33.6868L21.3185 30.5035C22.3319 33.2081 24.9409 35.1334 27.9996 35.1334C29.3143 35.1334 30.546 34.778 31.6029 34.1576L35.5114 37.3574Z" fill="#28B446"/>
                                    <path d="M35.6596 18.7626L31.7721 21.9453C30.6783 21.2615 29.3853 20.8666 28 20.8666C24.8721 20.8666 22.2143 22.8802 21.2517 25.6818L17.3425 22.4813H17.3418C19.339 18.6308 23.3622 16 28 16C30.9117 16 33.5814 17.0372 35.6596 18.7626Z" fill="#F14336"/>
                                    </g>
                                    <defs>
                                    <clipPath id="clip0_800_1056">
                                    <rect width="24" height="24" fill="white" transform="translate(16 16)"/>
                                    </clipPath>
                                    </defs>
                                </svg>

                            </a>  --}}
                        
                            {{-- <a href="{{route('twitter_login')}}" class="btn btn-outline-light">
                                <svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="56" height="56" rx="28" fill="black"/>
                                    <path d="M32.619 16H32.7898C32.9268 17.6925 32.2807 18.9572 31.4956 19.873C30.7252 20.7825 29.6703 21.6646 27.9641 21.5308C27.8502 19.8625 28.4973 18.6916 29.2814 17.7779C30.0086 16.9264 31.3417 16.1686 32.619 16ZM37.7841 33.6167V33.6641C37.3046 35.1163 36.6206 36.361 35.7859 37.516C35.024 38.5646 34.0903 39.9758 32.423 39.9758C30.9824 39.9758 30.0254 39.0494 28.549 39.0241C26.9871 38.9988 26.1282 39.7987 24.7002 40H24.2133C23.1647 39.8482 22.3184 39.0178 21.7019 38.2695C19.884 36.0585 18.4791 33.2025 18.2178 29.5476V28.4737C18.3284 25.858 19.5994 23.7313 21.2888 22.7006C22.1804 22.1525 23.406 21.6857 24.7708 21.8943C25.3557 21.985 25.9532 22.1852 26.477 22.3833C26.9734 22.5741 27.5941 22.9124 28.1822 22.8945C28.5806 22.8829 28.9768 22.6753 29.3784 22.5288C30.5545 22.1041 31.7074 21.6172 33.2271 21.8459C35.0535 22.122 36.3498 22.9335 37.1507 24.1855C35.6057 25.1688 34.3843 26.6505 34.593 29.1809C34.7784 31.4794 36.1148 32.8241 37.7841 33.6167Z" fill="white"/>
                                </svg>
                            </a> --}}
                        
                            {{-- <a href="{{ route('facebook_login') }}" class="btn btn-outline-light">
                                <svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="56" height="56" rx="28" fill="#3D6AD6"/>
                                    <path d="M25.7047 29.2399C25.6293 29.2399 24.0484 29.2404 23.3193 29.2397C22.9439 29.2394 22.81 29.1045 22.81 28.7268C22.8094 27.7565 22.8094 26.7861 22.81 25.8158C22.8103 25.4429 22.9519 25.3005 23.3224 25.3003C24.0515 25.2998 25.6238 25.3001 25.7047 25.3001V23.1876C25.7051 22.2338 25.8753 21.3209 26.3599 20.4849C26.8559 19.6293 27.5779 19.0433 28.5031 18.7044C29.0956 18.4872 29.7107 18.4005 30.3395 18.4001C31.1263 18.3996 31.913 18.4003 32.6999 18.4017C33.0381 18.4022 33.1881 18.5517 33.1888 18.8923C33.1903 19.805 33.1903 20.7177 33.1888 21.6302C33.1883 21.9744 33.0446 22.1126 32.6987 22.1165C32.0539 22.1234 31.4085 22.1191 30.7643 22.145C30.1137 22.145 29.7715 22.4628 29.7715 23.1362C29.7559 23.8485 29.765 24.5616 29.765 25.2998C29.8259 25.2998 31.6775 25.2996 32.543 25.2998C32.9361 25.2998 33.0705 25.4349 33.0705 25.8302C33.0705 26.7953 33.0703 27.7605 33.0695 28.7256C33.0693 29.1151 32.9431 29.2394 32.5475 29.2397C31.6821 29.2401 29.8377 29.2399 29.7571 29.2399V37.0507C29.7571 37.4671 29.626 37.5998 29.2151 37.5998H26.2096C25.8465 37.5998 25.7049 37.4587 25.7049 37.0956L25.7047 29.2399Z" fill="white"/>
                                </svg>
                            </a> --}}
                </div>
                
            </div>
        
            <div class="col-md-6 rounded-4 p-0 md:!ps-5 pe-2.5">
                <div class="swiper mySwiper rounded-4">
                    <div class="swiper-wrapper rounded-4">
                    <div class="swiper-slide">
                        <img src="{{ asset('Images/signin-1.webp') }}" class="rounded-4 w-100 h-90"/>
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
<style>
    .loginPage {
        background: #f8f9fa;
        padding-top: 70px !important;
    }
    .loginPage--admin {
        padding-top: 1rem !important;
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

    .active-tab {
        background-color: white;    
        color: black;   
        box-shadow: 0 4px 8px #f0eff2;           
        border: 2px solid #f0eff2 !important;  
        z-index: 1; /* Bring the active tab to the front if necessary */
    }

    .inactive-tab {
        background-color: #f0eff2 !important;  
        color: black !important;                
        border: 1px solid #f0eff2  !important;
        opacity: 40%; 
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
    .loginPage--admin {
        padding-top: 1rem !important;
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
    .loginPage--admin {
        padding-top: 1rem !important;
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

<script>
    // Toggle Password Visibility
    document.addEventListener('DOMContentLoaded', function () {
    const togglePassword = document.querySelector('#togglePassword');
    const passwordField = document.querySelector('#password');

    togglePassword.addEventListener('click', function () {
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        this.classList.toggle('fa-eye-slash'); // Toggle between eye and eye-slash icons
    });
});
</script>

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
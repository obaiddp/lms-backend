@extends('layouts.auth')

@section('content')
    <!-- Sing Up Area Start -->
    {{-- <section class="sign-up-page p-0">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-md-5">
                    <div class="sign-up-left-content">
                        <p>{{ __(get_option('sign_up_left_text')) }}</p>
                        @if(get_option('sign_up_left_image'))
                            <div class="sign-up-bottom-img">
                                
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-7">
                    
                </div>
            </div>
        </div>
    </section> --}}
    <!-- Sing Up Area End -->

    <section style="background-color: black" class="auth-section">
        <a href="/" class="btn"
            style="z-index: 99999; position: absolute; top: 1rem; left: 1rem; color: black; background-color: rgba(255, 255, 255, 0.6); padding: 6px 10px; border: none; border-radius: 5px; cursor: pointer;">
            <span class="fas fa-arrow-left"></span> {{ __('Back') }}
        </a>
        <img src="{{getImageFile(get_option('sign_up_left_image'))}}" alt="hero" class="auth-img img-fluid">
        <div class="auth-div">
            <div class="login-form">
                <h5>{{__('Sign In')}}</h5>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <div class="auth-form-input">
                        <div class="auth-input">
                            <input type="text" name="email" value="{{old('email')}}" class="form-control email" placeholder="{{ __('Email or Phone Number') }}" required>
                            <span class="fas fa-envelope"></span>
                        </div>
                        @if ($errors->has('email'))
                            <div class="text-danger auth-error"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('email') }}</div>
                        @endif
                    </div>
                    <div class="auth-form-input">
                        <div class="auth-input">
                            <input class="form-control password" name="password" value="{{old('password')}}" placeholder="Password" type="password" required>
                            <span class="toggle cursor fas fa-eye pass-icon"></span>
                        </div>
                        @if ($errors->has('password'))
                            <div class="text-danger auth-error"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('password') }}</div>
                        @endif
                    </div>

                    <a href="{{ route('forget-password') }}" class="auth-forgot">{{__('Forgot Password')}}?</a>
                    
                    <button type="submit" class="theme-btn theme-button3 font-15 fw-bold w-100">{{__('Sign In')}}</button>
                    <p class="new-user">{{__('New User')}}? 
                        <a href="{{route('sign-up-as-instructor')}}" class="">
                            {{__('Sign Up As Instructor')}}
                        </a> 
                        
                        <input type="hidden" name="slug_updated" id="slug_updated" value="{{$slug}}">
                        <input type="hidden" name="st_type_updated" id="st_type_updated" value="{{$type}}"> 
                        /
                        @if ($type != null) @if ($type == 1) 
                        <a href="{{route('sign-up-as-student')}}/1/{{$slug}}" class="">
                            {{__('Sign Up As Student')}}
                        </a>

                        @else 
                        <a href="{{route('sign-up-as-student')}}/2/{{$slug}}" class="">
                            {{__('Sign Up As Student')}}
                        </a> 
                        
                        @endif 
                        
                        @else
                        <a href="{{route('sign-up-as-student')}}" class="">
                            {{__('Sign Up As Student')}}
                        </a> 
                        @endif
                    </p>
                    <div class="connect-with">
                        <hr class="line">
                        <span class="connect-text">or connect with</span>
                        <hr class="line">
                    </div>
                    <div class="social-media-login-wrap d-flex"> 
                        @if(env('FACEBOOK_LOGIN_STATUS') == 1) 
                            <a href="{{ route('login.facebook') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 112.196 112.196" id="facebook" width="38" height="38">
                                    <circle cx="56.098" cy="56.098" r="56.098" fill="#3b5998"></circle>
                                    <path fill="#fff"
                                        d="M70.201 58.294h-10.01v36.672H45.025V58.294h-7.213V45.406h7.213v-8.34c0-5.964 2.833-15.303 15.301-15.303l11.234.047v12.51h-8.151c-1.337 0-3.217.668-3.217 3.513v7.585h11.334l-1.325 12.876z">
                                    </path>
                                </svg>
                            </a> 
                        @endif 
                        @if(env('GOOGLE_LOGIN_STATUS') == 1) 
                            <a href="{{ route('login.google') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" id="google" width="32" height="32">
                                    <path fill="#fbbb00"
                                        d="M113.47 309.408 95.648 375.94l-65.139 1.378C11.042 341.211 0 299.9 0 256c0-42.451 10.324-82.483 28.624-117.732h.014L86.63 148.9l25.404 57.644c-5.317 15.501-8.215 32.141-8.215 49.456.002 18.792 3.406 36.797 9.651 53.408z">
                                    </path>
                                    <path fill="#518ef8"
                                        d="M507.527 208.176C510.467 223.662 512 239.655 512 256c0 18.328-1.927 36.206-5.598 53.451-12.462 58.683-45.025 109.925-90.134 146.187l-.014-.014-73.044-3.727-10.338-64.535c29.932-17.554 53.324-45.025 65.646-77.911h-136.89V208.176h245.899z">
                                    </path>
                                    <path fill="#28b446"
                                        d="m416.253 455.624.014.014C372.396 490.901 316.666 512 256 512c-97.491 0-182.252-54.491-225.491-134.681l82.961-67.91c21.619 57.698 77.278 98.771 142.53 98.771 28.047 0 54.323-7.582 76.87-20.818l83.383 68.262z">
                                    </path>
                                    <path fill="#f14336"
                                        d="m419.404 58.936-82.933 67.896C313.136 112.246 285.552 103.82 256 103.82c-66.729 0-123.429 42.957-143.965 102.724l-83.397-68.276h-.014C71.23 56.123 157.06 0 256 0c62.115 0 119.068 22.126 163.404 58.936z">
                                    </path>
                                </svg>
                            </a> 
                        @endif 
                        @if(env('TWITTER_LOGIN_STATUS') == 1) 
                            <a href="{{ route('login.twitter') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="32" height="32">
                                    <path
                                        d="M14.095479,10.316482L22.286354,1h-1.940718l-7.115352,8.087682L7.551414,1H1l8.589488,12.231093L1,23h1.940717  l7.509372-8.542861L16.448587,23H23L14.095479,10.316482z M11.436522,13.338465l-0.871624-1.218704l-6.924311-9.68815h2.981339  l5.58978,7.82155l0.867949,1.218704l7.26506,10.166271h-2.981339L11.436522,13.338465z" />
                                </svg>
                            </a>
                        @endif 
                    </div>
                </form> 
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        "use strict"
        $('#adminCredentialShow').on('click', function (){
            $('.email').val('admin@gmail.com');
            $('.password').val('123456');
        });

        $('#instructorCredentialShow').on('click', function (){
            $('.email').val('instructor@gmail.com');
            $('.password').val('123456');
        });

        $('#studentCredentialShow').on('click', function (){
            $('.email').val('student@gmail.com');
            $('.password').val('123456');
        });

        $('#affiliatorCredentialShow').on('click', function (){
            $('.email').val('affililator@gmail.com');
            $('.password').val('123456');
        });

        $('#organizationCredentialShow').on('click', function (){
            $('.email').val('organization@gmail.com');
            $('.password').val('123456');
        });
    </script>
@endpush

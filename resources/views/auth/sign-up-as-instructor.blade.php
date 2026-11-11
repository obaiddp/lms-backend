@extends('layouts.auth')

@section('content')

<section class="auth-section">
    <img src="{{getImageFile(get_option('sign_up_left_image'))}}" alt="hero" class="auth-img img-fluid">
    <div class="auth-div">
        <a href="/" class="btn"
            style="z-index: 99999; position: absolute; top: 1rem; left: 1rem; color: black; background-color: rgba(255, 255, 255, 0.6); padding: 6px 10px; border: none; border-radius: 5px; cursor: pointer;">
            <span class="fas fa-arrow-left"></span> {{ __('Back') }}
        </a>
        <div class="sign-up-form">
            <h5>{{__('Create Instructor Account')}}</h5>
            <form method="POST" action="{{route('store.sign-up-as-instructor')}}" enctype="multipart/form-data"> 
                @csrf 

                <div class="auth-form-input">
                    <div class="auth-input">
                        <input type="email" name="email" id="email" value="{{old('email')}}" class="form-control" placeholder="Email" required>
                        <span class="fas fa-envelope"></span>
                    </div> 
                    @if ($errors->has('email')) 
                    <div class="text-danger auth-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ $errors->first('email') }}
                    </div> 
                    @endif
                </div>

                <div class="auth-form-input">
                    <div class="auth-input">
                        <select class="form-control" id="account_type" name="account_type" value="{{old('account_type')}}" required>
                            <option value="" disabled selected hidden>{{ __('Select Account Type') }}</option>
                            <option value="{{ USER_ROLE_INSTRUCTOR }}">{{ __('Instructor') }}</option>
                            <option value="{{ USER_ROLE_ORGANIZATION }}">{{ __('Organization') }}</option>
                        </select>
                    </div> 
                    @if ($errors->has('account_type')) 
                    <div class="text-danger auth-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ $errors->first('account_type') }}
                    </div> 
                    @endif
                </div>
                
                <div class="d-flex justify-content-between align-items-center" style="height: 2.8rem;">
                    <div class="auth-form-input" style="width: 36%">
                        <div class="auth-input">
                            <select class="form-control" name="area_code" required>
                                <option value="" disabled selected hidden>{{ __('Select Code') }}</option>
                                @foreach ($countries as $country)
                                <option value="{{ $country->phonecode }}" @if(old('area_code')==$country->
                                    phonecode) selected @endif>{{ $country->short_name.'('.$country->phonecode.')' }}</option>
                                @endforeach
                            </select>
                        </div> 
                        @if ($errors->has('area_code')) 
                        <div class="text-danger auth-error">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ $errors->first('area_code') }}
                        </div> 
                        @endif
                    </div>

                    <div class="auth-form-input" style="width: 60%">
                        <div class="auth-input">
                            <input type="text" name="mobile_number" id="mobile_number" value="{{old('mobile_number')}}" class="form-control" placeholder={{__('Phone Number')}} required>
                            <span class="fas fa-phone"></span>
                        </div> 
                        @if ($errors->has('mobile_number')) 
                        <div class="text-danger auth-error">
                            <i class="fas fa-exclamation-triangle"></i> 
                            {{ $errors->first('mobile_number') }}
                        </div> 
                        @endif
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center" style="height: 2.8rem; margin-top: 0.8rem;">
                    <div class="auth-form-input" style="width: 48%">
                        <div class="auth-input">
                            <input type="text" name="first_name" id="first_name" value="{{old('first_name')}}" class="form-control" placeholder="{{__('First Name')}}" required>
                        </div> 
                        @if ($errors->has('first_name')) 
                        <div class="text-danger auth-error">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ $errors->first('last_name') }}
                        </div> 
                        @endif
                    </div>
                    <div class="auth-form-input" style="width: 48%">
                        <div class="auth-input">
                            <input type="text" name="last_name" id="last_name" value="{{old('last_name')}}" class="form-control" placeholder="{{__('Last Name')}}" required>
                        </div> 
                        @if ($errors->has('last_name')) 
                        <div class="text-danger auth-error">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ $errors->first('last_name') }}
                        </div> 
                        @endif
                    </div>
                </div>

                <div class="auth-form-input">
                    <div class="auth-input">
                        <input class="form-control password" name="password" value="{{old('password')}}" placeholder="Password" type="password" required>
                        <span class="toggle cursor fas fa-eye pass-icon"></span>
                    </div> 
                    @if ($errors->has('password')) 
                    <div class="text-danger auth-error">
                        <i class="fas fa-exclamation-triangle"></i> 
                        {{ $errors->first('password') }}
                    </div> 
                    @endif
                </div>

                <div class="auth-form-input">
                    <div class="auth-input">
                        <input name="professional_title" id="professional_title" value="{{old('professional_title')}}"
                                    class="form-control" placeholder={{__('Professional Title')}} required>
                    </div> 
                    @if ($errors->has('professional_title')) 
                    <div class="text-danger auth-error">
                        <i class="fas fa-exclamation-triangle"></i> 
                        {{ $errors->first('professional_title') }}
                    </div> 
                    @endif
                </div>

                <div class="auth-form-input">
                    <div class="auth-input">
                        <input name="address" id="address" value="{{old('address')}}" class="form-control" placeholder={{__('Address')}} required>
                    </div> @if ($errors->has('address')) <div class="text-danger auth-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ $errors->first('address') }}
                    </div> @endif
                </div>

                <div class="auth-form-input">
                    <div class="auth-input">
                        <textarea name="about_me" class="form-control" cols="30" rows="4" placeholder={{__('Bio')}} required>{{ old('about_me') }}</textarea>
                    </div> @if ($errors->has('about_me')) <div class="text-danger auth-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ $errors->first('about_me') }}
                    </div> @endif
                </div>

                <div class="auth-form-input">
                        <div class="auth-input">
                            <label class="label-text-title color-heading font-medium font-16 mb-2">
                                {{__('CV')}} (Maximum Image Upload Size is 10mb) </label>
                            <input type="file" name="cv_file" accept="application/pdf" class="form-control"
                                style="margin-top: 0 !important;" required/>
                        </div> @if ($errors->has('cv_file')) <div class="text-danger auth-error">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ $errors->first('cv_file') }}
                        </div> @endif
                    </div>

                    <div class="auth-form-input">
                        <div class="auth-input d-flex my-4">
                            <div style="width: 18%;">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked" checked required>
                            </div>
                            <label class="form-check-label reg-label mb-0" for="flexCheckChecked"> 
                                By clicking Create account, I agree that I have read and accepted the 
                                <a href="{{ route('terms-conditions') }}" class="color-hover text-decoration-underline">
                                    Terms of Use
                                </a> and
                                <a href="{{ route('privacy-policy') }}" class="color-hover text-decoration-underline">
                                    Privacy Policy.
                                </a>
                            </label>
                        </div>
                    </div>

                <button type="submit"
                    class="theme-btn theme-button3 font-15 fw-bold w-100">{{__('Sign Up')}}</button>
                    <p class="old-user">{{__('Already have an account?')}} <a href="{{route('login')}}"
                            class="color-hover text-decoration-underline font-medium">{{__('Sign In')}}</a></p>
            </form>
        </div>
    </div>
</section>
@endsection

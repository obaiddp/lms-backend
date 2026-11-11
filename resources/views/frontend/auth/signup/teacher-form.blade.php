<form id="teacher-form" method="POST" action="{{route('store.sign-up-as-instructor')}}" enctype="multipart/form-data"> 
    @csrf 
    <input type="hidden" name="signup_type2" value="teacher">

    <div class="row mb-3 d-flex flex-column flex-md-row gap-4 gap-md-0">
        <div class="col-md-6 mb-3 auth-form-input">
            <label for="first_name" class="mb-1 text-sm text-black font-bold aileron">{{__('First Name')}}</label>
            <input type="text" name="first_name" class="form-control aileron" id="first_name" 
                   value="{{ old('first_name') }}" placeholder="{{__('Enter your first name')}}" required>
            @error('first_name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="col-md-6 mb-3 auth-form-input">
            <label for="last_name" class="mb-1 text-sm text-black font-bold aileron">{{__('Last Name')}}</label>
            <input type="text" name="last_name" class="form-control aileron" id="last_name" 
                   value="{{ old('last_name') }}" placeholder="{{__('Enter your last name')}}" required>
            @error('last_name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>

 
    <div class="row mb-3 d-flex flex-column flex-md-row gap-4 gap-md-0">
     
        <div class="col-md-6 mb-3 auth-form-input">
            <label for="email" class="mb-1 text-sm text-black font-bold aileron">{{__('Email')}}</label>
            <input type="email" name="email" class="form-control aileron" id="email" 
                   value="{{ old('email') }}" placeholder="{{__('Enter your email')}}" required>
            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
        </div>


        <div class="col-md-6 auth-form-input">
            <label for="account_type" class="mb-1 text-sm text-black font-bold aileron">{{__('Account Type')}}</label>
            <select name="account_type" class="form-control aileron" id="account_type" required>
                <option value="">{{ __('Select Account Type') }}</option>
                <option value="{{ USER_ROLE_INSTRUCTOR }}">{{ __('Instructor') }}</option>
                <option value="{{ USER_ROLE_ORGANIZATION }}">{{ __('Organization') }}</option>
            </select>
            @error('account_type') <small class="text-danger aileron">{{ $message }}</small> @enderror
        </div>
    </div>


    <div class="row mb-3 d-flex flex-column flex-md-row gap-4 gap-md-0">
        <div class="col-md-6 auth-form-input">
            <label for="area_code" class="mb-1 text-sm text-black font-bold aileron">{{__('Code')}}</label>
            <select name="area_code" class="form-control aileron" id="area_code" required>
                <option value="">{{ __('Select Code') }}</option>
                @foreach ($countries as $country)
                <option value="{{ $country->phonecode }}" @if(old('area_code')==$country->
                    phonecode) selected @endif>{{ $country->short_name.'('.$country->phonecode.')' }}</option>
                @endforeach
            </select>
            @error('area_code') <small class="text-danger aileron">{{ $message }}</small> @enderror
        </div>


        <div class="col-md-6 mb-3 auth-form-input">
            <label for="mobile_number" class="mb-1 text-sm text-black font-bold aileron">{{__('Phone Number')}}</label>
            <input type="tel" name="mobile_number" class="form-control aileron" id="mobile_number" 
                   value="{{ old('mobile_number') }}" placeholder="{{__('Enter your phone number')}}" required>
                   <input type="hidden" name="area_code_3" id="area_code_3">
            @error('mobile_number') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>



    <div class="row mb-3 d-flex flex-column flex-md-row gap-4 gap-md-0">
        <div class="col-md-6 auth-form-input">
            <label for="password" class="mb-1 text-sm text-black font-bold aileron">{{__('Password')}}</label>
            <div class="position-relative">
                <input type="password" name="password" class="form-control pe-5 password aileron" id="password2" placeholder="*********" required>
                <i class="fas fa-eye position-absolute end-0 top-50 translate-middle-y me-3" id="togglePassword2" style="cursor: pointer;"></i>
            </div>
            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <!-- Confirm Password -->
        <div class="col-md-6">
            <label for="password_confirmation" class="mb-1 text-sm text-black font-bold aileron">{{__('Confirm Password')}} </label>
            <div class="position-relative">
                <input type="password" name="password_confirmation" class="form-control pe-5 password aileron" id="password_confirmation2" placeholder="*********" required>
                <i class="fas fa-eye position-absolute end-0 top-50 translate-middle-y me-3" id="togglePasswordConfirm2" style="cursor: pointer;"></i>
            </div>
            @error('password_confirmation') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>




  

    <div class="row mb-3 d-flex flex-column flex-md-row gap-4 gap-md-0">
        <div class="col-md-6 mb-3 auth-form-input">
            <label for="professional_title" class="mb-1 text-sm text-black font-bold aileron">{{__('Professional Title')}}</label>
            <input type="text" name="professional_title" class="form-control aileron" id="professional_title" 
                   value="{{ old('professional_title') }}" placeholder="{{__('Enter your professional title')}}" required>
            @error('professional_title') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="col-md-6 mb-3 auth-form-input">
            <label for="address" class="mb-1 text-sm text-black font-bold aileron">{{__('Address')}}</label>
            <textarea class="form-control aileron" name="address" id="address" cols="30" rows="10" placeholder={{__('Address')}} required></textarea>
            @error('address') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>



    <div class="row mb-3 d-flex flex-column flex-md-row gap-4 gap-md-0">
        <div class="col-md-12 mb-3 auth-form-input">
            <label for="about_me" class="mb-1 text-sm text-black font-bold aileron">{{__('Bio')}}</label>
            <textarea class="form-control aileron" name="about_me" id="about_me" cols="30" rows="10" placeholder={{__('Bio')}} required></textarea>
            @error('about_me') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>





    <div class="row mb-3 d-flex flex-column flex-md-row gap-4 gap-md-0">
        <div class="col-md-12 mb-3 auth-form-input">
            <label for="cv_file" class="mb-1 text-sm text-black font-bold aileron">{{__('CV')}} (Maximum Image Upload Size is 10mb)</label>
            <input type="file" name="cv_file" accept="application/pdf" class="form-control aileron" style="margin-top: 0 !important;" id="cv_file" required>
            @error('cv_file') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>





    <div class="row mb-3 d-flex flex-column flex-md-row gap-4 gap-md-0">
        <div class="col-md-12 mb-3 auth-form-input" style="display: flex; align-items: start;">
            <div style="width: 4%;">
                <input class="form-check-input aileron" type="checkbox" value="" id="flexCheckChecked" checked required>
            </div>

            <label class="form-check-label mb-1 text-sm text-black font-bold aileron" for="flexCheckChecked"> 
                {{__('By clicking Create account, I agree that I have read and accepted the')}}
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
        class="theme-btn aileron theme-button1 theme-button3 w-100 font-14 !rounded !fw-light">{{__('Sign Up As Instructor')}}</button>
        {{-- <p class="old-user">{{__('Already have an account?')}} <a href="{{route('login')}}"
                class="color-hover text-decoration-underline font-medium">{{__('Sign In')}}</a></p> --}}
</form>


<script>
    const input2 = document.querySelector("#mobile_number");
    const areaCodeSelect = document.querySelector("#area_code");
    const hiddenCodeInput = document.querySelector("#area_code_3");
    let iti2;
  
    function initPhoneInput2(initialCountry = "pk") {
      iti2 = window.intlTelInput(input2, {
        initialCountry: initialCountry,
        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/utils.js",
      });
  
      // 1️⃣ When user changes the flag manually
      input2.addEventListener("countrychange", () => {
        const countryData = iti2.getSelectedCountryData();
        const dialCode = countryData.dialCode;
  
        // Update both dropdown and hidden input
        hiddenCodeInput.value = dialCode;
        [...areaCodeSelect.options].forEach(opt => {
          if (opt.textContent.includes(`(${dialCode})`)) {
            areaCodeSelect.value = opt.value;
          }
        });
  
        // Store for future load
        localStorage.setItem("userCountry", countryData.iso2);
      });
  
      // 2️⃣ When user changes the <select> dropdown
      areaCodeSelect.addEventListener("change", () => {
        const selectedOption = areaCodeSelect.options[areaCodeSelect.selectedIndex];
        const match = selectedOption.textContent.match(/\((\d+)\)/);
        if (!match) return;
  
        const dialCode = match[1];
        const countryMatch = window.intlTelInputGlobals.getCountryData()
          .find(c => c.dialCode === dialCode);
  
        if (countryMatch) {
          iti2.setCountry(countryMatch.iso2);
          hiddenCodeInput.value = dialCode;
        }
      });
  
      // Pre-fill hidden input on load
      const defaultCountryData = iti2.getSelectedCountryData();
      hiddenCodeInput.value = defaultCountryData.dialCode;
    }
  
    function detectInitialCountry() {
      const saved = localStorage.getItem("userCountry");
      if (saved) {
        initPhoneInput2(saved);
      } else {
        navigator.geolocation.getCurrentPosition(
          (pos) => {
            const { latitude, longitude } = pos.coords;
            fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latitude}&lon=${longitude}`)
              .then(res => res.json())
              .then(data => {
                const code = (data?.address?.country_code || "pk").toLowerCase();
                localStorage.setItem("userCountry", code);
                initPhoneInput2(code);
              })
              .catch(() => initPhoneInput2("pk"));
          },
          () => initPhoneInput2("pk"),
          { timeout: 5000 }
        );
      }
    }
  
    detectInitialCountry();
  </script>
  
<form id="student-form" method="POST" action="{{ route('register-as-student') }}">
    @csrf
    <input type="hidden" name="signup_type2" value="student">
     <input type="hidden" id="preCourseType" value="{{ $type }}">
    <input type="hidden" id="preCourseSlug" value="{{ $slug }}"> 

    @if (isset($form1Data['free_signup']))
         <input type="hidden" name="freeSignUp" id="freeSignUp" value="{{ $form1Data['free_signup']  }}"> 
    @else
         <input type="hidden" name="freeSignUp" id="freeSignUp" value="0"> 
    @endif
   
    

    <!-- Progress Bar -->
    <div class="progress-container mb-4">
        <div class="progress-steps d-flex justify-content-between align-items-center px-3 mt-2">
            <div class="step-circle active" data-step="1">1</div>
            <div class="step-line"></div>
            <div class="step-circle" data-step="2">2</div>
            <div class="step-line"></div>
            <div class="step-circle" data-step="3">3</div>
            <div class="step-line"></div>
            <div class="step-circle" data-step="4">4</div>
        </div>
    </div>

    <!-- Step 1: Basic Information -->
    <div class="form-step" id="step1">

        <div class="row mb-3">
            <div class="col-md-12">
                <label class="mb-1 text-sm text-black font-bold aileron">Select Course Type</label>
            </div>

           
            <div class="px-10 row justify-content-around">
                <div class="form-check form-check-inline m-0 col-md-5">
                    <input class="form-check-input aileron course-type" 
                           id="liveCheckbox" 
                           type="radio" 
                           name="study_type" 
                           value="1" 
                          {{ $type == 1 ? 'checked' : '' }}>
                    <label class="form-check-label aileron mb-0" for="liveCheckbox">Live</label>
                </div>
            
                <div class="form-check form-check-inline m-0 col-md-5">
                    <input class="form-check-input aileron course-type" 
                           id="selfLearningCheckbox" 
                           type="radio" 
                           name="study_type" 
                           value="2" 
                           {{ $type == 2 ? 'checked' : '' }}>
                    <label class="form-check-label aileron mb-0" for="selfLearningCheckbox">Self Learning</label>
                </div>
            </div>

            @error('study_type') <small class="text-danger aileron" style="text-align: left !important;">{{ $message }}</small> @enderror
        </div>
    </div>

    <!-- Step 2: Contact Details -->
    <div class="form-step d-none" id="step2">
        <div class="row mb-3 course-section d-none">
            <div class="col-md-12">
                <label for="course" class="mb-1 text-sm text-black font-bold aileron course-label">Course</label>
                <select name="course" class="form-control aileron" id="course" required>
                    {{-- <option value="">Select a course</option> --}}
                    @foreach($courses as $course)
                    <option value="{{ $course->id }}" data-type="{{ $course->course_type }}" 
                         {{ isset($selectedCourse) && $selectedCourse && $selectedCourse->id == $course->id ? 'selected' : '' }}>
                        {{ $course->title }}
                    </option>
                    @endforeach
                </select>
                <small class="text-danger aileron d-none" id="course-error">Please select a course</small>
                @error('course') <small class="text-danger aileron">{{ $message }}</small> @enderror
            </div>
        </div>
    </div>

    <!-- Step 3: Course Preferences -->
    <div class="form-step d-none" id="step3">

        <div class="row mb-3 d-flex flex-column flex-md-row gap-4 gap-md-0">
            <div class="col-md-6 mb-3">
                <label for="fullname" class="mb-1 text-sm text-black font-bold aileron">Full Name</label>
                <input type="text" name="fullname" class="form-control aileron" id="fullname"
                    value="{{ old('fullname', $form1Data['fullname'] ?? '') }}" placeholder="Enter your full name" required>
                @error('fullname') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-6">
                <label for="phone" class="mb-1 text-sm text-black font-bold aileron">WhatsApp Number</label>
                <input type="tel" name="phone" class="form-control aileron" id="phone"
                    value="{{ old('phone', $form1Data['phone'] ?? '') }}" placeholder="Enter your whatsapp number" required>
                @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                <input type="hidden"  class="form-control aileron" name="area_code_2" id="area_code_2">
            </div>
        </div>

        <div class="row mb-3 d-flex flex-column flex-md-row gap-4 gap-md-0">
             <div class="col-md-6 mb-3">
                <label for="email" class="mb-1 text-sm text-black font-bold aileron">Email</label>
                <input type="email" name="email" class="form-control aileron" id="email"
                    value="{{ old('email', $form1Data['email'] ?? '') }}" placeholder="Enter your email" required>
                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-6">
                <label for="country" class="mb-1 text-sm text-black font-bold aileron">Country</label>
                <select name="country" class="form-control aileron" id="country" required>
                    <option value="">Select Country</option>
                    @foreach($countries as $country)
                    <option value="{{ $country->id }}" {{ old('country', $form1Data['country'] ?? '') == $country->id ? 'selected' : '' }}>
                        {{ $country->country_name }}
                    </option>
                    @endforeach
                </select>
                @error('country') <small class="text-danger aileron">{{ $message }}</small> @enderror
            </div>

          
            
          
        </div>

        <div class="row mb-3 d-flex flex-column flex-md-row gap-4 gap-md-0">
        
           

              <div class="col-md-6">
                <label for="city" class="mb-1 text-sm text-black font-bold aileron">City</label>
                <input type="text" name="city" class="form-control aileron" id="city"
                    value="{{ old('city') }}" placeholder="Enter your city" required>
                @error('city') <small class="text-danger aileron">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-6">
                <label for="age" class="mb-1 text-sm text-black font-bold aileron">Age</label>
                <input type="number" name="age" class="form-control aileron" id="age"
                    value="{{ old('age') }}" placeholder="Enter your age" min="1" max="100" required>
                @error('age') <small class="text-danger aileron">{{ $message }}</small> @enderror
            </div>
        </div>

        <div class="row mb-3 d-flex flex-column flex-md-row gap-4 gap-md-0">
            <div class="col-md-6">
                <label for="school_grade" class="mb-1 text-sm text-black font-bold aileron">School Grade (Optional)</label>
                <input type="text" name="school_grade" class="form-control aileron" id="school_grade"
                    value="{{ old('school_grade') }}" placeholder="Enter your school grade">
                @error('school_grade') <small class="text-danger aileron">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-6">
                <label for="parent_name" class="mb-1 text-sm text-black font-bold aileron">Parent's Name (Optional for adults)</label>
                <input type="text" name="parent_name" class="form-control aileron" id="parent_name"
                    value="{{ old('parent_name') }}" placeholder="Enter parent's name">
                @error('parent_name') <small class="text-danger aileron">{{ $message }}</small> @enderror
            </div>

        </div>  

       


        <div class="row mb-3 d-flex flex-column flex-md-row gap-4 gap-md-0">
            <div class="col-md-6">
                <label for="password" class="mb-1 text-sm text-black font-bold aileron">Password</label>
                <div class="position-relative">
                    <input type="password" name="password" class="form-control pe-5 password aileron" id="password" placeholder="*********" required>
                    <i class="fas fa-eye position-absolute end-0 top-50 translate-middle-y me-3" id="togglePassword" style="cursor: pointer;"></i>
                </div>
                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        
            <div class="col-md-6">
                <label for="password_confirmation" class="mb-1 text-sm text-black font-bold aileron">Confirm Password</label>
                <div class="position-relative">
                    <input type="password" name="password_confirmation" class="form-control pe-5 password aileron" id="password_confirmation" placeholder="*********" required>
                    <i class="fas fa-eye position-absolute end-0 top-50 translate-middle-y me-3" id="togglePasswordConfirm" style="cursor: pointer;"></i>
                </div>
                @error('password_confirmation') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>
            

        <div class="live-course-sections d-none row d-flex flex-column flex-md-row gap-4 gap-md-0">
                 <div class="col-md-12 mb-3">
                    <label for="learning_goals" class="mb-1 text-sm text-black font-bold aileron">Learning Goals</label>
                    <textarea name="learning_goals" id="learning_goals" class="form-control aileron" cols="30" rows="10">{{old('learning_goals')}}</textarea>
                  
                    @error('learning_goals') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-12 mb-3">
                    <label for="preferred_teacher" class="mb-1 text-sm text-black font-bold aileron">For your evaluation, do you prefer</label>
                    <select name="preferred_teacher" class="form-control aileron" id="preferred_teacher">
                        <option value="">Select</option>
                        <option value="Male" {{ old('preferred_teacher') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('preferred_teacher') == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Either" {{ old('preferred_teacher') == 'Either' ? 'selected' : '' }}>Either</option>
                    </select>
                    @error('preferred_teacher') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            <div class="col-md-12 mb-3">
                <label for="how_many_students" class="mb-1 text-sm text-black font-bold aileron">How many students will join?</label>
                <input type="number" name="how_many_students" class="form-control aileron"
                    value="{{ old('how_many_students') }}" placeholder="Enter number of students">
                @error('how_many_students') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-md-12" id="calendar-picker" data-available-slots="{{ json_encode($availableSlots) }}">
                    <div class="mb-4">
                        <h6 class="mt-3 mb-4 calendar-text aileron">{{__('Pick a date and time for 1 hour free trial lesson')}}</h6>
                        <div id="wrapper" class="wrapper mb-2">
                            <div class="container-calendar">
                                <div class="d-flex justify-content-between align-items-center px-2 mb-3 position-relative">
                                    <h6 class="month-year-display text-center" id="monthAndYear"></h6>
                                    <div class="button-container-calendar position-absolute">
                                        <button id="previous" class="nav-btn">&#8249;</button>
                                        <button id="next" class="nav-btn">&#8250;</button>
                                    </div>
                                </div>
                                <div class="table-wrapper">
                                    <table class="table-calendar table text-center" id="calendar" data-lang="en">
                                        <thead id="thead-month"></thead>
                                        <tbody id="calendar-body"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="preferDate" id="preferDate" value="">
                    <input type="hidden" name="preferSlot" id="preferSlot" value="">
                    <div id="time-slots">
                        <div class="d-flex align-items-center gap-2">
                            <div>
                                <svg width="32" height="20" viewBox="0 0 32 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16 6.25C11.3473 6.25 7.5625 10.0348 7.5625 14.6875C7.5625 16.1706 7.95344 17.6291 8.69225 18.9072H23.3078C24.0466 17.6291 24.4375 16.1706 24.4375 14.6875C24.4375 10.0348 20.6527 6.25 16 6.25ZM16 4.375C16.5182 4.375 16.9375 3.95569 16.9375 3.4375V1.5625C16.9375 1.04431 16.5182 0.625 16 0.625C15.4818 0.625 15.0625 1.04431 15.0625 1.5625V3.4375C15.0625 3.95569 15.4818 4.375 16 4.375ZM4.75 13.75H2.875C2.35681 13.75 1.9375 14.1693 1.9375 14.6875C1.9375 15.2057 2.35681 15.625 2.875 15.625H4.75C5.26819 15.625 5.6875 15.2057 5.6875 14.6875C5.6875 14.1693 5.26819 13.75 4.75 13.75ZM29.125 13.75H27.25C26.7318 13.75 26.3125 14.1693 26.3125 14.6875C26.3125 15.2057 26.7318 15.625 27.25 15.625H29.125C29.6432 15.625 30.0625 15.2057 30.0625 14.6875C30.0625 14.1693 29.6432 13.75 29.125 13.75ZM28.1784 7.65625C27.9184 7.20675 27.3425 7.052 26.8976 7.31294L25.2743 8.25044C24.8257 8.50956 24.6719 9.08263 24.931 9.53125C25.1903 9.98025 25.7648 10.1328 26.2118 9.87456L27.8351 8.93706C28.2836 8.678 28.4374 8.10488 28.1784 7.65625ZM21.1562 5.7565C21.6024 6.01425 22.1773 5.863 22.4371 5.41319L23.3746 3.78994C23.6337 3.34131 23.4799 2.76819 23.0312 2.50912C22.5836 2.24819 22.0095 2.40294 21.7504 2.85244L20.8129 4.47569C20.5538 4.92431 20.7076 5.49744 21.1562 5.7565ZM9.56294 5.41319C9.82225 5.86219 10.3967 6.01475 10.8438 5.7565C11.2924 5.49738 11.4462 4.92431 11.1871 4.47569L10.2496 2.85244C9.98862 2.40294 9.4155 2.24819 8.96875 2.50912C8.52012 2.76825 8.36631 3.34131 8.62544 3.78994L9.56294 5.41319ZM6.72569 8.25044L5.10244 7.31294C4.65381 7.052 4.08162 7.20675 3.82162 7.65625C3.5625 8.10488 3.71631 8.678 4.16494 8.93706L5.78819 9.87456C6.23438 10.1323 6.80925 9.98106 7.069 9.53125C7.32812 9.08263 7.17431 8.5095 6.72569 8.25044Z" fill="url(#paint0_linear_800_1209)" />
                                    <path d="M31.0625 17.5H0.9375C0.419312 17.5 0 17.9193 0 18.4375C0 18.9557 0.419312 19.375 0.9375 19.375H31.0625C31.5807 19.375 32 18.9557 32 18.4375C32 17.9193 31.5807 17.5 31.0625 17.5Z" fill="url(#paint1_linear_800_1209)" />
                                    <defs>
                                        <linearGradient id="paint0_linear_800_1209" x1="16" y1="18.9072" x2="16" y2="0.625" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#FD5900" />
                                            <stop offset="1" stop-color="#FFDE00" />
                                        </linearGradient>
                                        <linearGradient id="paint1_linear_800_1209" x1="16" y1="19.375" x2="16" y2="17.5" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#FFE59A" />
                                            <stop offset="1" stop-color="#FFFFD5" />
                                        </linearGradient>
                                    </defs>
                                </svg>

                            </div>
                            <div>
                                <h6 class="color-black mt-3">{{__('Available Time Slots')}}</h6>
                                <p class="grayNew font-semibold" style="font-size:14px ">{{__('Select your preferred time')}}</p>
                            </div>
                        </div>
                        <div id="slots" class="time-slot-container mb-4">

                            <!-- Time slots will be dynamically inserted here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>




        {{-- <div class="row mb-3 d-flex flex-column flex-md-row gap-4 gap-md-0">
          

          

            <div class="col-md-6">
                <label for="timezone" class="mb-1 text-sm text-black font-bold aileron">Timezone</label>
                <select name="timezone" class="form-control select2 aileron" id="timezone" required>
                    <option value="">Select Timezone</option>
                    @foreach($timeZonesArray as $key => $timezone)
                    <option value="{{ $key }}" {{ old('timezone') == $timezone ? 'selected' : '' }}>
                        {{ $timezone }}
                    </option>
                    @endforeach
                </select>
                @error('timezone') <small class="text-danger aileron">{{ $message }}</small> @enderror
            </div>
            
            <div class="col-md-6">
                <label for="referral" class="mb-1 text-sm text-black font-bold aileron">Referral Code</label>
                <input type="text" name="referral" class="form-control aileron" id="referral"
                    value="{{ old('referral') }}" placeholder="Enter referral code">
            </div>
        </div> --}}

        {{-- <div class="row d-flex flex-column flex-md-row gap-4 gap-md-0">
            <div class="col-md-12 mb-3">
                <label for="what_will_you_use" class="mb-1 text-sm text-black font-bold aileron">What will you use CEF for?</label>
                <select name="what_will_you_use" class="form-control aileron" id="what_will_you_use">
                    <option value="">Select</option>
                    <option value="quran" {{ old('what_will_you_use') == 'quran' ? 'selected' : '' }}>Quran</option>
                    <option value="islamic_studies" {{ old('what_will_you_use') == 'islamic_studies' ? 'selected' : '' }}>Islamic Studies</option>
                    <option value="arabic" {{ old('what_will_you_use') == 'arabic' ? 'selected' : '' }}>Arabic</option>
                    <option value="others" {{ old('what_will_you_use') == 'others' ? 'selected' : '' }}>Others</option>
                </select>
                @error('what_will_you_use') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-12 mb-3">
                <label for="learn_about_lms" class="mb-1 text-sm text-black font-bold aileron">How did you learn about CEF Online?</label>
                <select name="learn_about_lms" class="form-control aileron" id="learn_about_lms" required>
                    <option value="">Select</option>
                    <option value="A Friend" {{ old('learn_about_lms') == 'A Friend' ? 'selected' : '' }}>A Friend</option>
                    <option value="Social Media" {{ old('learn_about_lms') == 'Social Media' ? 'selected' : '' }}>Social Media</option>
                    <option value="E-Mail" {{ old('learn_about_lms') == 'E-Mail' ? 'selected' : '' }}>E-Mail</option>
                    <option value="Other" {{ old('learn_about_lms') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('learn_about_lms') <small class="text-danger aileron">{{ $message }}</small> @enderror
            </div>
        </div> --}}

    

   
    </div>

    <!-- Step 4: Additional Information -->
<div class="form-step d-none" id="step4">
    <div class="mb-4">
            {{-- <div class="alert alert-info" id="course-summary-alert" style="display: none;">
                <strong>You've selected the <span id="selected-course-type"></span> course: <span id="selected-course-name"></span></strong>
            </div> --}}
            <div class="alert alert-info" id="course-summary-alert" style="display: none;">
                <ul class="list-unstyled mb-0">
                    <li><strong>Name:</strong> <span id="student-name-display">-</span></li>
                    <li><strong>Email:</strong> <span id="student-email-display">-</span></li>
                    <li><strong>WhatsApp Number:</strong> <span id="student-phone-display">-</span></li>
                    <li><strong>You've selected the <span id="selected-course-type"></span> course: <span id="selected-course-name"></span></strong></li>
                </ul>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
              
                // Get the correct form elements
                // var courseTypeInputs = document.querySelectorAll('input[name="study_type[]"]');
                var courseTypeInputs = document.querySelectorAll('input[name="study_type"]');
                var courseSelect = document.getElementById('course');
                var summaryAlert = document.getElementById('course-summary-alert');
                var typeSpan = document.getElementById('selected-course-type');
                var nameSpan = document.getElementById('selected-course-name');
                var studentNameSpan = document.getElementById('student-name-display');
                var studentEmailSpan = document.getElementById('student-email-display');
                var studentPhoneSpan = document.getElementById('student-phone-display');

                // function updateSummary() {
                //     console.log("START111")
                    
                //     // Get selected course type
                //     // var selectedType = document.querySelector('input[name="study_type[]"]:checked');
                //     var selectedType = document.querySelector('input[name="study_type"]:checked');
                //     var type = '';
                //     if (selectedType) {
                //         type = selectedType.value === '1' ? 'Live' : 'Self Learning';
                //     }
                    
                //     // Get selected course name
                //     var name = '';
                //     if (courseSelect && courseSelect.selectedIndex > 0) {
                //         name = courseSelect.options[courseSelect.selectedIndex].text;
                //     }

                //      // Input field where student name is entered (adjust ID if different)
                //     var studentNameInput = document.getElementById('fullname').value;
                //     var studentEmailInput = document.getElementById('email').value;
                //     var studentPhoneInput = document.getElementById('area_code_2').value + document.getElementById('phone').value;

                //      console.log(type)
                //       console.log(name)
                //     console.log("START")
                //     console.log(studentNameInput)
                //     console.log(studentEmailInput)
                //     console.log(studentPhoneInput)
                //     console.log("END")
                //     var studentNameSpan = document.getElementById('student-name-display');
                //     var studentEmailSpan = document.getElementById('student-email-display');
                //     var studentPhoneSpan = document.getElementById('student-phone-display');

                //     // Update display
                //     if (type && name) {
                //         typeSpan.textContent = type;
                //         nameSpan.textContent = name;
                //         studentNameSpan.textContent = studentNameInput;
                //         studentEmailSpan.textContent = studentEmailInput;
                //         studentPhoneSpan.textContent = studentPhoneInput;
                //         summaryAlert.style.display = 'block';
                //     } else {
                //         summaryAlert.style.display = 'none';
                //     }
                // }

                // Add event listeners to course type radio buttons
                courseTypeInputs.forEach(function(input) {
                    
                    input.addEventListener('change', updateSummary);
                });

                // Add event listener to course select
                if (courseSelect) {
                   
                    courseSelect.addEventListener('change', updateSummary);
                }

                // Initial check
                updateSummary();
            });
        </script>

    </div>

    <!-- Navigation Buttons -->
    <div class="d-flex justify-content-between mt-4">
        <button type="button" class="btn theme-btn theme-button1 d-none" style="background-color: #989898 !important; border-color: #989898 !important; color: white;" id="prevBtn" onclick="prevStep()">Previous</button>
        <button type="button" class="btn theme-btn theme-button1" id="nextBtn" onclick="nextStep()">Next</button>
        <button type="submit" class="btn theme-btn theme-button1 d-none" id="submitBtn">Sign Up</button>
    </div>
</form>

<style>
    .progress-container {
        width: 100%;
        padding: 20px 0;
    }

    .progress-labels {
        width: 100%;
    }

    .step-label {
        font-size: 14px;
        color: #333;
        font-weight: 500;
        text-align: center;
        width: 25%;
    }

    .progress-steps {
        position: relative;
        width: 100%;
    }

    .step-circle {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid #ccc;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #666;
        z-index: 2;
        position: relative;
    }

    .step-circle.active {
        background: #0a3a75;
        border-color: #0a3a75;
        color: white;
    }

    .step-circle.completed {
        background: #0a3a75;
        border-color: #0a3a75;
        color: #0a3a75;
    }

    .step-line {
        flex: 1;
        height: 2px;
        background: #ccc;
        margin: 0 -2px;
        z-index: 1;
    }

    .step-line.active {
        background: #000;
    }

    /* Mobile Responsive Styles */
    @media (max-width: 576px) {
        .progress-container {
            padding: 15px 0;
        }

        .step-label {
            font-size: 12px;
        }

        .step-circle {
            width: 25px;
            height: 25px;
            font-size: 12px;
        }

        .progress-labels {
            padding: 0 10px;
        }
    }

    @media (max-width: 400px) {
        .step-label {
            font-size: 10px;
        }

        .step-circle {
            width: 20px;
            height: 20px;
            font-size: 10px;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@17.0.8/build/js/intlTelInput.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@17.0.8/build/js/utils.js"></script>

<script>


    $(document).ready(function () {
        const courseTypeInputs = $('.course-type');
        const courseSection = $('.course-section');
        const courseSelect = $('#course');
        const courseLabel = $('.course-label');
        const liveCourseSection = $('.live-course-sections');
        const courseError = $('#course-error');
    
        // Store all original options for filtering
        const courseOptions = courseSelect.find('option').clone();
    
        // Fields that should be MANDATORY for Self Learning (type 2)
        const selfLearningRequired = [
            'fullname',
            'phone',
            'email',
            'country',
            'password',
            'password_confirmation'
        ];
    

          // Conditional fields to hide/show based on course type
        const conditionalFields = [
            '#city',
            '#age',
            '#school_grade',
            '#parent_name'
        ];


        // === Function: Filter Courses by Type ===
        function filterCourses(type) {
            // Clear and refill course dropdown
            courseSelect.empty().append('<option value="">Select Course</option>');
        
            const validTypes = type === '1' ? ['3'] : ['1', '2'];
        
            courseOptions.each(function () {
                const option = $(this);
                const dataType = option.data('type');
                if (!dataType || validTypes.includes(dataType.toString())) {
                    courseSelect.append(option.clone());
                }
            });
        
            // Update label
            courseLabel.text(`Course (${type === '1' ? 'Live' : 'Self Learning'})`);
        
            // Show/hide Live-specific fields
            if (type === '1') {
                liveCourseSection.removeClass('d-none');
            } else {
                liveCourseSection.addClass('d-none');
            }
        
            // Toggle visibility and required rules
            toggleFieldVisibilityAndValidation(type === '1'); // true if Live
            // Toggle required fields
         
        }
    

         // === NEW: Toggle Visibility & Required Attributes Based on Course Type ===
      
           function toggleFieldVisibilityAndValidation(isLive) {
            const excludedFromRequired = ['#school_grade', '#parent_name'];
            conditionalFields.forEach(selector => {
                const $field = $(selector);
                const $row = $field.closest('.col-md-6'); // Each field is wrapped in .col-md-6

                if (isLive) {
                    // Show and require for Live courses
                    $row.removeClass('d-none');


                    // ❌ Do NOT make these required
                    if (!excludedFromRequired.includes(selector)) {
                        $field.attr('required', 'required');
                    }

                    // $field.attr('required', 'required');
                } else {
                    // Hide and remove requirement for Self-Learning
                    $field.removeAttr('required');
                    $field.removeClass('is-invalid'); // Clear error state
                    $row.addClass('d-none');
                }

            });

              // Handle WhatsApp Number (#phone): required only in Live
            const $phone = $('#phone');
            if (isLive) {
                $phone.attr('required', 'required');
            } else {
                $phone.removeAttr('required');
                $phone.removeClass('is-invalid'); // Clear validation state
            }

        }


      
    
        // === Validate Course Dropdown ===
        courseSelect.on('change', function () {
            if ($(this).val()) {
                $(this).removeClass('is-invalid');
                courseError.addClass('d-none');
            } else {
                $(this).addClass('is-invalid');
                courseError.removeClass('d-none');
            }
        });
    
        // === Handle Radio Button Changes ===
        courseTypeInputs.on('change', function () {
            const type = $(this).val();
            filterCourses(type);
        });
    
        // === Initial Load Setup ===
        const selectedRadio = $('.course-type:checked');
        if (selectedRadio.length) {
            const type = selectedRadio.val();
            filterCourses(type);
        } else {
            courseSection.addClass('d-none');
            liveCourseSection.addClass('d-none');
        }
    });



    $(document).ready(function () {
        let preType = $("#preCourseType").val();
        let preSlug = $("#preCourseSlug").val();

        // Step switching function
        function goToStep(stepNumber) {
            $(".step").hide();
            $("#step-" + stepNumber).show();
        }


        // Reusable function to fetch course details by slug
        function getCourseDetails(slug, callback) {
            $.ajax({
                url: '/get-course-details/' + slug,
                type: 'GET',
                success: function (data) {
                    if (typeof callback === "function") {
                        callback(data);
                    }
                },
                error: function () {
                    alert("Unable to fetch course details.");
                }
            });
        }

        // If both pre-filled
        if (preType && preSlug) {
            // Populate Step 1 and Step 2 fields automatically
            $("#course_type").val(preType).trigger('change');
            nextStep();
            // Use the function here
            getCourseDetails(preSlug, function (data) {
                $("#course").val(data.id);

                // Jump directly to Step 3
                nextStep();
            });
        }

    });
    var input = document.querySelector("#phone");
     let iti;

    // errorMsg = document.querySelector("#error-msg")
    // validMsg = document.querySelector("#valid-msg");

    var errorMap = [
        "Invalid number",
        "Invalid country code",
        "Too short",
        "Too long",
        "Invalid number"
    ];
    var submitBtn = document.querySelector("#submitButton");
    // var fullPhoneInput = document.querySelector("#full_phone");

    const initPhoneInput = (countryCode) => {
        iti = window.intlTelInput(input, {
            initialCountry: countryCode,
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@17.0.8/build/js/utils.js"
        });
  
             input.addEventListener("countrychange", () => {
            const code = window.intlTelInputGlobals.getInstance(input).getSelectedCountryData().iso2;
            localStorage.setItem("userCountry", code);
        });

    }


    // const initPhoneInput = (countryCode) => {
    //     window.intlTelInput(input, {
    //         initialCountry: countryCode,
    //         utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@17.0.8/build/js/utils.js"
    //     });

    //     input.addEventListener("countrychange", () => {
    //         const code = window.intlTelInputGlobals.getInstance(input).getSelectedCountryData().iso2;
    //         localStorage.setItem("userCountry", code);
    //     });
    // };

    const savedCountry = localStorage.getItem("userCountry");

    if (savedCountry) {
        initPhoneInput(savedCountry);
    } else {
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const {
                    latitude,
                    longitude
                } = pos.coords;
                fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latitude}&lon=${longitude}`)
                    .then(res => res.json())
                    .then(data => {
                        const code = (data?.address?.country_code || "pk").toLowerCase();
                        localStorage.setItem("userCountry", code);
                        initPhoneInput(code);
                    })
                    .catch(() => initPhoneInput("pk"));
            },
            () => initPhoneInput("pk"), {
                timeout: 5000
            }
        );
    }


    var reset = () => {
        input.classList.remove("error");
    };


    // Run validation logic
    const validateOnBlur = () => {
        reset();
        const value = input.value.trim();
        if (value) {
            if (iti.isValidNumber()) {
                const countryData = iti.getSelectedCountryData();
                document.querySelector("#area_code_2").value = countryData.dialCode || "";
                input.classList.remove("is-invalid", "error");
                input.classList.add("is-valid");
                submitBtn.disabled = false;
            } else {
                input.classList.remove("is-valid");
                input.classList.add("is-invalid", "error");
                document.querySelector("#area_code_2").value = "";
                submitBtn.disabled = true;
            }
        }
    };


    // On blur
    input.addEventListener('blur', validateOnBlur);

    // On flag change (so area code updates)
    input.addEventListener('countrychange', validateOnBlur);



    // on keyup / change flag: reset
    input.addEventListener('change', reset);
    input.addEventListener('keyup', reset);


    window.addEventListener("DOMContentLoaded", () => {
        const prefilled = input.value.trim();
        if (prefilled) {
            iti.setNumber(prefilled); // auto selects country flag
            validateOnBlur(); // trigger validation on load
        }
    });


    
</script>

<script> 
</script>



<script>
    let currentStep = 1;
    const totalSteps = 4;

    // Declare variables in the global scope
    let courseSelect, summaryAlert, typeSpan, nameSpan, studentNameSpan, studentEmailSpan, studentPhoneSpan;

    document.addEventListener('DOMContentLoaded', function() {
        const courseTypeInputs = document.querySelectorAll('.course-type');
        const courseSection = document.querySelector('.course-section');
        // const courseSelect = document.getElementById('course');
        courseSelect = document.getElementById('course'); // <-- Assign the global variable
        const courseLabel = document.querySelector('.course-label');
        const courseOptions = Array.from(courseSelect.options);
        const liveCourseSection = document.querySelector('.live-course-sections');
        const courseError = document.getElementById('course-error');

        // Add event listener for course select validation
        courseSelect.addEventListener('change', function() {
            if (!this.value) {
                this.classList.add('is-invalid');
                courseError.classList.remove('d-none');
            } else {
                this.classList.remove('is-invalid');
                courseError.classList.add('d-none');
            }
        });

        // Function to filter courses based on type
        function filterCourses(type) {
            courseSection.classList.remove('d-none');
            courseSelect.innerHTML = '<option value="">Select Course</option>';

            // Map study type to course type
            // If type is 1 (Live radio), show type 3 courses
            // If type is 2 (Self Learning radio), show type 1 and 2 courses
            const validTypes = type === '1' ? ['3'] : ['1', '2'];

            courseOptions.forEach(option => {
                if (option.value === '' || validTypes.includes(option.dataset.type)) {
                    courseSelect.appendChild(option.cloneNode(true));
                }
            });

            // Update label based on type
            courseLabel.textContent = `Course (${type === '1' ? 'Live' : 'Self Learning'})`;

            // Show/hide live course sections based on type
            if (type === '1') {
                liveCourseSection.classList.remove('d-none');
            } else {
                liveCourseSection.classList.add('d-none');
            }
        }

        // Event listener for course type selection
        courseTypeInputs.forEach(input => {
            input.addEventListener('change', function() {
                filterCourses(this.value);
            });
        });

        // Initial load - if a course type is already selected
        const selectedType = document.querySelector('.course-type:checked');
        if (selectedType) {
            filterCourses(selectedType.value);
        } else {
            courseSection.classList.add('d-none');
            liveCourseSection.classList.add('d-none');
        }

        
     
          // Also assign the other global variables needed for updateSummary
        summaryAlert = document.getElementById('course-summary-alert');
        typeSpan = document.getElementById('selected-course-type');
        nameSpan = document.getElementById('selected-course-name');
        studentNameSpan = document.getElementById('student-name-display');
        studentEmailSpan = document.getElementById('student-email-display');
        studentPhoneSpan = document.getElementById('student-phone-display');
    });


    function updateSummary() {
                
                    
                    // Get selected course type
                    // var selectedType = document.querySelector('input[name="study_type[]"]:checked');
                    var selectedType = document.querySelector('input[name="study_type"]:checked');
                    var type = '';
                    if (selectedType) {
                        type = selectedType.value === '1' ? 'Live' : 'Self Learning';
                    }
                    
                    // Get selected course name
                    var name = '';
                    if (courseSelect && courseSelect.selectedIndex > 0) {
                        name = courseSelect.options[courseSelect.selectedIndex].text;
                    }

                     // Input field where student name is entered (adjust ID if different)
                    var studentNameInput = document.getElementById('fullname').value;
                    var studentEmailInput = document.getElementById('email').value;
                    var studentPhoneInput = document.getElementById('area_code_2').value + document.getElementById('phone').value;

                  
                    // var studentNameSpan = document.getElementById('student-name-display');
                    // var studentEmailSpan = document.getElementById('student-email-display');
                    // var studentPhoneSpan = document.getElementById('student-phone-display');

                    // Update display
                    if (type && name) {
                        typeSpan.textContent = type;
                        nameSpan.textContent = name;
                        studentNameSpan.textContent = studentNameInput;
                        studentEmailSpan.textContent = studentEmailInput;
                        studentPhoneSpan.textContent = studentPhoneInput;
                        summaryAlert.style.display = 'block';
                    } else {
                        summaryAlert.style.display = 'none';
                    }
    }

    function updateProgressBar() {
        // Update step circles and lines
        document.querySelectorAll('.step-circle').forEach((circle, index) => {
            if (index + 1 < currentStep) {
                circle.classList.add('completed');
                circle.classList.add('active');
            } else if (index + 1 === currentStep) {
                circle.classList.add('active');
                circle.classList.remove('completed');
            } else {
                circle.classList.remove('active', 'completed');
            }
        });

        // Update connecting lines
        document.querySelectorAll('.step-line').forEach((line, index) => {
            if (index + 1 < currentStep) {
                line.classList.add('active');
            } else {
                line.classList.remove('active');
            }
        });
    }



    function showStep(step) {
        // Hide all steps
        document.querySelectorAll('.form-step').forEach(el => {
            el.classList.add('d-none');
        });

        // Show current step
        document.getElementById(`step${step}`).classList.remove('d-none');

        // Update buttons
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');

        prevBtn.classList.toggle('d-none', step === 1);
        nextBtn.classList.toggle('d-none', step === totalSteps);
        submitBtn.classList.toggle('d-none', step !== totalSteps);

        if(step === totalSteps){
        
            updateSummary();
        }
       
        updateProgressBar();
    }

    function validateStep(step) {
        const currentStepElement = document.getElementById(`step${step}`);
        const requiredFields = currentStepElement.querySelectorAll('[required]');
        let isValid = true;

        // Special validation for step 1 - check radio buttons
        if (step === 1) {
            const courseTypeInputs = document.querySelectorAll('.course-type');
            const isRadioSelected = Array.from(courseTypeInputs).some(input => input.checked);
            
            if (!isRadioSelected) {
                isValid = false;
                // Add visual feedback
                courseTypeInputs.forEach(input => {
                    input.closest('.form-check').classList.add('is-invalid');
                });
                // Show error message
                const errorDiv = document.createElement('small');
                errorDiv.className = 'text-danger aileron d-block text-start mt-2 radio-error';
                errorDiv.textContent = 'Please select a course type';
                
                // Remove existing error message if any
                const existingError = currentStepElement.querySelector('.radio-error');
                if (existingError) {
                    existingError.remove();
                }
                
                courseTypeInputs[0].closest('.row').appendChild(errorDiv);
            } else {
                // Remove error styling and message if selection is made
                courseTypeInputs.forEach(input => {
                    input.closest('.form-check').classList.remove('is-invalid');
                });
                const existingError = currentStepElement.querySelector('.radio-error');
                if (existingError) {
                    existingError.remove();
                }
            }
        }

        // Special validation for step 2 - check course selection
        if (step === 2) {
            const courseSelect = document.getElementById('course');
            const courseError = document.getElementById('course-error');
            const courseSection = document.querySelector('.course-section');
            
            // Only validate if course section is visible
            if (!courseSection.classList.contains('d-none')) {
                if (!courseSelect.value) {
                    isValid = false;
                    courseSelect.classList.add('is-invalid');
                    courseError.classList.remove('d-none');
                } else {
                    courseSelect.classList.remove('is-invalid');
                    courseError.classList.add('d-none');
                }
            }
        }

        // Regular validation for required fields
        requiredFields.forEach(field => {
            if (!field.value) {
                isValid = false;
                field.classList.add('is-invalid');
            } else {
                field.classList.remove('is-invalid');
            }
        });

        return isValid;
    }

    function nextStep() {
        if (validateStep(currentStep) && currentStep < totalSteps) {
            currentStep++;
            showStep(currentStep);
        }
    }

    function prevStep() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    }

    // Initialize form
    document.addEventListener('DOMContentLoaded', () => {
        showStep(1);

        // Add validation listeners to required fields
        document.querySelectorAll('[required]').forEach(field => {
            field.addEventListener('input', () => {
                if (field.value) {
                    field.classList.remove('is-invalid');
                }
            });
        });
    });
</script>









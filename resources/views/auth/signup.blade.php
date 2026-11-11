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
@endsection

@section('content')
<div class="signupPage">
    <!-- Page Header Start -->
    <header class="breadcrumb-section">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">
                        <i class="fas fa-home"></i>
                        Home
                    </a>
                </li>
                <li class="breadcrumb-separator"> &gt; </li>
                <li class="breadcrumb-item active" aria-current="page">Sign Up</li>
            </ol>
        </nav>
    </header>
    <!-- Page Header End -->

    <section>
        <div class="container d-flex justify-content-center align-items-center">
            <div class="row p-4 bg-white justify-content-center" style="width: 100%;">
                <div class="col-md-6 mr-4">
                    <div class="mb-3">
                        <h1 class="fs-5 text-black font-bold">Join CEF</h1>
                        <p style="font-size: 14px">Create your account</p>
                    </div>
                    <div class="btn-group w-100 mt-2 mb-4" role="group">
                        <a href="{{ route('login') }}" class="btn btn-light w-50 active font-bold">Sign In</a>
                        <a href="{{ route('sign-up-as-student') }}" class="btn btn-light w-50 font-bold ">Sign Up</a>
                    </div>

                    <form method="POST" action="">
                        @csrf

                        <div class="row">
                            <!-- Full Name -->
                            <div class="col-md-6 mb-3">
                                <label for="fullname" class="text-sm text-black font-bold">Full Name</label>
                                <input type="text" name="fullname" class="form-control" id="fullname" 
                                       value="{{ old('fullname') }}" placeholder="Enter your full name" required>
                                @error('fullname') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                    
                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="text-sm text-black font-bold">Email</label>
                                <input type="email" name="email" class="form-control" id="email" 
                                       value="{{ old('email') }}" placeholder="Enter your email" required>
                                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    
                        <div class="row mb-3">
                            <!-- Password -->
                            <div class="col-md-6">
                                <label for="password" class="text-sm text-black font-bold">Password</label>
                                <div class="input-group">
                                    <input type="password" name="password" class="form-control" id="password" placeholder="********" required>
                                    <span class="password-toggle">
                                        <i class="fas fa-eye" id="togglePassword" style="cursor: pointer;"></i>
                                    </span>
                                </div>
                                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                    
                            <!-- Confirm Password -->
                            <div class="col-md-6">
                                <label for="password_confirmation" class="text-sm text-black font-bold">Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="********" required>
                                    <span class="password-toggle">
                                        <i class="fas fa-eye" id="togglePasswordConfirm" style="cursor: pointer;"></i>
                                    </span>
                                </div>
                                @error('password_confirmation') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    
                        <div class="row mb-3">
                            <!-- Phone Number -->
                            <div class="col-md-6">
                                <label for="phone" class="text-sm text-black font-bold">Phone Number</label>
                                <input type="text" name="phone" class="form-control" id="phone" 
                                       value="{{ old('phone') }}" placeholder="Enter your phone number" required>
                                @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                    
                            <!-- Timezone -->
                            <div class="col-md-6">
                                <label for="timezone" class="text-sm text-black font-bold">Timezone</label>
                                <select name="timezone" class="form-control" id="timezone" required>
                                    <option value="">Select Timezone</option>
                                    @foreach($timeZonesArray as $timezone)
                                        <option value="{{ $timezone }}" {{ old('timezone') == $timezone ? 'selected' : '' }}>{{ $timezone }}</option>
                                    @endforeach
                                </select>
                                @error('timezone') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    
                        <div class="row mb-3">
                            <!-- Country -->
                            <div class="col-md-6">
                                <label for="country" class="text-sm text-black font-bold">Country</label>
                                <select name="country" class="form-control" id="country" required>
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}" {{ old('country') == $country->id ? 'selected' : '' }}>
                                            {{ $country->country_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('country') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                    
                            <!-- Referral Code -->
                            <div class="col-md-6">
                                <label for="referral" class="text-sm text-black font-bold">Referral Code</label>
                                <input type="text" name="referral" class="form-control" id="referral" 
                                       value="{{ old('referral') }}" placeholder="Enter referral code">
                            </div>
                        </div>
                    
                        <div class="row mb-3">
                            <!-- Study Type Checkboxes -->
                            <div class="col-md-6 px-4">
                                <div class="form-check form-check-inline me-3">
                                    <input class="form-check-input" id="liveCheckbox" type="checkbox" name="study_type[]" value="1" {{ old('study_type') && in_array('1', old('study_type')) ? 'checked' : '' }} >
                                    <label class="form-check-label" for="liveCheckbox">Live</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" id="selfLearningCheckbox" type="checkbox" name="study_type[]" value="2" {{ old('study_type') && in_array('2', old('study_type')) ? 'checked' : '' }} >
                                    <label class="form-check-label" for="selfLearningCheckbox">Self Learning</label>
                                </div>
                                @error('study_type') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    
                        <div class="row mb-3">
                            <!-- What will you use CEF for -->
                            <div class="col-md-6">
                                <label for="what_will_you_use" class="text-sm text-black font-bold">What will you use CEF for?</label>
                                <select name="what_will_you_use" class="form-control" id="what_will_you_use" required>
                                    <option value="">Select</option>
                                    <option value="quran" {{ old('what_will_you_use') == 'quran' ? 'selected' : '' }}>Quran</option>
                                    <option value="islamic_studies" {{ old('what_will_you_use') == 'islamic_studies' ? 'selected' : '' }}>Islamic Studies</option>
                                    <option value="arabic" {{ old('what_will_you_use') == 'arabic' ? 'selected' : '' }}>Arabic</option>
                                    <option value="others" {{ old('what_will_you_use') == 'others' ? 'selected' : '' }}>Others</option>
                                </select>
                                @error('what_will_you_use') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                    
                            <!-- How many students -->
                            <div class="col-md-6">
                                <label for="how_many_students" class="text-sm text-black font-bold">How many students will join?</label>
                                <input type="number" name="how_many_students" class="form-control" 
                                       value="{{ old('how_many_students') }}" placeholder="Enter number of students" required>
                                @error('how_many_students') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>


                        <div class="container">
                            <div class="row">
                                <div class="col-md-12" id="calendar-picker">
                                    <div class=" mb-4">
                                        <h6 class=" mt-3 mb-4 calendar-text">Pick a date and time for 1 hour free trial lesson</h6>
                                        <div id="wrapper" class="wrapper mb-2">
                                            <div class="container-calendar">
                                                <div class="d-flex justify-content-between align-items-center px-2 mb-3">
                                                    <h6 class="month-year-display text-center" id="monthAndYear"></h6>
                                                    <div class="button-container-calendar">
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
                                    {{-- <div class="d-none" id="step-4">
                                        <h6 class="color-black font-bold mt-3">Available Time Slot</h6>
                                        <p class="text-muted font-semibold" style="font-size:14px ">2 PM to 4 PM</p>
                                    
                                        <div id="slots" class="time-slot-container mb-4">
                                            <label class="slot">
                                                <input type="radio" name="preferTime" value="2:00 PM"> <span>2:00 PM</span>
                                            </label>
                                            <label class="slot">
                                                <input type="radio" name="preferTime" value="2:30 PM"> <span>2:30 PM</span>
                                            </label>
                                            <label class="slot">
                                                <input type="radio" name="preferTime" value="3:00 PM"> <span>3:00 PM</span>
                                            </label>
                                            <label class="slot">
                                                <input type="radio" name="preferTime" value="3:30 PM"> <span>3:30 PM</span>
                                            </label>
                                            <label class="slot">
                                                <input type="radio" name="preferTime" value="4:00 PM"> <span>4:00 PM</span>
                                            </label>
                                        </div>
                                    </div> --}}
                                    


                                </div>
                            </div>
                        </div>
                        
                        
                        <button type="submit" class="theme-btn theme-button1 theme-button3 w-100 font-15 fw-bold">Sign Up</button>
                    </form>
                </div>

                <div class="col-md-5 d-none d-md-block bg-secondary rounded-4 reduced-height">
                    <!-- Placeholder for image/illustration -->
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
<script>
document.addEventListener("DOMContentLoaded", function () {
    let currentMonth, currentYear, monthAndYear;
    const today = new Date();
    const liveCheckbox = document.getElementById("liveCheckbox");
    const calendarWrapper = document.getElementById("calendar-picker");
    const togglePassword = document.querySelector('#togglePassword');
    const passwordField = document.querySelector('#password');

    const togglePasswordConfirm = document.querySelector('#togglePasswordConfirm');
    const passwordConfirmField = document.querySelector('#password_confirmation');

    currentMonth = today.getMonth();
    currentYear = today.getFullYear();
    monthAndYear = document.getElementById("monthAndYear");

    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    const dayNames = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

    let dataHead = "<tr>";
    dayNames.forEach(day => dataHead += `<th>${day}</th>`);
    dataHead += "</tr>";
    document.getElementById("thead-month").innerHTML = dataHead;

    showCalendar(currentMonth, currentYear);

    document.getElementById("next").addEventListener("click", () => navigateMonth(1));
    document.getElementById("previous").addEventListener("click", () => navigateMonth(-1));

    liveCheckbox.addEventListener("change", function () {
        if (liveCheckbox.checked) {
            calendarWrapper.style.display = "block";  // Show calendar
        } else {
            calendarWrapper.style.display = "none";   // Hide calendar
        }
    });

    function navigateMonth(direction) {
        currentYear = (currentMonth === 11 && direction === 1) ? currentYear + 1 : (currentMonth === 0 && direction === -1) ? currentYear - 1 : currentYear;
        currentMonth = (currentMonth + direction + 12) % 12;
        showCalendar(currentMonth, currentYear);
    }

    function showCalendar(month, year) {
        const firstDay = new Date(year, month).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const tbl = document.getElementById("calendar-body");
        tbl.innerHTML = "";
        monthAndYear.innerHTML = `${monthNames[month]} ${year}`;

        let date = 1;

        for (let i = 0; i < 6; i++) {
            const row = document.createElement("tr");
            for (let j = 0; j < 7; j++) {
                const cell = document.createElement("td");
                cell.classList.add("calendar-cell");

                if (i === 0 && j < firstDay) {
                    cell.classList.add("empty-cell");
                } else if (date > daysInMonth) {
                    break;
                } else {
                    cell.innerHTML = `<span>${date}</span>`;
                    if (new Date(year, month, date) >= today) {
                        cell.classList.add("active-cell");
                        cell.addEventListener("click", selectDate);
                    } else {
                        cell.classList.add("disabled-cell");
                    }
                    date++;
                }
                row.appendChild(cell);
            }
            tbl.appendChild(row);
        }
    }

    function selectDate() {
        document.querySelectorAll(".calendar-cell").forEach(td => td.classList.remove("selected"));
        this.classList.add("selected");

        const selectedDate = this.querySelector("span").innerText;
        document.getElementById("preferDate").value = `${monthAndYear.innerText} ${selectedDate}`;

        document.getElementById("step-4").classList.remove("d-none");
    }

    // Initially hide the calendar if Live checkbox isn't selected
    if (!liveCheckbox.checked) {
        calendarWrapper.style.display = "none";
    }

    
    // Toggle password visibility for the password field
    togglePassword.addEventListener('click', function () {
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        this.classList.toggle('fa-eye-slash');
    });

    // Toggle password visibility for the confirmation field
    togglePasswordConfirm.addEventListener('click', function () {
        const type = passwordConfirmField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordConfirmField.setAttribute('type', type);
        this.classList.toggle('fa-eye-slash');
    });
});




</script>
<style>
    .signupPage {
        background: #f8f9fa;
        padding-top: 90px !important;
    }

    .breadcrumb-section {
        background: #F0F5F2 !important;
        padding-top: 25px !important;
        padding-bottom: 5px !important;
        padding-left: 95px !important;
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
        height: 550px !important; /* Lower height for the left section */
    }
  
 

/* Change hover and selected cell appearance */

select.form-control {
    -webkit-appearance: menulist; /* Ensures dropdown arrow appears */
    -moz-appearance: menulist;
    appearance: menulist;
}


.table-calendar td:hover span,
.selected-cell span {
    background-color: #0a3a75;  /* Blue on selection */
    color: #fff;
    border: 1px solid #0a3a75;
    border-radius: 50%;
    padding: 4px;
}
.calendar-text {
    font-size: 16px !important;
    font-weight: 700 !important;
    color: #000000 !important;
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

th, td {
    max-width: 4rem !important;
    min-width: 4rem !important;
    text-align: center;
    height: 3rem !important; /* Compact height */
    padding: 4px !important;    /* Minimal padding */
    font-size: 16px !important; /* Smaller font */
    border: 1px solid #dee2e6;
    color: #000000;
}

/* Style for the selected and hover states */
.date-picker {
    cursor: pointer;
    text-align: center;
    padding: 5px;    /* Small padding to keep circular structure */
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
#next, #previous {
    font-size: 1.5rem !important;
    padding: 4px 8px;
}

/* Time Slot Container: Full Width with 4 slots per row */
.time-slot-container {
    display: grid;              /* Use Grid layout */
    grid-template-columns: repeat(4, 1fr);  /* 4 slots in a row */
    gap: 10px;                  /* Add some space between slots */
    width: 100%;                /* Full width of the container */
}

/* Slot Styling */
.slot {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 8px;
    border: 2px solid #ccc;     /* Default gray border */
    border-radius: 5px;
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 500;
    transition: background-color 0.3s, border-color 0.3s;
}

/* Hide default radio button */
.slot input {
    display: none;              /* Hide the radio button */
}

/* Style for selected slots (blue border when checked) */
.slot input:checked + span {
    border: 2px solid #0a3a75;  /* Blue border when checked */
    color: white;
    padding: 8px;
    border-radius: 5px;
}

/* Slot hover effect */
.slot:hover {
    border-color: #0a3a75;      /* Blue border on hover */
}



</style>

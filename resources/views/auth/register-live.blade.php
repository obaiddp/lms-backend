@extends('layouts.auth') @section('content') 
<section style="background-color: #000000" class="auth-section">
    <img src="{{getImageFile(get_option('sign_up_left_image'))}}" alt="hero" class="auth-img img-fluid">
    <div  class="auth-div">
        <a href="/" class="btn"
            style="z-index: 99999; position: absolute; top: 1rem; left: 1rem; color: black; background-color: rgba(255, 255, 255, 0.6); padding: 6px 10px; border: none; border-radius: 5px; cursor: pointer;">
            <span class="fas fa-arrow-left"></span> {{ __('Back') }}
        </a>
        <div class="sign-up-form">
            <h5>Let's get your Journey Started</h5>
            <ul class="progressBar my-2">
                @php
                    $isVisible = 1;
                @endphp
                @if ($isVisible)
                    <li>
                        <div class="step first active">
                            <p>1</p>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="transparent"
                                class="bi bi-check-lg" viewBox="0 0 16 16">
                                <path
                                    d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z" />
                            </svg>
                        </div>
                    </li>
                @endif

                <li>
             
                    <div class="step second">
                        <p>2</p>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="transparent"
                            class="bi bi-check-lg" viewBox="0 0 16 16">
                            <path
                                d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z" />
                        </svg>
                    </div>
                </li>

                <li>
        
                    <div class="step third">
                        <p>3</p>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="transparent"
                            class="bi bi-check-lg" viewBox="0 0 16 16">
                            <path
                                d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z" />
                        </svg>
                    </div>
                </li>
            </ul>
            <form id="multiStepForm" class="w-100 px-4">
                <input type="hidden" name="token" id="token" value="{{ csrf_token() }}">
                <input type="hidden" name="new_student_id" id="new_student_id" value="{{$studentID}}">
                <input type="hidden" name="new_trial_id" id="new_trial_id" value="">
        
                <input type="hidden" name="check_status" id="check_status" value="step_1_create">
                <input type="hidden" name="type_st" id="type_st" value="{{$type}}">
                <input type="hidden" name="slug" id="slug" value="{{$slug}}">
                <input type="hidden" name="isUser" id="isUser" value="{{$isUser}}">
                <input type="hidden" name="userID" id="userID" value="{{$userID}}">
                
    
                <div class="step-content mt-3 active" id="step-1">
                    <h6 class="text-center mb-4 text-white">Your personal details</h6>
                    <div class="auth-input mb-3">
                        <input name="fname" id="fname" type="text" class="form-control form-svg"
                            placeholder="First Name" aria-label="First Name" aria-describedby="FirstName" required>
                        <span class="fas fa-user"></span>
                        <span class="error-message" id="fname_error"></span>
                    </div>
                    <div class="auth-input mb-3">
                        <input name="lname" id="lname" type="text" class="form-control form-svg" placeholder="Last Name"
                            aria-label="Last Name" aria-describedby="LastName" required>
                        <span class="fas fa-user"></span>
                        <span class="error-message" id="lname_error"></span>
                    </div>
                    <div class="auth-input mb-3">
                        <input type="email" name="email" id="email" class="form-control form-svg" placeholder="Email"
                            aria-label="Email" aria-describedby="Email">
                        <span class="fas fa-envelope"></span>
                        <span class="error-message" id="email_error"></span>
                    </div>
                    <div class="auth-input mb-3">
                        <input type="password" name="password" id="password" class="form-control password"
                            placeholder="*********">
                        <span class="toggle cursor fas fa-eye pass-icon"></span> <span class="error-message"
                            id="password_error"></span>
                    </div>
                    <div class="auth-input mb-3">
                        <select class="time-zone" name="time_zone" id="time_zone" required>
                            <option value ="">{{ __("Select Time Zone") }}</option>
                            @if (!empty($timeZonesArray))
                                @foreach ($timeZonesArray as $key => $timezone)
                                    <option value="{{$key}}"
                                    {{-- @if($instructor->user->time_zone== $key) 
                                        selected 
                                    @endif --}}
                                    >
                                        {{$timezone}}
                                    </option>
                                @endforeach
                            @endif
                          
                         
                        </select>
                        <span class="error-message" id="time_zone_error"></span>
                    </div>
                    <div class="input-group mb-3">
                        <input id="phone" class="form-control" type="tel" name="phone" />
                        <span class="error-message" id="phone_error"></span>
                    </div>
                    <input type="hidden" name="area_code" id="area_code" value="">
                    <div class="auth-input mb-3">
                        <select id="country-id" name="country_id" placeholder="Select Country">
                            <option value="">Select Country</option> @if (!$countries->isEmpty()) @foreach ($countries
                            as $country) <option value="{{$country->id}}">{{$country->country_name}}</option>
                            @endforeach @endif
                        </select>
                        <span class="error-message" id="country_id_error"></span>
                    </div>
                    <div class="auth-input mb-3">
                        <input id="referral_code" name="referral_code" type="text" class="form-control"
                            placeholder="Referral/Promo Code" aria-label="Referral/Promo Code" aria-describedby="Code">
                        <span class="error-message" id="referral_code_error"></span>
                    </div>
                    <div hidden class="learning">
                        <label style="font-size:15px" for="1">Live</label>
                        <input class="st_type" name="st_type" type="checkbox" id="1" value="1" @if($type==1) checked
                            @endif>
                        <label style="margin-left:15px ; font-size:15px" for="2">Self Learning</label>
                        <input class="st_type" name="st_type" type="checkbox" id="2" value="2" @if($type==2) checked
                            @endif>
                        <span class="error-message" id="st_type_error"></span>
                    </div>
                </div>
              

                <div class="step-content" id="step-2">
                    <h6 class="text-center mt-3 mb-4">Pick a date and time for your free trial lesson (1 hour)</h6>
                    <div id="wrapper" class="wrapper mb-2">
                        <div class="container-calendar">
                            <div class="d-flex justify-content-between align-items-center px-4">
                                <h6 class="w-50" id="monthAndYear"></h6>
                                <div class="button-container-calendar">
                                    {{-- <button id="previous" type="button" onclick="previous()">&#8249;</button>
                                    <button id="next" type="button" onclick="next()">&#8250;</button> --}}
                                    <button id="previous" type="button" disabled>&#8249;</button>
                                    <button id="next" type="button">&#8250;</button>
                                </div>
                            </div>
                            <table class="table-calendar" id="calendar" data-lang="en">
                                <thead id="thead-month"></thead>
                                <tbody id="calendar-body"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="preferDate" id="preferDate" value="">
                <input type="hidden" name="selectedDate" id="selectedDate" value="">
                <input type="hidden" name="selectedMonth" id="selectedMonth" value="">
                <input type="hidden" name="selectedYear" id="selectedYear" value="">
                <div class="step-content" id="step-3">
                    <h6 class="text-center mt-3 mb-2">Select Slot</h6>
                    <div id="slots" class="slots mb-2 mt-4">
                        <div class="w-80 mx-auto d-flex flex-col flex-wrap">
                            <div class="slot">
                                <label for="1:00 AM">
                                    <input type="radio" name="preferTime" id="1:00 AM" value="1:00 AM" checked> 1:00 AM
                                </label>
                            </div>
                            <div class="slot">
                                <label for="12:00 PM">
                                    <input type="radio" name="preferTime" id="12:00 PM" value="12:00 PM"> 12:00 PM
                                </label>
                            </div>
                            <div class="slot">
                                <label for="12:15 PM">
                                    <input type="radio" name="preferTime" id="12:15 PM" value="12:15 PM"> 12:15 PM
                                </label>
                            </div>
                            <div class="slot">
                                <label for="12:30 PM">
                                    <input type="radio" name="preferTime" id="12:30 PM" value="12:30 PM"> 12:30 PM
                                </label>
                            </div>
                            <div class="slot">
                                <label for="11:30 PM">
                                    <input type="radio" name="preferTime" id="11:30 PM" value="11:30 PM"> 11:30 PM
                                </label>
                            </div>
                            {{-- <div class="slot">11:30 PM</div> --}}
                        </div>
                    </div>
                </div>

            </form>
            <div class="button-container w-100 px-4">
                <button id="nextBtn" class="theme-btn btn btn-primary w-100 my-2">Next</button>
                <button id="prevBtn" class="theme-btn btn btn-secondary w-100" disabled>Previous</button>
                <span id="message" style="display:none"></span>
            </div>
        </div>
    </div>
</section> @endsection <script src="{{asset('frontend/assets/vendor/jquery/jquery-3.6.0.min.js')}}">
</script>
<script src="{{asset('frontend/assets/vendor/jquery/popper.min.js')}}"></script>
<script>
    const spinner =
    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
    // Global variables
        var currentMonth, currentYear, monthAndYear;
        var months = [];
        var days = [];

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

            var monthDefault = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            var dayDefault = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

            if (lang == "en") {
                months = monthDefault;
                days = dayDefault;
            } else if (lang == "id") {
                months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                days = ["Ming", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"];
            } else if (lang == "fr") {
                months = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
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

        // var container = $('#calendar-body');
        function showCalendar(month, year) {
          
            var selectedDate=$('#selectedDate').val();
            var selectedMonth=$('#selectedMonth').val();
            // selectedMonth = (parseInt(selectedMonth) + parseInt(1))
            var selectedYear=$('#selectedYear').val();
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
                      
                        if (selectedDate === date  && selectedYear === year && selectedMonth === month) {
                       
                            // alert(selectedDate+'-----'+date)
                            // alert(selectedYear+'-----'+year)
                            // alert(selectedMonth+'-----'+month)
                            var dateValue = year+'-'+ (parseInt(month)+ parseInt(1))+'-'+date;
                            $('#preferDate').val(dateValue)
                            cell.className = "date-picker selected";
                        }
                        else if (date === today.getDate() && year === today.getFullYear() && month === today.getMonth()) {
                            // alert(selectedDate+'-----'+date)
                            // alert(selectedYear+'-----'+year)
                            // alert(selectedMonth+'-----'+month)
                            var dateValue = year+'-'+ (parseInt(month)+ parseInt(1))+'-'+date;
                            $('#preferDate').val(dateValue)
                            cell.className = "date-picker selected";
                        }
                        else if(year < today.getFullYear()){
                            cell.setAttribute('disabled', 'true');
                            cell.className = "disabled-td";
                        }
                        else if(year === today.getFullYear() && month < today.getMonth()){
                            cell.setAttribute('disabled', 'true');
                            cell.className = "disabled-td";
                        }
                        else if(month === today.getMonth() && date < today.getDate()){
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

        $(document).ready(function () {
            $("#calendar-body").on('click', 'tr td', function() {
    //Use .text() as td doesn't have method .val()
    //Empty first time as the td:first has no text until clicked.
  
    $("#calendar-body tr td").removeClass('selected')
  
    var date = $(this).attr('data-date');
    var month = $(this).attr('data-month');
    var year = $(this).attr('data-year');
 
    if(date === ""){
    
        $(this).attr('disabled', 'true');
        $(this).addClass('disabled-td')
    }
    else if(year < today.getFullYear()){
        $(this).attr('disabled', 'true');
        $(this).addClass('disabled-td')
    }
    else if(year === today.getFullYear() && month < today.getMonth()){
        $(this).attr('disabled', 'true');
        $(this).addClass('disabled-td')
    }
    else if(month === today.getMonth() && date < today.getDate()){
        $(this).attr('disabled', 'true');
        $(this).addClass('disabled-td')
    }
    else{
        var dateValue = year+'-'+ (parseInt(month))+'-'+date;
        $('#preferDate').val(dateValue)
        $('#selectedDate').val(date)
        $('#selectedMonth').val(month)
        $('#selectedYear').val(year)
        $(this).addClass('date-picker selected');
    }
  
  
 
  
});
        });
    


         function daysInMonth(month, year) {
            // Create a date object for the 1st of the next month and get the last day of the previous month
            const date = new Date(year, month + 1, 0);
            // Return the day of the month for the last day of the month
            return date.getDate();
        }
</script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        var isUser = $("#isUser").val();
  
        const phoneInputField = document.querySelector("#phone");
        var countryCodeInput = document.querySelector("#area_code");
        const phoneInput = window.intlTelInput(phoneInputField, {
            utilsScript:
            "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        });
        phoneInputField.addEventListener('countrychange', function(e) {
            var countryData = phoneInput.getSelectedCountryData();
            countryCodeInput.value = countryData.dialCode;
        });

        // Step navigation logic

            var first = document.querySelector(".first");
            var second = document.querySelector(".second");
            var third = document.querySelector(".third");
            var steps = [first, second, third];
     
         
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
            var checkStatus =  $('#check_status').val();
       
    if(checkStatus == 'step_1_create'){
                var form = document.getElementById('multiStepForm');
                var formFields = form.querySelectorAll('input[name],select[name]');
                var fieldsToSubmit = ['st_type','fname','lname','email', 'password','time_zone','country_id','area_code','phone','referral_code','token']; // List the specific fields to validate
                var phoneValue = document.getElementById('phone');
             
                if(phoneValue != ""){
                    var fieldsToValidate = ['st_type','fname','lname','email', 'password','time_zone','country_id','phone']; // List the specific fields to validate
                }
                else{
                    var fieldsToValidate = ['st_type','fname','lname','email', 'password','time_zone','country_id']; // List the specific fields to validate
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

        
            fieldsToValidate.forEach(fieldName => {var field = form.querySelector(`[name="${fieldName}"]`);
            if (field && !field.value.trim()) {
                field.classList.add('error');
                var errorElement = document.getElementById(`${fieldName}_error`);
                if (errorElement) {
                    errorElement.textContent = `${fieldName.replace('_', ' ')} is required`;
                }
                isValid = false;
            }
            else if (fieldName === 'email' && field.value.trim()) {
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

        if(isValid){
            var email = $('input[name="email"]').val()

            $("#nextBtn").attr("disabled", "disabled");
        $("#message").css('color','green');
        $("#message").css('font-size','20px');
        $("#message").css('text-align','center');
        $("#message").text("Please Wait!!!!");
        $("#message").css('display','block');

            $.ajax({
                    type: "POST",
                    url: "{{route('check-student-email')}}",
                    data: {
                        email : email,
                    },
                    dataType: "JSON",
                    headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "Accept-Language": 'en',
                    },
                    success: function (response) {
                        if (!response.unique) {
                            $('input[name="email"]').addClass('error');
                                var errorElement = document.getElementById('email_error');
                                if (errorElement) {
                                    errorElement.textContent = 'Email is already taken';
                                }
                                isValid = false;

                              $("#nextBtn").removeAttr("disabled");
                              $("#message").text("");
                              $("#message").css('display','none');
                        }
                        else{
                            var phone_value = $('input[name="phone"]').val()

                            $("#nextBtn").attr("disabled", "disabled");
        $("#message").css('color','green');
        $("#message").css('font-size','20px');
        $("#message").css('text-align','center');
        $("#message").text("Please Wait!!!!");
        $("#message").css('display','block');

                            $.ajax({
                    type: "POST",
                    url: "{{route('check-student-phone')}}",
                    data: {
                        phone : phone_value,
                    },
                    dataType: "JSON",
                    headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "Accept-Language": 'en',
                    },
                    success: function (response) {
                        if (!response.unique) {
                            $('input[name="phone"]').addClass('error');
                                var errorElement = document.getElementById('phone_error');
                                if (errorElement) {
                                    errorElement.textContent = 'Phone is already taken';
                                }
                                isValid = false;
                            $("#nextBtn").removeAttr("disabled");
                              $("#message").text("");
                              $("#message").css('display','none');
                        }
                        else{
            if (isValid) {

        $("#nextBtn").attr("disabled", "disabled");
        $("#message").css('color','green');
        $("#message").css('font-size','20px');
        $("#message").css('text-align','center');
        $("#message").text("Please Wait!!!!");
        $("#message").css('display','block');
                let formData = {};
                fieldsToSubmit.forEach(fieldName => {
                var field = form.querySelector(`[name="${fieldName}"]`);
                if (field) {
                    formData[fieldName] = field.value;
                }
                });
                var st_values = [];
                $('.st_type').each(function(){
                    var $this = $(this);
                    if ($this.is(':checked')) {
                    st_values.push($this.val());
                    }
                    });
                formData['st_type'] = st_values;
                console.log(formData)
                $.ajax({
                    type: "POST",
                    url: "{{route('store.sign-up-as-student-step1')}}",
                    data: formData,
                    dataType: "JSON",
                    headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "Accept-Language": 'en',
                    },
                    success: function (response) {
                        if(response.status){
                            console.log(formData);
                              $('#check_status').val("step_2_create_free_trial");
                              $('#new_student_id').val(response.student_id);
                              $('#type_st').val(response.stType);
                              $("#nextBtn").removeAttr("disabled");
                              $("#message").text("");
                              $("#message").css('display','none');
                         
                            if (currentStep < steps.length - 1) {
                                if(response.stType == 2){ //Only Self Learning Selected
                                    $("#step-2-field-1").css('display','none');
                                    $("#step-2-field-2").css('display','none');
                                    $(".third").css('display','none');
                                }   
                                else{
                                    $("#step-2-field-1").css('display','block');
                                    $("#step-2-field-2").css('display','block');
                                    $(".third").css('display','block');
                                }
                                 currentStep++;
                                 updateStep();
                            }
                        }
                    }
                });
            
            }
            else {
            console.log('Specific fields have errors');
                                $("#nextBtn").removeAttr("disabled");
                              $("#message").text("");
                              $("#message").css('display','none');
            }
                        }
                    }
                });
                        }
                    }
                });
            } 
            else{
                console.log('Specific fields have errors'); 
                                $("#nextBtn").removeAttr("disabled");
                              $("#message").text("");
                              $("#message").css('display','none');
            }

    }
    else if(checkStatus == 'step_1_edit'){
            var studentID =  $('#new_student_id').val();
                var form = document.getElementById('multiStepForm');
                var fieldsToSubmit = ['st_type','fname','lname','email', 'password','time_zone','country_id','area_code','phone','referral_code','token','new_student_id']; // List the specific fields to validate
                var phoneValue = document.getElementById('phone');
             
                if(phoneValue != ""){
                    var fieldsToValidate = ['st_type','fname','lname','email', 'password','time_zone','country_id','phone']; // List the specific fields to validate
                }
                else{
                    var fieldsToValidate = ['st_type','fname','lname','email', 'password','time_zone','country_id']; // List the specific fields to validate
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
            }
            else if (fieldName === 'email' && field.value.trim()) {
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
        $("#message").css('color','green');
        $("#message").css('font-size','20px');
        $("#message").css('text-align','center');
        $("#message").text("Please Wait!!!!");
        $("#message").css('display','block');

            var email = $('input[name="email"]').val();
                $.ajax({
                    type: "POST",
                    url: "{{route('check-student-email-update')}}",
                    data: {
                        email : email,
                        given_student_id : studentID,
                    },
                    dataType: "JSON",
                    headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "Accept-Language": 'en',
                    },
                    success: function (response) {
                        if (!response.unique) {
                            $('input[name="email"]').addClass('error');
                                var errorElement = document.getElementById('email_error');
                                if (errorElement) {
                                    errorElement.textContent = 'Email is already taken';
                                }
                                isValid = false;
                                $("#nextBtn").removeAttr("disabled");
                              $("#message").text("");
                              $("#message").css('display','none');
                        }
                        else{

                            $("#nextBtn").attr("disabled", "disabled");
        $("#message").css('color','green');
        $("#message").css('font-size','20px');
        $("#message").css('text-align','center');
        $("#message").text("Please Wait!!!!");
        $("#message").css('display','block');


                            var phone_value = $('input[name="phone"]').val();
                $.ajax({
                    type: "POST",
                    url: "{{route('check-student-phone-update')}}",
                    data: {
                        phone : phone_value,
                        given_student_id : studentID,
                    },
                    dataType: "JSON",
                    headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "Accept-Language": 'en',
                    },
                    success: function (response) {
                        if (!response.unique) {
                            $('input[name="phone"]').addClass('error');
                                var errorElement = document.getElementById('phone_error');
                                if (errorElement) {
                                    errorElement.textContent = 'Phone is already taken';
                                }
                                isValid = false;
                                $("#nextBtn").removeAttr("disabled");
                              $("#message").text("");
                              $("#message").css('display','none');
                        }
                        else{
                            if (isValid) {
        $("#nextBtn").attr("disabled", "disabled");
        $("#message").css('color','green');
        $("#message").css('font-size','20px');
        $("#message").css('text-align','center');
        $("#message").text("Please Wait!!!!");
        $("#message").css('display','block');
                let formData = {};
                fieldsToSubmit.forEach(fieldName => {
                var field = form.querySelector(`[name="${fieldName}"]`);
                if (field) {
                    formData[fieldName] = field.value;
                }
                });
                var st_values = [];
                $('.st_type').each(function(){
                    var $this = $(this);
                    if ($this.is(':checked')) {
                    st_values.push($this.val());
                    }
                    });
                formData['st_type'] = st_values;
                console.log(formData)
                $.ajax({
                    type: "POST",
                    url: "{{route('update.sign-up-as-student-step1')}}",
                    data: formData,
                    dataType: "JSON",
                    headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "Accept-Language": 'en',
                    },
                    success: function (response) {
                        if(response.status){
                            console.log(formData);
                                var studentTrial =  $('#new_trial_id').val();
                                if(studentTrial){
                                    $('#check_status').val("step_2_update_free_trial");
                                    $('#new_trial_id').val(studentTrial);
                                }
                                else{
                                    $('#check_status').val("step_2_create_free_trial");
                                }
                            //   $('#check_status').val("step_2_create_free_trial");
                              $('#new_student_id').val(response.student_id);
                              $('#type_st').val(response.stType);
                              $("#nextBtn").removeAttr("disabled");
                              $("#message").text("");
                              $("#message").css('display','none');
                         
                            if (currentStep < steps.length - 1) {
                                if(response.stType == 2){ //Only Self Learning Selected
                                 
                                    $("#step-2-field-1").css('display','none');
                                    $("#step-2-field-2").css('display','none');
                                    $(".third").css('display','none');
                                }   
                                else{
                                  
                                    $("#step-2-field-1").css('display','block');
                                    $("#step-2-field-2").css('display','block');
                                    $(".third").css('display','block');
                                }
                                 currentStep++;
                                 updateStep();
                            }
                        }
                    }
                });
            
            }
            else {
            console.log('Specific fields have errors');
                                $("#nextBtn").removeAttr("disabled");
                              $("#message").text("");
                              $("#message").css('display','none');
            }
                        }
                    }
                });
                        }
                    }
                });
            }
            else{
                console.log('Specific fields have errors');
                                $("#nextBtn").removeAttr("disabled");
                              $("#message").text("");
                              $("#message").css('display','none');
            }

    }
    else if(checkStatus == 'step_2_create_free_trial'){
                var studentID =  $('#new_student_id').val();
                var form = document.getElementById('multiStepForm');
                var fieldsToSubmit = ['lms_uses','no_of_joined_students','preferred_teacher', 'learn_about_lms','token','new_student_id']; // List the specific fields to validate
                var fieldsToValidate = ['lms_uses','no_of_joined_students','preferred_teacher', 'learn_about_lms']; // List the specific fields to validate
                let isValid = true;
             
                $('#check_status').val("step_3_update_free_trial");
                if (currentStep < steps.length - 1) {
                                 currentStep++;
                                 updateStep();
                }    

    }
    else if(checkStatus == 'step_2_update_free_trial'){
                var form = document.getElementById('multiStepForm');
                var fieldsToSubmit = ['lms_uses','no_of_joined_students','preferred_teacher', 'learn_about_lms','token','new_student_id','new_trial_id']; // List the specific fields to validate
                var fieldsToValidate = ['lms_uses','no_of_joined_students','preferred_teacher', 'learn_about_lms']; // List the specific fields to validate
                let isValid = true;
                $('#check_status').val("step_3_update_free_trial");
                if (currentStep < steps.length - 1) {
                                 currentStep++;
                                 updateStep();
                }    
    }
    else if(checkStatus == 'step_3_update_free_trial'){
        
            var updated_slug = $('#slug').val();
            $("#nextBtn").attr("disabled", "disabled");
            $("#prevBtn").attr("disabled", "disabled");
            $("#message").css('color','green');
            $("#message").css('font-size','20px');
            $("#message").css('text-align','center');
            $("#message").text("Please Wait!!!!");
            $("#message").css('display','block');

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
                    url: "{{route('update.sign-up-as-student-live-step4')}}",
                    data: formData,
                    dataType: "JSON",
                    headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                "Accept-Language": 'en',
                    },
                    success: function (response) {
                        if(response.status){
                            $("#nextBtn").attr("disabled", "disabled");
                            $("#prevBtn").attr("disabled", "disabled");
        $("#message").css('color','green');
        $("#message").css('font-size','20px');
        $("#message").css('text-align','center');
        $("#message").text(response.message);
        $("#message").css('display','block'); 
        setTimeout(() => {
            window.location.href = response.url;
        }, 3000);

                        }
                        else{
                           $("#nextBtn").attr("disabled", "disabled");
                           $("#prevBtn").attr("disabled", "disabled");
        $("#message").css('color','red');
        $("#message").css('font-size','20px');
        $("#message").css('text-align','center');
        $("#message").text(response.message);
        $("#message").css('display','block'); 
            setTimeout(() => {
                window.location.href = response.url;
            }, 3000);

                        }
                    }
                });
    }
  
}

    function prevStep() {
            var checkStatus =  $('#check_status').val();
            if(checkStatus == 'step_2_create_free_trial'){

                $('#check_status').val("step_1_edit");
                if (currentStep > 0) {
                currentStep--;
                updateStep();
                }
            }
            else if(checkStatus == 'step_3_update_free_trial'){

                $('#check_status').val("step_2_update_free_trial");
                if (currentStep > 0) {
                currentStep--;
                updateStep();
                }
            }
            else if(checkStatus == 'step_2_update_free_trial'){

                $('#check_status').val("step_1_edit");
                if (currentStep > 0) {
                currentStep--;
                updateStep();
                }
            }
            
    }

        document.getElementById('nextBtn').addEventListener("click", nextStep);
        document.getElementById('prevBtn').addEventListener("click", prevStep);

        updateButtons();
    });
</script>
<script>
    $(document).ready(function () {
        $('#country-id').selectize({ create: false,sortField: 'text', searchField: 'text', onDropdownOpen: function($dropdown) { this.clear(); } });
    });
</script>
<script>
    $(document).ready(function () {
            var $select = $('#time_zone').selectize({
                create: false,
                searchField: 'text',
                onDropdownOpen: function($dropdown) {
                    this.clear();
                }
            });
        });
</script>

<style>
    th,
    td {
        max-width: 3rem !important;
        min-width: 3rem !important;
        text-align: center !important;
        height: 3rem;
        color: white !important;
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
        background-color: transparent !important;
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
        max-height: 7rem !important;
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

    .selectize-input.items.has-options.full.has-items {
        border: 0.1rem solid white !important;
        border-radius: 4rem;
        padding: 6px 4px 6px 14px ! IMPORTANT;
        text-align: start;
        color: white !important;
        display: flex;
        height: 2rem !important;
    }

    .selectize-input.items.has-options.full.has-items input#country-id-selectized,
    .selectize-input.items.has-options.full.has-items input#time_zone-selectized {
        display: none !important
    }

    .selectize-input.items.has-options.full.has-items .item {
        margin-top: 0.5rem;
        overflow: hidden !important;
        height: 1.2rem;
        text-overflow: ellipsis;
        white-space: nowrap;
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

    .iti__country-list {
        overflow-x: scroll !important;
        width: 28vw !important;
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
        color: var(--theme-color) !important;
    }

    #back {
        position: absolute;
        font-size: 50px;
        top: -1rem;
    }

    #slots {
        position: relative;
    }

    span.iti__country-name,
    span.iti__dial-code {
        color: black !important;
        font-size: 0.8rem !important;
    }

    .selectize-control.single .selectize-input {
        background: transparent !important;
    }

    .selectize-input.items.not-full.has-options {
        padding: 0 !important;
    }

    input#country-id-selectized,
    input#time_zone-selectized {
        width: calc(100% - 27px) !important;
        padding: 0 12px !important;
    }

    .selectize-control {
        position: relative;
        width: 100% !important;
    }

    .selectize-input.items.not-full.has-options {
        padding: 0 !important;
        width: 100% !important;
    }

    .selectize-dropdown-content {
        text-align: start !important;
    }

    .learning {
    display: flex;
    width: 100%;
    flex-direction: row;
    justify-content: space-evenly;
    align-items: flex-start;
    height: 4rem !important;
    position: relative !important;
}

span.error-message {
    position: absolute !important;
    text-align: start !important;
    font-size: 0.6rem !important;
    left: 1rem !important;
    top: 2.6rem !important;
}

button#prevBtn {
    margin-top: 0 !important;
}
</style>
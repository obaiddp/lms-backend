@extends('layouts.admin')

@section('content')
<!-- Page content area start -->
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="customers__area bg-style mb-30">
                    <div class="item-title d-flex justify-content-between">
                        <h2>{{ __('Edit Student') }}</h2>
                    </div>
                    <form action="{{route('student.update', $student->uuid)}}" method="post" class="form-horizontal"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{__('First Name')}} <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" value="{{ $student->first_name }}"
                                        placeholder="First name" class="form-control" required>
                                    @if ($errors->has('first_name'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('first_name') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{__('Last Name')}} <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" value="{{ $student->last_name }}"
                                        placeholder="Last name" class="form-control" required>
                                    @if ($errors->has('last_name'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('last_name') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{__('Email')}} <span class="text-danger">*</span></label>
                                    <input type="email" name="email" value="{{ @$student->user->email }}"
                                        placeholder="Email" class="form-control" required>
                                    @if ($errors->has('email'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('email') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{ __('Password') }} </label>
                                    <input type="password" name="password" value="" placeholder="Password"
                                        class="form-control">
                                    @if ($errors->has('password'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('password') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Area
                                        Code')
                                        }} <span class="text-danger">*</span></label>
                                    <select class="form-control" name="area_code">
                                        <option value>{{ __("Select Code") }}</option>
                                        @foreach ($countries as $country)
                                        <option value="{{ $country->phonecode }}" @if(old('area_code', @$student->
                                            user->area_code)==$country->
                                            phonecode) selected @endif>{{ $country->short_name.'('.$country->phonecode.')' }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('area_code'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('area_code') }}</span>
                                    @endif
                                </div>
                            </div>



                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{__('Phone Number')}} <span class="text-danger">*</span></label>
                                    {{-- <input type="text" name="phone_number" value="{{ $student->phone_number }}"
                                        placeholder="Phone number" class="form-control" required> --}}
                                    <input type="text" name="phone_number" value="{{ $student->phone_number }}"
                                        placeholder="Phone number" class="form-control" required>
                                    @if ($errors->has('phone_number'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('phone_number') }}</span>
                                    @endif
                                </div>
                            </div>
                        
                            
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Time Zone')
                                        }} <span class="text-danger">*</span></label>
                                    <select id="time_zone" class="form-control" name="time_zone" required>
                                        <option value ="">{{ __("Select Time Zone") }}</option>
                                        @if (!empty($timeZonesArray))
                                            @foreach ($timeZonesArray as $key => $timezone)
                                                <option value="{{$key}}"
                                                @if($student->user->time_zone== $key) 
                                                    selected 
                                                @endif>
                                                    {{$timezone}}
                                                </option>
                                            @endforeach
                                        @endif
                                      
                                     
                                    </select>
                                    @if ($errors->has('time_zone'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('time_zone') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{ __('Address') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="address" value="{{ $student->address }}"
                                        placeholder="Address" class="form-control" required>
                                    @if ($errors->has('address'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('address') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{ __('Postal Code') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="postal_code" value="{{ $student->postal_code }}"
                                        placeholder="Postal Code" class="form-control" required>
                                    @if ($errors->has('postal_code'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('postal_code') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{__('Country')}}</label>
                                    <select name="country_id" id="country_id" class="form-select">
                                        <option value="">{{__('Select Country')}}</option>
                                        @foreach($countries as $country)
                                        <option value="{{$country->id}}" @if(old('country_id'))
                                            {{old('country_id')==$country->id ? 'selected' : '' }}
                                            @else
                                            {{$student->country_id == $country->id ? 'selected' : '' }}
                                            @endif >{{$country->country_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{__('State')}}</label>
                                    <select name="state_id" id="state_id" class="form-select">
                                        <option value="">{{__('Select State')}}</option>
                                        @if(old('country_id'))
                                        @foreach($states as $state)
                                        <option value="{{$state->id}}" {{old('state_id')==$state->id ? 'selected' : ''}}
                                            >{{$state->name}}</option>
                                        @endforeach
                                        @else
                                        @if($student->country)
                                        @foreach($student->country->states as $selected_state)
                                        <option value="{{$selected_state->id}}" {{$student->state_id ==
                                            $selected_state->id ? 'selected' : '' }} >{{$selected_state->name}}</option>
                                        @endforeach
                                        @endif
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{__('City')}}</label>
                                    <select name="city_id" id="city_id" class="form-select">
                                        <option value="">{{__('Select City')}}</option>
                                        @if(old('state_id'))
                                        @foreach($cities as $city)
                                        <option value="{{$city->id}}" {{old('city_id')==$city->id ? 'selected' : '' }}
                                            >{{$city->name}}</option>
                                        @endforeach
                                        @else
                                        @if($student->state)
                                        @foreach($student->state->cities as $selected_city)
                                        <option value="{{$selected_city->id}}" {{$student->city_id == $selected_city->id
                                            ? 'selected' : '' }} >{{$selected_city->name}}</option>
                                        @endforeach
                                        @endif
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{__('Gender')}}<span class="text-danger">*</span></label>
                                    <select name="gender" id="gender" class="form-select" required>
                                        <option value="">{{__('Select Option')}}</option>
                                        <option value="Male" {{ $student->gender == 'Male' ? 'selected' : '' }} >{{
                                            __('Male') }}</option>
                                        <option value="Female" {{ $student->gender == 'Female' ? 'selected' : '' }} >{{
                                            __('Female') }}</option>
                                        <option value="Others" {{ $student->gender == 'Others' ? 'selected' : '' }} >{{
                                            __('Others') }}</option>
                                    </select>
                                </div>
                            </div>
                            {{-- <input type="hidden" name="enrollment_id" value="{{$enrollment_id}}"> --}}
                            {{-- <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{__('Enrolled Course')}} ({{__('Live')}})</label>

                                    <select  multiple="multiple" name="course[]" id="course" class="form-select">
                                        <option value="">{{ __('Select Courses') }}</option>
                                        @if(!$allCourses->isEmpty())
                                        @foreach($allCourses as $course)
                                            <option value="{{$course->id}}"
                                                <?php if( in_array($course->id, $enrollmentIDs)){ echo 'selected="selected"'; } ?>
                                            >{{$course->title}}</option>
                                        @endforeach
                                        @endif
                                    </select>

                                 
                                </div>
                            </div> --}}

                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{__('Is Featured')}}<span class="text-danger">*</span></label>
                                    <select name="is_featured" id="is_featured" class="form-select" required>
                                        <option value="">{{__('Select Option')}}</option>
                                        <option value="1" {{ $student->is_featured === 1 ? 'selected' : '' }} >{{ __('Yes') }}</option>
                                        <option value="0" {{ $student->is_featured === 0 ? 'selected' : '' }} >{{ __('No') }}</option>
                                     
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div class="input__group mb-25">
                                    <label>{{ __('About Student') }} <span class="text-danger">*</span></label>
                                    <textarea name="about_me" id="" cols="15" rows="5" required>{{ $student->about_me
                                        }}</textarea>
                                    @if ($errors->has('about_me'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('about_me') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="upload-img-box mb-25">
                                    @if(@$student->user->image)
                                    <img src="{{asset(@$student->user->image_path)}}" alt="img">
                                    @else
                                    <img src="" alt="No img">
                                    @endif
                                    <input type="file" name="image" id="image" accept="image/*"
                                        onchange="previewStudentImageFree(this)"
                                        @if (!$student->user->image)
                                            required
                                        @endif>
                                    <div class="upload-img-box-icon">
                                        <i class="fa fa-camera"></i>
                                        <p class="m-0">{{__('Image')}}</p>
                                    </div>
                                </div>
                            </div>
                            @if ($errors->has('image'))
                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                $errors->first('image') }}</span>
                            @endif
                            <p>{{ __('Any standard image file is accepted; no fixed dimensions or file size limit.') }}</p>
                        </div>








                        {{-- Active Enrollments Discount Blocks --}}
                        @if(isset($activeEnrollments) && $activeEnrollments->count())
                            <h4 class="mt-4">Active Enrollments — Discounts</h4>
                            @foreach($activeEnrollments as $enrollment)
                                @php
                                    $eid = $enrollment->id;
                                    $course = $enrollment->course;
                                    // fallback values
                                    $existingStudentsCount = (float)($enrollment->students_count ?? 1);
                                    $course_price = isset($course->price) ? (float)$course->price : (float)$enrollment->original_price ?? 0;
                                    // $admission_fee = (float)($enrollment->admission_fee ?? (float)($enrollment->admission_fee ?? (float) (get_option('admission_fee') ?? 0)));
                                    $admission_fee = (float) get_option('admission_fee') ? get_option('admission_fee') : (float)($enrollment->admission_fee);
                                    $existingDiscountCourse = (float)($enrollment->discount ?? 0);
                                    $existingDiscountTypeCourse = isset($enrollment->discount_type) ? (int)$enrollment->discount_type : 0;
                                    $existingNetCourse = (float)($enrollment->net_original_price ?? $course_price);
                                    $existingDiscountAdmission = (float)($enrollment->discount_admission_fee ?? 0);
                                    $existingDiscountTypeAdmission = isset($enrollment->discount_type_admission_fee) ? (int)$enrollment->discount_type_admission_fee : 0;
                                    $existingNetAdmission = (float)($enrollment->net_admission_fee ?? $admission_fee);
                                @endphp

                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5>Enrollment #{{ $eid }} — Course: {{ $course->title ?? 'N/A' }}</h5>
                                    
                                        {{-- Course Section --}}
                                        <div class="row align-items-end mb-3">
                                            <div class="col-md-3">
                                                <label class="form-label">Course Name</label>
                                                <input type="text" class="form-control" value="{{ $course->title ?? '' }}" readonly>
                                                <input type="hidden" name="course_id[{{ $eid }}]" value="{{ $course->id ?? '' }}">
                                            </div>
                                        
                                            <div class="col-md-2">
                                                <label class="form-label">Course Price</label>
                                                <input type="text" class="form-control" id="course_price_display_{{ $eid }}" value="{{ number_format($course_price,2) }}" readonly>
                                                <input type="hidden" id="course_price_{{ $eid }}" name="course_price[{{ $eid }}]" value="{{ $course_price }}">
                                            </div>
                                        
                                            {{-- Number of students --}}
                                            <div class="col-md-2">
                                                <label>{{ __('Number of Students') }}</label>
                                                <input type="number" name="students_count[{{ $eid }}]" id="students_count_{{ $eid }}" class="form-control" value="{{ old('students_count.'.$eid, $existingStudentsCount) }}" min="1" step="1" required>
                                            </div>

                                            <div class="col-md-2">
                                                <label class="form-label">Discount Course Price</label>
                                                <input type="number" step="0.01" min="0" name="discount_course_price[{{ $eid }}]" id="discount_course_price_{{ $eid }}" class="form-control" value="{{ old('discount_course_price.'.$eid, $existingDiscountCourse) }}">
                                            </div>
                                        
                                            <div class="col-md-2">
                                                <label class="form-label">Discount Type Course Price</label>
                                                <select name="discount_type_course_price[{{ $eid }}]" id="discount_type_course_price_{{ $eid }}" class="form-control">
                                                    <option value="0" {{ $existingDiscountTypeCourse === 0 ? 'selected' : '' }}>Amount</option>
                                                    <option value="1" {{ $existingDiscountTypeCourse === 1 ? 'selected' : '' }}>Percentage (%)</option>
                                                </select>
                                            </div>
                                        
                                            <div class="col-md-3">
                                                <label class="form-label">Net Course Price</label>
                                                <input type="text" class="form-control" id="net_course_price_display_{{ $eid }}" value="{{ number_format($existingNetCourse,2) }}" readonly>
                                                <input type="hidden" name="net_course_price[{{ $eid }}]" id="net_course_price_{{ $eid }}" value="{{ number_format($existingNetCourse,2,'.','') }}">
                                            </div>
                                        </div>
                                    
                                        {{-- Admission Section --}}
                                        <div class="row align-items-end mb-3">
                                            <div class="col-md-3">
                                             
                                              
                                                <label class="form-label">Admission Fee</label>
                                                <input type="text" class="form-control" id="admission_fee_display_{{ $eid }}" value="{{ number_format($admission_fee,2) }}" readonly>
                                                <input type="hidden" id="admission_fee_{{ $eid }}" name="admission_fee[{{ $eid }}]" value="{{ number_format($admission_fee,2,'.','') }}">
                                            </div>
                                        
                                            <div class="col-md-2">
                                                <label class="form-label">Discount Admission Fee</label>
                                                <input type="number" step="0.01" min="0" name="discount_admission_fee[{{ $eid }}]" id="discount_admission_fee_{{ $eid }}" class="form-control" value="{{ old('discount_admission_fee.'.$eid, $existingDiscountAdmission) }}">
                                            </div>
                                        
                                            <div class="col-md-2">
                                                <label class="form-label">Discount Type Admission Fee</label>
                                                <select name="discount_type_admission_fee[{{ $eid }}]" id="discount_type_admission_fee_{{ $eid }}" class="form-control">
                                                    <option value="0" {{ $existingDiscountTypeAdmission === 0 ? 'selected' : '' }}>Amount</option>
                                                    <option value="1" {{ $existingDiscountTypeAdmission === 1 ? 'selected' : '' }}>Percentage (%)</option>
                                                </select>
                                            </div>
                                        
                                            <div class="col-md-3">
                                                <label class="form-label">Net Admission Fee</label>
                                                <input type="text" class="form-control" id="net_admission_fee_display_{{ $eid }}" value="{{ number_format($existingNetAdmission,2) }}" readonly>
                                                <input type="hidden" name="net_admission_fee[{{ $eid }}]" id="net_admission_fee_{{ $eid }}" value="{{ number_format($existingNetAdmission,2,'.','') }}">
                                            </div>
                                        </div>
                                    
                                        {{-- Total Net Price --}}
                                        <div class="row mb-3">
                                            <div class="col-md-4 offset-md-8">
                                                <label class="form-label fw-bold">Total Net Price</label>
                                                <input type="text" class="form-control fw-bold" id="net_price_display_{{ $eid }}" readonly>
                                                <input type="hidden" name="net_price[{{ $eid }}]" id="net_price_{{ $eid }}" value="0">
                                            </div>
                                        </div>
                                    
                                    </div>
                                </div>
                            @endforeach
                        @endif




                        <div class="row mb-3">
                            <div class="col-md-12 text-right">
                                <button class="btn btn-primary" type="submit">{{ __('Save') }}</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- Page content area end -->
@endsection

@push('style')
<link rel="stylesheet" href="{{asset('admin/css/custom/image-preview.css')}}">
@endpush

@push('script')
<script src="{{asset('admin/js/custom/image-preview.js')}}"></script>
<script src="{{asset('admin/js/custom/admin-profile.js')}}"></script>
<script>




  $(document).ready(function () {

    // Safe float parser
    function parseFloatSafe(v) {
        return isNaN(parseFloat(v)) ? 0 : parseFloat(v);
    }
    function parseIntSafe(v){ return isNaN(parseInt(v)) ? 1 : parseInt(v); }

    // Recalculate for a single enrollment by ID
    function recalcEnrollment(eid) {
        // ===== Course Price Block =====
        const studentsCount = parseIntSafe($("#students_count_" + eid).val());
        const coursePricePerUnit = parseFloatSafe($("#course_price_" + eid).val()); // per student
        // Updated (total) course price = per-unit * students_count
        const coursePrice = coursePricePerUnit * studentsCount;

        const discountCourse = parseFloatSafe($('#discount_course_price_' + eid).val());
        const discountTypeCourse = $('#discount_type_course_price_' + eid).val();
        let discountAmountCourse = 0;

        if (discountTypeCourse === '0') {
            discountAmountCourse = discountCourse;
        } else {
            discountAmountCourse = (coursePrice * discountCourse) / 100;
        }

        if (discountAmountCourse > coursePrice) discountAmountCourse = coursePrice;
        const netCourse = Math.max(coursePrice - discountAmountCourse, 0);

        $("#course_price_display_" + eid).val(coursePrice.toFixed(2));
        $('#net_course_price_display_' + eid).val(netCourse.toFixed(2));
        $('#net_course_price_' + eid).val(netCourse.toFixed(2));

         // Admission Fee Discount Calculation
        const admissionPerUnit = parseFloatSafe($("#admission_fee_" + eid).val()); // per student
        const admissionFee = admissionPerUnit * studentsCount;
        // Show total (before discount)
   
        const discountAdmission = parseFloatSafe($('#discount_admission_fee_' + eid).val());
        const discountTypeAdmission = $('#discount_type_admission_fee_' + eid).val();

        let discountAmountAdmission = 0;

        if (discountTypeAdmission === '0') {
            discountAmountAdmission = discountAdmission;
        } else {
            discountAmountAdmission = (admissionFee * discountAdmission) / 100;
        }

        if (discountAmountAdmission > admissionFee) discountAmountAdmission = admissionFee;
        const netAdmission = Math.max(admissionFee - discountAmountAdmission, 0);

        $("#admission_fee_display_" + eid).val(admissionFee.toFixed(2));
        $('#net_admission_fee_display_' + eid).val(netAdmission.toFixed(2));
        $('#net_admission_fee_' + eid).val(netAdmission.toFixed(2));

        // ===== Total Block =====
        const totalNet = netCourse + netAdmission;
        $('#net_price_display_' + eid).val(totalNet.toFixed(2));
        $('#net_price_' + eid).val(totalNet.toFixed(2));
    }

    // Attach listeners to all enrollment input groups
    $('[id^="course_price_"]').each(function () {
      
        const eid = $(this).attr('id').replace('course_price_', '');

       
        const relatedIds = [
            'discount_course_price_' + eid,
            'discount_type_course_price_' + eid,
            'discount_admission_fee_' + eid,
            'discount_type_admission_fee_' + eid,
            'students_count_' +eid
        ];

        relatedIds.forEach(function (rid) {
            const $el = $('#' + rid);
            if (!$el.length) return;

            const ev = $el.is('select') ? 'change' : 'input';
            $el.on(ev, function () {
                recalcEnrollment(eid);
            });
        });

        // Run initial calculation
        recalcEnrollment(eid);
    });
});






    $(document).ready(function () {
  $('#course').select2({
// dropdownParent: $('#edit_drop_modal'),
width: '100%',
allowClear: true,
placeholder: 'Select Courses',


});


$('#time_zone').select2({
// dropdownParent: $('#edit_drop_modal'),
width: '100%',
allowClear: true,
placeholder: 'Select Time Zone',


});


});
</script>
@endpush
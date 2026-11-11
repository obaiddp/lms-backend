@extends('layouts.admin')

@section('content')
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2>{{ __('Add Student Payment') }}</h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('Dashboard')}}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ __('Add Student Payment') }}</li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="customers__area bg-style mb-30">
                        <div class="item-title d-flex justify-content-between">
                            <h2>{{ __('Add Student Payment') }}</h2>
                        </div>
                        <form action="{{route('student_payment.store_payment')}}" method="post" class="form-horizontal">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label class="label-text-title color-heading font-medium font-14 mb-3">
                                            {{ __('Student') }} <span class="text-danger">*</span>
                                        </label>
                                        <select name="student_id" id="student_id" class="form-select" required>
                                            <option value="">{{ __('Select Student') }}</option>
                                            @foreach($students as $student)
                                                <option value="{{$student->id}}" {{ old('student_id')==$student->id ? 'selected' : '' }}>{{$student->name}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('student_id'))
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('student_id') }}</span>
                                        @endif
                                    </div>
                                </div>
                            
                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label class="label-text-title color-heading font-medium font-14 mb-3">
                                            {{ __('Course') }} <span class="text-danger">*</span>
                                        </label>
                                        <select name="course_id" id="course_id" class="form-select" required>
                                            <option value="">{{ __('Select Course') }}</option>
                                            @foreach($courses as $course)
                                                <option value="{{$course->id}}" {{ old('course_id')==$course->id ? 'selected' : '' }}>{{$course->title}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('course_id'))
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('course_id') }}</span>
                                        @endif
                                    </div>
                                </div>
                            
                                {{-- Course Price (display + hidden input) --}}
                                <div class="col-md-3">
                                    <div class="input__group mb-25">
                                        <label>{{__('Course Price')}} ({{get_currency_code()}}) <span class="text-danger">*</span></label>
                                        {{-- Display the total course price (per-student * students_count) --}}
                                        <input type="text" id="course_price_display" value="{{ old('price', number_format(0,2)) }}" class="form-control" readonly>
                                        {{-- Hidden: per-student price (this is what course.get_price returns and is submitted) --}}
                                        <input type="hidden" id="course_price" name="price" value="{{ old('price', 0) }}">
                                    </div>
                                </div>
                            

                                {{-- Number of students --}}
                                <div class="col-md-2">
                                    <div class="input__group mb-25">
                                        <label>{{ __('Number of Students') }}</label>
                                        <input type="number" name="students_count" id="students_count" class="form-control" value="{{ old('students_count', 1) }}" min="1" step="1" required>
                                    </div>
                                </div>

                                {{-- Discount for Course Price --}}
                                <div class="col-md-3">
                                    <div class="input__group mb-25">
                                        <label>{{ __('Discount Course Price') }}</label>
                                        <input type="number" step="0.01" min="0" name="discount_course_price" id="discount_course_price" class="form-control" value="{{ old('discount_course_price', 0) }}">
                                    </div>
                                </div>
                            
                                <div class="col-md-2">
                                    <div class="input__group mb-25">
                                        <label>{{ __('Discount Type') }}</label>
                                        <select name="discount_type_course_price" id="discount_type_course_price" class="form-control">
                                            <option value="0" {{ old('discount_type_course_price','0')=='0'? 'selected':'' }}>Amount</option>
                                            <option value="1" {{ old('discount_type_course_price')=='1'? 'selected':'' }}>Percentage (%)</option>
                                        </select>
                                    </div>
                                </div>
                            
                                <div class="col-md-3">
                                    <div class="input__group mb-25">
                                        <label>{{ __('Net Course Price') }}</label>
                                        <input type="text" class="form-control" id="net_course_price_display" readonly>
                                        <input type="hidden" name="net_course_price" id="net_course_price" value="0">
                                    </div>
                                </div>
                            
                                {{-- Admission fee block (may be hidden if not applicable) --}}
                                <div id="admission_block" class="w-100">
                                    <div class="row">
                                        {{-- <div class="col-md-3">
                                            <div class="input__group mb-25">
                                                <label>{{__('Admission Fee')}} ({{get_currency_code()}})</label>
                                                <input type="text" id="admission_fee_display" class="form-control" readonly value="{{ number_format((float)$admissionFee,2) }}">
                                                <input type="hidden" id="admission_fee" name="admission_fee" value="{{ (float)$admissionFee }}">
                                            </div>
                                        </div> --}}
                                    
                                        {{-- Admission Price (display + hidden input) --}}
                                        <div class="col-md-3">
                                            <div class="input__group mb-25">
                                                <label>{{ __('Admission Price') }} ({{ get_currency_code() }}) <span class="text-danger">*</span></label>
                                            
                                                {{-- Display the total admission price (calculated dynamically like course price) --}}
                                                <input type="text" id="admission_fee_display" 
                                                       value="{{ number_format((float)$admissionFee,2) }}"
                                                       class="form-control" readonly>
                                            
                                                {{-- Hidden actual admission price that gets submitted --}}
                                                <input type="hidden" id="admission_fee" name="admission_fee" 
                                                       value="{{ (float)$admissionFee }}">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="input__group mb-25">
                                                <label>{{ __('Discount Admission Fee') }}</label>
                                                <input type="number" step="0.01" min="0" name="discount_admission_fee" id="discount_admission_fee" class="form-control" value="{{ old('discount_admission_fee', 0) }}">
                                            </div>
                                        </div>
                                    
                                        <div class="col-md-2">
                                            <div class="input__group mb-25">
                                                <label>{{ __('Discount Type') }}</label>
                                                <select name="discount_type_admission_fee" id="discount_type_admission_fee" class="form-control">
                                                    <option value="0" {{ old('discount_type_admission_fee','0')=='0'? 'selected':'' }}>Amount</option>
                                                    <option value="1" {{ old('discount_type_admission_fee')=='1'? 'selected':'' }}>Percentage (%)</option>
                                                </select>
                                            </div>
                                        </div>
                                    
                                        <div class="col-md-3">
                                            <div class="input__group mb-25">
                                                <label>{{ __('Net Admission Fee') }}</label>
                                                <input type="text" class="form-control" id="net_admission_fee_display" readonly>
                                                <input type="hidden" name="net_admission_fee" id="net_admission_fee" value="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                                {{-- Total net price --}}
                                <div class="col-md-4 offset-md-8">
                                    <div class="input__group mb-25">
                                        <label class="fw-bold">{{ __('Total Net Price') }}</label>
                                        <input type="text" class="form-control fw-bold" id="net_price_display" readonly>
                                        <input type="hidden" name="net_price" id="net_price" value="0">
                                    </div>
                                </div>
                            
                                {{-- Month/Year selects (existing) --}}
                                <div class="col-md-6">
                                    <div class="input__group mb-25">
                                        <label class="label-text-title color-heading font-medium font-14 mb-3">
                                            {{ __('Month') }} <span class="text-danger">*</span>
                                        </label>
                                        <select name="month" id="month" class="form-select" required>
                                            <option value="">{{ __('Select Month') }}</option>
                                            <option value='1'>{{__('January')}}</option>
                                            <option value='2'>{{ __('Febuary')}}</option>
                                            <option value='3'>{{ __('March')}}</option>
                                            <option value='4'>{{ __('April')}}</option>
                                            <option value='5'>{{ __('May')}}</option>
                                            <option value='6'>{{ __('June')}}</option>
                                            <option value='7'>{{ __('July')}}</option>
                                            <option value='8'>{{ __('August')}}</option>
                                            <option value='9'>{{ __('September')}}</option>
                                            <option value='10'>{{ __('October')}}</option>
                                            <option value='11'>{{ __('November')}}</option>
                                            <option value='12'>{{ __('December')}}</option>
                                        </select>
                                    </div>
                                </div>
                            
                                <div class="col-md-6">
                                    <div class="input__group mb-25">
                                        <label class="label-text-title color-heading font-medium font-14 mb-3">
                                            {{ __('Year') }} <span class="text-danger">*</span>
                                        </label>
                                        <select name="year" id="year" class="form-select" required>
                                            <option value="">{{ __('Select Year') }}</option>
                                            @for($year = \Carbon\Carbon::now()->format('Y'); $year < \Carbon\Carbon::now()->
                                            addYear(10)->format('Y');
                                            $year++)
                                            <option value="{{$year}}">{{$year}}</option>
                                             @endfor
                                            {{-- <option value='2022'>{{ __('2022')}}</option>
                                            <option value='2023'>{{ __('2023')}}</option>
                                            <option value='2024'>{{ __('2024')}}</option>
                                            <option value='2025'>{{ __('2025')}}</option>
                                            <option value='2026'>{{ __('2026')}}</option>
                                            <option value='2027'>{{ __('2027')}}</option>
                                            <option value='2028'>{{ __('2028')}}</option>
                                            <option value='2029'>{{ __('2029')}}</option>
                                            <option value='2030'>{{ __('2030')}}</option>
                                            <option value='2031'>{{ __('2031')}}</option>
                                            <option value='2032'>{{ __('2032')}}</option>
                                            <option value='2033'>{{ __('2033')}}</option>
                                            <option value='2034'>{{ __('2034')}}</option>
                                            <option value='2035'>{{ __('2035')}}</option> --}}
                                        </select>
                                    </div>
                                </div>
                            
                                {{-- Hidden flag to indicate admission applicability (1 or 0) --}}
                                <input type="hidden" id="admission_applicable" name="admission_applicable" value="1">
                            
                            </div>
                            
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
    // select2 for student
    $("#student_id").select2({ width: '100%', placeholder: "Select Student" });

    function parseFloatSafe(v){ return isNaN(parseFloat(v)) ? 0 : parseFloat(v); }
    function parseIntSafe(v){ return isNaN(parseInt(v)) ? 1 : parseInt(v); }

    // initial values from blade
    var admissionFeePerStudent = parseFloat($("#admission_fee").val() || 0);

    function recalc() {
        const studentsCount = parseIntSafe($("#students_count").val());
        const coursePricePerUnit = parseFloatSafe($("#course_price").val()); // per student
        // Updated (total) course price = per-unit * students_count
        const coursePriceTotal = coursePricePerUnit * studentsCount;

        const discountCourse = parseFloatSafe($("#discount_course_price").val());
        const discountTypeCourse = $("#discount_type_course_price").val();
        let discountAmountCourse = 0;
        if (discountTypeCourse === '0') {
            discountAmountCourse = discountCourse;
        } else {
            discountAmountCourse = (coursePriceTotal * discountCourse) / 100;
        }
        if (discountAmountCourse > coursePriceTotal) discountAmountCourse = coursePriceTotal;
        const netCourse = Math.max(coursePriceTotal - discountAmountCourse, 0);

        $("#course_price_display").val(coursePriceTotal.toFixed(2));
        // keep hidden course_price as per-unit price (so backend can recalc and know per-unit)
        // but we will also update a hidden field net_course_price etc.
        $("#net_course_price_display").val(netCourse.toFixed(2));
        $("#net_course_price").val(netCourse.toFixed(2));

        // Admission only if admission_applicable hidden field is 1
        const admissionApplicable = $("#admission_applicable").val() === '1';
        let netAdmission = 0;
        if (admissionApplicable) {
            const admissionPerUnit = parseFloatSafe($("#admission_fee").val()); // per student
            const admissionTotal = admissionPerUnit * studentsCount;

            // Show total (before discount)
            $("#admission_fee_display").val(admissionTotal.toFixed(2));

            const discountAdmission = parseFloatSafe($("#discount_admission_fee").val());
            const discountTypeAdmission = $("#discount_type_admission_fee").val();
            let discountAmountAdmission = 0;
            if (discountTypeAdmission === '0') {
                discountAmountAdmission = discountAdmission;
            } else {
                discountAmountAdmission = (admissionTotal * discountAdmission) / 100;
            }
            if (discountAmountAdmission > admissionTotal) discountAmountAdmission = admissionTotal;
            netAdmission = Math.max(admissionTotal - discountAmountAdmission, 0);
        } else {
            netAdmission = 0;
        }

        $("#net_admission_fee_display").val(netAdmission.toFixed(2));
        $("#net_admission_fee").val(netAdmission.toFixed(2));

    
        const totalNet = netCourse + netAdmission;
        $("#net_price_display").val(totalNet.toFixed(2));
        $("#net_price").val(totalNet.toFixed(2));
    }

    // bind recalculation events
    ['discount_course_price','discount_type_course_price','discount_admission_fee','discount_type_admission_fee','students_count']
        .forEach(function(id) {
            $("#"+id).on('input change', recalc);
        });

    // When course changes: get per-student course price via AJAX and then recalc
    $('#course_id').change(function (e) {
        e.preventDefault();
        var course_id = $(this).val();
        if(!course_id) {
            $("#course_price_display").val((0).toFixed(2));
            $("#course_price").val(0);
            recalc();
            return;
        }
        $.ajax({
            type: "POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: '{{route('course.get_price')}}',
            data: {course_id:course_id},
            dataType: "JSON",
            success: function (response) {
                // response expected to be numeric per-student price
                var perUnitPrice = parseFloat(response) || 0;
                // keep hidden value as per-unit price (submitted as 'price')
                $("#course_price").val(perUnitPrice);
                // display will be perUnitPrice * students_count (recalc handles that)
                recalc();
            },
            error: function(){
                $("#course_price_display").val((0).toFixed(2));
                $("#course_price").val(0);
                recalc();
            }
        });

        // Also check if admission fee should be shown/hidden for this student-course combo
        checkAdmissionApplicability();
    });

    // When student selection changes, re-check applicability (if a course is selected)
    $('#student_id').change(function(){
        checkAdmissionApplicability();
    });

    function checkAdmissionApplicability() {
        var student_id = $('#student_id').val();
        var course_id = $('#course_id').val();
        if(!student_id || !course_id) {
            // default: show admission block and set applicable = 1
            $("#admission_block").show();
            $("#admission_applicable").val('1');
            recalc();
            return;
        }
        $.ajax({
            type: "POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: '{{ route("admin.student.check_enrollment") }}',
            data: { student_id: student_id, course_id: course_id },
            dataType: "JSON",
            success: function(res) {
                if (res.enrolled === true) {
                    $("#admission_block").hide();
                    $("#admission_applicable").val('0');
                } else {
                    $("#admission_block").show();
                    $("#admission_applicable").val('1');
                }
                recalc();
            },
            error: function(){
                $("#admission_block").show();
                $("#admission_applicable").val('1');
                recalc();
            }
        });
    }

    // initial recalc
    recalc();
    });
    </script>
@endpush



{{-- @push('script')
    <script src="{{asset('admin/js/custom/image-preview.js')}}"></script>
    <script src="{{asset('admin/js/custom/admin-profile.js')}}"></script>
    <script>
       
        // $(document).ready(function () {
   
        //     $("#student_id").select2({
        //          width: '100%',
        //       placeholder: "Select Student",
        //     //   multiple: true,
        //     //   allowClear: true
        //     });


        //     });
     

        //         $('#course_id').change(function (e) { 
        //             e.preventDefault();
        //             var course_id = $(this).val();
                
        //             $.ajax({
        //                 type: "POST",
        //                 headers:
        //                             {
        //                                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //                             },
        //                 url: '{{route('course.get_price')}}',
        //                 data: {course_id:course_id},
        //                 dataType: "JSON",
        //                 success: function (response) {
        //                     $('#price').val(response)
        //                     console.log(response)
        //                 }
        //             });
                
        // });




        $(document).ready(function () {
            // select2 for student
            $("#student_id").select2({ width: '100%', placeholder: "Select Student" });
            
            // initial values from blade
            var admissionFee = parseFloat($("#admission_fee").val() || 0);
            
            function parseFloatSafe(v){ return isNaN(parseFloat(v)) ? 0 : parseFloat(v); }
            
            function recalc() {
                const coursePrice = parseFloatSafe($("#course_price").val());
                const discountCourse = parseFloatSafe($("#discount_course_price").val());
                const discountTypeCourse = $("#discount_type_course_price").val();
                let discountAmountCourse = 0;
                if (discountTypeCourse === '0') {
                    discountAmountCourse = discountCourse;
                } else {
                    discountAmountCourse = (coursePrice * discountCourse) / 100;
                }
                if (discountAmountCourse > coursePrice) discountAmountCourse = coursePrice;
                const netCourse = Math.max(coursePrice - discountAmountCourse, 0);
                $("#net_course_price_display").val(netCourse.toFixed(2));
                $("#net_course_price").val(netCourse.toFixed(2));
            
                // Admission only if admission_applicable hidden field is 1
                const admissionApplicable = $("#admission_applicable").val() === '1';
                let netAdmission = 0;
                if (admissionApplicable) {
                    const admission = parseFloatSafe($("#admission_fee").val());
                    const discountAdmission = parseFloatSafe($("#discount_admission_fee").val());
                    const discountTypeAdmission = $("#discount_type_admission_fee").val();
                    let discountAmountAdmission = 0;
                    if (discountTypeAdmission === '0') {
                        discountAmountAdmission = discountAdmission;
                    } else {
                        discountAmountAdmission = (admission * discountAdmission) / 100;
                    }
                    if (discountAmountAdmission > admission) discountAmountAdmission = admission;
                    netAdmission = Math.max(admission - discountAmountAdmission, 0);
                } else {
                    netAdmission = 0;
                }
                $("#net_admission_fee_display").val(netAdmission.toFixed(2));
                $("#net_admission_fee").val(netAdmission.toFixed(2));
            
                const totalNet = netCourse + netAdmission;
                $("#net_price_display").val(totalNet.toFixed(2));
                $("#net_price").val(totalNet.toFixed(2));
            }
        
            // bind recalculation events
            ['discount_course_price','discount_type_course_price','discount_admission_fee','discount_type_admission_fee']
                .forEach(function(id) {
                    $("#"+id).on('input change', recalc);
                });
            
            // When course changes: get course price via your existing AJAX and then recalc
            $('#course_id').change(function (e) {
                e.preventDefault();
                var course_id = $(this).val();
                if(!course_id) {
                    $("#course_price_display").val((0).toFixed(2));
                    $("#course_price").val(0);
                    recalc();
                    return;
                }
                $.ajax({
                    type: "POST",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    url: '{{route('course.get_price')}}',
                    data: {course_id:course_id},
                    dataType: "JSON",
                    success: function (response) {
                        // response expected to be numeric price (as before)
                        var price = parseFloat(response) || 0;
                        $("#course_price_display").val(price.toFixed(2));
                        $("#course_price").val(price);
                        recalc();
                    },
                    error: function(){
                        $("#course_price_display").val((0).toFixed(2));
                        $("#course_price").val(0);
                        recalc();
                    }
                });
            
                // Also check if admission fee should be shown/hidden for this student-course combo
                checkAdmissionApplicability();
            });
        
            // When student selection changes, re-check applicability (if a course is selected)
            $('#student_id').change(function(){
                checkAdmissionApplicability();
            });
        
            function checkAdmissionApplicability() {
                var student_id = $('#student_id').val();
                var course_id = $('#course_id').val();
                if(!student_id || !course_id) {
                    // default: show admission block and set applicable = 1
                    $("#admission_block").show();
                    $("#admission_applicable").val('1');
                    recalc();
                    return;
                }
                $.ajax({
                    type: "POST",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    url: '{{ route("admin.student.check_enrollment") }}', // add route definition below
                    data: { student_id: student_id, course_id: course_id },
                    dataType: "JSON",
                    success: function(res) {
                        if (res.enrolled === true) {
                            // student already enrolled -> hide admission fee and set applicable = 0
                            $("#admission_block").hide();
                            $("#admission_applicable").val('0');
                        } else {
                            $("#admission_block").show();
                            $("#admission_applicable").val('1');
                        }
                        recalc();
                    },
                    error: function(){
                        // on error, keep admission visible to be safe
                        $("#admission_block").show();
                        $("#admission_applicable").val('1');
                        recalc();
                    }
                });
        }

        // initial recalc
        recalc();
    });


    </script>
@endpush --}}

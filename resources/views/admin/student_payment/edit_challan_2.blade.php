@extends('layouts.admin')

@section('content')
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <!-- breadcrumb -->
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2>{{ __('Edit Challan Payment') }}</h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('Dashboard')}}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ __('Edit Challan Payment') }}</li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <!-- form -->
            <div class="row">
                <div class="col-md-12">
                    <div class="customers__area bg-style mb-30">
                        <div class="item-title d-flex justify-content-between">
                            <h2>{{ __('Edit Challan Payment') }}</h2>
                        </div>

                        {{-- form updates existing cart --}}
                        <form action="{{ route('student_payment.update_payment_3', $cart->id) }}" method="post" class="form-horizontal">
                            @csrf
                        

                            <div class="row">
                                {{-- Student --}}
                                <input type="hidden" name="student_id" id="student_id" value="{{$selected_student_id}}">
                                {{-- Course --}}
                               
                                <input type="hidden" name="course_id" id="course_id" value="{{$selected_course_id}}">
                                {{-- Course Price (display + hidden per-student) --}}
                                <div class="col-md-3">
                                    <div class="input__group mb-25">
                                        <label>{{__('Course Price')}} ({{get_currency_code()}}) <span class="text-danger">*</span></label>
                                        <input type="text" id="course_price_display" value="{{ old('price', number_format($cart->price ?? 0, 2)) }}" class="form-control" readonly>
                                        <input type="hidden" id="course_price" name="price" value="{{ old('price', number_format( (isset($course_unit_price) ? $course_unit_price : 0), 2, '.', '') ) }}">
                                    </div>
                                </div>

                                {{-- Number of students --}}
                                <div class="col-md-2">
                                    <div class="input__group mb-25">
                                        <label>{{ __('Number of Students') }}</label>
                                        <input type="number" name="students_count" id="students_count" class="form-control" value="{{ old('students_count', $cart->students_count ?? 1) }}" min="1" step="1" required>
                                    </div>
                                </div>

                                {{-- Discount for Course Price --}}
                                <div class="col-md-3">
                                    <div class="input__group mb-25">
                                        <label>{{ __('Discount Course Price') }}</label>
                                        <input type="number" step="0.01" min="0" name="discount_course_price" id="discount_course_price" class="form-control" value="{{ old('discount_course_price', isset($cart->discount) ? $cart->discount : 0) }}">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="input__group mb-25">
                                        <label>{{ __('Discount Type') }}</label>
                                        <select name="discount_type_course_price" id="discount_type_course_price" class="form-control">
                                            <option value="0" {{ old('discount_type_course_price', ($cart->discount_type ?? '0'))=='0'? 'selected':'' }}>Amount</option>
                                            <option value="1" {{ old('discount_type_course_price', ($cart->discount_type ?? '0'))=='1'? 'selected':'' }}>Percentage (%)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="input__group mb-25">
                                        <label>{{ __('Net Course Price') }}</label>
                                        <input type="text" class="form-control" id="net_course_price_display" readonly value="{{ old('net_course_price', number_format($cart->net_original_price ?? 0,2)) }}">
                                        <input type="hidden" name="net_course_price" id="net_course_price" value="{{ old('net_course_price', $cart->net_original_price ?? 0) }}">
                                    </div>
                                </div>

                                {{-- Admission block --}}
                                <div id="admission_block" class="w-100">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="input__group mb-25">
                                                <label>{{ __('Admission Price') }} ({{ get_currency_code() }}) <span class="text-danger">*</span></label>

                                                {{-- display admission total (per-student * students_count) --}}
                                                <input type="text" id="admission_fee_display"
                                                       value="{{ number_format( (float)($cart->admission_fee ?? ( (float)$admissionFee * ($cart->students_count ?? 1) ) ), 2) }}"
                                                       class="form-control" readonly>

                                                {{-- hidden per-student admission fee --}}
                                                <input type="hidden" id="admission_fee" name="admission_fee" value="{{ old('admission_fee', (float)$admissionFee) }}">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="input__group mb-25">
                                                <label>{{ __('Discount Admission Fee') }}</label>
                                                <input type="number" step="0.01" min="0" name="discount_admission_fee" id="discount_admission_fee" class="form-control" value="{{ old('discount_admission_fee', $cart->discount_admission_fee ?? 0) }}">
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="input__group mb-25">
                                                <label>{{ __('Discount Type') }}</label>
                                                <select name="discount_type_admission_fee" id="discount_type_admission_fee" class="form-control">
                                                    <option value="0" {{ old('discount_type_admission_fee', ($cart->discount_type_admission_fee ?? '0'))=='0'? 'selected':'' }}>Amount</option>
                                                    <option value="1" {{ old('discount_type_admission_fee', ($cart->discount_type_admission_fee ?? '0'))=='1'? 'selected':'' }}>Percentage (%)</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="input__group mb-25">
                                                <label>{{ __('Net Admission Fee') }}</label>
                                                <input type="text" class="form-control" id="net_admission_fee_display" readonly value="{{ old('net_admission_fee', number_format($cart->net_admission_fee ?? 0,2)) }}">
                                                <input type="hidden" name="net_admission_fee" id="net_admission_fee" value="{{ old('net_admission_fee', $cart->net_admission_fee ?? 0) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Total net price --}}
                                <div class="col-md-4 offset-md-8">
                                    <div class="input__group mb-25">
                                        <label class="fw-bold">{{ __('Total Net Price') }}</label>
                                        <input type="text" class="form-control fw-bold" id="net_price_display" readonly value="{{ old('net_price', number_format($cart->price ?? 0,2)) }}">
                                        <input type="hidden" name="net_price" id="net_price" value="{{ old('net_price', $cart->price ?? 0) }}">
                                    </div>
                                </div>

                                {{-- Month & Year --}}
                                {{-- <div class="col-md-6">
                                    <div class="input__group mb-25">
                                        <label class="label-text-title color-heading font-medium font-14 mb-3">
                                            {{ __('Month') }} <span class="text-danger">*</span>
                                        </label>
                                        <select name="month" id="month" class="form-select" required>
                                            <option value="">{{ __('Select Month') }}</option>
                                            @foreach(range(1,12) as $m)
                                                <option value="{{ $m }}" {{ (old('month', $cart->month ?? null) == $m) ? 'selected' : '' }}>
                                                    {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                                </option>
                                            @endforeach
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
                                            @foreach([2022,2023,2024,2025] as $y)
                                                <option value="{{ $y }}" {{ (old('year', $cart->year ?? null) == $y) ? 'selected' : '' }}>
                                                    {{ $y }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div> --}}

                                <input type="hidden" id="admission_applicable" name="admission_applicable" value="{{ old('admission_applicable', (isset($cart) && $cart->admission_fee > 0) ? '1' : '1') }}">
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12 text-right">
                                    <a href="{{ route('student_payment.pending') }}" class="btn btn-secondary">{{ __('Back') }}</a>
                                    <button class="btn btn-primary" type="submit">{{ __('Update') }}</button>
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
        // initialize select2
        // $("#student_id").select2({ width: '100%', placeholder: "Select Student" });

        // Ensure admission fee per-student is set from blade variable (admissionFee from controller)
        var admissionPerUnit = parseFloat("{{ (float)$admissionFee }}") || 0;
        // If a cart exists, we may have a course_unit_price computed in controller as $course_unit_price
        var initialCourseUnit = parseFloat("{{ isset($course_unit_price) ? $course_unit_price : (old('price', 0)) }}") || 0;
        // Write them into hidden inputs to match your JS expectations
        $("#course_price").val(initialCourseUnit);
        $("#admission_fee").val(admissionPerUnit);

        // If cart had admission_fee = 0, hide admission block initially (controller sets admission_applicable logic)
        var admission_applicable = "{{ old('admission_applicable', (isset($cart) && $cart->admission_fee > 0) ? '1' : '1') }}";
        $("#admission_applicable").val(admission_applicable);
        if (admission_applicable === '0') {
            $("#admission_block").hide();
        } else {
            $("#admission_block").show();
        }

        // The rest of your existing dynamic calculation JS (recalc, ajax on course change etc.)
        function parseFloatSafe(v){ return isNaN(parseFloat(v)) ? 0 : parseFloat(v); }
        function parseIntSafe(v){ return isNaN(parseInt(v)) ? 1 : parseInt(v); }

        function recalc() {
            const studentsCount = parseIntSafe($("#students_count").val());
            const coursePricePerUnit = parseFloatSafe($("#course_price").val()); // per student
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
            $("#net_course_price_display").val(netCourse.toFixed(2));
            $("#net_course_price").val(netCourse.toFixed(2));

            const admissionApplicable = $("#admission_applicable").val() === '1';
            let netAdmission = 0;
            if (admissionApplicable) {
                const admissionPer = parseFloatSafe($("#admission_fee").val());
                const admissionTotal = admissionPer * studentsCount;
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

        ['discount_course_price','discount_type_course_price','discount_admission_fee','discount_type_admission_fee','students_count']
            .forEach(function(id) {
                $("#"+id).on('input change', recalc);
            });

        // ajax get per-student price when course changed
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
                    var perUnitPrice = parseFloat(response) || 0;
                    $("#course_price").val(perUnitPrice);
                    recalc();
                },
                error: function(){
                    $("#course_price_display").val((0).toFixed(2));
                    $("#course_price").val(0);
                    recalc();
                }
            });

            checkAdmissionApplicability();
        });

        $('#student_id').change(function(){
            checkAdmissionApplicability();
        });

        function checkAdmissionApplicability() {
            var student_id = $('#student_id').val();
            var course_id = $('#course_id').val();
            if(!student_id || !course_id) {
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

        // initial recalc to populate displays
        recalc();
    });
    </script>
@endpush
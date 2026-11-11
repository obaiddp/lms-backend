@extends('layouts.admin')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-8">
                <h2>Approve Enrollment Request</h2>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('student.free_classes') }}" class="btn btn-secondary">Back to list</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <form id="approve-form" action="{{ route('admin.trial.approve.submit', $trial->id) }}" method="POST">
                    @csrf

                    {{-- Course Section --}}
                    <div class="row align-items-end mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Course Name</label>
                            <input type="text" class="form-control" value="{{ $course->title }}" readonly>
                            <input type="hidden" name="course_id" value="{{ $course->id }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Course Price</label>
                            <input type="text" class="form-control" id="course_price_display" value="{{ number_format((float)$course->price, 2) }}" readonly>
                            <input type="hidden" id="course_price" name="course_price" value="{{ (float)$course->price }}">
                        </div>

                        {{-- Number of students --}}
                        <div class="col-md-2">
                            <div class="input__group mb-25">
                                <label>{{ __('Number of Students') }}</label>
                                <input type="number" name="students_count" id="students_count" class="form-control" value="{{ old('students_count', 1) }}" min="1" step="1" required>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Discount Course Price</label>
                            <input type="number" step="0.01" min="0" name="discount_course_price" id="discount_course_price" class="form-control" value="0">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Discount Type Course Price</label>
                            <select name="discount_type_course_price" id="discount_type_course_price" class="form-control">
                                <option value="0">Amount</option>
                                <option value="1">Percentage (%)</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Net Course Price</label>
                            <input type="text" class="form-control" id="net_course_price_display" readonly>
                            <input type="hidden" name="net_course_price" id="net_course_price" value="0">
                        </div>
                    </div>

                    {{-- Admission Section --}}
                    <div class="row align-items-end mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Admission Fee</label>
                            <input type="text" class="form-control" id="admission_fee_display" value="{{ number_format((float)$admissionFee, 2) }}" readonly>
                            <input type="hidden" id="admission_fee" name="admission_fee" value="{{ (float)$admissionFee }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Discount Admission Fee</label>
                            <input type="number" step="0.01" min="0" name="discount_admission_fee" id="discount_admission_fee" class="form-control" value="0">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Discount Type Admission Fee</label>
                            <select name="discount_type_admission_fee" id="discount_type_admission_fee" class="form-control">
                                <option value="0">Amount</option>
                                <option value="1">Percentage (%)</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Net Admission Fee</label>
                            <input type="text" class="form-control" id="net_admission_fee_display" readonly>
                            <input type="hidden" name="net_admission_fee" id="net_admission_fee" value="0">
                        </div>
                    </div>

                    {{-- Total Net Price --}}
                    <div class="row mb-3">
                        <div class="col-md-4 offset-md-8">
                            <label class="form-label fw-bold">Total Net Price</label>
                            <input type="text" class="form-control fw-bold" id="net_price_display" readonly>
                            <input type="hidden" name="net_price" id="net_price" value="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <button class="btn btn-primary" type="submit">Save & Send Email with Challan</button>
                        <a href="{{ route('student.free_classes') }}" class="btn btn-secondary">Cancel</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection




@push('script')
<script>
(function(){
    function parseFloatSafe(v){ return isNaN(parseFloat(v)) ? 0 : parseFloat(v); }
    function parseIntSafe(v){ return isNaN(parseInt(v)) ? 1 : parseInt(v); }
    function recalc() {
        // Course Price Discount Calculation
        const studentsCount = parseIntSafe($("#students_count").val());
        const coursePricePerUnit = parseFloatSafe($("#course_price").val()); // per student
        // Updated (total) course price = per-unit * students_count
        const coursePrice = coursePricePerUnit * studentsCount;

        const discountCourse = parseFloatSafe(document.getElementById('discount_course_price').value);
        const discountTypeCourse = document.getElementById('discount_type_course_price').value;
        let discountAmountCourse = 0;

        if (discountTypeCourse === '0') {
            discountAmountCourse = discountCourse;
        } else {
            discountAmountCourse = (coursePrice * discountCourse) / 100;
        }

        if (discountAmountCourse > coursePrice) discountAmountCourse = coursePrice;
        const netCourse = Math.max(coursePrice - discountAmountCourse, 0);

        $("#course_price_display").val(coursePrice.toFixed(2));
        document.getElementById('net_course_price_display').value = netCourse.toFixed(2);
        document.getElementById('net_course_price').value = netCourse.toFixed(2);

        // Admission Fee Discount Calculation
        const admissionPerUnit = parseFloatSafe($("#admission_fee").val()); // per student
        const admissionFee = admissionPerUnit * studentsCount;
        // Show total (before discount)
        $("#admission_fee_display").val(admissionFee.toFixed(2));


        const discountAdmission = parseFloatSafe(document.getElementById('discount_admission_fee').value);
        const discountTypeAdmission = document.getElementById('discount_type_admission_fee').value;
        let discountAmountAdmission = 0;

        if (discountTypeAdmission === '0') {
            discountAmountAdmission = discountAdmission;
        } else {
            discountAmountAdmission = (admissionFee * discountAdmission) / 100;
        }

        if (discountAmountAdmission > admissionFee) discountAmountAdmission = admissionFee;
        const netAdmission = Math.max(admissionFee - discountAmountAdmission, 0);

        document.getElementById('net_admission_fee_display').value = netAdmission.toFixed(2);
        document.getElementById('net_admission_fee').value = netAdmission.toFixed(2);

        // Total Net Price
        const totalNet = netCourse + netAdmission;
        document.getElementById('net_price_display').value = totalNet.toFixed(2);
        document.getElementById('net_price').value = totalNet.toFixed(2);
    }

    // Event bindings
    ['discount_course_price', 'discount_type_course_price', 'discount_admission_fee', 'discount_type_admission_fee','students_count']
        .forEach(id => document.getElementById(id).addEventListener('input', recalc));
    document.getElementById('discount_type_course_price').addEventListener('change', recalc);
    document.getElementById('discount_type_admission_fee').addEventListener('change', recalc);

    // Initial calculation
    recalc();
})();
</script>
@endpush




{{-- @push('script')
<script>
(function(){
    function parseFloatSafe(v){ return isNaN(parseFloat(v)) ? 0 : parseFloat(v); }

    function recalc() {
        // Course Price Discount Calculation
        const coursePrice = parseFloatSafe(document.getElementById('course_price').value);
        const discountCourse = parseFloatSafe(document.getElementById('discount_course_price').value);
        const discountTypeCourse = document.getElementById('discount_type_course_price').value;
        let discountAmountCourse = 0;

        if (discountTypeCourse === '0') {
            discountAmountCourse = discountCourse;
        } else {
            discountAmountCourse = (coursePrice * discountCourse) / 100;
        }

        if (discountAmountCourse > coursePrice) discountAmountCourse = coursePrice;
        const netCourse = Math.max(coursePrice - discountAmountCourse, 0);

        document.getElementById('net_course_price_display').value = netCourse.toFixed(2);
        document.getElementById('net_course_price').value = netCourse.toFixed(2);

        // Admission Fee Discount Calculation
        const admissionFee = parseFloatSafe(document.getElementById('admission_fee').value);
        const discountAdmission = parseFloatSafe(document.getElementById('discount_admission_fee').value);
        const discountTypeAdmission = document.getElementById('discount_type_admission_fee').value;
        let discountAmountAdmission = 0;

        if (discountTypeAdmission === '0') {
            discountAmountAdmission = discountAdmission;
        } else {
            discountAmountAdmission = (admissionFee * discountAdmission) / 100;
        }

        if (discountAmountAdmission > admissionFee) discountAmountAdmission = admissionFee;
        const netAdmission = Math.max(admissionFee - discountAmountAdmission, 0);

        document.getElementById('net_admission_fee_display').value = netAdmission.toFixed(2);
        document.getElementById('net_admission_fee').value = netAdmission.toFixed(2);

        // Total Net Price
        const totalNet = netCourse + netAdmission;
        document.getElementById('net_price_display').value = totalNet.toFixed(2);
        document.getElementById('net_price').value = totalNet.toFixed(2);
    }

    // Event bindings
    ['discount_course_price', 'discount_type_course_price', 'discount_admission_fee', 'discount_type_admission_fee']
        .forEach(id => document.getElementById(id).addEventListener('input', recalc));
    document.getElementById('discount_type_course_price').addEventListener('change', recalc);
    document.getElementById('discount_type_admission_fee').addEventListener('change', recalc);

    // Initial calculation
    recalc();
})();
</script>
@endpush --}}
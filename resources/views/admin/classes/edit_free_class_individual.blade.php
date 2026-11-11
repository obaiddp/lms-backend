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
                            <h2>{{ __('Edit Free Class') }}</h2>
                        </div>
                    </div>
                    <div class="breadcrumb__content__right">
                        <nav aria-label="breadcrumb">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a
                                        href="{{route('admin.dashboard')}}">{{__('Dashboard')}}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ __('Edit Free Class') }}</li>
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
                        <h2>{{ __('Edit Free Class') }}</h2>
                    </div>
                    <form action="{{route('classes.update_free_class_individual',['id'=>$class_id])}}" method="post" class="form-horizontal"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                          
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Student')
                                        }} <span class="text-danger">*</span></label>
                                        <input type="text" name="student_name" id="student_name" value="{{$given_student_name}}" readonly>
                                        <input type="hidden" name="student_id" id="student_id" value="{{$given_student_id}}">
                                        {{-- <input type="hidden" name="free_class_id" id="free_class_id" value="{{$given_free_class_id}}"> --}}
                                    @if ($errors->has('student_id'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('student_id') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Instructor')
                                        }} <span class="text-danger">*</span></label>
                                    <select class="form-control" name="instructor_id" id="instructor_id" required>
                                        <option value>{{ __("Select Instructor") }}</option>
                                        @foreach ($instructors as $instructor)
                                        <option value="{{ $instructor->id }}" <?php
                                            if ($instructor->id == $class_detail->instructor_id) {
                                                echo "selected";
                                            }
                                            ?>>{{ $instructor->first_name.' '.$instructor->last_name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('instructor_id'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('instructor_id') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="input__group mb-25">
                                    <label class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Course')
                                        }} <span class="text-danger">*</span></label>
                                     <select class="form-control" name="course_id" id="course_id" required>
                                        <option value="">{{ __("Select Course") }}</option>
                                        @foreach ($courses as $course)
                                        <option value="{{ $course->id }}" <?php
                                            if ($course->id == $class_detail->course_id) {
                                                echo "selected";
                                            }
                                            ?>>{{ $course->title}}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('course_id'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('course_id') }}</span>
                                    @endif
                                </div>
                            </div>
                       
                            {{-- <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{__('Total Classes')}} <span class="text-danger">*</span></label>
                                    <input type="number" name="total_classes" value="{{old('total_classes')}}"
                                        placeholder="{{__('Total Classes')}}" class="form-control" required>
                                    @if ($errors->has('total_classes'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('total_classes') }}</span>
                                    @endif
                                </div>
                            </div> --}}

                          
                            <div class="col-md-12">
                                <div class="input__group mb-25">
                                    <label class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Prefer Days')
                                        }} <span class="text-danger">*</span></label>
                                    <select id="select2" class="form-control" name="preferdays[]" required>
                                        {{-- <option value ="" disabled>{{ __("Select Days") }}</option> --}}
                                        <option value="Monday"<?php
                                        if(!empty($prefer_day_array)){
                                     if( in_array("Monday", $prefer_day_array)){ echo 'selected="selected"'; }} ?>>Monday</option>
                                        <option value="Tuesday"<?php
                                        if(!empty($prefer_day_array)){
                                     if( in_array("Tuesday", $prefer_day_array)){ echo 'selected="selected"'; }} ?>>Tuesday</option>
                                        <option value="Wednesday"<?php
                                        if(!empty($prefer_day_array)){
                                     if( in_array("Wednesday", $prefer_day_array)){ echo 'selected="selected"'; }} ?>>Wednesday</option>
                                        <option value="Thursday"<?php
                                        if(!empty($prefer_day_array)){
                                     if( in_array("Thursday", $prefer_day_array)){ echo 'selected="selected"'; }} ?>>Thursday</option>
                                        <option value="Friday"<?php
                                        if(!empty($prefer_day_array)){
                                     if( in_array("Friday", $prefer_day_array)){ echo 'selected="selected"'; }} ?>>Friday</option>
                                        <option value="Saturday"<?php
                                        if(!empty($prefer_day_array)){
                                     if( in_array("Saturday", $prefer_day_array)){ echo 'selected="selected"'; }} ?>>Saturday</option>
                                        <option value="Sunday"<?php
                                        if(!empty($prefer_day_array)){
                                     if( in_array("Sunday", $prefer_day_array)){ echo 'selected="selected"'; }} ?>>Sunday</option>
                                    </select>
                               
                                </div>
                            </div>
                            
                           
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{ __('Prefer Time') }} <span class="text-danger">*</span></label>
                                
                                    <input type="time" name="prefer_time"  value="{{$class_detail->class_time}}"
                                        placeholder="{{ __('Prefer Time') }}" class="form-control"  required>
                                    @if ($errors->has('prefer_time'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('prefer_time') }}</span>
                                    @endif
                                </div>
                            </div>
                          
                          
                            
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{ __('Select Date') }} <span class="text-danger">*</span></label>
                                    <input type="date" name="prefer_date" value="{{$given_lesson_date}}"
                                        placeholder="{{ __('Select Date') }}" class="form-control" required>
                                    @if ($errors->has('prefer_date'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('prefer_date') }}</span>
                                    @endif
                                </div>
                            </div>
                          

                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{__('Select Duration')}} <span class="text-danger">*</span></label>
                                    <input type="number" name="duration" value="{{$class->duration}}"
                                        placeholder="{{__('Select Duration')}}" class="form-control" required>
                                    @if ($errors->has('duration'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('duration') }}</span>
                                    @endif
                                </div>
                            </div>



                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Learning Tool')
                                        }} <span class="text-danger">*</span></label>
                                    <select class="form-control" name="learning_tool" required>
                                        <option value ="">{{ __("Select Learning Tool") }}</option>
                                        {{-- <option value="Jitsi">JitsiMeet</option> --}}
                                        <option value="BBB"<?php
                                        if ("BBB" == $class->learning_tool) {
                                            echo "selected";
                                        }
                                        ?>>BigBlueButton</option>
                                        <option value="Zoom"<?php
                                        if ("Zoom" == $class->learning_tool) {
                                            echo "selected";
                                        }
                                        ?>>Zoom</option>
                                         <option value="Google_Meet"<?php
                                         if ("Google_Meet" == $class->learning_tool) {
                                             echo "selected";
                                         }
                                         ?>>Google Meet</option>
                                    </select>
                               
                                </div>
                            </div>
                        </div>
                     

                        <div class="row mb-3">
                            <div class="col-md-12 text-right">
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
<link rel="stylesheet" href="{{asset('common/css/select2.css')}}">
@endpush

@push('script')
<script src="{{asset('admin/js/custom/image-preview.js')}}"></script>
<script src="{{asset('admin/js/custom/admin-profile.js')}}"></script>
<script src="{{asset('common/js/select2.min.js')}}"></script>
<script>
    $(document).ready(function () {
        $("#select2").select2({
     width: '100%',
  placeholder: "Select Day",
  multiple: true,
//   allowClear: true
});
    });
     

    $('#instructor_id').change(function (e) { 
        e.preventDefault();
        var instructor_id = $(this).val();
        $.ajax({
            type: "POST",
            headers:
                        {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
            url: '{{route('classes.get_tutor_courses')}}',
            data: {instructor_id:instructor_id},
            dataType: "JSON",
            success: function (response) {
                var select = $('#course_id');
                        select.empty();
                        select.append('<option value="">Select Course</option>');
                        $.each(response, function(key, value) {
                            select.append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                console.log(response)
            }
        });
        // alert(instructor_id)
        
    });
</script>
@endpush
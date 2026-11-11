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
                                <h2>{{ __('Assign Resource') }}</h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('Dashboard')}}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ __('Assign Resource') }}</li>
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
                            <h2>{{ __('AssignResource') }}</h2>
                        </div>
                        <form action="{{route('admin.course.store_assigned_resources')}}" method="post" class="form-horizontal" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                           
                                <input type="hidden" name="course_id" value={{$courseID}}>
                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label
                                        class="label-text-title color-heading font-medium font-14 mb-3">{{
                                        __('Resources') }} <span class="text-danger">*</span></label>
                                    <select style="max-height: 6rem !important;overflow: auto !important;"  multiple="multiple" name="resource[]" id="resource" class="form-select assign_resource"
                                        required>
                                        <option value="">{{ __('Select Resources') }}</option>
                                        @foreach($resources as $resource)
                                        <option value="{{$resource->id}}"<?php if( in_array($resource->id, $resourceIDs)){ echo 'selected="selected"'; } ?> >{{$resource->name}}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('category_id'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('category_id') }}</span>
                                    @endif
                                    </div>
                                </div>
                        
                           
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
            $('#resource').select2({
    // dropdownParent: $('#edit_drop_modal'),
    width: '100%',
    allowClear: true,
    placeholder: 'Select Resources',
 
   
});

        });
    $('#category_id').change(function (e) { 
        e.preventDefault();
        var category_id = $(this).val();
  
        $.ajax({
            type: "POST",
            headers:
                        {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
            url: '{{route('classes.get_category_subcategories')}}',
            data: {category_id:category_id},
            dataType: "JSON",
            success: function (response) {
                var select = $('#subcategory_id');
                        select.empty();
                        select.append('<option value="">Select Subcategory</option>');
                        $.each(response, function(key, value) {
                            select.append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                console.log(response)
            }
        });
 
    });
    </script>
@endpush

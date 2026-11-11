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
                            <h2>{{ __('Add Time Slot') }}</h2>
                        </div>
                    </div>
                    <div class="breadcrumb__content__right">
                        <nav aria-label="breadcrumb">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a
                                        href="{{route('admin.dashboard')}}">{{__('Dashboard')}}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ __('Add Time Slot') }}</li>
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
                        <h2>{{ __('Add Time Slot') }}</h2>
                    </div>
                    <form action="{{ route('time_slots.store') }}" method="post" class="form-horizontal"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            {{-- <div class="col-md-6">

                                <div class="input__group mb-25">
                                    <label for="date" class="form-label">{{__('Date')}} <span class="text-danger">*</span></label>
                                 
                                    <input type="date" class="form-control" id="date" name="date" required 
                                    min="{{ now()->addDay()->format('Y-m-d') }}">
                                    @if ($errors->has('date'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('date') }}</span>
                                    @endif
                                </div>
                            </div> --}}
                            <div class="col-md-6">

                                <div class="input__group mb-25">
                                    <label for="start_time" class="form-label">{{__('Start Time')}} <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="start_time" name="start_time" required>
                                    @if ($errors->has('start_time'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('start_time') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">

                                <div class="input__group mb-25">
                                    <label for="end_time" class="form-label">{{__('End Time')}} <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="end_time" name="end_time" required>
                                    @if ($errors->has('end_time'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('end_time') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $timeSlot->is_active ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">{{__('Active')}}</label>
                                 
                                    @if ($errors->has('is_active'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('is_active') }}</span>
                                    @endif
                                </div>
                            </div>

                         
                              
                        </div>
                     
                        <div class="row mb-3">
                            <div class="col-md-12 text-right">
                                <button class="btn btn-primary" type="submit">{{ __('Save') }}</button>
                                <a href="{{ route('time_slots.index') }}" class="btn btn-secondary">Cancel</a>
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
@endpush
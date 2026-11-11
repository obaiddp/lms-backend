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
                            <h2>{{ __('Edit Time Slot') }}</h2>
                        </div>
                    </div>
                    <div class="breadcrumb__content__right">
                        <nav aria-label="breadcrumb">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a
                                        href="{{route('admin.dashboard')}}">{{__('Dashboard')}}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ __('Edit Time Slot') }}</li>
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
                        <h2>{{ __('Edit Time Slot') }}</h2>
                    </div>
                    <form action="{{ route('time_slots.update',['slot'=>$timeSlot->id]) }}" method="post" class="form-horizontal"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            {{-- <div class="col-md-6">

                                <input type="hidden" name="date" value="{{ $timeSlot->date->format('Y-m-d') }}">
                                <div class="input__group mb-25">
                                    <label class="form-label">{{__('Date (Read Only)')}} </label>
                                    <input type="text" class="form-control"  value="{{ $timeSlot->date->format('D, M j, Y') }}" readonly>
                                    @if ($errors->has('date'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('date') }}</span>
                                    @endif
                                </div>
                            </div> --}}
                            <div class="col-md-6">

                                <div class="input__group mb-25">
                                    <label for="start_time" class="form-label">{{__('Start Time')}} <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="start_time" name="start_time" value="{{ old('start_time', $timeSlot->start_time->format('H:i')) }}" required>
                                    @if ($errors->has('start_time'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('start_time') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">

                                <div class="input__group mb-25">
                                    <label for="end_time" class="form-label">{{__('End Time')}} <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="end_time" name="end_time" value="{{ old('end_time', $timeSlot->end_time->format('H:i')) }}" required>
                                    @if ($errors->has('end_time'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('end_time') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $timeSlot->is_active) ? 'checked' : '' }}>
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
                                <button class="btn btn-primary" type="submit">{{ __('Update') }}</button>
                                <a href="{{ route('time_slots.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>






                    <div class="mt-5">
                        <h4>{{__('Bookings for This Slot')}}</h4>
                        @if($timeSlot->bookings->isEmpty())
                            <p>{{__('No bookings yet for this time slot.')}}</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Email</th>
                                            <th>Booked At</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($timeSlot->bookings as $booking)
                                        <tr>
                                            <td>{{ $booking->user->name }}</td>
                                            <td>{{ $booking->user->email }}</td>
                                            <td>{{ $booking->created_at->format('M j, Y h:i A') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $booking->status === 'booked' ? 'success' : 'secondary' }}">
                                                    {{ ucfirst($booking->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>


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
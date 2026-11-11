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
                                <h2>{{ __('Application Settings') }}</h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('Dashboard')}}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ __(@$title) }}</li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-4">
                    @include('admin.application_settings.sidebar')
                </div>
                <div class="col-lg-9 col-md-8">
                    <div class="customers__area bg-style mb-30">
                        <div class="item-title d-flex justify-content-between">
                            <h2>{{ __('Edit Zoom Account') }}</h2>
                            <a href="{{ route('settings.zoom_account.index') }}" class="btn btn-success btn-sm" >
                                <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                            </a>
                        </div>
                        <form action="{{route('settings.zoom_account.update', [$zoomAccount->id])}}" method="post" class="form-horizontal">
                            @csrf
                            @method('patch')
                            <div class="row">

                        <div class="row input__group mb-25">
                            <label class="col-lg-3">{{ __('Zoom SDK Key') }}<span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-9">
                            <input required type="text" name="sdk_key" value="{{$zoomAccount->sdk_key}}" class="form-control" placeholder="{{__('Zoom SDK Key')}}" required>
                        @if ($errors->has('sdk_key'))
                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('sdk_key') }}</span>
                        @endif
                            </div>
                        </div>
                        <div class="row input__group mb-25">
                            <label class="col-lg-3">{{ __('Zoom SDK Secret') }}<span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-9">
                            <input type="text" name="sdk_secret" value="{{$zoomAccount->sdk_secret}}" class="form-control" placeholder="{{__('Zoom SDK Secret')}}" required>
                        @if ($errors->has('sdk_secret'))
                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('sdk_secret') }}</span>
                        @endif</div>
                        </div>

                        <div class="row input__group mb-25">
                            <label class="col-lg-3">{{ __('Zoom API Key') }}<span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-9">
                            <input type="text" name="api_key" value="{{$zoomAccount->api_key}}" class="form-control" placeholder="{{__('Zoom API Key')}}" required>
                        @if ($errors->has('api_key'))
                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('api_key') }}</span>
                        @endif</div>
                        </div>


                        <div class="row input__group mb-25">
                            <label class="col-lg-3">{{ __('Zoom API Secret') }}<span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-9">
                            <input type="text" name="api_secret" value="{{$zoomAccount->api_secret}}" class="form-control" placeholder="{{__('Zoom API Secret')}}" required>
                        @if ($errors->has('api_secret'))
                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('api_secret') }}</span>
                        @endif</div>
                        </div>


                        <div class="row input__group mb-25">
                            <label class="col-lg-3">{{ __('Zoom API JWT Token') }}<span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-9">
                            <input type="text" name="api_jwt_token" value="{{$zoomAccount->api_jwt_token}}" class="form-control" placeholder="{{__('Zoom API JWT TOKEN')}}" required>
                        @if ($errors->has('api_jwt_token'))
                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('api_jwt_token') }}</span>
                        @endif</div>
                        </div>

                        <div class="row input__group mb-25">
                            <label class="col-lg-3">{{ __('Status') }}<span
                                    class="text-danger"></span></label>
                            <div class="col-lg-9">
                            <select name="status" id="status" class="" required>
                            <option value="">{{__('Select Option')}}</option>
                            <option value="1" {{ $zoomAccount->status == 1 ? 'selected' : '' }}>{{ __('Active') }}</option>
                            <option value="0" {{ $zoomAccount->status != 1 ? 'selected' : '' }}>{{ __('Disable') }}</option>
                        </select>    
                        </div>
                        </div>

                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12 text-right">
                                    @updateButton
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

@extends('layouts.admin')

{{-- @section('breadcrumb')
    <div class="page-banner-content text-center">
        <h3 class="page-banner-heading text-white pb-15"> {{ __('Resources') }} </h3>

        <!-- Breadcrumb Start-->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item font-14"><a href="{{route('instructor.dashboard')}}">{{__('Dashboard')}}</a></li>
                <li class="breadcrumb-item font-14"><a href="{{ route('instructor.course') }}">{{__('My Courses')}}</a></li>
                <li class="breadcrumb-item font-14"><a href="{{route('resource.index', [$course->uuid])}}">{{__('Resource List') }}</a></li>
                <li class="breadcrumb-item font-14 active" aria-current="page">{{ __('Add Resources') }}</li>
            </ol>
        </nav>
    </div>
@endsection --}}

@section('content')
<div class="page-content">
    <div class="container-fluid">
            {{-- <div class="instructor-my-courses-title d-flex justify-content-between align-items-center">
                <h6>{{ __('Add Resource') }}</h6>
                <p>{{ @$course->title }}</p>
            </div> --}}
            
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb__content">
                    <div class="breadcrumb__content__left">
                        <div class="breadcrumb__title">
                            <h2>{{__('Add Resource')}}</h2>
                        </div>
                    </div>
                    <div class="breadcrumb__content__right">
                        <nav aria-label="breadcrumb">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('Dashboard')}}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{__('Add Resource')}}</li>
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
                        <h6>{{ __('Add Resource') }}</h6>
                        <p>{{ @$course->title }}</p>
                    </div>
                    <form class="create-new-quiz-form" action="{{ route('admin.course.resource.store', $course->uuid) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-30">
                            <div class="col-md-12">
                                <div class="create-assignment-upload-files">
                                    <div>
                                        <input type="file" name="file" class="form-control" title="Upload Your Files" />
                                    </div>
                                    <p class="font-14 color-heading text-center mt-2 color-gray">{{ __('Accepted files') }}: ZIP</p>
                                </div>
                            </div>
                            @if ($errors->has('file'))
                                <span class="text-danger mt-2"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('file') }}</span>
                            @endif
                        </div>

                        <div>
                            <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

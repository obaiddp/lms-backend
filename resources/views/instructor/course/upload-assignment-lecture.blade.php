@extends('layouts.instructor')

@section('breadcrumb')
<div class="page-banner-content text-center">
    <h3 class="page-banner-heading text-white pb-15"> {{__('Upload Self Learning Course')}} </h3>

    <!-- Breadcrumb Start-->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb justify-content-center">
            <li class="breadcrumb-item font-14"><a href="{{route('admin.dashboard')}}">{{__('Dashboard')}}</a></li>
            <li class="breadcrumb-item font-14"><a href="{{ route('admin.course.index') }}">{{__('My Courses')}}</a></li>
            <li class="breadcrumb-item font-14 active" aria-current="page">{{__('Upload Course')}}</li>
        </ol>
    </nav>
</div>
@endsection

@section('content2')
<div class="instructor-profile-right-part instructor-upload-course-box-part">
    <div class="instructor-upload-course-box">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div id="msform">
                        <!-- progressbar -->
                        <ul id="progressbar"
                            class="upload-course-item-block d-flex align-items-center justify-content-center">
                            <li class="active" id="account"><strong>{{ __('Overview') }}</strong></li>
                            <li class="active" id="quiz"><strong>{{ __('Quiz') }}</strong></li>
                            <li class="active" id="assignment"><strong>{{ __('Assignment') }}</strong></li>
                            <li class="active" id="personal"><strong>{{ __('Content') }}</strong></li>
                            <li id="instructor"><strong>{{ __('Instructors') }}</strong></li>
                            <li id="confirm"><strong>{{ __('Submit Process') }}</strong></li>
                        </ul>

                        <!-- Upload Course Step-1 Item Start -->
                        <div class="upload-course-step-item upload-course-overview-step-item">
                            <!-- Upload Course Step-2 Item Start -->
                            <div class="upload-course-step-item upload-course-video-step-item">

                            @if(auth()->user()->role == 2) 
                                <form method="POST" action="{{route('store.lecture', [$course->uuid, $lesson->uuid])}}"
                                    class="row g-3 needs-validation" novalidate enctype="multipart/form-data">
                            @elseif(auth()->user()->role == 1) 
                            <form method="POST" action="{{route('admin.store.assignment.lecture', [$course->uuid, $lesson->uuid])}}"
                                    class="row g-3 needs-validation" novalidate enctype="multipart/form-data">
                                    @endif
                                    @csrf
                                    <!-- Upload Course Video-4 start -->
                                    <div id="upload-course-video-4">
                                        <div class="upload-course-item-block course-overview-step1 radius-8">

                                          

                                         
                                            <div class="main-upload-video-processing-box">
                                                <div class="d-flex main-upload-video-processing-item">
                                                    <div class="flex-grow-1 ms-3">
                                                        <div class="row mb-30">
                                                            <div class="col-md-12">
                                                                <label class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Lecture Assignment') }} <span class="text-danger">*</span></label>
                                                                <select  name="lecture_assignment_id" id="lecture_assignment_id" class="select2 form-select">
                                                                <option value="">{{ __('Select Assignment') }}</option>
                                                                @foreach($lesson_assignments as $assignment)
                                                                <option value="{{$assignment->id}}">{{$assignment->name}}</option>
                                                                @endforeach
                                                                </select>

                                                                @if ($errors->has('lecture_assignment_id'))
                                                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                                                    $errors->first('lecture_assignment_id') }}</span>
                                                                @endif

                                                            </div>
                                                        </div>

                                                        <div class="row mb-30">
                                                            <div class="col-md-12">
                                                                <label class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Lecture Title') }} <span class="text-danger">*</span></label>
                                                                <input type="text" name="title" value="{{old('title')}}" class="form-control" placeholder="{{ __('First steps') }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-30">
                                                            <div class="col-md-12">
                                                                <label
                                                                    class="label-text-title color-heading font-medium font-16 mb-3">{{
                                                                    __('Learner\'s Visibility') }} <span
                                                                        class="text-danger">*</span></label>
                                                                <select name="lecture_type" class="form-select"
                                                                    required>
                                                                    <option value="">--Select Option--</option>
                                                                    <option value="1" @if(old('lecture_type')==1)
                                                                        selected @endif>{{ __('Show') }}</option>
                                                                    <option value="2" @if(old('lecture_type')==2)
                                                                        selected @endif>{{ __('Lock') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-30 d-none" id="fileDuration">
                                                            <div class="col-md-12">
                                                                <label class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Lesson File Duration') }} (00:00) <span class="text-danger">*</span></label>
                                                                <input type="text" name="youtube_file_duration" value="{{old('file_duration')}}" class="form-control customFileDuration" placeholder="{{ __('First file duration') }}" >
                                                                @if ($errors->has('youtube_file_duration'))
                                                                <span class="text-danger"><i
                                                                        class="fas fa-exclamation-triangle"></i> {{
                                                                    $errors->first('youtube_file_duration') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        @if($course->drip_content == DRIP_AFTER_DAY)
                                                        <div class="row mb-30" id="drip-day">
                                                            <div class="col-md-12">
                                                                <label
                                                                    class="label-text-title color-heading font-medium font-16 mb-3">{{
                                                                    __('Lesson available after x days') }} <span class="text-danger">*</span></label>
                                                                <input type="number" min=1 required name="after_day"
                                                                    value="{{old('unlock_date', 0)}}"
                                                                    class="form-control" placeholder="Days">
                                                                @if ($errors->has('after_day'))
                                                                <span class="text-danger"><i
                                                                        class="fas fa-exclamation-triangle"></i> {{
                                                                    $errors->first('after_day') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        @elseif($course->drip_content == DRIP_UNLOCK_DATE)
                                                        <div class="row mb-30" id="drip-date">
                                                            <div class="col-md-12">
                                                                <div class="input__group text-black">
                                                                    <label
                                                                        class="label-text-title color-heading font-medium font-16 mb-3">{{
                                                                        __('Lesson available by date') }} <span class="text-danger">*</span></label>
                                                                    <input type="date" name="unlock_date"
                                                                        value="{{old('unlock_date')}}"
                                                                        class="form-control" required>
                                                                    @if ($errors->has('unlock_date'))
                                                                    <span class="text-danger"><i
                                                                            class="fas fa-exclamation-triangle"></i> {{
                                                                        $errors->first('unlock_date') }}</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @elseif($course->drip_content == DRIP_PRE_IDS)
                                                        <div class="row mb-30" id="drip-pre-requisite">
                                                            <div class="col-md-12">
                                                                <label
                                                                    class="label-text-title color-heading font-medium font-16 mb-3">{{
                                                                    __('Pre-requisites lesson') }}
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <select name="pre_ids[]" required class="select2 form-select" multiple>
                                                                    @foreach ($lessons as $pr_lesson)
                                                                    <optgroup label="{{ $pr_lesson->name }}">
                                                                        @foreach ($pr_lesson->lectures as $pr_lecture)
                                                                        <option value="{{ $pr_lecture->id }}">{{ $pr_lecture->title }}</option>
                                                                        @endforeach
                                                                      </optgroup>
                                                                    @endforeach
                                                                </select>

                                                                @if ($errors->has('pre_ids'))
                                                                <span class="text-danger"><i
                                                                        class="fas fa-exclamation-triangle"></i> {{
                                                                    $errors->first('pre_ids') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        @endif
                                                        <div class="row mb-30">
                                                            <div
                                                                class="col-md-12 main-upload-video-processing-item-btns">
                                                                <button style="width: max-content !important; border-radius: 0.4rem !important;" type="submit"
                                                                    class="theme-btn upload-video-processing-item-save-btn">{{__('Save')}}</button>
                                                                    @if(auth()->user()->role == 2) 
                                                                <a href="{{route('instructor.course.edit', [$course->uuid, 'step=lesson'])}}"
                                                                    class="theme-btn default-hover-btn default-back-btn theme-button3">{{__('Back')}}</a>
                                                                    @elseif(auth()->user()->role == 1) 
                                                                    <a href="{{route('admin.course.edit', [$course->uuid, 'step=lesson'])}}"
                                                                    class="theme-btn default-hover-btn default-back-btn theme-button3">{{__('Back')}}</a>
                                                                    @endif

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <!-- Upload Course Video-4 end -->
                                </form>
                            </div>
                            <!-- Upload Course Step-6 Item End -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" value="{{ old('type') }}" class="oldTypeYoutube">
@endsection

@push('style')
<link rel="stylesheet" href="{{asset('frontend/assets/css/custom/img-view.css')}}">

<link rel="stylesheet" href="{{asset('common/css/select2.css')}}">
<!-- Summernote CSS - CDN Link -->
<link href="{{ asset('common/css/summernote/summernote.min.css') }}" rel="stylesheet">
<link href="{{ asset('common/css/summernote/summernote-lite.min.css') }}" rel="stylesheet">
<!-- //Summernote CSS - CDN Link -->
@endpush

@push('script')

<script src="{{asset('common/js/select2.min.js')}}"></script>
<script src="{{asset('frontend/assets/js/custom/form-validation.js')}}"></script>
<script src="{{asset('frontend/assets/js/custom/img-view.js')}}"></script>
<script src="{{asset('frontend/assets/js/custom/upload-lesson.js')}}"></script>

<!-- Summernote JS - CDN Link -->
<script src="{{ asset('common/js/summernote/summernote-lite.min.js') }}"></script>
<script>
    $(document).ready(function() {
        // $('#lecture_resources').select2();

        $('.select2').select2();
        $("#summernote").summernote({dialogsInBody: true});
        $('.dropdown-toggle').dropdown();
    });
</script>
<!-- //Summernote JS - CDN Link -->
@endpush


<style>
.close-btn-add-course {
    position: absolute;
    top: 1rem !important;
    right: 0.4rem !important;
    width: 3rem !important;
}

a.close-btn2-add-course{
    display: flex;
    justify-content: center;
    align-items: center;
    min-width: 1.5rem !important;
    height: 1.5rem !important;
    max-width: 1.5rem !important;
    border-radius: 100%;
}

ul#progressbar {
    position: sticky;
    top: 6rem !important;
    z-index: 998;
    background: white;
}
</style>
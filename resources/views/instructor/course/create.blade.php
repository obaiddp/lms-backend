@extends('layouts.instructor')

@section('header-new')
<div class="page-banner-content text-center" style="margin-top: 20px; margin-bottom: 20px;">
    <h4 class="pb-15"> {{__('Upload Self Learning Course')}} </h4>
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
                            <li id="quiz"><strong>{{ __('Quiz') }}</strong></li>
                            <li id="assignment"><strong>{{ __('Assignment') }}</strong></li>
                            <li id="personal"><strong>{{ __('Content') }}</strong></li>
                            <li id="instructor"><strong>{{ __('Instructors') }}</strong></li>
                            <li id="confirm"><strong>{{ __('Submit Process') }}</strong></li>
                        </ul>

                        <!-- Upload Course Step-1 Item Start -->
                        <div class="upload-course-step-item upload-course-overview-step-item">
                            <!-- Upload Course Overview-1 start -->
                            <div id="upload-course-overview-1">
                                @if(auth()->user()->role == 2) 
                                <form method="POST" action="{{route('instructor.course.store')}}" enctype="multipart/form-data" id="step1"
                                @elseif(auth()->user()->role == 1)  
                                <form method="POST" action="{{route('admin.course.store')}}" enctype="multipart/form-data" id="step1"
                                @endif
                                    class="row g-3 needs-validation" novalidate>
                                    @csrf
                                    @if(get_option('courseUploadRuleTitle'))
                                        <div class="upload-course-item-block course-overview-step1 radius-8 mb-30">
                                            <div class="upload-course-item-block-title mb-3">
                                                <h6 class="font-20">{{ __(get_option('courseUploadRuleTitle')) }}</h6>
                                            </div>
                                            <ul class="mb-30">
                                                @foreach($rules as $rule)
                                                <li><span class="iconify" data-icon="akar-icons:arrow-right"></span>{{
                                                    $rule->description }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    <div class="upload-course-item-block course-overview-step1 radius-8 ">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="upload-course-item-block-title mb-3">
                                                    <h6 class="font-20">{{ __('Course Details') }}</h6>
                                                </div>
                                                <div class="row mb-30">
                                                    <div class="col-md-6">
                                                        <div class="label-text-title color-heading font-medium font-16 mb-3">{{
                                                            __('Course Type') }}
                                                            <span class="text-danger">*</span>
                                                        </div>

                                                        <select name="course_type" id="course_type" class="form-select"
                                                            required>
                                                            <option value="">{{ __('Select Course
                                                                Type') }}</option>
                                                            <option value="{{ COURSE_TYPE_GENERAL }}"
                                                                {{old('course_type')==COURSE_TYPE_GENERAL ? 'selected' : '' }}>
                                                                General</option>
                                                            <option value="{{ COURSE_TYPE_SCORM }}"
                                                                {{old('course_type')==COURSE_TYPE_SCORM ? 'selected' : '' }}>
                                                                SCORM</option>
                                                        </select>

                                                        @if ($errors->has('course_type'))
                                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                                            $errors->first('course_type') }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="label-text-title color-heading font-medium font-16 mb-3">
                                                            {{ __('Course Title') }}
                                                            <span class="text-danger">*</span>
                                                        </div>
                                                        <input type="text" name="title" value="{{old('title')}}"
                                                            class="form-control" placeholder="Type your course title" required>
                                                        @if ($errors->has('title'))
                                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                                            $errors->first('title') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="row mb-30">
                                                    <div class="col-md-12">
                                                        <div class="label-text-title color-heading font-medium font-16 mb-3">
                                                            {{ __('Course Subtitle') }}
                                                            <span class="text-danger">*</span>
                                                        </div>
                                                        <textarea class="form-control" name="subtitle" cols="30" rows="10"
                                                            required
                                                            placeholder="Course subtitle in 1000 characters">{{old('subtitle')}}</textarea>
                                                        @if ($errors->has('subtitle'))
                                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                                            $errors->first('subtitle') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="row mb-30">
                                                    <div class="col-md-12">
                                                        <div class="label-text-title color-heading font-medium font-16 mb-3">
                                                            {{ __('Short Description') }}
                                                        </div>
                                                        <textarea class="form-control"
                                                            cols="30"
                                                            rows="5"
                                                            name="short_description"
                                                            
                                                            class="form-control"
                                                            placeholder="Short Description"
                                                            >{{old('short_description')}}</textarea>
                                                        @if ($errors->has('short_description'))
                                                            <span class="text-danger"><i
                                                                                        class="fas fa-exclamation-triangle"></i>
                                                                            {{ $errors->first('short_description') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if(get_option('subscription_mode'))
                                                    <div class="row mb-30">
                                                        <div class="col-md-12">
                                                            <div class="label-text-title color-heading font-medium font-16 mb-3">{{
                                                                __('Enable for subscription') }}
                                                                <span class="text-danger">*</span>
                                                            </div>

                                                            <select name="is_subscription_enable" id="is_subscription_enable" class="form-select"
                                                                required>
                                                                <option value="{{ PACKAGE_STATUS_ACTIVE }}"
                                                                    {{old('is_subscription_enable')==PACKAGE_STATUS_ACTIVE ? 'selected' : '' }}>
                                                                    {{ __("Enable") }}</option>
                                                                <option value="{{ PACKAGE_STATUS_DISABLED }}"
                                                                    {{old('is_subscription_enable')==PACKAGE_STATUS_DISABLED ? 'selected' : '' }}>
                                                                    {{ __("Disabled") }}</option>
                                                            </select>

                                                            @if ($errors->has('is_subscription_enable'))
                                                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                                                $errors->first('is_subscription_enable') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="row mb-30">
                                                    <div class="col-md-12">
                                                        <div class="label-text-title color-heading font-medium font-16 mb-3">
                                                            {{ __('Course Description Key Points') }}
                                                            <span class="text-danger">*</span>
                                                        </div>
                                                        <div id="add_repeater">
                                                            <div data-repeater-list="key_points" class="">
                                                                <label for="name" class="text-lg-right text-black"> {{
                                                                    __('Name') }} </label>
                                                                <div data-repeater-item=""
                                                                    class="form-group row align-items-center position-relative">
                                                                    <div class="custom-form-group mb-3 col-md-12">
                                                                        <input type="text" name="name" id="name" value=""
                                                                            class="form-control"
                                                                            placeholder="Type key point name" required>
                                                                    </div>

                                                                    <div class="col mb-0 close-btn-add-course">
                                                                        <a href="javascript:;" data-repeater-delete=""
                                                                            class="close-btn2-add-course default-hover-btn">
                                                                            <span class="iconify m-0"
                                                                                data-icon="akar-icons:cross"></span>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12 d-flex justify-content-end">
                                                                <a id="add" href="javascript:;" data-repeater-create=""
                                                                    class="theme-btn default-hover-btn theme-button1">
                                                                    <span class="iconify" data-icon="akar-icons:plus"></span> {{
                                                                    __('Add') }}
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-30">
                                                    <div class="col-md-12">
                                                        <div class="label-text-title color-heading font-medium font-16 mb-3">
                                                            {{ __('Course Description') }}
                                                            <span class="text-danger">*</span>
                                                        </div>
                                                        <textarea name="description" id="summernote" required>{{old('description')}}</textarea>
                                                        {{-- <textarea class="form-control" name="description" cols="30" rows="10"
                                                            required
                                                            placeholder="Course description">{{old('description')}}</textarea> --}}

                                                        @if ($errors->has('description'))
                                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                                            $errors->first('description') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="upload-course-item-block-title mb-3">
                                                    <h6 class="font-20">{{ __('Meta Details') }}</h6>
                                                </div>
                                                <div class="row mb-30" style="margin-top: 1.4rem !important;">
                                                    <div class="col-md-6">
                                                        <label class="font-medium font-15 color-heading">{{__('Meta Title')}}</label>
                                                        <input type="text" name="meta_title" class="form-control" placeholder="{{ __('Meta Title') }}">
                                                        @if ($errors->has('meta_title'))
                                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('meta_title') }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="font-medium font-15 color-heading">{{__('Meta Keywords')}}</label>
                                                        <input type="text" name="meta_keywords" class="form-control" placeholder="{{ __('Type meta keywords (comma separated)') }}">
                                                        @if ($errors->has('meta_keywords'))
                                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('meta_keywords') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="row mb-30" style="margin-top: 2.3rem !important;">
                                                    <div class="col-md-12">
                                                        <label class="font-medium font-15 color-heading">{{__('Meta Description')}}</label>
                                                        <textarea class="form-control" name="meta_description" id="exampleFormControlTextarea1" rows="3" placeholder="{{ __('Type Meta Description') }}"></textarea>
                                                        @if ($errors->has('meta_description'))
                                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('meta_description') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="row mb-30" style="margin-top: 2rem !important;">
                                                    <div class="col-md-12">
                                                        <div class="input__group mb-25">
                                                            <label>{{ __('OG Image') }}</label>
                                                            <div class="upload-img-box">
                                                                <img src="">
                                                                <input type="file" name="og_image" id="og_image" accept="image/*" onchange="previewFile(this)">
                                                                <div class="upload-img-box-icon">
                                                                    <i class="fa fa-camera"></i>
                                                                    <p class="m-0">{{__('OG Image')}}</p>
                                                                </div>
                                                            </div>
                                                            @if ($errors->has('og_image'))
                                                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('og_image') }}</span>
                                                            @endif
                                                            <p><span class="text-black">{{ __('Accepted Files') }}:</span> PNG, JPG <br> <span class="text-black">{{ __('Recommend Size') }}:</span> 1200 x 627</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="input__group mb-25">
                                                            <label>{{ __('Cover Image') }}</label>
                                                            <div class="upload-img-box">
                                                                <img src="">
                                                                <input type="file" name="cover_image" id="cover_image" accept="image/*" onchange="previewCourseFile(this)">
                                                                <div class="upload-img-box-icon">
                                                                    <i class="fa fa-camera"></i>
                                                                    <p class="m-0">{{__('Cover Image')}}</p>
                                                                </div>
                                                            </div>
                                                            @if ($errors->has('cover_image'))
                                                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('cover_image') }}</span>
                                                            @endif
                                                            <p><span class="text-black">{{ __('Accepted Files') }}:</span> PNG, JPG <br> <span class="text-black">{{ __('Recommend Size') }}:</span> 1900 x 400</p>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="input__group mb-25">
                                                            <label>{{ __('Home Image 1') }}</label>
                                                            <div class="upload-img-box">
                                                                <img src="">
                                                                <input type="file" name="home_image_1" id="home_image_1" accept="image/*" onchange="previewFileHome1(this)">
                                                                <div class="upload-img-box-icon">
                                                                    <i class="fa fa-camera"></i>
                                                                    <p class="m-0">{{__('Home Image 1')}}</p>
                                                                </div>
                                                            </div>
                                                            @if ($errors->has('home_image_1'))
                                                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('home_image_1') }}</span>
                                                            @endif
                                                            <p><span class="text-black">{{ __('Accepted Files') }}:</span> PNG, JPG, JPEG <br> <span class="text-black">{{ __('Recommend Size') }}:</span> 220 x 360</p>
                                                        </div>
                                                    </div>




                                                    <div class="col-md-12">
                                                        <div class="input__group mb-25">
                                                            <label>{{ __('Home Image 2') }}</label>
                                                            <div class="upload-img-box">
                                                                <img src="">
                                                                <input type="file" name="home_image_2" id="home_image_2" accept="image/*" onchange="previewFileHome2(this)">
                                                                <div class="upload-img-box-icon">
                                                                    <i class="fa fa-camera"></i>
                                                                    <p class="m-0">{{__('Home Image 2')}}</p>
                                                                </div>
                                                            </div>
                                                            @if ($errors->has('home_image_2'))
                                                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('home_image_2') }}</span>
                                                            @endif
                                                            <p><span class="text-black">{{ __('Accepted Files') }}:</span> PNG, JPG, JPEG <br> <span class="text-black">{{ __('Recommend Size') }}:</span> 260 x 420</p>
                                                        </div>
                                                    </div>



                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="stepper-action-btns d-flex justify-content-end p-0 mr-0">
                                        @if(auth()->user()->role == 2)
                                            <a href="{{route('instructor.course')}}"
                                                class="theme-btn theme-button3">{{__('Cancel')}}</a>
                                        @elseif(auth()->user()->role == 1)
                                            <a href="{{route('admin.course.index')}}"
                                            class="theme-btn theme-button3">{{__('Cancel')}}</a>
                                        @endif

                                        <button style="width: max-content !important; border-radius: 0.4rem !important;" type="submit"
                                            class="theme-btn default-hover-btn theme-button1">{{__('Save and
                                            continue')}}</button>
                                    </div>
                                </form>
                    </div>
                            <!-- Upload Course Overview-1 end -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@push('style')
    <link rel="stylesheet" href="{{asset('admin/css/custom/image-preview.css')}}">
    <link href="{{ asset('common/css/summernote/summernote.min.css') }}" rel="stylesheet">
    <link href="{{ asset('common/css/summernote/summernote-lite.min.css') }}" rel="stylesheet">
@endpush

<style>
.close-btn-add-course {
    position: absolute;
    top: 1rem !important;
    right: 0.4rem !important;
    width: 3rem !important;
}

a.close-btn2-add-course {
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
    top: 5rem !important;
    z-index: 998;
    background: white;
}
</style>



@push('script')
<script src="{{asset('frontend/assets/js/custom/upload-course.js')}}"></script>
<script src="{{ asset('common/js/jquery.repeater.min.js') }}"></script>
<script src="{{ asset('common/js/add-repeater.js') }}"></script>
<script src="{{asset('admin/js/custom/image-preview.js')}}"></script>
<script src="{{ asset('common/js/summernote/summernote-lite.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $("#summernote").summernote({dialogsInBody: true});
        $('.dropdown-toggle').dropdown();
    });
</script>
@endpush


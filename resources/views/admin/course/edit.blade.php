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
                                <h2>{{ __('Update Live Course') }}</h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('Dashboard')}}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ __('Edit Live Course') }}</li>
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
                            <h2>{{ __('Edit Live Course') }}</h2>
                        </div>
                        <form action="{{route('admin.course.update_standard', $course->uuid)}}" method="post" class="form-horizontal" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label>{{__('Course Name')}} <span class="text-danger">*</span></label>
                                        <input type="text" name="title" value="{{ $course->title }}" placeholder="{{ __('Course Name') }}" class="form-control" required>
                                        @if ($errors->has('title'))
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('title') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label>{{__('Course SubTitle')}} <span class="text-danger">*</span></label>
                                        <input type="text" name="subtitle" value="{{ $course->subtitle }}" placeholder="{{ __('Course SubTitle') }}" class="form-control" required>
                                        @if ($errors->has('subtitle'))
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('subtitle') }}</span>
                                        @endif
                                    </div>
                                </div>


                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label
                                        class="label-text-title color-heading font-medium font-14 mb-3">{{
                                        __('Course Category') }} <span class="text-danger">*</span></label>
                                    <select name="category_id" id="category_id" class="form-select"
                                        required>
                                        <option value="">{{ __('Select Category') }}</option>
                                        @foreach($categories as $category)
                                        <option value="{{$category->id}}" @if(old('category_id'))
                                            {{old('category_id')==$category->id ? 'selected' : '' }} 
                                            @else
                                            {{ $course->category_id == $category->id ? 'selected' : '' }}
                                            @endif >{{$category->name}}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('category_id'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('category_id') }}</span>
                                    @endif
                                    </div>
                                </div>


                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label
                                        class="label-text-title color-heading font-medium font-16 mb-3">{{
                                        __('Course Subcategory') }} <span class="text-danger">*</span></label>
                                    <select name="subcategory_id" id="subcategory_id" class="form-select"
                                        required>
                                        <option value="">{{ __('Select Subcategory') }}</option>
                                        @foreach($subcategories as $subcategory)
                                        <option value="{{$subcategory->id}}"{{$subcategory->id ==
                                                        $course->subcategory_id ? 'selected' : '' }}>{{$subcategory->name}}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('subcategory_id'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('subcategory_id') }}</span>
                                    @endif
                                </div>





                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label
                                        class="label-text-title color-heading font-medium font-14 mb-3">{{
                                        __('Course Instructor') }} <span class="text-danger">*</span></label>
                                    <select name="course_instructor_id" id="course_instructor_id" class="form-select"
                                        required>
                                        <option value="">{{ __('Select Instructor') }}</option>
                                        @foreach($instructors as $instructor)
                                        <option value="{{$instructor->id}}" @if(old('course_instructor_id'))
                                            {{old('course_instructor_id')==$instructor->id ? 'selected' : '' }} 
                                            @else
                                            {{ $course->instructor_id == $instructor->id ? 'selected' : '' }}
                                            @endif >{{$instructor->first_name.' '.$instructor->last_name}}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('course_instructor_id'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('course_instructor_id') }}</span>
                                    @endif
                                    </div>
                                </div>



                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Academic Coordinators')
                                            }}</label>
                                        <select class="form-control" name="coordinator_id[]" multiple="multiple" id="coordinator_selectpicker">
                                            <option disabled>Select Coordinator</option>
                                                @if(!$coordinators->isEmpty())
                                                     @foreach ($coordinators as $coordinator)
                                                        <option value="{{ $coordinator->id }}" <?php if( in_array($coordinator->id, $check_coordinators)){ echo 'selected="selected"'; } ?>>
                                                            <?php 

                                                        echo "<strong>".$coordinator->user?->name."</strong>";
                                                        echo ' ('.$coordinator->id.')';
                                                        ?>
                                                        </option>
                                                     @endforeach
                                                @endif
                                        </select>
                                        @if ($errors->has('coordinator_id'))
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                            $errors->first('coordinator_id') }}</span>
                                        @endif
                                    </div>
                                </div>



                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label>{{__('Classes')}} <span class="text-danger"></span></label>
                                        <input type="number" name="classes" value="{{ $course->classes }}" placeholder="{{ __('Classes') }}" class="form-control">
                                        @if ($errors->has('classes'))
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('classes') }}</span>
                                        @endif
                                    </div>
                                </div>




                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label>{{__('Duration')}} <span class="text-danger"></span></label>
                                        <input type="number" name="duration" value="{{ $course->duration }}" placeholder="{{ __('Duration') }}" class="form-control">
                                        @if ($errors->has('duration'))
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('duration') }}</span>
                                        @endif
                                    </div>
                                </div>



                                
                            <div class="row align-items-center">
                                <div class="col-md-12 mb-30">
                                 
                                        <label>{{__('Duration Type')}} <span class="text-danger"></span></label>
                                        <br>
                                        <input type="radio"
                                        checked
                                        {{ $course->duration_type == 1 ? 'checked' : '' }}
                                        id="1"
                                        name="duration_type"
                                        value="1">
                                        <label for="1">{{ __('Hours') }}</label>



                                        <input style="margin-left: 5px" type="radio"
                                      
                                        {{ $course->duration_type == 2 ? 'checked' : '' }}
                                        id="2"
                                        name="duration_type"
                                        value="2">
                                        <label for="1">{{ __('Days') }}</label>

                                      

                                        <input style="margin-left: 5px" type="radio"
                                     
                                        {{ $course->duration_type == 3 ? 'checked' : '' }}
                                        id="3"
                                        name="duration_type"
                                        value="3">
                                        <label for="1">{{ __('Months') }}</label>

                                        @if ($errors->has('duration'))
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('duration') }}</span>
                                        @endif
                                 
                                </div>
                            </div>





                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label>{{ __('Short Description') }} </label>
                                        <textarea name="short_description" id="short_description" cols="30" rows="8">{{$course->short_description}}</textarea>
                                    
                                        @if ($errors->has('short_description'))
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('short_description') }}</span>
                                        @endif
                                    </div>
                                </div>




                                <div class="col-md-12">
                                    <div class="upload-course-item-block-title mb-3">
                                                    <h6 class="font-20">{{ __('Meta Details') }}</h6>
                                    </div>
                                    <div class="row"
                                                        style="margin-top: 1.4rem !important;">
                                                    <div class="col-md-6">
                                                                    <label
                                                                                                class="font-medium font-15 color-heading">{{ __('Meta Title') }}</label>
                                                                    <input type="text"
                                                                                                name="meta_title"
                                                                                                value="{{ $course->meta_title }}"
                                                                                                class="form-control"
                                                                                                placeholder="{{ __('Meta Title') }}">
                                                                    @if ($errors->has('meta_title'))
                                                                                    <span class="text-danger"><i
                                                                                                                class="fas fa-exclamation-triangle"></i>
                                                                                                    {{ $errors->first('meta_title') }}</span>
                                                                    @endif
                                                    </div>
                                                    <div class="col-md-6">
                                                                    <label
                                                                                                class="font-medium font-15 color-heading">{{ __('Meta Keywords') }}</label>
                                                                    <input type="text"
                                                                                                name="meta_keywords"
                                                                                                value="{{ $course->meta_keywords }}"
                                                                                                class="form-control"
                                                                                                placeholder="{{ __('Type meta keywords (comma separated)') }}">
                                                                    @if ($errors->has('meta_keywords'))
                                                                                    <span class="text-danger"><i
                                                                                                                class="fas fa-exclamation-triangle"></i>
                                                                                                    {{ $errors->first('meta_keywords') }}</span>
                                                                    @endif
                                                    </div>
                                    </div>
                                    <div class="row"
                                                        style="margin-top: 2.3rem !important;">
                                                    <div class="col-md-12">
                                                                    <label
                                                                                                class="font-medium font-15 color-heading">{{ __('Meta Description') }}</label>
                                                                    <textarea class="form-control"
                                                                              name="meta_description"
                                                                              id="exampleFormControlTextarea1"
                                                                              rows="3"
                                                                              placeholder="{{ __('Type Meta Description') }}">{{ $course->meta_description }}</textarea>
                                                                    @if ($errors->has('meta_description'))
                                                                                    <span class="text-danger"><i
                                                                                                                class="fas fa-exclamation-triangle"></i>
                                                                                                    {{ $errors->first('meta_description') }}</span>
                                                                    @endif
                                                    </div>
                                    </div>
                                    <div class="row"
                                                        style="margin-top: 2.3rem !important;">
                                                    <div class="col-md-12">
                                                                    <div class="input__group mb-25">
                                                                                    <label>{{ __('OG Image') }}</label>
                                                                                    <div class="upload-img-box">
                                                                                                    @if ($course->og_image != null && $course->og_image != '')
                                                                                                                    <img src="{{ getImageFile($course->og_image) }}">
                                                                                                    @else
                                                                                                                    <img src="">
                                                                                                    @endif
                                                                                                    <input type="file"
                                                                                                                                name="og_image"
                                                                                                                                id="og_image"
                                                                                                                                accept="image/*"
                                                                                                                                onchange="previewFile(this)">
                                                                                                    <div class="upload-img-box-icon">
                                                                                                                    <i class="fa fa-camera"></i>
                                                                                                                    <p class="m-0">{{ __('OG Image') }}</p>
                                                                                                    </div>
                                                                                    </div>
                                                                                    @if ($errors->has('og_image'))
                                                                                                    <span class="text-danger"><i
                                                                                                                                class="fas fa-exclamation-triangle"></i>
                                                                                                                    {{ $errors->first('og_image') }}</span>
                                                                                    @endif
                                                                                    <p><span class="text-black">{{ __('Accepted Files') }}:</span>
                                                                                                    PNG, JPG <br> <span
                                                                                                                            class="text-black">{{ __('Recommend Size') }}:</span>
                                                                                                    1200 x 627</p>
                                                                    </div>
                                                    </div>
                                    </div>
                                 
                    </div>



                    <div class="row align-items-center">
                        <div class="col-12">
                            <label
                                class="label-text-title color-heading font-medium font-16 mb-3">{{
                                __('Home Image 1') }}
                            </label>
                        </div>
                        <div class="col-md-6 mb-30">
                            <div class="upload-img-box mt-3 height-200">
                                @if($course->home_image_1)
                                <img src="{{getImageFile($course->home_image_1)}}">
                                @else
                                <img src="">
                                @endif
                           
                                <input type="file" name="home_image_1" id="home_image_1" accept="image/*"
                                    onchange="previewFileHome1(this)">
                                <div class="upload-img-box-icon">
                                    <i class="fa fa-camera"></i>
                                    <p class="m-0">{{__('Image')}}</p>
                                </div>
                            </div>
                            @if ($errors->has('home_image_1'))
                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                $errors->first('home_image_1') }}</span>
                            @endif
                        </div>
                        <div class="col-md-6 mb-30">
                            <p class="font-14 color-gray">{{ __('Recomended image format & size') }}:
                                220px X 360px (5MB)</p>
                            <p class="font-14 color-gray">{{ __('Accepted image filetype') }}: jpg,
                                jpeg, png</p>
                        </div>
                    </div>




                    <div class="row align-items-center">
                        <div class="col-12">
                            <label
                                class="label-text-title color-heading font-medium font-16 mb-3">{{
                                __('Home Image 2') }}
                            </label>
                        </div>
                        <div class="col-md-6 mb-30">
                            <div class="upload-img-box mt-3 height-200">
                                @if($course->home_image_2)
                                <img src="{{getImageFile($course->home_image_2)}}">
                                @else
                                <img src="">
                                @endif
                           
                                <input type="file" name="home_image_2" id="home_image_2" accept="image/*"
                                    onchange="previewFileHome2(this)">
                                <div class="upload-img-box-icon">
                                    <i class="fa fa-camera"></i>
                                    <p class="m-0">{{__('Image')}}</p>
                                </div>
                            </div>
                            @if ($errors->has('home_image_2'))
                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                $errors->first('home_image_2') }}</span>
                            @endif
                        </div>
                        <div class="col-md-6 mb-30">
                            <p class="font-14 color-gray">{{ __('Recomended image format & size') }}:
                                260px X 420px (5MB)</p>
                            <p class="font-14 color-gray">{{ __('Accepted image filetype') }}: jpg,
                                jpeg, png</p>
                        </div>
                    </div>


                    <div class="row align-items-center">
                        <div class="col-12">
                            <label
                                class="label-text-title color-heading font-medium font-16 mb-3">{{
                                __('Course Image') }}
                            </label>
                        </div>
                        <div class="col-md-6 mb-30">
                            <div class="upload-img-box mt-3 height-200">
                                @if($course->image)
                                <img src="{{getImageFile($course->image)}}">
                                @else
                                <img src="">
                                @endif
                                {{-- <input type="file" name="image" id="image" accept="image/*"
                                    onchange="previewFile(this)" @if(!$course->image) required
                                    @endif> --}}
                              
                                    <input type="file"
											name="image"
											id="image"
											accept="image/*"
											onchange="previewFile(this)"
											@if (!$course->image) required @endif>
                                <div class="upload-img-box-icon">
                                    <i class="fa fa-camera"></i>
                                    <p class="m-0">{{__('Image')}}</p>
                                </div>
                            </div>
                            @if ($errors->has('image'))
                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                $errors->first('image') }}</span>
                            @endif
                        </div>
                        <div class="col-md-6 mb-30">
                            <p class="font-14 color-gray">{{ __('Recomended image format & size') }}:
                                575px X 450px (1MB)</p>
                            <p class="font-14 color-gray">{{ __('Accepted image filetype') }}: jpg,
                                jpeg, png</p>
                        </div>
                    </div>


                             






                                <div class="row align-items-center">
                                    <div class="col-12">
                                        <label
                                            class="label-text-title color-heading font-medium font-16 mb-3">{{
                                            __('Course Cover Image') }}
                                        </label>
                                    </div>
                                    <div class="col-md-6 mb-30">
                                        <div class="upload-img-box mt-3 height-200">
                                            @if($course->cover_image)
                                            <img src="{{getImageFile($course->cover_image)}}">
                                            @else
                                            <img src="">
                                            @endif
                                            {{-- <input type="file" name="cover_image" id="cover_image" accept="image/*"
                                                onchange="previewCourseFile(this)" @if(!$course->cover_image) required
                                                @endif> --}}
                                            <input type="file" name="cover_image" id="cover_image" accept="image/*"
                                                onchange="previewCourseFile(this)">
                                            <div class="upload-img-box-icon">
                                                <i class="fa fa-camera"></i>
                                                <p class="m-0">{{__('Cover Image')}}</p>
                                            </div>
                                        </div>
                                        @if ($errors->has('cover_image'))
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                            $errors->first('cover_image') }}</span>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mb-30">
                                        <p class="font-14 color-gray">{{ __('Recomended image format & size') }}:
                                            1900px X 400px (5MB)</p>
                                        <p class="font-14 color-gray">{{ __('Accepted image filetype') }}: jpg,
                                            jpeg, png</p>
                                    </div>
                                </div>



                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label>{{__('Course Price')}} <span class="text-danger">*</span></label>
                                        <input type="number" name="price" value="{{ (int)$course->price }}" placeholder="{{ __('Price') }}" class="form-control" required>
                                        @if ($errors->has('price'))
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('price') }}</span>
                                        @endif
                                    </div>
                                </div>

                               
                            
                                <div class="col-md-6">
                                    <div class="input__group mb-25">
                                        <label>{{__('Course Language')}} <span class="text-danger">*</span></label>
                                        <select name="course_lang_id" id="course_lang_id" class="form-select" required>
                                            <option value="">{{__('Select Language')}}</option>
                                            @if (!$course_languages->isEmpty())
                                            @foreach($course_languages as $course_language)
                                            <option value="{{$course_language->id}}"
                                                @if($course->course_language_id)
                                                {{$course->course_language_id==$course_language->id ? 'selected' :
                                                '' }}
                                                @endif
                                                >{{$course_language->name}}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                             
                           
                                <div class="col-md-6">
                                    <div class="input__group mb-25">
                                        <label>{{__('Visibilty')}}<span class="text-danger">*</span></label>
                                        <select name="learner_accessibility" id="gender" class="form-select" required>
                                            <option value="">{{__('Select Option')}}</option>
                                            <option  value="paid" {{ $course->learner_accessibility == 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                                            <option value="free" {{ $course->learner_accessibility == 'free' ? 'selected' : '' }} >{{
                                            __('Free') }}</option>
                                          
                                          
                                        </select>
                                    </div>
                                </div>
                           
                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label>{{ __('Description') }} <span class="text-danger">*</span></label>
                                        <textarea name="description" id="summernote" required>{{$course->description}}</textarea>
                                        @if ($errors->has('description'))
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('description') }}</span>
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
    <link href="{{ asset('common/css/summernote/summernote.min.css') }}" rel="stylesheet">
    <link href="{{ asset('common/css/summernote/summernote-lite.min.css') }}" rel="stylesheet">
@endpush

@push('script')
    <script src="{{asset('admin/js/custom/image-preview.js')}}"></script>
    <script src="{{asset('admin/js/custom/admin-profile.js')}}"></script>
    <script src="{{ asset('common/js/summernote/summernote-lite.min.js') }}"></script>

    <script>
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

<script>
    $(document).ready(function() {
        $("#summernote").summernote({dialogsInBody: true});
        $('.dropdown-toggle').dropdown();
    });


</script>



    <script>
            
            $(document).ready(function () {
                $('#coordinator_selectpicker').select2({
                    width: '100%',
                    // allowClear: true,
                    placeholder: 'Select Coordinator',
                    
                }); 
            });



    </script>

@endpush

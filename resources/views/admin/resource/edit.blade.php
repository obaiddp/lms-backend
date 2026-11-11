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
                                <h2>{{ __('Edit Resource') }}</h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('Dashboard')}}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ __('Edit Resource') }}</li>
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
                            <h2>{{ __('Edit Resource') }}</h2>
                        </div>
                        <form action="{{route('admin.resource.update',['id'=>$resource->id])}}" method="post" class="form-horizontal" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="existing_path" value="{{$existingPath}}">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label>{{__('Resource Title')}} <span class="text-danger">*</span></label>
                                        <input type="text" name="resource_title" value="{{$resource->name}}" placeholder="{{ __('Resource Title') }}" class="form-control" required>
                                        @if ($errors->has('resource_title'))
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('resource_title') }}</span>
                                        @endif
                                    </div>
                                </div>

                             
                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                      
                                        <label
                                        class="label-text-title color-heading font-medium font-14 mb-3">{{
                                        __('Courses') }} <span class="text-danger">*</span></label>
                                    <select  multiple="multiple" name="course[]" id="course" class="form-select"
                                        required>
                                        <option value="">{{ __('Select Courses') }}</option>
                                        @foreach($courses as $course)
                                      
                                        <option value="{{$course->id}}"  @php
                                            if( in_array($course->id, $courseIDs)){
                                               echo 'selected="selected"';
                                              }
                                           
                                          @endphp>{{$course->title}}</option>
                                        @endforeach
                                    </select>

                                    
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label>{{__('Resource Category')}} <span class="text-danger">*</span></label>

                                         <select  id="resource_category_id" name="resource_category_id" class="form-control" required>
                                                    <option value="">{{__('Select Category')}}</option>
                                                    <?php foreach ($categories as $category) {
                                                        ?>
                                                        <option value="{{$category->id}}"
                                                        @php
                                                      
                                                        if ($category->id == $resource->category_id){
                                                            echo "selected";
                                                        }
                                                         
                                                        @endphp
                                                        >{{$category->name}}</option>
                                                    <?php } ?>
                                                </select>
                                        @if ($errors->has('resource_category_id'))
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('resource_category_id') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label>{{__('Allowed Types')}}:</label>
                                        <label  name="allowed" id="allowed" class="form-control">
                                    
                                    </div>
                                </div>
                               

                                {{-- <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label>{{__('Upload Resource')}} <span class="text-danger">*</span></label>
                                        <input type="file" name="resource_file" class="form-control" required>
                                        @if ($errors->has('resource_file'))
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('resource_file') }}</span>
                                        @endif
                                    </div>
                                </div> --}}


                            <div id="video">
                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                            <label>{{__('Upload Resource')}} <span class="text-danger">*</span></label>
                                            <input type="file" name="video_file" class="form-control" id="video_file">
                                            @if ($errors->has('video_file'))
                                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('video_file') }}</span>
                                            @endif
                                      
                                    </div>
                                </div>
                            </div>


                            <div id="youtube" class="d-none">
                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                    <label>{{ __('Lesson Youtube Video ID') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="youtube_url_path" class="form-control youtube-url" value="@if($resource->youtube_url_path){{$resource->youtube_url_path}}@endif" id="youtube_url_path" placeholder="{{ __('Type Your Youtube Video ID') }}">
                                    @if ($errors->has('youtube_url_path'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('youtube_url_path') }}</span>
                                    @endif
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                    <label>{{ __('Youtube Video Duration') }} (00:00) <span class="text-danger">*</span></label>
                                    <input type="text" name="youtube_file_duration" value="@if($resource->youtube_video_duration){{$resource->youtube_video_duration}}@endif" class="form-control customFileDuration" placeholder="{{ __('Youtube video duration') }}">
                                    @if ($errors->has('youtube_file_duration'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('youtube_file_duration') }}</span>
                                    @endif
                                    </div>
                                </div>
                            </div>

                            <div id="text" class="d-none">
                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                            <label>{{__('Description')}} <span class="text-danger">*</span></label>
                                            <textarea name="text_description" id="summernote"
                                                    class="textDescription" cols="30" rows="10" required>@if($resource->text){{$resource->text}}@endif</textarea>
                                            @if ($errors->has('text_description'))
                                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('text_description') }}</span>
                                            @endif
                                      
                                    </div>
                                </div>
                            </div>

                            <div id="imageDiv" class="d-none">
                                <div class="row align-items-center">
                                    <div class="col-12">
                                        <label>{{__('Upload Image')}} <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-4 mb-30">
                                        <div class="upload-img-box mt-3 height-200">
                                            <img src="">
                                            <input type="file" name="image" id="image"
                                                onchange="previewFile(this)">
                                            <div class="upload-img-box-icon">
                                                <i class="fa fa-camera"></i>
                                                <p class="m-0">{{__('Image')}}</p>
                                            </div>
                                        </div>
                                        @if ($errors->has('image'))
                                        <span class="text-danger"><i
                                                class="fas fa-exclamation-triangle"></i> {{
                                            $errors->first('image') }}</span>
                                        @endif
                                    </div>
                                    <div class="col-md-8 mb-30">
                                        <p class="font-14 color-gray">{{ __('Preferable image size:') }} (10MB)</p>
                                        <p class="font-14 color-gray">{{ __('Preferable filetype:') }} jpg, jpeg, png</p>
                                    </div>
                                </div>
                            </div>


                            <div id="pdfDiv" class="d-none">
                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                            <label>{{__('Upload PDF')}} <span class="text-danger">*</span></label>
                                            <input type="file" name="pdf" 
                                                        class="form-control" value="{{ old('pdf') }}" id="pdf"
                                                        title="Upload lesson pdf">
                                            
                                            @if ($errors->has('pdf'))
                                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{$errors->first('pdf') }}</span>
                                            @endif
                                    </div>
                                </div>
                            </div>

                            <div id="slide_documentDiv" class="d-none">
                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label>{{__('Upload Slide Document')}} <span class="text-danger">*</span></label>
                                        <input type="file" name="slide_document" 
                                        class="form-control" value="{{ old('slide_document') }}" id="slide_document"
                                        title="Upload lesson slide_document">

                                        @if ($errors->has('slide_document'))
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{$errors->first('slide_document') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div id="audioDiv" class="d-none">
                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label>{{__('Upload Audio')}} <span class="text-danger">*</span></label>
                                        <input type="file" name="audio" 
                                        class="form-control" value="{{ old('audio') }}" id="audio"
                                        title="Upload lesson audio">

                                        @if ($errors->has('audio'))
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{$errors->first('audio') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label>{{__('Sorting Number')}}</label>
                                        <input type="number" name="sorting_number" value="{{$resource->sort_number}}" placeholder="{{ __('Sorting Number') }}" class="form-control">
                                        @if ($errors->has('sorting_number'))
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('sorting_number') }}</span>
                                        @endif
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
@endpush

@push('script')
    <script src="{{asset('admin/js/custom/image-preview.js')}}"></script>
    <script src="{{asset('admin/js/custom/admin-profile.js')}}"></script>
    <script>
          $(document).ready(function () {
            $('#course').select2({
    // dropdownParent: $('#edit_drop_modal'),
    width: '100%',
    allowClear: true,
    placeholder: 'Select Courses',
 
   
});

        });
        $('#resource_category_id').on('change', function (e) {
            e.preventDefault();
            var category_id = $(this).val();
            $.ajax({
            type: "POST",
            headers:
                        {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
            url: '{{route('admin.resource.get_category_types')}}',
            data: {category_id:category_id},
            dataType: "JSON",
            success: function (response) {
             
                $('#allowed').html(response.category_types);  
                resourceType(response.category_name)    
            }
        });
        });

        function resourceType(type)
    {
        if (type === 'Video') {
            $('#video').removeClass('d-none');
            $('#youtube').addClass('d-none');
            $('#text').addClass('d-none');
            $('#imageDiv').addClass('d-none');
            $('#pdfDiv').addClass('d-none');
            $('#slide_documentDiv').addClass('d-none');
            $('#audioDiv').addClass('d-none');


            $('#youtube_url_path').removeAttr("required");
            $('#video_file').attr("required", true);
            $('.textDescription').removeAttr("required");
            $('#image').removeAttr("required");
            $('#pdf').removeAttr("required");
            $('#slide_document').removeAttr("required");
            $('#audio').removeAttr("required");

        } else if (type === 'Youtube') {
            $('#video').addClass('d-none');
            $('#youtube').removeClass('d-none');
            $('#text').addClass('d-none');
            $('#imageDiv').addClass('d-none');
            $('#pdfDiv').addClass('d-none');
            $('#slide_documentDiv').addClass('d-none');
            $('#audioDiv').addClass('d-none');

            $('.customFileDuration').attr("required", true);
            $('#youtube_url_path').attr("required", true);

            $('#video_file').removeAttr("required");
            $('.textDescription').removeAttr("required");
            $('#image').removeAttr("required");
            $('#pdf').removeAttr("required");
            $('#slide_document').removeAttr("required");
            $('#audio').removeAttr("required");
        } 
        else if (type === 'Text') {
            $('#video').addClass('d-none');
            $('#youtube').addClass('d-none');
            $('#text').removeClass('d-none');
            $('#imageDiv').addClass('d-none');
            $('#pdfDiv').addClass('d-none');
            $('#slide_documentDiv').addClass('d-none');
            $('#audioDiv').addClass('d-none');
         
            $('.textDescription').attr("required", true);
            $('#youtube_url_path').removeAttr("required");
            $('.customFileDuration').removeAttr("required");
            $('#video_file').removeAttr("required");

            $('#image').removeAttr("required");
            $('#pdf').removeAttr("required");
            $('#slide_document').removeAttr("required");
            $('#audio').removeAttr("required");
        } 
        else if (type === 'Image') {
            $('#video').addClass('d-none');
            $('#youtube').addClass('d-none');
            $('#text').addClass('d-none');
            $('#imageDiv').removeClass('d-none');
            $('#pdfDiv').addClass('d-none');
            $('#slide_documentDiv').addClass('d-none');
            $('#audioDiv').addClass('d-none');

            $('#youtube_url_path').removeAttr("required");
            $('.customFileDuration').removeAttr("required");
            $('#video_file').removeAttr("required");
            $('.textDescription').removeAttr("required");
            $('#image').attr("required", true);
            $('#pdf').removeAttr("required");
            $('#slide_document').removeAttr("required");
            $('#audio').removeAttr("required");
        } 
        else if (type === 'PDF') {
            $('#video').addClass('d-none');
            $('#youtube').addClass('d-none');
            $('#text').addClass('d-none');
            $('#imageDiv').addClass('d-none');
            $('#pdfDiv').removeClass('d-none');
            $('#slide_documentDiv').addClass('d-none');
            $('#audioDiv').addClass('d-none');

            $('#youtube_url_path').removeAttr("required");
            $('.customFileDuration').removeAttr("required");
            $('#video_file').removeAttr("required");
            $('.textDescription').removeAttr("required");
            $('#image').removeAttr("required");
            $('#pdf').attr("required", true);
            $('#slide_document').removeAttr("required");
            $('#audio').removeAttr("required");
        } 
        else if (type === 'Slide Document') {
            $('#video').addClass('d-none');
            $('#youtube').addClass('d-none');
            $('#text').addClass('d-none');
            $('#imageDiv').addClass('d-none');
            $('#pdfDiv').addClass('d-none');
            $('#slide_documentDiv').removeClass('d-none');
            $('#audioDiv').addClass('d-none');

            $('#youtube_url_path').removeAttr("required");
            $('.customFileDuration').removeAttr("required");
            $('#video_file').removeAttr("required");
            $('.textDescription').removeAttr("required");
            $('#image').removeAttr("required");
            $('#pdf').removeAttr("required");
            $('#slide_document').attr("required", true);
            $('#audio').removeAttr("required");
        } 
        else if (type === 'Audio') {
            $('#video').addClass('d-none');
            $('#youtube').addClass('d-none');
            $('#text').addClass('d-none');
            $('#imageDiv').addClass('d-none');
            $('#pdfDiv').addClass('d-none');
            $('#slide_documentDiv').addClass('d-none');
            $('#audioDiv').removeClass('d-none');

            $('#youtube_url_path').removeAttr("required");
            $('.customFileDuration').removeAttr("required");
            $('#video_file').removeAttr("required");
            $('.textDescription').removeAttr("required");
            $('#image').removeAttr("required");
            $('#pdf').removeAttr("required");
            $('#slide_document').removeAttr("required");
            $('#audio').attr("required", true);
        }
    }
        $(document).ready(function () {
        
            $('#resource_category_id').trigger('change');
        });
    </script>
@endpush

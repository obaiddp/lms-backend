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
                                <h2>{{ __('Edit Resource Category') }}</h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('Dashboard')}}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ __('Edit Resource Category') }}</li>
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
                            <h2>{{ __('Edit Resource Category') }}</h2>
                        </div>
                        <form action="{{route('admin.resource.category.update',['id'=>$category->id])}}" method="post" class="form-horizontal" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label>{{__('Category Name')}} <span class="text-danger">*</span></label>
                                        <input type="text" name="category_name" value="{{$category->name}}" placeholder="{{ __('Category Name') }}" class="form-control" required>
                                        @if ($errors->has('category_name'))
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('category_name') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                               
                                        <label class="col-sm-2">{{__('Category Types')}} </label>
                                        <div class="col-sm-6">
                                        <input type="checkbox" name="categorytype[]"  value="mp3" @php if(in_array("mp3", $category_types)){ echo 'checked="checked"'; } @endphp> MP3

                                        <input type="checkbox" name="categorytype[]"  value="wav" @php if(in_array("wav", $category_types)){ echo 'checked="checked"'; } @endphp> WAV
            
                                        <input type="checkbox" name="categorytype[]"  value="m4a" @php if(in_array("m4a", $category_types)){ echo 'checked="checked"'; } @endphp> M4A <br>
            
                                        <input type="checkbox" name="categorytype[]"  value="mp4" @php if(in_array("mp4", $category_types)){ echo 'checked="checked"'; } @endphp> MP4
            
                                        <input type="checkbox" name="categorytype[]"  value="webm" @php if(in_array("webm", $category_types)){ echo 'checked="checked"'; } @endphp> WEBM
            
                                        <input type="checkbox" name="categorytype[]"  value="mkv" @php if(in_array("mkv", $category_types)){ echo 'checked="checked"'; } @endphp> MKV <br>
                                        <input type="checkbox" name="categorytype[]"  value="jpg" @php if(in_array("jpg", $category_types)){ echo 'checked="checked"'; } @endphp> JPG
            
            
                                        <input type="checkbox" name="categorytype[]"  value="gif" @php if(in_array("gif", $category_types)){ echo 'checked="checked"'; } @endphp> GIF
            
                                        <input type="checkbox" name="categorytype[]"  value="png" @php if(in_array("png", $category_types)){ echo 'checked="checked"'; } @endphp> PNG
            
                                        <input type="checkbox" name="categorytype[]"  value="webp" @php if(in_array("webp", $category_types)){ echo 'checked="checked"'; } @endphp> WEBP <br>
            
                                        <input type="checkbox" name="categorytype[]"  value="pdf" @php if(in_array("pdf", $category_types)){ echo 'checked="checked"'; } @endphp> PDF
            
                                        <input type="checkbox" name="categorytype[]"  value="docx" @php if(in_array("docx", $category_types)){ echo 'checked="checked"'; } @endphp> DOCX
                                        <input type="checkbox" name="categorytype[]"  value="pptx" @php if(in_array("pptx", $category_types)){ echo 'checked="checked"'; } @endphp> PPTX
                                        <input type="checkbox" name="categorytype[]"  value="zip" @php if(in_array("zip", $category_types)){ echo 'checked="checked"'; } @endphp> ZIP
                                        <input type="checkbox" name="categorytype[]"  value="xlsx" @php if(in_array("xlsx", $category_types)){ echo 'checked="checked"'; } @endphp> XLSX
                                        @if ($errors->has('category_type'))
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('category_type') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <br>
                                        <label>{{__('Category Description')}} </label>
                                        <textarea name="category_desc" id="" cols="30" rows="10">{{$category->description}}</textarea>
                                        {{-- <input type="text" name="category_desc" value="{{old('category_desc')}}" placeholder="{{ __('Category Description') }}" class="form-control"> --}}
                                        @if ($errors->has('category_desc'))
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('category_desc') }}</span>
                                        @endif
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

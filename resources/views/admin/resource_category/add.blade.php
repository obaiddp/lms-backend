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
                                <h2>{{ __('Add Resource Category') }}</h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('Dashboard')}}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ __('Add Resource Category') }}</li>
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
                            <h2>{{ __('Add Resource Category') }}</h2>
                        </div>
                        <form action="{{route('admin.resource.category.store')}}" method="post" class="form-horizontal" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label>{{__('Category Name')}} <span class="text-danger">*</span></label>
                                        <input type="text" name="category_name" value="{{old('category_name')}}" placeholder="{{ __('Category Name') }}" class="form-control" required>
                                        @if ($errors->has('category_name'))
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('category_name') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                               
                                        <label class="col-sm-2">{{__('Category Types')}} </label>
                                        <div class="col-sm-6">
                                        <input type="checkbox" name="categorytype[]"  value="mp3"> MP3

                                        <input type="checkbox" name="categorytype[]"  value="wav"> WAV
            
                                        <input type="checkbox" name="categorytype[]"  value="m4a"> M4A <br>
            
                                        <input type="checkbox" name="categorytype[]"  value="mp4"> MP4
            
                                        <input type="checkbox" name="categorytype[]"  value="webm"> WEBM
            
                                        <input type="checkbox" name="categorytype[]"  value="mkv"> MKV <br>
                                        <input type="checkbox" name="categorytype[]"  value="jpg"> JPG
            
            
                                        <input type="checkbox" name="categorytype[]"  value="gif"> GIF
            
                                        <input type="checkbox" name="categorytype[]"  value="png"> PNG
            
                                        <input type="checkbox" name="categorytype[]"  value="webp"> WEBP <br>
            
                                        <input type="checkbox" name="categorytype[]"  value="pdf"> PDF
            
                                        <input type="checkbox" name="categorytype[]"  value="docx"> DOCX
                                        <input type="checkbox" name="categorytype[]"  value="pptx"> PPTX
                                        <input type="checkbox" name="categorytype[]"  value="zip"> ZIP
                                        <input type="checkbox" name="categorytype[]"  value="xlsx"> XLSX
                                        @if ($errors->has('category_type'))
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('category_type') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <br>
                                        <label>{{__('Category Description')}} </label>
                                        <textarea name="category_desc" id="" cols="30" rows="10"></textarea>
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
  
    </script>
@endpush

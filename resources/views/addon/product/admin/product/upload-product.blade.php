@extends('layouts.instructor')

@section('breadcrumb')
<div class="page-banner-content text-center">
    <h3 class="page-banner-heading text-white pb-15"> {{__('Upload Product')}} </h3>

    <!-- Breadcrumb Start-->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb justify-content-center">
            <li class="breadcrumb-item font-14"><a href="{{route('admin.dashboard')}}">{{__('Dashboard')}}</a></li>
            <li class="breadcrumb-item font-14 active" aria-current="page">{{__('Upload Product')}}</li>
        </ol>
    </nav>
</div>
@endsection

@section('content2')
<div class="instructor-profile-right-part instructor-upload-course-box-part">
    <div class="instructor-upload-course-box">
        <div class="row">
            {{-- {{route('lms_product.instructor.product.upload.store')}} --}}
            <div class="col-12">
                <div class="card">
                    <form method="POST" action="{{route('lms_product.admin.product.store')}}" enctype="multipart/form-data"  data-handler="getProductResponse" class="ajax row g-3 needs-validation" novalidate>
                        @csrf

                        <div class="upload-course-item-block course-overview-step1 radius-8">
                            <div class="upload-course-item-block-title mb-3">
                                @if($type == PHYSICAL_PRODUCT)
                                <input type="hidden" name="type" value="{{ PHYSICAL_PRODUCT }}">
                                <h6 class="font-20">{{ __('Add Physical Product') }}</h6>
                                @else
                                <input type="hidden" name="type" value="{{ DIGITAL_PRODUCT }}">
                                <h6 class="font-20">{{ __('Add Digital Product') }}</h6>
                                @endif
                            </div>

                            <div class="row mb-30">
                                <div class="col-md-12">
                                    <label class="font-medium font-15 color-heading">{{__('Title')}}</label>
                                    <span class="text-danger">*</span>
                                    <input type="text" name="title" class="form-control slugable"  onkeyup="slugable()" placeholder="{{ __('Title') }}">
                                    @if ($errors->has('title'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('title') }}</span>
                                    @endif

                                </div>
                            </div>
                            
                            <div class="row mb-30">
                                <div class="col-md-12">
                                    <label class="font-medium font-15 color-heading">{{__('Slug')}}</label>
                                    <span class="text-danger">*</span>
                                    <input type="text" name="slug" class="form-control slug" onkeyup="getMyself()" placeholder="{{ __('Slug') }}">
                                    @if ($errors->has('slug'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('slug') }}</span>
                                    @endif

                                </div>
                            </div>

                            <div class="row mb-30">
                                <div class="col-md-12">
                                    <div class="label-text-title color-heading font-medium font-16 mb-3">{{
                                        __('Category') }}
                                        <span class="text-danger">*</span>
                                    </div>

                                    <select name="product_category_id" id="product_category_id" class="form-select" required>
                                        <option value="">{{ __('Select Category') }}</option>
                                      
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('product_category_id'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('product_category_id') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-30">
                                <div class="col-md-12">
                                    <label
                                        class="label-text-title color-heading font-medium font-16 mb-3">{{
                                        __('Tags') }}
                                    </label>
                                    <select name="tag_ids[]" class="select2" multiple>
                                        @foreach($tags as $tag)
                                        <option value="{{$tag->id}}">{{$tag->name}}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('tag'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('tag') }}</span>
                                    @endif

                                </div>
                            </div>

                            <div class="row mb-30">
                                <div class="col-md-12">
                                    <div class="label-text-title color-heading font-medium font-16 mb-3">
                                        {{ __('Price') }}
                                        <span class="text-danger">*</span>
                                    </div>
                                    <input type="number" step=".0001" min=0 id="old_price" name="old_price" class="form-control" placeholder="0.00">
                                    @if ($errors->has('old_price'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('old_price') }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="row mb-30">
                                <div class="col-md-12">
                                    <div class="label-text-title color-heading font-medium font-16 mb-3">
                                        {{ __('Discount Percentage') }}
                                        <span class="text-danger">*</span>
                                    </div>
                                    <input type="number" step=".0001" min=0 id="discount_percentage" name="discount_percentage" value="0" class="form-control" placeholder="0.00">
                                    @if ($errors->has('discount_percentage'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('discount_percentage') }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="row mb-30">
                                <div class="col-md-12">
                                    <div class="label-text-title color-heading font-medium font-16 mb-3">
                                        {{ __('Current Price') }}
                                        <span class="text-danger">*</span>
                                    </div>
                                    <input type="number" step=".0001" id="current_price" readonly min=0 max=100 name="current_price" class="form-control" placeholder="0.00">
                                    @if ($errors->has('current_price'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('current_price') }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="row mb-30">
                                <div class="col-md-12">
                                    <div class="label-text-title color-heading font-medium font-16 mb-3">
                                        {{ __('Quantity') }}
                                        <span class="text-danger">*</span>
                                    </div>
                                    <input type="number" step="10000" min=0 name="quantity" class="form-control" placeholder="0">
                                    @if ($errors->has('quantity'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('quantity') }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            @if($type == PHYSICAL_PRODUCT)
                            <div class="row mb-30">
                                <div class="col-md-12">
                                    <div class="label-text-title color-heading font-medium font-16 mb-3">
                                        {{ __('Shipping Charge') }}
                                    </div>
                                    <input type="number" step=".0001"  min=0 name="shipping_charge" class="form-control" placeholder="0.00">
                                    @if ($errors->has('shipping_charge'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('shipping_charge') }}</span>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <div class="row mb-30">
                                <div class="col-md-12">
                                    <div class="label-text-title color-heading font-medium font-16 mb-3">
                                        {{ __('Description') }}
                                        <span class="text-danger">*</span>
                                    </div>
                                    <textarea class="form-control product-summernote" name="description" cols="30" rows="10" required
                                        placeholder="Description">{{old('description')}}</textarea>
                                    @if ($errors->has('description'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('description') }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="row mb-30">
                                <div class="col-md-12">
                                    <div class="label-text-title color-heading font-medium font-16 mb-3">
                                        {{ __('Shipping Return') }}
                                    </div>
                                    <textarea class="form-control product-summernote" name="shipping_return" cols="30" rows="10" required
                                        placeholder="Shipping Return">{{old('shipping_return')}}</textarea>
                                    @if ($errors->has('shipping_return'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('shipping_return') }}</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="row mb-30">
                                <div class="col-md-12">
                                    <div class="label-text-title color-heading font-medium font-16 mb-3">
                                        {{ __('Additional Information') }}
                                    </div>
                                    <textarea class="form-control product-summernote" name="additional_information" cols="30" rows="10" required
                                        placeholder="Additional Information">{{old('additional_information')}}</textarea>
                                    @if ($errors->has('additional_information'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('additional_information') }}</span>
                                    @endif
                                </div>
                            </div>
                            @if($type == DIGITAL_PRODUCT)
                            <div class="row mb-30">
                                <div class="col-md-12">
                                    <div class="label-text-title color-heading font-medium font-16 mb-3">
                                        {{ __('File') }}
                                        <span class="text-danger">*</span>
                                    </div>
                                    <div class="border p-20 rounded-1">
                                        <div class="upload-course-video-4-wrap upload-introduction-box-content-left d-flex align-items-center flex-column">
                                            <input type="file" name="file" class="form-control" accept="zip,application/octet-stream,application/zip,application/x-zip,application/x-zip-compressed" id="file" title="{{ __('Upload File') }}">
                                        </div>

                                        <p class="font-14 color-gray text-center mt-3 pb-30">{{ __('No file selected') }} (ZIP only)</p>
                                        @if ($errors->has('file'))
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('file') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="row">
                                <div class="col-md-12 mb-30">
                                    <div class="input__group mb-25">
                                        <label class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Thumbnail') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="upload-img-box">
                                            <img src="">
                                            <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                                                onchange="previewFile(this)">
                                            <div class="upload-img-box-icon">
                                                <i class="fa fa-camera"></i>
                                                <p class="m-0">{{__('Thumbnail')}}</p>
                                            </div>
                                        </div>
                                        @if ($errors->has('thumbnail'))
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                            $errors->first('thumbnail') }}</span>
                                        @endif
                                        <p><span class="text-black">{{ __('Accepted') }}:</span> PNG, JPG <br>
                                            <span class="text-black">{{ __('Recommend') }}:</span> 250 x 250</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 mb-30">
                                    <div class="input__group mb-25">
                                        <label class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Product Image 1') }}</label>
                                        <div class="upload-img-box">
                                            <img src="">
                                            <input type="file" name="image_1" id="image_1" accept="image/*"
                                                onchange="previewFile(this)">
                                            <div class="upload-img-box-icon">
                                                <i class="fa fa-camera"></i>
                                                <p class="m-0">{{__('Image 1')}}</p>
                                            </div>
                                        </div>
                                        @if ($errors->has('image_1'))
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                            $errors->first('image_1') }}</span>
                                        @endif
                                        <p><span class="text-black">{{ __('Accepted') }}:</span> PNG, JPG <br>
                                            <span class="text-black">{{ __('Recommend') }}:</span> 800 x 800</p>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-30">
                                    <div class="input__group mb-25">
                                        <label class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Product Image 2') }}</label>
                                        <div class="upload-img-box">
                                            <img src="">
                                            <input type="file" name="image_2" id="image_2" accept="image/*"
                                                onchange="previewFile(this)">
                                            <div class="upload-img-box-icon">
                                                <i class="fa fa-camera"></i>
                                                <p class="m-0">{{__('Image 2')}}</p>
                                            </div>
                                        </div>
                                        @if ($errors->has('image_2'))
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                            $errors->first('image_2') }}</span>
                                        @endif
                                        <p><span class="text-black">{{ __('Accepted') }}:</span> PNG, JPG <br>
                                            <span class="text-black">{{ __('Recommend') }}:</span> 800 x 800</p>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-30">
                                    <div class="input__group mb-25">
                                        <label class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Product Image 3') }}</label>
                                        <div class="upload-img-box">
                                            <img src="">
                                            <input type="file" name="image_3" id="image_3" accept="image/*"
                                                onchange="previewFile(this)">
                                            <div class="upload-img-box-icon">
                                                <i class="fa fa-camera"></i>
                                                <p class="m-0">{{__('Image 3')}}</p>
                                            </div>
                                        </div>
                                        @if ($errors->has('image_3'))
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                            $errors->first('image_3') }}</span>
                                        @endif
                                        <p><span class="text-black">{{ __('Accepted') }}:</span> PNG, JPG <br>
                                            <span class="text-black">{{ __('Recommend') }}:</span> 800 x 800</p>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-30">
                                    <div class="input__group mb-25">
                                        <label class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Product Image 4') }}</label>
                                        <div class="upload-img-box">
                                            <img src="">
                                            <input type="file" name="image_4" id="image_4" accept="image/*"
                                                onchange="previewFile(this)">
                                            <div class="upload-img-box-icon">
                                                <i class="fa fa-camera"></i>
                                                <p class="m-0">{{__('Image 4')}}</p>
                                            </div>
                                        </div>
                                        @if ($errors->has('image_4'))
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                            $errors->first('image_4') }}</span>
                                        @endif
                                        <p><span class="text-black">{{ __('Accepted') }}:</span> PNG, JPG <br>
                                            <span class="text-black">{{ __('Recommend') }}:</span> 800 x 800</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-30">
                                    <label class="font-medium font-15 color-heading">{{__('Meta Title')}}</label>
                                    <input type="text" name="meta_title" class="form-control"
                                        placeholder="{{ __('Meta Title') }}">
                                    @if ($errors->has('meta_title'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('meta_title') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-30">
                                    <label class="font-medium font-15 color-heading">{{__('Meta Description')}}</label>
                                    <textarea class="form-control product-summernote" name="meta_description"
                                        id="exampleFormControlTextarea1" rows="3"
                                        placeholder="{{ __('Type Meta Description') }}"></textarea>
                                    @if ($errors->has('meta_description'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('meta_description') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-30">
                                    <label class="font-medium font-15 color-heading">{{__('Meta Keywords')}}</label>
                                    <input type="text" name="meta_keywords" class="form-control"
                                        placeholder="{{ __('Type meta keywords (comma separated)') }}">
                                    @if ($errors->has('meta_keywords'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                        $errors->first('meta_keywords') }}</span>
                                    @endif

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-30">
                                    <div class="input__group mb-25">
                                        <label class="label-text-title color-heading font-medium font-16 mb-3">{{ __('OG Image') }}</label>
                                        <div class="upload-img-box">
                                            <img src="">
                                            <input type="file" name="og_image" id="og_image" accept="image/*"
                                                onchange="previewFile(this)">
                                            <div class="upload-img-box-icon">
                                                <i class="fa fa-camera"></i>
                                                <p class="m-0">{{__('OG Image')}}</p>
                                            </div>
                                        </div>
                                        @if ($errors->has('og_image'))
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{
                                            $errors->first('og_image') }}</span>
                                        @endif
                                        <p><span class="text-black">{{ __('Accepted') }}:</span> PNG, JPG <br>
                                            <span class="text-black">{{ __('Recommend') }}:</span> 1200 x 627</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="stepper-action-btns">
                            <a href="{{route('lms_product.instructor.product.my-product')}}" class="theme-btn theme-button3">{{__('Cancel')}}</a>
                            <button type="submit" class="theme-btn default-hover-btn theme-button1">{{__('Upload')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="redirect-url" value="{{ route('lms_product.instructor.product.my-product') }}">
@endsection

@push('style')
<link href="{{ asset('common/css/summernote/summernote.min.css') }}" rel="stylesheet">
<link href="{{ asset('common/css/summernote/summernote-lite.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('common/css/select2.css')}}">
<link rel="stylesheet" href="{{asset('admin/css/custom/image-preview.css')}}">
@endpush

@push('script')
<script src="{{ asset('common/js/summernote/summernote-lite.min.js') }}"></script>
<script src="{{asset('common/js/select2.min.js')}}"></script>
<script src="{{asset('admin/js/custom/slug.js')}}"></script>
<script src="{{asset('admin/js/custom/image-preview.js')}}"></script>
<script src="{{asset('addon/product/js/product-upload.js')}}"></script>

@endpush
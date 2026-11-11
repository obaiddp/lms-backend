@extends('layouts.instructor')
@push('style')
<link rel="stylesheet" href="{{ asset('addon/product/css/ecommerce-product.css') }}">
@endpush
@section('breadcrumb')
<div class="page-banner-content text-center">
    <h3 class="page-banner-heading text-white pb-15"> {{ __($title) }} </h3>

    <!-- Breadcrumb Start-->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb justify-content-center">
            <li class="breadcrumb-item font-14"><a href="{{ route('instructor.dashboard') }}">{{ __('Dashboard') }}</a>
            </li>
            <li class="breadcrumb-item font-14 active" aria-current="page">{{ __($title) }}</li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
<div>
    <div class="upload-product">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h6 class="dashboard-title"> {{ __('My Product') }}</h6>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="menu-products table-responsive">
                        <table class="table table-size">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __("Image") }}</th>
                                    <th scope="col">{{ __("Product") }}</th>
                                    <th scope="col">{{ __("Category") }}</th>
                                    <th scope="col">{{ __("Price") }}</th>
                                    <th scope="col">{{ __("Type") }}</th>
                                    <th scope="col">{{ __("Status") }}</th>
                                    <th scope="col">{{ __("Action") }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $product)
                                <tr>
                                    <td scope="row">
                                        <img class="products-img-list" src="{{getImageFile($product->thumbnail_path)}}" alt="{{ $product->title }}">
                                    </td>
                                    <td>{{ $product->title }}</td>
                                    <td>{{ $product->category->name }}</td>
                                    <td>
                                        @if(get_currency_placement() == 'after')
                                            {{$product->current_price}} {{ get_currency_symbol() }}
                                        @else
                                            {{ get_currency_symbol() }} {{$product->current_price}}
                                        @endif
                                    </td>
                                    <td>{{ getProductByType($product->type) }}</td>
                                    <td>
                                        ({{ $product->quantity >=0 ? $product->quantity : 0 }})
                                        <button class="product-btu {{ $product->quantity < 1  ? 'out-stock-btu' : 'stock-btu' }} stock-modal-btn" data-slug="{{ $product->slug }}" >
                                            {{ $product->quantity < 1 ? __('Out Of Stock') : __('In Stock') }}
                                        </button>
                                    </td>
                                    <td>
                                        <div class="mean-all-icon">
                                            <div class="icon-view-list">
                                                <a href="{{ route('lms_product.instructor.product.edit', [$product->slug]) }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                        viewBox="0 0 20 20" fill="none">
                                                        <g clip-path="url(#clip0_2734_130627)">
                                                            <path
                                                                d="M9.16797 3.33203H3.33464C2.89261 3.33203 2.46868 3.50763 2.15612 3.82019C1.84356 4.13275 1.66797 4.55667 1.66797 4.9987V16.6654C1.66797 17.1074 1.84356 17.5313 2.15612 17.8439C2.46868 18.1564 2.89261 18.332 3.33464 18.332H15.0013C15.4433 18.332 15.8673 18.1564 16.1798 17.8439C16.4924 17.5313 16.668 17.1074 16.668 16.6654V10.832"
                                                                stroke="#52526C" stroke-width="1.5" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                            <path
                                                                d="M15.418 2.08417C15.7495 1.75265 16.1991 1.56641 16.668 1.56641C17.1368 1.56641 17.5864 1.75265 17.918 2.08417C18.2495 2.41569 18.4357 2.86533 18.4357 3.33417C18.4357 3.80301 18.2495 4.25265 17.918 4.58417L10.0013 12.5008L6.66797 13.3342L7.5013 10.0008L15.418 2.08417Z"
                                                                stroke="#52526C" stroke-width="1.5" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_2734_130627">
                                                                <rect width="20" height="20" fill="white" />
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </a>
                                            </div>

                                            <div class="icon-view-list">
                                                <button class="deleteItem" data-formid="delete_row_form_{{$product->slug}}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                        viewBox="0 0 20 20" fill="none">
                                                        <path d="M2.5 5H4.16667H17.5" stroke="#52526C" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path
                                                            d="M6.66797 5.0013V3.33464C6.66797 2.89261 6.84356 2.46868 7.15612 2.15612C7.46869 1.84356 7.89261 1.66797 8.33464 1.66797H11.668C12.11 1.66797 12.5339 1.84356 12.8465 2.15612C13.159 2.46868 13.3346 2.89261 13.3346 3.33464V5.0013M15.8346 5.0013V16.668C15.8346 17.11 15.659 17.5339 15.3465 17.8465C15.0339 18.159 14.61 18.3346 14.168 18.3346H5.83464C5.39261 18.3346 4.96868 18.159 4.65612 17.8465C4.34356 17.5339 4.16797 17.11 4.16797 16.668V5.0013H15.8346Z"
                                                            stroke="#52526C" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path d="M8.33203 9.16797V14.168" stroke="#52526C"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path d="M11.668 9.16797V14.168" stroke="#52526C" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </button>
                                                <form action="{{ route('lms_product.instructor.product.delete', [$product->slug]) }}" method="post" id="delete_row_form_{{ $product->slug }}">
                                                    {{ method_field('DELETE') }}
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="text-center" colspan="7">
                                        {{ __('No Data Found') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-12">
                    @if(@$products->hasPages())
                        {{ @$products->links('frontend.paginate.paginate') }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalStock" tabindex="-1" aria-labelledby="modalStockLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="model-area-box">
                <h4 class="model-title">{{ __('Add new Stock') }}</h4>
                <form method="POST" action="{{route('lms_product.instructor.product.add-stock')}}" enctype="multipart/form-data" data-handler="getStockResponse" class="ajax row g-3 needs-validation">
                    @csrf
                    <div class="mb-3 quantity-area">
                        <h6>{{ __('Number of Quantity') }}</h6>
                        <input type="hidden" name="slug" value="">
                        <input type="number" min=1 name="quantity">
                    </div>
                    <button type="submit" class="theme-btn theme-button2 load-more-btn">{{ __('Update') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{asset('common/js/select2.min.js')}}"></script>
<script src="{{ asset('common/js/summernote/summernote-lite.min.js') }}"></script>
<script src="{{asset('addon/product/js/product-upload.js')}}"></script>
@endpush
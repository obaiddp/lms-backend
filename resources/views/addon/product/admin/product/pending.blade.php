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
                                <h2>{{__($title)}}</h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('Dashboard')}}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{__('Manage Product')}}</li>
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
                            <h2>{{ __($title) }}</h2>
                        </div>
                        <div class="customers__table">
                            <table id="customers-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th>{{__('Image')}}</th>
                                    <th>{{__('Title')}}</th>
                                    <th>{{__('Type')}}</th>
                                    <th>{{__('Author')}}</th>
                                    <th>{{__('Category')}}</th>
                                    <th>{{__('Price')}}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($products as $product)
                                    <tr class="removable-item">
                                        <td>
                                            <a href="#"> <img src="{{getImageFile($product->thumbnail_path)}}" width="80"> </a>
                                        </td>
                                        <td>
                                            <a href="{{route('lms_product.frontend.view', [$product->slug])}}" class="btn-action mr-30" title="View Details">     
                                            {{$product->title}}
                                            </a>
                                        </td>
                                        <td>
                                            {{getProductByType($product->type)}}
                                        </td>

                                        <td>
                                            {{$product->user ? $product->user->name : '' }}
                                        </td>
                                        <td>
                                            {{$product->category ? $product->category->name : '' }}
                                        </td>
                                        <td>
                                            @if(get_currency_placement() == 'after')
                                                {{$product->current_price}} {{ get_currency_symbol() }}
                                            @else
                                                {{ get_currency_symbol() }} {{$product->current_price}}
                                            @endif
                                        </td>
                                        <td>

                                            <div class="">

                                                <a href="{{route('lms_product.admin.product.status-change', [$product->slug, 1])}}" class="btn-action approve-btn mr-30" title="Make as Active">
                                                    {{__('Approve')}}
                                                </a>

                                                <a href="{{route('lms_product.frontend.view', [$product->slug])}}" target="_blank" class="btn-action mr-30" title="View Details">
                                                    <img src="{{asset('admin/images/icons/eye-2.svg')}}" alt="eye">
                                                </a>

                                                <button class="btn-action ms-2 deleteItem" data-formid="delete_row_form_{{$product->slug}}">
                                                    <img src="{{asset('admin/images/icons/trash-2.svg')}}" alt="trash">
                                                </button>

                                                <form action="{{route('lms_product.admin.product.delete', [$product->slug])}}" method="get" id="delete_row_form_{{ $product->slug }}">

                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="mt-3">
                                {{$products->links()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Page content area end -->
@endsection

@push('style')
    <link rel="stylesheet" href="{{asset('admin/css/jquery.dataTables.min.css')}}">
@endpush

@push('script')
    <script src="{{asset('admin/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('admin/js/custom/data-table-page.js')}}"></script>
@endpush

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
                                        <li class="breadcrumb-item active" aria-current="page">{{__($title)}}</li>
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
                                <h2>{{__('Product Category List')}}</h2>
                                <a href="{{route('lms_product.admin.category.create')}}" class="btn btn-success btn-sm"> <i class="fa fa-plus"></i> {{__('Add Category')}} </a>
                            </div>
                            <div class="customers__table">
                                <table id="customers-table" class="row-border data-table-filter table-style">
                                    <thead>
                                    <tr>
                                        <th>{{__('Image')}}</th>
                                        <th>{{__('Name')}}</th>
                                        <th>{{__('Feature')}}</th>
                                        <th>{{ __('Total Product') }}</th>
                                        <th>{{__('Action')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($productCategories as $productCategory)
                                        <tr class="removable-item">
                                        <td>
                                            <div class="user-info">
                                                <div class="user-info__img">
                                                    <img src="{{getImageFile($productCategory->image_path)}}" alt="category">
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{$productCategory->name}}
                                        </td>
                                        <td>
                                            @if($productCategory->is_feature == 'yes')
                                                <span class="status active">{{ __('Yes') }}</span>
                                            @else
                                                <span class="status blocked">{{ __('No') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $productCategory->products?->count() }}
                                        </td>
                                        <td>
                                            <div class="action__buttons">
                                                <a href="{{route('lms_product.admin.category.edit', [$productCategory->slug])}}" class="btn-action" title="Edit">
                                                    <img src="{{asset('admin/images/icons/edit-2.svg')}}" alt="edit">
                                                </a>
                                                <button class="btn-action ms-2 deleteItem" data-formid="delete_row_form_{{$productCategory->slug}}">
                                                    <img src="{{asset('admin/images/icons/trash-2.svg')}}" alt="trash">
                                                </button>

                                                <form action="{{route('lms_product.admin.category.delete', [$productCategory->slug])}}" method="get" id="delete_row_form_{{ $productCategory->slug }}">

                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div class="mt-3">
                                    {{$productCategories->links()}}
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
    <link rel="stylesheet" href="{{asset('admin/css/jquery.dataTables.min.css')}}" />
@endpush

@push('script')
    <script src="{{asset('admin/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('admin/js/custom/data-table-page.js')}}"></script>
@endpush

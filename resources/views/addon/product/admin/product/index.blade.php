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
                            <a  href="{{route('lms_product.admin.product.create',['step'=>'step-1'])}}" class="btn btn-blue mr-30" title="Add  Product">
                               {{__('Add Product')}}
                            </a>
                        </div>
                        <div class="customers__table all-product">
                            <table id="customers-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th>{{__('Image')}}</th>
                                    <th>{{__('Title')}}</th>
                                    <th>{{__('Type')}}</th>
                                    <th>{{__('Author')}}</th>
                                    <th>{{__('Category')}}</th>
                                    <th>{{__('Price')}}</th>
                                    <th>{{__('Featured')}}</th>
                                    <th>{{__('Status')}}</th>
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
                                            @if($product->status == 1)
                                            <span id="hidden_id" style="display: none">{{$product->id}}</span>
                                            <select name="status" class="status change-feature label-inline font-weight-bolder mb-1 badge badge-info">
                                                <option value="1" @if($product->is_feature == 1) selected @endif>{{ __('YES') }}</option>
                                                <option value="0" @if($product->is_feature == 0) selected @endif>{{ __('NO') }}</option>
                                            </select>
                                            @endif
                                        </td>
                                        <td>
                                            @if($product->status == 1)
                                            <span class="status active">{{ __('Published') }}</span>
                                            @elseif($product->status == 2)
                                                <span class="status active">{{ __('Waiting for Review') }}</span>
                                            @elseif($product->status == 3)
                                                <span class="status blocked">{{ __('Hold') }}</span>
                                            @elseif($product->status == 4)
                                                <span class="status ">{{ __('Draft') }}</span>
                                            @elseif($product->status == 6)
                                                <span class="status pending">{{ __('Upcoming Pending') }}</span>
                                            @elseif($product->status == 7)
                                                <span class="status upcoming">{{ __('Upcoming') }}</span>
                                            @else
                                                <span class="status ">{{ __('Pending') }}</span>
                                            @endif
                                        </td>
                                        <td>

                                            <div class="">
                                                @if($product->status == 1)
                                                <a href="{{route('lms_product.admin.product.status-change', [$product->slug, 3])}}" class="btn-action hold-btn mr-30" title="Make as Hold">
                                                    {{__('Hold')}}
                                                </a>
                                                @elseif($product->status == 3)
                                                <a href="{{route('lms_product.admin.product.status-change', [$product->slug, 1])}}" class="btn-action approve-btn mr-30" title="Make as Active">
                                                    {{__('Approve')}}
                                                </a>
                                                @endif

                                                <a href="{{route('lms_product.frontend.view', [$product->slug])}}" class="btn-action mr-30" title="View Details">
                                                    <img src="{{asset('admin/images/icons/eye-2.svg')}}" alt="eye">
                                                </a>
                                                <a href="{{route('lms_product.admin.product.edit', [$product->slug])}}" class="btn-action mr-30" title="Edit Product">
                                                    <img src="{{asset('admin/images/icons/edit.svg')}}" alt="edit">
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
    <script>
        (function ($) {
        "use strict";

            $(".change-feature").change(function () {
                var id = $(this).closest('tr').find('#hidden_id').html();
                var status_value = $(this).closest('tr').find('.status option:selected').val();
                Swal.fire({
                    title: "{{ __('Are you sure to change?') }}",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "{{__('Yes, Change it!')}}",
                    cancelButtonText: "{{__('No, cancel!')}}",
                    reverseButtons: true
                }).then(function (result) {
                    if (result.value) {
                        $.ajax({
                            type: "POST",
                            url: "{{route('lms_product.admin.product.feature-change')}}",
                            data: {"status": status_value, "id": id, "_token": "{{ csrf_token() }}",},
                            datatype: "json",
                            success: function (data) {
                                toastr.options.positionClass = 'toast-bottom-right';
                                toastr.success('', "{{ __('Feature has been updated') }}");
                                location.reload();
                            },
                            error: function () {
                                alert("Error!");
                            },
                        });
                    } else if (result.dismiss === "cancel") {
                        location.reload();
                    }
                });
            });
            
        })(jQuery)
    </script>
@endpush

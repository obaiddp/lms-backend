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
            <li class="breadcrumb-item font-14"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
            </li>
            <li class="breadcrumb-item font-14 active" aria-current="page">{{ __($title) }}</li>
        </ol>
    </nav>
</div>
@endsection

@section('content2')
<div>
    <div class="upload-product">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h6 class="dashboard-title"> {{ __('Course Order History') }}</h6>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="menu-products table-responsive">
                        <table class="table table-size-2">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __("Image") }}</th>
                                    <th scope="col">{{ __("Order No") }}</th>
                                    <th scope="col">{{ __("Course") }}</th>
                                    <th scope="col">{{ __("Student") }}</th>
                                    <th scope="col">{{ __("Type") }}</th>
                                    <th scope="col">{{ __("Amount") }}</th>
                                
                                    <th scope="col">{{ __("Status") }}</th>
                                    {{-- <th scope="col">{{ __("Action") }}</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orderItems as $orderItem)
                                <tr>
                                    <td scope="row">
                                        <img class="products-img-list" src="{{getImageFile($orderItem->course?->image_path)}}" alt="{{ $orderItem->course?->title }}">
                                    </td>
                                    <td>{{ $orderItem->order?->order_number }}</td>
                                    <td>
                                        @if ($orderItem->course)
                                            <a href="{{route('admin.course.view', [$orderItem->course->uuid])}}" class="btn-action mr-30" title="View Details">
                                            {{ $orderItem->course?->title }}
                                            </a>
                                        @endif
                                     
                                    </td>
                                    <td>
                                        {{ $orderItem->user->name }}
                                    </td>
                                    <td>
                                        @if ($orderItem->course)
                                            @if ($orderItem->course->course_type = 1)
                                                {{ "General" }}
                                            @elseif ($orderItem->course->course_type = 2)
                                                {{ "Scorm" }}
                                            @elseif ($orderItem->course->course_type = 3)
                                                {{ "Live" }}
                                            @endif
                                        @endif
                                      
                                    </td>
                                    {{-- <td><span class="product-btu stock-btu">{{strtoupper($orderItem->course?->learner_accessibility) }}</span></td> --}}
                                    <td>
                                        @if(get_currency_placement() == 'after')
                                            {{$orderItem->unit_price}} {{ get_currency_symbol() }}
                                        @else
                                            {{ get_currency_symbol() }} {{$orderItem->unit_price}}
                                        @endif
                                    </td>
                              
                                    <td>
                                        {{-- @if ($orderItem->course) --}}
                                            {{-- @if ($orderItem->course?->learner_accessibility == 'free')
                                                <span class="product-btu stock-btu">{{ __('Free') }}</span>
                                            @else --}}
                                                  @if($orderItem->order?->payment_status == 'due')
                                                  <span class="product-btu pending-status">{{ __('Due') }}</span>
                                                  @elseif($orderItem->order?->payment_status == 'paid')
                                                  <span class="product-btu stock-btu">{{ __('Paid') }}</span>
                                                  @elseif($orderItem->order?->payment_status == 'free')
                                                  <span class="product-btu stock-btu">{{ __('Free') }}</span>
                                              
                                                  @endif
                                            {{-- @endif  --}}
                                        {{-- @endif --}}
                                          
                                    </td>
                                    {{-- <td>
                                        @if(!in_array($orderItem->delivery_status, [3,4]))
                                        <div class="mean-all-icon">
                                            <div class="icon-view-list status-parent">
                                                <select name="delivery_status" {{ in_array($orderItem->delivery_status, [3,4]) ? 'disabled' : '' }} data-id="{{$orderItem->id}}">
                                                    <option {{ $orderItem->delivery_status == 0 ? 'selected' : '' }} value="0">{{ __('Pending') }}</option>
                                                    <option {{ $orderItem->delivery_status == 1 ? 'selected' : '' }} value="1">{{ __('Processing') }}</option>
                                                    <option {{ $orderItem->delivery_status == 2 ? 'selected' : '' }} value="2">{{ __('Shipped') }}</option>
                                                    <option {{ $orderItem->delivery_status == 3 ? 'selected' : '' }} value="3">{{ __('Delivered') }}</option>
                                                    <option {{ $orderItem->delivery_status == 4 ? 'selected' : '' }} value="4">{{ __('Cancelled') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        @endif
                                    </td> --}}
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
                <div class="col-md-12">
                    @if(@$orderItems->hasPages())
                        {{ @$orderItems->links('frontend.paginate.paginate') }}
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
<script>
    (function ($) {
        "use strict";
        $(document).on('change', ':input[name=delivery_status]', function() {
            var id = $(this).data('id');
            var delivery_status = $(this).val();
            Swal.fire({
                title: "{{ __('Are you sure?') }}",
                text: "{{ __('Student will get notified') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, change it!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        type: 'POST',
                        data: {'id' : id, 'delivery_status' : delivery_status, "_token": "{{ csrf_token() }}",},
                        url: "{{ route('lms_product.admin.product.orders.status_change') }}",
                        success: function (response) {
                            toastr.success(response.message);
                            location.reload();
                        }
                    })
                }
            })
        });
    })(jQuery)

</script>
@endpush
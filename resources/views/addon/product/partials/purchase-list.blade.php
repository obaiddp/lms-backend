@if(count($orderItems) > 0)
<div class="table-responsive courses-table">
    <table class="table bg-white my-courses-page-table">
        <thead>
            <tr>
                <th scope="col" class="color-gray font-15 font-medium">{{__('Product')}}</th>
                <th scope="col" class="color-gray font-15 font-medium">{{__('Type')}}</th>
                <th scope="col" class="color-gray font-15 font-medium">{{__('Quantity')}}</th>
                <th scope="col" class="color-gray font-15 font-medium">{{__('Author')}}</th>
                <th scope="col" class="color-gray font-15 font-medium">{{__('Price')}}</th>
                <th scope="col" class="color-gray font-15 font-medium">{{__('Order ID')}}</th>
                <th scope="col" class="color-gray font-15 font-medium">{{__('Payment Status')}}</th>
                <th scope="col" class="color-gray font-15 font-medium">{{__('Delivery Status')}}</th>
            </tr>
        </thead>
        <tbody>

            @foreach($orderItems as $orderItem)
            <tr>
                <td class="wishlist-course-item">
                    <div class="card course-item wishlist-item border-0 d-flex align-items-center">
                        <div class="card-body flex-grow-1">
                            <h5 class="card-title course-title"><a target="_blank"
                                href="{{ route('lms_product.frontend.view-student', @$orderItem->product->slug) }}">{{
                                    @$orderItem->product->title }}</a></h5>
                            <div class="card-text font-medium font-11 mb-1">
                                <button
                                    class="color-gray2 me-2 font-medium bg-transparent border-0 my-course-give-a-review-btn star-full my-learning-give-review productReview"
                                    data-product_id="{{ @$orderItem->product->id }}"
                                     data-bs-toggle="modal"
                                    data-bs-target="#writeReviewModal">
                                    <span class="iconify me-1"
                                        data-icon="bi:star-fill">
                                    </span>
                                    {{ __('Give') }}
                                    </button>

                                    @if(@$orderItem->unit_price > 0)
                                        @if(@$orderItem->product->type == PHYSICAL_PRODUCT)
                                <a target="_blank" href="{{route('student.download-invoice', [@$orderItem->id])}}"
                                    class="color-gray2 me-2 my-learning-invoice"><img
                                        src="{{ asset('frontend/assets/img/courses-img/invoice-icon.png') }}"
                                        alt="report" class="me-1">{{__('Invoice')}}
                                </a>
                                @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </td>

                <td class="wishlist-price font-15 color-heading">
                    <span>{{ getProductByType(@$orderItem->product->type) }}</span>
                    @if(@$orderItem->product->type == DIGITAL_PRODUCT && $orderItem->delivery_status == 3)
                    <span><a target="__blank" class="fw-bold text-primary" href="{{ asset(@$orderItem->product->main_file_path) }}">{{ __('Download') }}</a></span>
                    @endif
                </td>

                <td class="wishlist-price font-15 color-heading">
                    {{ @$orderItem->unit }}
                </td>

                <td class="wishlist-price font-15 color-heading">
                    {{ @$orderItem->product->user->name }}
                </td>
                
                <td class="wishlist-price font-15 color-heading">
                    @if(get_currency_placement() == 'after')
                    {{ $orderItem->unit_price }} {{ get_currency_symbol() }}
                    @else
                    {{ get_currency_symbol() }} {{ $orderItem->unit_price }}
                    @endif
                </td>

                <td class="wishlist-price font-15 color-heading">
                    {{@$orderItem->order->order_number}}
                </td>

                <td class="wishlist-add-to-cart-btn">
                    @if($orderItem->order->payment_status == 'due')
                    <span class="product-btu pending-status">{{ __('Due') }}</span>
                    @elseif($orderItem->order->payment_status == 'free')
                    <span class="product-btu stock-btu">{{ __('Free') }}</span>
                    @elseif($orderItem->order->payment_status == 'paid')
                    <span class="product-btu stock-btu">{{ __('Paid') }}</span>
                    @endif
                </td>

                <td class="wishlist-add-to-cart-btn">
                    @if($orderItem->delivery_status == 0)
                    <span class="product-btu pending-status">{{ __('Pending') }}</span>
                    @elseif($orderItem->delivery_status == 1)
                    <span class="product-btu stock-btu">{{ __('Processing') }}</span>
                    @elseif($orderItem->delivery_status == 2)
                    <span class="product-btu stock-btu">{{ __('Shipped') }}</span>
                    @elseif($orderItem->delivery_status == 3)
                    <span class="product-btu stock-btu">{{ __('Delivered') }}</span>
                    @elseif($orderItem->delivery_status == 4)
                    <span class="product-btu out-stock-btu">{{ __('Cancelled') }}</span>
                    @endif
                </td>
            </tr>
            @endforeach

        </tbody>
    </table>
</div>

<div class="courses-cards">
    @foreach($orderItems as $orderItem)
    <div class="course-card">
        <div class="wishlist-course-item">
            <div class="card course-item wishlist-item border-0 d-flex align-items-center">
                <div class="card-body flex-grow-1 ps-0">
                    <h5 class="card-title course-title ms-0"><a target="_blank"
                            href="{{ route('lms_product.frontend.view', @$orderItem->product->slug) }}">{{
                            @$orderItem->product->title }}</a></h5>
                    <div class="card-text font-medium font-11 mb-1">
                        <button
                            class="color-gray2 me-2 font-medium bg-transparent border-0 my-course-give-a-review-btn star-full my-learning-give-review productReview"
                            data-product_id="{{ @$orderItem->product->id }}"
                                data-bs-toggle="modal"
                            data-bs-target="#writeReviewModal">
                            <span class="iconify me-1"
                                data-icon="bi:star-fill">
                            </span>
                            {{ __('Give') }}
                            </button>

                            @if(@$orderItem->unit_price > 0)
                                @if(@$orderItem->product->type == PHYSICAL_PRODUCT)
                        <a target="_blank" href="{{route('student.download-invoice', [@$orderItem->id])}}"
                            class="color-gray2 me-2 my-learning-invoice"><img
                                src="{{ asset('frontend/assets/img/courses-img/invoice-icon.png') }}"
                                alt="report" class="me-1">{{__('Invoice')}}
                        </a>
                        @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="wishlist-price font-15 color-heading">
            <strong class="me-2">Product Type:</strong>
            <span>{{ getProductByType(@$orderItem->product->type) }}</span>
            @if(@$orderItem->product->type == DIGITAL_PRODUCT && $orderItem->delivery_status == 3)
            <span><a target="__blank" class="fw-bold text-primary" href="{{ asset(@$orderItem->product->main_file_path) }}">{{ __('Download') }}</a></span>
            @endif
        </div>

        <div class="wishlist-price font-15 color-heading">
            <strong class="me-2">Quantity:</strong>
            {{ @$orderItem->unit }}
        </div>

        <div class="wishlist-price font-15 color-heading">
            <strong class="me-2">Author:</strong>
            {{ @$orderItem->product->user->name }}
        </div>
        
        <div class="wishlist-price font-15 color-heading">
            <strong class="me-2">Price:</strong>
            @if(get_currency_placement() == 'after')
            {{ $orderItem->unit_price }} {{ get_currency_symbol() }}
            @else
            {{ get_currency_symbol() }} {{ $orderItem->unit_price }}
            @endif
        </div>

        <div class="wishlist-price font-15 color-heading">
            <strong class="me-2">Order ID:</strong>
            {{@$orderItem->order->order_number}}
        </div>

        <div class="wishlist-add-to-cart-btn font-15 color-heading">
            <strong class="me-2">Payment Status:</strong>
            @if($orderItem->order->payment_status == 'due')
            <span class="product-btu pending-status">{{ __('Due') }}</span>
            @elseif($orderItem->order->payment_status == 'free')
            <span class="product-btu stock-btu">{{ __('Free') }}</span>
            @elseif($orderItem->order->payment_status == 'paid')
            <span class="product-btu stock-btu">{{ __('Paid') }}</span>
            @endif
        </div>

        <div class="wishlist-add-to-cart-btn font-15 color-heading">
            <strong class="me-2">Delivery Status:</strong>
            @if($orderItem->delivery_status == 0)
            <span class="product-btu pending-status">{{ __('Pending') }}</span>
            @elseif($orderItem->delivery_status == 1)
            <span class="product-btu stock-btu">{{ __('Processing') }}</span>
            @elseif($orderItem->delivery_status == 2)
            <span class="product-btu stock-btu">{{ __('Shipped') }}</span>
            @elseif($orderItem->delivery_status == 3)
            <span class="product-btu stock-btu">{{ __('Delivered') }}</span>
            @elseif($orderItem->delivery_status == 4)
            <span class="product-btu out-stock-btu">{{ __('Cancelled') }}</span>
            @endif
        </div>
    </div>
    @endforeach
</div>
@else
<!-- If there is no data Show Empty Design Start -->
<div class="empty-data">
    <img src="{{ asset('frontend/assets/img/empty-data-img.png') }}" alt="img" class="img-fluid">
    <h4 class="my-3">{{ __('Empty Product') }}</h4>
</div>
<!-- If there is no data Show Empty Design End -->
@endif
<!-- Pagination Start -->
@if(@$orderItems->hasPages())
{{ @$orderItems->links('frontend.paginate.paginate') }}
@endif
<!-- Pagination End -->
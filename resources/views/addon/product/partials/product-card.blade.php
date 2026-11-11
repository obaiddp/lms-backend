<a href="{{ route('lms_product.frontend.view', $product->slug) }}" class="product-flex !w-full">
@if ($product->old_price > $product->current_price)
    <span
        class="sale-tag">Sale</span>
@endif
    <img src="{{ getImageFile($product->thumbnail_path) }}" alt="{{ $product->title }}"
        class="product-img">
    <h4
        class="product-title">
        {{ $product->title }} </h4>
    @php
        $reviewCount = $product->reviews()->count();
        $averate_percent = $product->average_review * 20;
    @endphp
    @if ($product->old_price > $product->current_price)
                                <span class="new-price">
                                    @if ($currencyPlacement ?? get_currency_placement() == 'after')
                                        <span
                                            class="new-price-text">{{ number_format((float) $product->current_price, fmod($product->current_price, 1) == 0 ? 0 : 2) }}
                                            {{ $currencySymbol ?? get_currency_symbol() }}</span>
                                    @else
                                        <span
                                            class="new-price-text">{{ $currencySymbol ?? get_currency_symbol() }}
                                            {{ number_format((float) $product->current_price, fmod($product->current_price, 1) == 0 ? 0 : 2) }}</span>
                                    @endif
                                </span>
                                <span>/</span>
                                <span class="old-price">
                                    @if ($currencyPlacement ?? get_currency_placement() == 'after')
                                        <span class="gray-text">{{ number_format((float) $product->old_price, fmod($product->old_price, 1) == 0 ? 0 : 2)             }}
                                            {{ $currencySymbol ?? get_currency_symbol() }}</span>
                                    @else
                                        <span class="gray-text">{{ $currencySymbol ?? get_currency_symbol() }}
                                            {{ number_format((float) $product->old_price, fmod($product->old_price, 1) == 0 ? 0 : 2) }}</span>
                                    @endif
                                </span>
                            @else
                                <span class="new-price">
                                    @if ($currencyPlacement ?? get_currency_placement() == 'after')
                                        <span
                                            class="new-price-text">{{ number_format((float) $product->current_price, fmod($product->current_price, 1) == 0 ? 0 : 2) }}
                                            {{ $currencySymbol ?? get_currency_symbol() }}</span>
                                    @else
                                        <span
                                            class="new-price-text">{{ $currencySymbol ?? get_currency_symbol() }}
                                            {{ number_format((float) $product->current_price, fmod($product->current_price, 1) == 0 ? 0 : 2)     }}</span>
                                    @endif
                                </span>
                            @endif
                            <hr class="line">
                            <div class="product-description">
                                <h6 class="product-category">{{$product->category->name}}</h6>
                                @if (@$product->average_review > 0)
                                    <div class="product-reviews">
                                        <span>
                                            <svg width="16" height="15" viewBox="0 0 16 15" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M7.60845 0L10.0066 4.69921L15.2169 5.52786L11.4888 9.26079L12.3107 14.4721L7.60845 12.08L2.90617 14.4721L3.72814 9.26079L0 5.52786L5.21029 4.69921L7.60845 0Z"
                                                    fill="#FEC801" />
                                            </svg>
                                        </span>
                                        <span
                                            class="product-total-reviews">{{ number_format(@$product->average_review, 1) }}
                                            ({{ $reviewCount }} reviews)</span>
                                    </div>
                                @endif
                              
                            </div>
                            @if ($product->quantity > 0)
                                @if (auth()->user())
                                    @if (auth()->user()->role == 3)
                                        <div
                                            class="product-overlay">
                                            <button type="button" data-product_id="{{ $product->id }}" data-quantity=1
                                                data-route="{{ route('student.addToCart') }}"
                                                class="product-overlay-button"
                                                aria-current="page">
                                                {{ __('Add to cart') }}
                                            </button>
                                        </div>
                                    @else
                                        <div
                                            class="product-overlay">
                                            <button type="button" data-product_id="{{ $product->id }}" data-quantity=1
                                                data-route="{{ route('student.addToCart') }}"
                                                class="product-overlay-button"
                                                aria-current="page">
                                                {{ __('Add to cart') }}
                                            </button>
                                        </div>
                                    @endif
                                @else
                                    {{-- <div
                                        class="product-overlay">
                                        <button type="button"
                                            class="product-overlay-button"
                                            aria-current="page">
                                            {{ __('Out of stock') }}
                                        </button>
                                    </div> --}}
                                @endif
                            @else
                                <div
                                class="product-overlay">
                                <button type="button"
                                    class="product-overlay-button"
                                    aria-current="page">
                                    {{ __('Out of stock') }}
                                </button>
                                </div>
                            @endif
</a>
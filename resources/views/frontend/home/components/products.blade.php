@if (!$topProducts->isEmpty())
<section class="products-section">
    <div class="home-page-container">
        <p
            class="section-tag">
            <span class="section-tag-icon">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M1.68226 4.33298L6.42758 1.96032C6.59649 1.87586 6.79532 1.87586 6.96423 1.96032L11.7096 4.33298C12.1518 4.55409 12.1518 5.18518 11.7096 5.40629L6.96423 7.77895C6.79532 7.86341 6.59649 7.86341 6.42758 7.77895L1.68226 5.40629C1.24003 5.18518 1.24003 4.55409 1.68226 4.33298Z"
                        stroke="#013370" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M13.3912 7.30432V5.17389L12.1738 4.56519" stroke="#013370" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path
                        d="M2.43555 5.78259V8.58215C2.43555 9.35439 2.88014 10.0577 3.57774 10.3889L5.83861 11.4623C6.38144 11.7201 7.01139 11.7201 7.55422 11.4623L9.81509 10.3889C10.5127 10.0577 10.9573 9.35439 10.9573 8.58215V5.78259"
                        stroke="#013370" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </span>
            <span class="section-tag-title">Our Featured
                Products
            </span>
        </p>
        <h1 class="section-heading-home">Extensive
            products
        </h1>

        <div class="product-section-wrap">
            @foreach ($topProducts as $key => $product)
            <a href="{{ route('lms_product.frontend.view', $product->slug) }}"
                class="product-flex">
                @if ($product->old_price > $product->current_price)
                <span
                    class="sale-tag">Sale
                </span>
                @endif
                <img loading="lazy" src="{{ getImageFile($product->thumbnail_path) }}" alt="{{ $product->title }}"
                    class="product-img">
                <h4
                    class="product-title">
                    {{ $product->title }}
                </h4>
                @php
                $reviewCount = $product->reviews()->count();
                $averate_percent = $product->average_review * 20;
                @endphp

                @if ($product->old_price > $product->current_price)
                <span class="new-price">
                    @if ($currencyPlacement ?? get_currency_placement() == 'after')
                    <span
                        class="new-price-text">{{ number_format((float) $product->current_price, fmod($product->current_price, 1) == 0 ? 0 : 2) }}
                        {{ $currencySymbol ?? get_currency_symbol() }}
                    </span>
                    @else
                    <span
                        class="new-price-text">{{ $currencySymbol ?? get_currency_symbol() }}
                        {{ number_format((float) $product->current_price, fmod($product->current_price, 1) == 0 ? 0 : 2) }}
                    </span>
                    @endif
                </span>
                <span>/</span>
                <span class="old-price">
                    @if ($currencyPlacement ?? get_currency_placement() == 'after')
                    <span class="gray-text">{{ number_format((float) $product->old_price, fmod($product->old_price, 1) == 0 ? 0 : 2) }}
                        {{ $currencySymbol ?? get_currency_symbol() }}
                    </span>
                    @else
                    <span class="gray-text">{{ $currencySymbol ?? get_currency_symbol() }}
                        {{ number_format((float) $product->old_price, fmod($product->old_price, 1) == 0 ? 0 : 2) }}
                    </span>
                    @endif
                </span>
                @else
                <span class="new-price">
                    @if ($currencyPlacement ?? get_currency_placement() == 'after')
                    <span
                        class="new-price-text">{{ number_format((float) $product->current_price, fmod($product->current_price, 1) == 0 ? 0 : 2) }}
                        {{ $currencySymbol ?? get_currency_symbol() }}
                    </span>
                    @else
                    <span
                        class="new-price-text">{{ $currencySymbol ?? get_currency_symbol() }}
                        {{ number_format((float) $product->current_price, fmod($product->current_price, 1) == 0 ? 0 : 2) }}
                    </span>
                    @endif
                </span>
                @endif

                <hr class="line">

                <div class="product-description">
                    <h6 class="product-category">{{$product->category->name}}</h6>
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
                            ({{ $reviewCount }} reviews)
                        </span>
                    </div>
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
            @endforeach
        </div>

        <div class="product-section-slider">
            <div class="swiper products-slider">
                <div class="swiper-wrapper">
                    @foreach ($topProducts as $key => $product)
                    <div class="swiper-slide">
                        <a href="{{ route('lms_product.frontend.view', $product->slug) }}"
                            class="product-flex">
                            @if ($product->old_price > $product->current_price)
                            <span
                                class="sale-tag">Sale
                            </span>
                            @endif
                            <img loading="lazy" src="{{ getImageFile($product->thumbnail_path) }}" alt="{{ $product->title }}"
                                class="product-img">
                            <h4
                                class="product-title">
                                {{ $product->title }}
                            </h4>
                            @php
                            $reviewCount = $product->reviews()->count();
                            $averate_percent = $product->average_review * 20;
                            @endphp

                            @if ($product->old_price > $product->current_price)
                            <span class="new-price">
                                @if ($currencyPlacement ?? get_currency_placement() == 'after')
                                <span
                                    class="new-price-text">{{ number_format((float) $product->current_price, fmod($product->current_price, 1) == 0 ? 0 : 2) }}
                                    {{ $currencySymbol ?? get_currency_symbol() }}
                                </span>
                                @else
                                <span
                                    class="new-price-text">{{ $currencySymbol ?? get_currency_symbol() }}
                                    {{ number_format((float) $product->current_price, fmod($product->current_price, 1) == 0 ? 0 : 2) }}
                                </span>
                                @endif
                            </span>
                            <span>/</span>
                            <span class="old-price">
                                @if ($currencyPlacement ?? get_currency_placement() == 'after')
                                <span class="gray-text">{{ number_format((float) $product->old_price, fmod($product->old_price, 1) == 0 ? 0 : 2) }}
                                    {{ $currencySymbol ?? get_currency_symbol() }}
                                </span>
                                @else
                                <span class="gray-text">{{ $currencySymbol ?? get_currency_symbol() }}
                                    {{ number_format((float) $product->old_price, fmod($product->old_price, 1) == 0 ? 0 : 2) }}
                                </span>
                                @endif
                            </span>
                            @else
                            <span class="new-price">
                                @if ($currencyPlacement ?? get_currency_placement() == 'after')
                                <span
                                    class="new-price-text">{{ number_format((float) $product->current_price, fmod($product->current_price, 1) == 0 ? 0 : 2) }}
                                    {{ $currencySymbol ?? get_currency_symbol() }}
                                </span>
                                @else
                                <span
                                    class="new-price-text">{{ $currencySymbol ?? get_currency_symbol() }}
                                    {{ number_format((float) $product->current_price, fmod($product->current_price, 1) == 0 ? 0 : 2) }}
                                </span>
                                @endif
                            </span>
                            @endif

                            <hr class="line">

                            <div class="product-description">
                                <h6 class="product-category">{{$product->category->name}}</h6>
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
                                        ({{ $reviewCount }} reviews)
                                    </span>
                                </div>
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
                    </div>
                    @endforeach
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>

        <div class="view-all-btn-home">
            <a href="{{ route('lms_product.frontend.list') }}"
                class="view-all-btn-home-btn"
                aria-current="page">
                {{ __('View All Products') }}
            </a>
        </div>
    </div>
</section>
@endif
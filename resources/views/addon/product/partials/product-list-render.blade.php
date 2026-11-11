<div class="row">
    {{-- single product area --}}
    @foreach ($products as $product)
    <div class="col-sm-6 col-md-6 col-lg-4 col-xl-4 col-lg-6">
        @include('addon.product.partials.product-card')
    </div>
    @endforeach
    {{-- single product area --}}
</div>
<div class="row">
    {{$products->links()}}
</div>
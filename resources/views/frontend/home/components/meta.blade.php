@section('meta')
@php
$metaData = getMeta('home');
@endphp
<meta name="description" content="{{ __($metaData['meta_description']) }}">
<meta name="keywords" content="{{ __($metaData['meta_keyword']) }}">
<!-- Open Graph meta tags for social sharing -->
<meta property="og:type" content="Learning">
<meta property="og:title" content="{{ __($metaData['meta_title']) }}">
<meta property="og:description" content="{{ __($metaData['meta_description']) }}">
<meta property="og:image" content="{{ __($metaData['og_image']) }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="{{ __(get_option('app_name')) }}">
<!-- Twitter Card meta tags for Twitter sharing -->
<meta name="twitter:card" content="Learning">
<meta name="twitter:title" content="{{ __($metaData['meta_title']) }}">
<meta name="twitter:description" content="{{ __($metaData['meta_description']) }}">
<meta name="twitter:image" content="{{ __($metaData['og_image']) }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@17.0.8/build/css/intlTelInput.css">
@if (isAddonInstalled('LMSZAIPRODUCT'))
<link rel="stylesheet" href="{{ asset('addon/product/css/ecommerce-product.css') }}">
@endif
@endsection
@extends('frontend.layouts.app')
@section('meta')
    @php
        $metaData = getMeta('terms_and_condition');
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
@endsection
@section('content')

    <div class="pt-20">
    <header class="breadcrumb-section px-3 xl:px-0">
        <nav aria-label="breadcrumb" class="home-page-container">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" class="aileron">
                        <i class="fas fa-home"></i>
                        Home
                    </a>
                </li>
                <li class="breadcrumb-separator">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4.5 2.25L8.25 6L4.5 9.75" stroke="#77878F" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </li>
                <li class="breadcrumb-item active aileron" aria-current="page">Terms & Conditions</li>
            </ol>
        </nav>
    </header>
      
        <!-- FAQ Area Start -->
        <section class="faq-area support-tickets-page section-t-space">
            <div class="container">
            <div class="row">
                <div class="section-title text-center">
                    <h3 class="section-heading aileron">{{ __('Terms & Conditions') }}</h3>
                </div>
            </div>
                <!-- Tab Content-->
                <div class="row align-items-center">
                    <div class="col-md-12">
                        {!! @$policy->description !!}
                    </div>
                </div>

            </div>
        </section>
        <!-- FAQ Area End -->
    </div>
    <style>
    .breadcrumb-section {
        background: #F0F5F2 !important;
        padding-top: 10px !important;
        padding-bottom: 5px !important;
        width: 100%;
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        padding: 0;
        margin: 0;
        list-style: none;
    }

    .breadcrumb-item {
        display: flex;
        align-items: center;
    }

    .breadcrumb-item a {
        text-decoration: none;
        color: inherit;
    }

    .breadcrumb-separator {
        margin: 0 8px;
        color: #6c757d;
    }

    .breadcrumb-item.active {
        color: #6c757d;
    }
</style>
@endsection

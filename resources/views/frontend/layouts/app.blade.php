<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="{{ get_option('app_copyright') }}">
    <meta name="robots" content="index, follow">
    <meta name="csrf-token" content="{{ csrf_token() }}" >
    <meta name="msapplication-TileImage" content="{{ getImageFile(get_option('app_logo')) }}">
    <meta name="msapplication-TileColor" content="rgba(103, 20, 222,.55)">
    <meta name="theme-color" content="#754FFE">

    @hasSection('meta')
        @yield('meta')
    @else
    @php
        $defaultMeta = getMeta('default');
    @endphp

    <meta name="description" content="{{ $defaultMeta['meta_description'] }}">
    <meta name="keywords" content="{{ $defaultMeta['meta_keyword'] }}">
    
    <!-- Open Graph meta tags for social sharing -->
    <meta property="og:type" content="Learning">
    <meta property="og:title" content="{{ $defaultMeta['meta_title'] }}">
    <meta property="og:description" content="{{ $defaultMeta['meta_description'] }}">
    <meta property="og:image" content="{{ $defaultMeta['og_image'] }}">
    <meta property="og:url" content="{{ url()->current() }}">

    <meta property="og:site_name" content="{{ get_option('app_name') }}">
    
    <!-- Twitter Card meta tags for Twitter sharing -->
    <meta name="twitter:card" content="Learning">
    <meta name="twitter:title" content="{{ $defaultMeta['meta_title'] }}">
    <meta name="twitter:description" content="{{ $defaultMeta['meta_description'] }}">
    <meta name="twitter:image" content="{{ $defaultMeta['og_image'] }}">
    @endif  

    <title>{{ get_option('app_name') }} - {{ __(@$pageTitle) }}</title>
    <script src="{{ asset('js/bundle.js') }}" defer></script>
    <!--=======================================
      All Css Style link
    ===========================================-->

    <!-- Bootstrap core CSS -->
   
    <link href="{{ asset('frontend/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Font Awesome for this template -->
    <link href="{{ asset('frontend/assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="{{ asset('frontend/assets/fonts/feather/feather.css') }}">

    <!-- Sweet Alert css -->
    <link rel="stylesheet" href="{{asset('admin/sweetalert2/sweetalert2.css')}}">

    <!-- Custom styles for this template -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/style.css') }}?v={{ time() }}">

    @stack('theme-style')
    
    <link href="{{ asset('frontend/assets/css/extra.css') }}" rel="stylesheet">

    <!-- Responsive Css-->
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/responsive.css') }}">

    <!-- FAVICONS -->
    <link rel="icon" href="{{ getImageFile(get_option('app_fav_icon')) }}" type="image/png" sizes="16x16">
    <link rel="shortcut icon" href="{{ getImageFile(get_option('app_fav_icon')) }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ getImageFile(get_option('app_fav_icon')) }}">

    <link rel="apple-touch-icon-precomposed" type="image/x-icon" href="{{ getImageFile(get_option('app_fav_icon')) }}" sizes="72x72" >
    <link rel="apple-touch-icon-precomposed" type="image/x-icon" href="{{ getImageFile(get_option('app_fav_icon')) }}" sizes="114x114" >
    <link rel="apple-touch-icon-precomposed" type="image/x-icon" href="{{ getImageFile(get_option('app_fav_icon')) }}" sizes="144x144" >
    <link rel="apple-touch-icon-precomposed" type="image/x-icon" href="{{ getImageFile(get_option('app_fav_icon')) }}" >

    @stack('style')
    @toastr_css
    @include(getThemePath().'.layouts.dynamic-style')

    
    @if(get_option('pwa_enable'))
    <!-- PWA  -->
    <meta name="theme-color" content="{{ empty(get_option('app_theme_color')) ? '#5e3fd7' : get_option('app_theme_color') }}"/>
    <link rel="apple-touch-icon" href="{{ getImageFile(get_option('app_fav_icon')) }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    @endif
    @if(isEnableOpenAI())
    <link rel="stylesheet" href="{{asset('addon/AI/css/main.css')}}">
    @endif

    <script async src="https://www.googletagmanager.com/gtag/js?id={{ get_option('measurement_id') }}"></script>
    <script async>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', "{{ get_option('measurement_id') }}");
    </script>

    <!-- Enable native lazy loading -->
    <script>
        if ('loading' in HTMLImageElement.prototype) {
            const images = document.querySelectorAll("img[loading='lazy']");
            images.forEach(img => img.loading = "lazy");
        }
    </script>

    <link rel="stylesheet" href="{{ asset('frontend/assets/css/navbar-styles.css') }}">

<style>

   .main-content {
    margin-top: 280px;
}


        .resources-container {
                display: flex;
                flex-wrap: wrap;
                gap: 50px;
                padding: 5px;
                border: 1px solid black;
                border-radius: 10px;
                /* width: auto;
            min-width: 200px; */
                margin-bottom: 15px
            }

            #resourcesContainer {
                display: flex;
                flex-wrap: wrap;
                gap: 50px;
                padding: 5px;
                border: none;
                
                border-radius: 10px;
                margin-bottom: 15px
            }

           .resource-box {
                cursor: pointer;
                width: 12vw;
                height: 12vw;
                font-size: 20px;
                margin-right: 10px;
                margin-bottom: 10px;
                padding: 5px;
                border-radius: 5px;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .resource-box:hover {
                background-color: #f0f0f0;
            }

         .course-box {
                margin-right: 20px;
                cursor: pointer;
                padding: 10px;
                border-radius: 5px;
            }

            .back-btn {
            background: #89BD40;
            width: 3vw;
            height: 3vw;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .back-btn:hover {
            cursor: pointer;
        }


        .progress-bar {
    position: relative;
    width: 100px;
    height: 100px;
    background: conic-gradient(#4caf50 0% 0%, #e0e0e0 0% 100%);
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    transition: background 0.3s ease; /* Smooth animation */
}

.progress-bar span {
    position: absolute;
    font-size: 1rem;
    font-weight: bold;
    color: #000;
}














.range-value-wrap label {
    display: block;
    margin-bottom: 5px;
}

.price-range-field {
    padding: 5px;
    font-size: 14px;
}

.filterPrice {
    padding: 5px 10px;
    font-size: 14px;
    cursor: pointer;
}




.give-rating-star {
    font-size: 30px;
    cursor: pointer;
    color: gray; /* Default grey */
    transition: color 0.3s;
}

.give-rating-star.active {
    color: gold; /* Yellow when selected */
}


.give-rating-star span {
    font-size: 30px;
    cursor: pointer;
    color: gray !important; /* Force grey stars by default */
    transition: color 0.3s;
}


</style>


<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '858450995338517');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=858450995338517&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-NL3Q7M4L');</script>
<!-- End Google Tag Manager -->

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NL3Q7M4L"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->


</head>

@php
    $selectedLanguage = selectedLanguage();
@endphp

<body class="{{$selectedLanguage->rtl == 1 ? 'direction-rtl' : 'direction-ltr' }}">


@if(View::hasSection('content'))

    @if(get_option('allow_preloader') == 1)
    <!-- Pre Loader Area start -->
    <div id="preloader">
        <div id="preloader_status"><img src="{{getImageFile(get_option('app_preloader'))}}" alt="img" /></div>
    </div>
    <!-- Pre Loader Area End -->
    @endif
    <!--Main Menu/Navbar Area Start -->
    @if(!request()->routeIs('home') && !request()->routeIs('manager') && !request()->routeIs('forget-password'))
    @include(getThemePath().'.layouts.navbar')
    @endif
    <!--Main Menu/Navbar Area End -->

    <!-- Main Content Start-->
    <!-- In app.blade.php -->
 
        @yield('content')
   
    {{-- @yield('content') --}}
    <!-- Main Content End-->

    <!-- Footer Start -->
    @if(!request()->routeIs('home') && !request()->routeIs('manager') && !request()->routeIs('forget-password'))
    @include(getThemePath().'.layouts.footer')
    @endif
    <!-- Footer End -->
@endif

@if(View::hasSection('content2'))

    @if(get_option('allow_preloader') == 1)
    <!-- Pre Loader Area start -->
    <div id="preloader">
        <div id="preloader_status"><img src="{{getImageFile(get_option('app_preloader'))}}" alt="img" /></div>
    </div>
    <!-- Pre Loader Area End -->
    @endif
    @yield('content2')
@endif
@if(View::hasSection('content3'))
    @yield('content3')
@endif

<!-- PWA Install Button Start -->
<button class="d-none pwa-install-btn bg-white position-fixed radius-4" id="installApp">
    <span class="iconify" data-icon="logos:pwa"></span>
</button>
<!-- PWA Install Button End -->

<script async>
    var deleteTitle = '{{ __("Sure! You want to delete?") }}';
    var deleteText = '{{ __("You wont be able to revert this!") }}';
    var deleteConfirmButton = '{{ __("Yes, Delete It!") }}';
    var deleteSuccessText = '{{ __("Item has been deleted") }}';
</script>
<!--=======================================
    All Jquery Script link
===========================================-->
<!-- Bootstrap core JavaScript -->
<script src="{{ asset('frontend/assets/vendor/jquery/jquery-3.6.0.min.js') }}"></script>
<script async src="{{ asset('frontend/assets/vendor/jquery/popper.min.js') }}"></script>
<script async src="{{ asset('frontend/assets/vendor/bootstrap/js/bootstrap.min.js') }}"></script>

<!-- ==== Plugin JavaScript ==== -->
<script async src="{{ asset('frontend/assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>


<!--WayPoints JS Script-->
<script async src="{{ asset('frontend/assets/js/waypoints.min.js') }}"></script>

<!--Counter Up JS Script-->
<script async src="{{ asset('frontend/assets/js/jquery.counterup.min.js') }}"></script>


<!--Feather Icon-->
<script async src="{{ asset('frontend/assets/js/feather.min.js') }}"></script>

<!--Iconify Icon-->
<script async src="{{ asset('common/js/iconify.min.js') }}"></script>

<!-- Menu js -->
<script async src="{{ asset('frontend/assets/js/menu.js') }}"></script>

<!-- Custom scripts for this template -->
<script async src="{{ asset('frontend/assets/js/frontend-custom.js') }}"></script>


<script async src="{{asset('admin/sweetalert2/sweetalert2.all.js')}}"></script>
<input type="hidden" id="base_url" value="{{url('/')}}">
<!-- Start:: Navbar Search  -->
<input type="hidden" class="search_route" value="{{ route('search-course.list') }}">
<script async src="{{ asset('frontend/assets/js/custom/search-course.js') }}"></script>
<!-- End:: Navbar Search  -->
<script>
    






    function getLanguage(){
        return {
            "sEmptyTable": "{{ __('No data available in table') }}",
            "sInfo": "{{__('Showing _START_ To _END_ Of _TOTAL_ Entries')}}",
            "sInfoEmpty": "{{__('Showing 0 to 0 of 0 entries')}}",
            "sInfoFiltered": "{{__('(filtered from _MAX_ total entries)')}}",
            "sInfoPostFix": "",
            "sInfoThousands": ",",
            "sLengthMenu": "{{__('Show _MENU_ entries')}}",
            "sLoadingRecords": "{{__('Loading...')}}",
            "sProcessing": "{{__('Processing...')}}",
            "sSearch": "{{__('Search:')}}",
            "sZeroRecords": "{{__('No matching records found')}}",
            "oPaginate": {
                "sFirst": "{{__('First')}}",
                "sLast": "{{__('Last')}}",
                "sNext": "{{__('Next')}}",
                "sPrevious": "{{__('Previous')}}"
            },
            "oAria": {
                "sSortAscending": ": {{__('activate to sort column ascending')}}",
                "sSortDescending": ": {{__('activate to sort column descending')}}"
            }
        };
    }
</script>
    <script src="https://unpkg.com/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@if(isEnableOpenAI())
<script async src="{{asset('addon/AI/js/main.js')}}"></script>
@endif

@stack('script')

@toastr_js
@toastr_render

@if (@$errors->any())
    <script async>
        "use strict";
        @foreach ($errors->all() as $error)
        toastr.options.positionClass = 'toast-bottom-right';
        toastr.error("{{ $error }}")
        @endforeach
    </script>
@endif

@if(get_option('pwa_enable'))
<script async src="{{ asset('sw.js') }}"></script>
<script async>
    if (!navigator.serviceWorker.controller) {
        navigator.serviceWorker.register("/sw.js").then(function (reg) {
            console.log("Service worker has been registered for scope: " + reg.scope);
        });
    }

    let deferredPrompt;
    window.addEventListener('beforeinstallprompt', (e) => {
        $('#installApp').removeClass('d-none');
        deferredPrompt = e;
    });

    const installApp = document.getElementById('installApp');
    installApp.addEventListener('click', async () => {
        if (deferredPrompt !== null) {
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            if (outcome === 'accepted') {
                deferredPrompt = null;
            }
        }
    });




 
</script>
@endif

<script async src="{{ asset('frontend/assets/js/navbar-scroll.js') }}"></script>
</body>

</html>

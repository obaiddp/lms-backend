<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="<?php echo e(get_option('app_copyright')); ?>">
    <meta name="robots" content="index, follow">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" >
    <meta name="msapplication-TileImage" content="<?php echo e(getImageFile(get_option('app_logo'))); ?>">
    <meta name="msapplication-TileColor" content="rgba(103, 20, 222,.55)">
    <meta name="theme-color" content="#754FFE">

    <?php if (! empty(trim($__env->yieldContent('meta')))): ?>
        <?php echo $__env->yieldContent('meta'); ?>
    <?php else: ?>
    <?php
        $defaultMeta = getMeta('default');
    ?>

    <meta name="description" content="<?php echo e($defaultMeta['meta_description']); ?>">
    <meta name="keywords" content="<?php echo e($defaultMeta['meta_keyword']); ?>">
    
    <!-- Open Graph meta tags for social sharing -->
    <meta property="og:type" content="Learning">
    <meta property="og:title" content="<?php echo e($defaultMeta['meta_title']); ?>">
    <meta property="og:description" content="<?php echo e($defaultMeta['meta_description']); ?>">
    <meta property="og:image" content="<?php echo e($defaultMeta['og_image']); ?>">
    <meta property="og:url" content="<?php echo e(url()->current()); ?>">

    <meta property="og:site_name" content="<?php echo e(get_option('app_name')); ?>">
    
    <!-- Twitter Card meta tags for Twitter sharing -->
    <meta name="twitter:card" content="Learning">
    <meta name="twitter:title" content="<?php echo e($defaultMeta['meta_title']); ?>">
    <meta name="twitter:description" content="<?php echo e($defaultMeta['meta_description']); ?>">
    <meta name="twitter:image" content="<?php echo e($defaultMeta['og_image']); ?>">
    <?php endif; ?>  

    <title><?php echo e(get_option('app_name')); ?> - <?php echo e(__(@$pageTitle)); ?></title>
    <script src="<?php echo e(asset('js/bundle.js')); ?>" defer></script>
    <!--=======================================
      All Css Style link
    ===========================================-->

    <!-- Bootstrap core CSS -->
   
    <link href="<?php echo e(asset('frontend/assets/vendor/bootstrap/css/bootstrap.min.css')); ?>" rel="stylesheet">

    <!-- Font Awesome for this template -->
    <link href="<?php echo e(asset('frontend/assets/vendor/fontawesome-free/css/all.min.css')); ?>" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="<?php echo e(asset('frontend/assets/fonts/feather/feather.css')); ?>">

    <!-- Sweet Alert css -->
    <link rel="stylesheet" href="<?php echo e(asset('admin/sweetalert2/sweetalert2.css')); ?>">

    <!-- Custom styles for this template -->
    <link rel="stylesheet" href="<?php echo e(asset('frontend/assets/css/style.css')); ?>?v=<?php echo e(time()); ?>">

    <?php echo $__env->yieldPushContent('theme-style'); ?>
    
    <link href="<?php echo e(asset('frontend/assets/css/extra.css')); ?>" rel="stylesheet">

    <!-- Responsive Css-->
    <link rel="stylesheet" href="<?php echo e(asset('frontend/assets/css/responsive.css')); ?>">

    <!-- FAVICONS -->
    <link rel="icon" href="<?php echo e(getImageFile(get_option('app_fav_icon'))); ?>" type="image/png" sizes="16x16">
    <link rel="shortcut icon" href="<?php echo e(getImageFile(get_option('app_fav_icon'))); ?>" type="image/x-icon">
    <link rel="shortcut icon" href="<?php echo e(getImageFile(get_option('app_fav_icon'))); ?>">

    <link rel="apple-touch-icon-precomposed" type="image/x-icon" href="<?php echo e(getImageFile(get_option('app_fav_icon'))); ?>" sizes="72x72" >
    <link rel="apple-touch-icon-precomposed" type="image/x-icon" href="<?php echo e(getImageFile(get_option('app_fav_icon'))); ?>" sizes="114x114" >
    <link rel="apple-touch-icon-precomposed" type="image/x-icon" href="<?php echo e(getImageFile(get_option('app_fav_icon'))); ?>" sizes="144x144" >
    <link rel="apple-touch-icon-precomposed" type="image/x-icon" href="<?php echo e(getImageFile(get_option('app_fav_icon'))); ?>" >

    <?php echo $__env->yieldPushContent('style'); ?>
    <?php echo toastr_css(); ?>
    <?php echo $__env->make(getThemePath().'.layouts.dynamic-style', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php if(get_option('pwa_enable')): ?>
    <!-- PWA  -->
    <meta name="theme-color" content="<?php echo e(empty(get_option('app_theme_color')) ? '#5e3fd7' : get_option('app_theme_color')); ?>"/>
    <link rel="apple-touch-icon" href="<?php echo e(getImageFile(get_option('app_fav_icon'))); ?>">
    <link rel="manifest" href="<?php echo e(asset('manifest.json')); ?>">
    <?php endif; ?>
    <?php if(isEnableOpenAI()): ?>
    <link rel="stylesheet" href="<?php echo e(asset('addon/AI/css/main.css')); ?>">
    <?php endif; ?>

    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo e(get_option('measurement_id')); ?>"></script>
    <script async>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', "<?php echo e(get_option('measurement_id')); ?>");
    </script>

    <!-- Enable native lazy loading -->
    <script>
        if ('loading' in HTMLImageElement.prototype) {
            const images = document.querySelectorAll("img[loading='lazy']");
            images.forEach(img => img.loading = "lazy");
        }
    </script>

    <link rel="stylesheet" href="<?php echo e(asset('frontend/assets/css/navbar-styles.css')); ?>">

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

<?php
    $selectedLanguage = selectedLanguage();
?>

<body class="<?php echo e($selectedLanguage->rtl == 1 ? 'direction-rtl' : 'direction-ltr'); ?>">


<?php if(View::hasSection('content')): ?>

    <?php if(get_option('allow_preloader') == 1): ?>
    <!-- Pre Loader Area start -->
    <div id="preloader">
        <div id="preloader_status"><img src="<?php echo e(getImageFile(get_option('app_preloader'))); ?>" alt="img" /></div>
    </div>
    <!-- Pre Loader Area End -->
    <?php endif; ?>
    <!--Main Menu/Navbar Area Start -->
    <?php if(!request()->routeIs('home') && !request()->routeIs('manager') && !request()->routeIs('forget-password')): ?>
    <?php echo $__env->make(getThemePath().'.layouts.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
    <!--Main Menu/Navbar Area End -->

    <!-- Main Content Start-->
    <!-- In app.blade.php -->
 
        <?php echo $__env->yieldContent('content'); ?>
   
    
    <!-- Main Content End-->

    <!-- Footer Start -->
    <?php if(!request()->routeIs('home') && !request()->routeIs('manager') && !request()->routeIs('forget-password')): ?>
    <?php echo $__env->make(getThemePath().'.layouts.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
    <!-- Footer End -->
<?php endif; ?>

<?php if(View::hasSection('content2')): ?>

    <?php if(get_option('allow_preloader') == 1): ?>
    <!-- Pre Loader Area start -->
    <div id="preloader">
        <div id="preloader_status"><img src="<?php echo e(getImageFile(get_option('app_preloader'))); ?>" alt="img" /></div>
    </div>
    <!-- Pre Loader Area End -->
    <?php endif; ?>
    <?php echo $__env->yieldContent('content2'); ?>
<?php endif; ?>
<?php if(View::hasSection('content3')): ?>
    <?php echo $__env->yieldContent('content3'); ?>
<?php endif; ?>

<!-- PWA Install Button Start -->
<button class="d-none pwa-install-btn bg-white position-fixed radius-4" id="installApp">
    <span class="iconify" data-icon="logos:pwa"></span>
</button>
<!-- PWA Install Button End -->

<script async>
    var deleteTitle = '<?php echo e(__("Sure! You want to delete?")); ?>';
    var deleteText = '<?php echo e(__("You wont be able to revert this!")); ?>';
    var deleteConfirmButton = '<?php echo e(__("Yes, Delete It!")); ?>';
    var deleteSuccessText = '<?php echo e(__("Item has been deleted")); ?>';
</script>
<!--=======================================
    All Jquery Script link
===========================================-->
<!-- Bootstrap core JavaScript -->
<script src="<?php echo e(asset('frontend/assets/vendor/jquery/jquery-3.6.0.min.js')); ?>"></script>
<script async src="<?php echo e(asset('frontend/assets/vendor/jquery/popper.min.js')); ?>"></script>
<script async src="<?php echo e(asset('frontend/assets/vendor/bootstrap/js/bootstrap.min.js')); ?>"></script>

<!-- ==== Plugin JavaScript ==== -->
<script async src="<?php echo e(asset('frontend/assets/vendor/jquery-easing/jquery.easing.min.js')); ?>"></script>


<!--WayPoints JS Script-->
<script async src="<?php echo e(asset('frontend/assets/js/waypoints.min.js')); ?>"></script>

<!--Counter Up JS Script-->
<script async src="<?php echo e(asset('frontend/assets/js/jquery.counterup.min.js')); ?>"></script>


<!--Feather Icon-->
<script async src="<?php echo e(asset('frontend/assets/js/feather.min.js')); ?>"></script>

<!--Iconify Icon-->
<script async src="<?php echo e(asset('common/js/iconify.min.js')); ?>"></script>

<!-- Menu js -->
<script async src="<?php echo e(asset('frontend/assets/js/menu.js')); ?>"></script>

<!-- Custom scripts for this template -->
<script async src="<?php echo e(asset('frontend/assets/js/frontend-custom.js')); ?>"></script>


<script async src="<?php echo e(asset('admin/sweetalert2/sweetalert2.all.js')); ?>"></script>
<input type="hidden" id="base_url" value="<?php echo e(url('/')); ?>">
<!-- Start:: Navbar Search  -->
<input type="hidden" class="search_route" value="<?php echo e(route('search-course.list')); ?>">
<script async src="<?php echo e(asset('frontend/assets/js/custom/search-course.js')); ?>"></script>
<!-- End:: Navbar Search  -->
<script>
    






    function getLanguage(){
        return {
            "sEmptyTable": "<?php echo e(__('No data available in table')); ?>",
            "sInfo": "<?php echo e(__('Showing _START_ To _END_ Of _TOTAL_ Entries')); ?>",
            "sInfoEmpty": "<?php echo e(__('Showing 0 to 0 of 0 entries')); ?>",
            "sInfoFiltered": "<?php echo e(__('(filtered from _MAX_ total entries)')); ?>",
            "sInfoPostFix": "",
            "sInfoThousands": ",",
            "sLengthMenu": "<?php echo e(__('Show _MENU_ entries')); ?>",
            "sLoadingRecords": "<?php echo e(__('Loading...')); ?>",
            "sProcessing": "<?php echo e(__('Processing...')); ?>",
            "sSearch": "<?php echo e(__('Search:')); ?>",
            "sZeroRecords": "<?php echo e(__('No matching records found')); ?>",
            "oPaginate": {
                "sFirst": "<?php echo e(__('First')); ?>",
                "sLast": "<?php echo e(__('Last')); ?>",
                "sNext": "<?php echo e(__('Next')); ?>",
                "sPrevious": "<?php echo e(__('Previous')); ?>"
            },
            "oAria": {
                "sSortAscending": ": <?php echo e(__('activate to sort column ascending')); ?>",
                "sSortDescending": ": <?php echo e(__('activate to sort column descending')); ?>"
            }
        };
    }
</script>
    <script src="https://unpkg.com/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<?php if(isEnableOpenAI()): ?>
<script async src="<?php echo e(asset('addon/AI/js/main.js')); ?>"></script>
<?php endif; ?>

<?php echo $__env->yieldPushContent('script'); ?>

<?php echo toastr_js(); ?>
<?php echo app('toastr')->render(); ?>

<?php if(@$errors->any()): ?>
    <script async>
        "use strict";
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        toastr.options.positionClass = 'toast-bottom-right';
        toastr.error("<?php echo e($error); ?>")
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </script>
<?php endif; ?>

<?php if(get_option('pwa_enable')): ?>
<script async src="<?php echo e(asset('sw.js')); ?>"></script>
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
<?php endif; ?>

<script async src="<?php echo e(asset('frontend/assets/js/navbar-scroll.js')); ?>"></script>
</body>

</html>
<?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/frontend/layouts/app.blade.php ENDPATH**/ ?>
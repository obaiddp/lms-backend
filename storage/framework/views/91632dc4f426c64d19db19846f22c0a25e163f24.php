<?php $__env->startSection('meta'); ?>
    <?php
        $metaData = getMeta('login');
    ?>

    <meta name="description" content="<?php echo e($metaData['meta_description']); ?>">
    <meta name="keywords" content="<?php echo e($metaData['meta_keyword']); ?>">

    <!-- Open Graph meta tags for social sharing -->
    <meta property="og:type" content="Learning">
    <meta property="og:title" content="<?php echo e($metaData['meta_title']); ?>">
    <meta property="og:description" content="<?php echo e($metaData['meta_description']); ?>">
    <meta property="og:image" content="<?php echo e($metaData['og_image']); ?>">
    <meta property="og:url" content="<?php echo e(url()->current()); ?>">

    <meta property="og:site_name" content="<?php echo e(get_option('app_name')); ?>">

    <!-- Twitter Card meta tags for Twitter sharing -->
    <meta name="twitter:card" content="Learning">
    <meta name="twitter:title" content="<?php echo e($metaData['meta_title']); ?>">
    <meta name="twitter:description" content="<?php echo e($metaData['meta_description']); ?>">
    <meta name="twitter:image" content="<?php echo e($metaData['og_image']); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="loginPage <?php if(isset($fromAdmin) && $fromAdmin): ?> loginPage--admin <?php endif; ?>">
    <?php if(!isset($fromAdmin) || !$fromAdmin): ?>
    <!-- Page Header Start -->
    <header class="breadcrumb-section px-3 xl:px-0">
        <div class="home-page-container d-flex justify-content-between align-items-center">
            <nav aria-label="breadcrumb" class="home-page-container">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item">
                        <a href="<?php echo e(route('home')); ?>" class="aileron">
                            <i class="fas fa-home"></i> <!-- Home icon -->
                            Home
                        </a>
                    </li>
                    <li class="breadcrumb-separator">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.5 2.25L8.25 6L4.5 9.75" stroke="#77878F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </li> 
                    <!-- Custom separator -->
                    <li class="breadcrumb-item active aileron" aria-current="page">Login</li>
                </ol>
             </nav>
            <?php if(auth()->guard()->guest()): ?>
                <?php if($type == null): ?>
                    <a href="<?php echo e(route('sign-up-as-student')); ?>" class="btn btn-sm btn-login aileron d-flex align-items-center">
                        <i class="fas fa-sign-in-alt me-2"></i> Don,t have an account? Sign Up
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('sign-up-as-student',['type'=> $type, 'slug'=> $slug])); ?>" class="btn btn-sm btn-login aileron d-flex align-items-center">
                        <i class="fas fa-sign-in-alt me-2"></i> Don,t have an account? Sign Up
                    </a>
                <?php endif; ?>
             <?php endif; ?>
        </div>
    </header>
    <!-- Page Header End -->
    <?php endif; ?>
    <section class="bg-white">
        <div class="home-page-container d-flex justify-content-center align-items-center">
        <div class="row bg-white justify-content-start align-items-center mt-4 px-3 xl:px-0" style="width: 100%;">
            <div class="col-md-6 p-0 ps-2.5">
                <div class="mb-1">
                    <h1 class="fs-5 text-black font-bold aileron">Welcome to CEF</h1>
                    <p style="font-size: 14px" class="aileron grayNew">Please enter your credentials</p>
                </div>

                <?php if($type == null): ?>
                    
                <?php endif; ?>
                <br>
                <form method="POST" action="<?php echo e(route('login')); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="fromAdmin" id="fromAdmin" value=<?php echo e($fromAdmin); ?>>
                 
                     <input type="hidden" name="slug_updated" id="slug_updated" value=<?php echo e($slug); ?>>
                       <input type="hidden" name="st_type_updated" id="st_type_updated" value=<?php echo e($type); ?>>
                    <div class="mb-3">
                        <label for="email" class="mb-1 text-sm text-black font-bold aileron">Email or Phone</label>
                        <input type="text" name="email" class="form-control aileron" id="email" 
                            placeholder="Enter your email or phone" required value="<?php echo e(old('email')); ?>">
                        <?php if($errors->has('email')): ?>
                            <span class="text-danger aileron"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('email')); ?></span>
                        <?php endif; ?>
                    </div>
                    

                    <div class="mb-3">
                        <label for="password" class="mb-1 text-sm text-black font-bold aileron">Password</label>
                        <div class="position-relative">
                            <input type="password" name="password" class="form-control aileron pe-5 password" id="password" placeholder="*********" required>
                            <i class="position-absolute end-0 top-50 translate-middle-y me-3" id="togglePassword" style="cursor: pointer;">
                                <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_800_1128)">
                                <path d="M15.8667 8.15202C15.78 7.91936 13.6667 2.46002 8 2.46002C2.33333 2.46002 0.217333 7.91936 0.133333 8.15202L0 8.50002L0.133333 8.84802C0.22 9.08069 2.33333 14.54 8 14.54C13.6667 14.54 15.7827 9.08069 15.8667 8.84802L16 8.50002L15.8667 8.15202ZM8 12.5567C4.388 12.5567 2.63933 9.54736 2.14 8.50002C2.64067 7.45002 4.39 4.44336 8 4.44336C11.61 4.44336 13.3587 7.44936 13.86 8.50002C13.3587 9.55069 11.61 12.5567 8 12.5567Z" fill="#013370"/>
                                <path d="M7.99967 11.1667C9.47243 11.1667 10.6663 9.9728 10.6663 8.50004C10.6663 7.02728 9.47243 5.83337 7.99967 5.83337C6.52692 5.83337 5.33301 7.02728 5.33301 8.50004C5.33301 9.9728 6.52692 11.1667 7.99967 11.1667Z" fill="#013370"/>
                                </g>
                                <defs>
                                <clipPath id="clip0_800_1128">
                                <rect width="16" height="16" fill="white" transform="translate(0 0.5)"/>
                                </clipPath>
                                </defs>
                                </svg>
                            </i>
                        </div>
                        <?php if($errors->has('password')): ?>
                            <span class="text-danger aileron"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('password')); ?></span>
                        <?php endif; ?>
                    </div>
                    

                    <button type="submit" class="theme-btn aileron theme-button1 theme-button3 w-100 font-14 !rounded !fw-light">Login</button>
                    <a href="<?php echo e(route('forget-password')); ?>" class="aileron text-decoration-underline d-block text-center mb-3">Forgot Password</a>
                </form>
                
                
                <div class="d-flex justify-content-center gap-3">
                            
                        
                            
                        
                            
                </div>
                
            </div>
        
            <div class="col-md-6 rounded-4 p-0 md:!ps-5 pe-2.5">
                <div class="swiper mySwiper rounded-4">
                    <div class="swiper-wrapper rounded-4">
                    <div class="swiper-slide">
                        <img src="<?php echo e(asset('Images/signin-1.webp')); ?>" class="rounded-4 w-100 h-90"/>
                    </div>
                    <div class="swiper-slide">
                        <img src="<?php echo e(asset('Images/signin-2.webp')); ?>" class="rounded-4 w-100 h-90" />
                    </div>
                    <div class="swiper-slide">
                        <img src="<?php echo e(asset('Images/signin-3.webp')); ?>" class="rounded-4 w-100 h-90" />
                    </div>
                    <div class="swiper-slide">
                        <img src="<?php echo e(asset('Images/signin-4.webp')); ?>" class="rounded-4 w-100 h-90" />
                    </div>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
            </div>
        </div>
        
    </section>
</div>
<?php $__env->stopSection(); ?>
<style>
    .loginPage {
        background: #f8f9fa;
        padding-top: 70px !important;
    }
    .loginPage--admin {
        padding-top: 1rem !important;
    }
    .breadcrumb-section {
        background: #F0F5F2 !important;
        padding-top: 25px !important;
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
        margin: 0 8px; /* Adjust spacing as needed */
        color: #6c757d; /* Adjust color as needed */
    }

    .breadcrumb-item.active {
        color: #6c757d; /* Adjust color for the active item */
    }

    .fas.fa-home {
        margin-right: 5px; /* Space between icon and text */
    }

    .btn-group .btn {
        border: 1px solid #d3d3d3; /* Light gray border */
        padding: 10px 0;
    }

    .active-tab {
        background-color: white;    
        color: black;   
        box-shadow: 0 4px 8px #f0eff2;           
        border: 2px solid #f0eff2 !important;  
        z-index: 1; /* Bring the active tab to the front if necessary */
    }

    .inactive-tab {
        background-color: #f0eff2 !important;  
        color: black !important;                
        border: 1px solid #f0eff2  !important;
        opacity: 40%; 
    }

    .my-tab {
        height: 2.2rem !important;
        padding: 0 !important;
        font-size: 0.8rem !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        box-shadow: none !important;
    }

    .btn-group .btn:first-child {
        border-right: none; /* Remove the border between the two buttons */
    }

    .form-control::placeholder {
            color: #999999 !important;
    font-size: 0.9rem !important;
    height: 2.8rem !important;
    }

    .form-control {
            color: #999999 !important;
    font-size: 0.9rem !important;
    height: 2.8rem !important;
    }

    .swiper.mySwiper.rounded-4.swiper-initialized.swiper-horizontal.swiper-backface-hidden {
    height: 35rem;
}

@media (min-width: 300px) and (max-width: 640px){
    .loginPage {
    padding-top: 4.4rem !important;
    }
    .loginPage--admin {
        padding-top: 1rem !important;
    }
    .swiper.mySwiper.rounded-4.swiper-initialized.swiper-horizontal.swiper-backface-hidden{
        height: 20rem;
        margin-top: 4rem;
    }
}

@media (min-width: 531px) and (max-width: 767px){
    .loginPage {
        padding-top: 5.4rem !important;
    }
    .loginPage--admin {
        padding-top: 1rem !important;
    }
    .swiper.mySwiper.rounded-4.swiper-initialized.swiper-horizontal.swiper-backface-hidden{
        height: 34rem;
        margin-top: 4rem;
    }
}

@media (min-width: 768px) and (max-width: 992px){
    .swiper.mySwiper.rounded-4.swiper-initialized.swiper-horizontal.swiper-backface-hidden {
        height: 22rem;
    }
}

@media (min-width: 993px) and (max-width: 1199px){
    .swiper.mySwiper.rounded-4.swiper-initialized.swiper-horizontal.swiper-backface-hidden {
        height: 28rem;
    }
}

</style>

<script>
    // Toggle Password Visibility
    document.addEventListener('DOMContentLoaded', function () {
    const togglePassword = document.querySelector('#togglePassword');
    const passwordField = document.querySelector('#password');

    togglePassword.addEventListener('click', function () {
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        this.classList.toggle('fa-eye-slash'); // Toggle between eye and eye-slash icons
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var swiper = new Swiper(".mySwiper", {
        pagination: {
            el: ".swiper-pagination",
        },
        slidesPerView: 1,
        spaceBetween: 30,
        loop: true,
        autoplay: {
            delay: 2500,
            disableOnInteraction: false,
        }
    });
});
</script>
<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/frontend/auth/login.blade.php ENDPATH**/ ?>
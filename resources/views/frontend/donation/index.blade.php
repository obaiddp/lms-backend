@extends('frontend.layouts.app')

@section('meta')
  
@php
$metaData = getMeta('donation');
@endphp

<meta name="description" content="{{ $metaData['meta_description'] }}">
<meta name="keywords" content="{{ $metaData['meta_keyword'] }}">

<!-- Open Graph meta tags for social sharing -->
<meta property="og:type" content="Learning">
<meta property="og:title" content="{{ $metaData['meta_title'] }}">
<meta property="og:description" content="{{ $metaData['meta_description'] }}">
<meta property="og:image" content="{{ $metaData['og_image'] }}">
<meta property="og:url" content="{{ url()->current() }}">

<meta property="og:site_name" content="{{ get_option('app_name') }}">

<!-- Twitter Card meta tags for Twitter sharing -->
<meta name="twitter:card" content="Learning">
<meta name="twitter:title" content="{{ $metaData['meta_title'] }}">
<meta name="twitter:description" content="{{ $metaData['meta_description'] }}">
<meta name="twitter:image" content="{{ $metaData['og_image'] }}">

@endsection

@section('content')
<section class="hero-section position-relative d-flex align-items-center justify-content-center text-center py-5" 
style="padding-top: 90px !important; height: 90vh !important;">
    <div class="container position-relative">
        <h1 class="text-black font-weight-bold mb-3 aileron" style="font-size: 64px; font-weight: 600;">
            Transform Lives with Character Education Foundation
        </h1>
        <p class="lead mb-4 text-center mx-auto aileron" style="width: 70% !important; font-size: 18px; font-weight: 500; color: #000000;">
            Unleash a Global Awakening: Inviting All Our Students to Donate to Character Education Foundation Scholarship Fund 
            and Empower Thousands with Divine Knowledge.
        </p>
        <div class="d-flex justify-content-center mt-4">
            <a href="#" class="custom-btn1 mx-2 aileron">Donate Now</a>
            <a href="#" class="custom-btn2 mx-2 d-flex align-items-center aileron">Become a Fundraiser</a>
            <img src="{{ asset('frontend/assets/img/donation/donation-arrow.png') }}" class="hero-arrow" alt="Arrow Down Icon">

        </div>
    </div>

    <!-- Background image with folded corner -->
    <img src="{{ asset('frontend/assets/img/donation/donation-hero-bg.png') }}" class="background-image position-absolute top-0 start-0 w-100 h-100" alt="Background Folded Image">
    <img src="{{ asset('frontend/assets/img/donation/donation-intersect.png') }}" class="corner-overlay position-absolute bottom-0 end-0" alt="Corner Fold Overlay">
</section>

<!-- Card Section with Blue Background and Lines Overlay -->
<section class="card-section position-relative" style="margin-top: -80px;">
    <div class="lines-overlay">
        <img src="{{ asset('frontend/assets/img/donation/lines.png') }}" alt="Decorative Lines Image" class="lines-image">
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="donation-card card-1">
                    <div class="hanger-icon">
                        <img src="{{ asset('frontend/assets/img/donation/hanger-icon.png') }}" alt="Hanger Icon">
                    </div>
                    <div class="card-content aileron">
                        <div class="per-month-tag">Per Month</div>
                        <h2 class="price-heading">PKR 5,000</h2>
                        <h3 class="title">Sponsor a Quranic Student</h3>
                        <p class="description">Imagine the reward of guiding a child toward a lifelong connection with the Quran! Your donation ensures a student can complete the Tajweed ul ...</p>
                        <a href="#" class="donate-btn">Donate Now</a>
                    </div>
                </div>
            </div>
        
            <div class="col-md-3">
                <div class="donation-card card-2">
                    <div class="hanger-icon">
                        <img src="{{ asset('frontend/assets/img/donation/hanger-icon.png') }}" alt="Hanger Icon">
                    </div>
                    <div class="card-content aileron">
                        <div class="per-month-tag">Per Month</div>
                        <h2 class="price-heading">PKR 5,000</h2>
                        <h3 class="title">Sponsor a Quranic Student</h3>
                        <p class="description">Imagine the reward of guiding a child toward a lifelong connection with the Quran! Your donation ensures a student can complete the Tajweed ul ...</p>
                        <a href="#" class="donate-btn">Donate Now</a>
                    </div>
                </div>
            </div>
        
            <div class="col-md-3">
                <div class="donation-card card-3">
                    <div class="hanger-icon">
                        <img src="{{ asset('frontend/assets/img/donation/hanger-icon.png') }}" alt="Hanger Icon">
                    </div>
                    <div class="card-content aileron">
                        <div class="per-month-tag">Per Month</div>
                        <h2 class="price-heading">PKR 10,000</h2>
                        <h3 class="title">Donate a Physical Library</h3>
                        <p class="description">
                            Teachers are the backbone of Islamic education. With your support, a qualified Quranic teacher can dedicate their time to nurturing young minds,
                        </p>
                        <a href="#" class="donate-btn">Donate Now</a>
                    </div>
                </div>
            </div>
        
            <div class="col-md-3">
                <div class="donation-card card-4">
                    <div class="hanger-icon">
                        <img src="{{ asset('frontend/assets/img/donation/hanger-icon.png') }}" alt="Hanger Icon">
                    </div>
                    <div class="card-content aileron">
                        <div class="per-month-tag">Per Month</div>
                        <h2 class="price-heading">Any Amount</h2>
                        <h3 class="title">CEF Online Academy</h3>
                        <p class="description">CEF Online is growing into a leading global platform for Quranic learning, character development,and Islamic education. With your support, we can...</p>
                        <a href="#" class="donate-btn">Donate Now</a>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</section>

<section id="new-section" style="height:30vh; z-index:-9999; position:relative; overflow:hidden;">
   
</section>

<!-- Donation Section -->
@include('frontend.donation.donation-section')


<!-- Support Section -->
@include('frontend.donation.support-section')

<!-- Carousel Section -->
@include('frontend.donation.carousel-section')

<!-- Achievements Section -->
@include('frontend.donation.achievements')

<!-- Transforming Lives Section -->
@include('frontend.donation.transformingLives-section')
@endsection


<style>
 .hero-section {
    overflow: hidden;
    position: relative;
    z-index: 10; /* Ensure it stays above the next section */
}

.background-image {
    border-bottom-right-radius: 80px;
    object-fit: cover;
    z-index: -1;
}

.corner-overlay {
    width: 100px;
    height: 100px;
    z-index: 20; /* Keep it above the next section */
    position: absolute;
    bottom: 0;
    right: 0;
}

.hero-arrow {
    position: absolute;
    left: 70%;
    top: 90%;
    transform: translateX(-50%);
    z-index: 1; /* Ensure it appears above other sections */
}

.card-section {
    background-color: #013370;
    padding: 80px 0; /* Add more padding to maintain consistent height */
    position: relative;
    
    z-index: 1;

}

.lines-overlay {
    position: absolute;
    top: 0;
    width: 100%;
    height: 100%;
    z-index: 1;  /* Behind the cards */
    display: flex;
    justify-content: center;
}

.lines-image {
    width: 100%;
    opacity: 0.7;
}

.card {
    position: relative;
    z-index: 2; /* Make sure cards are above the lines image */
}

.custom-btn1 {
    background-color: #8DC03E;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 18px;
    transition: background-color 0.3s ease;
}

.custom-btn2 {
    background-color: #013370;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 18px;
    transition: background-color 0.3s ease;
}



.donation-card {
    position: relative;
    width: 100%;
    max-width: 280px;
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    padding: 20px;
    overflow: visible;  /* Allow icon to show outside the card */
    margin: 0 auto;
    z-index: 2;  /* Ensure the card is above the background and lines */
}

.card-1 {
    top: 180;
    left: 0;
}

.card-2 {
    top: 120px;  /* Lower the card slightly */
    left: -10px;  /* Offset slightly to the left */
}

.card-3 {
    top: 180px;  /* Slightly move upward */
    left: -20px;  /* Move to the right */
}

.card-4 {
    top: 200px;  /* Lower more for a staggered effect */
    left: -20px;  /* Offset to the left */
}

.hanger-icon {
    position: absolute;
    top: -40px;  /* Keep it above the card */
    left: 50%;
    transform: translateX(-50%);
    z-index: 3;  /* Make sure it's above the blue background */
}

.hanger-icon img {
    width: 40px;
}

.per-month-tag {
    display: inline-block;
    background-color: rgba(1, 51, 112, 0.1); /* #0133701A */
    color: #013370;
    font-size: 14px;
    padding: 5px 10px;
    border-radius: 15px;
    margin-bottom: 10px;
}

.price-heading {
    color: #013370;
    font-size: 32px;
    font-weight: bold;
    margin-bottom: 10px;
}

.title {
    font-size: 17px;
    font-weight: bold;
    color: #000000;
    margin-bottom: 10px;
}

.description {
    font-size: 16px;
    line-height: 1.5;
    color: #4A4A4A;
    margin-bottom: 20px;
}

.donate-btn {
    display: inline-block;
    background-color: #8BC540; /* Green button color */
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    font-weight: bold;
    text-decoration: none;
}

@media (max-width: 768px) {
    .hero-section {
        padding-top: 70px !important;
        height: auto !important;
        text-align: center;
    }

    .hero-section h1 {
        font-size: 28px !important;
        padding: 0 15px;
    }

    .hero-section p {
        width: 100% !important;
        font-size: 16px;
        padding: 0 15px;
    }

    .custom-btn1,
    .custom-btn2 {
        padding: 6px 10px;
        font-size: 12px;
    }

    .hero-arrow {
        display: none; /* Hide on mobile to prevent awkward positioning */
    }
    .card-1 {
        margin-top: 90px !important;
    }
    .donation-card {
        top: 0 !important;
        left: 0 !important;
        margin: 50px auto;
    }

    .card-section {
        padding: 40px 15px;
    }

    .lines-image {
        display: none; /* optional: hide lines on smaller viewports */
    }

    .corner-overlay {
        width: 60px;
        height: 60px;
    }
}

#new-section {
    height: 30vh;
    background:#F0F5F2;
    position: relative;
    overflow: hidden;
}
</style>
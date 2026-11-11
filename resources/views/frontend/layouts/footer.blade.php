<script src="https://cdn.tailwindcss.com"></script>

<style>
    
.info-section{
    width: 100%;
    height: 14rem;
    margin-bottom: -10rem;
    z-index: 50;
    margin-top: 2rem;
}

.banner-homepage{
    width: 64rem;
    height: 8rem;
    margin: 0 auto;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    position: relative;
    background: linear-gradient(to right, #013370, #013370), url('{{ asset("Images/bg-image.png") }}');
    background-blend-mode: multiply;
    background-size: 100%;
    background-position: 0rem 32rem;
}

.footer-container{
    width: 64rem;
    margin: 0 auto;
}

.footer-top-part {
    padding-top: 8rem !important;
    padding-bottom: 4rem !important;
}

.banner-wrap{
    position: absolute;
    top: 1.56rem;
    left: 0;
    display: flex;
    width: 100%;
    align-items: center;
}

.banner-wrap-text{
    width: 80%;
    padding-inline: 2rem;
}

.banner-wrap-text h1{
    color: white;
    font-size: 2rem;
    font-weight: 600;
    font-family: 'Aileron';
}
.banner-wrap-text h2{
    color: white;
    font-family: 'Aileron';
    margin-top: 0.6rem;
}

.banner-wrap-btn{
    width: 20%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.banner-wrap-btn a{
    color: white;
    background-color: #8dc03e;
    padding: 0.75rem 1.25rem;
    font-weight: 600;
    border-radius: 0.5rem;
    margin: 1rem auto;
    font-size: 0.8rem;
}

.banner-wrap-text a:hover{
    color: white;
}

.container.banner-homepage svg {
    width: 80vw !important;
    margin: auto;
}

.footer-widget {
    margin-left: 3rem;
}

.footer-widget p{
    width: 90%;
}

.footer-links .second-ul{
    margin-left: 1rem;
}

@media (min-width: 300px) and (max-width: 530px){


    .banner-homepage{
        background-position: 0rem 28rem;
        width: 100%;
        height: 12rem;
    }

    .banner-wrap {
        flex-direction: column;
    }

    .banner-wrap-text {
        width: 100%;
        padding-inline: 1rem;
    }

    .banner-wrap-btn {
        width: 90%;
        top: 2.2rem;
    }

    .banner-wrap-btn a {
        width: 100% !important;
        text-align: center;
    }

    .container.banner-homepage svg rect {
        height: 44rem !important;
    }

    .info-section {
        height: 12rem;
        margin-bottom: -6rem;
    }

    .banner-wrap-text h1 {
        font-size: 1.4rem;
        text-align: center;
    }

    .banner-wrap-text h2 {
        text-align: center;
    }

    .footer-container {
        padding-inline: 0 !important;
        width: 100% !important;
    }

    .footer-contact-info {
        margin-left: 1rem !important;
    }

    .copyright-wrapper .justify-content-start {
        justify-content: center !important;
    }

    .copyright-wrapper{
        padding-bottom: 5rem;
        width: 100% !important;
    }

    .footer-top-part {
        width: 100% !important;
        padding-bottom: 0 !important;
    }
}

@media (min-width: 531px) and (max-width: 767px){
    .banner-homepage{
        background-position: 0rem 28rem;
        width: 100% !important;
        height: 12rem;
    }

    .banner-wrap {
        flex-direction: column;
    }

    .banner-wrap-text {
        width: 100%;
        padding-inline: 1rem;
    }

    .banner-wrap-btn {
        width: 90%;
        top: 2.2rem;
    }

    .banner-wrap-btn a {
        width: 100% !important;
        text-align: center;
    }

    .container.banner-homepage svg rect {
        height: 44rem !important;
    }

    .info-section {
        height: 12rem;
        margin-bottom: -6rem;
    }

    .banner-wrap-text h1 {
        font-size: 2rem;
        text-align: center;
    }

    .banner-wrap-text h2 {
        text-align: center;
        font-size: 1rem;
    }

    .footer-container {
        padding-inline: 0 !important;
        width: 100% !important;
    }


    .footer-contact-info {
        margin-left: 1rem !important;
    }

    .copyright-wrapper .justify-content-start,
    .copyright-wrapper .justify-content-end {
        justify-content: center !important;
    }

    .copyright-wrapper{
        padding-bottom: 5rem;
        width: 100% !important;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        gap: 0.8rem;
    }

    .footer-top-part {
        width: 100% !important;
        padding-bottom: 0 !important;
    }
}

@media (min-width: 768px) and (max-width: 992px){

    .banner-homepage{
        background-position: 0rem 28rem;
        width: 100% !important;
        height: 8rem;
    }

    .banner-wrap-text {
        width: 75%;
    }

    .copyright-wrapper .justify-content-start,
    .copyright-wrapper .justify-content-end {
        justify-content: center !important;
    }

    .copyright-wrapper{
        padding-bottom: 5rem;
        width: 100% !important;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        gap: 0.8rem;
    }
}

@media (min-width: 993px) and (max-width: 1199px){
    .banner-homepage{
        background-position: 0rem 28rem;
        width: 100% !important;
        height: 8rem;
    }
}

</style>

<section class="info-section">
    <div class="container mx-auto">
        <div class="banner-homepage">
            <div class="banner-wrap">
                <div class="banner-wrap-text">
                <h1>Need more information?</h1>
                <h2>Write your concern to us and our specialist will get back to you.</h2>
                </div>
                <div class="banner-wrap-btn">
                <a href="{{ route('contact') }}"
                    aria-current="page">
                    {{__('Contact Us')}}
                </a></script>
            </div>
        </div>
    </div>
    </div>
</section>
<footer class="footer-area footer-gradient-bg position-relative">
    <div class="section-overlay">
        <div class="container footer-container">
            <!-- footer-widget-area -->
            <div class="row footer-top-part">
                <div class="col-12 col-sm-12 col-md-6 col-lg-4">
                    <div class="footer-widget footer-about">
                        <img src="{{ asset(get_option('app_logo')) }}" alt="Logo">
                        <p>{{ __(get_option('footer_quote')) }}</p>
                        <!-- <div class="footer-social mt-30">
                            <ul class="d-flex align-items-center">
                                @if (get_option('facebook_url'))
                                    <li><a href="{{ get_option('facebook_url') }}"><span class="iconify"
                                                data-icon="ant-design:facebook-filled"></span></a></li>
                                @endif
                                @if (get_option('twitter_url'))
                                    <li><a href="{{ get_option('twitter_url') }}"><span class="iconify"
                                                data-icon="ant-design:twitter-square-filled"></span></a></li>
                                @endif
                                @if (get_option('linkedin_url'))
                                    <li><a href="{{ get_option('linkedin_url') }}"><span class="iconify"
                                                data-icon="ant-design:linkedin-filled"></span></a></li>
                                @endif
                                @if (get_option('pinterest_url'))
                                    <li><a href="{{ get_option('pinterest_url') }}"><span class="iconify"
                                                data-icon="fa-brands:pinterest-square" data-width="1em"
                                                data-height="1em"></span></a></li>
                                @endif
                                @if (get_option('instagram_url'))
                                    <li><a href="{{ get_option('instagram_url') }}"><span class="iconify"
                                                data-icon="fa-brands:instagram-square" data-width="1em"
                                                data-height="1em"></span></a></li>
                                @endif
                                @if (get_option('tiktok_url'))
                                    <li><a href="{{ get_option('tiktok_url') }}"><span class="iconify"
                                                data-icon="iconoir:tiktok" data-width="1em"
                                                data-height="1em"></span></a></li>
                                @endif
                            </ul>
                        </div> -->
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6 col-lg-4">
                    <div class="footer-widget">
                        <h6 class="footer-widget-title -ms-3">{{ __('Company') }}</h6>
                        <div class="footer-links d-flex">
                            <ul>
                                @php
                                    $totalMenus = count($footerLeftMenus ?? []);
                                    $firstHalfCount = ceil($totalMenus / 2);
                                    if ($totalMenus % 2 != 0) {
                                        $firstHalfCount++;
                                    }
                                @endphp
                                {{-- @foreach (($footerLeftMenus ?? []) as $key => $footerLeftMenu)
                                    @if ($key < $firstHalfCount)
                                    <li><a href="{{ $footerLeftMenu->url }}">{{ __($footerLeftMenu->name) }}</a></li>
                                    @endif
                                @endforeach --}}
                                <li><a href="{{ route('about') }}">{{ __('About')  }}</a></li>
                                <li><a href="{{ route('faq') }}">{{ __('FAQ')  }}</a></li>
                                <li><a href="{{ route('blogs') }}">{{ __('Blogs')  }}</a></li>
                             
                                {{-- <li><a href="{{ route('cookie-policy') }}">{{ __('Cookie Policy')  }}</a></li> --}}


                                {{-- <li><a href="{{ route('about') }}">{{ __('About')  }}</a></li>
                                <li><a href="{{ route('faq') }}">{{__('FAQ')}}</a></li>
                                <li><a href="{{ route('blogs') }}">{{ __('Blogs') }}</a></li> --}}
                            </ul>
                            @if (checkPrivacyPolicy() || checkRefundPolicy() || checkTermsPolicy())
                                <ul class="second-ul">
                                    {{-- @foreach (($footerLeftMenus ?? []) as $key => $footerLeftMenu)
                                        @if ($key >= $firstHalfCount)
                                        <li><a href="{{ $footerLeftMenu->url }}">{{ __($footerLeftMenu->name) }}</a></li>
                                        @endif
                                    @endforeach --}}
                                    @if (checkPrivacyPolicy())
                                        <li><a href="{{ route('privacy-policy') }}">{{ __('Privacy Policy')  }}</a></li>
                                    @endif
                                    @if (checkRefundPolicy())
                                           <li><a href="{{ route('refund-policy') }}">{{ __('Refund Policy')  }}</a></li>
                                    @endif
                                
                                    @if (checkTermsPolicy())
                                        <li><a href="{{ route('terms-conditions') }}">{{ __('Terms Conditions')  }}</a></li>
                                    @endif
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6 col-lg-1">
                    <div class="footer-widget lg:!ms-0">
                        <h6 class="footer-widget-title -ms-3">{{ __('Support') }}</h6>
                        <div class="footer-links d-flex">
                            <ul>
                                {{-- @foreach ($footerRightMenus ?? [] as $footerRightMenu)
                                <li><a href="{{ $footerRightMenu->url }}">{{ __($footerRightMenu->name) }}</a>
                                </li>
                                @endforeach --}}
                                <li><a href="{{ route('contact') }}">{{ __('Contact')  }}</a></li>
                                <li><a href="{{ route('support-ticket-faq') }}">{{ __('Support')  }}</a></li>
                                @if (!get_option('private_mode') || !auth()->guest())
                                <li><a href="{{ route('courses') }}">{{ __('Courses')  }}</a></li>
                                {{-- <li><a href="{{ route('manager') }}">{{ __('Manager')  }}</a></li> --}}
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6 col-lg-3">
                    <div class="footer-widget footer-contact-info">
                        <h6 class="footer-widget-title">{{ __('Contact Info') }}</h6>

                        <div class="footer-links d-flex icons">
                            <ul>
                                <li><span class="iconify"
                                        data-icon="carbon:location-filled"></span><span>{{ __(get_option('app_location')) }}</span>
                                </li>
                                <li><span class="iconify"
                                        data-icon="fluent-emoji-high-contrast:telephone-receiver"></span><a
                                        href="tel:{{ get_option('app_contact_number') }}">{{ __(get_option('app_contact_number')) }}</a>
                                </li>
                                <li><span class="iconify" data-icon="ic:round-email"></span><a
                                        href="mailto:{{ get_option('app_email') }}">{{ __(get_option('app_email')) }}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!--copyright-text-->
            <div class="row copyright-wrapper">
                <div class="col-12 col-md-12 col-lg-12 d-flex justify-content-center ">
                    <div class="copyright-text text-center">
                        <p class="text-white font-13">{{ __(get_option('app_copyright')) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

@if (!$instructors->isEmpty())
<section class="our-tutors">
    <div class="container">
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
            <span class="section-tag-title">Our Tutors</span>
        </p>
        <h1
            class="section-heading-home">
            Teachers Who Inspire
        </h1>
        {{-- <div class="our-tutors-slider">
            <div class="swiper teacher-slider">
                <div class="swiper-wrapper">
                    @foreach($instructors as $instructor)
                    <div class="swiper-slide">
                        <div class="slide-wrap">
                            <div
                                class="video-area-left position-relative d-flex align-items-center justify-content-center">
                                <div class="course-info-video-img">
                                    <img class="tutor-img" loading="lazy" src="{{ $instructor->user?->image_path }}" alt="{{ $instructor->image_url }}">
                                </div>
                                @if ($instructor->video_check)
                                <button type="button" class="play-btn position-absolute" style="background:transparent !important"
                                    data-bs-toggle="modal" data-bs-target="#newVideoPlayerModal_{{$instructor->id}}">
                                    <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="80" height="80" rx="40" fill="black" />
                                        <path d="M44.1563 38.4636C45.1158 39.2631 45.1158 40.7369 44.1563 41.5364L37.2804 47.2664C35.9777 48.3519 34 47.4256 34 45.7299L34 34.2701C34 32.5744 35.9777 31.6481 37.2804 32.7336L44.1563 38.4636Z" fill="white" />
                                    </svg>
                                </button>
                                @endif
                            </div>
                            <a href="{{ route('userProfile', ['user' => $instructor->id]) }}" class="tutor-name">{{ $instructor->name }}</a>
                            <p class="tutor-designation">{{ $instructor->professional_title }}</p>
                            <p
                                class="tutor-text">
                                {{ $instructor->about_me }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="swiper-scrollbar2 swiper-scrollbar-horizontal"></div>
        </div> --}}


        <div class="our-tutors-slider">
            <div class="swiper teacher-slider">
                <div class="swiper-wrapper">
                    @foreach($instructors as $instructor)
                    <div class="swiper-slide" data-profile-url="https://portal.cefonlineacademy.com/tutor/myprofile">
                        <div class="slide-wrap">
                            <div class="video-area-left position-relative d-flex align-items-center justify-content-center">
                                <div class="course-info-video-img">
                                    <img class="tutor-img" loading="lazy" src="{{ $instructor->user?->image_path }}" alt="{{ $instructor->name }}">
                                </div>
                                @if ($instructor->video_check)
                                <button type="button"
                                    class="play-btn position-absolute"
                                    style="background: transparent !important; z-index: 10;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#newVideoPlayerModal_{{$instructor->id}}"
                                    onclick="event.stopPropagation();">
                                    <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="80" height="80" rx="40" fill="black" />
                                        <path d="M44.1563 38.4636C45.1158 39.2631 45.1158 40.7369 44.1563 41.5364L37.2804 47.2664C35.9777 48.3519 34 47.4256 34 45.7299L34 34.2701C34 32.5744 35.9777 31.6481 37.2804 32.7336L44.1563 38.4636Z" fill="white" />
                                    </svg>
                                </button>
                                @endif
                            </div>
                            <div class="tutor-name">{{ $instructor->name }}</div>
                            <p class="tutor-designation">{{ $instructor->professional_title }}</p>
                            <p class="tutor-text">{{ $instructor->about_me }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="swiper-scrollbar2 swiper-scrollbar-horizontal"></div>
        </div>



        @foreach($instructors as $instructor)
        @if ($instructor->video_check)
        <div class="modal fade VideoTypeModal" id="newVideoPlayerModal_{{$instructor->id}}" tabindex="-1"
            aria-labelledby="newVideoPlayerModal" aria-hidden="true">
            <div class="modal-header border-bottom-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span
                        class="iconify" data-icon="akar-icons:cross">
                    </span>
                </button>
            </div>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="video-player-area">
                            @if ($instructor->video_check == 1 && getVideoFile($instructor->video))
                            <video id="player"
                                playsinline
                                controls
                                data-poster="{{ getImageFile($instructor->user?->image_path) }}"
                                controlsList="nodownload">
                                <source src="{{ getVideoFile($instructor->video) }}"
                                    type="video/mp4">
                            </video>
                            @endif
                            @if ($instructor->video_check == 2)
                            @php
                            $srcFile = getVideoUrl($instructor->video);
                            @endphp
                            <div class="plyr__video-embed"
                                id="playerVideoYoutube">
                                <iframe src="{{$srcFile }}"
                                    allowfullscreen
                                    allowtransparency
                                    allow="autoplay">
                                </iframe>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endforeach
    </div>
</section>
@endif

<x-video-modal />   

<script>

   


    document.addEventListener('DOMContentLoaded', function() {
        var swiper3 = new Swiper(".teacher-slider", {
            spaceBetween: 10,
            initialSlide: 2,
            loop: false,
            scrollbar: {
                el: ".swiper-scrollbar2",
                hide: false,
            },
            breakpoints: {
                320: {
                    slidesPerView: 1,
                    centeredSlides: true,
                },
                576: {
                    slidesPerView: 2,
                    centeredSlides: true,
                },
                1024: {
                    slidesPerView: 5,
                    centeredSlides: true,
                },
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {



        var swiper13 = new Swiper(".featuredCoursesSlider", {
            spaceBetween: 10,
            slidesPerView: 1,
            navigation: {
                nextEl: ".featured-next-btn",
                prevEl: ".featured-prev-btn",
            },
            loop: false,
        });
    });

    document.querySelector('.featured-prev-btn').addEventListener('click', function() {
        swiper13.slidePrev();
    });

    document.querySelector('.featured-next-btn').addEventListener('click', function() {
        swiper13.slideNext();
    });

    document.addEventListener('DOMContentLoaded', function() {
        var swiper4 = new Swiper(".products-slider", {
            spaceBetween: 10,
            loop: true,
            centeredSlides: true,
            pagination: {
                el: ".swiper-pagination",
            },
            breakpoints: {
                320: {
                    slidesPerView: 1,
                },
                576: {
                    slidesPerView: 1,
                },
                767: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 5,
                },
            },
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        var swiper4 = new Swiper(".grid-slider", {
            spaceBetween: 10,
            autoplay: {
                delay: 8000,
                disableOnInteraction: false,
            },
            speed: 1000,
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },
            loop: true,
            centeredSlides: true,
            slidesPerView: 1,
            pagination: {
                el: ".swiper-pagination",
            },
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        var swiper5 = new Swiper(".course-slider", {
            spaceBetween: 10,
            loop: true,
            centeredSlides: true,
            pagination: {
                el: ".swiper-pagination",
            },
            breakpoints: {
                320: {
                    slidesPerView: 1,
                },
                576: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 5,
                },
            },
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        var swiper5 = new Swiper(".blogs-slider", {
            spaceBetween: 10,
            loop: true,
            centeredSlides: true,
            pagination: {
                el: ".swiper-pagination",
            },
            breakpoints: {
                320: {
                    slidesPerView: 1,
                },
                576: {
                    slidesPerView: 1,
                },
                767: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 5,
                },
            },
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        var swiper6 = new Swiper(".variety-slider", {
            spaceBetween: 10,
            loop: true,
            centeredSlides: true,
            pagination: {
                el: ".swiper-pagination",
            },
            breakpoints: {
                320: {
                    slidesPerView: 1,
                },
                576: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 5,
                },
            },
        });
    });
</script>

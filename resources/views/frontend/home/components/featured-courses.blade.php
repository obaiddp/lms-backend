@if (!$featuredCourses->isEmpty())
<section class="featured-courses">
    <div class="featured-courses-slider home-page-container">
        <div class="swiper featuredCoursesSlider">
            <div class="swiper-wrapper">
                @foreach ($featuredCourses as $key=> $featuredCourse)
                <div class="swiper-slide">
                    <div class="featured-courses-wrap home-page-container">
                        <div class="featured-courses-left">
                            <p class="section-tag">
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
                                <span class="section-tag-title">Featured Courses</span>
                            </p>
                            <a href="{{route('course-details',['slug'=>$featuredCourse->slug])}}">
                                <h1 class="section-heading-home">{{ $featuredCourse->title }}</h1>
                            </a>
                            <p class="featured-section-description">
                                {!! html_entity_decode(Str::limit($featuredCourse->short_description, 200)) !!}
                            </p>
                            <br>
                            <span style="margin-top:10px" class="section-tag-title">
                                <a href="{{route('course-details',['slug'=>$featuredCourse->slug])}}" class="view-all-btn-home-btn" aria-current="page">
                                    {{ __('Learn More') }}
                                </a>
                            </span>
                        </div>
                        <div class="featured-courses-right">
                            @if ($featuredCourse->image_path)
                            <div class="featured-courses-left-wrap">
                                <img loading="lazy" src="{{ getImageFile($featuredCourse->image_path) }}" alt="Course Image" class="course-image-home">
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @if ($featuredCourse->count() > 1)
        <div class="featured-button-container home-page-container">
            <button class="featured-prev-btn">
                <svg width="14" height="12" viewBox="0 0 14 12" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M2.61279 6.74128L13.0528 6.74128C13.2203 6.72515 13.3763 6.63999 13.49 6.5026C13.6036 6.36521 13.6667 6.18559 13.6667 5.99919C13.6667 5.81279 13.6036 5.63316 13.49 5.49577C13.3763 5.35838 13.2203 5.27322 13.0528 5.25709L2.61279 5.25709L6.16252 1.32315C6.23586 1.25714 6.29582 1.17473 6.33849 1.08129C6.38115 0.987857 6.40556 0.885499 6.41013 0.780898C6.4147 0.676298 6.39932 0.571805 6.36499 0.474239C6.33067 0.376673 6.27817 0.288226 6.21092 0.214671C6.14367 0.141116 6.06319 0.084106 5.97472 0.0473605C5.88626 0.0106149 5.7918 -0.00503995 5.6975 0.00141753C5.60321 0.00787502 5.5112 0.0362997 5.42749 0.0848366C5.34378 0.133374 5.27024 0.200932 5.21167 0.283103L0.523777 5.47869C0.401659 5.61777 0.333328 5.80418 0.333328 5.99825C0.333328 6.19233 0.401659 6.37874 0.523777 6.51781L5.21167 11.7143C5.27005 11.797 5.34353 11.865 5.42729 11.914C5.51105 11.963 5.6032 11.9917 5.6977 11.9985C5.7922 12.0052 5.88692 11.9897 5.97564 11.953C6.06436 11.9163 6.14509 11.8593 6.21252 11.7856C6.27996 11.7119 6.33258 11.6232 6.36694 11.5254C6.4013 11.4276 6.41662 11.3228 6.4119 11.218C6.40718 11.1131 6.38252 11.0106 6.33954 10.917C6.29657 10.8235 6.23624 10.7411 6.16252 10.6752L2.60607 6.74128H2.61279Z"
                        fill="white" />
                </svg>
            </button>
            <button class="featured-next-btn">
                <svg width="14" height="12" viewBox="0 0 14 12" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M11.3872 6.74128L0.947131 6.74128C0.779606 6.72515 0.623625 6.63999 0.509981 6.5026C0.396338 6.36521 0.333293 6.18559 0.333293 5.99919C0.333293 5.81279 0.396338 5.63316 0.509981 5.49577C0.623625 5.35838 0.779606 5.27322 0.947131 5.25709L11.3872 5.25709L7.83743 1.32315C7.76409 1.25714 7.70413 1.17473 7.66147 1.08129C7.6188 0.987857 7.59439 0.885499 7.58982 0.780898C7.58526 0.676298 7.60064 0.571805 7.63496 0.474239C7.66929 0.376673 7.72179 0.288226 7.78904 0.214671C7.85628 0.141116 7.93676 0.084106 8.02523 0.0473605C8.1137 0.0106149 8.20816 -0.00503995 8.30245 0.00141753C8.39675 0.00787502 8.48875 0.0362997 8.57246 0.0848366C8.65618 0.133374 8.72972 0.200932 8.78828 0.283103L13.4762 5.47869C13.5983 5.61777 13.6666 5.80418 13.6666 5.99825C13.6666 6.19233 13.5983 6.37874 13.4762 6.51781L8.78828 11.7143C8.7299 11.797 8.65643 11.865 8.57266 11.914C8.4889 11.963 8.39675 11.9917 8.30225 11.9985C8.20775 12.0052 8.11304 11.9897 8.02431 11.953C7.93559 11.9163 7.85487 11.8593 7.78743 11.7856C7.72 11.7119 7.66738 11.6232 7.63302 11.5254C7.59866 11.4276 7.58333 11.3228 7.58806 11.218C7.59278 11.1131 7.61743 11.0106 7.66041 10.917C7.70339 10.8235 7.76371 10.7411 7.83743 10.6752L11.3939 6.74128H11.3872Z"
                        fill="white" />
                </svg>
            </button>
        </div>
        @endif

    </div>
</section>
@endif

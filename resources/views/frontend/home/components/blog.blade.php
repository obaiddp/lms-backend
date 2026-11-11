<section class="blog-section-home">
    <div class="home-page-container">
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
            <span class="section-tag-title">CEF Blog</span>
        </p>
        <h1
            class="section-heading-home">
            Our latest blogs
        </h1>

        <div class="blog-list">
            @foreach ($blogs as $blog)
            <a href="{{ route('blog-details', $blog->slug) }}" class="blog">
                <img loading="lazy" src="{{ $blog->image ? $blog->image : asset('uploads_demo/blog/default.jpg') }}"
                    alt="{{ $blog->title }}" class="blog-image">
                <h1>
                    {{ $blog->title }}
                </h1>
            </a>
            @endforeach
        </div>

        <div class="blog-section-slider">
            <div class="swiper blogs-slider">
                <div class="swiper-wrapper">
                    @foreach ($blogs as $blog)
                    <div class="swiper-slide blog-list2">
                        <a href="{{ route('blog-details', $blog->slug) }}" class="blog">
                            <img loading="lazy" src="{{ $blog->image ? $blog->image : asset('uploads_demo/blog/default.jpg') }}"
                                alt="{{ $blog->title }}" class="blog-image">
                            <h1>
                                {{ $blog->title }}
                            </h1>
                            <p>{!! Str::limit($blog->details, 240) !!}</p>
                            <div class="read-more">
                                Read
                                More
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </div>
</section>
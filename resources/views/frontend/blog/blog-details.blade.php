@extends('frontend.layouts.app')

@section('meta')
    <meta name="description" content="{{ __($blog->meta_description) }}">
    <meta name="keywords" content="{{ __($blog->meta_keywords) }}">

    <!-- Open Graph meta tags for social sharing -->
    <meta property="og:type" content="Learning">
    <meta property="og:title" content="{{ __($blog->meta_title) }}">
    <meta property="og:description" content="{{ __($blog->meta_description) }}">
    <meta property="og:image" content="{{ getImageFile($blog->og_image) }}">
    <meta property="og:url" content="{{ url()->current() }}">

    <meta property="og:site_name" content="{{ __(get_option('app_name')) }}">

    <!-- Twitter Card meta tags for Twitter sharing -->
    <meta name="twitter:card" content="Learning">
    <meta name="twitter:title" content="{{ __($blog->meta_title) }}">
    <meta name="twitter:description" content="{{ __($blog->meta_description) }}">
    <meta name="twitter:image" content="{{ getImageFile($blog->og_image) }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
@endsection

@section('content')
    <div>
        <header class="page-banner-header gradient-bg position-relative pt-20">
        </header>

        <div class="courses-crumb course-details-crumb">
            <div class="home-page-container">
                <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="me-2"><svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M11.875 16.25V12.5C11.875 12.3343 11.8092 12.1753 11.6919 12.0581C11.5747 11.9409 11.4158 11.875 11.25 11.875H8.75C8.58424 11.875 8.42527 11.9409 8.30806 12.0581C8.19085 12.1753 8.125 12.3343 8.125 12.5V16.25C8.125 16.4158 8.05915 16.5747 7.94194 16.692C7.82473 16.8092 7.66576 16.875 7.5 16.875H3.75C3.58424 16.875 3.42527 16.8092 3.30806 16.692C3.19085 16.5747 3.125 16.4158 3.125 16.25V9.02345C3.1264 8.93696 3.14509 8.85162 3.17998 8.77245C3.21486 8.69329 3.26523 8.62191 3.32812 8.56252L9.57812 2.88283C9.69334 2.77742 9.84384 2.71896 10 2.71896C10.1562 2.71896 10.3067 2.77742 10.4219 2.88283L16.6719 8.56252C16.7348 8.62191 16.7851 8.69329 16.82 8.77245C16.8549 8.85162 16.8736 8.93696 16.875 9.02345V16.25C16.875 16.4158 16.8092 16.5747 16.6919 16.692C16.5747 16.8092 16.4158 16.875 16.25 16.875H12.5C12.3342 16.875 12.1753 16.8092 12.0581 16.692C11.9408 16.5747 11.875 16.4158 11.875 16.25Z"
                                stroke="#5F6C72" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </li>
                    <li class="breadcrumb-item font-14"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                    <li class="breadcrumb-item font-14" aria-current="page">{{ __('Blogs') }}</li>
                    <li class="breadcrumb-item font-14 active" aria-current="page">{{ __($blog->title) }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <section class="pb-2">
            <div class="home-page-container">
                <h1 class="blog-detail-title">{{ __($blog->title) }}</h1>
                <div class="blog-detail-date">Jan 05, 2025   -   08:45 AM</div>
                <img src="{{ getImageFile($blog->image_path) }}" alt="img" class="img-fluid blog-detail-img">
                    <div class="blog-detail-info d-flex">
                        <div class="blog-info-section">
                            @php
                                $detailsHtml = html_entity_decode($blog->details);
                                $extractedHeadings = [];
                                
                                // Extract headings and modify the HTML to add IDs directly
                                $modifiedHtml = preg_replace_callback('/<h([1-6])(?:\s[^>]*)?>(.+?)<\/h\1>/is', function($matches) use (&$extractedHeadings) {
                                    $headingLevel = $matches[1];
                                    $headingContent = $matches[2];
                                    $fullMatch = $matches[0];
                                    
                                    // Clean up the heading text
                                    $headingText = trim(strip_tags($headingContent));
                                    $headingText = preg_replace('/\s+/', ' ', $headingText);
                                    
                                    if ($headingText !== '') {
                                        $headingId = 'heading-' . \Illuminate\Support\Str::slug($headingText);
                                        
                                        $extractedHeadings[] = [
                                            'text' => $headingText,
                                            'id' => $headingId,
                                            'level' => $headingLevel
                                        ];
                                        
                                        // Check if the heading already has an id attribute
                                        if (preg_match('/\sid\s*=\s*["\'][^"\']*["\']/', $fullMatch)) {
                                            // If it has an id, replace it
                                            return preg_replace('/\sid\s*=\s*["\'][^"\']*["\']/', ' id="' . $headingId . '"', $fullMatch);
                                        } else {
                                            // If no id, add it
                                            return preg_replace('/<h' . $headingLevel . '(?:\s[^>]*)?>/i', '<h' . $headingLevel . ' id="' . $headingId . '">', $fullMatch);
                                        }
                                    }
                                    
                                    return $fullMatch;
                                }, $detailsHtml);
                                
                                // Store the modified HTML for use in the content area
                                $blog->modified_details = $modifiedHtml;
                            @endphp
                            <div class="blog-info-heading">
                                @forelse($extractedHeadings as $heading)
                                    <a href="#{{ $heading['id'] }}" class="blog-info-title" style="text-decoration: none; color: inherit; display: block;">{{ $heading['text'] }}</a>
                                @empty
                                    <h2 class="blog-info-title">{{ __($blog->title) }}</h2>
                                @endforelse
                            </div>
                            
                            <div class="blog-share-section">
                                <h2 class="blog-share-title">Share Article</h2>
                                <div class="blog-share-icons">
                                    @php
                                        $currentUrl = url()->current();
                                        $blogTitle = __($blog->title);
                                        $blogDescription = __($blog->meta_description);
                                        $blogImage = getImageFile($blog->og_image);
                                    @endphp
                                    
                                    <a href="javascript:void(0)" 
                                       onclick="shareOnFacebook('{{ $currentUrl }}', '{{ addslashes($blogTitle) }}', '{{ addslashes($blogDescription) }}')" 
                                       class="blog-share-icon facebook" 
                                       title="Share on Facebook">
                                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M16 32C24.8366 32 32 24.8366 32 16C32 7.16344 24.8366 0 16 0C7.16344 0 0 7.16344 0 16C0 24.8366 7.16344 32 16 32Z" fill="#3B5998"/>
                                            <path d="M20.023 16.6263H17.168V27.0858H12.8424V16.6263H10.7852V12.9505H12.8424V10.5718C12.8424 8.87078 13.6504 6.20715 17.2065 6.20715L20.4106 6.22056V9.7886H18.0858C17.7045 9.7886 17.1683 9.97912 17.1683 10.7906V12.9539H20.4009L20.023 16.6263Z" fill="white"/>
                                        </svg>
                                    </a>
                                    
                                    <a href="javascript:void(0)" 
                                       onclick="shareOnTwitter('{{ $currentUrl }}', '{{ addslashes($blogTitle) }}', '{{ addslashes($blogDescription) }}')" 
                                       class="blog-share-icon twitter" 
                                       title="Share on Twitter/X">
                                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g clip-path="url(#clip0_823_1325)">
                                            <path d="M16 0C7.16375 0 0 7.16375 0 16C0 24.8363 7.16375 32 16 32C24.8363 32 32 24.8363 32 16C32 7.16375 24.8363 0 16 0Z" fill="black"/>
                                            <path d="M17.7506 14.5521L24.6 6.59021H22.9769L17.0296 13.5034L12.2795 6.59021H6.80078L13.9839 17.0442L6.80078 25.3934H8.42397L14.7045 18.0929L19.721 25.3934H25.1997L17.7502 14.5521H17.7506ZM9.00881 7.81213H11.5019L22.9777 24.2271H20.4846L9.00881 7.81213Z" fill="white"/>
                                            </g>
                                            <defs>
                                            <clipPath id="clip0_823_1325">
                                            <rect width="32" height="32" fill="white"/>
                                            </clipPath>
                                            </defs>
                                        </svg>
                                    </a>
                                    
                                    <a href="javascript:void(0)" 
                                       onclick="shareOnLinkedIn('{{ $currentUrl }}', '{{ addslashes($blogTitle) }}', '{{ addslashes($blogDescription) }}')" 
                                       class="blog-share-icon linkedin" 
                                       title="Share on LinkedIn">
                                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g clip-path="url(#clip0_823_1329)">
                                            <path d="M16 31.9998C24.8366 31.9998 32 24.8363 32 15.9998C32 7.1632 24.8366 -0.000244141 16 -0.000244141C7.16344 -0.000244141 0 7.1632 0 15.9998C0 24.8363 7.16344 31.9998 16 31.9998Z" fill="#007AB9"/>
                                            <path d="M25.5603 17.2872V23.8836H21.7359V17.7292C21.7359 16.1839 21.1837 15.1286 19.799 15.1286C18.7423 15.1286 18.1145 15.8391 17.8373 16.5271C17.7366 16.7729 17.7106 17.1143 17.7106 17.4591V23.8833H13.8859C13.8859 23.8833 13.9373 13.4599 13.8859 12.3809H17.7109V14.0109C17.7032 14.0237 17.6924 14.0363 17.6856 14.0485H17.7109V14.0109C18.2192 13.2288 19.1256 12.1108 21.1578 12.1108C23.6739 12.1108 25.5603 13.7548 25.5603 17.2872ZM9.88492 6.8363C8.57663 6.8363 7.7207 7.69509 7.7207 8.8234C7.7207 9.92775 8.55182 10.8113 9.83472 10.8113H9.85953C11.1935 10.8113 12.0229 9.92775 12.0229 8.8234C11.9975 7.69509 11.1935 6.8363 9.88492 6.8363ZM7.94802 23.8836H11.7713V12.3809H7.94802V23.8836Z" fill="#F1F2F2"/>
                                            </g>
                                            <defs>
                                            <clipPath id="clip0_823_1329">
                                            <rect width="32" height="32" fill="white"/>
                                            </clipPath>
                                            </defs>
                                        </svg>
                                    </a>
                                    
                                    <a href="javascript:void(0)" 
                                       onclick="shareOnMedium('{{ $currentUrl }}', '{{ addslashes($blogTitle) }}')" 
                                       class="blog-share-icon medium" 
                                       title="Share on Medium">
                                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g clip-path="url(#clip0_823_1333)">
                                            <path d="M16 32C24.8366 32 32 24.8366 32 16C32 7.16344 24.8366 0 16 0C7.16344 0 0 7.16344 0 16C0 24.8366 7.16344 32 16 32Z" fill="black"/>
                                            <path d="M17.5573 16C17.5573 19.8129 14.4874 22.9038 10.7005 22.9038C6.91362 22.9038 3.84375 19.8129 3.84375 16C3.84375 12.1871 6.91363 9.09619 10.7006 9.09619C14.4874 9.09619 17.5573 12.1871 17.5573 16ZM25.0794 16C25.0794 19.5892 23.5444 22.4988 21.651 22.4988C19.7576 22.4988 18.2226 19.5892 18.2226 16C18.2226 12.4108 19.7576 9.50125 21.651 9.50125C23.5444 9.50125 25.0794 12.4108 25.0794 16ZM28.1562 16C28.1562 19.2158 27.6164 21.8226 26.9504 21.8226C26.2845 21.8226 25.7447 19.2158 25.7447 16C25.7447 12.7843 26.2844 10.1774 26.9504 10.1774C27.6164 10.1774 28.1562 12.7843 28.1562 16Z" fill="white"/>
                                            </g>
                                            <defs>
                                            <clipPath id="clip0_823_1333">
                                            <rect width="32" height="32" fill="white"/>
                                            </clipPath>
                                            </defs>
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            <div class="blog-detail-tags">
                                <span class="blog-detail-tag">#Quran</span>
                                <span class="blog-detail-tag">#Hadith</span>
                                <span class="blog-detail-tag">#Insights</span>
                            </div>
                        </div>
                        <div class="blog-detail-content" id="blog-content">
                            {{-- {!! $blog->details !!} --}}
                            {{-- {!! $blog->cleaned_details !!} --}}
                            {!! $blog->modified_details !!}
                        </div>
                    </div>
            </div>
        </section>

        <section class="blog-section-home" style="background: #EDEEF3;">
            <div class="home-page-container">
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
                    <span class="section-tag-title">Related Blogs</span>
                </p>
                <h1 class="section-heading-home">You May Also Like</h1>
                <div class="blog-list">
                    @foreach($recentBlogs as $recentBlog)
                    <a href="{{ route('blog-details', $recentBlog->slug) }}" class="blog-item-page">
                        <img src="{{ getImageFile($recentBlog->image_path) }}" alt="{{ $recentBlog->title }}">
                        <div class="blog-info">
                            <div class="blog-cat">{{ $recentBlog->category->name }}</div>
                            <div class="blog-title">{{ $recentBlog->title }}</div>
                            <div class="blog-info-wrap">
                                <div class="blog-info-item-icon">
                                    <img
                                        src="{{ getImageFile($recentBlog->user->image_path) }}"
                                        alt="{{ $recentBlog->user->name }}"
                                    />  
                                    <div class="blog-info-item-text">{{ $recentBlog->user->name }}</div>
                                </div>
                                <div class="blog-read">
                                    {{ $recentBlog->reading_time }} min read
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                <div class="blog-section-slider">
                    <div class="swiper blogs-slider">
                        <div class="swiper-wrapper">
                            @foreach($recentBlogs as $recentBlog)
                            <a href="{{ route('blog-details', $recentBlog->slug) }}" class="blog-item-page swiper-slide">
                                <img src="{{ getImageFile($recentBlog->image_path) }}" alt="{{ $recentBlog->title }}">
                                <div class="blog-info">
                                    <div class="blog-cat">{{ $recentBlog->category->name }}</div>
                                    <div class="blog-title">{{ $recentBlog->title }}</div>
                                    <div class="blog-info-wrap">
                                        <div class="blog-info-item-icon">
                                            <img
                                                src="{{ getImageFile($recentBlog->user->image_path) }}"
                                                alt="{{ $recentBlog->user->name }}"
                                            />  
                                            <div class="blog-info-item-text">{{ $recentBlog->user->name }}</div>
                                        </div>
                                        <div class="blog-read">
                                            {{ $recentBlog->reading_time }} min read
                                        </div>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="pt-12">
            <div class="home-page-container">
                <h1 class="section-heading-home">Leave a Reply</h1>

            <form class="comment-form">
                <p class="required-note">Your email address will not be published. Required fields are marked *</p>
                
                <div class="d-flex comment-form-inputs">
                    <div class="form-group">
                        <label for="name">Your name *</label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="comment">Your comment</label>
                    <textarea id="comment" name="comment" required></textarea>
                </div>

                <div class="form-check">
                    <input type="checkbox" id="save-info" name="save-info">
                    <label for="save-info">Save my name, email, and website in this browser for the next time I comment.</label>
                </div>

                <button type="submit" class="btn-submit">Post Comment</button>
            </form>
            </div>
        </section>
    </div>

    <!-- Comment Reply Modal Start -->
    <div class="modal fade" id="commentReplyModal" tabindex="-1" aria-labelledby="writeReviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="writeReviewModalLabel">{{ __('Comment Reply') }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('blog-comment-reply.store') }}" method="post">
                    @csrf
                    <input type="hidden" name="parent_id" class="parent_id">
                    <input type="hidden" name="blog_id" class="blog_id" value="{{ $blog->id }}">
                    <input type="hidden" name="user_id" class="user_id" value="{{ @Auth::user()->id }}">
                    <div class="modal-body">
                        <!-- Leave a Comment Area-->

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group para-color font-14 color-gray mb-30">
                                    <input type="text" name="name" class="form-control" required value="{{ @Auth::user()->name }}" readonly>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group para-color font-14 color-gray mb-30">
                                    <input type="email" name="email" class="form-control" id="cus_email" required value="{{ @Auth::user()->email }}" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group para-color font-14 color-gray nessage-text mb-30">
                                    <textarea name="comment" rows="3" class="form-control" id="cus_comment" required placeholder="{{ __('Your Comments *') }}"></textarea>
                                </div>
                            </div>
                        </div>
                        <!-- Leave a Comment Area-->
                    </div>
                    <div class="modal-footer d-flex justify-content-between align-items-center">
                        <button type="button" class="theme-btn theme-button3 modal-back-btn !text-white !rounded-md" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="theme-btn theme-button1 default-hover-btn">{{ __('Submit Review') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Comment Reply Modal End -->
    <input type="hidden" class="searchBlogRoute" value="{{ route('search-blog.list') }}">


    <input type="hidden" class="blog_id" value="{{ @$blog->id }}">
    <input type="hidden" class="user_id" value="{{ @Auth::user()->id }}">
    <input type="hidden" class="user_name" value="{{ @Auth::user()->name }}">
    <input type="hidden" class="user_email" value="{{ @Auth::user()->email }}">
    <input type="hidden" class="blogCommentStoreRoute" value="{{ route('blog-comment.store') }}">
@endsection

@push('script')
    <!-- Start:: Blog Search & Blog Comment and reply-->
    <script src="{{ asset('frontend/assets/js/custom/search-blog-list.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/custom/blog-comment-reply.js') }}"></script>
    <script>
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

            // IDs are already added to headings in the HTML by PHP
            // Just verify that all headings have IDs for debugging
            const blogContent = document.getElementById('blog-content');
            if (blogContent) {
                const headings = blogContent.querySelectorAll('h1, h2, h3, h4, h5, h6');
                console.log('Found headings with IDs:', headings.length);
                
                headings.forEach(function(heading, index) {
                    console.log(`Heading ${index + 1}: "${heading.textContent.trim()}" - ID: "${heading.id}"`);
                });
            }

            // Enable smooth scrolling for anchor links with offset for fixed navigation
            document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href').substring(1);
                    const targetElement = document.getElementById(targetId);
                    if (targetElement) {
                        // Calculate offset to account for fixed navigation
                        const navHeight = 100; // Adjust this value based on your navigation height
                        const elementPosition = targetElement.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - navHeight;

                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // Check if native Web Share API is supported and show/hide button accordingly
            if (navigator.share) {
                const nativeShareBtn = document.querySelector('.native-share');
                if (nativeShareBtn) {
                    nativeShareBtn.style.display = 'inline-block';
                }
            }
        });

        // Social Media Share Functions
        function shareOnFacebook(url, title, description) {
            const shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}&quote=${encodeURIComponent(title + ' - ' + description)}`;
            openShareWindow(shareUrl, 'Facebook Share');
        }

        function shareOnTwitter(url, title, description) {
            const text = `${title} - ${description}`;
            const shareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(text)}`;
            openShareWindow(shareUrl, 'Twitter Share');
        }

        function shareOnLinkedIn(url, title, description) {
            const shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}&title=${encodeURIComponent(title)}&summary=${encodeURIComponent(description)}`;
            openShareWindow(shareUrl, 'LinkedIn Share');
        }

        function shareOnMedium(url, title) {
            const shareUrl = `https://medium.com/new-story?url=${encodeURIComponent(url)}&title=${encodeURIComponent(title)}`;
            openShareWindow(shareUrl, 'Medium Share');
        }

        function copyToClipboard(url) {
            if (navigator.clipboard && window.isSecureContext) {
                // Use modern Clipboard API
                navigator.clipboard.writeText(url).then(function() {
                    showShareMessage('Link copied to clipboard!', 'success');
                }).catch(function(err) {
                    console.error('Failed to copy: ', err);
                    fallbackCopyToClipboard(url);
                });
            } else {
                // Fallback for older browsers
                fallbackCopyToClipboard(url);
            }
        }

        function fallbackCopyToClipboard(text) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    showShareMessage('Link copied to clipboard!', 'success');
                } else {
                    showShareMessage('Failed to copy link. Please copy manually: ' + text, 'error');
                }
            } catch (err) {
                console.error('Fallback copy failed: ', err);
                showShareMessage('Failed to copy link. Please copy manually: ' + text, 'error');
            }
            
            document.body.removeChild(textArea);
        }

        function nativeShare(url, title, description) {
            if (navigator.share) {
                navigator.share({
                    title: title,
                    text: description,
                    url: url
                }).then(() => {
                    console.log('Successful share');
                }).catch((error) => {
                    console.error('Error sharing:', error);
                    showShareMessage('Share failed. Please try another method.', 'error');
                });
            } else {
                showShareMessage('Sharing not supported on this device.', 'error');
            }
        }

        function openShareWindow(url, title) {
            const windowFeatures = 'width=600,height=400,scrollbars=yes,resizable=yes,toolbar=no,menubar=no,location=no,directories=no,status=no';
            window.open(url, title, windowFeatures);
        }

        function showShareMessage(message, type) {
            // Create a toast/notification
            const toast = document.createElement('div');
            toast.className = `share-toast ${type}`;
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 12px 20px;
                border-radius: 5px;
                color: white;
                font-weight: 500;
                z-index: 10000;
                opacity: 0;
                transition: opacity 0.3s ease;
                max-width: 300px;
                word-wrap: break-word;
                ${type === 'success' ? 'background-color: #28a745;' : 'background-color: #dc3545;'}
            `;
            toast.textContent = message;
            
            document.body.appendChild(toast);
            
            // Fade in
            setTimeout(() => {
                toast.style.opacity = '1';
            }, 100);
            
            // Remove after 3 seconds
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => {
                    if (document.body.contains(toast)) {
                        document.body.removeChild(toast);
                    }
                }, 300);
            }, 3000);
        }
    </script>
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js" async></script>

    <!-- End:: Blog Search & Blog Comment and reply-->
@endpush

@extends('frontend.layouts.app')
@section('meta')
    @php
        $metaData = getMeta('blog');
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

<div>

<x-frontend.partials.header 
    title="Discover our latest blogs"
    subtitle="Discover the milestones that set our Islamic online education apart, from pioneering initiatives to esteemed recognition."
    :breadcrumbs="[
        ['text' => 'Home', 'url' => url('/')],
        ['text' => 'Blogs']
    ]"
    margin-top="lg-mt-10"
    height="h-28 sm-h-32 md-h-30 lg-h-36"
    height2="h-20 sm-h-26 md-h-24"
/>

<section class="blog-page-area">
    @if($recentBlogs->count() > 0)
    <a href="{{ route('blog-details', $recentBlogs[0]->slug) }}" class="home-page-container blog-page-area-container d-flex justify-content-center align-items-center">
        <img
            class="blog-page-area-img"
            src="{{ getImageFile($recentBlogs[0]->image_path) }}"
            alt="{{ $recentBlogs[0]->title }}"
        />
        <div class="blog-page-area-content">
            <div class="blog-page-area-content-wrap">
                <h2 class="blog-page-area-title">
                    {{ __($recentBlogs[0]->title) }}
                </h2>
                <div class="blog-page-area-arrow-icon">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M25.0006 8V21C25.0006 21.2652 24.8952 21.5196 24.7077 21.7071C24.5201 21.8946 24.2658 22 24.0006 22C23.7353 22 23.481 21.8946 23.2934 21.7071C23.1059 21.5196 23.0006 21.2652 23.0006 21V10.4137L8.70806 24.7075C8.52042 24.8951 8.26592 25.0006 8.00056 25.0006C7.73519 25.0006 7.4807 24.8951 7.29306 24.7075C7.10542 24.5199 7 24.2654 7 24C7 23.7346 7.10542 23.4801 7.29306 23.2925L21.5868 9H11.0006C10.7353 9 10.481 8.89464 10.2934 8.70711C10.1059 8.51957 10.0006 8.26522 10.0006 8C10.0006 7.73478 10.1059 7.48043 10.2934 7.29289C10.481 7.10536 10.7353 7 11.0006 7H24.0006C24.2658 7 24.5201 7.10536 24.7077 7.29289C24.8952 7.48043 25.0006 7.73478 25.0006 8Z"
                            fill="white"
                        />
                    </svg>
                </div>
            </div>
            <div class="blog-info-wrap">
                <div class="blog-info-item">
                    <div class="blog-info-item-icon">
                        <img
                            src="{{ getImageFile($recentBlogs[0]->user->image) }}"
                            alt="{{ $recentBlogs[0]->user->name }}"
                        />
                        <div class="blog-info-item-text" style="color: white !important;">{{ $recentBlogs[0]->user->name }}</div>
                    </div>
                    <div class="blog-creation-date">
                        <div class="blog-creation-date-icon">
                            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="0.5" y="0.5" width="39" height="39" rx="19.5" stroke="#CBD5E1" />
                                <path d="M26.25 12.1875H24.6875V11.875C24.6875 11.6264 24.5887 11.3879 24.4129 11.2121C24.2371 11.0363 23.9986 10.9375 23.75 10.9375C23.5014 10.9375 23.2629 11.0363 23.0871 11.2121C22.9113 11.3879 22.8125 11.6264 22.8125 11.875V12.1875H17.1875V11.875C17.1875 11.6264 17.0887 11.3879 16.9129 11.2121C16.7371 11.0363 16.4986 10.9375 16.25 10.9375C16.0014 10.9375 15.7629 11.0363 15.5871 11.2121C15.4113 11.3879 15.3125 11.6264 15.3125 11.875V12.1875H13.75C13.3356 12.1875 12.9382 12.3521 12.6451 12.6451C12.3521 12.9382 12.1875 13.3356 12.1875 13.75V26.25C12.1875 26.6644 12.3521 27.0618 12.6451 27.3549C12.9382 27.6479 13.3356 27.8125 13.75 27.8125H26.25C26.6644 27.8125 27.0618 27.6479 27.3549 27.3549C27.6479 27.0618 27.8125 26.6644 27.8125 26.25V13.75C27.8125 13.3356 27.6479 12.9382 27.3549 12.6451C27.0618 12.3521 26.6644 12.1875 26.25 12.1875ZM15.3125 14.0625C15.3125 14.3111 15.4113 14.5496 15.5871 14.7254C15.7629 14.9012 16.0014 15 16.25 15C16.4986 15 16.7371 14.9012 16.9129 14.7254C17.0887 14.5496 17.1875 14.3111 17.1875 14.0625H22.8125C22.8125 14.3111 22.9113 14.5496 23.0871 14.7254C23.2629 14.9012 23.5014 15 23.75 15C23.9986 15 24.2371 14.9012 24.4129 14.7254C24.5887 14.5496 24.6875 14.3111 24.6875 14.0625H25.9375V15.9375H14.0625V14.0625H15.3125ZM14.0625 25.9375V17.8125H25.9375V25.9375H14.0625Z" fill="white"/>
                            </svg>
                        </div>
                        <div class="blog-creation-date-text">
                            {{ $recentBlogs[0]->created_at->format('Y-m-d') }}
                        </div>
                    </div>
                </div>

                @if($recentBlogs[0]->tags->count() > 0)
                    <div class="blog-tags">
                        @foreach($recentBlogs[0]->tags as $tag)
                            <div class="blog-tag">{{ $tag->name }}</div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </a>
    @endif
</section>

<section class="blog-page-list">
    <div class="home-page-container d-flex">
        <div class="blog-main-list">
            @forelse($blogs as $blog)
            <a href="{{ route('blog-details', $blog->slug) }}" class="blog-item-page">
                <img src="{{ getImageFile($blog->image_path) }}" alt="">
                <div class="blog-info">
                    <div class="blog-cat">Category</div>
                    <div class="blog-title">{{ __($blog->title) }}</div>
                    <div class="blog-info-wrap">
                        <div class="blog-info-item-icon">
                            <img
                                src="{{ getImageFile($blog->user->image) }}"
                                alt="{{ $blog->user->name }}"
                            />  
                            <div class="blog-info-item-text">{{ $blog->user->name }}</div>
                        </div>
                        <div class="blog-read">
                            0 min read
                        </div>
                    </div>
                </div>
            </a>
            @empty
                <div class="no-course-found text-center">
                    <img src="{{ asset('frontend/assets/img/empty-data-img.png') }}" alt="img" class="img-fluid">
                    <h5 class="mt-3">{{ __('Blog Not Found') }}</h5>
                </div>
            @endforelse
            @if(@$blogs->hasPages())
                {{ @$blogs->links('frontend.paginate.paginate') }}
            @endif
        </div>
        <div class="blog-sidebar">
            <h1>Featured</h1>
            <div class="sidebar-blogs">
                @foreach($recentBlogs as $recentBlog)
                <a href="{{ route('blog-details', $recentBlog->slug) }}" class="sidebar-blog-item">
                    <img src="{{ getImageFile($recentBlog->image_path) }}" alt="">
                    <div class="sidebar-blog-info">
                        <div class="sidebar-blog-date">{{ $recentBlog->created_at->format(' j  M, Y')  }}</div>
                        <div class="sidebar-blog-title">{{ __($recentBlog->title) }}</div>
                    </div>
                </a>
                @endforeach
            </div>
            <br>
            <h1>Recent Blogs</h1>
            <div class="sidebar-blogs">
                @foreach($recentBlogs as $recentBlog)
                <a href="{{ route('blog-details', $recentBlog->slug) }}" class="sidebar-blog-item">
                    <img src="{{ getImageFile($recentBlog->image_path) }}" alt="">
                    <div class="sidebar-blog-info">
                        <div class="sidebar-blog-date">{{ $recentBlog->created_at->format(' j  M, Y')  }}</div>
                        <div class="sidebar-blog-title">{{ __($recentBlog->title) }}</div>
                    </div>
                </a>
                @endforeach
            </div>
            <br>
            <h1>Categories</h1>
            <ul class="sidebar-blog-categories">
                @foreach($blogCategories as $blogCategory)
                <li><a href="{{ route('categoryBlogs', $blogCategory->slug) }}" class="font-15">{{ __($blogCategory->name) }} ({{ $blogCategory->active_blogs_count }})</a></li>
                @endforeach
            </ul>
            <br>
            <h1>Tags</h1>
            <ul class="blogs-sidebar-tags">
                @foreach($tags as $tag)
                <li>    
                    <a href="">{{ $tag->name }}</a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>

</div>

<input type="hidden" class="searchBlogRoute" value="{{ route('search-blog.list') }}">

@endsection

@push('script')
    <!-- Start:: Blog Search  -->
    <script src="{{ asset('frontend/assets/js/custom/search-blog-list.js') }}"></script>
    <!-- End:: Blog Search  -->
@endpush

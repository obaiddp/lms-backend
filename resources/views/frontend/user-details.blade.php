@extends('frontend.layouts.app')
@php
$userRelation = getUserRoleRelation($user);
@endphp

@section('meta')
<meta name="description" content="{{ __($user->meta_description) }}">
<meta name="keywords" content="{{ __($user->meta_keywords) }}">

<!-- Open Graph meta tags for social sharing -->
<meta property="og:type" content="Learning">
<meta property="og:title" content="{{ __($user->meta_title) }}">
<meta property="og:description" content="{{ __($user->meta_description) }}">
<meta property="og:image" content="{{ getImageFile($user->og_image) }}">
<meta property="og:url" content="{{ url()->current() }}">

<meta property="og:site_name" content="{{ __(get_option('app_name')) }}">

<!-- Twitter Card meta tags for Twitter sharing -->
<meta name="twitter:card" content="Learning">
<meta name="twitter:title" content="{{ __($user->meta_title) }}">
<meta name="twitter:description" content="{{ __($user->meta_description) }}">
<meta name="twitter:image" content="{{ getImageFile($user->og_image) }}">
@endsection

@section('content')
@push('style')
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@endpush
<div>
    <x-frontend.partials.header
        title="Our foremost instructional experts"
        subtitle="Our foremost instructional experts bring unparalleled knowledge and experience to every learning opportunity"
        :breadcrumbs="[
        ['text' => 'Home', 'url' => url('/')],
        ['text' => 'Courses']
    ]"
        margin-top="lg-mt-10"
        height="h-30 md-h-32 lg-h-36"
        height2="h-20 sm-h-26 md-h-23" />


    <!-- Instructor Details Area Start -->
    <section class="instructor-details-area section-t-space">
        <div class="home-page-container">
            <div class="row instructor-details-main-row w-100 mx-auto">
                <div class="col-12 col-md-12 col-lg-8 col-xl-9">
                    <div class="instructor-details-left-content">
                        <!-- Profile Header: avatar, name, title, stats, social -->
                        <div class="card border-0 mb-30">
                            <div class="card-body p-3 p-md-4">
                                <div class="row g-3 align-items-center">
                                    <div class="col-auto">
                                        <img src="{{ getImageFile($user->image_path) }}" alt="{{ $user->name }} avatar" class="rounded-3 object-fit-cover" style="width: 10rem;height: 10rem;">
                                    </div>
                                    <div class="col">
                                        <h5 class="mb-1 fw-semibold text-dark" style="font-size:18px; line-height:1.3;">{{ $user->$userRelation?->name }}</h5>
                                        <p class="text-muted" style="font-size:12px;">{{ $user->$userRelation?->professional_title }}</p>

                                        @if ($average_rating > 0)
                                            <div class="d-flex flex-wrap align-items-center gap-3 small text-secondary mt-1">
                                                <div class="d-inline-flex align-items-center gap-1">
                                                    <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M7.04894 0.92705C7.3483 0.00573921 8.6517 0.00573969 8.95106 0.92705L10.0206 4.21885C10.1545 4.63087 10.5385 4.90983 10.9717 4.90983H14.4329C15.4016 4.90983 15.8044 6.14945 15.0207 6.71885L12.2205 8.75329C11.87 9.00793 11.7234 9.4593 11.8572 9.87132L12.9268 13.1631C13.2261 14.0844 12.1717 14.8506 11.388 14.2812L8.58778 12.2467C8.2373 11.9921 7.7627 11.9921 7.41221 12.2467L4.61204 14.2812C3.82833 14.8506 2.77385 14.0844 3.0732 13.1631L4.14277 9.87132C4.27665 9.4593 4.12999 9.00793 3.7795 8.75329L0.979333 6.71885C0.195619 6.14945 0.598395 4.90983 1.56712 4.90983H5.02832C5.46154 4.90983 5.8455 4.63087 5.97937 4.21885L7.04894 0.92705Z" fill="#FFA439" />
                                                    </svg>
                                                    <span>{{ number_format(@$average_rating, 1) }} ({{ $total_rating }} {{ __('reviews') }})</span>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <div class="d-inline-flex align-items-center gap-4 small text-secondary mt-2">
                                            <div class="d-inline-flex align-items-center gap-1 small text-secondary">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_1660_767)">
                                                        <path d="M15.0625 0.78125H0.9375C0.420625 0.78125 0 1.20187 0 1.71875V11.0312C0 11.5481 0.420625 11.9688 0.9375 11.9688H15.0625C15.5794 11.9688 16 11.5481 16 11.0312V1.71875C16 1.20187 15.5794 0.78125 15.0625 0.78125ZM13 14.2812H10.4688V12.9062H5.53125V14.2812H3C2.74125 14.2812 2.53125 14.4913 2.53125 14.75C2.53125 15.0087 2.74125 15.2188 3 15.2188H13C13.2587 15.2188 13.4688 15.0087 13.4688 14.75C13.4688 14.4913 13.2587 14.2812 13 14.2812Z" fill="#818B9C" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_1660_767">
                                                            <rect width="16" height="16" fill="white" />
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                                <span>{{ @$instructorCoursesCount }} {{ __('Courses') }}</span>
                                            </div>
                                            <div class="d-inline-flex align-items-center gap-1 small text-secondary">
                                                <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M5.7145 5.99997C6.27968 5.99997 6.83217 5.83237 7.30209 5.51836C7.77202 5.20436 8.13827 4.75805 8.35453 4.23588C8.5708 3.71371 8.62736 3.13913 8.51706 2.58482C8.40676 2.03051 8.13456 1.52135 7.73488 1.12174C7.3352 0.722131 6.826 0.45002 6.27166 0.33982C5.71733 0.229619 5.14277 0.286278 4.62064 0.502632C4.0985 0.718986 3.65226 1.08532 3.33833 1.5553C3.02441 2.02528 2.8569 2.57779 2.857 3.14297C2.85779 3.90054 3.15912 4.62684 3.69485 5.16248C4.23058 5.69811 4.95693 5.99931 5.7145 5.99997ZM9.4525 8.36047C8.72841 7.61777 7.79936 7.10777 6.78402 6.89562C5.76869 6.68347 4.71316 6.7788 3.75226 7.16943C2.79136 7.56007 1.96873 8.22828 1.38943 9.08871C0.81013 9.94913 0.500469 10.9627 0.5 12C0.5 12.1326 0.552678 12.2598 0.646447 12.3535C0.740215 12.4473 0.867392 12.5 1 12.5H10.43C10.5626 12.5 10.6898 12.4473 10.7836 12.3535C10.8773 12.2598 10.93 12.1326 10.93 12C10.9319 11.4698 10.8501 10.9426 10.6875 10.438C10.4454 9.65778 10.0222 8.94591 9.4525 8.36047Z" fill="#818B9C" />
                                                    <path d="M11.75 6.25C12.9236 6.25 13.875 5.2986 13.875 4.125C13.875 2.95139 12.9236 2 11.75 2C10.5764 2 9.625 2.95139 9.625 4.125C9.625 5.2986 10.5764 6.25 11.75 6.25Z" fill="#818B9C" />
                                                    <path d="M11.7507 6.83502C11.0804 6.83754 10.4232 7.02007 9.84766 7.36352C9.95616 7.46052 10.0657 7.55602 10.1682 7.66102C10.849 8.3608 11.3547 9.2116 11.6442 10.144C11.7433 10.451 11.8164 10.7658 11.8627 11.085H15.0007C15.1333 11.085 15.2604 11.0323 15.3542 10.9386C15.448 10.8448 15.5007 10.7176 15.5007 10.585C15.4996 9.59079 15.1042 8.63757 14.4011 7.93454C13.6981 7.23151 12.7449 6.83608 11.7507 6.83502Z" fill="#818B9C" />
                                                </svg>
                                                <span>{{ @$totalStudent }} {{ __('Students') }}</span>
                                            </div>
                                        </div>
                                        <div class="search-instructor-bottom-item font-14 font-medium d-flex justify-content-between align-items-center">
                                            <div class="search-instructor-award-img d-inline-flex flex-wrap justify-content-center">
                                                {{-- @foreach ($user->badges as $badge)
                                                <img src="{{ asset($badge->badge_image) }}" title="{{ $badge->name }}" alt="{{ $badge->name }}"
                                                    class="fit-image rounded-circle">
                                                @endforeach --}}
                                            </div>
                                            <div class="d-flex d-md-inline-flex justify-content-start justify-content-md-end gap-2">
                                                @if ($facebook)
                                                    <a href="{{$facebook}}" aria-label="Facebook">
                                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <g clip-path="url(#clip0_1660_782)">
                                                                <path d="M9.23896 16V8.70218H11.6875L12.0549 5.85725H9.23896V4.04118C9.23896 3.21776 9.46668 2.65661 10.6488 2.65661L12.154 2.65599V0.111384C11.8937 0.0775563 11.0002 0 9.96017 0C7.78849 0 6.30172 1.32557 6.30172 3.75942V5.85725H3.8457V8.70218H6.30172V16H9.23896Z" fill="#013370" />
                                                            </g>
                                                            <defs>
                                                                <clipPath id="clip0_1660_782">
                                                                    <rect width="16" height="16" fill="white" />
                                                                </clipPath>
                                                            </defs>
                                                        </svg>
                                                    </a>
                                                @endif
                                               
                                                 @if ($linkedin)
                                                <a href="{{$linkedin}}" aria-label="LinkedIn">
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M14.3996 14.4V9.71198C14.3996 7.40798 13.9036 5.64798 11.2156 5.64798C9.91961 5.64798 9.05561 6.35198 8.70361 7.02398H8.67161V5.85598H6.12761V14.4H8.78361V10.16C8.78361 9.03998 8.99161 7.96798 10.3676 7.96798C11.7276 7.96798 11.7436 9.23198 11.7436 10.224V14.384H14.3996V14.4ZM1.80761 5.85598H4.46361V14.4H1.80761V5.85598ZM3.13561 1.59998C2.28761 1.59998 1.59961 2.28798 1.59961 3.13598C1.59961 3.98398 2.28761 4.68798 3.13561 4.68798C3.98361 4.68798 4.67161 3.98398 4.67161 3.13598C4.67161 2.28798 3.98361 1.59998 3.13561 1.59998Z" fill="#0785DF" />
                                                    </svg>
                                                </a>
                                                  @endif

                                                    @if ($twitter)
                                                <a href="{{$twitter}}" aria-label="Twitter">
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g clip-path="url(#clip0_1660_790)">
                                                            <path d="M9.4893 6.77491L15.3176 0H13.9365L8.87577 5.88256L4.8338 0H0.171875L6.28412 8.89547L0.171875 16H1.55307L6.8973 9.78782L11.1659 16H15.8278L9.48896 6.77491H9.4893ZM7.59756 8.97384L6.97826 8.08805L2.05073 1.03974H4.17217L8.14874 6.72795L8.76804 7.61374L13.9371 15.0075H11.8157L7.59756 8.97418V8.97384Z" fill="black" />
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_1660_790">
                                                                <rect width="16" height="16" fill="white" />
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </a>
                                                  @endif
                                                  @if ($pinterest)
                                                <a href="{{$pinterest}}" aria-label="Pinterest"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g clip-path="url(#clip0_1660_794)">
                                                            <path d="M11.2335 0H4.76649C2.13823 0 0 2.13823 0 4.76649V11.2335C0 13.8618 2.13823 16 4.76649 16H11.2335C13.8618 16 16 13.8618 16 11.2335V4.76649C16 2.13823 13.8617 0 11.2335 0ZM14.3904 11.2335C14.3904 12.977 12.977 14.3904 11.2335 14.3904H4.76649C3.023 14.3904 1.6096 12.977 1.6096 11.2335V4.76649C1.6096 3.02297 3.023 1.6096 4.76649 1.6096H11.2335C12.977 1.6096 14.3904 3.02297 14.3904 4.76649V11.2335Z" fill="#8A02C9" />
                                                            <path d="M8.00145 3.86182C5.71966 3.86182 3.86328 5.7182 3.86328 7.99996C3.86328 10.2817 5.71966 12.1381 8.00145 12.1381C10.2832 12.1381 12.1396 10.2817 12.1396 7.99996C12.1396 5.71817 10.2832 3.86182 8.00145 3.86182ZM8.00145 10.5286C6.60495 10.5286 5.47288 9.39649 5.47288 7.99999C5.47288 6.60348 6.60498 5.47142 8.00145 5.47142C9.39795 5.47142 10.53 6.60348 10.53 7.99999C10.53 9.39646 9.39792 10.5286 8.00145 10.5286Z" fill="#8A02C9" />
                                                            <path d="M12.1459 4.88455C12.6935 4.88455 13.1375 4.4406 13.1375 3.89296C13.1375 3.34532 12.6935 2.90137 12.1459 2.90137C11.5982 2.90137 11.1543 3.34532 11.1543 3.89296C11.1543 4.4406 11.5982 4.88455 12.1459 4.88455Z" fill="#8A02C9" />
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_1660_794">
                                                                <rect width="16" height="16" fill="white" />
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </a>
                                                  @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-3 mb-0 text-secondary" style="font-size:14px; line-height:1.6;">{{ @$user->$userRelation?->about_me }}</p>
                            </div>
                        </div>

                        <!-- Instructor Intro Video -->
                        @if(@$user->$userRelation->video && @$user->$userRelation->video_check)
                        <div class="card border-0 shadow-sm mb-30">
                            <div class="ratio ratio-16x9 bg-light position-relative rounded-3 overflow-hidden">
                                @if($user->$userRelation->video_check == 1 && getVideoFile($user->$userRelation->video))
                                <!-- Normal video file -->
                                <video class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover"
                                    controls
                                    playsinline
                                    data-poster="{{ getImageFile($user->image_path) }}"
                                    controlsList="nodownload">
                                    <source src="{{ getVideoFile($user->$userRelation->video) }}" type="video/mp4">
                                    {{ __('Your browser does not support the video tag.') }}
                                </video>
                                @elseif($user->$userRelation->video_check == 2)
                                <!-- YouTube video -->
                                @php
                                $videoUrl = getVideoUrl($user->$userRelation->video);
                                @endphp
                                <iframe class="position-absolute top-0 start-0 w-100 h-100"
                                    src="{{ $videoUrl }}"
                                    title="{{ __('Instructor Intro Video') }}"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen>
                                </iframe>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Certifications, Awards, Courses Tabs -->
                        <div class="card border-0 mb-30">
                            <div class="card-body p-0">
                                <!-- Tab Navigation -->
                                <div class="nav nav-tabs border-bottom" id="profileTabs" role="tablist">
                                    <button class="nav-link active px-4 py-3 border-0 bg-transparent" id="certifications-tab" data-bs-toggle="tab" data-bs-target="#certifications" type="button" role="tab" aria-controls="certifications" aria-selected="true">
                                        {{ __('Certifications') }}
                                    </button>
                                    <button class="nav-link px-4 py-3 border-0 bg-transparent" id="awards-tab" data-bs-toggle="tab" data-bs-target="#awards" type="button" role="tab" aria-controls="awards" aria-selected="false">
                                        {{ __('Awards') }}
                                    </button>
                                    <button class="nav-link px-4 py-3 border-0 bg-transparent" id="courses-tab" data-bs-toggle="tab" data-bs-target="#courses" type="button" role="tab" aria-controls="courses" aria-selected="false">
                                        {{ __('My Courses') }}
                                    </button>
                                </div>

                                <!-- Tab Content -->
                                <div class="tab-content mt-4" id="profileTabsContent">
                                    <!-- Certifications Tab -->
                                    <div class="tab-pane fade show active" id="certifications" role="tabpanel" aria-labelledby="certifications-tab">
                                        <div class="table-responsive certifications-table">
                                            @if(isset($user->certifications) && $user->certifications->count())
                                            <table class="table table-borderless mb-0">
                                                <thead class="border-bottom">
                                                    <tr>
                                                        <th class="px-4 py-3 text-secondary fw-normal" style="font-size: 14px;">
                                                            {{ __('Certificate Name') }}
                                                        </th>
                                                        <th class="px-4 py-3 text-secondary fw-normal" style="font-size: 14px;">
                                                            {{ __('Completed At') }}
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(isset($user->certifications) && $user->certifications->count())
                                                    @foreach($user->certifications as $certification)
                                                    <tr>
                                                        <td class="px-4 py-3 text-dark" style="font-size: 14px;">
                                                            {{ $certification->name ?? $certification->certificate_name }}
                                                        </td>
                                                        <td class="px-4 py-3 text-secondary" style="font-size: 14px;">
                                                            {{ $certification->completed_at ? \Carbon\Carbon::parse($certification->completed_at)->format('F j, Y') : 'N/A' }}
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    @else
                                                    <!-- Sample data for demonstration -->
                                                        <div class="text-center py-5">
                                                            <div class="text-muted empty-state">
                                                                <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-3">
                                                                    <path d="M32 8L38 22L54 22L42 32L46 50L32 42L18 50L22 32L10 22L26 22L32 8Z" stroke="#D1D5DB" stroke-width="2" fill="none" />
                                                                </svg>
                                                                <p class="mb-0">{{ __('No cerficates to display') }}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </tbody>
                                            </table>
                                            @else
                                                <!-- Sample data for demonstration -->
                                                    <div class="text-center py-5">
                                                        <div class="text-muted empty-state">
                                                            <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-3">
                                                                <path d="M32 8L38 22L54 22L42 32L46 50L32 42L18 50L22 32L10 22L26 22L32 8Z" stroke="#D1D5DB" stroke-width="2" fill="none" />
                                                            </svg>
                                                            <p class="mb-0">{{ __('No cerficates to display') }}</p>
                                                        </div>
                                                    </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Awards Tab -->
                                    <div class="tab-pane fade" id="awards" role="tabpanel" aria-labelledby="awards-tab">
                                        <div class="p-4">
                                            @if(isset($user->awards) && $user->awards->count())
                                            <div class="row g-3">
                                                @foreach($user->awards as $award)
                                                <div class="col-12 col-md-6">
                                                    <div class="card border-0 bg-light awards-card">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-center gap-3">
                                                                <div class="flex-shrink-0">
                                                                    <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M10 1L12.5 6.5L19 6.5L14.5 10.5L16.5 18L10 14L3.5 18L5.5 10.5L1 6.5L7.5 6.5L10 1Z" fill="white" />
                                                                        </svg>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <h6 class="mb-1" style="font-size: 14px;">{{ $award->name }}</h6>
                                                                    <p class="text-muted mb-0" style="font-size: 12px;">{{ $award->description }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                            @else
                                            <div class="text-center py-5">
                                                <div class="text-muted empty-state">
                                                    <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-3">
                                                        <path d="M32 8L38 22L54 22L42 32L46 50L32 42L18 50L22 32L10 22L26 22L32 8Z" stroke="#D1D5DB" stroke-width="2" fill="none" />
                                                    </svg>
                                                    <p class="mb-0">{{ __('No awards to display') }}</p>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- My Courses Tab -->
                                    <div class="tab-pane fade" id="courses" role="tabpanel" aria-labelledby="courses-tab">
                                        <div class="p-4">
                                            {{-- @if($user->courses && $user->courses->count()) --}}
                                            @if(!$getUserCourses->isEmpty())
                                                <div class="row g-4">
                                                    {{-- @foreach($user->courses->take(6) as $course) --}}
                                                    @foreach($getUserCourses as $course)
                                                    <div class="col-12 col-md-6 col-lg-4">
                                                    <div class="card border-0 shadow-sm h-100 course-card">
                                                        <div class="position-relative">
                                                            <img src="{{ getImageFile($course->image_path) }}" alt="{{ $course->title }}" class="card-img-top" style="height: 160px; object-fit: cover;">
                                                            @if($course->price == 0)
                                                            <span class="badge bg-success position-absolute top-0 end-0 m-2">{{ __('Free') }}</span>
                                                            @else
                                                            <span class="badge bg-primary position-absolute top-0 end-0 m-2">{{ get_currency_symbol() }}{{ $course->price }}</span>
                                                            @endif
                                                        </div>
                                                        <div class="card-body p-3">
                                                            <h6 class="card-title mb-2" style="font-size: 14px; line-height: 1.4;">{{ Str::limit($course->title, 60) }}</h6>
                                                            <div class="d-flex align-items-center gap-2 text-muted mb-2">
                                                                <small>{{ getCourseStudents($course->id,$user->id) }} {{ __('Students') }}</small>
                                                                <span>•</span>
                                                                <small>{{ $course->lectures?->count() }} {{ __('Lectures') }}</small>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-1">
                                                                <div class="text-warning">
                                                                    @for($i = 1; $i <= 5; $i++)
                                                                        @if($i <=$course->average_rating)
                                                                        <svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor">
                                                                            <path d="M6 1L7.5 4.5L11 4.5L8.5 7L9.5 10.5L6 8.5L2.5 10.5L3.5 7L1 4.5L4.5 4.5L6 1Z" />
                                                                        </svg>
                                                                        @else
                                                                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor">
                                                                            <path d="M6 1L7.5 4.5L11 4.5L8.5 7L9.5 10.5L6 8.5L2.5 10.5L3.5 7L1 4.5L4.5 4.5L6 1Z" stroke-width="1" />
                                                                        </svg>
                                                                        @endif
                                                                        @endfor
                                                                </div>
                                                                <small class="text-muted">({{ number_format($course->average_rating ?? 0, 1) }})</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                                @if($getUserCourses->count() > 6)
                                                <div class="text-center mt-4">
                                                    <a href="{{ route('courses') }}?instructor={{ $user->id }}" class="btn btn-outline-primary btn-sm">
                                                        {{ __('View All Courses') }} ({{ $getUserCourses->count() }})
                                                    </a>
                                                </div>
                                                @endif
                                            @else
                                            <div class="text-center py-5">
                                                <div class="text-muted empty-state">
                                                    <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-3">
                                                        <rect x="8" y="16" width="48" height="32" rx="4" stroke="#D1D5DB" stroke-width="2" fill="none" />
                                                        <path d="M16 24L48 24M16 32L48 32M16 40L40 40" stroke="#D1D5DB" stroke-width="2" />
                                                    </svg>
                                                    <p class="mb-0">{{ __('No courses to display') }}</p>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- <div class="review-content">
                            <div class="review-tab-top-part">
                                <h2 class="section-title mt-4">Instructor Reviews</h2>

                                <div class="rating-summary-container d-flex align-items-start gap-4">
                                    <!-- Average Rating Circle -->
                                    <div class="average-rating-circle" style="min-width: 90px;">
                                        <div class="circle-wrapper" style="border: 2px solid #f0f0f0; border-radius: 50%; width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                                            <div class="circle-value" style="font-size: 2rem; font-weight: 600; color: #222;">4.5</div>
                                        </div>
                                        <div class="rating-meta mt-2 text-center">
                                            <div class="rating-stars mb-1" style="color: #FFC107;">
                                                <span class="star filled"><span class="iconify" data-icon="bi:star-fill"></span></span>
                                                <span class="star filled"><span class="iconify" data-icon="bi:star-fill"></span></span>
                                                <span class="star filled"><span class="iconify" data-icon="bi:star-fill"></span></span>
                                                <span class="star filled"><span class="iconify" data-icon="bi:star-fill"></span></span>
                                                <span class="star filled"><span class="iconify" data-icon="bi:star-fill"></span></span>
                                            </div>
                                            <p class="rating-count mb-0" style="font-size: 13px; color: #888;">from 1,25k reviews</p>
                                        </div>
                                    </div>

                                    <!-- Rating Bars -->
                                    <div class="review-progress-bar-wrap flex-grow-1" style="min-width: 220px;">
                                        <div class="barras mb-1">
                                            <div class="progress-bar-box d-flex align-items-center">
                                                <div class="progress-hint d-flex align-items-center" style="width: 40px;">
                                                    <span style="font-weight: 500; color: #222;">5.0</span>
                                                </div>
                                                <div class="barra flex-grow-1 mx-2" style="background: #f0f0f0; border-radius: 4px; height: 8px; width: 100%;">
                                                    <div class="barra-nivel" style="background: #FFC107; width: 90%; height: 100%; border-radius: 4px;"></div>
                                                </div>
                                                <div class="progress-hint-value font-14 color-heading" style="width: 40px; text-align: right;">2823</div>
                                            </div>
                                        </div>
                                        <div class="barras mb-1">
                                            <div class="progress-bar-box d-flex align-items-center">
                                                <div class="progress-hint d-flex align-items-center" style="width: 40px;">
                                                    <span style="font-weight: 500; color: #222;">4.0</span>
                                                </div>
                                                <div class="barra flex-grow-1 mx-2" style="background: #f0f0f0; border-radius: 4px; height: 8px; width: 100%;">
                                                    <div class="barra-nivel" style="background: #FFC107; width: 60%; height: 100%; border-radius: 4px;"></div>
                                                </div>
                                                <div class="progress-hint-value font-14 color-heading" style="width: 40px; text-align: right;">38</div>
                                            </div>
                                        </div>
                                        <div class="barras mb-1">
                                            <div class="progress-bar-box d-flex align-items-center">
                                                <div class="progress-hint d-flex align-items-center" style="width: 40px;">
                                                    <span style="font-weight: 500; color: #222;">3.0</span>
                                                </div>
                                                <div class="barra flex-grow-1 mx-2" style="background: #f0f0f0; border-radius: 4px; height: 8px; width: 100%;">
                                                    <div class="barra-nivel" style="background: #FFC107; width: 10%; height: 100%; border-radius: 4px;"></div>
                                                </div>
                                                <div class="progress-hint-value font-14 color-heading" style="width: 40px; text-align: right;">4</div>
                                            </div>
                                        </div>
                                        <div class="barras mb-1">
                                            <div class="progress-bar-box d-flex align-items-center">
                                                <div class="progress-hint d-flex align-items-center" style="width: 40px;">
                                                    <span style="font-weight: 500; color: #222;">2.0</span>
                                                </div>
                                                <div class="barra flex-grow-1 mx-2" style="background: #f0f0f0; border-radius: 4px; height: 8px; width: 100%;">
                                                    <div class="barra-nivel" style="background: #FFC107; width: 2%; height: 100%; border-radius: 4px;"></div>
                                                </div>
                                                <div class="progress-hint-value font-14 color-heading" style="width: 40px; text-align: right;">0</div>
                                            </div>
                                        </div>
                                        <div class="barras mb-1">
                                            <div class="progress-bar-box d-flex align-items-center">
                                                <div class="progress-hint d-flex align-items-center" style="width: 40px;">
                                                    <span style="font-weight: 500; color: #222;">1.0</span>
                                                </div>
                                                <div class="barra flex-grow-1 mx-2" style="background: #f0f0f0; border-radius: 4px; height: 8px; width: 100%;">
                                                    <div class="barra-nivel" style="background: #FFC107; width: 0%; height: 100%; border-radius: 4px;"></div>
                                                </div>
                                                <div class="progress-hint-value font-14 color-heading" style="width: 40px; text-align: right;">0</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Review Filter Tabs -->
                                <div class="review-filter-tabs my-3">
                                    <button class="filter-tab active btn btn-primary btn-sm me-2">All Reviews</button>
                                    <button class="filter-tab btn btn-outline-secondary btn-sm me-2">Positive Reviews</button>
                                    <button class="filter-tab btn btn-outline-secondary btn-sm">Negative Reviews</button>
                                </div>

                                <!-- Review List -->
                                <div class="review-list">
                                    <!-- Review 1 -->
                                    <div class="review-item border-bottom pb-3 mb-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="me-2 d-flex gap-1" style="color: #FFC107;">
                                                <span class="iconify" data-icon="bi:star-fill"></span>
                                                <span class="iconify" data-icon="bi:star-fill"></span>
                                                <span class="iconify" data-icon="bi:star-fill"></span>
                                                <span class="iconify" data-icon="bi:star-fill"></span>
                                                <span class="iconify" data-icon="bi:star-fill"></span>
                                            </div>
                                        </div>
                                        <div class="mb-2" style="font-size: 15px;">
                                            Professor Rizwan Khan is an inspiring teacher, combining academic excellence with deep moral values. His guidance has greatly shaped my educational journey
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Adeel Ahmed" class="rounded-circle" width="32" height="32">
                                                <span style="font-weight: 500;">Adeel Ahmed</span>
                                                <span class="text-muted" style="font-size: 13px;">- Student</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="text-muted" style="font-size: 13px;">28 January 2025 03:29 PM</span>
                                                <button class="btn btn-light btn-sm d-flex align-items-center gap-1" style="font-size: 13px;">
                                                    <span class="iconify" data-icon="mdi:thumb-up-outline"></span> 128
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Review 2 -->
                                    <div class="review-item border-bottom pb-3 mb-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="me-2 d-flex gap-1" style="color: #FFC107;">
                                                <span class="iconify" data-icon="bi:star-fill"></span>
                                                <span class="iconify" data-icon="bi:star-fill"></span>
                                                <span class="iconify" data-icon="bi:star-fill"></span>
                                                <span class="iconify" data-icon="bi:star-fill"></span>
                                                <span class="iconify" data-icon="bi:star-fill"></span>
                                            </div>
                                        </div>
                                        <div class="mb-2" style="font-size: 15px;">
                                            Professor Khan is a dedicated and passionate educator. His commitment to both knowledge and ethics makes him an exceptional mentor
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Fatima Shahid" class="rounded-circle" width="32" height="32">
                                                <span style="font-weight: 500;">Fatima Shahid</span>
                                                <span class="text-muted" style="font-size: 13px;">- Student</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="text-muted" style="font-size: 13px;">28 January 2025 03:29 PM</span>
                                                <button class="btn btn-light btn-sm d-flex align-items-center gap-1" style="font-size: 13px;">
                                                    <span class="iconify" data-icon="mdi:thumb-up-outline"></span> 53
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Review 3 -->
                                    <div class="review-item border-bottom pb-3 mb-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="me-2 d-flex gap-1" style="color: #FFC107;">
                                                <span class="iconify" data-icon="bi:star-fill"></span>
                                                <span class="iconify" data-icon="bi:star-fill"></span>
                                                <span class="iconify" data-icon="bi:star-fill"></span>
                                                <span class="iconify" data-icon="bi:star-fill"></span>
                                                <span class="iconify" data-icon="bi:star-fill"></span>
                                            </div>
                                        </div>
                                        <div class="mb-2" style="font-size: 15px;">
                                            As a parent, I am grateful for Professor Khan’s positive impact on my child’s academic and personal growth. His teaching is truly exceptional!
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="https://randomuser.me/api/portraits/men/45.jpg" alt="Imran Khan" class="rounded-circle" width="32" height="32">
                                                <span style="font-weight: 500;">Imran Khan</span>
                                                <span class="text-muted" style="font-size: 13px;">- Parent</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="text-muted" style="font-size: 13px;">28 January 2025 03:29 PM</span>
                                                <button class="btn btn-light btn-sm d-flex align-items-center gap-1" style="font-size: 13px;">
                                                    <span class="iconify" data-icon="mdi:thumb-up-outline"></span> 41
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div> --}}




                        <!--Write Review Modal Start-->
                        {{-- <div class="modal fade" id="writeReviewModal" tabindex="-1" aria-labelledby="writeReviewModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h6 class="modal-title" id="writeReviewModalLabel">Write a Review</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form enctype="multipart/form-data">
                                            <div class="row mb-4">
                                                <div class="col-md-12 text-center">
                                                    <div class="btn-group give-rating-group" role="group" aria-label="Star Rating">
                                                        <input type="radio" class="btn-check" id="btncheck1" name="rating" value="1">
                                                        <label class="give-rating-star" for="btncheck1"><span class="iconify" data-icon="bi:star-fill"></span></label>
                                                        <input type="radio" class="btn-check" id="btncheck2" name="rating" value="2">
                                                        <label class="give-rating-star" for="btncheck2"><span class="iconify" data-icon="bi:star-fill"></span></label>
                                                        <input type="radio" class="btn-check" id="btncheck3" name="rating" value="3">
                                                        <label class="give-rating-star" for="btncheck3"><span class="iconify" data-icon="bi:star-fill"></span></label>
                                                        <input type="radio" class="btn-check" id="btncheck4" name="rating" value="4">
                                                        <label class="give-rating-star" for="btncheck4"><span class="iconify" data-icon="bi:star-fill"></span></label>
                                                        <input type="radio" class="btn-check" id="btncheck5" name="rating" value="5">
                                                        <label class="give-rating-star" for="btncheck5"><span class="iconify" data-icon="bi:star-fill"></span></label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row align-items-center">
                                                <div class="col-md-6 pe-0">
                                                    <div class="col-12">
                                                        <label class="label-text-title color-heading font-medium font-16 mb-3">Video (Optional)</label>
                                                    </div>
                                                    <div class="col-md-12 mb-30">
                                                        <input type="radio"
                                                            id="video_check"
                                                            class="intro_video_check"
                                                            name="intro_video_check"
                                                            value="1">
                                                        <label for="video_check">Video Upload</label><br>

                                                        <input type="radio"
                                                            id="youtube_check"
                                                            class="intro_video_check"
                                                            name="intro_video_check"
                                                            value="2">
                                                        <label for="youtube_check">Youtube Video (write only video Id or link)</label><br>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="col-md-12 mb-30">
                                                        <input type="file"
                                                            name="video"
                                                            id="video_review"
                                                            accept="video/mp4"
                                                            class="form-control d-none">
                                                        <input type="text"
                                                            name="youtube_video_id"
                                                            id="youtube_video_id"
                                                            placeholder="Type your youtube video ID or link"
                                                            value=""
                                                            class="form-control d-none">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-30">
                                                <div class="col-md-12">
                                                    <label class="font-medium font-15 color-heading">Feedback</label>
                                                    <textarea class="form-control feedback" id="exampleFormControlTextarea1" rows="3"
                                                        placeholder="Please write your feedback here"></textarea>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer d-flex justify-content-between align-items-center">
                                        <button type="button" class="theme-btn theme-button3 !text-white !rounded-md" data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" class="theme-btn theme-button1 submitReview !text-white !rounded-md">Submit review</button>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                        <!--Write Review Modal End-->
                    </div>
                </div>

                <div class="col-12 col-md-12 col-lg-4 col-xl-3">
                    <div class="instructor-stats-card">
                        <div class="card border-0 shadow-sm" style="border-radius: 12px; background: #f8f9fa;">
                            <div class="card-body p-2">
                                <!-- Stats List -->
                                <div class="instructor-stats-list">
                                    <!-- Courses -->
                                    <div class="d-flex justify-content-between align-items-center mb-3 p-2 border-bottom">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M8 8.69333L2.66667 7.16933V1.99999C2.66673 1.69048 2.73863 1.3852 2.87671 1.10819C3.01479 0.831175 3.21528 0.589992 3.4624 0.40362C3.70951 0.217248 3.99651 0.0907749 4.30081 0.034155C4.6051 -0.0224649 4.91839 -0.00768609 5.216 0.0773281L5.83267 0.199995C6.25979 0.313835 6.63782 0.564546 6.90886 0.91373C7.1799 1.26291 7.329 1.69132 7.33333 2.13333V7.11333L8 7.304L8.66667 7.11333V2.13333C8.66881 1.69951 8.81126 1.27803 9.07273 0.93185C9.33419 0.585672 9.70064 0.333379 10.1173 0.212661L10.834 0.0666614C11.1249 -0.0120233 11.4301 -0.0223967 11.7257 0.0363513C12.0213 0.0950993 12.2993 0.221379 12.538 0.405328C12.7857 0.590705 12.9867 0.831456 13.1248 1.10834C13.2629 1.38523 13.3343 1.69058 13.3333 1.99999V7.16866L8 8.69333ZM16 13.3333V4C15.9983 3.58782 15.8693 3.18624 15.6306 2.8502C15.3919 2.51415 15.0553 2.26005 14.6667 2.12266V8.17533L8 10.08L1.33333 8.17533V2.12266C0.944728 2.26005 0.608061 2.51415 0.369392 2.8502C0.130723 3.18624 0.00170864 3.58782 0 4L0 13.3333H7.33333V14.6667H4.66667V16H11.3333V14.6667H8.66667V13.3333H16Z" fill="#818B9C" />
                                                </svg>
                                            </div>
                                            <div class="stat-content">
                                                <div class="stat-label text-muted" style="font-size: 14px;">{{ __('Courses') }}</div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="stat-number text-dark" style="font-size: 18px;">{{ @$getUserCourses->count() }}</div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3 p-2 border-bottom">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M15.0625 0.78125H0.9375C0.420625 0.78125 0 1.20187 0 1.71875V11.0312C0 11.5481 0.420625 11.9688 0.9375 11.9688H15.0625C15.5794 11.9688 16 11.5481 16 11.0312V1.71875C16 1.20187 15.5794 0.78125 15.0625 0.78125ZM13 14.2812H10.4688V12.9062H5.53125V14.2812H3C2.74125 14.2812 2.53125 14.4913 2.53125 14.75C2.53125 15.0087 2.74125 15.2188 3 15.2188H13C13.2587 15.2188 13.4688 15.0087 13.4688 14.75C13.4688 14.4913 13.2587 14.2812 13 14.2812Z" fill="#818B9C" />
                                                </svg>
                                            </div>
                                            <div class="stat-content">
                                                <div class="stat-label text-muted" style="font-size: 14px;">{{ __('Video Lectures') }}</div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="stat-number text-dark" style="font-size: 18px;">{{ @$total_lectures }}</div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3 p-2 border-bottom">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M8 10.6667C7.47258 10.6667 6.95701 10.5103 6.51848 10.2173C6.07995 9.92424 5.73815 9.50776 5.53632 9.02049C5.33449 8.53322 5.28168 7.99704 5.38457 7.47976C5.48747 6.96248 5.74144 6.48732 6.11438 6.11438C6.48732 5.74144 6.96248 5.48747 7.47976 5.38457C7.99704 5.28168 8.53322 5.33449 9.02049 5.53632C9.50776 5.73816 9.92424 6.07995 10.2173 6.51848C10.5103 6.95701 10.6667 7.47258 10.6667 8C10.6667 8.70725 10.3857 9.38552 9.88562 9.88562C9.38552 10.3857 8.70724 10.6667 8 10.6667ZM3.78867 10.6667H0.666667C0.489856 10.6667 0.320286 10.5964 0.195262 10.4714C0.0702379 10.3464 0 10.1768 0 10C0.00631723 9.04274 0.354483 8.11924 0.981704 7.39605C1.60892 6.67287 2.47391 6.19762 3.42067 6.056C3.53068 6.04059 3.6428 6.05291 3.74684 6.09185C3.85088 6.13079 3.94355 6.19511 4.01641 6.27896C4.08927 6.36282 4.14002 6.46356 4.16405 6.57201C4.18808 6.68047 4.18462 6.79322 4.154 6.9C4.05184 7.25774 4.00001 7.62796 4 8C4.00019 8.59317 4.1337 9.17871 4.39067 9.71334C4.439 9.81481 4.46089 9.92686 4.4543 10.0391C4.44772 10.1513 4.41287 10.26 4.353 10.3551C4.29313 10.4502 4.21019 10.5287 4.11187 10.5831C4.01356 10.6376 3.90306 10.6664 3.79067 10.6667H3.78867ZM11.3333 16H4.66667C4.48986 16 4.32029 15.9298 4.19526 15.8047C4.07024 15.6797 4 15.5101 4 15.3333C4 14.2725 4.42143 13.2551 5.17157 12.5049C5.92172 11.7548 6.93913 11.3333 8 11.3333C9.06087 11.3333 10.0783 11.7548 10.8284 12.5049C11.5786 13.2551 12 14.2725 12 15.3333C12 15.5101 11.9298 15.6797 11.8047 15.8047C11.6797 15.9298 11.5101 16 11.3333 16ZM12 5.33333C11.4726 5.33333 10.957 5.17694 10.5185 4.88392C10.0799 4.5909 9.73816 4.17443 9.53632 3.68716C9.33449 3.19989 9.28168 2.66371 9.38457 2.14643C9.48747 1.62914 9.74144 1.15399 10.1144 0.78105C10.4873 0.40811 10.9625 0.154134 11.4798 0.0512405C11.997 -0.0516535 12.5332 0.00115542 13.0205 0.202989C13.5078 0.404823 13.9242 0.746616 14.2173 1.18515C14.5103 1.62368 14.6667 2.13925 14.6667 2.66667C14.6667 3.37391 14.3857 4.05219 13.8856 4.55229C13.3855 5.05238 12.7072 5.33333 12 5.33333ZM4 5.33333C3.47258 5.33333 2.95701 5.17694 2.51848 4.88392C2.07995 4.5909 1.73816 4.17443 1.53632 3.68716C1.33449 3.19989 1.28168 2.66371 1.38457 2.14643C1.48747 1.62914 1.74144 1.15399 2.11438 0.78105C2.48732 0.40811 2.96248 0.154134 3.47976 0.0512405C3.99704 -0.0516535 4.53322 0.00115542 5.02049 0.202989C5.50776 0.404823 5.92424 0.746616 6.21725 1.18515C6.51027 1.62368 6.66667 2.13925 6.66667 2.66667C6.66667 3.37391 6.38572 4.05219 5.88562 4.55229C5.38552 5.05238 4.70724 5.33333 4 5.33333ZM15.3333 10.6667H12.2113C12.0989 10.6664 11.9884 10.6376 11.8901 10.5831C11.7918 10.5287 11.7089 10.4502 11.649 10.3551C11.5891 10.26 11.5543 10.1513 11.5477 10.0391C11.5411 9.92686 11.563 9.81481 11.6113 9.71334C11.8676 9.17855 12.0004 8.59302 12 8C11.9989 7.62781 11.946 7.25758 11.8427 6.9C11.8119 6.79291 11.8084 6.67981 11.8326 6.57104C11.8568 6.46227 11.9078 6.36129 11.9811 6.27734C12.0544 6.19339 12.1475 6.12914 12.252 6.09048C12.3565 6.05181 12.4691 6.03996 12.5793 6.056C13.5261 6.19762 14.3911 6.67287 15.0183 7.39605C15.6455 8.11924 15.9937 9.04274 16 10C16 10.1768 15.9298 10.3464 15.8047 10.4714C15.6797 10.5964 15.5101 10.6667 15.3333 10.6667Z" fill="#818B9C" />
                                                </svg>

                                            </div>
                                            <div class="stat-content">
                                                <div class="stat-label text-muted" style="font-size: 14px;">{{ __('Students') }}</div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="stat-number text-dark" style="font-size: 18px;">{{ @$totalStudent }}</div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3 p-2 border-bottom">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="">
                                                <svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M14.5276 0.00836182H5.48906C4.67719 0.00836182 4.01666 0.668893 4.01666 1.48177C4.01666 2.29364 4.67719 2.95417 5.48906 2.95417H14.5276C15.3394 2.95417 16 2.29364 16 1.48177C16 0.668862 15.3395 0.00836182 14.5276 0.00836182ZM1.47294 0.00836182C0.660781 0.00836182 0 0.669112 0 1.48127C0 2.29342 0.660781 2.95417 1.47294 2.95417C2.28509 2.95417 2.94584 2.29342 2.94584 1.48127C2.94584 0.669112 2.28509 0.00836182 1.47294 0.00836182ZM1.47294 4.52711C0.660781 4.52711 0 5.18786 0 6.00002C0 6.81217 0.660781 7.47292 1.47294 7.47292C2.28509 7.47292 2.94584 6.81217 2.94584 6.00002C2.94584 5.18786 2.28509 4.52711 1.47294 4.52711ZM1.47294 9.04586C0.660781 9.04586 0 9.70661 0 10.5188C0 11.3309 0.660781 11.9917 1.47294 11.9917C2.28509 11.9917 2.94584 11.3309 2.94584 10.5188C2.94584 9.70661 2.28509 9.04586 1.47294 9.04586ZM14.5276 4.52711H5.48906C4.67719 4.52711 4.01666 5.18764 4.01666 6.00052C4.01666 6.81239 4.67719 7.47292 5.48906 7.47292H14.5276C15.3394 7.47292 16 6.81239 16 6.00052C16 5.18761 15.3395 4.52711 14.5276 4.52711ZM14.5276 9.04586H5.48906C4.67719 9.04586 4.01666 9.70639 4.01666 10.5193C4.01666 11.3311 4.67719 11.9917 5.48906 11.9917H14.5276C15.3394 11.9917 16 11.3311 16 10.5193C16 9.70639 15.3395 9.04586 14.5276 9.04586Z" fill="#818B9C" />
                                                </svg>

                                            </div>
                                            <div class="stat-content">
                                                <div class="stat-label text-muted" style="font-size: 14px;">{{ __('Quizzes') }}</div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="stat-number text-dark" style="font-size: 18px;">{{ @$total_quizzes }}</div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3 p-2 border-bottom">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M8.84667 4.98668C8.57826 5.25498 8.36535 5.57353 8.22007 5.92413C8.0748 6.27473 8.00002 6.65051 8 7.03002V7.66668C8 7.75509 8.03512 7.83988 8.09763 7.90239C8.16014 7.9649 8.24493 8.00002 8.33333 8.00002H8.97C9.73611 7.99991 10.4708 7.69562 11.0127 7.15402L15.4893 2.67735C16.056 2.11068 16.1647 1.18668 15.6573 0.566017C15.5221 0.399488 15.3534 0.263219 15.1621 0.166034C14.9709 0.0688494 14.7613 0.0129157 14.5471 0.00185467C14.3329 -0.00920632 14.1187 0.0248521 13.9184 0.101825C13.7182 0.178797 13.5363 0.296967 13.3847 0.448684L8.84667 4.98668ZM11.956 8.09602C11.5648 8.48943 11.0995 8.80131 10.5869 9.01359C10.0744 9.22587 9.52478 9.33433 8.97 9.33268H8C7.64638 9.33268 7.30724 9.19221 7.05719 8.94216C6.80714 8.69211 6.66667 8.35297 6.66667 7.99935V7.02935C6.66667 5.90135 7.106 4.84135 7.90333 4.04335L11.5487 0.399351C11.0732 0.140832 10.5412 0.00366515 10 1.7319e-05H3.33333C1.49533 1.7319e-05 0 1.49535 0 3.33335V12.6667C0 14.5047 1.49533 16 3.33333 16H10C11.838 16 13.3333 14.5047 13.3333 12.6667V6.71935L11.956 8.09669V8.09602ZM5.44467 11.1107L4.32 12.2354C4.05244 12.5045 3.69097 12.6595 3.31154 12.6677C2.93211 12.6759 2.56428 12.5367 2.28533 12.2794L1.83933 11.8934C1.71326 11.7755 1.63776 11.6133 1.62871 11.4409C1.61965 11.2686 1.67773 11.0994 1.79076 10.9689C1.90378 10.8385 2.06296 10.7569 2.23486 10.7413C2.40677 10.7257 2.57802 10.7774 2.71267 10.8854L3.172 11.284C3.24067 11.346 3.324 11.3454 3.37667 11.2927L4.50133 10.168C4.62634 10.043 4.79588 9.97279 4.97267 9.97279C5.14945 9.97279 5.31899 10.043 5.444 10.168C5.56901 10.293 5.63923 10.4626 5.63923 10.6394C5.63923 10.8161 5.56967 10.9857 5.44467 11.1107ZM5.44467 7.77735L4.32 8.90202C4.05244 9.17117 3.69097 9.32614 3.31154 9.33434C2.93211 9.34255 2.56428 9.20335 2.28533 8.94602L1.83933 8.56002C1.71326 8.44213 1.63776 8.27997 1.62871 8.1076C1.61965 7.93523 1.67773 7.76606 1.79076 7.6356C1.90378 7.50515 2.06296 7.42356 2.23486 7.40798C2.40677 7.39239 2.57802 7.44402 2.71267 7.55202L3.172 7.95068C3.24067 8.01268 3.324 8.01202 3.37667 7.95935L4.50133 6.83468C4.56323 6.77279 4.63671 6.72369 4.71758 6.69019C4.79845 6.65669 4.88513 6.63945 4.97267 6.63945C5.0602 6.63945 5.14688 6.65669 5.22775 6.69019C5.30862 6.72369 5.3821 6.77279 5.444 6.83468C5.5059 6.89658 5.555 6.97006 5.58849 7.05093C5.62199 7.13181 5.63923 7.21848 5.63923 7.30602C5.63923 7.39355 5.62199 7.48023 5.58849 7.5611C5.555 7.64197 5.50656 7.71546 5.44467 7.77735ZM5.44467 4.47068L4.32 5.59535C4.05244 5.86451 3.69097 6.01947 3.31154 6.02767C2.93211 6.03588 2.56428 5.89669 2.28533 5.63935L1.83933 5.25335C1.71326 5.13546 1.63776 4.97331 1.62871 4.80094C1.61965 4.62857 1.67773 4.45939 1.79076 4.32894C1.90378 4.19848 2.06296 4.11689 2.23486 4.10131C2.40677 4.08572 2.57802 4.13735 2.71267 4.24535L3.172 4.64402C3.24067 4.70668 3.324 4.70535 3.37667 4.65268L4.50133 3.52802C4.62634 3.40301 4.79588 3.33279 4.97267 3.33279C5.14945 3.33279 5.31899 3.40301 5.444 3.52802C5.56901 3.65302 5.63923 3.82257 5.63923 3.99935C5.63923 4.17614 5.56967 4.34568 5.44467 4.47068ZM10.6667 11.9994H8C7.82319 11.9994 7.65362 11.9291 7.5286 11.8041C7.40357 11.6791 7.33333 11.5095 7.33333 11.3327C7.33333 11.1559 7.40357 10.9863 7.5286 10.8613C7.65362 10.7363 7.82319 10.666 8 10.666H10.6667C10.8435 10.666 11.013 10.7363 11.1381 10.8613C11.2631 10.9863 11.3333 11.1559 11.3333 11.3327C11.3333 11.5095 11.2631 11.6791 11.1381 11.8041C11.013 11.9291 10.8435 11.9994 10.6667 11.9994Z" fill="#818B9C" />
                                                </svg>

                                            </div>
                                            <div class="stat-content">
                                                <div class="stat-label text-muted" style="font-size: 14px;">{{ __('Assignments') }}</div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="stat-number text-dark" style="font-size: 18px;">{{ @$total_assignments }}</div>
                                        </div>
                                    </div>
                                    {{-- <div class="d-flex justify-content-between align-items-center mb-3 p-2 border-bottom">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M9.95242 6H10.0004C11.1031 6 12.0004 5.10267 12.0004 4V2C12.0004 0.897333 11.1031 0 10.0004 0H6.00042C4.89775 0 4.00042 0.897333 4.00042 2V4C4.00042 5.10267 4.89775 6 6.00042 6H6.13508L7.35775 7.078C7.55108 7.24867 7.79308 7.334 8.03508 7.334C8.27375 7.334 8.51242 7.25 8.70042 7.08333L9.95242 6.00067V6ZM3.66708 12C4.95375 12 6.00042 10.9533 6.00042 9.66667C6.00042 8.38 4.95375 7.33333 3.66708 7.33333C2.38042 7.33333 1.33375 8.38 1.33375 9.66667C1.33375 10.9533 2.38042 12 3.66708 12ZM10.0004 9.66667C10.0004 10.9533 11.0471 12 12.3338 12C13.6204 12 14.6671 10.9533 14.6671 9.66667C14.6671 8.38 13.6204 7.33333 12.3338 7.33333C11.0471 7.33333 10.0004 8.38 10.0004 9.66667ZM3.66708 12.6667C2.04442 12.6667 0.588417 13.644 0.0424165 15.0993C0.00461651 15.2002 -0.00819156 15.3087 0.00508812 15.4155C0.0183678 15.5224 0.0573399 15.6245 0.11867 15.713C0.18 15.8015 0.261863 15.8739 0.357254 15.9238C0.452645 15.9738 0.558724 16 0.666417 16H6.66642C6.77405 15.9998 6.88005 15.9736 6.97535 15.9235C7.07066 15.8735 7.15245 15.8012 7.21375 15.7127C7.27505 15.6242 7.31404 15.5222 7.32739 15.4154C7.34074 15.3086 7.32805 15.2002 7.29042 15.0993C6.74508 13.644 5.28842 12.6667 3.66575 12.6667H3.66708ZM15.9584 15.0993C15.4131 13.644 13.9564 12.6667 12.3338 12.6667C10.7111 12.6667 9.25508 13.644 8.70908 15.0993C8.67128 15.2002 8.65848 15.3087 8.67175 15.4155C8.68503 15.5224 8.72401 15.6245 8.78534 15.713C8.84667 15.8015 8.92853 15.8739 9.02392 15.9238C9.11931 15.9738 9.22539 16 9.33308 16H15.3331C15.4407 15.9998 15.5467 15.9736 15.642 15.9235C15.7373 15.8735 15.8191 15.8012 15.8804 15.7127C15.9417 15.6242 15.9807 15.5222 15.9941 15.4154C16.0074 15.3086 15.9947 15.2002 15.9571 15.0993H15.9584Z" fill="#818B9C" />
                                                </svg>

                                            </div>
                                            <div class="stat-content">
                                                <div class="stat-label text-muted" style="font-size: 14px;">{{ __('Meetings') }}</div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="stat-number text-dark" style="font-size: 18px;">{{ $totalMeeting }}</div>
                                        </div>
                                    </div> --}}
                                    <div class="d-flex justify-content-between align-items-center mb-3 p-2 border-bottom">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="">
                                                <svg width="14" height="16" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M6.9997 0.0280151C5.23566 0.0299559 3.54442 0.731532 2.29699 1.97883C1.04957 3.22614 0.347821 4.91731 0.345703 6.68135C0.345703 8.39468 1.67237 11.076 4.28904 14.6507C4.60054 15.0774 5.00841 15.4246 5.47944 15.664C5.95047 15.9033 6.47135 16.028 6.9997 16.028C7.52805 16.028 8.04894 15.9033 8.51997 15.664C8.991 15.4246 9.39886 15.0774 9.71037 14.6507C12.327 11.076 13.6537 8.39468 13.6537 6.68135C13.6516 4.91731 12.9498 3.22614 11.7024 1.97883C10.455 0.731532 8.76374 0.0299559 6.9997 0.0280151ZM6.9997 9.33335C6.47229 9.33335 5.95671 9.17695 5.51818 8.88393C5.07965 8.59092 4.73786 8.17444 4.53602 7.68717C4.33419 7.1999 4.28138 6.66372 4.38428 6.14644C4.48717 5.62916 4.74115 5.154 5.11409 4.78106C5.48703 4.40812 5.96218 4.15415 6.47946 4.05126C6.99675 3.94836 7.53292 4.00117 8.02019 4.203C8.50746 4.40484 8.92394 4.74663 9.21696 5.18516C9.50997 5.62369 9.66637 6.13927 9.66637 6.66668C9.66637 7.37393 9.38542 8.0522 8.88532 8.5523C8.38522 9.0524 7.70695 9.33335 6.9997 9.33335Z" fill="#818B9C" />
                                                </svg>

                                            </div>
                                            <div class="stat-content">
                                                <div class="stat-label text-muted" style="font-size: 14px;">{{ __('Address') }}</div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="stat-number text-dark" style="font-size: 18px;">{{ @$user->$userRelation?->address }}</div>
                                        </div>
                                    </div>

                                    <!-- Follow Button -->
                                    {{-- <div class="follow-button-container">
                                        <button type="button" class="btn w-100 text-white fw-semibold" style="background-color: #1a365d; border-radius: 8px; padding: 6px 12px; font-size: 16px;">
                                            {{ __('Follow') }}
                                        </button>
                                    </div> --}}

                                    @if(!get_option('private_mode') || !auth()->guest())
                                    @if(@$user->$userRelation->consultation_available == 1)
                                    @php $hourly_fee = 0; @endphp
                                    @if(get_currency_placement() == 'after')
                                    @php $hourly_fee = @$user->$userRelation->hourly_rate . ' ' . get_currency_symbol() . '/h';
                                    @endphp
                                    @else
                                    @php $hourly_fee = get_currency_symbol() . ' ' . @$user->$userRelation->hourly_rate . '/h';
                                    @endphp
                                    @endif
                                    <div class="instructor-bottom-item mt-3">
                                        <button type="button" data-type="{{ @$user->$userRelation->available_type }}"
                                            data-booking_instructor_user_id="{{ @$user->$userRelation->user_id }}"
                                            data-hourly_fee="{{ $hourly_fee }}"
                                            data-hourly_rate="{{ @$user->$userRelation->hourly_rate }}"
                                            data-get_off_days_route="{{ route('getOffDays', @$user->$userRelation->user_id) }}"
                                            class="btn btn-outline-primary w-100 bookSchedule"
                                            data-bs-toggle="modal" data-bs-target="#consultationBookingModal">{{ __('Book Schedule') }}
                                        </button>
                                    </div>
                                    @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>
    <!-- Instructor Details Area End -->

</div>
<input type="hidden" value="3" class="course_paginate_number">
<input type="hidden" class="instructorCoursePaginateRoute" value="{{ route('instructorCoursePaginate', $user->id) }}">
<input type="hidden" class="organizationInstructorsPaginateRoute" value="{{ route('organizationInstructorPaginate', $user->id) }}">

@include('frontend.home.partial.consultation-booking-schedule-modal')
@endsection

@push('script')
<script>
    const followRoute = "{{ route('follow') }}";
    const unfollowRoute = "{{ route('unfollow') }}";
    const userInstructorId = "{{ $user->id }}";
</script>
<script src="{{ asset('frontend/assets/js/course/addToCart.js') }}"></script>
<script src="{{ asset('frontend/assets/js/course/addToWishlist.js') }}"></script>
<script src="{{ asset('frontend/assets/js/custom/instructor-course-paginate.js') }}"></script>
<script src="{{ asset('frontend/assets/js/custom/booking.js') }}"></script>
<script src="{{ asset('frontend/assets/js/custom/follow.js') }}"></script>
@endpush
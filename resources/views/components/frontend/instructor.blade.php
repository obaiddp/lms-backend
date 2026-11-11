<div class="card instructor-item search-instructor-item position-relative text-center border-0 {{ $type == INSTRUCTOR_CARD_TYPE_THREE ? 'p-0' : 'p-0' }} px-0">
    @php
    $percent = $user->hourly_rate && $user->hourly_old_rate ? (($user->hourly_old_rate -
    $user->hourly_rate) * 100) / $user->hourly_old_rate : 0;
    @endphp
    @if ($percent && $user->consultation_available == 1)
    <span class="instructor-price-cutoff badge radius-3 font-12 font-medium position-absolute bg-orange">{{
        round(@$percent) }}%
        {{ __('off') }}</span>
    @endif

    <div class="search-instructor-img-wrap mb-15 position-relative">
        <a
            href="{{ route('userProfile', $user->id) }}">
            <img src="{{ getImageFile(@$user->image_path) }}" alt="instructor"
                class="fit-image rounded-circle">
        </a>
        @if ($user->video_check)
        <button type="button" class="play-btn position-absolute top-50 start-50 translate-middle" style="background:transparent !important"
            data-bs-toggle="modal" data-bs-target="#newVideoPlayerModal_{{$user->id}}">
            <svg width="40" height="40" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="80" height="80" rx="40" fill="black" />
                <path d="M44.1563 38.4636C45.1158 39.2631 45.1158 40.7369 44.1563 41.5364L37.2804 47.2664C35.9777 48.3519 34 47.4256 34 45.7299L34 34.2701C34 32.5744 35.9777 31.6481 37.2804 32.7336L44.1563 38.4636Z" fill="white" />
            </svg>
        </button>
        @endif
    </div>

    <div class="card-body p-0">
        <h6 class="card-title aileron font-bold !mb-0"><a href="{{ route('userProfile', $user->id) }}">{{
                $user->name }}</a>
        </h6>
        <p class="card-text aileron instructor-designation font-medium !mb-0" style="font-size: 14px;">
            {{ @$user->professional_title }}
            @if(get_instructor_ranking_level($user->badges))<span class="mx-2">||</span>{{ get_instructor_ranking_level($user->badges) }}
        </p>@endif

        <?php
        $average_rating = $user->courses->where('average_rating', '>', 0)->avg('average_rating');
        ?>
        <div
            class="course-rating search-instructor-rating w-100 mb-15 d-inline-flex align-items-center justify-content-center">
            <span class="font-medium font-14 me-2">{{ number_format(@$average_rating, 1) }}</span>
            <div class="star-ratings">
                <div class="fill-ratings" style="width: {{ $average_rating * 20 }}%">
                    <span>★★★★★</span>
                </div>
                <div class="empty-ratings">
                    <span>★★★★★</span>
                </div>
            </div>
            <span class="rating-count font-14 ms-2">({{ count(@$user->courses->where('average_rating', '>', 0))
                }})</span>
        </div>
    </div>
</div>
<x-video-modal />   

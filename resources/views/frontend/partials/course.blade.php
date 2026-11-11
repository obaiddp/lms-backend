<div
    onclick="window.location.href='{{ route('course-details', $course->slug) }}'"
    class="course-item-home course-list-home {{ $course->status != STATUS_UPCOMING_APPROVED ? '' : 'course-item-upcoming' }} border-0 radius-3 bg-white w-100">
    <p
        class="course-tag-home">
        {{ $course->language->name }}
    </p>
    <a href="{{ route('course-details', $course->slug) }}"
        class="course-name-home">{{ Str::limit($course->title, 40)}}</a>
    <p class="course-rating-home">
        <span>
            <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M7.60845 0L10.0066 4.69921L15.2169 5.52786L11.4888 9.26079L12.3107 14.4721L7.60845 12.08L2.90617 14.4721L3.72814 9.26079L0 5.52786L5.21029 4.69921L7.60845 0Z"
                    fill="#FEC801" />
            </svg>
        </span>
        <span class="">{{ $course->average_rating }}</span>
    </p>
    <img src="{{getImageFile($course->image_path)}}" alt="Course"
        class="course-image-home">
    <p class="course-description-home"> {{ $course->subtitle}} </p>
    <hr class="my-2">
    <div class="course-price-home-container">
        <h2 class="course-price-home">
            @if($course->price == 0)
            Free
            @else
            {{ number_format((float) $course->price, fmod($course->price, 1) == 0 ? 0 : 2) }}
            <span class="currency-symbol">
                {{ get_currency_symbol() }}
            </span>
            @endif
        </h2>
        <div class="course-price-home-container-icon">
            <span>
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <rect x="0.5" y="0.5" width="19" height="19" rx="1.5" fill="white" />
                    <rect x="0.5" y="0.5" width="19" height="19" rx="1.5" stroke="black" />
                    <path
                        d="M11.9288 9.59143C12.2111 9.79063 12.2111 10.2093 11.9288 10.4085L7.91328 13.2421C7.58206 13.4758 7.125 13.2389 7.125 12.8335L7.125 7.16637C7.125 6.761 7.58207 6.52412 7.91328 6.75784L11.9288 9.59143Z"
                        fill="white" stroke="black" />
                </svg>
            </span>
            <span class="classes"> {{ @$course->classes > 0 ? @$course->classes : '0' }} classes</span>
        </div>
    </div>
</div>
@if (!$topCourses->isEmpty())
<section class="courses-section">
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
            <span class="section-tag-title">Our Popular Courses</span>
        </p>

        <h1 class="section-heading-home">
            A broad selection of courses
        </h1>

        <p class="section-subheading-home">Explore premium online courses with fresh, expert content added regularly.</p>

        <div class="section-home-tabs">
            <ul id="courseTabs" class="course-tabs-home" role="tablist">
                <li class="course-tab-home course-tab-active-class" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">
                    All Courses
                </li>
                @foreach ($categories as $category)
                <li class="course-tab-home" id="cat-{{ $category->id }}-tab" data-bs-toggle="tab" data-bs-target="#cat-{{ $category->id }}" type="button" role="tab" aria-controls="cat-{{ $category->id }}" aria-selected="false">
                    {{ $category->name }}
                </li>
                @endforeach
            </ul>
        </div>

        <div class="tab-content" id="courseTabsContent">
            <div class="course-container tab-pane fade active show" id="all" role="tabpanel" aria-labelledby="all-tab">
                @foreach ($topCourses as $key => $course)
                <div onclick="window.location.href='{{ route('course-details', $course->slug) }}'"
                    class="course-item-home">
                    <p class="course-tag-home">
                        {{ $course->language->name }}
                    </p>
                    <a href="{{ route('course-details', $course->slug) }}"
                        class="course-name-home">{{ $course->title }}
                    </a>
                    <p class="course-rating-home">
                        <span>
                            <svg width="16" height="15" viewBox="0 0 16 15" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.60845 0L10.0066 4.69921L15.2169 5.52786L11.4888 9.26079L12.3107 14.4721L7.60845 12.08L2.90617 14.4721L3.72814 9.26079L0 5.52786L5.21029 4.69921L7.60845 0Z"
                                    fill="#FEC801" />
                            </svg>
                        </span>
                        <span>
                            {{ $course->average_rating }}
                        </span>
                    </p>
                    <img loading="lazy" src="{{ getImageFile($course->image_path) }}" alt="Course" class="course-image-home">
                    <p class="course-description-home">
                        @php
                        if($course->short_description){
                        $resultDesc = $course->short_description;
                        }
                        else{
                        $resultDesc = $course->subtitle;
                        }
                        @endphp
                        {!! html_entity_decode(Str::limit($resultDesc, 200)) !!}
                    </p>
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
                        @if ($course->course_type == COURSE_TYPE_GENERAL)
                        <div class="course-price-home-container-icon">
                            <span>
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect x="0.5" y="0.5" width="19" height="19" rx="1.5"
                                        fill="white" />
                                    <rect x="0.5" y="0.5" width="19" height="19" rx="1.5"
                                        stroke="black" />
                                    <path
                                        d="M11.9288 9.59143C12.2111 9.79063 12.2111 10.2093 11.9288 10.4085L7.91328 13.2421C7.58206 13.4758 7.125 13.2389 7.125 12.8335L7.125 7.16637C7.125 6.761 7.58207 6.52412 7.91328 6.75784L11.9288 9.59143Z"
                                        fill="white" stroke="black" />
                                </svg>
                            </span>
                            <span class="classes">{{ @$course->classes > 0 ? @$course->classes : '0' }} Classes</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            @foreach ($topCategoryWiseCourses as $category)
            <div class="course-container tab-pane fade" id="cat-{{ $category['category']->id }}" role="tabpanel" aria-labelledby="cat-{{ $category['category']->id }}-tab">
                @foreach ($category['categoryCourses'] as $key => $course)
                <div onclick="window.location.href='{{ route('course-details', $course->slug) }}'"
                    class="course-item-home">
                    <p class="course-tag-home">
                        {{ $course->language->name }}
                    </p>
                    <a href="{{ route('course-details', $course->slug) }}"
                        class="course-name-home">{{ $course->title }}
                    </a>
                    <p class="course-rating-home">
                        <span>
                            <svg width="16" height="15" viewBox="0 0 16 15" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M7.60845 0L10.0066 4.69921L15.2169 5.52786L11.4888 9.26079L12.3107 14.4721L7.60845 12.08L2.90617 14.4721L3.72814 9.26079L0 5.52786L5.21029 4.69921L7.60845 0Z"
                                    fill="#FEC801" />
                            </svg>
                        </span>
                        <span class="">{{ $course->average_rating }}</span>
                    </p>
                    <img loading="lazy" src="{{ getImageFile($course->image_path) }}" alt="Course" class="course-image-home">
                    <p class="course-description-home">
                        @php
                        if($course->short_description){
                        $resultDesc2 = $course->short_description;
                        }
                        else{
                        $resultDesc2 = $course->subtitle;
                        }
                        @endphp
                        {!! html_entity_decode(Str::limit($resultDesc2, 200)) !!}
                    </p>
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
                        @if ($course->course_type == COURSE_TYPE_GENERAL)
                        <div class="course-price-home-container-icon">
                            <span>
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect x="0.5" y="0.5" width="19" height="19" rx="1.5"
                                        fill="white" />
                                    <rect x="0.5" y="0.5" width="19" height="19" rx="1.5"
                                        stroke="black" />
                                    <path
                                        d="M11.9288 9.59143C12.2111 9.79063 12.2111 10.2093 11.9288 10.4085L7.91328 13.2421C7.58206 13.4758 7.125 13.2389 7.125 12.8335L7.125 7.16637C7.125 6.761 7.58207 6.52412 7.91328 6.75784L11.9288 9.59143Z"
                                        fill="white" stroke="black" />
                                </svg>
                            </span>
                            <span class="classes">{{ @$course->lectures->count() > 0 ? @$course->video_duration : '0' }} Classes</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach
        </div>

        <div class="view-all-btn-home">
            <a href="{{ route('courses') }}" class="view-all-btn-home-btn" aria-current="page">
                {{ __('View All Courses') }}
            </a>
        </div>
    </div>
</section>
@endif

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const tabs = document.querySelectorAll("#courseTabs li");

        tabs.forEach(tab => {
            tab.addEventListener("click", function() {
                tabs.forEach(t => t.classList.remove("course-tab-active-class"));

                this.classList.add("course-tab-active-class");
            });
        });
    });

    document.addEventListener("DOMContentLoaded", function() {
        const tabs = document.querySelectorAll(".course-tab-home");
        const allTabContent = document.getElementById("all");

        tabs.forEach(tab => {
            tab.addEventListener("click", function() {
                if (this.id === "all-tab") {
                    allTabContent.classList.add("show", "active");
                } else {
                    allTabContent.classList.remove("show", "active");
                }
            });
        });
    });
</script>
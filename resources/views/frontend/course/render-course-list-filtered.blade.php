

<div class="row courses-grids px-4" id="appendCourseList">

@forelse($courses as $course)
    <!-- Course item start -->
    @php
        $userRelation = getUserRoleRelation($course->user);
     
    @endphp
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4 px-2 my-3">
        @include('frontend.partials.course')
    </div>
    <!-- Course item end -->
@empty
    <div class="no-course-found text-center">
        <img src="{{ asset('frontend/assets/img/empty-data-img.png') }}" alt="img" class="img-fluid">
        <h5 class="mt-3">{{ __('Courses Not Found') }}</h5>
    </div>
@endforelse

<!-- Course item end -->
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileFilterSelect = document.querySelector('.mobile-filter-select');
        const coursesLeftSidebar = document.querySelector('.coursesLeftSidebar');
        const filterCloseMobile = document.querySelector('.filter-close-mobile');
        
        if (mobileFilterSelect) {
            mobileFilterSelect.addEventListener('click', function() {
                if (coursesLeftSidebar) {
                    coursesLeftSidebar.style.display = 'block';
                    coursesLeftSidebar.style.zIndex = '999';
                    coursesLeftSidebar.style.background = 'white';
                    coursesLeftSidebar.style.width = '100%';
                    coursesLeftSidebar.style.height = '100vh';
                    coursesLeftSidebar.style.overflow = 'auto';
                    document.body.style.overflow = 'hidden';
                }
            });
        }
        
        if (filterCloseMobile) {
            filterCloseMobile.addEventListener('click', function() {
                if (coursesLeftSidebar) {
                    coursesLeftSidebar.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            });
        }
    });
</script>

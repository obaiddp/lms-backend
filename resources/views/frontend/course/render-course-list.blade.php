
<div class="courses-filter-wrap">
    <form action="#" class="mx-2">
        <div class="input-group">
        
            <input class="form-control me-2 searchCourse" id="searchCourse" type="search" name="keyword"
                value="" placeholder="{{__('Search Course')}}..."
                aria-label="Search">
                <span class="input-group-text" id="basic-addon1">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9.0625 15.625C12.6869 15.625 15.625 12.6869 15.625 9.0625C15.625 5.43813 12.6869 2.5 9.0625 2.5C5.43813 2.5 2.5 5.43813 2.5 9.0625C2.5 12.6869 5.43813 15.625 9.0625 15.625Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M13.7031 13.7031L17.5 17.5" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <div class="search-bar-suggestion-box searchBox d-none custom-scrollbar">
                    <ul class="appendCourseSearchList">
                    </ul>
                </div>
                <!-- Search Bar Suggestion Box End -->
        </div>
   
      
    </form>

    <div class="filter-select">
        <label for="filterby"> Filter by: </label>
        <select id="filterby" name="filterby" class="form-control filterSortBy" aria-label="Default select example">
            <option value="1" selected>All</option>
            <option value="2">Newest</option>
            <option value="3">Oldest</option>
        </select>
    </div>
    <div class="mobile-filter-select d-flex">
        <svg class="sidebar-svg" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M22.5 10.5H1.5C0.671578 10.5 0 11.1716 0 12C0 12.8284 0.671578 13.5 1.5 13.5H22.5C23.3284 13.5 24 12.8284 24 12C24 11.1716 23.3284 10.5 22.5 10.5ZM1.5 6.50003H22.5C23.3284 6.50003 24 5.82845 24 5.00003C24 4.17161 23.3284 3.50003 22.5 3.50003H1.5C0.671578 3.50003 0 4.17161 0 5.00003C0 5.82845 0.671578 6.50003 1.5 6.50003ZM22.5 17.5H1.5C0.671578 17.5 0 18.1716 0 19C0 19.8284 0.671578 20.5 1.5 20.5H22.5C23.3284 20.5 24 19.8284 24 19C24 18.1716 23.3284 17.5 22.5 17.5Z" fill="black"/>
        </svg>
    </div>
    
</div>
<div class="row courses-grids ps-0 pe-2 md:!ps-4 mt-3" id="appendCourseList">

@forelse($courses as $course)
    <!-- Course item start -->
    @php
        $userRelation = getUserRoleRelation($course->user);
     
    @endphp
    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4 px-2 my-3 !ms-0 !ps-5 !pe-0 !sm:pe-12 xl:!px-1 xl:!my-1">
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
<div class="col-12">
    @if($courses->hasPages() && $courses->count() > 0)
        <!-- Load More Button-->
        <div class="d-flex justify-content-center align-items-center" >
            <button  id="loadMoreBtn" type="button" class="theme-btn theme-button2 load-more-btn d-flex">{{ __('Load More') }} <span class="iconify" data-icon="icon-park-outline:loading-one"></span></button>
        </div>
    @endif
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

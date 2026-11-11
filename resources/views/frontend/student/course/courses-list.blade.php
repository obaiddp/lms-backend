@extends('frontend.layouts.app')

<div>

@section('navbar-header')
{{__('My Courses')}}
@endsection

@section('content2')
<!-- Wishlist Page Area Start -->
<section class="wishlist-page-area my-courses-page">
    <div class="row">
            <!-- Courses Filter Bar Start-->
            <div class="col-12">
                <div class="courses-filter-bar d-flex align-items-start justify-content-between">
                    <!-- <div class="filter-bar-left">
                        <a href="{{ route('courses') }}" class="theme-btn theme-button1 theme-button3">{{__('Browse More Course')}}</a>
                    </div> -->

                    {{-- <div class="filter-bar-right">
                        <div class="filter-box d-flex align-items-center justify-content-end bg-white">
                            <div class="filter-box-short-icon d-flex justify-content-start align-items-center"><img src="{{ asset('frontend/assets/img/icons-svg/filter-list-icon.png') }}" alt="short"> <p>{{ __('Sort By') }}:</p></div>
                            <select class="form-select form-select-sm filterBy" >
                                <option value="">{{__('Select')}}</option>
                                <option value="1">{{__('Newest')}}</option>
                                <option value="2">{{__('Oldest')}}</option>
                            </select>
                        </div>
                    </div> --}}
                </div>
            </div>
            <!-- Courses Filter Bar End-->

            
            <div class="course-single-details-left-content mt-0">

                <!-- Tab panel nav list -->
                <div class="course-tab-nav-wrap course-details-tab-nav-wrap d-flex justify-content-between">
                    <ul class="nav nav-tabs tab-nav-list border-0" id="myTab" role="tablist">
                        {{-- <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Tab 1</a>
                        </li> --}}

                        <li class="nav-item" role="presentation">
                            <a class="nav-link text-uppercase {{!$action_type ? 'active' : '' }}"
                                id="self-learning-tab" data-bs-toggle="tab" href="#self_learning" role="tab"
                                aria-controls="self_learning" aria-selected="true">{{__('Self Learning')}}</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link text-uppercase" id="live-tab" data-bs-toggle="tab"
                                href="#live" role="tab" aria-controls="live"
                                aria-selected="true">{{__('Live')}}</a>
                        </li>
                    </ul>
                </div>
                <!-- Tab panel nav list -->

            </div>

            {{-- <div class="appendCourseList"> --}}
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="self_learning" role="tabpanel" aria-labelledby="self-learning-tab">
                    @include('frontend.student.course.render-courses-list')
                </div>
                <div class="tab-pane fade" id="live" role="tabpanel" aria-labelledby="live-tab">
                    @include('frontend.student.course.render-courses-list-live')
                </div>
            
            </div>
            {{-- </div> --}}
    </div>
</section>
<!-- Wishlist Page Area End -->

    <!--Write Review Modal Start-->
    <div class="modal fade" id="writeReviewModal" tabindex="-1" aria-labelledby="writeReviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="writeReviewModalLabel">{{ __('Write a Review') }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row mb-4">
                            <div class="col-md-12 text-center">
                                <div class="btn-group give-rating-group" role="group" aria-label="Basic checkbox toggle button group">
                                    <input type="checkbox" class="btn-check" id="btncheck1" name="rating">
                                    <label class="give-rating-star" for="btncheck1"><span class="iconify" data-icon="bi:star-fill"></span></label>

                                    <input type="checkbox" class="btn-check" id="btncheck2" name="rating">
                                    <label class="give-rating-star" for="btncheck2"><span class="iconify" data-icon="bi:star-fill"></span></label>

                                    <input type="checkbox" class="btn-check" id="btncheck3" name="rating">
                                    <label class="give-rating-star" for="btncheck3"><span class="iconify" data-icon="bi:star-fill"></span></label>

                                    <input type="checkbox" class="btn-check" id="btncheck4" name="rating">
                                    <label class="give-rating-star" for="btncheck4"><span class="iconify" data-icon="bi:star-fill"></span></label>

                                    <input type="checkbox" class="btn-check" id="btncheck5" name="rating">
                                    <label class="give-rating-star" for="btncheck5"><span class="iconify" data-icon="bi:star-fill"></span></label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-30">
                            <div class="col-md-12">
                                <label class="font-medium font-15 color-heading">{{ __('Feedback') }}</label>
                                <textarea class="form-control feedback" id="exampleFormControlTextarea1" rows="3" placeholder="{{ __('Please write your feedback here') }}"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-between align-items-center">
                    <button type="button" class="theme-btn theme-button3" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="theme-btn theme-button1 submitReview">{{ __('Submit Review') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!--Write Review Modal End-->
    
    <!--Refund Modal Start-->
    <div class="modal fade" id="refundModal" tabindex="-1" aria-labelledby="refundModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="refundModalLabel">{{ __('Refund Request') }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row mb-30">
                            <div class="col-md-12">
                                <input type="hidden" name="refund_id" id="refund-id" value="">
                                <label class="font-medium font-15 color-heading">{{ __('Refund Amount') }}</label>
                                <input type="text" class="form-control" disabled value="" id="refund-amount">
                            </div>
                            <div class="col-md-12 mt-15">
                                <label class="font-medium font-15 color-heading">{{ __('Reason') }}</label>
                                <textarea class="form-control" id="refund-feedback" rows="3" placeholder="{{ __('Please write your refund request reason') }}"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-between align-items-center">
                    <button type="button" class="theme-btn theme-button3" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="theme-btn theme-button1 submitRefund">{{ __('Refund Request') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!--Refund Modal End-->

</div>
<input type="hidden" class="course_id">
<input type="hidden" class="studentReviewCreateRoute" value="{{ route('student.review.create') }}">
<input type="hidden" class="studentRefundRequestRoute" value="{{ route('student.refund.request') }}">
<input type="hidden" class="courseMyLearningRoute" value="{{ route('student.my-learning') }}">
@endsection

@push('script')
    <script src="{{ asset('frontend/assets/js/course/student-my-learning-filter.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/course/course-review-create.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/course/course-refund-request.js') }}"></script>
@endpush

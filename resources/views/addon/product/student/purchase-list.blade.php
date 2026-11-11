@extends('frontend.layouts.app')

@push('style')
<link rel="stylesheet" href="{{ asset('addon/product/css/ecommerce-product.css') }}">
<link rel="stylesheet" href="{{ asset('frontend/assets/css/dashboard.css') }}">
@endpush

@section('navbar-header')
{{__('My Products')}}
@endsection

@section('content2')
<div>

<!-- Wishlist Page Area Start -->
<section class="wishlist-page-area my-courses-page mt-4 mx-2">
    <div>
        <div class="row">
            <!-- Courses Filter Bar Start-->
            <div class="col-12">
                <div class="courses-filter-bar d-flex align-items-start justify-content-end">
                    <!-- <div class="filter-bar-left">
                        <a href="{{ route('lms_product.frontend.list') }}" class="theme-btn theme-button1 theme-button3">{{__('Browse More Product')}}</a>
                    </div> -->

                    <div class="filter-bar-right">
                        <div class="filter-box d-flex align-items-center justify-content-end bg-white">
                            <div class="filter-box-short-icon d-flex justify-content-start align-items-center"><img src="{{ asset('frontend/assets/img/icons-svg/filter-list-icon.png') }}" alt="short"> <p>{{ __('Sort By') }}:</p></div>
                            <select class="form-select form-select-sm filterBy" >
                                <option value="">{{__('Select')}}</option>
                                <option value="1">{{__('Newest')}}</option>
                                <option value="2">{{__('Oldest')}}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Courses Filter Bar End-->

            <div class="appendCourseList">
            @include('addon.product.partials.purchase-list')
            </div>
        </div>
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

</div>
<input type="hidden" class="product_id">
<input type="hidden" class="studentReviewCreateRoute" value="{{ route('lms_product.student.review.create') }}">
<input type="hidden" class="courseMyProductRoute" value="{{ route('lms_product.student.purchase_list') }}">
@endsection
@push('script')
    <script src="{{ asset('addon/product/js/my-product-filter.js') }}"></script>
    <script src="{{ asset('addon/product/js/product-review-create.js') }}"></script>
@endpush

<script>
    document.addEventListener('DOMContentLoaded', function() {
            // Select all anchor (<a>) tags in the document
            var links = document.querySelectorAll('a');

            // Add an event listener to each link
            links.forEach(function(link) {
                link.addEventListener('contextmenu', function(event) {
                    // Prevent the context menu from appearing
                    event.preventDefault();
                });
            });
        });
</script>

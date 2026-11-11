<div class="review-content">
    <div class="review-tab-top-part">
        <h2 class="section-title mt-4">Students Feedback</h2>
        
        <div class="rating-summary-container">
            <!-- Average Rating Circle -->
            <div class="average-rating-circle">
                <div class="circle-wrapper">
                    <div class="circle-value">{{ number_format($course->average_rating, 1) }}</div>
                </div>
                <div class="rating-meta">
                    <div class="rating-stars">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="star {{ $i <= $course->average_rating ? 'filled' : '' }}">
                                <span class="iconify" data-icon="bi:star-fill"></span>
                            </span>
                        @endfor
                    </div>
                    <p class="rating-count">from {{ number_format($total_user_review) }} reviews</p>
                </div>
            </div>

            <!-- Rating Bars -->
            <div class="review-progress-bar-wrap pl-4">
                <!-- Progress Bar -->
                <div class="barras">
                    <div class="progress-bar-box d-flex align-items-center">
                        <div class="progress-hint">
                            <div class="progress-star d-flex align-items-center me-2">
                                <div class="hint-number me-3 font-medium color-heading">5</div>
                                <ul class="rating-list d-flex align-items-center">
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="barra">
                            <div class="barra-nivel" data-nivel="{{ number_format($five_star_percentage, 2) }}%"></div>
                        </div>
                        <div class="progress-hint-value font-14 color-heading">{{ $five_star_count }}</div>
                    </div>
                </div>
                <!-- Progress Bar -->
                <div class="barras">
                    <div class="progress-bar-box d-flex align-items-center">
                        <div class="progress-hint">
                            <div class="progress-star d-flex align-items-center me-2">
                                <div class="hint-number me-3 font-medium color-heading">4</div>
                                <ul class="rating-list d-flex align-items-center">
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li><span class="iconify" data-icon="bi:star-fill"></span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="barra">
                            <div class="barra-nivel" data-nivel="{{ number_format($four_star_percentage, 2) }}%"></div>
                        </div>
                        <div class="progress-hint-value font-14 color-heading">{{ $four_star_count }}</div>
                    </div>
                </div>
                <!-- Progress Bar -->
                <div class="barras">
                    <div class="progress-bar-box d-flex align-items-center">
                        <div class="progress-hint">
                            <div class="progress-star d-flex align-items-center me-2">
                                <div class="hint-number me-3 font-medium color-heading">3</div>
                                <ul class="rating-list d-flex align-items-center">
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li><span class="iconify" data-icon="bi:star-fill"></span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="barra">
                            <div class="barra-nivel" data-nivel="{{ number_format($three_star_percentage, 2) }}%"></div>
                        </div>
                        <div class="progress-hint-value font-14 color-heading">{{ $three_star_count }}</div>
                    </div>
                </div>
                <!-- Progress Bar -->
                <div class="barras">
                    <div class="progress-bar-box d-flex align-items-center">
                        <div class="progress-hint">
                            <div class="progress-star d-flex align-items-center me-2">
                                <div class="hint-number me-3 font-medium color-heading">2</div>
                                <ul class="rating-list d-flex align-items-center">
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li><span class="iconify" data-icon="bi:star-fill"></span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="barra">
                            <div class="barra-nivel" data-nivel="{{ number_format($two_star_percentage, 2) }}%"></div>
                        </div>
                        <div class="progress-hint-value font-14 color-heading">{{ $two_star_count }}</div>
                    </div>
                </div>
                <!-- Progress Bar -->
                <div class="barras">
                    <div class="progress-bar-box d-flex align-items-center">
                        <div class="progress-hint">
                            <div class="progress-star d-flex align-items-center me-2">
                                <div class="hint-number me-3 font-medium color-heading">1</div>
                                <ul class="rating-list d-flex align-items-center">
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li><span class="iconify" data-icon="bi:star-fill"></span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="barra">
                            <div class="barra-nivel" data-nivel="{{ number_format($first_star_percentage, 2) }}%"></div>
                        </div>
                        <div class="progress-hint-value font-14 color-heading">{{ $first_star_count }}</div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Review Filter Tabs -->
        {{-- <div class="review-filter-tabs">
            <button class="filter-tab active">All Reviews</button>
            <button class="filter-tab">Positive Reviews</button>
            <button class="filter-tab">Negative Reviews</button>
        </div> --}}

        <!-- Review List -->
        <div class="review-list">
            <div id="appendReviews">
                @include('frontend.student.course.partial.render-partial-review-list')
            </div>

            @if(count($loadMoreShowButtonReviews) > 3)
                <!-- Load More Button-->
                <div class="load-more-container" id="loadMoreBtn">
                    <button class="load-more-btn loadMore">{{ __('Load More') }} <span class="iconify" data-icon="icon-park-outline:loading-one"></span></button>
                </div>
            @endif
        </div>
    </div>

    @if (@Auth::user() && $authRole == 3)
        <div class="d-flex justify-content-center justify-content-lg-between align-items-center mt-3">

            <button type="button" data-bs-toggle="modal" data-bs-target="#writeReviewModal" class="btn-review-new">{{__('Write a review')}}</button>

            {{-- <div class="courses-pagination">
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center mt-0">
                    <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.4702 4.07992L8.95019 10.5999C8.18019 11.3699 8.18019 12.6299 8.95019 13.3999L15.4702 19.9199" stroke="#141414" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.95019 4.07992L15.4702 10.5999C16.2402 11.3699 16.2402 12.6299 15.4702 13.3999L8.9502 19.9199" stroke="#141414" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </li>
                </ul>
            </nav>
            </div> --}}
        </div>
    @endif





     <!--Write Review Modal Start-->
    <div class="modal fade" id="writeReviewModal" tabindex="-1" aria-labelledby="writeReviewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="writeReviewModalLabel">{{__('Write a Review')}}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form enctype="multipart/form-data">
                        <div class="row mb-4">
                            <div class="col-md-12 text-center">
                        
                                <div class="btn-group give-rating-group" role="group" aria-label="Star Rating">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <input type="radio" class="btn-check" id="btncheck{{ $i }}" name="rating" value="{{ $i }}">
                                        <label class="give-rating-star" for="btncheck{{ $i }}"><span class="iconify" data-icon="bi:star-fill"></span></label>
                                    @endfor
                                </div>

                            </div>
                        </div>



                        <div class="row align-items-center">
                            <div class="col-md-6 pe-0">
                                    <div class="col-12">
                                            <label
                                                class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Video') }}
                                            ({{ __('Optional') }})</label>
                                    </div>
                                    <div class="col-md-12 mb-30">
                                        <input type="radio"
                                                id="video_check"
                                                class="intro_video_check"
                                                name="intro_video_check"
                                                value="1">
                                        <label for="video_check">{{ __('Video Upload') }}</label><br>
                                        
                                        <input type="radio"
                                                id="youtube_check"
                                                class="intro_video_check"
                                                name="intro_video_check"
                                                value="2">
                                        <label for="youtube_check">{{ __('Youtube Video') }}
                                                        ({{ __('write only video Id or link') }})</label><br>
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
											placeholder="{{ __('Type your youtube video ID or link') }}"
											value="{{ $course->youtube_video_id }}"
											class="form-control d-none">
                                </div>
                            </div>
                        </div>


                        <div class="row mb-30">
                            <div class="col-md-12">
                                <label class="font-medium font-15 color-heading">{{__('Feedback')}}</label>
                              
                                <textarea class="form-control feedback" id="exampleFormControlTextarea1" rows="3"
                                    placeholder="Please write your feedback here">{{ old('feedback') }}</textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-between align-items-center">
                    <button type="button" class="theme-btn theme-button3 !text-white !rounded-md" data-bs-dismiss="modal">{{__('Cancel')}}</button>
                    <button type="button" class="theme-btn theme-button1 submitReview !text-white !rounded-md">{{__('Submit review')}}</button>
                </div>
            </div>
        </div>
    </div>
    <!--Write Review Modal End-->

    </div>
</div>







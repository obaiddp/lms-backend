@extends('layouts.instructor')

@section('breadcrumb')
				<div class="page-banner-content text-center">
								<h3 class="page-banner-heading text-white pb-15"> {{ __('Upload Self Learning Course') }} </h3>

								<!-- Breadcrumb Start-->
								<nav aria-label="breadcrumb">
												<ol class="breadcrumb justify-content-center">
																<li class="breadcrumb-item font-14"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
																<li class="breadcrumb-item font-14"><a href="{{ route('admin.course.index') }}">{{ __('My Courses') }}</a>
																</li>
																<li class="breadcrumb-item font-14 active"
																				aria-current="page">{{ __('Upload Course') }}</li>
												</ol>
								</nav>
				</div>
@endsection

@section('content2')
				<div class="instructor-profile-right-part instructor-upload-course-box-part">
								<div class="instructor-upload-course-box">
												<div class="row">
																<div class="col-12">
																				<div class="card">
																								<div id="msform">
																												<!-- progressbar -->
																												<ul id="progressbar"
																																class="upload-course-item-block d-flex align-items-center justify-content-center">
																																<li class="active"
																																				id="account"><strong>{{ __('Overview') }}</strong></li>
																																<li class="active"
																																				id="quiz"><strong>{{ __('Quiz') }}</strong></li>
																																<li class="active"
																																				id="assignment"><strong>{{ __('Assignment') }}</strong></li>
																																<li class="active"
																																				id="personal"><strong>{{ __('Content') }}</strong></li>
																																<li class="active"><strong>{{ __('Instructors') }}</strong></li>
																																<li class="active"
																																				id="confirm"><strong>{{ __('Submit Process') }}</strong></li>
																												</ul>

																												<!-- Upload Course Step-1 Item Start -->
																												<div class="upload-course-step-item upload-course-overview-step-item">
																																<!-- Upload Course Step-3 Item Start -->
																																<div class="upload-course-step-item">
																																				<div class="upload-course-item-block course-overview-step1 radius-8 mb-0 pb-0">
																																								<div class="form-last-step">
																																												<div class="last-step-content-wrap">
																																																<h4 class="mb-3">Finish!</h4>
																																																<div class="stepper-action-btns">
																																																				<a href="{{ route('admin.course.index') }}"
																																																							class="theme-btn theme-button3">{{ __('Cancel') }}</a>
																																																				@if ($course->status == 1)
																																																								<a href="{{ route('admin.course.upload-finished', [$course->uuid]) }}"
																																																											type="button"
																																																											class="theme-btn theme-button1">{{ __('Done') }}</a>
																																																				@else
																																																								<a style="width: max-content !important; border-radius: 0.4rem !important;"
																																																											href="{{ route('admin.course.upload-finished', [$course->uuid]) }}"
																																																											type="button"
																																																											class="theme-btn theme-button1">{{ __('Submit for review') }}</a>
																																																				@endif
																																																</div>
																																												</div>
																																								</div>
																																				</div>
																																</div>
																																<!-- Upload Course Step-3 Item End -->
																												</div>
																								</div>
																				</div>
																</div>
												</div>
								</div>
				</div>
@endsection

<style>
				.close-btn-add-course {
								position: absolute;
								top: 1rem !important;
								right: 0.4rem !important;
								width: 3rem !important;
				}

				a.close-btn2-add-course {
								display: flex;
								justify-content: center;
								align-items: center;
								min-width: 1.5rem !important;
								height: 1.5rem !important;
								max-width: 1.5rem !important;
								border-radius: 100%;
				}

				ul#progressbar {
								position: sticky;
								top: 5rem !important;
								z-index: 998;
								background: white;
				}
</style>

@extends('layouts.instructor')

@section('breadcrumb')
				<div class="page-banner-content text-center">
								<h3 class="page-banner-heading text-white pb-15"> {{ __('Quiz') }} </h3>

								<!-- Breadcrumb Start-->
								<nav aria-label="breadcrumb">
												<ol class="breadcrumb justify-content-center">
																<li class="breadcrumb-item font-14"><a href="{{ route('instructor.dashboard') }}">{{ __('Dashboard') }}</a>
																</li>
																<li class="breadcrumb-item font-14 active"
																				aria-current="page">{{ __('Quiz List') }}</li>
												</ol>
								</nav>
				</div>
@endsection

@section('content2')
				<div class="instructor-profile-right-part">
								<div class="instructor-quiz-list-page">

												<!-- progressbar -->
												<ul id="progressbar"
																class="upload-course-item-block d-flex align-items-center justify-content-center">
																<li class="active"
																				id="account"><strong>{{ __('Overview') }}</strong></li>
																<li class="active"
																				id="quiz"><strong>{{ __('Quiz') }}</strong></li>
																<li id="assignment"><strong>{{ __('Assignment') }}</strong></li>
																<li id="personal"><strong>{{ __('Content') }}</strong></li>
																<li id="instructor"><strong>{{ __('Instructors') }}</strong></li>
																<li id="confirm"><strong>{{ __('Submit Process') }}</strong></li>
												</ul>

												<div class="row m-0 quiz-list-page-top mb-4">
																<div class="col-md-7">
																				<div class="quiz-list-page-top-left">
																								<h5 class="text-white mb-2"> {{ __('Course Name') }}: {{ $course->title }}</h5>
																								<p class="text-white mb-4">{{ __('Quiz List') }}</p>
																								<a href="{{ route('admin.exam.create', [$course->uuid]) }}"
																											class="create-new-quiz-btn font-medium">{{ __('Create New Quiz') }}</a>
																				</div>
																</div>

																<div class="col-md-5">
																				<div class="quiz-list-page-top-right">
																								<img src="{{ asset('frontend/assets/img/quiz-img/quiz-text-img.png') }}"
																													alt="img"
																													class="img-fluid">
																				</div>
																</div>
												</div>

												<div class="row">
																<div class="col-12">
																				@if ($exams->count() > 0)
																								<div class="table-responsive">
																												<table class="table">
																																<thead>
																																				<tr>
																																								<th scope="col">{{ __('Quiz Name') }}</th>
																																								<th scope="col">{{ __('Quiz Types') }}</th>
																																								<th scope="col">{{ __('Total Question') }}</th>
																																								<th scope="col">{{ __('Passing Percentage') }}</th>
																																								<th scope="col">{{ __('Status') }}</th>
																																								<th scope="col">{{ __('Add Question') }}</th>
																																								<th scope="col">{{ __('Action') }}</th>
																																				</tr>
																																</thead>
																																<tbody>
																																				@foreach ($exams as $exam)
																																								<tr>
																																												<td>{{ $exam->name }}</td>
																																												<td>{{ ucfirst(str_replace('_', ' ', $exam->type)) }}</td>
																																												<td>{{ $exam->questions->count() }}</td>
																																												<td>{{ $exam->passing_percentage }}</td>
																																												<td>
																																																@if ($exam->status == 1)
																																																				<div class="quiz-status">{{ __('Publish') }}</div>
																																																@else
																																																				<div class="quiz-status unpublish">{{ __('Unpublish') }}</div>
																																																@endif
																																												</td>
																																												<td><a style="width: max-content !important; border-radius: 0.4rem !important;"
																																																			href="{{ route('admin.exam.question', [$exam->uuid, $course->uuid]) }}"
																																																			class="add-question-btn theme-btn theme-button1 default-hover-btn">{{ __('Add Question') }}</a>
																																												</td>
																																												<td>
																																																<div class="dropdown">
																																																				<button type="button"
																																																												id="dropdownMenuButton1"
																																																												data-bs-toggle="dropdown"
																																																												aria-expanded="false">
																																																								<span class="iconify"
																																																														data-icon="charm:menu-meatball"></span>
																																																				</button>
																																																				<ul class="dropdown-menu dropdown-menu-end"
																																																								aria-labelledby="dropdownMenuButton1">

																																																								@if ($exam->status == 1)
																																																												<li><a class="dropdown-item"
																																																																			href="{{ route('admin.exam.status-change', [$exam->uuid, 0]) }}"><span
																																																																										class="iconify"
																																																																										data-icon="ic:outline-publish"></span>{{ __('Unpublish') }}</a>
																																																												</li>
																																																								@else
																																																												<li><a class="dropdown-item"
																																																																			href="{{ route('admin.exam.status-change', [$exam->uuid, 1]) }}"><span
																																																																										class="iconify"
																																																																										data-icon="ic:outline-publish"></span>{{ __('Publish') }}</a>
																																																												</li>
																																																								@endif
																																																								<li><a class="dropdown-item"
																																																															href="{{ route('admin.exam.view', [$exam->uuid]) }}"><span
																																																																						class="iconify"
																																																																						data-icon="carbon:view"></span>{{ __('View') }}</a>
																																																								</li>
																																																								<li><a class="dropdown-item"
																																																															href="{{ route('admin.exam.edit', [$exam->uuid]) }}"><span
																																																																						class="iconify"
																																																																						data-icon="clarity:note-edit-line"></span>{{ __('Edit') }}</a>
																																																								</li>
																																																								<li><a href="{{ route('admin.exam.delete', [$exam->uuid]) }}"
																																																															class="dropdown-item"><span class="iconify"
																																																																						data-icon="gg:trash"></span>{{ __('Delete') }}</a>
																																																								</li>
																																																				</ul>
																																																</div>
																																												</td>
																																								</tr>
																																				@endforeach

																																</tbody>
																												</table>
																								</div>
																				@else
																								<!-- If there is no data Show Empty Design Start -->
																								<div class="empty-data">
																												<img src="{{ asset('frontend/assets/img/empty-data-img.png') }}"
																																	alt="img"
																																	class="img-fluid">
																												<h5 class="my-3">{{ __('Empty Quiz') }}</h5>
																								</div>
																								<!-- If there is no data Show Empty Design End -->
																				@endif

																</div>
												</div>

								</div>

								<br>
								<div class="stepper-action-btns d-flex justify-content-end align-items-center add-more-lesson-box p-0 mr-0">
												<div>
																@if (auth()->user()->role == 2)
																				<a href="{{ route('instructor.course.edit', [$course->uuid, 'step=category']) }}"
																							class="theme-btn theme-button3">{{ __('Back') }}</a>
																@elseif(auth()->user()->role == 1)
																				<a href="{{ route('admin.course.edit', [$course->uuid, 'step=category']) }}"
																							class="theme-btn theme-button3">{{ __('Back') }}</a>
																@endif
																{{-- @if ($course->lectures->count() > 0) --}}
																@if (auth()->user()->role == 2)
																				<a style="width: max-content !important; border-radius: 0.4rem !important;"
																							href="{{ route('instructor.course.edit', [$course->uuid, 'step=assignment']) }}"
																							type="button"
																							class="theme-btn theme-button1">{{ __('Save and continue') }}</a>
																@elseif(auth()->user()->role == 1)
																				<a style="width: max-content !important; border-radius: 0.4rem !important;"
																							href="{{ route('admin.course.edit', [$course->uuid, 'step=assignment']) }}"
																							type="button"
																							class="theme-btn theme-button1">{{ __('Save and continue') }}</a>
																@endif
																{{-- @endif --}}
												</div>
								</div>
				</div>

@endsection

@push('style')
				<!-- Video Player css -->
				<link rel="stylesheet"
										href="{{ asset('frontend/assets/vendor/video-player/plyr.css') }}">

				<!-- Summernote CSS - CDN Link -->
				<link href="{{ asset('common/css/summernote/summernote.min.css') }}"
										rel="stylesheet">
				<link href="{{ asset('common/css/summernote/summernote-lite.min.css') }}"
										rel="stylesheet">
				<!-- //Summernote CSS - CDN Link -->
@endpush

@push('script')
				<script>
								$(function() {
												'use strict'
												$('.editLesson').on('click', function(e) {
																e.preventDefault();
																$('#lessonName').val($(this).data('name'))
																let route = $(this).data('route');

																$('#updateEditModal').attr("action", route)
												})
								})
				</script>
				<script src="{{ asset('frontend/assets/js/custom/form-validation.js') }}"></script>
				<script src="{{ asset('frontend/assets/js/custom/upload-lesson.js') }}"></script>
				<script src="{{ asset('frontend/assets/js/custom/index.js') }}"></script>

				<!-- Video Player js -->
				<script src="{{ asset('frontend/assets/vendor/video-player/plyr.js') }}"></script>
				<script>
								"use strict"
								const zai_player = new Plyr('#player');
								const zai_player1 = new Plyr('#playerVideoYoutube');
								const zai_player2 = new Plyr('#playerVideoHTML5');
								const zai_player3 = new Plyr('#playerVideoVimeo');
				</script>
@endpush
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

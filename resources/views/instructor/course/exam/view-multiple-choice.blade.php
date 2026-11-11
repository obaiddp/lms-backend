@extends('layouts.instructor')

@section('breadcrumb')
    <div class="page-banner-content text-center">
        <h3 class="page-banner-heading text-white pb-15"> {{__('Quiz')}} </h3>

        <!-- Breadcrumb Start-->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item font-14"><a href="{{route('admin.dashboard')}}">{{__('Dashboard')}}</a></li>
                <li class="breadcrumb-item font-14"><a href="{{route('admin.course.edit', [$exam->course->uuid, 'step=quiz'])}}">{{ __('Quiz List') }}</a></li>
                <li class="breadcrumb-item font-14 active" aria-current="page">{{ __('Multiple Choice') }}</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content2')
    <div class="instructor-profile-right-part">
        <div class="instructor-quiz-list-page instructor-quiz-details-page">

            <div class="instructor-my-courses-title d-flex justify-content-between align-items-center ">
                <h6> {{$exam->name}}</h6>
                <p>{{ @$exam->course->title }}</p>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">{{ __('Question') }}</th>
                                <th scope="col">{{ __('Option 01') }}</th>
                                <th scope="col">{{ __('Option 02') }}</th>
                                <th scope="col">{{ __('Option 03') }}</th>
                                <th scope="col">{{ __('Option 04') }}</th>
                                <th scope="col">{{ __('Action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($exam->questions->count() > 0)
                                @foreach($exam->questions as $key => $question)
                                    <tr>
                                        <td>{{\Illuminate\Support\Str::words($question->name, 1)}}</td>
                                        @foreach($question->options as $key_option => $option)
                                            <td> @if($option->is_correct_answer == 'yes') <span class="iconify" data-icon="bi:check-lg"></span> @endif {{\Illuminate\Support\Str::words($option->name, 2)}}</td>
                                        @endforeach
                                        <td>
                                            <div class="quiz-details-action-btns">
                                                <a  href="{{route('admin.exam.edit-mcq', [$question->uuid])}}"  class="quiz-details-btn"><span class="iconify" data-icon="bxs:edit"></span></a>
                                                <a href="javascript:void(0);" data-url="{{route('admin.exam.delete-question', [$question->uuid])}}" class="quiz-details-btn delete"><span class="iconify" data-icon="gg:trash"></span></a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif

                            </tbody>
                        </table>
                    </div>
                    <a href="{{route('admin.course.edit', [$exam->course->uuid, 'step=quiz'])}}" class="theme-btn theme-button3 quiz-back-btn default-hover-btn">{{ __('Back to Quiz') }}</a>
                    <a href="{{route('admin.course.edit', [$exam->course->uuid, 'step=lesson'])}}" class="theme-btn theme-button3 quiz-back-btn default-hover-btn">{{ __('Back to Lesson') }}</a>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('script')
    <script src="{{asset('frontend/assets/js/custom/form-validation.js')}}"></script>
@endpush




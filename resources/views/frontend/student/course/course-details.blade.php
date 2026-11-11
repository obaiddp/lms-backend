@extends('frontend.layouts.app')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

@section('content2')
@section('navbar-header')
@if ($action_type == 'quiz-result' || $action_type == 'start-quiz' || $action_type == 'leaderboard')
<span ><a href={{route('student.my-course.show',['slug'=>$course->slug])}}>{{__($course->title)}}</a></span>
@else
{{ __($course->title) }}
@endif

@endsection

@section('navbar-header-right')
    <ul class="course-watch-banner-items">
        <li
            class="font-14 font-medium color-heading {{ $course->course_type == COURSE_TYPE_GENERAL ? '' : 'd-none' }}">
            <span class="iconify me-2" data-icon="carbon:video"></span>{{
            @$course->lectures->count() }}
            {{ __('Lectures') }}
        </li>
        <li
            class="font-14 font-medium color-heading {{ $course->course_type == COURSE_TYPE_GENERAL ? '' : 'd-none' }}">
            <span class="iconify me-2" data-icon="akar-icons:book"></span>{{
            @$course->lessons->count() }}
            {{ __('sections') }}
        </li>
        <li class="font-14 font-medium color-heading"><span class="iconify me-2"
                data-icon="ant-design:clock-circle-outlined"></span>
            @if($course->course_type == COURSE_TYPE_GENERAL)
            {{@$course->lectures->count() > 0 ? @$course->video_duration : '0 min'}}
            @else
            {{@$course->scorm_course->duration ? $course->scorm_course->duration : '0 min'}}
            @endif
        </li>
    </ul>
@endsection

@if(!empty(get_option('certificate_regular')) && get_option('certificate_regular') != '' )
@push('style')
<style>
    .certificateFont {
        @font-face {
            font-family: "certificateFont";
            url: {{ asset(get_option('certificate_font')) }},
        }
    }

    .formatted-text-box {
    line-height: 1.6; /* Controls the line spacing */
    text-align: justify; /* Aligns the text evenly */
    font-size: 16px;
    font-family: Arial, sans-serif;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    white-space: normal;
    word-wrap: break-word;
    line-height: 1.6;
    }

    .card-text {
        border: 2px solid #ccc;
        border-radius: 10px;
        padding: 20px;
        background-color: #f9f9f9;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        max-width: 600px;
        margin: 20px auto;
    }

    .card-body-text {
        display: flex;
        flex-direction: column;
    }

</style>
@endpush
@endif

<div>

    <!-- Course Single Details Area Start -->
    <section class="course-single-details-area course-watch-page-area mt-3 mx-2">
            <div class="row">                <!--div-row-start -->

                <div class="col-md-12">     <!--div-row-col-md-8-start -->
                    <!-- Tab panel nav list -->
                    <div class="course-tab-nav-wrap course-details-tab-nav-wrap d-flex justify-content-between">
                        <ul class="nav nav-tabs tab-nav-list border-0" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link text-uppercase {{!$action_type ? 'active' : '' }}"
                                    id="Overview-tab" data-bs-toggle="tab" href="#Overview" role="tab"
                                    aria-controls="Overview" aria-selected="true">{{__('Overview')}}</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link text-uppercase" 
                                    id="Content-tab" data-bs-toggle="tab" href="#Content" role="tab"
                                    aria-controls="Content" aria-selected="true">{{__('Content')}}</a>
                            </li>
                            {{-- <li class="nav-item" role="presentation">
                                <a class="nav-link text-uppercase" id="Resources-tab" data-bs-toggle="tab"
                                    href="#Resources" role="tab" aria-controls="Resources"
                                    aria-selected="true">{{__('Resources')}}</a>
                            </li> --}}
                            @if($course->course_type == COURSE_TYPE_GENERAL)
                            <li class="nav-item" role="presentation">
                                <a class="nav-link text-uppercase {{$action_type ? 'active' : '' }}" id="Quiz-tab"
                                    data-bs-toggle="tab" href="#Quiz" role="tab" aria-controls="Quiz"
                                    aria-selected="false">{{__('Quiz')}}</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link text-uppercase" id="Assignment-tab" data-bs-toggle="tab"
                                    href="#Assignment" role="tab" aria-controls="Quiz"
                                    aria-selected="false">{{__('Assignment')}}</a>
                            </li>
                            @endif
                            <li class="nav-item" role="presentation">
                                <a class="nav-link text-uppercase" id="Notice-tab" data-bs-toggle="tab"
                                    href="#Notice" role="tab" aria-controls="Notice"
                                    aria-selected="false">{{__('Notice')}}</a>
                            </li>
                            @if ($course->course_type == 3)
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link text-uppercase" id="LiveClass-tab" data-bs-toggle="tab"
                                        href="#LiveClass" role="tab" aria-controls="LiveClass"
                                        aria-selected="false">{{__('Live Class')}}</a>
                                </li>
                            @endif
                           
                            <li class="nav-item" role="presentation">
                                <a class="nav-link text-uppercase" id="Discussion-tab" data-bs-toggle="tab"
                                    href="#Discussion" role="tab" aria-controls="Discussion"
                                    aria-selected="false">{{__('Discussion')}}</a>
                            </li>
                            @if($course->course_type == COURSE_TYPE_GENERAL)
                            {{-- <li class="nav-item" role="presentation">
                                <a class="nav-link text-uppercase" id="Certificate-tab" data-bs-toggle="tab"
                                    href="#Certificate" role="tab" aria-controls="Certificate"
                                    aria-selected="false">{{__('Certificate')}}</a>
                            </li> --}}
                            @endif
                            <li class="nav-item" role="presentation">
                                <a class="nav-link text-uppercase" id="Review-tab" data-bs-toggle="tab"
                                    href="#Review" role="tab" aria-controls="Review"
                                    aria-selected="false">{{__('Review')}}</a>
                            </li>
                        </ul>
                    </div>
                            <!-- Tab panel nav list -->
                    <div
                        class="col-12 col-md-12 col-lg-12 {{ $course->course_type == COURSE_TYPE_GENERAL ? 'col-xl-12 col-xxl-12' : '' }}">
                        <div class="course-single-details-left-content mt-0">

                            <!-- Tab Content-->
                            <div class="tab-content" id="myTabContent">
                                @include('frontend.student.course.partial.partial-overview-tab')
                                @include('frontend.student.course.partial.partial-content-tab')
                                @include('frontend.student.course.partial.partial-resources-tab')
                                @include('frontend.student.course.partial.partial-review-tab')
                                @include('frontend.student.course.partial.partial-quiz-tab')
                                @include('frontend.student.course.partial.partial-assignment-tab')
                                @include('frontend.student.course.partial.partial-notice-tab')
                                @include('frontend.student.course.partial.partial-liveclass-tab')
                                @include('frontend.student.course.partial.partial-discussion-tab')
                                @include('frontend.student.course.partial.partial-certificate-tab')
                            </div>

                            <div class="course-watch-inner-title-wrap d-flex justify-content-between align-items-start mt-30">
                                <div class="course-watch-inner-title-left-part">
                                    <!-- <h4 class="mb-3 lectureName">{{__('Introduction')}}</h4> -->
                                    <div class="course-watch-enrolled-wrap d-flex">
                                        <ul>
                                            @foreach($enrolled_students as $enrolled_student)
                                            <li><img src="{{ getImageFile(@$enrolled_student->user->image_path) }}" alt=""></li>
                                            @endforeach
                                        </ul>
                                        <div class="enrolled-count font-12 ms-2"><span class="color-heading font-medium">{{
                                                $total_enrolled_students }}</span> <span
                                                class="d-block text-uppercase">{{__('Enrolled')}}</span>
                                        </div>
                                    </div>  
                                </div>

                                <div class="course-watch-inner-title-right-part">
                                    <div class="publish-update-time text-end">
                                        <h6 class="font-14">{{__('Last update')}}</h6>
                                        <p class="font-12">{{ $course->updated_at->format('d M Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  <!--div-row-col-md-8-end -->

             


            </div>    <!--div-row-end -->
    </section>

    <!-- Course Single Details Area End -->

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
                    <form>
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
                    <button type="button" class="theme-btn theme-button1 submitReview !rounded-md">{{__('Submit review')}}</button>
                </div>
            </div>
        </div>
    </div>
    <!--Write Review Modal End-->
    
    
</div>

{{-- <input type="hidden" id="base_url" value="{{url('/')}}"> --}}
<input type="hidden" class="course_id" value="{{ @$course->id}}">
<input type="hidden" class="normalVideoSource" value="{{ @$video_src }}">
<input type="hidden" class="youTubeVideoSource" value="{{ @$youtube_video_src }}">
<input type="hidden" class="vimeoVideoSource" value="{{ @$vimeo_video_src }}">
<input type="hidden" class="lecture_id" value="{{ @$lecture_id_check }}">
<input type="hidden" class="enrollment_id" value="{{ @@$enrollment->id }}">
<input type="hidden" class="videoCompletedRoute" value="{{  route('student.video.completed') }}">
<input type="hidden" class="certificateSaveRoute" value="{{  route('student.save-certificate') }}">
@if(@$nextLectureUuid)
<input type="hidden" class="nextLectureRoute"
    value="{{  route('student.my-course.show', [$course->slug, 'lecture_uuid' => $nextLectureUuid]) }}">
@endif
<input type="hidden" class="nextLectureId" value="{{  @$nextLectureId }}">

<input type="hidden" class="studentReviewCreateRoute" value="{{ route('student.review.create') }}">
<input type="hidden" class="studentReviewPaginateRoute" value="{{ route('student.reviewPaginate', $course->id) }}">



<input type="hidden" class="studentAssignmentListRoute" value="{{ route('student.assignment-list') }}">
@endsection

@push('style')
<!-- Video Player css -->
<link rel="stylesheet" href="{{ asset('frontend/assets/vendor/video-player/plyr.css') }}">
@endpush

@push('script')




<script src="{{ asset('frontend/assets/vendor/jquery/jquery-3.6.0.min.js') }}"></script>

<script src="{{ asset('frontend/assets/js/tinymce.min.js') }}"></script>
<script src="{{ asset('frontend/assets/js/tinymce-script.js') }}"></script>
<script src="{{ asset('frontend/assets/js/vimeo/api/player.js') }}"></script>
<script src="{{ asset('frontend/assets/js/custom/rating.js') }}"></script>
<script src="{{ asset('frontend/assets/js/course/student-review-paginate.js') }}"></script>
<script src="{{ asset('frontend/assets/js/course/student-assignment.js') }}"></script>
<script src="{{ asset('frontend/assets/js/course/zoom-copy-url.js') }}"></script>



<script>
    "use strict"


    $(".start-conversation-btn-wrap > button").on("click", function () {
        // $('.main-navigation').toggleClass('open');
        $(".discussion-righ-wrap").toggleClass("show-editor");
        return false;
    });


    $(".intro_video_check").click(function() {
                    var intro_video_check = $("input[name='intro_video_check']:checked").val();
                  
                    introVideoCheck(intro_video_check);
    });


        function introVideoCheck(intro_video_check) {
         
            if (intro_video_check == 1) {
             
                            $('#video_review').removeClass('d-none');
                            $('.videoSource').removeClass('d-none');
                            $('.videoSourceYoutube').addClass('d-none');
                            $('#youtube_video_id').addClass('d-none');
            }
            if (intro_video_check == 2) {
              
                            $('#video_review').addClass('d-none');
                            $('.videoSource').addClass('d-none');
                            $('.videoSourceYoutube').removeClass('d-none');
                            $('#youtube_video_id').removeClass('d-none');
            }
        }
                            
</script>



<script>
     //  var base_url = $('#base_url').val();
     var base_url = window.location.origin;
    $(document).ready(function () {
    
        ajaxCallfirstStudent(); // To output when the page loads
        setInterval(ajaxCallfirstStudent, (50 * 1000)); // x * 1000 to get it in seconds
    });

$(document).on("click", ".create_zoom_meeting_link_liveclass", function(event){
    event.preventDefault();
    meetingId = $(this).attr("data-meeting_id");
    userId = $(this).attr("data-user_id");
    lessonId = $(this).attr("data-lesson_id");
    if(userId == null){
        userId = 'NewUser_'+ (new Date()).getTime();
    }
    userName = $(this).attr("data-user_name");
    if(userName == null){
        userName = 'New_Student';
    }
    var res = userName.replace(/\s+/g, "");
    userType = $(this).attr("data-user_type");
    var murl = '/'+'student/NewZoomMeetingLiveClass/'+meetingId+'/'+userType+'/'+res+'/'+userId+'/'+lessonId;
    var currentHref = murl;
    window.open(currentHref, '_self');
    // window.location.href = currentHref;
 
});

function ajaxCallfirstStudent() {
    var base_url = window.location.origin;
    $.ajax({
        type: "POST",
        url: base_url+"/student/checkLessonStartTimeLiveClass",
        data: {"_token": "{{ csrf_token() }}"},
        dataType: "JSON",
        success: (function (result) {
                    console.log("ENTERED INSIDE NOW");
            $('#lesson_id').val(result.l_id);                       
            if(result.learning_tool == 'zoom'){
                $('#lec_link1_'+result.l_id).attr('href', result.url); 
                $('#lec_link1_'+result.l_id).attr('target', '_self'); 
                $('#lec_link1_'+result.l_id).html("Start Class"); 
                $('#lec_link1_'+result.l_id).css("background-color", "#499619"); 
                $('#lec_link1_'+result.l_id).css("color", "#fff"); 
                $('#lec_link1_'+result.l_id).css("padding", "5px 10px"); 
                $('#lec_link1_'+result.l_id).attr('data-lesson_id', result.l_id); 
                $('#lec_link1_'+result.l_id).attr('data-meeting_id', result.meeting_id); 
                $('#lec_link1_'+result.l_id).attr('data-user_name', result.user_name); 
                $('#lec_link1_'+result.l_id).attr('data-user_id', result.user_id); 
                $('#lec_link1_'+result.l_id).attr('data-user_type', result.user_type); 
                $('#lec_link1_'+result.l_id).addClass('create_zoom_meeting_link_liveclass');
            }
            else if(result.learning_tool == 'bbb'){
                console.log("ENTERED INSIDE BBB Now");
                        $('#lec_link1_'+result.l_id).attr('href', result.url); 
                        $('#lec_link1_'+result.l_id).attr('target', '_blank'); 
                        $('#lec_link1_'+result.l_id).html("Start Class"); 
                        $('#lec_link1_'+result.l_id).css("padding", "5px 10px"); 
                        $('#lec_link1_'+result.l_id).css("background-color", "#499619"); 
                        $('#lec_link1_'+result.l_id).css("color", "#fff"); 
            }
            IsmeetingCreated = $('#lec_link1_'+result.l_id).attr('data-meeting_created'); 
            if(IsmeetingCreated != 'Yes'){
                var audioElement = document.createElement('audio');
            }
            $('#lec_link1_'+result.l_id).attr('data-meeting_created', 'Yes'); 
        }),
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
            console.log("Status: " + textStatus)
            console.log("Error: " + errorThrown)
        }    
    })
}
</script>

<script>
    "use strict"
        //Youtube Video duration done;
        var course_id = $('.course_id').val();
        var lecture_id = $('.lecture_id').val();
        var enrollment_id = $('.enrollment_id').val();
        var videoCompletedRoute = $('.videoCompletedRoute').val();
        var certificateSaveRoute = $('.certificateSaveRoute').val();
        var nextLectureRoute = $('.nextLectureRoute').val();
        var youTubeVideoSource = $('.youTubeVideoSource').val()

        var tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

        var player;
        var duration = 0;
        var interval;






        function reloadWithQueryParams(baseRoute,lectureUUID, href = null) {
         
        
            const currentUrl = baseRoute;
          
            // Define additional query parameters you want to append
            const additionalParams = '?lecture_uuid='+lectureUUID;

            //  // Check if the current URL already has query parameters
            // const separator = currentUrl.includes('?') ? '&' : '?';

            //  // Construct the new URL with additional parameters
            var newUrl = currentUrl + additionalParams;
            var resourceURL = href;
            $.ajax({
                type: "GET",
                url: newUrl,
                // data: {
                //     'lecture_uuid' => lectureUUID,
                // },
                // dataType: "JSON",
                success: function (response) {
                    // alert(resourceURL)
                    window.location.href = resourceURL;
                    // setTimeout(() => {
                       
                    // }, 100); // Adjust timeout if needed
                }
            });
          
         
         
        }


        function onYouTubeIframeAPIReady() {
            player = new YT.Player('playerVideoYoutube', {
                height: '626',
                width: '100%',
                videoId: youTubeVideoSource,
                events: {
                    'onReady': onPlayerReady,
                    'onStateChange': onPlayerStateChange
                }
            });
        }

        function onPlayerReady(event) {
            event.target.playVideo();
        }

        var done = false;

        function onPlayerStateChange(event) {
            if (event.data == YT.PlayerState.ENDED) {
                callCompleteCourse();
            }
        }

    @if($course->course_type == COURSE_TYPE_SCORM)

        function timerHandler() {
            duration++;
            if(duration > 30){
                $.post("{{ route('student.my-course.completed_duration', $course->id) }}", {duration : duration, _token: $('meta[name="csrf-token"]').attr('content')}).then(resData => {
                    if(typeof resData.data.html != 'undefined' && resData.data.html != null ){
                        $('#demo-certificate').html(resData.data.html).promise().then(function(){
                            saveToServer(resData.data.certificate_number);
                        });
                    }
                });

                duration = 1;
            }
        }

        // Start timer
        function startTimer() {
            if($(document.activeElement).hasClass('scorm-content')){
                interval = window.setInterval(timerHandler, 1000);
            }
            else{
                clearInterval(interval);
                interval = false;
            }
        }

        // Stop timer
        function stopTimer() {
            clearInterval(interval);
            interval = false;
        }

        (function() {
            var hidden = "hidden";

            // Standards:
            if (hidden in document)
                document.addEventListener("visibilitychange", onchange);
            else if ((hidden = "mozHidden") in document)
                document.addEventListener("mozvisibilitychange", onchange);
            else if ((hidden = "webkitHidden") in document)
                document.addEventListener("webkitvisibilitychange", onchange);
            else if ((hidden = "msHidden") in document)
                document.addEventListener("msvisibilitychange", onchange);
            // IE 9 and lower:
            else if ("onfocusin" in document)
                document.onfocusin = document.onfocusout = onchange;
            // All others:
            else
                window.onpageshow = window.onpagehide
                = window.onfocus = window.onblur = onchange;

            function onchange (evt) {
                var v = "visible",
                    h = "hidden",
                    evtMap = {
                        focus: v,
                        focusin: v,
                        pageshow: v,
                        blur: h,
                        focusout: h,
                        pagehide: h
                    };

                evt = evt || window.event;
                if (evt.type in evtMap) {
                    if(evtMap[evt.type] == 'visible'){
                        startTimer();
                    }
                    else{
                        stopTimer();
                    }
                } else {
                    if(this[hidden]){
                        stopTimer();
                    }
                    else{
                        startTimer();
                    }
                }
            }

            // set the initial state (but only if browser supports the Page Visibility API)
            if( document[hidden] !== undefined ){
                onchange({type: document[hidden] ? "blur" : "focus"});
            }

            window.addEventListener('load', startTimer);
        })();
    @endif

</script>
<script src="{{ asset('frontend/assets/js/html2canvas.js') }}"></script>
<script src="{{ asset('frontend/assets/js/custom/course-video-ended.js') }}"></script>
<!-- Video Player js -->
<script src="{{ asset('frontend/assets/vendor/video-player/plyr.js') }}"></script>
<script>
    const zai_player = new Plyr('#player');
        const zai_player1 = new Plyr('#player1');
        const zai_player2 = new Plyr('#playerVideoYoutube');
        const zai_player3 = new Plyr('#youtubePlayer');
        const zai_player4 = new Plyr('#playerVideoVimeo');
        const zai_player5 = new Plyr('#audioplayer');

        const backBtn = document.getElementById( "back-btn" );
        const resContainer = document.getElementById( "resourcesContainer" );
        const catContainer = document.getElementById( "resourcesCategoriesContainer" );




backBtn.addEventListener( 'click', function ()
        {
            if ( resContainer.style.display === 'flex' )
            {
                catContainer.style.display = 'none';
                catContainer.style.transition = 'transform 0.5s ease, opacity 0.5s ease 0s';
                catContainer.style.opacity = '0.1';
                // catContainer.style.display = 'flex';
                catContainer.style.display = 'block';
                resContainer.style.transition = 'transform 0.5s ease, opacity 0.5s ease';
                resContainer.style.transform = 'scale(0.8)';
                resContainer.style.opacity = '0';
                resContainer.style.display = 'none';
                // cn.style.display = 'none';
                // cName.style.display = 'none';
                setTimeout( () =>
                {
                    catContainer.style.transition = 'transform 0.5s ease, opacity 0.5s ease 0s';
                    backBtn.style.display = 'none';
                    catContainer.style.opacity = '1';
                    catContainer.style.transform = 'scale(1)';
                }, 200 );
            }
        }
);



function toggleIcon ( courseBox )
{
    // alert("1234")
    backBtn.style.display = 'flex';
    catContainer.style.transition = 'transform 0.5s ease, opacity 0.5s ease';
    catContainer.style.transform = 'scale(1.1)';
    catContainer.style.opacity = '0';
    resContainer.style.transition = 'transform 0.5s ease, opacity 0.5s ease';
    resContainer.style.transform = 'scale(0.8)';
    resContainer.style.opacity = '0.1';

    setTimeout( () =>
            {
                resContainer.style.transition = 'transform 0.5s ease, opacity 0.5s ease';
                resContainer.style.transform = 'scale(1)';
                resContainer.style.opacity = '1';
                catContainer.style.display = 'none';
                resContainer.style.display = 'flex';
            }, 300 );
}

  
document.querySelectorAll( '.course-box' ).forEach( courseBox =>
        {
            courseBox.addEventListener( 'click', function ()
            {
                toggleIcon( courseBox );
            } );
        } );

function showResources ( categoryID ,courseID )
        {
            var resourcesContainer = document.getElementById( 'resourcesContainer' );
            resourcesContainer.innerHTML = '';
            $.ajax({
    type: "POST",
    url: "{{ route('student.resource.get_by_course_category') }}",
    data: {"_token": "{{ csrf_token() }}",category_id:categoryID , course_id:courseID},
    // url: "/tutor/GetStudentNames/"+classId,
            dataType: "JSON",
            success: (function (result) {
                console.log(result)
                if ( !$.isEmptyObject( result ) )
                {
                    var resourcesContainer = document.getElementById( 'resourcesContainer' );
                    resourcesContainer.innerHTML = '';
                    result.forEach( function ( resource )
                        {
                            var resourceID = resource.resource_id;
                            var resource_name = resource.resource_name;
                            var resource_ext = resource.resource_ext;
                            var resource_path = resource.resource_path;
                            var resourceType = resource.resource_type;

                          
                            if(resourceType == 'Text' || resourceType == 'Youtube'){
                                var url = "{{ route('student.resource.view_full_other') }}/"+resourceID;
                            }
                            else{
                                var url = "{{ route('student.resource.view_full') }}/"+resource_ext+'/'+resource_path;
                            }
                            alert(url)
                            // var url = "{{ route('student.resource.view_full') }}/"+resource_ext+'/'+resource_path;

                            var resourceBox = document.createElement( 'div' );
                            resourceBox.className = 'resource-box';
                            resourceBox.innerHTML = '<a class="resource-link" target ="_blank" href ="' + url + '" >' + '<span class="folder"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><g fill="none"><path d="M24 0v24H0V0zM12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035c-.01-.004-.019-.001-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427c-.002-.01-.009-.017-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093c.012.004.023 0 .029-.008l.004-.014l-.034-.614c-.003-.012-.01-.02-.02-.022m-.715.002a.023.023 0 0 0-.027.006l-.006.014l-.034.614c0 .012.007.02.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"/><path fill="#89bd40" d="M13.586 2a2 2 0 0 1 1.284.467l.13.119L19.414 7a2 2 0 0 1 .578 1.238l.008.176V20a2 2 0 0 1-1.85 1.995L18 22H6a2 2 0 0 1-1.995-1.85L4 20V4a2 2 0 0 1 1.85-1.995L6 2zM12 4H6v16h12V10h-4.5A1.5 1.5 0 0 1 12 8.5zm2 .414V8h3.586z"/></g></svg></span>' + resource_name + '</a>';

                            resourcesContainer.appendChild( resourceBox );
                          
                        });
                        setTimeout( () =>
            {
                $( '#resourcesContainer' ).attr( 'hidden', null );
                $( '#resourcesContainer' ).show();
            }, 300 );
                }
                else{
                  
                    var resourcesContainer = document.getElementById( 'resourcesContainer' );
                    resourcesContainer.innerHTML = '';
                            var resourceBox = document.createElement( 'div' );
                            resourceBox.className = 'resource-box';
                            resourceBox.innerHTML = 'This Course Category Has No Resources Allocated Yet!!!';
                            resourcesContainer.appendChild( resourceBox );
                            $( '#resourcesContainer' ).attr( 'hidden', null );
                            $( '#resourcesContainer' ).show();
                }
            })
        })
        }


</script>
<!-- Video Player js -->

<script type="text/javascript">
    'use strict';
	//For Scorm course body
	$(document).ready(function(){
	  var width = $('#scorm_player').width();
	  $('#scorm_player').attr("height", width/2);
	  window.onresize = function(event) {
	    var width = $('#scorm_player').width();
	    $('#scorm_player').attr("height", width/2);
	  };

        @if(isset($certificateData) && isset($certificateData['html']))
            saveToServer({{ $certificateData['certificate_number'] }});
        @endif
	});
	//End for Scorm course body
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab'); // Get the 'tab' parameter from the URL

    if (tabParam) {
        // Select the tab link corresponding to the parameter
        const tabLink = document.querySelector(`#${tabParam}-tab`);

        if (tabLink) {
            // Use Bootstrap's Tab API to activate the tab
            const tab = new bootstrap.Tab(tabLink); // Requires Bootstrap 5
            tab.show(); // Activates the tab and displays the corresponding content
        }
    }
});
</script>

@endpush

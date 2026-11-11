<div class="tab-pane fade" id="Content" role="tabpanel" aria-labelledby="Content-tab">

<div class="row">
    <div class="col-md-12">   <!--div-row-col-md-4-start -->

        @if($course->course_type == COURSE_TYPE_GENERAL)

        <div class="row">  <!--div-row-col-md-4-ROW-start --> 
            
            <div class="col-12 col-md-4 px-0 mt-4">   <!--div-col-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12-start -->
                        <div class="course-single-details-right-content course-watch-right-content mt-0">
                            <div class="curriculum-content course-watch-right-side">

                                <div class="review-progress-bar-wrap">
                                    <!-- Progress Bar -->
                                    <div class="barras mb-4">
                                        <div class="progress-bar-box">
                                            <div
                                                class="progress-hint-value font-14 color-heading d-flex justify-content-between">
                                                <p class="font-20 font-medium color-heading">{{ __('Course Content') }}</p>
                                                <span class="font-15">{{number_format(studentCourseProgress(@$course->id, @$enrollment->id),
                                                    2)}}% Done</span>
                                            </div>
                                            <div class="barra">
                                                <div class="barra-nivel"
                                                    data-nivel="{{studentCourseProgress(@$course->id, @$enrollment->id)}}%"></div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                <div class="course-watch-right-accordion-wrap custom-scrollbar">
                                    <div class="accordion" id="accordionExample1">
                                        @php
                                        $lockCount = 0;
                                        $outerLoopKey = 0;
                                        $innerLoopKey = 0;
                                        @endphp
                                        @forelse(@$course->lessons as $key => $lesson)
                                            @php
                                                if($key == 0){
                                                    $outerLoopKey = 1;
                                                }
                                                else{
                                                    $outerLoopKey = 0;
                                                }
                                            @endphp
                                        <div class="accordion-item">

                                            <h2 class="accordion-header" id="heading{{ $key }}">
                                                <button
                                                    class="accordion-button font-medium font-18 d-flex justify-content-between {{ @$lesson_id_check == $lesson->id ?  '' : 'collapsed' }}"
                                                    type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapse{{ $key }}"
                                                    aria-expanded="{{ @$lesson_id_check == $lesson->id ?  true : false }}"
                                                    aria-controls="collapse{{ $key }}">
                                                    <span class="px-2">{{ $lesson->name }}</span>
                                                </button>
                                            </h2>

                                            <div id="collapse{{ $key }}"
                                                class="accordion-collapse collapse {{ @$lesson_id_check == $lesson->id ?  'show' : '' }}"
                                                aria-labelledby="heading{{ $key }}" data-bs-parent="#accordionExample1">
                                                <div class="accordion-body">
                                                    <div class="play-list">
                                                        
                                                        @forelse($lesson->lectures as $key2 => $lecture)
                                                        <!-- Play List Item Start-->
                                                        @php
                                                            if($key2 == 0){
                                                                $innerLoopKey = 1;
                                                            }
                                                            else{
                                                                $innerLoopKey = 0;
                                                            }

                                                            if($innerLoopKey && $outerLoopKey){
                                                                $loopFirst = True;
                                                            }
                                                            else{
                                                                $loopFirst = False;
                                                            }

                                                        
                                                            // $lock = checkStudentCourseIsLock($course_lecture_views, $course, $lecture, $enrollment, $loopFirst);
                                                        $lock = checkStudentCourseIsLock($course_lecture_views, $course, $lecture, $enrollment, $loop->first);
                                                        // $lock = checkStudentCourseIsLock($course_lecture_views, $course, $lecture, $enrollment, $isFirst);
                                                        @endphp

                                                        <div
                                                            class="play-list-item @if(checkStudentCourseView($course_lecture_views, $course->id, $lecture->id))  watchFinishedCourse @elseif($lecture->id == @$lecture_id_check)  watchContinuingCourse @endif d-flex align-items-center justify-content-between font-15">
                                                            
                                                            <div class="play-list-left d-flex align-items-center">
                                                                
                                                                <div>
                                                                    @if(checkStudentCourseView($course_lecture_views,
                                                                    $course->id,
                                                                    $lecture->id))
                                                                
                                                                    <span class="iconify font-17"
                                                                        data-icon="ant-design:check-circle-filled"></span>
                                                                    @elseif($lecture->id == @$lecture_id_check)
                                                                
                                                                    <span class="iconify font-17"
                                                                        data-icon="fluent:radio-button-24-filled"></span>
                                                                    @else
                                                                
                                                                    <span class="iconify font-17"
                                                                        data-icon="fluent:radio-button-24-regular"></span>
                                                                    @endif
                                                                </div>

                                                                @if($lock)
                                                        
                                                                <span>
                                                                    <p class="ps-2">{{ $lecture->title }}</p>
                                                                </span>
                                                                    @if($lecture->quiz_id != null)
                                                                        <span style="margin-left:5px; margin-top:3px" class="badge badge-success">{{__('Quiz')}}</span>
                                                                    @elseif($lecture->assignment_id != null)
                                                                        <span style="margin-left:2px; margin-top:3px" class="badge badge-primary">{{__('Assignment')}}</span>
                                                                    @endif
                                                            
                                                                @else
                                                            

                                                                @php
                                                                $lectureUUID =  $lecture->uuid;
                                                                
                                                                @endphp

                                                                @if ($lecture->resource)
                                                                @if ($lecture->resource->type == 'Text' || $lecture->resource->type == 'Youtube')
                                                                <a
                                                                onclick="reloadWithQueryParams('{{ $BaseRoute }}','{{$lectureUUID}}','{{route('student.resource.view_full_other', ['resource_id'=>$lecture->resource->id , 'lecture_id'=>$lecture->id])}}')"

                                                                href="javascript:void(0)"
                                                                >
                                                                <p class="ps-2">{{ $lecture->title }}</p>
                                                                </a>
                                                                @else
                                                                <a
                                                                
                                                                onclick="reloadWithQueryParams('{{ $BaseRoute }}','{{$lectureUUID}}','{{route('student.resource.view_full', ['ext'=>$lecture->resource->file_extension , 'path'=> $lecture->resource->file_name ,'lecture_id'=>$lecture->id])}}')"

                                                                href="javascript:void(0)">
                                                                <p class="ps-2">{{ $lecture->title }}</p>
                                                                </a>
                                                                @endif
                                                                @elseif($lecture->quiz_id != null)
                                                                    @if(checkStudentCourseView($course_lecture_views,
                                                                    $course->id,
                                                                    $lecture->id))
                                                                    {{-- @php
                                                                            echo "2222";
                                                                        @endphp --}}
                                                                        <a
                                                                        
                                                                        onclick="reloadWithQueryParams('{{ $BaseRoute }}','{{$lectureUUID}}','{{route('student.my-course.show', [$lecture->course?->slug, 'quiz-result', $lecture->quiz?->uuid])}}')"

                                                                        href="javascript:void(0)">
                                                                        <p class="ps-2">{{ $lecture->title }}</p>
                                                                        </a>
                                                                    @else
                                                                    
                                                                        @if(check_take_attempt_exam($lecture->quiz_id) == 'no')
                                                                        {{-- @php
                                                                        echo "3333";
                                                                    @endphp --}}
                                                                        <a
                                                                        
                                                                        onclick="reloadWithQueryParams('{{ $BaseRoute }}','{{$lectureUUID}}','{{route('student.my-course.show', [$lecture->course?->slug, 'start-quiz', $lecture->quiz?->uuid])}}')"

                                                                        href="javascript:void(0)">
                                                                        <p class="ps-2">{{ $lecture->title }}</p>
                                                                        </a>
                                                                        {{-- @elseif(check_take_attempt_exam($lecture->quiz_id) == 'no_restart')
                                                                        <a
                                                                        
                                                                        onclick="reloadWithQueryParams('{{ $BaseRoute }}','{{$lectureUUID}}','{{route('student.my-course.show', [$lecture->course?->slug, 'restart-quiz', $lecture->quiz?->uuid])}}')"
                                                                        href="{{route('student.my-course.show', [$lecture->course?->slug, 'restart-quiz', $lecture->quiz?->uuid])}}">
                                                                        <p class="ps-2">{{ $lecture->title }}</p>
                                                                        </a> --}}
                                                                        @else
                                                                    
                                                                        <a
                                                                        
                                                                        onclick="reloadWithQueryParams('{{ $BaseRoute }}','{{$lectureUUID}}','{{route('student.my-course.show', [$lecture->course?->slug, 'quiz-result', $lecture->quiz?->uuid])}}')"

                                                                        href="javascript:void(0)">
                                                                        <p class="ps-2">{{ $lecture->title }}</p>
                                                                        </a>
                                                                        @endif
                                                                    @endif
                                                                    

                                                                        <span style="margin-left:5px; margin-top:3px" class="badge badge-success">{{__('Quiz')}}</span>
                                                                @elseif($lecture->assignment_id != null)
                                                                @if(checkStudentCourseView($course_lecture_views,
                                                                $course->id,
                                                                $lecture->id))
                                                                    <button class="viewAssignmentResult"
                                                                    data-route="{{ route('student.assignment-result') }}" data-assignment_id="{{ $lecture->assignment?->id }}"
                                                                    data-lecture_uuid="{{ $lectureUUID }}">
                                                                    <p class="ps-2">{{ $lecture->title }}</p>
                                                                    </button>
                                                                @else
                                                                        <button class="viewAssignmentDetails"
                                                                        data-route="{{ route('student.assignment-details') }}" data-assignment_id="{{ $lecture->assignment?->id }}"
                                                                        data-lecture_uuid="{{ $lectureUUID }}">
                                                                        <p class="ps-2">{{ $lecture->title }}</p>
                                                                        </button>
                                                                @endif
                                                                        <span style="margin-left:5px; margin-top:3px" class="badge badge-primary">{{__('Assignment')}}</span>
                                                                @else       <!--For Lecture Direct Title -->
                                                                    <p class="ps-2">{{ $lecture->title }}</p>
                                                                @endif
                                                          
                                                                
                                                                @endif
                                                            </div>

                                                            <div class="play-list-right d-flex">
                                                                @if($lock)
                                                            
                                                                <span class="video-time-count"><span class="iconify me-5"
                                                                        data-icon="ant-design:lock-outlined"></span>{{
                                                                    $lecture->file_duration }}</span>
                                                                @else
                                                            
                                                                <span class="video-time-count">{{ $lecture->file_duration
                                                                    }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <!-- Play List Item End-->

                                                        @empty
                                                        <div class="row">
                                                            <p>{{ __('No Data Found') }}!</p>
                                                        </div>
                                                        @endforelse
                                                            
                                                        
                                                        
                                                            </div>
                                                        </div>
                                                    </div>




                                                </div>
                                                    @empty
                                                        <div class="row">
                                                            <p>{{ __('No Course Lectures Found') }}!</p>
                                                        </div>
                                                    @endforelse

                                                </div>
                </div>

                            </div>
                        </div>
            </div>

            <div class="col-12 col-md-8 px-0 mt-4">
                @if(@$lecture_type)
                @if(@$lecture_type == 'Youtube' || @$lecture_type == 'youtube')
                    <div class="video-player-area">
                        <div class="plyr__video-embed" id="playerVideoYoutube">
                            <iframe src="https://www.youtube.com/embed/{{ @$youtube_video_src }}" 
                                allowtransparency allow="autoplay">
                            </iframe>
                        </div>
                    </div>

                @elseif(@$lecture_type == 'vimeo')
                    <div class="video-player-area">
                        <div class="plyr__video-embed" id="playerVideoVimeo">
                            <iframe
                                src="https://player.vimeo.com/video/{{ @$vimeo_video_src }}?loop=false&amp;byline=false&amp;portrait=false&amp;title=false&amp;speed=true&amp;transparent=0&amp;gesture=media"
                                allowfullscreen allowtransparency allow="autoplay"></iframe>
                        </div>
                    </div>

                @elseif(@$lecture_type == 'Video' || @$lecture_type == 'video')
                    <div class="video-player-area">
                        <video id="player" class="myVideo" playsinline controls controlsList="nodownload">
                            <source src="{{ asset('uploads/course_resource')}}{{'/'}}{{$video_src}}" type="video/mp4" >
                        </video>
                    </div>

                @elseif(@$lecture_type == 'Text' || @$lecture_type == 'text')
                    <div class="video-player-area">
                        <div class="card-text">
                            <div class="card-body-text">
                                    <p class="formatted-text-box">{{@$text_src}}</p>
                            </div>
                        </div>
                    </div>

                @elseif(@$lecture_type == 'Image' || @$lecture_type == 'image')
                    <img src="{{ asset('uploads/course_resource')}}{{'/'}}{{$image_src}}" alt="Image File" class="img-fluid">
                @elseif(@$lecture_type == 'PDF' || @$lecture_type == 'pdf')
                    <embed src="{{ asset('uploads/course_resource')}}{{'/'}}{{$pdf_src}}" class="pdf-reader-frame">
                @elseif(@$lecture_type == 'Slide Document' || @$lecture_type == 'slide document')
                    <iframe src="{{ asset('uploads/course_resource')}}{{'/'}}{{$slide_document_src}}{{'/index.html'}}" width="100%" style="height: 90%" frameborder="0"
                        scrolling="no"></iframe>
                @elseif(@$lecture_type == 'Audio' || @$lecture_type == 'audio')
                    <audio id="audioplayer" class="myVideo" controls>
                        <source src="{{ asset('uploads/course_resource')}}{{'/'}}{{$audio_src}}" type="audio/mp3" >
                    </audio>
                    @endif

                @else
                    @if($course->intro_video_check == 1 && getVideoFile($course->video))
                        <div class="video-player-area">
                            <video id="player1" playsinline controls data-poster="{{ getImageFile($course->image) }}"
                                controlsList="nodownload">
                                <source src="{{ getVideoFile(@$course->video) }}" type="video/mp4" >
                            </video>
                        </div>
                    @elseif($course->intro_video_check == 2 && $course->youtube_video_id)
                        <div class="video-player-area">
                            <div class="plyr__video-embed" id="youtubePlayer">
                                <iframe src="https://www.youtube.com/embed/{{ @$course->youtube_video_id }}" allowfullscreen
                                    allowtransparency allow="autoplay">
                                </iframe>
                            </div>
                        </div>
                    @else
                        <img src="{{ getImageFile($course->image) }}" alt="" class="w-100 img-fluid">
                    @endif
                @endif
            </div>

            {{-- <div class="col-12 col-md-6 px-0 mt-4">
                @if(@$lecture_type)
                    @if(@$lecture_type == 'Youtube' || @$lecture_type == 'youtube')
                    <div class="video-player-area">
                        <div class="plyr__video-embed" id="playerVideoYoutube">
                            <iframe src="https://www.youtube.com/embed/{{ @$youtube_video_src }}" 
                                allowtransparency allow="autoplay">
                            </iframe>
                        </div>
                    </div>

                    @elseif(@$lecture_type == 'vimeo')
                    <div class="video-player-area">
                        <div class="plyr__video-embed" id="playerVideoVimeo">
                            <iframe
                                src="https://player.vimeo.com/video/{{ @$vimeo_video_src }}?loop=false&amp;byline=false&amp;portrait=false&amp;title=false&amp;speed=true&amp;transparent=0&amp;gesture=media"
                                allowfullscreen allowtransparency allow="autoplay"></iframe>
                        </div>
                    </div>

                    @elseif(@$lecture_type == 'Video' || @$lecture_type == 'video')
                    <div class="video-player-area">
                        <video id="player" class="myVideo" playsinline controls controlsList="nodownload">
                            <source src="{{ asset('uploads/course_resource')}}{{'/'}}{{$video_src}}" type="video/mp4" >
                        </video>
                    </div>

                    @elseif(@$lecture_type == 'Text' || @$lecture_type == 'text')
                    <div class="video-player-area">
                        <div class="card-text">
                            <div class="card-body-text">
                                    <p class="formatted-text-box">{{@$text_src}}</p>
                            </div>
                        </div>
                    </div>

                    @elseif(@$lecture_type == 'Image' || @$lecture_type == 'image')
                    <img src="{{ asset('uploads/course_resource')}}{{'/'}}{{$image_src}}" alt="Image File" class="img-fluid">
                    @elseif(@$lecture_type == 'PDF' || @$lecture_type == 'pdf')
                    <embed src=" {{ asset('uploads/course_resource')}}{{'/'}}{{$pdf_src}}" class="pdf-reader-frame">
                    @elseif(@$lecture_type == 'Slide Document' || @$lecture_type == 'slide document')
                    <iframe src="{{ asset('uploads/course_resource')}}{{'/'}}{{$slide_document_src}}{{'/index.html'}}" width="100%" style="height: 90%" frameborder="0"
                        scrolling="no"></iframe>
                    @elseif(@$lecture_type == 'Audio' || @$lecture_type == 'audio')
                    <audio id="audioplayer" class="myVideo" controls>
                        <source src="{{ asset('uploads/course_resource')}}{{'/'}}{{$audio_src}}" type="audio/mp3" >
                    </audio>
                    @endif

                @else
                    @if($course->intro_video_check == 1 && getVideoFile($course->video))
                        <div class="video-player-area">
                            <video id="player1" playsinline controls data-poster="{{ getImageFile($course->image) }}"
                                controlsList="nodownload">
                                <source src="{{ getVideoFile(@$course->video) }}" type="video/mp4" >
                            </video>
                        </div>
                    @elseif($course->intro_video_check == 2 && $course->youtube_video_id)
                        <div class="video-player-area">
                            <div class="plyr__video-embed" id="youtubePlayer">
                                <iframe src="https://www.youtube.com/embed/{{ @$course->youtube_video_id }}" allowfullscreen
                                    allowtransparency allow="autoplay">
                                </iframe>
                            </div>
                        </div>
                    @else
                        <img src="{{ getImageFile($course->image) }}" alt="" class="w-100 img-fluid">
                    @endif
                @endif
            </div>     --}}
        </div> <!--div-row-col-md-4-ROW-end -->

        @elseif($course->course_type == COURSE_TYPE_SCORM)

                @if ($course->scorm_course)
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <iframe id="scorm_player" class="scorm-content" frameBorder="0" src="{{ asset("scorm/".$course->scorm_course->uuid.'/'.$course->scorm_course->entry_url)
                            }}"
                            width="100%" title="Scorm course"></iframe>
                    </div>
                </div>
                @endif
            
        @else

        <div class="col-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
            <div class="course-single-details-right-content course-watch-right-content mt-0">
                <div class="curriculum-content course-watch-right-side">
                    <div class="review-progress-bar-wrap">
                        <!-- Progress Bar -->
                        <div class="barras mb-4">
                            <div class="progress-bar-box">
                                <div
                                    class="progress-hint-value font-14 color-heading d-flex justify-content-between">
                                    <p class="font-20 font-medium color-heading">{{ __('Course Content') }}</p>

                                @if($course->course_type == COURSE_TYPE_GENERAL)
                                <span class="font-15">{{number_format(studentCourseProgress(@$course->id, @$enrollment->id),
                                        2)}}% Done</span>
                                @endif
                                </div>

                                @if($course->course_type == COURSE_TYPE_GENERAL)
                                <div class="barra">
                                    <div class="barra-nivel"
                                        data-nivel="{{studentCourseProgress(@$course->id, @$enrollment->id)}}%"></div>
                                </div>
                                @endif
                            </div>
                        </div>

                    </div>
                    <div class="course-watch-right-accordion-wrap custom-scrollbar">
                        <div class="accordion" id="accordionExample1">
                            @php
                            $lockCount = 0;
                            @endphp
                            @forelse(@$categories_with_resources as $key => $category)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headinglive{{ $key }}">
                                    <button
                                        class="accordion-button font-medium font-18 d-flex justify-content-between collapsed"
                                        type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapselive{{ $key }}"
                                        aria-expanded="true"
                                        aria-controls="collapselive{{ $key }}">
                                        <span class="px-2">{{ $category['category_name'] }}</span>   
                                    </button>
                                </h2>

                                <div id="collapselive{{ $key }}"
                                    class="accordion-collapse collapse"
                                    aria-labelledby="headinglive{{ $key }}" data-bs-parent="#accordionExample1">
                                    <div class="accordion-body">
                                        <div class="play-list">

                                            @forelse($category['category_resources'] as $resource)
                                            <!-- Category Resource Start-->
                                            <div
                                                class="play-list-item">
                                                <div class="play-list-left d-flex align-items-center">
                                                
                                                    <span class="iconify font-17"
                                                    data-icon="fluent:radio-button-24-regular"></span>
                                                    @if ($resource['resource_type'] == 'Text' || $resource['resource_type'] == 'Youtube')
                                                    <a
                                                    
                                                    href="{{route('student.resource.view_full_other', ['resource_id'=>$resource['resource_id']])}}">
                                                    <p class="ps-2">{{ $resource['resource_name'] }}</p>
                                                </a>
                                                    @else
                                                    <a
                                                    
                                                    href="{{route('student.resource.view_full', ['ext'=>$resource['resource_ext'] , 'path'=> $resource['resource_path']])}}">
                                                    <p class="ps-2">{{ $resource['resource_name'] }}</p>
                                                </a>
                                                    @endif
                                                
                                                </div>
                                            
                                            </div>
                                            <!-- Category Resource End-->
                                            @empty
                                            <div class="row">
                                                <p>{{ __('No Data Found') }}!</p>
                                            </div>
                                            @endforelse

                                        

                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="row">
                                <p>{{ __('No Course Resources Found') }}!</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>      <!-- My Code End -->
                        
        <div class="row">
            <div class="col-12">
                <div style="overflow: hidden; height: 0;">
                    <div id="certificate-preview-div-hidden">
                        <div style="font-family: certificate_font" class="pdf-wrapper-box" id="demo-certificate">
                            @if(isset($certificateData) && isset($certificateData['html']))
                            {!! $certificateData['html'] !!}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div style="overflow: hidden; height: 0;">
                    <div id="certificate-preview-div-hidden">
                        <div style="font-family: certificate_font" class="pdf-wrapper-box" id="demo-certificate">
                            @if(isset($certificateData) && isset($certificateData['html']))
                            {!! $certificateData['html'] !!}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @endif
    </div>
</div>
    
</div>

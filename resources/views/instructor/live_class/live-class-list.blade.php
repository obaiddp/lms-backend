@extends('layouts.instructor')

@section('breadcrumb')
<div class="page-banner-content text-center">
    <h3 class="page-banner-heading text-white pb-15"> {{__('My Courses')}} </h3>

    <!-- Breadcrumb Start-->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb justify-content-center">
            <li class="breadcrumb-item font-14"><a href="{{route('instructor.dashboard')}}">{{__('Dashboard')}}</a></li>
            <li class="breadcrumb-item font-14"><a href="{{ route('live-class.course-live-class.index') }}">{{ __('Live
                    Class Course List') }}</a></li>
            <li class="breadcrumb-item font-14 active" aria-current="page">{{ __('Live Class List') }}</li>
        </ol>
    </nav>
</div>
@endsection

@section('content3')
<div class="instructor-profile-right-part" id="meet">
    <div class="instructor-quiz-list-page instructor-live-class-list-page">

        <div class="instructor-my-courses-title d-flex justify-content-between align-items-center">
            <h6>{{ @$course->title }}</h6>
        </div>

        <div class="row">
            <div class="col-12">
                <ul class="nav nav-tabs assignment-nav-tabs live-class-list-nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ @$navUpcomingActive }}" id="upcoming-tab" data-bs-toggle="tab"
                            data-bs-target="#upcoming" type="button" role="tab" aria-controls="upcoming"
                            aria-selected="true">{{ __('Upcoming') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ @$navCurrentActive }}" id="current-tab" data-bs-toggle="tab"
                            data-bs-target="#current" type="button" role="tab" aria-controls="current"
                            aria-selected="false">{{ __('Current') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ @$navPastActive }}" id="past-tab" data-bs-toggle="tab"
                            data-bs-target="#past" type="button" role="tab" aria-controls="past"
                            aria-selected="false">{{ __('Past') }}
                        </button>
                    </li>
                </ul>

                <div class="tab-content live-class-list" id="myTabContent">
                    <div class="tab-pane fade {{ @$tabUpcomingActive }}" id="upcoming" role="tabpanel"
                        aria-labelledby="upcoming-tab">
                        @if(count($upcoming_live_classes) > 0)
                        <div class="table-responsive table-responsive-xl">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __('Topic') }}</th>
                                        <th scope="col">{{ __('Date & Time') }}</th>
                                        <th scope="col">{{ __('Time Duration') }}</th>
                                        <th scope="col">{{ __('Learning Tool') }}</th>
                                        <th scope="col">{{ __('Status') }}</th>
                                        <th scope="col">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcoming_live_classes as $upcoming_live_class)
                                    <tr>
                                        <td>{{ Str::limit($upcoming_live_class->class_topic, 50) }}</td>
                                        <td>{{ $upcoming_live_class->date .' '. $upcoming_live_class->time }}</td>
                                        <td>{{ $upcoming_live_class->duration }} minutes</td>
                                        <td>
                                            @if($upcoming_live_class->meeting_host_name == 'zoom')
                                            Zoom
                                            @elseif($upcoming_live_class->meeting_host_name == 'bbb')
                                            BigBlueButton
                                            @elseif($upcoming_live_class->meeting_host_name == 'google_meet')
                                            Google Meet
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                            $updated_time = changetimeByZone($upcoming_live_class->time, $instructor_id, 'instructor');
                                            $lesson_time = $updated_time;
                                            $curdate = strtotime(date("Y-m-d"));
                                            $lsndate = strtotime($upcoming_live_class->date);
                                            $updated_cur_time = changetimeByZone(date("H:i"), $instructor_id, 'instructor');
                                            $curtime = strtotime($updated_cur_time);
                                            $lsntime = strtotime($lesson_time);
                                            $lesson_duration = $upcoming_live_class->duration;
                                            $lsntimestamp = strtotime($lesson_time) + $lesson_duration * 60;
                                            $lsnendtime = $lsntimestamp;
                                            @endphp
                                             <?php if ($curdate < $lsndate) { ?>
                                                <span style="border:none;font-weight:bolder;background-color:#322740;color:white;" class="btn btn-warning m-1 py-1 px-2 small">Scheduled</span>
                                            <?php } 
                                            else if ($curdate == $lsndate) {
                                                if ($curtime < $lsntime) {
                                                    $id = 'lec_link1_' . $upcoming_live_class->id;
                                                    echo "<a class='btn btn-warning m-1 lec_link1 px-2 py-1 small' id='$id'>" . 'Waiting' . "</a>";
                                                }
                                                else if ($curtime >= $lsntime && $curtime < $lsnendtime && $upcoming_live_class->meeting_id == NULL && $upcoming_live_class->meeting_host_name == 'zoom') {
                                                        $id = 'lec_link1_' . $upcoming_live_class->id;
                                                        echo "<a class='btn btn-warning m-1 lec_link1 px-2 py-1 small' id='$id'>" . 'Waiting' . "</a>";
                                                } 
                                                else if ($curtime >= $lsntime && $curtime < $lsnendtime && $upcoming_live_class->InstructorURI == NULL && $upcoming_live_class->meeting_host_name == 'bbb') {
                                                        $id = 'lec_link1_' . $upcoming_live_class->id;
                                                        echo "<a class='btn btn-warning m-1 lec_link1 px-2 py-1 small' id='$id'>" . 'Waiting' . "</a>";
                                                } 
                                                else if ($curtime >= $lsntime && $curtime < $lsnendtime && $upcoming_live_class->meeting_id == NULL && $upcoming_live_class->meeting_host_name == 'google_meet') {
                                                        $id = 'lec_link1_' . $upcoming_live_class->id;
                                                        echo "<a class='btn btn-warning m-1 lec_link1 px-2 py-1 small' id='$id'>" . 'Waiting' . "</a>";
                                                } 
                                            } 
                                            ?>
                                        </td>
                                        <td><a href="javascript:void(0);"
                                                data-url="{{ route('live-class.delete', $upcoming_live_class->uuid) }}"
                                                class="theme-btn default-delete-btn-red delete"><span class="iconify"
                                                    data-icon="gg:trash"></span>{{ __('Delete') }}</a></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <!-- If there is no data Show Empty Design Start -->
                        <div class="empty-data">
                            <img src="{{ asset('frontend/assets/img/empty-data-img.png') }}" alt="img"
                                class="img-fluid">
                            <h4 class="my-3">{{ __('Empty Live Class') }}</h4>
                        </div>
                        <!-- If there is no data Show Empty Design End -->
                        @endif
                        <!-- Pagination Start -->
                        @if(@$upcoming_live_classes->hasPages())
                        {{ @$upcoming_live_classes->links('frontend.paginate.paginate') }}
                        @endif
                        <!-- Pagination End -->
                    </div>
                    <div class="tab-pane fade {{ @$tabCurrentActive }}" id="current" role="tabpanel"
                        aria-labelledby="current-tab">
                        @if(count($current_live_classes) > 0)
                        <div class="table-responsive table-responsive-xl">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __('Topic') }}</th>
                                        <th scope="col">{{ __('Date & Time') }}</th>
                                        <th scope="col">{{ __('Time Duration') }}</th>
                                        <th scope="col">{{ __('Learning Tool') }}</th>
                                        <th scope="col">{{ __('Status') }}</th>
                                        {{-- <th scope="col">{{ __('Action') }}</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($current_live_classes as $current_live_class)
                                    <tr>
                                        <td>{{ Str::limit($current_live_class->class_topic, 50) }}</td>
                                        <td>{{ $current_live_class->date .' '. $current_live_class->time }}</td>
                                        <td>{{ $current_live_class->duration }} minutes</td>
                                        {{-- <td>
                                            @if($current_live_class->meeting_host_name == 'zoom')
                                            Zoom
                                            <button
                                                class="theme-btn theme-button1 green-theme-btn default-hover-btn viewMeetingLink"
                                                data-item="{{ $current_live_class }}">
                                                {{ __('View') }}
                                            </button>
                                            @elseif($current_live_class->meeting_host_name == 'bbb')
                                            BigBlueButton
                                            <button
                                                class="theme-btn theme-button1 green-theme-btn default-hover-btn viewBBBMeetingLink"
                                                data-item="{{ $current_live_class }}"
                                                data-route="{{ route('instructor.join-bbb-meeting', $current_live_class->id) }}">
                                                {{ __('View') }}
                                            </button>
                                            @endif
                                        </td> --}}
                                        <td>
                                            @if($current_live_class->meeting_host_name == 'zoom')
                                            Zoom
                                            @elseif($current_live_class->meeting_host_name == 'bbb')
                                            BigBlueButton
                                            @elseif($current_live_class->meeting_host_name == 'google_meet')
                                            Google Meet
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                            $updated_time = changetimeByZone($current_live_class->time, $instructor_id, 'instructor');
                                            $lesson_time = $updated_time;
                                            $curdate = strtotime(date("Y-m-d"));
                                            $lsndate = strtotime($current_live_class->date);
                                            $updated_cur_time = changetimeByZone(date("H:i"), $instructor_id, 'instructor');
                                            $curtime = strtotime($updated_cur_time);
                                            $lsntime = strtotime($lesson_time);
                                            $lesson_duration = $current_live_class->duration;
                                            $lsntimestamp = strtotime($lesson_time) + $lesson_duration * 60;
                                            $lsnendtime = $lsntimestamp;
                                            @endphp
                                            
                                            <?php 
                                            if ($curdate == $lsndate) {
                                                if ($curtime >= $lsntime && $curtime < $lsnendtime && $current_live_class->meeting_id == NULL && $current_live_class->meeting_host_name == 'zoom') {
                                                        $id = 'lec_link1_' . $current_live_class->id;
                                                        echo "<a class='btn btn-warning m-1 lec_link1 px-2 py-1 small' id='$id'>" . 'Waiting' . "</a>";
                                                } 
                                                else if ($curtime >= $lsntime && $curtime < $lsnendtime && $current_live_class->InstructorURI == NULL && $current_live_class->meeting_host_name == 'bbb') {
                                                        $id = 'lec_link1_' . $current_live_class->id;
                                                        echo "<a class='btn btn-warning m-1 lec_link1 px-2 py-1 small' id='$id'>" . 'Waiting' . "</a>";
                                                } 
                                                else if ($curtime >= $lsntime && $curtime < $lsnendtime && $current_live_class->meeting_id == NULL && $current_live_class->meeting_host_name == 'google_meet') {
                                                        $id = 'lec_link1_' . $current_live_class->id;
                                                        echo "<a class='btn btn-warning m-1 lec_link1 px-2 py-1 small' id='$id'>" . 'Waiting' . "</a>";
                                                } 
                                                else if ($curtime == $lsntime && $current_live_class->status != "Completed" && $current_live_class->status != "Missed" && $current_live_class->meeting_host_name == 'zoom') { ?>
                                                <a href="javascript:void(0);" data-meeting_id="<?php echo $current_live_class->meeting_id; ?>" data-user_type="Tutor" data-user_name="<?php echo $instructor_name; ?>" data-user_id="<?php echo $instructor_id; ?>" class="btn btn-info m-1 create_zoom_meeting_link_live_class py-1 px-2 small" style="background-color:#499619;color: #fff">Start Class</a>
                                            <?php }
                                               else if ($curtime == $lsntime && ($current_live_class->status == "Completed" || $current_live_class->status == "Missed") && $current_live_class->meeting_host_name == 'zoom') { 
                                                    if ($current_live_class->status == "Completed" && $current_live_class->is_moderator_left == 1) {
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } 
                                                    else if ($current_live_class->status == "Missed" && $current_live_class->is_moderator_left == 1) {
                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                    }
                                                }
                                                else if ($curtime == $lsntime && $current_live_class->status != "Completed" && $current_live_class->status != "Missed" && $current_live_class->meeting_host_name == 'bbb') { 
                                                 ?>   
                                                    <a  href="javascript:void(0);" data-tutor_uri ="<?php echo $current_live_class->InstructorURI ; ?>" data-lesson_id ="<?php echo $current_live_class->id ; ?>" data-meeting_id ="<?php echo $current_live_class->meeting_id ; ?>"  class='btn btn-info m-1 start_bbb_individual_live_class py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                <?php } 
                                                 else if ($curtime == $lsntime && ($current_live_class->status == "Completed" || $current_live_class->status == "Missed") && $current_live_class->meeting_host_name == 'bbb') {
                                                    if ($current_live_class->status == "Completed" && $current_live_class->InstructorURI != null) {
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } else if ($current_live_class->status == "Missed" && $current_live_class->InstructorURI != null) {
                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                    }
                                                 }
                                                 else if ($curtime == $lsntime && $current_live_class->status != "Completed" && $current_live_class->status != "Missed" && $current_live_class->meeting_host_name == 'google_meet') { 
                                                 ?>   
                                                    <a  href="javascript:void(0);" data-tutor_uri ="<?php echo $current_live_class->google_meet_link ; ?>" data-lesson_id ="<?php echo $current_live_class->id ; ?>" data-meeting_id ="<?php echo $current_live_class->meeting_id ; ?>"  class='btn btn-info m-1 start_meet_individual_live_class py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                <?php } 
                                                 else if ($curtime == $lsntime && ($current_live_class->status == "Completed" || $current_live_class->status == "Missed") && $current_live_class->meeting_host_name == 'google_meet') {
                                                    if ($current_live_class->status == "Completed" && $current_live_class->meeting_id != null) {
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } else if ($current_live_class->status == "Missed" && $current_live_class->meeting_id != null) {
                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                    }
                                                 }

                                                 else if ($curtime > $lsntime && $curtime < $lsnendtime && $current_live_class->status != "Completed" && $current_live_class->status != "Missed" && $current_live_class->meeting_host_name == 'zoom') { ?>
                                                    <a href="javascript:void(0);" id="lec_link" data-meeting_id="<?php echo $current_live_class->meeting_id; ?>" data-user_type="Tutor" data-user_name="<?php echo $instructor_name; ?>" data-user_id="<?php echo $instructor_id; ?>" class="btn btn-info m-1 create_zoom_meeting_link_live_class py-1 px-2 small" style="background-color:#499619;color: #fff">Start Class</a>
                                                 <?php }
                                                    else if ($curtime > $lsntime && $curtime < $lsnendtime && $current_live_class->meeting_host_name == 'Zoom') {
                                                        if ($current_live_class->status == "Completed" && $current_live_class->is_moderator_left == 1) {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } else if ($current_live_class->status == "Missed" && $current_live_class->is_moderator_left == 1) {
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                        } else { ?>
                                                            <a href="javascript:void(0);" id="lec_link" data-meeting_id="<?php echo $current_live_class->meeting_id; ?>" data-user_type="Tutor" data-user_name="<?php echo $instructor_name; ?>" data-user_id="<?php echo $instructor_id; ?>" class="btn btn-info m-1 create_zoom_meeting_link_live_class py-1 px-2 small" style="background-color:#499619;color: #fff">Start Class</a>
                                                        <?php }
                                                    }
                                                    else if ($curtime > $lsntime && $curtime < $lsnendtime && $current_live_class->status != "Completed" && $current_live_class->status != "Missed" && $current_live_class->meeting_host_name == 'bbb') { ?>
                                                        <a  href="javascript:void(0);" data-tutor_uri ="<?php echo $current_live_class->InstructorURI ; ?>" data-lesson_id ="<?php echo $current_live_class->id ; ?>" data-meeting_id ="<?php echo $current_live_class->meeting_id ; ?>"  class='btn btn-info m-1 start_bbb_individual_live_class py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                    <?php }
                                                       else if ($curtime > $lsntime && $curtime < $lsnendtime && $current_live_class->meeting_host_name == 'bbb') {
                                                          if ($current_live_class->status == "Completed" && $current_live_class->InstructorURI != null) {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                          } else if ($current_live_class->status == "Missed" && $current_live_class->InstructorURI != null) {
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                          } else { ?>
                                                              <a  href="javascript:void(0);" data-tutor_uri ="<?php echo $current_live_class->InstructorURI ; ?>" data-lesson_id ="<?php echo $current_live_class->id ; ?>" data-meeting_id ="<?php echo $current_live_class->meeting_id ; ?>"  class='btn btn-info m-1 start_bbb_individual_live_class py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                          <?php }
                                                       }
                                                       else if ($curtime > $lsntime && $curtime < $lsnendtime && $current_live_class->status != "Completed" && $current_live_class->status != "Missed" && $current_live_class->meeting_host_name == 'google_meet') { ?>
                                                        <a  href="javascript:void(0);" data-tutor_uri ="<?php echo $current_live_class->google_meet_link ; ?>" data-lesson_id ="<?php echo $current_live_class->id ; ?>" data-meeting_id ="<?php echo $current_live_class->meeting_id ; ?>"  class='btn btn-info m-1 start_meet_individual_live_class py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                    <?php }
                                                       else if ($curtime > $lsntime && $curtime < $lsnendtime && $current_live_class->meeting_host_name == 'google_meet') {
                                                          if ($current_live_class->status == "Completed" && $current_live_class->meeting_id != null) {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                          } else if ($current_live_class->status == "Missed" && $current_live_class->meeting_id != null) {
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                          } else { ?>
                                                              <a  href="javascript:void(0);" data-tutor_uri ="<?php echo $current_live_class->google_meet_link ; ?>" data-lesson_id ="<?php echo $current_live_class->id ; ?>" data-meeting_id ="<?php echo $current_live_class->meeting_id ; ?>"  class='btn btn-info m-1 start_meet_individual_live_class py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                          <?php }
                                                       }

                                                       
                                                       else if ($curtime == $lsnendtime && $current_live_class->meeting_host_name == 'zoom') {
                                                          if (($current_live_class->status != "Completed" && $current_live_class->status != "Missed") && $current_live_class->meeting_host_name == 'zoom' && ($current_live_class->is_instructor_joined == '1' || $current_live_class->is_instructor_joined == "1" || $current_live_class->is_instructor_joined == 1 || $current_live_class->is_instructor_joined)) {
                                                            updateLessonStatusCompleteZoomInstructor($current_live_class->id, $current_live_class->meeting_id, null);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                          } else if (($current_live_class->status != "Completed" && $current_live_class->status != "Missed") && $current_live_class->meeting_host_name == 'zoom' && $current_live_class->is_instructor_joined == '0') {
                                                            updateLessonStatusMissedZoomInstructor($current_live_class->id, $current_live_class->meeting_id, null);
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                          } else {
                                                            if ($current_live_class->status == "Completed") {
                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                            } else {
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                          }
                                                       }
                                                       else if ($curtime == $lsnendtime && $current_live_class->meeting_host_name == 'bbb') {
                                                          if (($current_live_class->status != "Completed" && $current_live_class->status != "Missed") && $current_live_class->meeting_host_name == 'bbb' && ($current_live_class->is_instructor_joined == '1' || $current_live_class->is_instructor_joined == "1" || $current_live_class->is_instructor_joined == 1 || $current_live_class->is_instructor_joined)) {
                                                            updateLessonStatusCompleteZoomInstructor($current_live_class->id, $current_live_class->meeting_id, null);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                          } else if (($current_live_class->status != "Completed" && $current_live_class->status != "Missed") && $current_live_class->meeting_host_name == 'bbb' && $current_live_class->is_instructor_joined == '0') {
                                                            updateLessonStatusMissedZoomInstructor($current_live_class->id, $current_live_class->meeting_id, null);
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                          } else {
                                                            if ($current_live_class->status == "Completed") {
                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                            } else {
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                          }
                                                       }
                                                       else if ($curtime == $lsnendtime && $current_live_class->meeting_host_name == 'google_meet') {
                                                          if (($current_live_class->status != "Completed" && $current_live_class->status != "Missed") && $current_live_class->meeting_host_name == 'google_meet' && ($current_live_class->is_instructor_joined == '1' || $current_live_class->is_instructor_joined == "1" || $current_live_class->is_instructor_joined == 1 || $current_live_class->is_instructor_joined)) {
                                                            updateLessonStatusCompleteZoomInstructor($current_live_class->id, $current_live_class->meeting_id, null);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                          } else if (($current_live_class->status != "Completed" && $current_live_class->status != "Missed") && $current_live_class->meeting_host_name == 'google_meet' && $current_live_class->is_instructor_joined == '0') {
                                                            updateLessonStatusMissedZoomInstructor($current_live_class->id, $current_live_class->meeting_id, null);
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                          } else {
                                                            if ($current_live_class->status == "Completed") {
                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                            } else {
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                          }
                                                       }
                                                       }
                                                    ?>
                                         
                                        </td>
                                        {{-- <td><a href="javascript:void(0);"
                                                data-url="{{ route('live-class.delete', $current_live_class->uuid) }}"
                                                class="theme-btn default-delete-btn-red delete">
                                                <span class="iconify" data-icon="gg:trash"></span>{{ __('Delete') }}</a>
                                        </td> --}}
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <!-- If there is no data Show Empty Design Start -->
                        <div class="empty-data">
                            <img src="{{ asset('frontend/assets/img/empty-data-img.png') }}" alt="img"
                                class="img-fluid">
                            <h4 class="my-3">{{ __('Empty Current Class') }}</h4>
                        </div>
                        <!-- If there is no data Show Empty Design End -->
                        @endif
                        <!-- Pagination Start -->
                        @if(@$current_live_classes->hasPages())
                        {{ @$current_live_classes->links('frontend.paginate.paginate') }}
                        @endif
                        <!-- Pagination End -->
                    </div>
                    
                    <div class="tab-pane fade {{ @$tabPastActive }}" id="past" role="tabpanel"
                        aria-labelledby="past-tab">
                        @if(count($past_live_classes) > 0)
                        <div class="table-responsive table-responsive-xl">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __('Topic') }}</th>
                                        <th scope="col">{{ __('Date & Time') }}</th>
                                        <th scope="col">{{ __('Time Duration') }}</th>
                                        <th scope="col">{{ __('Learning Tool') }}</th>
                                        <th scope="col">{{ __('Status') }}</th>
                                        <th scope="col">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($past_live_classes as $past_live_class)
                                    <tr>
                                        <td>{{ Str::limit($past_live_class->class_topic, 50) }}</td>
                                        <td>{{ $past_live_class->date .' '. $past_live_class->time }}</td>
                                        <td>{{ $past_live_class->duration }} minutes</td>
                                        <td>
                                            @if($past_live_class->meeting_host_name == 'zoom')
                                            Zoom
                                            @elseif($past_live_class->meeting_host_name == 'bbb')
                                            BigBlueButton
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                            $updated_time = changetimeByZone($past_live_class->time, $instructor_id, 'instructor');
                                            $lesson_time = $updated_time;
                                            $curdate = strtotime(date("Y-m-d"));
                                            $lsndate = strtotime($past_live_class->date);
                                            $updated_cur_time = changetimeByZone(date("H:i"), $instructor_id, 'instructor');
                                            $curtime = strtotime($updated_cur_time);
                                            $lsntime = strtotime($lesson_time);
                                            $lesson_duration = $past_live_class->duration;
                                            $lsntimestamp = strtotime($lesson_time) + $lesson_duration * 60;
                                            $lsnendtime = $lsntimestamp;
                                            @endphp

                                            <?php 
                                            if ($curdate == $lsndate) {
                                              
                                                    if ($curtime == $lsnendtime && $past_live_class->meeting_host_name == 'zoom') {
                                                          if (($past_live_class->status != "Completed" && $past_live_class->status != "Missed") && $past_live_class->meeting_host_name == 'zoom' && ($past_live_class->is_instructor_joined == '1' || $past_live_class->is_instructor_joined == "1" || $past_live_class->is_instructor_joined == 1 || $past_live_class->is_instructor_joined)) {
                                                            updateLessonStatusCompleteZoomInstructor($past_live_class->id, $past_live_class->meeting_id, null);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                          } else if (($past_live_class->status != "Completed" && $past_live_class->status != "Missed") && $past_live_class->meeting_host_name == 'zoom' && $past_live_class->is_instructor_joined == '0') {
                                                            updateLessonStatusMissedZoomInstructor($past_live_class->id, $past_live_class->meeting_id, null);
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                          } else {
                                                            if ($past_live_class->status == "Completed") {
                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                            } else {
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                          }
                                                    }
                                                    else if ($curtime == $lsnendtime && $past_live_class->meeting_host_name == 'bbb') {
                                                          if (($past_live_class->status != "Completed" && $past_live_class->status != "Missed") && $past_live_class->meeting_host_name == 'bbb' && ($past_live_class->is_instructor_joined == '1' || $past_live_class->is_instructor_joined == "1" || $past_live_class->is_instructor_joined == 1 || $past_live_class->is_instructor_joined)) {
                                                            updateLessonStatusCompleteZoomInstructor($past_live_class->id, $past_live_class->meeting_id, null);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                          } else if (($past_live_class->status != "Completed" && $past_live_class->status != "Missed") && $past_live_class->meeting_host_name == 'bbb' && $past_live_class->is_instructor_joined == '0') {
                                                            updateLessonStatusMissedZoomInstructor($past_live_class->id, $past_live_class->meeting_id, null);
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                          } else {
                                                            if ($past_live_class->status == "Completed") {
                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                            } else {
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                          }
                                                    }
                                                    else if ($curtime == $lsnendtime && $past_live_class->meeting_host_name == 'google_meet') {
                                                          if (($past_live_class->status != "Completed" && $past_live_class->status != "Missed") && $past_live_class->meeting_host_name == 'google_meet' && ($past_live_class->is_instructor_joined == '1' || $past_live_class->is_instructor_joined == "1" || $past_live_class->is_instructor_joined == 1 || $past_live_class->is_instructor_joined)) {
                                                            updateLessonStatusCompleteZoomInstructor($past_live_class->id, $past_live_class->meeting_id, null);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                          } else if (($past_live_class->status != "Completed" && $past_live_class->status != "Missed") && $past_live_class->meeting_host_name == 'google_meet' && $past_live_class->is_instructor_joined == '0') {
                                                            updateLessonStatusMissedZoomInstructor($past_live_class->id, $past_live_class->meeting_id, null);
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                          } else {
                                                            if ($past_live_class->status == "Completed") {
                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                            } else {
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                          }
                                                    }

                                                else if ($curtime > $lsnendtime) {
                                                    if ($past_live_class->status == "Pending" && $past_live_class->meeting_host_name == 'zoom' && $past_live_class->is_instructor_joined == '1' || $past_live_class->is_instructor_joined == "1" || $past_live_class->is_instructor_joined == 1 || $past_live_class->is_instructor_joined) {
                                                            updateLessonStatusCompleteZoomInstructor($past_live_class->id, $past_live_class->meeting_id, null);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } 
                                                    else if ($past_live_class->status == "Pending" && $past_live_class->meeting_host_name == 'bbb' && $past_live_class->is_instructor_joined == '1' || $past_live_class->is_instructor_joined == "1" || $past_live_class->is_instructor_joined == 1 || $past_live_class->is_instructor_joined) {
                                                        updateLessonStatusCompleteZoomInstructor($past_live_class->id, $past_live_class->meeting_id, null);
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } 
                                                    else if ($past_live_class->status == "Pending" && $past_live_class->meeting_host_name == 'google_meet' && $past_live_class->is_instructor_joined == '1' || $past_live_class->is_instructor_joined == "1" || $past_live_class->is_instructor_joined == 1 || $past_live_class->is_instructor_joined) {
                                                        updateLessonStatusCompleteZoomInstructor($past_live_class->id, $past_live_class->meeting_id, null);
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } 
                                                    else if ($past_live_class->status == "Completed") {
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } 
                                                    else {
                                                            if ($past_live_class->status == "Missed") {
                                                                if (!empty($past_live_class->meeting_id)) {
                                                                    $res = check_missed_status_instructor($past_live_class->meeting_id);
                                                                    if ($res == 'Completed') {
                                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                                    } else {
                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                                    }
                                                                } else {
                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                                }
                                                            } 
                                                            else if ($past_live_class->status == "Pending" && $past_live_class->meeting_host_name == "zoom" && $past_live_class->is_instructor_joined == 0) {
                                                                updateLessonStatusMissedZoomInstructor($past_live_class->id, $past_live_class->meeting_id, null);
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                            else if ($past_live_class->status == "Pending" && $past_live_class->meeting_host_name == "bbb" && $past_live_class->is_instructor_joined == 0) {
                                                                updateLessonStatusMissedZoomInstructor($past_live_class->id, $past_live_class->meeting_id, null);
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                            else if ($past_live_class->status == "Pending" && $past_live_class->meeting_host_name == "google_meet" && $past_live_class->is_instructor_joined == 0) {
                                                                updateLessonStatusMissedZoomInstructor($past_live_class->id, $past_live_class->meeting_id, null);
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                             else {
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                    }
                                                }
                                            }
                                            else if ($curdate > $lsndate) {
                                                if ($past_live_class->status == "Pending" && $past_live_class->meeting_host_name == 'zoom' && $past_live_class->is_instructor_joined == '1' || $past_live_class->is_instructor_joined == "1" || $past_live_class->is_instructor_joined == 1 || $past_live_class->is_instructor_joined) {
                                                        updateLessonStatusCompleteZoomInstructor($past_live_class->id, $past_live_class->meeting_id, null);
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } 
                                                    else if ($past_live_class->status == "Pending" && $past_live_class->meeting_host_name == 'bbb' && $past_live_class->is_instructor_joined == '1' || $past_live_class->is_instructor_joined == "1" || $past_live_class->is_instructor_joined == 1 || $past_live_class->is_instructor_joined) {
                                                        updateLessonStatusCompleteZoomInstructor($past_live_class->id, $past_live_class->meeting_id, null);
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } 
                                                    else if ($past_live_class->status == "Pending" && $past_live_class->meeting_host_name == 'google_meet' && $past_live_class->is_instructor_joined == '1' || $past_live_class->is_instructor_joined == "1" || $past_live_class->is_instructor_joined == 1 || $past_live_class->is_instructor_joined) {
                                                        updateLessonStatusCompleteZoomInstructor($past_live_class->id, $past_live_class->meeting_id, null);
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } 
                                                    else if ($past_live_class->status == "Completed") {
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } 
                                                    else if ($past_live_class->status == "Pending") {
                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                    } 
                                                    else {
                                                        if ($past_live_class->status == "Missed") {
                                                            if (!empty($past_live_class->meeting_id)) {
                                                                $res = check_missed_status_instructor($past_live_class->meeting_id);
                                                                if ($res == 'Completed') {
                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                                } else {
                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                                }
                                                            } else {
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                        }
                                                    }
                                            }
                                            else{
                                                if ($past_live_class->status == "Completed") {
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                } else {
                                                        if ($past_live_class->status == "Missed") {
                                                            if (!empty($past_live_class->meeting_id)) {
                                                                $res = check_missed_status_instructor($past_live_class->meeting_id);
                                                                if ($res == 'Completed') {
                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                                } else {
                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                                }
                                                            } else {
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                        }
                                                }
                                            }
                                            ?>
                                        </td>
                                        <td><a href="javascript:void(0);"
                                                data-url="{{ route('live-class.delete', $past_live_class->uuid) }}"
                                                class="theme-btn default-delete-btn-red delete">
                                                <span class="iconify" data-icon="gg:trash"></span>{{ __('Delete') }}</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <!-- If there is no data Show Empty Design Start -->
                        <div class="empty-data">
                            <img src="{{ asset('frontend/assets/img/empty-data-img.png') }}" alt="img"
                                class="img-fluid">
                            <h4 class="my-3">{{ __('Empty Past Class') }}</h4>
                        </div>
                        <!-- If there is no data Show Empty Design End -->
                        @endif
                        <!-- Pagination Start -->
                        @if(@$past_live_classes->hasPages())
                        {{ @$past_live_classes->links('frontend.paginate.paginate') }}
                        @endif
                        <!-- Pagination End -->
                    </div>
                </div>

                <!-- Add Live Class Button Start -->
                <a href="{{ route('live-class.course-live-class.index') }}"
                    class="theme-btn theme-button3 quiz-back-btn default-hover-btn">{{ __('Back') }}</a>
                <a href="{{ route('live-class.create', $course->uuid) }}"
                    class="add-resources-btn theme-btn theme-button1 default-hover-btn">{{ __('Add Live Class') }}</a>
                <!-- Add Live Class Button End -->

            </div>
        </div>

    </div>
    <input type="hidden" id="base_url" value="{{url('/')}}">
</div>
@endsection

@section('modal')
<!--View Meeting Modal Start-->
<div class="modal fade viewMeetingLinkModal" id="viewMeetingModal" tabindex="-1" aria-labelledby="viewMeetingModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="viewMeetingModalLabel">{{ __('View Meeting') }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="d-none bbbMeetingDiv">
                    <div class="row mb-30">
                        <div class="col-md-12">
                            <div class="join-url-wrap position-relative">
                                <label class="font-medium font-15 color-heading">{{ __('Meeting ID') }}</label>
                                <input type="text" name="meeting_id" class="form-control" disabled readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-30">
                        <div class="col-md-12">
                            <div class="join-url-wrap position-relative">
                                <label class="font-medium font-15 color-heading">{{ __('Moderator Password') }}</label>
                                <input type="text" name="moderator_pw" class="form-control" disabled readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-30">
                        <div class="col-md-12">
                            <div class="join-url-wrap position-relative">
                                <label class="font-medium font-15 color-heading">{{ __('Attendee Password') }}</label>
                                <input type="" name="attendee_pw" class="form-control" disabled readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-30 d-none zoomMeetingDiv">
                    <div class="col-md-12">
                        <div class="join-url-wrap position-relative">
                            <label class="font-medium font-15 color-heading">{{ __('Start URL') }}</label>
                            <textarea name="start_url" class="start_url join-url-text form-control" id="start_url"
                                disabled readonly rows="3">
                            </textarea>
                            <button class="copy-text-btn position-absolute copyZoomStartUrl"><span class="iconify"
                                    data-icon="akar-icons:copy"></span></button>
                        </div>
                    </div>
                </div>
                <div class="row mb-30 d-none jitsiMeetingDiv">
                    <div class="col-md-12">
                        <div class="join-url-wrap position-relative">
                            <label class="font-medium font-15 color-heading">{{ __('Jitsi Meeting ID/Room') }}</label>
                            <input type="text" name="jitsi_meeting_id" class="form-control jitsi_meeting_id" disabled
                                readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between align-items-center">
                <a href="" target="_blank" class="theme-btn theme-button1 default-hover-btn green-theme-btn joinNow">{{
                    __('Start Now') }}</a>
            </div>
        </div>
    </div>
</div>
<!--View Meeting Modal End-->
@endsection
<script src="{{ asset('frontend/assets/vendor/jquery/jquery-3.6.0.min.js') }}"></script>
<script>
    //   var base_url = $('#base_url').val();
       var base_url = window.location.origin;
    $(document).ready(function () {
        ajaxCallfirstInstructor(); // To output when the page loads
        setInterval(ajaxCallfirstInstructor, (50 * 1000)); // x * 1000 to get it in seconds
    });


$(document).on("click", ".start_bbb_individual_live_class", function(event){
 event.preventDefault();
 meetingId = $(this).attr("data-meeting_id");
 lessonId = $(this).attr("data-lesson_id");
 tutorURI = $(this).attr("data-tutor_uri");
 var murl = tutorURI;
 var currentHref = murl;

    var murl_2 = base_url+'/'+ 'instructor/live-class/live-class/UpdateTutorMeetingStatusLiveClass/' +meetingId+'/'+lessonId;
  $.ajax({
    type: "GET",
    url: murl_2,
            dataType: "json",
            success: (function (data) {
                window.open(currentHref, '_blank');
            })
    });

});


$(document).on("click", ".start_meet_individual_live_class", function(event){
 event.preventDefault();
 meetingId = $(this).attr("data-meeting_id");
 lessonId = $(this).attr("data-lesson_id");
 tutorURI = $(this).attr("data-tutor_uri");
 var murl = tutorURI;
 var currentHref = murl;

    var murl_2 = base_url+'/'+ 'instructor/live-class/live-class/UpdateTutorMeetingStatusLiveClass/' +meetingId+'/'+lessonId;
  $.ajax({
    type: "GET",
    url: murl_2,
            dataType: "json",
            success: (function (data) {
                window.open(currentHref, '_blank');
            })
    });

});



$(document).on("click", ".create_zoom_meeting_link_live_class", function(event) {
        event.preventDefault();
        meetingId = $(this).attr("data-meeting_id");
        userId = $(this).attr("data-user_id") || 'NewUser_' + (new Date()).getTime();
        userName = $(this).attr("data-user_name") || 'New_Tutor';
        var res = userName.replace(/\s+/g, "");
        userType = $(this).attr("data-user_type");
        var murl = base_url + '/instructor/live-class/live-class/NewZoomMeetingLiveClass/' + meetingId + '/' + userType + '/' + res + '/' + userId;
        // window.open(murl, '_blank');
     
        window.open(murl, '_self');
});

    function ajaxCallfirstInstructor() {
        var base_url = window.location.origin;
        setTimeout(() => {
            $.ajax({
                type: "POST",
                url: base_url + "/instructor/live-class/live-class/checkLessonStartTimeLiveClass",
                data: { "_token": "{{ csrf_token() }}" },
                dataType: "JSON",
                success: (function (result) {
                    console.log(result);
                    console.log(result.l_id);
                    console.log(result.learning_tool);
                    console.log(result.meeting_id);
                    console.log(result.user_name);
                    console.log(result.user_type);
                    $('#lesson_id').val(result.l_id);
                 
                        console.log("ENTERED INSIDE NOW");
                        if (result.learning_tool == 'Zoom') {
                            $('#lec_link1_' + result.l_id).attr('href', 'javascript:void(0);');
                            // $('#lec_link1_' + result.l_id).attr('target', '_blank');
                            $('#lec_link1_' + result.l_id).attr('target', '_self');
                            $('#lec_link1_' + result.l_id).html("Start Class");
                            $('#lec_link1_' + result.l_id).css("background-color", "#499619");
                            $('#lec_link1_' + result.l_id).css("color", "#fff");
                            $('#lec_link1_' + result.l_id).attr('data-meeting_id', result.meeting_id);
                            $('#lec_link1_' + result.l_id).attr('data-user_name', result.user_name);
                            $('#lec_link1_' + result.l_id).attr('data-user_id', result.user_id);
                            $('#lec_link1_' + result.l_id).attr('data-user_type', result.user_type);
                            $('#lec_link1_' + result.l_id).addClass('create_zoom_meeting_link_live_class');
                        }
                        else if(result.learning_tool == 'BBB'){
                            console.log("ENTERED INSIDE BBB NOW");
                        $('#lec_link1_'+result.l_id).attr('href', 'javascript:void(0);'); 
                        $('#lec_link1_'+result.l_id).attr('target', '_blank'); 
                        $('#lec_link1_'+result.l_id).html("Start Class"); 
                        $('#lec_link1_'+result.l_id).css("background-color", "#499619"); 
                        $('#lec_link1_'+result.l_id).css("color", "#fff"); 
                        $('#lec_link1_'+result.l_id).attr('data-lesson_id', result.l_id); 
                        $('#lec_link1_'+result.l_id).attr('data-meeting_id', result.meeting_id); 
                        $('#lec_link1_'+result.l_id).attr('data-tutor_uri', result.url);
                        $('#lec_link1_'+result.l_id).addClass('start_bbb_individual_live_class'); 
                        }
                        else if(result.learning_tool == 'Google_Meet'){
                            console.log("ENTERED INSIDE Google_Meet NOW");
                        $('#lec_link1_'+result.l_id).attr('href', 'javascript:void(0);'); 
                        $('#lec_link1_'+result.l_id).attr('target', '_blank'); 
                        $('#lec_link1_'+result.l_id).html("Start Class"); 
                        $('#lec_link1_'+result.l_id).css("background-color", "#499619"); 
                        $('#lec_link1_'+result.l_id).css("color", "#fff"); 
                        $('#lec_link1_'+result.l_id).attr('data-lesson_id', result.l_id); 
                        $('#lec_link1_'+result.l_id).attr('data-meeting_id', result.meeting_id); 
                        $('#lec_link1_'+result.l_id).attr('data-tutor_uri', result.url);
                        $('#lec_link1_'+result.l_id).addClass('start_meet_individual_live_class'); 
                        }
                        IsmeetingCreated = $('#lec_link1_' + result.l_id).attr('data-meeting_created');
                        if (IsmeetingCreated != 'Yes') {
                            var audioElement = document.createElement('audio');
                            // audioElement.setAttribute('src', base_url + '/public/assets/notification_sound/Cool_Notification_2.mp3');
                        }
                        $('#lec_link1_' + result.l_id).attr('data-meeting_created', 'Yes');
                }),
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    console.log("Status: " + textStatus);
                    console.log("Error: " + errorThrown);
                }
            }).fail(function (jqXHR, textStatus, error) {
                console.log(jqXHR.responseText);
                console.log(error);
                console.log(textStatus);
            });
        }, 5000);
    }
</script>

@push('script')
<script src="{{ asset('frontend/assets/js/instructor/copy-zoom-url-and-show.js') }}"></script>
@endpush

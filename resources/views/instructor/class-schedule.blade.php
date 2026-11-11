@extends('layouts.instructor')

@section('breadcrumb')
    <div class="page-banner-content text-center">
        <h3 class="page-banner-heading text-white pb-15">{{ __('Class Schedule') }}</h3>
        <!-- Breadcrumb Start-->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item font-14"><a href="{{ route('instructor.dashboard') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item font-14 active" aria-current="page">{{ __('Class Schedule') }}</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content3')
    <div class="instructor-profile-right-part">
        <div class="instructor-quiz-list-page instructor-live-class-page">
            <div class="instructor-my-courses-title d-flex justify-content-between align-items-center">
                <h6>{{ __('Individual Class List') }}</h6>
            </div>
            <input type="hidden" name="base_url" id="base_url" value="{{ $base_url }}">
            <div class="row">
                <div class="col-12">
                    @if (!$getIndividualClasses->isEmpty())
                        {{-- Classes for desktop view --}}
                        <div class="table-responsive table-responsive-xl courses-table">
                            <table class="table" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __('No') }}</th>
                                        <th scope="col">{{ __('Student') }}</th>
                                     
                                        <th scope="col">{{ __('Course') }}</th>
                                        <th scope="col">{{ __('Learning Tool') }}</th>
                                        <th scope="col">{{ __('Date Time') }}</th>
                                        <th scope="col">{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i = 0; @endphp
                                    @foreach ($getIndividualClasses as $individual)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>
                                                <div class="table-data-with-img d-flex align-items-center">
                                                    <div class="flex-grow-1 table-data-course-name">{{ Str::limit($individual->student->first_name . ' ' . $individual->student->last_name, 50) }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="table-data-with-img d-flex align-items-center">
                                                    <div class="flex-grow-1 table-data-course-name">{{ Str::limit($individual->course->title, 50) }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="table-data-with-img d-flex align-items-center">
                                                    <div class="flex-grow-1 table-data-course-name">{{ Str::limit($individual->class?->learning_tool, 50) }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $updated_time = changetimeByZone($individual->class_time, $individual->instructor_id, 'instructor');
                                                    $lesson_time = $updated_time;
                                                    $curdate = strtotime(date("Y-m-d"));
                                                    $lsndate = strtotime($individual->class_date);
                                                    $updated_cur_time = changetimeByZone(date("H:i"), $individual->instructor_id, 'instructor');
                                                    $curtime = strtotime($updated_cur_time);
                                                    $lsntime = strtotime($lesson_time);
                                                    $lesson_duration = $individual->class->duration;
                                                    $lsntimestamp = strtotime($lesson_time) + $lesson_duration * 60;
                                                    $lsnendtime = $lsntimestamp;
                                                @endphp
                                                {{ date('d-m-Y', strtotime($individual->class_date)) . ' ' . $updated_time }}
                                            </td>
                                            <td>
                                                @php
                                                    $updated_time = changetimeByZone($individual->class_time, $individual->instructor_id, 'instructor');
                                                    $lesson_time = $updated_time;
                                                    $curdate = strtotime(date("Y-m-d"));
                                                    $lsndate = strtotime($individual->class_date);
                                                    $updated_cur_time = changetimeByZone(date("H:i"), $individual->instructor_id, 'instructor');
                                                    $curtime = strtotime($updated_cur_time);
                                                    $lsntime = strtotime($lesson_time);
                                                    $lesson_duration = $individual->class->duration;
                                                    $lsntimestamp = strtotime($lesson_time) + $lesson_duration * 60;
                                                    $lsnendtime = $lsntimestamp;
                                                @endphp
                                                <?php if ($curdate < $lsndate) { ?>
                                                    <span style="border:none;font-weight:bolder;background-color:#322740;color:white;" class="btn btn-warning m-1 py-1 px-2 small">Scheduled</span>
                                                <?php } 
                                                else if ($curdate == $lsndate) {
                                                    if ($curtime < $lsntime) {
                                                        $id = 'lec_link1_' . $individual->id;
                                                        echo "<a class='btn btn-warning m-1 lec_link1 px-2 py-1 small' id='$id'>" . 'Waiting' . "</a>";
                                                    } 
                                                    else if ($curtime >= $lsntime && $curtime < $lsnendtime && $individual->meeting_id == NULL && $individual->class->learning_tool == 'Zoom') {
                                                        $id = 'lec_link1_' . $individual->id;
                                                        echo "<a class='btn btn-warning m-1 lec_link1 px-2 py-1 small' id='$id'>" . 'Waiting' . "</a>";
                                                    } 
                                                    else if ($curtime >= $lsntime && $curtime < $lsnendtime && $individual->InstructorURI == NULL && $individual->class->learning_tool == 'BBB') {
                                                        $id = 'lec_link1_' . $individual->id;
                                                        echo "<a class='btn btn-warning m-1 lec_link1 px-2 py-1 small' id='$id'>" . 'Waiting' . "</a>";
                                                    } 
                                                    else if ($curtime >= $lsntime && $curtime < $lsnendtime && $individual->google_meet_link == NULL && $individual->class->learning_tool == 'Google_Meet') {
                                                        $id = 'lec_link1_' . $individual->id;
                                                        echo "<a class='btn btn-warning m-1 lec_link1 px-2 py-1 small' id='$id'>" . 'Waiting' . "</a>";
                                                    } 

                                                    else if ($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Zoom') { ?>
                                                        <a href="javascript:void(0);" data-meeting_id="<?php echo $individual->meeting_id; ?>" data-user_type="Tutor" data-user_name="<?php echo $individual->instructor->first_name . ' ' . $individual->instructor->last_name; ?>" data-user_id="<?php echo $individual->instructor_id; ?>" class="btn btn-info m-1 create_zoom_meeting_link py-1 px-2 small" style="background-color:#499619;color: #fff" target='_self'>Start Class</a>
                                                    <?php } 
                                                    else if ($curtime == $lsntime && ($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'Zoom') {
                                                        if ($individual->status == "Completed" && $individual->is_moderator_left == '1') {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } else if ($individual->status == "Missed" && $individual->is_moderator_left == '1') {
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                        }
                                                    } 
                                                    else if ($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'BBB') { ?>
                                                         <a  href="javascript:void(0);" data-tutor_uri ="<?php echo $individual->InstructorURI ; ?>" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>"  class='btn btn-info m-1 start_bbb_individual py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                    <?php } 
                                                    else if ($curtime == $lsntime && ($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'BBB') {
                                                        if ($individual->status == "Completed" && $individual->InstructorURI != null) {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } else if ($individual->status == "Missed" && $individual->InstructorURI != null) {
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                        }
                                                    } 
                                                    else if ($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Google_Meet') { ?>
                                                         <a  href="javascript:void(0);" data-tutor_uri ="<?php echo $individual->google_meet_link ; ?>" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>"  class='btn btn-info m-1 start_meet_individual py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                    <?php } 
                                                    else if ($curtime == $lsntime && ($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'Google_Meet') {
                                                        if ($individual->status == "Completed" && $individual->meeting_id != null) {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } else if ($individual->status == "Missed" && $individual->meeting_id != null) {
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                        }
                                                    } 

                                                    else if ($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Zoom') { ?>
                                                        <a href="javascript:void(0);" id="lec_link" data-meeting_id="<?php echo $individual->meeting_id; ?>" data-user_type="Tutor" data-user_name="<?php echo $individual->instructor->first_name . ' ' . $individual->instructor->last_name; ?>" data-user_id="<?php echo $individual->instructor_id; ?>" class="btn btn-info m-1 create_zoom_meeting_link py-1 px-2 small" style="background-color:#499619;color: #fff" target='_self'>Start Class</a>
                                                    <?php } 
                                                    else if ($curtime > $lsntime && $curtime < $lsnendtime && $individual->class->learning_tool == 'Zoom') {
                                                        if ($individual->status == "Completed" && $individual->is_moderator_left == '1') {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } else if ($individual->status == "Missed" && $individual->is_moderator_left == '1') {
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                        } else { ?>
                                                            <a href="javascript:void(0);" id="lec_link" data-meeting_id="<?php echo $individual->meeting_id; ?>" data-user_type="Tutor" data-user_name="<?php echo $individual->instructor->first_name . ' ' . $individual->instructor->last_name; ?>" data-user_id="<?php echo $individual->instructor_id; ?>" class="btn btn-info m-1 create_zoom_meeting_link py-1 px-2 small" style="background-color:#499619;color: #fff" target='_self'>Start Class</a>
                                                        <?php }
                                                    } 
                                                    
                                                    else if ($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'BBB') { ?>
                                                           <a  href="javascript:void(0);" data-tutor_uri ="<?php echo $individual->InstructorURI ; ?>" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>"  class='btn btn-info m-1 start_bbb_individual py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                    <?php } 
                                                    else if ($curtime > $lsntime && $curtime < $lsnendtime && $individual->class->learning_tool == 'BBB') {
                                                        if ($individual->status == "Completed" && $individual->InstructorURI != null) {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } else if ($individual->status == "Missed" && $individual->InstructorURI != null) {
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                        } else { ?>
                                                              <a  href="javascript:void(0);" data-tutor_uri ="<?php echo $individual->InstructorURI ; ?>" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>"  class='btn btn-info m-1 start_bbb_individual py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                        <?php }
                                                    } 
                                                    else if ($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Google_Meet') { ?>
                                                           <a  href="javascript:void(0);" data-tutor_uri ="<?php echo $individual->google_meet_link ; ?>" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>"  class='btn btn-info m-1 start_meet_individual py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                    <?php } 
                                                    else if ($curtime > $lsntime && $curtime < $lsnendtime && $individual->class->learning_tool == 'Google_Meet') {
                                                        if ($individual->status == "Completed" && $individual->google_meet_link != null) {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } else if ($individual->status == "Missed" && $individual->google_meet_link != null) {
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                        } else { ?>
                                                              <a  href="javascript:void(0);" data-tutor_uri ="<?php echo $individual->google_meet_link ; ?>" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>"  class='btn btn-info m-1 start_meet_individual py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                        <?php }
                                                    }

                                                    
                                                    else if ($curtime == $lsnendtime && $individual->class->learning_tool == 'Zoom') {
                                                        if (($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'Zoom' && ($individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined)) {
                                                            updateLessonStatusCompleteZoom($individual->id, $individual->meeting_id, null);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } else if (($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'Zoom' && $individual->is_instructor_joined == '0') {
                                                            updateLessonStatusMissedZoom($individual->id, $individual->meeting_id, null);
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                        } else {
                                                            if ($individual->status == "Completed") {
                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                            } else {
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                        }
                                                    } 
                                                    else if ($curtime == $lsnendtime && $individual->class->learning_tool == 'BBB') {
                                                        if (($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'BBB' && ($individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined)) {
                                                            updateLessonStatusComplete($individual->id, $individual->meeting_id, null);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } else if (($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'BBB' && $individual->is_instructor_joined == '0') {
                                                            updateLessonStatusMissed($individual->id, $individual->meeting_id, null);
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                        } else {
                                                            if ($individual->status == "Completed") {
                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                            } else {
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                        }
                                                    } 
                                                    else if ($curtime == $lsnendtime && $individual->class->learning_tool == 'Google_Meet') {
                                                        if (($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'Google_Meet' && ($individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined)) {
                                                            updateLessonStatusComplete($individual->id, $individual->meeting_id, null);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } else if (($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'Google_Meet' && $individual->is_instructor_joined == '0') {
                                                            updateLessonStatusMissed($individual->id, $individual->meeting_id, null);
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                        } else {
                                                            if ($individual->status == "Completed") {
                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                            } else {
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                        }
                                                    } 

                                                    
                                                    else if ($curtime > $lsnendtime) {
                                                        if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Zoom' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {
                                                            updateLessonStatusCompleteZoom($individual->id, $individual->meeting_id, null);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } 
                                                        else if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'BBB' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {
                                                            updateLessonStatusComplete($individual->id, $individual->meeting_id, null);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } 
                                                        else if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Google_Meet' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {
                                                            updateLessonStatusComplete($individual->id, $individual->meeting_id, null);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } 
                                                        else if ($individual->status == "Completed") {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } 
                                                        else {
                                                            if ($individual->status == "Missed") {
                                                                if (!empty($individual->meeting_id)) {
                                                                    $res = check_missed_status($individual->meeting_id);
                                                                    if ($res == 'Completed') {
                                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                                    } else {
                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                                    }
                                                                } else {
                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                                }
                                                            } 
                                                            else if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == "Zoom" && $individual->is_instructor_joined == '0') {
                                                                updateLessonStatusMissedZoom($individual->id, $individual->meeting_id, null);
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                            else if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == "BBB" && $individual->is_instructor_joined == '0') {
                                                                updateLessonStatusMissed($individual->id, $individual->meeting_id, null);
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                            else if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == "Google_Meet" && $individual->is_instructor_joined == '0') {
                                                                updateLessonStatusMissed($individual->id, $individual->meeting_id, null);
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                             else {
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                        }
                                                    }
                                                } 
                                                else if ($curdate > $lsndate) {
                                                    if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Zoom' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {
                                                        updateLessonStatusCompleteZoom($individual->id, $individual->meeting_id, null);
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } 
                                                    else if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'BBB' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {
                                                        updateLessonStatusComplete($individual->id, $individual->meeting_id, null);
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } 
                                                    else if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Google_Meet' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {
                                                        updateLessonStatusComplete($individual->id, $individual->meeting_id, null);
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } 
                                                    else if ($individual->status == "Completed") {
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } 
                                                    else if (($individual->status == "Pending" || $individual->status == "Scheduled")) {
                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                    } 
                                                    else {
                                                        if ($individual->status == "Missed") {
                                                            if (!empty($individual->meeting_id)) {
                                                                $res = check_missed_status($individual->meeting_id);
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
                                                } else {
                                                    if ($individual->status == "Completed") {
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } else {
                                                        if ($individual->status == "Missed") {
                                                            if (!empty($individual->meeting_id)) {
                                                                $res = check_missed_status($individual->meeting_id);
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
                                                } ?>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{-- Classes for mobile view --}}
                        <div class="courses-cards">
                            @php $i = 0; @endphp
                            @foreach ($getIndividualClasses as $individual)
                                <div class="course-card">
                                    <div class="font-15 color-heading">
                                        <strong>{{ __('Student Name') }}</strong>
                                        <div class="table-data-with-img d-flex align-items-center">
                                                <div class="flex-grow-1 table-data-course-name">{{ Str::limit($individual->student->first_name . ' ' . $individual->student->last_name, 50) }}</div>
                                            </div>
                                    </div>
                                    <div class="font-15 color-heading">
                                        <strong>{{ __('Course') }}</strong>
                                        <div class="table-data-with-img d-flex align-items-center">
                                                <div class="flex-grow-1 table-data-course-name">{{ Str::limit($individual->course->title, 50) }}</div>
                                            </div>
                                    </div>
                                    <div class="font-15 color-heading">
                                        <strong>{{ __('Learning Tool') }}</strong>
                                        <div class="table-data-with-img d-flex align-items-center">
                                                    <div class="flex-grow-1 table-data-course-name">{{ Str::limit($individual->class?->learning_tool, 50) }}</div>
                                                </div>
                                    </div>
                                    <div class="font-15 color-heading">
                                        <strong>{{ __('Date Time') }}</strong>
                                        @php
                                                    $updated_time = changetimeByZone($individual->class_time, $individual->instructor_id, 'instructor');
                                                    $lesson_time = $updated_time;
                                                    $curdate = strtotime(date("Y-m-d"));
                                                    $lsndate = strtotime($individual->class_date);
                                                    $updated_cur_time = changetimeByZone(date("H:i"), $individual->instructor_id, 'instructor');
                                                    $curtime = strtotime($updated_cur_time);
                                                    $lsntime = strtotime($lesson_time);
                                                    $lesson_duration = $individual->class->duration;
                                                    $lsntimestamp = strtotime($lesson_time) + $lesson_duration * 60;
                                                    $lsnendtime = $lsntimestamp;
                                                @endphp
                                                {{ date('d-m-Y', strtotime($individual->class_date)) . ' ' . $updated_time }}</div>
                                    <div class="font-15 color-heading">
                                        <strong>{{ __('Status') }}</strong>
                                        @php
                                                    $updated_time = changetimeByZone($individual->class_time, $individual->instructor_id, 'instructor');
                                                    $lesson_time = $updated_time;
                                                    $curdate = strtotime(date("Y-m-d"));
                                                    $lsndate = strtotime($individual->class_date);
                                                    $updated_cur_time = changetimeByZone(date("H:i"), $individual->instructor_id, 'instructor');
                                                    $curtime = strtotime($updated_cur_time);
                                                    $lsntime = strtotime($lesson_time);
                                                    $lesson_duration = $individual->class->duration;
                                                    $lsntimestamp = strtotime($lesson_time) + $lesson_duration * 60;
                                                    $lsnendtime = $lsntimestamp;
                                                @endphp
                                                <?php if ($curdate < $lsndate) { ?>
                                                    <span style="border:none;font-weight:bolder;background-color:#322740;color:white;" class="btn btn-warning m-1 py-1 px-2 small">Scheduled</span>
                                                <?php } 
                                                else if ($curdate == $lsndate) {
                                                    if ($curtime < $lsntime) {
                                                        $id = 'lec_link1_' . $individual->id;
                                                        echo "<a class='btn btn-warning m-1 lec_link1 px-2 py-1 small' id='$id'>" . 'Waiting' . "</a>";
                                                    } 
                                                    else if ($curtime >= $lsntime && $curtime < $lsnendtime && $individual->meeting_id == NULL && $individual->class->learning_tool == 'Zoom') {
                                                        $id = 'lec_link1_' . $individual->id;
                                                        echo "<a class='btn btn-warning m-1 lec_link1 px-2 py-1 small' id='$id'>" . 'Waiting' . "</a>";
                                                    } 
                                                    else if ($curtime >= $lsntime && $curtime < $lsnendtime && $individual->InstructorURI == NULL && $individual->class->learning_tool == 'BBB') {
                                                        $id = 'lec_link1_' . $individual->id;
                                                        echo "<a class='btn btn-warning m-1 lec_link1 px-2 py-1 small' id='$id'>" . 'Waiting' . "</a>";
                                                    } 
                                                    else if ($curtime >= $lsntime && $curtime < $lsnendtime && $individual->google_meet_link == NULL && $individual->class->learning_tool == 'Google_Meet') {
                                                        $id = 'lec_link1_' . $individual->id;
                                                        echo "<a class='btn btn-warning m-1 lec_link1 px-2 py-1 small' id='$id'>" . 'Waiting' . "</a>";
                                                    } 

                                                    else if ($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Zoom') { ?>
                                                        <a href="javascript:void(0);" data-meeting_id="<?php echo $individual->meeting_id; ?>" data-user_type="Tutor" data-user_name="<?php echo $individual->instructor->first_name . ' ' . $individual->instructor->last_name; ?>" data-user_id="<?php echo $individual->instructor_id; ?>" class="btn btn-info m-1 create_zoom_meeting_link py-1 px-2 small" style="background-color:#499619;color: #fff" target='_self'>Start Class</a>
                                                    <?php } 
                                                    else if ($curtime == $lsntime && ($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'Zoom') {
                                                        if ($individual->status == "Completed" && $individual->is_moderator_left == '1') {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } else if ($individual->status == "Missed" && $individual->is_moderator_left == '1') {
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                        }
                                                    } 
                                                    else if ($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'BBB') { ?>
                                                         <a  href="javascript:void(0);" data-tutor_uri ="<?php echo $individual->InstructorURI ; ?>" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>"  class='btn btn-info m-1 start_bbb_individual py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                    <?php } 
                                                    else if ($curtime == $lsntime && ($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'BBB') {
                                                        if ($individual->status == "Completed" && $individual->InstructorURI != null) {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } else if ($individual->status == "Missed" && $individual->InstructorURI != null) {
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                        }
                                                    } 
                                                    else if ($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Google_Meet') { ?>
                                                         <a  href="javascript:void(0);" data-tutor_uri ="<?php echo $individual->google_meet_link ; ?>" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>"  class='btn btn-info m-1 start_meet_individual py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                    <?php } 
                                                    else if ($curtime == $lsntime && ($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'Google_Meet') {
                                                        if ($individual->status == "Completed" && $individual->google_meet_link != null) {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } else if ($individual->status == "Missed" && $individual->google_meet_link != null) {
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                        }
                                                    } 

                                                    else if ($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Zoom') { ?>
                                                        <a href="javascript:void(0);" id="lec_link" data-meeting_id="<?php echo $individual->meeting_id; ?>" data-user_type="Tutor" data-user_name="<?php echo $individual->instructor->first_name . ' ' . $individual->instructor->last_name; ?>" data-user_id="<?php echo $individual->instructor_id; ?>" class="btn btn-info m-1 create_zoom_meeting_link py-1 px-2 small" style="background-color:#499619;color: #fff" target='_self'>Start Class</a>
                                                    <?php } 
                                                    else if ($curtime > $lsntime && $curtime < $lsnendtime && $individual->class->learning_tool == 'Zoom') {
                                                        if ($individual->status == "Completed" && $individual->is_moderator_left == '1') {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } else if ($individual->status == "Missed" && $individual->is_moderator_left == '1') {
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                        } else { ?>
                                                            <a href="javascript:void(0);" id="lec_link" data-meeting_id="<?php echo $individual->meeting_id; ?>" data-user_type="Tutor" data-user_name="<?php echo $individual->instructor->first_name . ' ' . $individual->instructor->last_name; ?>" data-user_id="<?php echo $individual->instructor_id; ?>" class="btn btn-info m-1 create_zoom_meeting_link py-1 px-2 small" style="background-color:#499619;color: #fff" target='_self'>Start Class</a>
                                                        <?php }
                                                    } 
                                                    else if ($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'BBB') { ?>
                                                           <a  href="javascript:void(0);" data-tutor_uri ="<?php echo $individual->InstructorURI ; ?>" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>"  class='btn btn-info m-1 start_bbb_individual py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                    <?php } 
                                                    else if ($curtime > $lsntime && $curtime < $lsnendtime && $individual->class->learning_tool == 'BBB') {
                                                        if ($individual->status == "Completed" && $individual->InstructorURI != null) {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } else if ($individual->status == "Missed" && $individual->InstructorURI != null) {
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                        } else { ?>
                                                              <a  href="javascript:void(0);" data-tutor_uri ="<?php echo $individual->InstructorURI ; ?>" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>"  class='btn btn-info m-1 start_bbb_individual py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                        <?php }
                                                    } 
                                                    else if ($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Google_Meet') { ?>
                                                           <a  href="javascript:void(0);" data-tutor_uri ="<?php echo $individual->google_meet_link ; ?>" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>"  class='btn btn-info m-1 start_meet_individual py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                    <?php } 
                                                    else if ($curtime > $lsntime && $curtime < $lsnendtime && $individual->class->learning_tool == 'Google_Meet') {
                                                        if ($individual->status == "Completed" && $individual->google_meet_link != null) {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } else if ($individual->status == "Missed" && $individual->google_meet_link != null) {
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                        } else { ?>
                                                              <a  href="javascript:void(0);" data-tutor_uri ="<?php echo $individual->google_meet_link ; ?>" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>"  class='btn btn-info m-1 start_meet_individual py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                        <?php }
                                                    }

                                                    
                                                    else if ($curtime == $lsnendtime && $individual->class->learning_tool == 'Zoom') {
                                                        if (($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'Zoom' && ($individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined)) {
                                                            updateLessonStatusCompleteZoom($individual->id, $individual->meeting_id, null);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } else if (($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'Zoom' && $individual->is_instructor_joined == '0') {
                                                            updateLessonStatusMissedZoom($individual->id, $individual->meeting_id, null);
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                        } else {
                                                            if ($individual->status == "Completed") {
                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                            } else {
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                        }
                                                    } 
                                                    else if ($curtime == $lsnendtime && $individual->class->learning_tool == 'BBB') {
                                                        if (($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'BBB' && ($individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined)) {
                                                            updateLessonStatusComplete($individual->id, $individual->meeting_id, null);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } else if (($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'BBB' && $individual->is_instructor_joined == '0') {
                                                            updateLessonStatusMissed($individual->id, $individual->meeting_id, null);
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                        } else {
                                                            if ($individual->status == "Completed") {
                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                            } else {
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                        }
                                                    } 
                                                    else if ($curtime == $lsnendtime && $individual->class->learning_tool == 'Google_Meet') {
                                                        if (($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'Google_Meet' && ($individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined)) {
                                                            updateLessonStatusComplete($individual->id, $individual->meeting_id, null);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } else if (($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'Google_Meet' && $individual->is_instructor_joined == '0') {
                                                            updateLessonStatusMissed($individual->id, $individual->meeting_id, null);
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                        } else {
                                                            if ($individual->status == "Completed") {
                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                            } else {
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                        }
                                                    } 

                                                    
                                                    else if ($curtime > $lsnendtime) {
                                                        if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Zoom' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {
                                                            updateLessonStatusCompleteZoom($individual->id, $individual->meeting_id, null);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } 
                                                        else if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'BBB' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {
                                                            updateLessonStatusComplete($individual->id, $individual->meeting_id, null);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } 
                                                        else if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Google_Meet' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {
                                                            updateLessonStatusComplete($individual->id, $individual->meeting_id, null);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } 
                                                        else if ($individual->status == "Completed") {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } 
                                                        else {
                                                            if ($individual->status == "Missed") {
                                                                if (!empty($individual->meeting_id)) {
                                                                    $res = check_missed_status($individual->meeting_id);
                                                                    if ($res == 'Completed') {
                                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                                    } else {
                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                                    }
                                                                } else {
                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                                }
                                                            } 
                                                            else if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == "Zoom" && $individual->is_instructor_joined == '0') {
                                                                updateLessonStatusMissedZoom($individual->id, $individual->meeting_id, null);
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                            else if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == "BBB" && $individual->is_instructor_joined == '0') {
                                                                updateLessonStatusMissed($individual->id, $individual->meeting_id, null);
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                            else if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == "Google_Meet" && $individual->is_instructor_joined == '0') {
                                                                updateLessonStatusMissed($individual->id, $individual->meeting_id, null);
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                             else {
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                        }
                                                    }
                                                } 
                                                else if ($curdate > $lsndate) {
                                                    if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Zoom' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {
                                                        updateLessonStatusCompleteZoom($individual->id, $individual->meeting_id, null);
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } 
                                                    else if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'BBB' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {
                                                        updateLessonStatusComplete($individual->id, $individual->meeting_id, null);
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } 
                                                    else if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Google_Meet' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {
                                                        updateLessonStatusComplete($individual->id, $individual->meeting_id, null);
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } 
                                                    else if ($individual->status == "Completed") {
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } 
                                                    else if (($individual->status == "Pending" || $individual->status == "Scheduled")) {
                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                    } 
                                                    else {
                                                        if ($individual->status == "Missed") {
                                                            if (!empty($individual->meeting_id)) {
                                                                $res = check_missed_status($individual->meeting_id);
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
                                                } else {
                                                    if ($individual->status == "Completed") {
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } else {
                                                        if ($individual->status == "Missed") {
                                                            if (!empty($individual->meeting_id)) {
                                                                $res = check_missed_status($individual->meeting_id);
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
                                                } ?>
                                    </div>
                                    </div>
                            @endforeach
            </div>
                        <!-- Pagination Start -->
                        @if (@$getIndividualClasses->hasPages())
                            {{ @$getIndividualClasses->links('frontend.paginate.paginate') }}
                        @endif
                        <!-- Pagination End -->
                    @else
                        <!-- If there is no data Show Empty Design Start -->
                        <div class="empty-data">
                            <img src="{{ asset('frontend/assets/img/empty-data-img.png') }}" alt="img" class="img-fluid">
                            <h5 class="my-3">{{ __('No Individual Classes Available') }}</h5>
                        </div>
                        <!-- If there is no data Show Empty Design End -->
                    @endif
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="instructor-profile-right-part">
        <div class="instructor-quiz-list-page instructor-live-class-page">
            <div class="instructor-my-courses-title d-flex justify-content-between align-items-center">
                <h6>{{ __('Group Class List') }}</h6>
            </div>
            <div class="row">
                <div class="col-12">
                    @if (!$getGroupClasses->isEmpty())
                        <div class="table-responsive table-responsive-xl courses-table">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __('No') }}</th>
                                        <th scope="col">{{ __('No. Of. Students') }}</th>
                                        <th scope="col">{{ __('Course') }}</th>
                                        <th scope="col">{{ __('Learning Tool') }}</th>
                                    
                                        <th scope="col">{{ __('Date Time') }}</th>
                                        <th scope="col">{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $j = 0; @endphp
                                    @foreach ($getGroupClasses as $group)
                                        <tr>
                                            <td>{{ ++$j }}</td>
                                            <td>
                                                <a class="total_students" class="btn" data-class_id="<?php echo $group->class->id; ?>" href="javascript::void(0);" data-toggle="modal" data-target="#showstudentnames">
                                                    <div class="table-data-with-img d-flex align-items-center">
                                                        <div class="flex-grow-1 table-data-course-name" style="margin-top: 1.26rem;"><?php echo $group->class->no_of_students; ?></div>
                                                    </div>
                                                </a>&nbsp;
                                            </td>
                                            <td>
                                                <div class="table-data-with-img d-flex align-items-center">
                                                    <div class="flex-grow-1 table-data-course-name">{{ Str::limit($group->course->title, 50) }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="table-data-with-img d-flex align-items-center">
                                                    <div class="flex-grow-1 table-data-course-name">{{ Str::limit($group->class?->learning_tool, 50) }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $updated_time = changetimeByZone($group->class_time, $group->instructor_id, 'instructor');
                                                    $lesson_time = $updated_time;
                                                    $curdate = strtotime(date("d-m-Y"));
                                                    $lsndate = strtotime($group->class_date);
                                                    $updated_cur_time = changetimeByZone(date("H:i"), $group->instructor_id, 'instructor');
                                                    $curtime = strtotime($updated_cur_time);
                                                    $lsntime = strtotime($lesson_time);
                                                    $lesson_duration = $group->class->duration;
                                                    $lsntimestamp = strtotime($lesson_time) + $lesson_duration * 60;
                                                    $lsnendtime = $lsntimestamp;
                                                @endphp
                                                {{ date('d-m-Y', strtotime($group->class_date)) . ' ' . $updated_time }}
                                            </td>
                                            <td>
                                                @php
                                                    $updated_time = changetimeByZone($group->class_time, $group->instructor_id, 'instructor');
                                                    $lesson_time = $updated_time;
                                                    $curdate = strtotime(date("d-m-Y"));
                                                    $lsndate = strtotime($group->class_date);
                                                    $updated_cur_time = changetimeByZone(date("H:i"), $group->instructor_id, 'instructor');
                                                    $curtime = strtotime($updated_cur_time);
                                                    $lsntime = strtotime($lesson_time);
                                                    $lesson_duration = $group->class->duration;
                                                    $lsntimestamp = strtotime($lesson_time) + $lesson_duration * 60;
                                                    $lsnendtime = $lsntimestamp;
                                                @endphp
                                                <?php if ($curdate < $lsndate) { ?>
                                                    <span style="border:none;font-weight:bolder;background-color:#322740;color:white;" class="btn btn-warning m-1 py-1 px-2 small">Scheduled</span>
                                                <?php } else if ($curdate == $lsndate) {
                                                    if ($curtime < $lsntime) {
                                                        $id = 'lec_link2_' . $group->group_id;
                                                        echo "<a class='btn btn-warning m-1 lec_link2 px-2 py-1 small' id='$id'>" . 'Waiting' . "</a>";
                                                    } 
                                                    else if ($curtime >= $lsntime && $curtime < $lsnendtime && $group->meeting_id == NULL && $group->class->learning_tool == 'Zoom') {
                                                        $id = 'lec_link2_' . $group->group_id;
                                                        echo "<a class='btn btn-warning m-1 lec_link2 px-2 py-1 small' id='$id'>" . 'Waiting' . "</a>";
                                                    }
                                                    else if ($curtime >= $lsntime && $curtime < $lsnendtime && $group->InstructorURI == NULL && $group->class->learning_tool == 'BBB') {
                                                        $id = 'lec_link2_' . $group->group_id;
                                                        echo "<a class='btn btn-warning m-1 lec_link2 px-2 py-1 small' id='$id'>" . 'Waiting' . "</a>";
                                                    }
                                                    else if ($curtime >= $lsntime && $curtime < $lsnendtime && $group->google_meet_link == NULL && $group->class->learning_tool == 'Google_Meet') {
                                                        $id = 'lec_link2_' . $group->group_id;
                                                        echo "<a class='btn btn-warning m-1 lec_link2 px-2 py-1 small' id='$id'>" . 'Waiting' . "</a>";
                                                    }


                                                     else if ($curtime == $lsntime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'Zoom') { ?>
                                                        <a href="javascript:void(0);" id="lec_lnk" data-group_id="<?php echo $group->group_id; ?>" data-meeting_id="<?php echo $group->meeting_id; ?>" data-user_type="Tutor" data-user_name="<?php echo $group->instructor->first_name . ' ' . $group->instructor->last_name; ?>" data-user_id="<?php echo $group->instructor_id; ?>" class="btn btn-info m-1 create_zoom_meeting_link_2 py-1 px-2 small" style="background-color:#499619;color: #fff" target='_self'>Start Class</a>
                                                    <?php } 
                                                      else if ($curtime == $lsntime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'BBB') { ?>
                                                         <a  href="javascript:void(0);" data-group_id ="<?php echo $group->group_id ; ?>" data-tutor_uri ="<?php echo $group->InstructorURI ; ?>" data-lesson_id ="<?php echo $group->id ; ?>" data-meeting_id ="<?php echo $group->meeting_id ; ?>"  class='btn btn-info m-1 start_bbb_group py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>                         
                                                    <?php } 
                                                      else if ($curtime == $lsntime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'Google_Meet') { ?>
                                                         <a  href="javascript:void(0);" data-group_id ="<?php echo $group->group_id ; ?>" data-tutor_uri ="<?php echo $group->google_meet_link ; ?>" data-lesson_id ="<?php echo $group->id ; ?>" data-meeting_id ="<?php echo $group->meeting_id ; ?>"  class='btn btn-info m-1 start_meet_group py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>                         
                                                    <?php } 


                                                    else if ($curtime > $lsntime && $curtime < $lsnendtime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'Zoom') { ?>
                                                        <a href="javascript:void(0);" id="lec_lnk" data-group_id="<?php echo $group->group_id; ?>" data-meeting_id="<?php echo $group->meeting_id; ?>" data-user_type="Tutor" data-user_name="<?php echo $group->instructor->first_name . ' ' . $group->instructor->last_name; ?>" data-user_id="<?php echo $group->instructor_id; ?>" class="btn btn-info m-1 create_zoom_meeting_link_2 py-1 px-2 small" style="background-color:#499619;color: #fff" target='_self'>Start Class</a>
                                                    <?php } 
                                                     else if ($curtime > $lsntime && $curtime < $lsnendtime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'BBB') { ?>
                                                         <a  href="javascript:void(0);" data-group_id ="<?php echo $group->group_id ; ?>" data-tutor_uri ="<?php echo $group->InstructorURI ; ?>" data-lesson_id ="<?php echo $group->id ; ?>" data-meeting_id ="<?php echo $group->meeting_id ; ?>"  class='btn btn-info m-1 start_bbb_group py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>                         
                                                    <?php } 
                                                      else if ($curtime > $lsntime && $curtime < $lsnendtime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'Google_Meet') { ?>
                                                         <a  href="javascript:void(0);" data-group_id ="<?php echo $group->group_id ; ?>" data-tutor_uri ="<?php echo $group->google_meet_link ; ?>" data-lesson_id ="<?php echo $group->id ; ?>" data-meeting_id ="<?php echo $group->meeting_id ; ?>"  class='btn btn-info m-1 start_meet_group py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>                         
                                                    <?php } 


                                                    else if ($curtime > $lsntime && $curtime < $lsnendtime && $group->class->learning_tool == 'Zoom') {
                                                        if ($group->status == "Completed" && $group->is_moderator_left == '1') {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } else if ($group->status == "Missed" && $group->is_moderator_left == '1') {
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                        } else { ?>
                                                            <a href="javascript:void(0);" id="lec_lnk" data-group_id="<?php echo $group->group_id; ?>" data-meeting_id="<?php echo $group->meeting_id; ?>" data-user_type="Tutor" data-user_name="<?php echo $group->instructor->first_name . ' ' . $group->instructor->last_name; ?>" data-user_id="<?php echo $group->instructor_id; ?>" class="btn btn-info m-1 create_zoom_meeting_link_2 py-1 px-2 small" style="background-color:#499619;color: #fff" target='_self'>Start Class</a>
                                                        <?php }
                                                    } 
                                                    else if ($curtime > $lsntime && $curtime < $lsnendtime && $group->class->learning_tool == 'BBB') {
                                                        if ($group->status == "Completed" && $group->InstructorURI != null) {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } else if ($group->status == "Missed" && $group->InstructorURI != null) {
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                        } else { ?>
                                                            <a  href="javascript:void(0);" data-group_id ="<?php echo $group->group_id ; ?>" data-tutor_uri ="<?php echo $group->InstructorURI ; ?>" data-lesson_id ="<?php echo $group->id ; ?>" data-meeting_id ="<?php echo $group->meeting_id ; ?>"  class='btn btn-info m-1 start_bbb_group py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>                         
                                                        <?php }
                                                    } 
                                                    else if ($curtime > $lsntime && $curtime < $lsnendtime && $group->class->learning_tool == 'Google_Meet') {
                                                        if ($group->status == "Completed" && $group->google_meet_link != null) {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } else if ($group->status == "Missed" && $group->google_meet_link != null) {
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                        } else { ?>
                                                            <a  href="javascript:void(0);" data-group_id ="<?php echo $group->group_id ; ?>" data-tutor_uri ="<?php echo $group->google_meet_link ; ?>" data-lesson_id ="<?php echo $group->id ; ?>" data-meeting_id ="<?php echo $group->meeting_id ; ?>"  class='btn btn-info m-1 start_meet_group py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>                         
                                                        <?php }
                                                    } 


                                                    else if ($curtime == $lsnendtime && $group->class->learning_tool == 'Zoom') {
                                                        if (($group->status != "Completed" && $group->status != "Missed") && $group->class->learning_tool == 'Zoom' && ($group->is_instructor_joined == '1' || $group->is_instructor_joined == "1" || $group->is_instructor_joined == 1 || $group->is_instructor_joined)) {
                                                            updateLessonStatusCompleteZoom($group->id, $group->meeting_id, $group->group_id);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } else if (($group->status != "Completed" && $group->status != "Missed") && $group->class->learning_tool == 'Zoom' && $group->is_instructor_joined == '0') {
                                                            updateLessonStatusMissedZoom($group->id, $group->meeting_id, $group->group_id);
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                        } else {
                                                            if ($group->status == "Completed") {
                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                            } else {
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                        }
                                                    }
                                                    else if ($curtime == $lsnendtime && $group->class->learning_tool == 'BBB') {
                                                        if (($group->status != "Completed" && $group->status != "Missed") && $group->class->learning_tool == 'BBB' && ($group->is_instructor_joined == '1' || $group->is_instructor_joined == "1" || $group->is_instructor_joined == 1 || $group->is_instructor_joined)) {
                                                            updateLessonStatusComplete($group->id, $group->meeting_id, $group->group_id);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } else if (($group->status != "Completed" && $group->status != "Missed") && $group->class->learning_tool == 'BBB' && $group->is_instructor_joined == '0') {
                                                            updateLessonStatusMissed($group->id, $group->meeting_id, $group->group_id);
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                        } else {
                                                            if ($group->status == "Completed") {
                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                            } else {
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                        }
                                                    }
                                                    else if ($curtime == $lsnendtime && $group->class->learning_tool == 'Google_Meet') {
                                                        if (($group->status != "Completed" && $group->status != "Missed") && $group->class->learning_tool == 'Google_Meet' && ($group->is_instructor_joined == '1' || $group->is_instructor_joined == "1" || $group->is_instructor_joined == 1 || $group->is_instructor_joined)) {
                                                            updateLessonStatusComplete($group->id, $group->meeting_id, $group->group_id);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } else if (($group->status != "Completed" && $group->status != "Missed") && $group->class->learning_tool == 'Google_Meet' && $group->is_instructor_joined == '0') {
                                                            updateLessonStatusMissed($group->id, $group->meeting_id, $group->group_id);
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                        } else {
                                                            if ($group->status == "Completed") {
                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                            } else {
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                        }
                                                    }


                                                    else if ($curtime > $lsnendtime) {
                                                        if ($group->class->learning_tool == 'Zoom' && $group->is_instructor_joined == '1' || $group->is_instructor_joined == "1" || $group->is_instructor_joined == 1 || $group->is_instructor_joined) {
                                                            updateLessonStatusCompleteZoom($group->id, $group->meeting_id, $group->group_id);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } 
                                                        else if ($group->class->learning_tool == 'BBB' && $group->is_instructor_joined == '1' || $group->is_instructor_joined == "1" || $group->is_instructor_joined == 1 || $group->is_instructor_joined) {
                                                            updateLessonStatusComplete($group->id, $group->meeting_id, $group->group_id);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } 
                                                        else if ($group->class->learning_tool == 'Google_Meet' && $group->is_instructor_joined == '1' || $group->is_instructor_joined == "1" || $group->is_instructor_joined == 1 || $group->is_instructor_joined) {
                                                            updateLessonStatusComplete($group->id, $group->meeting_id, $group->group_id);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } 

                                                        else if ($group->status == "Completed") {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } 
                                                        else {
                                                            if ($group->status == "Missed") {
                                                                if (!empty($group->meeting_id)) {
                                                                    $res = check_missed_status($group->meeting_id);
                                                                    if ($res == 'Completed') {
                                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                                    } else {
                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                                    }
                                                                } else {
                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                                }
                                                            } 
                                                            else if (($group->status == "Pending" || $group->status == "Scheduled") && $group->class->learning_tool == "Zoom" && $group->is_instructor_joined == '0') {
                                                                updateLessonStatusMissedZoom($group->id, $group->meeting_id, $group->group_id);
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            } 
                                                            else if (($group->status == "Pending" || $group->status == "Scheduled") && $group->class->learning_tool == "BBB" && $group->is_instructor_joined == '0') {
                                                                updateLessonStatusMissed($group->id, $group->meeting_id, $group->group_id);
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            } 
                                                            else if (($group->status == "Pending" || $group->status == "Scheduled") && $group->class->learning_tool == "Google_Meet" && $group->is_instructor_joined == '0') {
                                                                updateLessonStatusMissed($group->id, $group->meeting_id, $group->group_id);
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            } 
                                                            else {
                                                                echo "<span class='lesson-status py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                        }
                                                    }
                                                    
                                                } else if ($curdate > $lsndate) {
                                                    if ($group->class->learning_tool == 'Zoom' && $group->is_instructor_joined == '1' || $group->is_instructor_joined == "1" || $group->is_instructor_joined == 1 || $group->is_instructor_joined) {
                                                        updateLessonStatusCompleteZoom($group->id, $group->meeting_id, $group->group_id);
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    }
                                                    else if ($group->class->learning_tool == 'BBB' && $group->is_instructor_joined == '1' || $group->is_instructor_joined == "1" || $group->is_instructor_joined == 1 || $group->is_instructor_joined) {
                                                        updateLessonStatusComplete($group->id, $group->meeting_id, $group->group_id);
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    }
                                                    else if ($group->class->learning_tool == 'Google_Meet' && $group->is_instructor_joined == '1' || $group->is_instructor_joined == "1" || $group->is_instructor_joined == 1 || $group->is_instructor_joined) {
                                                        updateLessonStatusComplete($group->id, $group->meeting_id, $group->group_id);
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    }
                                                     else if ($group->status == "Completed") {
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } else if (($group->status == "Pending" || $group->status == "Scheduled")) {
                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                    } else {
                                                        if ($group->status == "Missed") {
                                                            if (!empty($group->meeting_id)) {
                                                                $res = check_missed_status($group->meeting_id);
                                                                if ($res == 'Completed') {
                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                                } else {
                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                                }
                                                            } else {
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                        } else {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        }
                                                    }
                                                } else {
                                                    if ($group->status == "Completed") {
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } else {
                                                        if ($group->status == "Missed") {
                                                            if (!empty($group->meeting_id)) {
                                                                $res = check_missed_status($group->meeting_id);
                                                                if ($res == 'Completed') {
                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                                } else {
                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                                }
                                                            } else {
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                        } else {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        }
                                                    }
                                                } ?>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="courses-cards">
                                    @php $j = 0; @endphp
                                    @foreach ($getGroupClasses as $group)
                                        <div class="course-card">
                                            <div class="font-15 color-heading">
                                                <strong>{{ __('No. Of. Students') }}</strong>
                                                <a class="total_students" class="btn" data-class_id="<?php echo $group->class->id; ?>" href="javascript::void(0);" data-toggle="modal" data-target="#showstudentnames">
                                                    <div class="table-data-with-img d-flex align-items-center">
                                                        <div class="flex-grow-1 table-data-course-name" style="margin-top: 1.26rem;"><?php echo $group->class->no_of_students; ?></div>
                                                    </div>
                                                </a>&nbsp;
                                            </div>
                                            <div class="font-15 color-heading">
                                                <strong>{{ __('Course') }}</strong>
                                                <div class="table-data-with-img d-flex align-items-center">
                                                    <div class="flex-grow-1 table-data-course-name">{{ Str::limit($group->course->title, 50) }}</div>
                                                </div>
                                            </div>
                                            <div class="font-15 color-heading">
                                                <strong>{{ __('Date Time') }}</strong>
                                                @php
                                                    $updated_time = changetimeByZone($group->class_time, $group->instructor_id, 'instructor');
                                                    $lesson_time = $updated_time;
                                                    $curdate = strtotime(date("d-m-Y"));
                                                    $lsndate = strtotime($group->class_date);
                                                    $updated_cur_time = changetimeByZone(date("H:i"), $group->instructor_id, 'instructor');
                                                    $curtime = strtotime($updated_cur_time);
                                                    $lsntime = strtotime($lesson_time);
                                                    $lesson_duration = $group->class->duration;
                                                    $lsntimestamp = strtotime($lesson_time) + $lesson_duration * 60;
                                                    $lsnendtime = $lsntimestamp;
                                                @endphp
                                                {{ date('d-m-Y', strtotime($group->class_date)) . ' ' . $updated_time }}
                                            </div>
                                            <div class="font-15 color-heading">
                                                <strong>{{ __('Status') }}</strong>
                                                @php
                                                    $updated_time = changetimeByZone($group->class_time, $group->instructor_id, 'instructor');
                                                    $lesson_time = $updated_time;
                                                    $curdate = strtotime(date("d-m-Y"));
                                                    $lsndate = strtotime($group->class_date);
                                                    $updated_cur_time = changetimeByZone(date("H:i"), $group->instructor_id, 'instructor');
                                                    $curtime = strtotime($updated_cur_time);
                                                    $lsntime = strtotime($lesson_time);
                                                    $lesson_duration = $group->class->duration;
                                                    $lsntimestamp = strtotime($lesson_time) + $lesson_duration * 60;
                                                    $lsnendtime = $lsntimestamp;
                                                @endphp
                                                <?php if ($curdate < $lsndate) { ?>
                                                    <span style="border:none;font-weight:bolder;background-color:#322740;color:white;" class="btn btn-warning m-1 py-1 px-2 small">Scheduled</span>
                                                <?php } else if ($curdate == $lsndate) {
                                                    if ($curtime < $lsntime) {
                                                        $id = 'lec_link2_' . $group->group_id;
                                                        echo "<a class='btn btn-warning m-1 lec_link2 px-2 py-1 small' id='$id'>" . 'Waiting' . "</a>";
                                                    } 
                                                    else if ($curtime >= $lsntime && $curtime < $lsnendtime && $group->meeting_id == NULL && $group->class->learning_tool == 'Zoom') {
                                                        $id = 'lec_link2_' . $group->group_id;
                                                        echo "<a class='btn btn-warning m-1 lec_link2 px-2 py-1 small' id='$id'>" . 'Waiting' . "</a>";
                                                    }
                                                    else if ($curtime >= $lsntime && $curtime < $lsnendtime && $group->InstructorURI == NULL && $group->class->learning_tool == 'BBB') {
                                                        $id = 'lec_link2_' . $group->group_id;
                                                        echo "<a class='btn btn-warning m-1 lec_link2 px-2 py-1 small' id='$id'>" . 'Waiting' . "</a>";
                                                    }

                                                     else if ($curtime == $lsntime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'Zoom') { ?>
                                                        <a href="javascript:void(0);" id="lec_lnk" data-group_id="<?php echo $group->group_id; ?>" data-meeting_id="<?php echo $group->meeting_id; ?>" data-user_type="Tutor" data-user_name="<?php echo $group->instructor->first_name . ' ' . $group->instructor->last_name; ?>" data-user_id="<?php echo $group->instructor_id; ?>" class="btn btn-info m-1 create_zoom_meeting_link_2 py-1 px-2 small" style="background-color:#499619;color: #fff" target='_self'>Start Class</a>
                                                    <?php } 
                                                      else if ($curtime == $lsntime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'BBB') { ?>
                                                         <a  href="javascript:void(0);" data-group_id ="<?php echo $group->group_id ; ?>" data-tutor_uri ="<?php echo $group->InstructorURI ; ?>" data-lesson_id ="<?php echo $group->id ; ?>" data-meeting_id ="<?php echo $group->meeting_id ; ?>"  class='btn btn-info m-1 start_bbb_group py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>                         
                                                    <?php } 

                                                    else if ($curtime > $lsntime && $curtime < $lsnendtime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'Zoom') { ?>
                                                        <a href="javascript:void(0);" id="lec_lnk" data-group_id="<?php echo $group->group_id; ?>" data-meeting_id="<?php echo $group->meeting_id; ?>" data-user_type="Tutor" data-user_name="<?php echo $group->instructor->first_name . ' ' . $group->instructor->last_name; ?>" data-user_id="<?php echo $group->instructor_id; ?>" class="btn btn-info m-1 create_zoom_meeting_link_2 py-1 px-2 small" style="background-color:#499619;color: #fff" target='_self'>Start Class</a>
                                                    <?php } 
                                                     else if ($curtime > $lsntime && $curtime < $lsnendtime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'BBB') { ?>
                                                         <a  href="javascript:void(0);" data-group_id ="<?php echo $group->group_id ; ?>" data-tutor_uri ="<?php echo $group->InstructorURI ; ?>" data-lesson_id ="<?php echo $group->id ; ?>" data-meeting_id ="<?php echo $group->meeting_id ; ?>"  class='btn btn-info m-1 start_bbb_group py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>                         
                                                    <?php } 

                                                    else if ($curtime > $lsntime && $curtime < $lsnendtime && $group->class->learning_tool == 'Zoom') {
                                                        if ($group->status == "Completed" && $group->is_moderator_left == '1') {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } else if ($group->status == "Missed" && $group->is_moderator_left == '1') {
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                        } else { ?>
                                                            <a href="javascript:void(0);" id="lec_lnk" data-group_id="<?php echo $group->group_id; ?>" data-meeting_id="<?php echo $group->meeting_id; ?>" data-user_type="Tutor" data-user_name="<?php echo $group->instructor->first_name . ' ' . $group->instructor->last_name; ?>" data-user_id="<?php echo $group->instructor_id; ?>" class="btn btn-info m-1 create_zoom_meeting_link_2 py-1 px-2 small" style="background-color:#499619;color: #fff" target='_self'>Start Class</a>
                                                        <?php }
                                                    } 
                                                    else if ($curtime > $lsntime && $curtime < $lsnendtime && $group->class->learning_tool == 'BBB') {
                                                        if ($group->status == "Completed" && $group->InstructorURI != null) {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } else if ($group->status == "Missed" && $group->InstructorURI != null) {
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                        } else { ?>
                                                            <a  href="javascript:void(0);" data-group_id ="<?php echo $group->group_id ; ?>" data-tutor_uri ="<?php echo $group->InstructorURI ; ?>" data-lesson_id ="<?php echo $group->id ; ?>" data-meeting_id ="<?php echo $group->meeting_id ; ?>"  class='btn btn-info m-1 start_bbb_group py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>                         
                                                        <?php }
                                                    } 

                                                    else if ($curtime == $lsnendtime && $group->class->learning_tool == 'Zoom') {
                                                        if (($group->status != "Completed" && $group->status != "Missed") && $group->class->learning_tool == 'Zoom' && ($group->is_instructor_joined == '1' || $group->is_instructor_joined == "1" || $group->is_instructor_joined == 1 || $group->is_instructor_joined)) {
                                                            updateLessonStatusCompleteZoom($group->id, $group->meeting_id, $group->group_id);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } else if (($group->status != "Completed" && $group->status != "Missed") && $group->class->learning_tool == 'Zoom' && $group->is_instructor_joined == '0') {
                                                            updateLessonStatusMissedZoom($group->id, $group->meeting_id, $group->group_id);
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                        } else {
                                                            if ($group->status == "Completed") {
                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                            } else {
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                        }
                                                    }
                                                    else if ($curtime == $lsnendtime && $group->class->learning_tool == 'BBB') {
                                                        if (($group->status != "Completed" && $group->status != "Missed") && $group->class->learning_tool == 'BBB' && ($group->is_instructor_joined == '1' || $group->is_instructor_joined == "1" || $group->is_instructor_joined == 1 || $group->is_instructor_joined)) {
                                                            updateLessonStatusComplete($group->id, $group->meeting_id, $group->group_id);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } else if (($group->status != "Completed" && $group->status != "Missed") && $group->class->learning_tool == 'BBB' && $group->is_instructor_joined == '0') {
                                                            updateLessonStatusMissed($group->id, $group->meeting_id, $group->group_id);
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                        } else {
                                                            if ($group->status == "Completed") {
                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                            } else {
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                        }
                                                    }

                                                    else if ($curtime > $lsnendtime) {
                                                        if ($group->class->learning_tool == 'Zoom' && $group->is_instructor_joined == '1' || $group->is_instructor_joined == "1" || $group->is_instructor_joined == 1 || $group->is_instructor_joined) {
                                                            updateLessonStatusCompleteZoom($group->id, $group->meeting_id, $group->group_id);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } 
                                                        else if ($group->class->learning_tool == 'BBB' && $group->is_instructor_joined == '1' || $group->is_instructor_joined == "1" || $group->is_instructor_joined == 1 || $group->is_instructor_joined) {
                                                            updateLessonStatusComplete($group->id, $group->meeting_id, $group->group_id);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } 
                                                        else if ($group->status == "Completed") {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        } 
                                                        else {
                                                            if ($group->status == "Missed") {
                                                                if (!empty($group->meeting_id)) {
                                                                    $res = check_missed_status($group->meeting_id);
                                                                    if ($res == 'Completed') {
                                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                                    } else {
                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                                    }
                                                                } else {
                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                                }
                                                            } 
                                                            else if ($group->status == "Pending" && $group->class->learning_tool == "Zoom" && $group->is_instructor_joined == '0') {
                                                                updateLessonStatusMissedZoom($group->id, $group->meeting_id, $group->group_id);
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            } 
                                                            else if ($group->status == "Pending" && $group->class->learning_tool == "BBB" && $group->is_instructor_joined == '0') {
                                                                updateLessonStatusMissed($group->id, $group->meeting_id, $group->group_id);
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            } 
                                                            else {
                                                                echo "<span class='lesson-status py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                        }
                                                    }
                                                    
                                                } else if ($curdate > $lsndate) {
                                                    if ($group->class->learning_tool == 'Zoom' && $group->is_instructor_joined == '1' || $group->is_instructor_joined == "1" || $group->is_instructor_joined == 1 || $group->is_instructor_joined) {
                                                        updateLessonStatusCompleteZoom($group->id, $group->meeting_id, $group->group_id);
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    }
                                                    else if ($group->class->learning_tool == 'BBB' && $group->is_instructor_joined == '1' || $group->is_instructor_joined == "1" || $group->is_instructor_joined == 1 || $group->is_instructor_joined) {
                                                        updateLessonStatusComplete($group->id, $group->meeting_id, $group->group_id);
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    }
                                                     else if ($group->status == "Completed") {
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } else if ($group->status == "Pending") {
                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                    } else {
                                                        if ($group->status == "Missed") {
                                                            if (!empty($group->meeting_id)) {
                                                                $res = check_missed_status($group->meeting_id);
                                                                if ($res == 'Completed') {
                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                                } else {
                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                                }
                                                            } else {
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                        } else {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        }
                                                    }
                                                } else {
                                                    if ($group->status == "Completed") {
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } else {
                                                        if ($group->status == "Missed") {
                                                            if (!empty($group->meeting_id)) {
                                                                $res = check_missed_status($group->meeting_id);
                                                                if ($res == 'Completed') {
                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                                } else {
                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                                }
                                                            } else {
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                            }
                                                        } else {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                        }
                                                    }
                                                } ?>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div> 
                        <!-- Pagination Start -->
                        @if (@$getGroupClasses->hasPages())
                            {{ @$getGroupClasses->links('frontend.paginate.paginate') }}
                        @endif
                        <!-- Pagination End -->
                    @else
                        <!-- If there is no data Show Empty Design Start -->
                        <div class="empty-data">
                            <img src="{{ asset('frontend/assets/img/empty-data-img.png') }}" alt="img" class="img-fluid">
                            <h5 class="my-3">{{ __('No Group Classes Available') }}</h5>
                        </div>
                        <!-- If there is no data Show Empty Design End -->
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="showstudentnames" tabindex="-1" role="dialog" aria-labelledby="showstudentnamesTitle-2" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="showstudentnamesTitle-2">Group Class Students</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div id="student_names_box" class="modal-body"></div>
            </div>
        </div>
    </div>
@endsection
<script src="{{ asset('frontend/assets/vendor/jquery/jquery-3.6.0.min.js') }}"></script>
<script>
    $(document).ready(function () {
        ajaxCallfirst(); // To output when the page loads
        setInterval(ajaxCallfirst, (50 * 1000)); // x * 1000 to get it in seconds
    });

    function ajaxCallfirst() {
        var base_url = window.location.origin;
        setTimeout(() => {
            $.ajax({
                type: "POST",
                url: base_url + "/instructor/live-class/live-class/checkLessonStartTime",
                data: { "_token": "{{ csrf_token() }}" },
                dataType: "JSON",
                success: (function (result) {
                    console.log("STARTED");
                    console.log(result);
                    console.log(result.l_id);
                    console.log(result.is_group);
                    console.log(result.learning_tool);
                    console.log(result.group_id);
                    console.log(result.meeting_id);
                    console.log(result.user_name);
                    console.log(result.user_type);
                    $('#lesson_id').val(result.l_id);
                    if (result.is_group == 'Yes') {
                        var group_id = result.group_id;
                        if (result.learning_tool == 'Zoom') {
                            $('#lec_link2_' + group_id).attr('href', 'javascript:void(0);');
                            $('#lec_link2_' + group_id).attr('target', '_self');
                            $('#lec_link2_' + group_id).html("Start Class");
                            $('#lec_link2_' + group_id).css("background-color", "#499619");
                            $('#lec_link2_' + group_id).css("padding", "5px 10px");
                            $('#lec_link2_' + group_id).css("color", "#fff");
                            $('#lec_link2_' + group_id).attr('data-meeting_id', result.meeting_id);
                            $('#lec_link2_' + group_id).attr('data-group_id', result.group_id);
                            $('#lec_link2_' + group_id).attr('data-user_name', result.user_name);
                            $('#lec_link2_' + group_id).attr('data-user_id', result.user_id);
                            $('#lec_link2_' + group_id).attr('data-user_type', result.user_type);
                            $('#lec_link2_' + group_id).addClass('create_zoom_meeting_link_2');
                        }
                        else if(result.learning_tool == 'BBB'){
                            $('#lec_link2_'+group_id).attr('href', 'javascript:void(0);'); 
                            $('#lec_link2_'+group_id).attr('target', '_blank'); 
                            $('#lec_link2_'+group_id).html("Start Class"); 
                            $('#lec_link2_'+group_id).css("background-color", "#499619"); 
                            $('#lec_link2_'+group_id).css("padding", "5px 10px"); 
                            $('#lec_link2_'+group_id).css("color", "#fff"); 
                            $('#lec_link2_'+group_id).attr('data-group_id', result.group_id); 
                            $('#lec_link2_'+group_id).attr('data-lesson_id', result.l_id); 
                            $('#lec_link2_'+group_id).attr('data-meeting_id', result.meeting_id); 
                            $('#lec_link2_'+group_id).attr('data-tutor_uri', result.url);
                            $('#lec_link2_'+group_id).addClass('start_bbb_group'); 
                        }
                        else if(result.learning_tool == 'Google_Meet'){
                            $('#lec_link2_'+group_id).attr('href', 'javascript:void(0);'); 
                            $('#lec_link2_'+group_id).attr('target', '_blank'); 
                            $('#lec_link2_'+group_id).html("Start Class"); 
                            $('#lec_link2_'+group_id).css("background-color", "#499619"); 
                            $('#lec_link2_'+group_id).css("padding", "5px 10px"); 
                            $('#lec_link2_'+group_id).css("color", "#fff"); 
                            $('#lec_link2_'+group_id).attr('data-group_id', result.group_id); 
                            $('#lec_link2_'+group_id).attr('data-lesson_id', result.l_id); 
                            $('#lec_link2_'+group_id).attr('data-meeting_id', result.meeting_id); 
                            $('#lec_link2_'+group_id).attr('data-tutor_uri', result.url);
                            $('#lec_link2_'+group_id).addClass('start_meet_group'); 
                        }

                        IsmeetingCreated = $('#lec_link2_' + group_id).attr('data-meeting_created_2');
                        if (IsmeetingCreated != 'Yes') {
                            var audioElement = document.createElement('audio');
                            audioElement.setAttribute('src', base_url + '/public/assets/notification_sound/Cool_Notification_2.mp3');
                        }
                        $('#lec_link2_' + group_id).attr('data-meeting_created_2', 'Yes');
                    } else if (result.is_group == 'No') {
                        console.log("ENTERED INSIDE INDIVIDUAL");
                        if (result.learning_tool == 'Zoom') {
                            $('#lec_link1_' + result.l_id).attr('href', 'javascript:void(0);');
                            $('#lec_link1_' + result.l_id).attr('target', '_self');
                            $('#lec_link1_' + result.l_id).html("Start Class");
                            $('#lec_link1_' + result.l_id).css("background-color", "#499619");
                            $('#lec_link1_' + result.l_id).css("color", "#fff");
                            $('#lec_link1_' + result.l_id).attr('data-meeting_id', result.meeting_id);
                            $('#lec_link1_' + result.l_id).attr('data-user_name', result.user_name);
                            $('#lec_link1_' + result.l_id).attr('data-user_id', result.user_id);
                            $('#lec_link1_' + result.l_id).attr('data-user_type', result.user_type);
                            $('#lec_link1_' + result.l_id).addClass('create_zoom_meeting_link');
                        }
                        else if(result.learning_tool == 'BBB'){
                            console.log("ENTERED INSIDE INDIVIDUAL BBB");
                        $('#lec_link1_'+result.l_id).attr('href', 'javascript:void(0);'); 
                        $('#lec_link1_'+result.l_id).attr('target', '_blank'); 
                        $('#lec_link1_'+result.l_id).html("Start Class"); 
                        $('#lec_link1_'+result.l_id).css("background-color", "#499619"); 
                        $('#lec_link1_'+result.l_id).css("color", "#fff"); 
                        $('#lec_link1_'+result.l_id).attr('data-lesson_id', result.l_id); 
                        $('#lec_link1_'+result.l_id).attr('data-meeting_id', result.meeting_id); 
                        $('#lec_link1_'+result.l_id).attr('data-tutor_uri', result.url);
                        $('#lec_link1_'+result.l_id).addClass('start_bbb_individual'); 
                        }
                        else if(result.learning_tool == 'Google_Meet'){
                            console.log("ENTERED INSIDE INDIVIDUAL BBB");
                        $('#lec_link1_'+result.l_id).attr('href', 'javascript:void(0);'); 
                        $('#lec_link1_'+result.l_id).attr('target', '_blank'); 
                        $('#lec_link1_'+result.l_id).html("Start Class"); 
                        $('#lec_link1_'+result.l_id).css("background-color", "#499619"); 
                        $('#lec_link1_'+result.l_id).css("color", "#fff"); 
                        $('#lec_link1_'+result.l_id).attr('data-lesson_id', result.l_id); 
                        $('#lec_link1_'+result.l_id).attr('data-meeting_id', result.meeting_id); 
                        $('#lec_link1_'+result.l_id).attr('data-tutor_uri', result.url);
                        $('#lec_link1_'+result.l_id).addClass('start_meet_individual'); 
                        }
                        IsmeetingCreated = $('#lec_link1_' + result.l_id).attr('data-meeting_created');
                        if (IsmeetingCreated != 'Yes') {
                            var audioElement = document.createElement('audio');
                            // audioElement.setAttribute('src', base_url + '/public/assets/notification_sound/Cool_Notification_2.mp3');
                        }
                        $('#lec_link1_' + result.l_id).attr('data-meeting_created', 'Yes');
                    }
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
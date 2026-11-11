@extends('frontend.layouts.app')


@section('navbar-header')
{{__('Class Schedule')}}
@endsection

@section('content2')
<div>
    <section class="student-profile-page">
        <div>
            <div class="student-profile-page-content">
                <div class="row">
                    <div class="col-12">
                        <div class="row bg-white">
                            <div class="col-lg-12 p-0">
                                <div class="student-profile-right-part">
                                    <input type="hidden" name="base_url" id="base_url" value="{{$base_url}}">
                                    <div class="row">
                                        <div class="col-12">
                                            @if(!$getAllClasses->isEmpty())
                                                {{-- Classes for desktop view --}}
                                                <div class="table-responsive table-responsive-xl courses-table">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">{{ __('No') }}</th>
                                                                <th scope="col">{{ __('Instructor') }}</th>
                                                                <th scope="col">{{ __('Course') }}</th>
                                                                <th scope="col">{{ __('Date Time') }}</th>
                                                                <th scope="col">{{ __('Status') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php $i = 0; @endphp
                                                            @foreach($getAllClasses as $individual)
                                                                <tr>
                                                                    <td>{{ ++$i }}</td>
                                                                    <td>
                                                                        <div class="table-data-with-img d-flex align-items-center">
                                                                            <div class="flex-grow-1 table-data-course-name">{{ Str::limit($individual->instructor->first_name.' '.$individual->instructor->last_name, 50) }}</div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="table-data-with-img d-flex align-items-center">
                                                                            <div class="flex-grow-1 table-data-course-name">{{ Str::limit($individual->course->title, 50) }}</div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        @php
                                                                            $updated_time = changetimeByZone($individual->class_time,$individual->student_id,'student');
                                                                            $lesson_time = $updated_time; 
                                                                            $curdate = strtotime(date("Y-m-d"));
                                                                            $lsndate = strtotime($individual->class_date); 
                                                                            $updated_cur_time = changetimeByZone(date("H:i"),$individual->student_id,'student');
                                                                            $curtime = strtotime($updated_cur_time);
                                                                            $lsntime = strtotime($lesson_time);
                                                                            $lesson_duration = $individual->class->duration;
                                                                            $lsntimestamp = strtotime($lesson_time) + $lesson_duration*60;
                                                                            $lsnendtime = $lsntimestamp;
                                                                        @endphp
                                                                        {{ date('d-m-Y',strtotime($individual->class_date)).' '.$updated_time }}
                                                                    </td>
                                                                    <td>
                                                                        @php
                                                                            $updated_time = changetimeByZone($individual->class_time,$individual->student_id,'student');
                                                                            $lesson_time = $updated_time; 
                                                                            $curdate = strtotime(date("Y-m-d"));
                                                                            $lsndate = strtotime($individual->class_date); 
                                                                            $updated_cur_time = changetimeByZone(date("H:i"),$individual->student_id,'student');
                                                                            $curtime = strtotime($updated_cur_time);
                                                                            $lsntime = strtotime($lesson_time);
                                                                            $lesson_duration = $individual->class->duration;
                                                                            $lsntimestamp = strtotime($lesson_time) + $lesson_duration*60;
                                                                            $lsnendtime = $lsntimestamp;
                                                                        @endphp
                                                                        <?php
                                                                        if($curdate <  $lsndate) { ?>
                                                                            <span style="border:none;font-weight:bolder;background-color:#322740;color:white;" class="btn btn-warning m-1 py-1 px-2 small">Scheduled</span>
                                                                        <?php } 
                                                                        else if($curdate == $lsndate) {
                                                                          
                                                                            if($curtime < $lsntime) {
                                                                             
                                                                                $id = 'lec_link1_'.$individual->id;
                                                                                echo "<a class='btn btn-warning m-1 lec_link1 py-1 px-2 small' id='$id'>Waiting</a>";
                                                                            } 
                                                                            else if($curtime >= $lsntime && $curtime < $lsnendtime && $individual->meeting_id == NULL && $individual->class->learning_tool == 'Zoom') {
                                                                                $id = 'lec_link1_'.$individual->id;
                                                                                echo "<a class='btn btn-warning m-1 lec_link1 py-1 px-2 small' id='$id'>Waiting</a>";
                                                                            }
                                                                            else if($curtime >= $lsntime && $curtime < $lsnendtime && $individual->StudentURI == NULL && $individual->class->learning_tool == 'BBB') {
                                                                                $id = 'lec_link1_'.$individual->id;
                                                                                echo "<a class='btn btn-warning m-1 lec_link1 py-1 px-2 small' id='$id'>Waiting</a>";
                                                                            }
                                                                            else if($curtime >= $lsntime && $curtime < $lsnendtime && $individual->google_meet_link == NULL  && $individual->class->learning_tool == 'Google_Meet') {
                                                                                $id = 'lec_link1_'.$individual->id;
                                                                                echo "<a class='btn btn-warning m-1 lec_link1 py-1 px-2 small' id='$id'>Waiting</a>";
                                                                            }

                                                                            else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Zoom') { ?>
                                                                                <a href="javascript:void(0);" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>" data-user_type ="<?php echo "Student" ; ?>" data-user_name ="<?php echo $individual->student->first_name.' '.$individual->student->last_name ; ?>" data-user_id ="<?php echo $individual->student_id ; ?>" class="btn btn-info m-1 create_zoom_meeting_link py-1 px-2 small" style="background-color:#499619;color: #fff" target='_self'>Start Class</a>
                                                                            <?php }
                                                                             else if($curtime == $lsntime && ($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'Zoom') {
                                                                                if($individual->status == "Completed" && $individual->is_moderator_left == '1') {
                                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                } else if($individual->status == "Missed" && $individual->is_moderator_left == '1') {
                                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>"; 
                                                                                }
                                                                            } 
                                                                            else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'BBB') { 
                                                                                $url = $individual->StudentURI;
                                                                                ?>
                                                                               	<a href="<?php echo $url; ?>"  class='btn btn-info m-1 py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                                               
                                                                            <?php }
                                                                             else if($curtime == $lsntime && ($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'BBB') {
                                                                                if($individual->status == "Completed" && $individual->StudentURI != null) {
                                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                } else if($individual->status == "Missed" && $individual->StudentURI != null) {
                                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>"; 
                                                                                }
                                                                            } 
                                                                            else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Google_Meet') { 
                                                                                $url = $individual->google_meet_link;
                                                                                ?>
                                                                               	<a href="<?php echo $url; ?>"  class='btn btn-info m-1 py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                                                  
                                                                            <?php }
                                                                             else if($curtime == $lsntime && ($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'Google_Meet') {
                                                                                if($individual->status == "Completed" && $individual->meeting_id != null) {
                                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                } else if($individual->status == "Missed" && $individual->meeting_id != null) {
                                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>"; 
                                                                                }
                                                                            }
                                                                              
                                                                            
                                                                         
                                                                            else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Zoom') { ?>   
                                                                                <a href="javascript:void(0);" id="lec_link" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>" data-user_type ="<?php echo "Student" ; ?>" data-user_name ="<?php echo $individual->student->first_name.' '.$individual->student->last_name ; ?>" data-user_id ="<?php echo $individual->student_id ; ?>" class="btn btn-info m-1 create_zoom_meeting_link py-1 px-2 small" style="background-color:#499619;color: #fff" target='_self'>Start Class</a>
                                                                            <?php 
                                                                            } 
                                                                            else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->class->learning_tool == 'Zoom') {
                                                                                if($individual->status == "Completed" && $individual->is_moderator_left == '1') {
                                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                } else if($individual->status == "Missed" && $individual->is_moderator_left == '1') {
                                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>"; 
                                                                                } else { ?>   
                                                                                    <a href="javascript:void(0);" id="lec_link" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>" data-user_type ="<?php echo "Student" ; ?>" data-user_name ="<?php echo $individual->student->first_name.' '.$individual->student->last_name ; ?>" data-user_id ="<?php echo $individual->student_id ; ?>" class="btn btn-info m-1 create_zoom_meeting_link py-1 px-2 small" style="background-color:#499619;color: #fff" target='_self'>Start Class</a>
                                                                                <?php }
                                                                            } 

                                                                            else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'BBB') { ?>   
                                                                                <?php
                                                                                $url = $individual->StudentURI;
                                                                                ?>
                                                                               	<a href="<?php echo $url; ?>"  class='btn btn-info m-1 py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                                            
                                                                            <?php 
                                                                            } 
                                                                            else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->class->learning_tool == 'BBB') {
                                                                                if($individual->status == "Completed" && $individual->StudentURI != null) {
                                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                } else if($individual->status == "Missed" && $individual->StudentURI != null) {
                                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>"; 
                                                                                } else { ?>   
                                                                             
                                                                                <a href="javascript:void(0);"  data-student_uri ="<?php echo $individual->StudentURI ; ?>" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>"  class='btn btn-info m-1 py-1 px-2 start_bbb_individual' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>  
                                                                                <?php }
                                                                            }
                                                                            else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Google_Meet') { ?>   
                                                                                <?php
                                                                                $url = $individual->google_meet_link;
                                                                                ?>
                                                                               	<a href="<?php echo $url; ?>"  class='btn btn-info m-1 py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                                            <?php 
                                                                            } 
                                                                            else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->class->learning_tool == 'Google_Meet') {
                                                                                if($individual->status == "Completed" && $individual->google_meet_link != null) {
                                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                } else if($individual->status == "Missed" && $individual->google_meet_link != null) {
                                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>"; 
                                                                                } else { ?>   
                                                                                <a href="javascript:void(0);"  data-student_uri ="<?php echo $individual->google_meet_link ; ?>" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>"  class='btn btn-info m-1 py-1 px-2 start_meet_individual' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>  
                                                                                <?php }
                                                                            }

                                                                            
                                                                            else if($curtime == $lsnendtime && $individual->class->learning_tool == 'Zoom') {
                                                                                if(($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'Zoom' && ($individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined)) {
                                                                                    updateLessonStatusCompleteZoom($individual->id,$individual->meeting_id,null);
                                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                } else if(($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'Zoom' && $individual->is_instructor_joined == '0') {
                                                                                    updateLessonStatusMissedZoom($individual->id,$individual->meeting_id,null);
                                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";  
                                                                                } else {
                                                                                    if($individual->status == "Completed") {
                                                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                    } else {
                                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";     
                                                                                    }
                                                                                }
                                                                            } 
                                                                            else if($curtime == $lsnendtime && $individual->class->learning_tool == 'BBB') {
                                                                                if(($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'BBB' && ($individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined)) {
                                                                                    updateLessonStatusComplete($individual->id,$individual->meeting_id,null);
                                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                } else if(($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'BBB' && $individual->is_instructor_joined == '0') {
                                                                                    updateLessonStatusMissed($individual->id,$individual->meeting_id,null);
                                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";  
                                                                                } else {
                                                                                    if($individual->status == "Completed") {
                                                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                    } else {
                                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";     
                                                                                    }
                                                                                }
                                                                            } 
                                                                            else if($curtime == $lsnendtime && $individual->class->learning_tool == 'Google_Meet') {
                                                                                if(($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'Google_Meet' && ($individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined)) {
                                                                                    updateLessonStatusComplete($individual->id,$individual->meeting_id,null);
                                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                } else if(($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'Google_Meet' && $individual->is_instructor_joined == '0') {
                                                                                    updateLessonStatusMissed($individual->id,$individual->meeting_id,null);
                                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";  
                                                                                } else {
                                                                                    if($individual->status == "Completed") {
                                                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                    } else {
                                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";     
                                                                                    }
                                                                                }
                                                                            } 


                                                                            else if($curtime > $lsnendtime) {
                                                                                if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Zoom' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {	
                                                                                    updateLessonStatusCompleteZoom($individual->id,$individual->meeting_id,null);
                                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                } 
                                                                                else if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'BBB' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {	
                                                                                    updateLessonStatusComplete($individual->id,$individual->meeting_id,null);
                                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                } 
                                                                                else if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Google_Meet' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {	
                                                                                    updateLessonStatusComplete($individual->id,$individual->meeting_id,null);
                                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                } 
                                                                                else if($individual->status == "Completed") {
                                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                } else {
                                                                                    if($individual->status == "Missed") {
                                                                                        if(!empty($individual->meeting_id)) {
                                                                                            $res = check_missed_status($individual->meeting_id);
                                                                                            if($res == 'Completed') {
                                                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                            } else {
                                                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";    
                                                                                            }  
                                                                                        } else {
                                                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";    
                                                                                        }
                                                                                    } 
                                                                                    else if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == "Zoom" && $individual->is_instructor_joined == '0') {
                                                                                        updateLessonStatusMissedZoom($individual->id,$individual->meeting_id,null);
                                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";  
                                                                                    }
                                                                                    else if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == "BBB" && $individual->is_instructor_joined == '0') {
                                                                                        updateLessonStatusMissed($individual->id,$individual->meeting_id,null);
                                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";  
                                                                                    }
                                                                                    else if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == "Google_Meet" && $individual->is_instructor_joined == '0') {
                                                                                        updateLessonStatusMissed($individual->id,$individual->meeting_id,null);
                                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";  
                                                                                    }
                                                                                     else {
                                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";    
                                                                                    }
                                                                                }
                                                                            }
                                                                        } 
                                                                        else if($curdate > $lsndate) {
                                                                            if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Zoom' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {	
                                                                                updateLessonStatusCompleteZoom($individual->id,$individual->meeting_id,null);
                                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                            } 
                                                                            else if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'BBB' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {	
                                                                                updateLessonStatusComplete($individual->id,$individual->meeting_id,null);
                                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                            } 
                                                                            else if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Google_Meet' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {	
                                                                                updateLessonStatusComplete($individual->id,$individual->meeting_id,null);
                                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                            } 
                                                                            else if($individual->status == "Completed") {
                                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                            } else if(($individual->status == "Pending" || $individual->status == "Scheduled")) {
                                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";    
                                                                            } else {
                                                                                if($individual->status == "Missed") {
                                                                                    if(!empty($individual->meeting_id)) {
                                                                                        $res = check_missed_status($individual->meeting_id);
                                                                                        if($res == 'Completed') {
                                                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                        } else {
                                                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";    
                                                                                        }  
                                                                                    } else {
                                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";    
                                                                                    }
                                                                                }
                                                                            }
                                                                        } 
                                                                        else {
                                                                            if($individual->status == "Completed") {  
                                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                            } else {
                                                                                if($individual->status == "Missed") {
                                                                                    if(!empty($individual->meeting_id)) {
                                                                                        $res = check_missed_status($individual->meeting_id);
                                                                                        if($res == 'Completed') {
                                                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                        } else {
                                                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";    
                                                                                        }
                                                                                    } else {
                                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";    
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
                                                    @foreach($getAllClasses as $individual)
                                                        <div class="course-card">
                                                            <div class="font-15 color-heading">
                                                                <strong>Instructor Name:</strong>
                                                                <div class="table-data-with-img d-flex align-items-center">
                                                                    <div class="flex-grow-1 table-data-course-name">{{ Str::limit($individual->instructor->first_name.' '.$individual->instructor->last_name, 50) }}</div>
                                                                </div>
                                                            </div>
                                                            <div class="font-15 color-heading">
                                                                <strong>Course:</strong>
                                                                <div class="table-data-with-img d-flex align-items-center">
                                                                    <div class="flex-grow-1 table-data-course-name">{{ Str::limit($individual->course->title, 50) }}</div>
                                                                </div>
                                                            </div>
                                                            <div class="font-15 color-heading">
                                                                <strong>Date Time:</strong>
                                                                @php
                                                                    $updated_time = changetimeByZone($individual->class_time,$individual->student_id,'student');
                                                                    $lesson_time = $updated_time; 
                                                                    $curdate = strtotime(date("Y-m-d"));
                                                                    $lsndate = strtotime($individual->class_date); 
                                                                    $updated_cur_time = changetimeByZone(date("H:i"),$individual->student_id,'student');
                                                                    $curtime = strtotime($updated_cur_time);
                                                                    $lsntime = strtotime($lesson_time);
                                                                    $lesson_duration = $individual->class->duration;
                                                                    $lsntimestamp = strtotime($lesson_time) + $lesson_duration*60;
                                                                    $lsnendtime = $lsntimestamp;
                                                                @endphp
                                                                {{ date('d-m-Y',strtotime($individual->class_date)).' '.$updated_time }}
                                                            </div>
                                                            <div class="font-15 color-heading">
                                                                <strong>Status:</strong>
                                                                        @php
                                                                            $updated_time = changetimeByZone($individual->class_time,$individual->student_id,'student');
                                                                            $lesson_time = $updated_time; 
                                                                            $curdate = strtotime(date("Y-m-d"));
                                                                            $lsndate = strtotime($individual->class_date); 
                                                                            $updated_cur_time = changetimeByZone(date("H:i"),$individual->student_id,'student');
                                                                            $curtime = strtotime($updated_cur_time);
                                                                            $lsntime = strtotime($lesson_time);
                                                                            $lesson_duration = $individual->class->duration;
                                                                            $lsntimestamp = strtotime($lesson_time) + $lesson_duration*60;
                                                                            $lsnendtime = $lsntimestamp;
                                                                        @endphp
                                                                        <?php
                                                                        if($curdate <  $lsndate) { ?>
                                                                            <span style="border:none;font-weight:bolder;background-color:#322740;color:white;" class="btn btn-warning m-1 py-1 px-2 small">Scheduled</span>
                                                                        <?php } 
                                                                        else if($curdate == $lsndate) {
                                                                          
                                                                            if($curtime < $lsntime) {
                                                                             
                                                                                $id = 'lec_link1_'.$individual->id;
                                                                                echo "<a class='btn btn-warning m-1 lec_link1 py-1 px-2 small' id='$id'>Waiting</a>";
                                                                            } 
                                                                            else if($curtime >= $lsntime && $curtime < $lsnendtime && $individual->meeting_id == NULL && $individual->class->learning_tool == 'Zoom') {
                                                                                $id = 'lec_link1_'.$individual->id;
                                                                                echo "<a class='btn btn-warning m-1 lec_link1 py-1 px-2 small' id='$id'>Waiting</a>";
                                                                            }
                                                                            else if($curtime >= $lsntime && $curtime < $lsnendtime && $individual->StudentURI == NULL && $individual->class->learning_tool == 'BBB') {
                                                                                $id = 'lec_link1_'.$individual->id;
                                                                                echo "<a class='btn btn-warning m-1 lec_link1 py-1 px-2 small' id='$id'>Waiting</a>";
                                                                            }
                                                                            else if($curtime >= $lsntime && $curtime < $lsnendtime && $individual->google_meet_link == NULL  && $individual->class->learning_tool == 'Google_Meet') {
                                                                                $id = 'lec_link1_'.$individual->id;
                                                                                echo "<a class='btn btn-warning m-1 lec_link1 py-1 px-2 small' id='$id'>Waiting</a>";
                                                                            }

                                                                            else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Zoom') { ?>
                                                                                <a href="javascript:void(0);" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>" data-user_type ="<?php echo "Student" ; ?>" data-user_name ="<?php echo $individual->student->first_name.' '.$individual->student->last_name ; ?>" data-user_id ="<?php echo $individual->student_id ; ?>" class="btn btn-info m-1 create_zoom_meeting_link py-1 px-2 small" style="background-color:#499619;color: #fff" target='_self'>Start Class</a>
                                                                            <?php }
                                                                             else if($curtime == $lsntime && ($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'Zoom') {
                                                                                if($individual->status == "Completed" && $individual->is_moderator_left == '1') {
                                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                } else if($individual->status == "Missed" && $individual->is_moderator_left == '1') {
                                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>"; 
                                                                                }
                                                                            } 
                                                                            else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'BBB') { 
                                                                                $url = $individual->StudentURI;
                                                                                ?>
                                                                               	<a href="<?php echo $url; ?>"  class='btn btn-info m-1 py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                                               
                                                                            <?php }
                                                                             else if($curtime == $lsntime && ($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'BBB') {
                                                                                if($individual->status == "Completed" && $individual->StudentURI != null) {
                                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                } else if($individual->status == "Missed" && $individual->StudentURI != null) {
                                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>"; 
                                                                                }
                                                                            } 
                                                                            else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Google_Meet') { 
                                                                                $url = $individual->google_meet_link;
                                                                                ?>
                                                                               	<a href="<?php echo $url; ?>"  class='btn btn-info m-1 py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                                                  
                                                                            <?php }
                                                                             else if($curtime == $lsntime && ($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'Google_Meet') {
                                                                                if($individual->status == "Completed" && $individual->google_meet_link != null) {
                                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                } else if($individual->status == "Missed" && $individual->google_meet_link != null) {
                                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>"; 
                                                                                }
                                                                            }
                                                                              
                                                                            
                                                                         
                                                                            else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Zoom') { ?>   
                                                                                <a href="javascript:void(0);" id="lec_link" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>" data-user_type ="<?php echo "Student" ; ?>" data-user_name ="<?php echo $individual->student->first_name.' '.$individual->student->last_name ; ?>" data-user_id ="<?php echo $individual->student_id ; ?>" class="btn btn-info m-1 create_zoom_meeting_link py-1 px-2 small" style="background-color:#499619;color: #fff" target='_self'>Start Class</a>
                                                                            <?php 
                                                                            } 
                                                                            else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->class->learning_tool == 'Zoom') {
                                                                                if($individual->status == "Completed" && $individual->is_moderator_left == '1') {
                                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                } else if($individual->status == "Missed" && $individual->is_moderator_left == '1') {
                                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>"; 
                                                                                } else { ?>   
                                                                                    <a href="javascript:void(0);" id="lec_link" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>" data-user_type ="<?php echo "Student" ; ?>" data-user_name ="<?php echo $individual->student->first_name.' '.$individual->student->last_name ; ?>" data-user_id ="<?php echo $individual->student_id ; ?>" class="btn btn-info m-1 create_zoom_meeting_link py-1 px-2 small" style="background-color:#499619;color: #fff" target='_self'>Start Class</a>
                                                                                <?php }
                                                                            } 

                                                                            else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'BBB') { ?>   
                                                                                <?php
                                                                                $url = $individual->StudentURI;
                                                                                ?>
                                                                               	<a href="<?php echo $url; ?>"  class='btn btn-info m-1 py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                                            
                                                                            <?php 
                                                                            } 
                                                                            else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->class->learning_tool == 'BBB') {
                                                                                if($individual->status == "Completed" && $individual->StudentURI != null) {
                                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                } else if($individual->status == "Missed" && $individual->StudentURI != null) {
                                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>"; 
                                                                                } else { ?>   
                                                                             
                                                                                <a href="javascript:void(0);"  data-student_uri ="<?php echo $individual->StudentURI ; ?>" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>"  class='btn btn-info m-1 py-1 px-2 start_bbb_individual' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>  
                                                                                <?php }
                                                                            }
                                                                            else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Google_Meet') { ?>   
                                                                                <?php
                                                                                $url = $individual->google_meet_link;
                                                                                ?>
                                                                               	<a href="<?php echo $url; ?>"  class='btn btn-info m-1 py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                                            <?php 
                                                                            } 
                                                                            else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->class->learning_tool == 'Google_Meet') {
                                                                                if($individual->status == "Completed" && $individual->google_meet_link != null) {
                                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                } else if($individual->status == "Missed" && $individual->google_meet_link != null) {
                                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>"; 
                                                                                } else { ?>   
                                                                                <a href="javascript:void(0);"  data-student_uri ="<?php echo $individual->google_meet_link ; ?>" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>"  class='btn btn-info m-1 py-1 px-2 start_meet_individual' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>  
                                                                                <?php }
                                                                            }

                                                                            
                                                                            else if($curtime == $lsnendtime && $individual->class->learning_tool == 'Zoom') {
                                                                                if(($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'Zoom' && ($individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined)) {
                                                                                    updateLessonStatusCompleteZoom($individual->id,$individual->meeting_id,null);
                                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                } else if(($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'Zoom' && $individual->is_instructor_joined == '0') {
                                                                                    updateLessonStatusMissedZoom($individual->id,$individual->meeting_id,null);
                                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";  
                                                                                } else {
                                                                                    if($individual->status == "Completed") {
                                                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                    } else {
                                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";     
                                                                                    }
                                                                                }
                                                                            } 
                                                                            else if($curtime == $lsnendtime && $individual->class->learning_tool == 'BBB') {
                                                                                if(($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'BBB' && ($individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined)) {
                                                                                    updateLessonStatusComplete($individual->id,$individual->meeting_id,null);
                                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                } else if(($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'BBB' && $individual->is_instructor_joined == '0') {
                                                                                    updateLessonStatusMissed($individual->id,$individual->meeting_id,null);
                                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";  
                                                                                } else {
                                                                                    if($individual->status == "Completed") {
                                                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                    } else {
                                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";     
                                                                                    }
                                                                                }
                                                                            } 
                                                                            else if($curtime == $lsnendtime && $individual->class->learning_tool == 'Google_Meet') {
                                                                                if(($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'Google_Meet' && ($individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined)) {
                                                                                    updateLessonStatusComplete($individual->id,$individual->meeting_id,null);
                                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                } else if(($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'Google_Meet' && $individual->is_instructor_joined == '0') {
                                                                                    updateLessonStatusMissed($individual->id,$individual->meeting_id,null);
                                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";  
                                                                                } else {
                                                                                    if($individual->status == "Completed") {
                                                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                    } else {
                                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";     
                                                                                    }
                                                                                }
                                                                            } 


                                                                            else if($curtime > $lsnendtime) {
                                                                                if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Zoom' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {	
                                                                                    updateLessonStatusCompleteZoom($individual->id,$individual->meeting_id,null);
                                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                } 
                                                                                else if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'BBB' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {	
                                                                                    updateLessonStatusComplete($individual->id,$individual->meeting_id,null);
                                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                } 
                                                                                else if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Google_Meet' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {	
                                                                                    updateLessonStatusComplete($individual->id,$individual->meeting_id,null);
                                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                } 
                                                                                else if($individual->status == "Completed") {
                                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                } else {
                                                                                    if($individual->status == "Missed") {
                                                                                        if(!empty($individual->meeting_id)) {
                                                                                            $res = check_missed_status($individual->meeting_id);
                                                                                            if($res == 'Completed') {
                                                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                            } else {
                                                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";    
                                                                                            }  
                                                                                        } else {
                                                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";    
                                                                                        }
                                                                                    } 
                                                                                    else if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == "Zoom" && $individual->is_instructor_joined == '0') {
                                                                                        updateLessonStatusMissedZoom($individual->id,$individual->meeting_id,null);
                                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";  
                                                                                    }
                                                                                    else if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == "BBB" && $individual->is_instructor_joined == '0') {
                                                                                        updateLessonStatusMissed($individual->id,$individual->meeting_id,null);
                                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";  
                                                                                    }
                                                                                    else if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == "Google_Meet" && $individual->is_instructor_joined == '0') {
                                                                                        updateLessonStatusMissed($individual->id,$individual->meeting_id,null);
                                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";  
                                                                                    }
                                                                                     else {
                                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";    
                                                                                    }
                                                                                }
                                                                            }
                                                                        } 
                                                                        else if($curdate > $lsndate) {
                                                                            if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Zoom' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {	
                                                                                updateLessonStatusCompleteZoom($individual->id,$individual->meeting_id,null);
                                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                            } 
                                                                            else if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'BBB' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {	
                                                                                updateLessonStatusComplete($individual->id,$individual->meeting_id,null);
                                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                            } 
                                                                            else if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Google_Meet' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {	
                                                                                updateLessonStatusComplete($individual->id,$individual->meeting_id,null);
                                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                            } 
                                                                            else if($individual->status == "Completed") {
                                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                            } else if(($individual->status == "Pending" || $individual->status == "Scheduled")) {
                                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";    
                                                                            } else {
                                                                                if($individual->status == "Missed") {
                                                                                    if(!empty($individual->meeting_id)) {
                                                                                        $res = check_missed_status($individual->meeting_id);
                                                                                        if($res == 'Completed') {
                                                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                        } else {
                                                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";    
                                                                                        }  
                                                                                    } else {
                                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";    
                                                                                    }
                                                                                }
                                                                            }
                                                                        } 
                                                                        else {
                                                                            if($individual->status == "Completed") {  
                                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                            } else {
                                                                                if($individual->status == "Missed") {
                                                                                    if(!empty($individual->meeting_id)) {
                                                                                        $res = check_missed_status($individual->meeting_id);
                                                                                        if($res == 'Completed') {
                                                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                                        } else {
                                                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";    
                                                                                        }
                                                                                    } else {
                                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";    
                                                                                    }
                                                                                }
                                                                            }
                                                                        } ?>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div> 
                                                @if(@$getAllClasses->hasPages())
                                                    {{ @$getAllClasses->links('frontend.paginate.paginate') }}
                                                @endif
                                            @else
                                                <div class="empty-data">
                                                    <img src="{{ asset('frontend/assets/img/empty-data-img.png') }}" alt="img" class="img-fluid">
                                                    <h5 class="my-3">{{ __('No Classes Available') }}</h5>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

<script src="{{ asset('frontend/assets/vendor/jquery/jquery-3.6.0.min.js') }}"></script>
<script>
var base_url = $('#base_url').val();
console.log("Base URL", base_url);

$(document).on("click", ".create_zoom_meeting_link", function(event){
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
    var murl = '/'+'student/NewZoomMeeting/'+meetingId+'/'+userType+'/'+res+'/'+userId+'/'+lessonId;
    var currentHref = murl;
    window.open(currentHref, '_self');
    // window.location.href = currentHref;
 
});


$(document).on("click", ".start_bbb_individual", function(event){
 event.preventDefault();
 meetingId = $(this).attr("data-meeting_id");
 lessonId = $(this).attr("data-lesson_id");
 studentURI = $(this).attr("data-student_uri");
 var murl = studentURI;
 var currentHref = murl;
 setTimeout(() => {
    window.open(currentHref, '_blank');
 }, 2000);
});


$(document).on("click", ".start_meet_individual", function(event){
 event.preventDefault();
 meetingId = $(this).attr("data-meeting_id");
 lessonId = $(this).attr("data-lesson_id");
 studentURI = $(this).attr("data-student_uri");
 var murl = studentURI;
 var currentHref = murl;
 setTimeout(() => {
    window.open(currentHref, '_blank');
 }, 2000);
});



$(document).ready(function () {
    ajaxCallfirst1(); 
    setInterval(ajaxCallfirst1, (50 * 1000)); 
});
function ajaxCallfirst1() {
    var base_url = window.location.origin;
    $.ajax({
        type: "POST",
        url: base_url+"/student/checkLessonStartTime",
        data: {"_token": "{{ csrf_token() }}"},
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
                    console.log("ENTERED INSIDE INDIVIDUAL");
            $('#lesson_id').val(result.l_id);                       
            if(result.learning_tool == 'Zoom'){
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
                $('#lec_link1_'+result.l_id).addClass('create_zoom_meeting_link');
            }
            else if(result.learning_tool == 'BBB'){
                console.log("ENTERED INSIDE INDIVIDUAL BBB");
                    
                        $('#lec_link1_'+result.l_id).attr('href', result.url); 
                        $('#lec_link1_'+result.l_id).attr('target', '_self'); 
                        $('#lec_link1_'+result.l_id).html("Start Class"); 
                        $('#lec_link1_'+result.l_id).css("padding", "5px 10px"); 
                        $('#lec_link1_'+result.l_id).css("background-color", "#499619"); 
                        $('#lec_link1_'+result.l_id).css("color", "#fff"); 

                        // $('#lec_link1_'+result.l_id).attr('href', 'javascript:void(0);'); 
                        // $('#lec_link1_'+result.l_id).attr('target', '_blank'); 
                        // $('#lec_link1_'+result.l_id).html("Start Class"); 
                        // $('#lec_link1_'+result.l_id).css("background-color", "#499619"); 
                        // $('#lec_link1_'+result.l_id).css("color", "#fff"); 
                        // $('#lec_link1_'+result.l_id).attr('data-lesson_id', result.l_id); 
                        // $('#lec_link1_'+result.l_id).attr('data-meeting_id', result.meeting_id); 
                        // $('#lec_link1_'+result.l_id).attr('data-student_uri', result.url);
                        // $('#lec_link1_'+result.l_id).addClass('start_bbb_individual'); 
                        // $('#lec_link1_'+result.l_id).addClass('start_meet_individual'); 

            }
            else if(result.learning_tool == 'Google_Meet'){
                console.log("ENTERED INSIDE INDIVIDUAL Google_Meet");
                    
                        $('#lec_link1_'+result.l_id).attr('href', result.url); 
                        $('#lec_link1_'+result.l_id).attr('target', '_self'); 
                        $('#lec_link1_'+result.l_id).html("Start Class"); 
                        $('#lec_link1_'+result.l_id).css("padding", "5px 10px"); 
                        $('#lec_link1_'+result.l_id).css("background-color", "#499619"); 
                        $('#lec_link1_'+result.l_id).css("color", "#fff"); 

                        // $('#lec_link1_'+result.l_id).attr('href', 'javascript:void(0);'); 
                        // $('#lec_link1_'+result.l_id).attr('target', '_blank'); 
                        // $('#lec_link1_'+result.l_id).html("Start Class"); 
                        // $('#lec_link1_'+result.l_id).css("background-color", "#499619"); 
                        // $('#lec_link1_'+result.l_id).css("color", "#fff"); 
                        // $('#lec_link1_'+result.l_id).attr('data-lesson_id', result.l_id); 
                        // $('#lec_link1_'+result.l_id).attr('data-meeting_id', result.meeting_id); 
                        // $('#lec_link1_'+result.l_id).attr('data-student_uri', result.url);
                        // $('#lec_link1_'+result.l_id).addClass('start_meet_individual'); 

            }
            IsmeetingCreated = $('#lec_link1_'+result.l_id).attr('data-meeting_created'); 
            if(IsmeetingCreated != 'Yes'){
                var audioElement = document.createElement('audio');
                // audioElement.setAttribute('src', base_url+'/public/assets/notification_sound/Cool_Notification_2.mp3');
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
@push('style')
<link rel="stylesheet" href="{{asset('admin/css/custom/image-preview.css')}}">
@endpush
@push('script')
<script src="{{asset('admin/js/custom/image-preview.js')}}"></script>
<script src="{{asset('frontend/assets/js/custom/student-profile.js')}}"></script>
@endpush

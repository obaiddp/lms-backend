@extends('frontend.layouts.app')

@section('navbar-header')
{{__('Class History')}}
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
                                <h6>{{ __('Individual Class History') }}</h6>
                                <hr>
                                <input type="hidden" name="base_url" id="base_url" value="{{$base_url}}">
                                {{-- <input type="hidden" id="base_url" value="{{url('/')}}"> --}}
                                <div class="row">
                                    <div class="col-12">
                                        @if(!$getAllClassesHistory->isEmpty())
                                            <div class="table-responsive table-responsive-xl courses-table">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">{{ __('No') }}</th>
                                                            <th scope="col">{{ __('Instructor') }}</th>
                                                            <th scope="col">{{ __('Type') }}</th>
                                                            <th scope="col">{{ __('Status') }}</th>
                                                            <th scope="col">{{ __('Course') }}</th>
                                                            <th scope="col">{{ __('Date Time') }}</th>
                                                            {{-- <th scope="col">{{ __('Action') }}</th> --}}
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $i = 0;
                                                        @endphp
                                                        @foreach($getAllClassesHistory as $individual)
                                                        <tr>
                                                            <td>{{ ++$i }}</td>
                                                            <td>
                                                                <div class="table-data-with-img d-flex align-items-center">
                                                                    <div class="flex-grow-1 table-data-course-name">{{ Str::limit($individual->instructor->first_name.' '.$individual->instructor->last_name, 50) }}</div>
                                                                </div>
                                                                <div>
                                                                
                                                                </div>
                                                            </td>
                                                            <td>
                                                              
                                                                <div class="table-data-with-img d-flex align-items-center">
                                                                    <div class="flex-grow-1 table-data-course-name">{{ Str::limit($individual->class?->type, 50) }}</div>
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
                                                                    $lsntime = strtotime($lesson_time) ;
                                                                    $lesson_duration =$individual->class->duration;
                                                                    $lsntimestamp = strtotime($lesson_time) + $lesson_duration*60;
                                                                    $lsnendtime = $lsntimestamp ;
                                                                
                                                                    // echo $updated_time.'-----'.$updated_cur_time ;
                                                                @endphp
                                                                
                                                                <?php
                                                                if($curdate <  $lsndate)
                                                                        {
                                                                           
                                                                            ?>
                                                                        <span style="border:none;font-weight:bolder;background-color:#322740;color:white;padding:4px;" class="btn btn-warning m-1 py-1 px-2">
                                                                            Scheduled  
                                                                </span>
                                                                <?php }
                                                                else if($curdate == $lsndate)
                                                                {
                                                                  
                                                                if($curtime < $lsntime)
                                                                    {
                                                                       
                                                                        $id = 'lec_link1_'.$individual->id;
                                                                        echo "<a class='btn btn-warning m-1 lec_link1 py-1 px-2' id='$id' style='padding:4px'>".'Waiting'."</a>";
                                            
                                                                    }

                                                                    else if($curtime >= $lsntime && $curtime < $lsnendtime && $individual->meeting_id == NULL && $individual->class->learning_tool == 'Zoom')
                                                                    {
                                                                       
                                                                        $id = 'lec_link1_'.$individual->id;
                                                                        echo "<a class='btn btn-warning m-1 lec_link1 py-1 px-2' id='$id' style='padding:4px'>".'Waiting'."</a>";
                                                                    }
                                                                    else if($curtime >= $lsntime && $curtime < $lsnendtime && $individual->StudentURI == NULL && $individual->class->learning_tool == 'BBB') {
                                                                         $id = 'lec_link1_'.$individual->id;
                                                                         echo "<a class='btn btn-warning m-1 lec_link1 py-1 px-2 small' id='$id'>Waiting</a>";
                                                                    }
                                                                    else if($curtime >= $lsntime && $curtime < $lsnendtime && $individual->meeting_id == NULL && $individual->class->learning_tool == 'Google_Meet') {
                                                                         $id = 'lec_link1_'.$individual->id;
                                                                         echo "<a class='btn btn-warning m-1 lec_link1 py-1 px-2 small' id='$id'>Waiting</a>";
                                                                    }
                                                                    else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Zoom')
                                            
                                                                    { 
                                                                      
                                                                        ?>
                                                                        <a href="javascript:void(0);" data-lesson_id ="<?php echo $individual->id ; ?>"  data-meeting_id ="<?php echo $individual->meeting_id ; ?>" data-user_type ="<?php echo "Student" ; ?>" data-user_name ="<?php echo $individual->student->first_name.' '.$individual->student->last_name ; ?>"  data-user_id ="<?php echo $individual->student_id ; ?>"  class="btn btn-info m-1 create_zoom_meeting_link py-1 px-2" style="background-color:#499619;color: #fff;padding:4px;" target='_blank'>Start Class  </a>
                                                                    <?php
                                                                    }
                                                                    else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'BBB') { 
                                                                                $url = $individual->StudentURI;
                                                                                ?>
                                                                               	<a href="<?php echo $url; ?>"  class='btn btn-info m-1 py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                                    <?php }
                                                                      else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Google_Meet') { 
                                                                                $url = $individual->google_meet_link;
                                                                                ?>
                                                                               	<a href="<?php echo $url; ?>"  class='btn btn-info m-1 py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                                    <?php }

                                                                    else if($curtime == $lsntime &&($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'Zoom')
                    
                                                                    { 
                                            
                                                                    if($individual->status == "Completed" && $individual->is_moderator_left == '1'){
                                                                            echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                                        }
                                                                        else if($individual->status == "Missed" && $individual->is_moderator_left == '1'){
                                                                            echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>"; 
                                                                        }
                                                                        ?>
                                                                    <?php
                                        
                                    
                                                                    }
                                                                    else if($curtime == $lsntime && ($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'BBB') {
                                                                        if($individual->status == "Completed" && $individual->StudentURI != null) {
                                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                        } else if($individual->status == "Missed" && $individual->StudentURI != null) {
                                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>"; 
                                                                        }
                                                                    } 
                                                                    else if($curtime == $lsntime && ($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'Google_Meet') {
                                                                        if($individual->status == "Completed" && $individual->meeting_id != null) {
                                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                        } else if($individual->status == "Missed" && $individual->meeting_id != null) {
                                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>"; 
                                                                        }
                                                                    } 

                                                                    else if($curtime > $lsntime && $curtime < $lsnendtime &&$individual->status != "Completed"&&$individual->status != "Missed" && ($individual->meeting_id == NULL) && $individual->class->learning_tool == 'Zoom')
                                                                                            {
                                                                                              
                                                                                                ?>
                                                                                                <a href="javascript:void(0);" data-lesson_id ="<?php echo $individual->id ; ?>"  data-meeting_id ="<?php echo $individual->meeting_id ; ?>" data-user_type ="<?php echo "Student" ; ?>" data-user_name ="<?php echo $individual->student->first_name.' '.$individual->student->last_name ; ?>"  data-user_id ="<?php echo $individual->student_id ; ?>"  class="btn btn-info m-1 create_zoom_meeting_link py-1 px-2" style="background-color:#499619;color: #fff;padding:4px;" target='_blank'>Start Class  </a>
                                                                                                <?php 
                                                                                            }
                                                                    else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'BBB') { ?>   
                                                                                <?php
                                                                                $url = $individual->StudentURI;
                                                                                ?>
                                                                               	<a href="<?php echo $url; ?>"  class='btn btn-info m-1 py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                                            <?php 
                                                                    }   
                                                                    else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Google_Meet') { ?>   
                                                                                <?php
                                                                                $url = $individual->google_meet_link;
                                                                                ?>
                                                                               	<a href="<?php echo $url; ?>"  class='btn btn-info m-1 py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                                            <?php 
                                                                    }   

                                                                    else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Zoom')
                                                                    { 
                                                                     
                                                                        ?>   
                                                                    <a  href="javascript:void(0);"  id="lec_link" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>" data-user_type ="<?php echo "Student" ; ?>" data-user_name ="<?php echo $individual->student->first_name.' '.$individual->student->last_name ; ?>"  data-user_id ="<?php echo $individual->student_id ; ?>"   class="btn btn-info m-1 create_zoom_meeting_link py-1 px-2" style="background-color:#499619;color: #fff;padding:4px;" target='_blank'>Start Class  </a>
                                                                    <?php 
                                                                    }
                                                                    else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'BBB') { ?>   
                                                                                <?php
                                                                                $url = $individual->StudentURI;
                                                                                ?>
                                                                               	<a href="<?php echo $url; ?>"  class='btn btn-info m-1 py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                                            <?php 
                                                                    }   
                                                                    else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Google_Meet') { ?>   
                                                                                <?php
                                                                                $url = $individual->google_meet_link;
                                                                                ?>
                                                                               	<a href="<?php echo $url; ?>"  class='btn btn-info m-1 py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                                            <?php 
                                                                    }   

                                                                    else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->class->learning_tool == 'Zoom')
                                                                    {
                                                                        if($individual->status == "Completed" && $individual->is_moderator_left == '1'){
                                                                            echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                                        }
                                                                        else if($individual->status == "Missed" && $individual->is_moderator_left == '1'){
                                                                            echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>"; 
                                                                        }
                                                                        else{
                                                                           
                                                                        ?>   
                                                                        <a  href="javascript:void(0);"  id="lec_link" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>" data-user_type ="<?php echo "Student" ; ?>" data-user_name ="<?php echo $individual->student->first_name.' '.$individual->student->last_name ; ?>"  data-user_id ="<?php echo $individual->student_id ; ?>"   class="btn btn-info m-1 create_zoom_meeting_link py-1 px-2" style="background-color:#499619;color: #fff;padding:4px;" target='_blank'>Start Class  </a>
                                                                        <?php 
                                                                        }
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
                                                                    else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->class->learning_tool == 'Google_Meet') {
                                                                        if($individual->status == "Completed" && $individual->meeting_id != null) {
                                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                        } else if($individual->status == "Missed" && $individual->meeting_id != null) {
                                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>"; 
                                                                        } else { ?>   
                                                                        <a href="javascript:void(0);"  data-student_uri ="<?php echo $individual->google_meet_link ; ?>" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>"  class='btn btn-info m-1 py-1 px-2 start_meet_individual' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>  
                                                                        <?php }
                                                                    } 

                                                                    else if($curtime == $lsnendtime && $individual->class->learning_tool == 'Zoom')
                                                                    {
                                                                        if(($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'Zoom' && ($individual->is_tutor_joined == '1' ||$individual->is_tutor_joined == "1" || $individual->is_tutor_joined == 1 || $individual->is_tutor_joined)){
                                                                            updateLessonStatusCompleteZoom($individual->id,$individual->meeting_id,null);
                                                                            echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                                            ?>   
                                                                        <?php 
                                                                        }
                                                                        else if(($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'Zoom' && $individual->is_tutor_joined == '0'){
                                                                            updateLessonStatusMissedZoom($individual->id,$individual->meeting_id,null);
                                                                            echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>";  
                                                                        ?>   
                                                                    <?php 
                                                                    }
                                                                        else{
                                                                            if($individual->status == "Completed"){
                                                                                echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                                            }
                                                                            else{
                                                                                echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>";     
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

                                                                    else if($curtime > $lsnendtime)
                                                                    {
                                                                        if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Zoom' && $individual->is_tutor_joined == '1' || $individual->is_tutor_joined == "1" || $individual->is_tutor_joined == 1 || $individual->is_tutor_joined)
                                                                            {	
                                                                                updateLessonStatusCompleteZoom($individual->id,$individual->meeting_id,null);
                                                                                echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                                            }
                                                                        else if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'BBB' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {	
                                                                            updateLessonStatusComplete($individual->id,$individual->meeting_id,null);
                                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                        }
                                                                        else if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Google_Meet' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {	
                                                                            updateLessonStatusComplete($individual->id,$individual->meeting_id,null);
                                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                        }
                                                                        else if($individual->status == "Completed"){
                                                                            echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                                        }
                                                                        else
                                                                        {
                                                                            if($individual->status == "Missed"){
                                                                                if(!empty($individual->meeting_id)){
                                                                                    $res = check_missed_status($individual->meeting_id);
                                                                                    if($res == 'Completed'){
                                                                                        echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                                                    }
                                                                                    else{
                                            
                                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>";    
                                                                                    }  
                                                                                }
                                                                                else{
                                                                                echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>";    
                                                                                }
                                                                            }
                                                                            else if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == "Zoom" && $individual->is_tutor_joined == '0'){
                                                                                        updateLessonStatusMissedZoom($individual->id,$individual->meeting_id,null);
                                                                                    
                                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>";  
                                                                                        ?>
                                                                                
                                                                                                                
                                                                            <?php }
                                                                            else if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == "BBB" && $individual->is_instructor_joined == '0') {
                                                                                        updateLessonStatusMissed($individual->id,$individual->meeting_id,null);
                                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";  
                                                                            }
                                                                            else if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == "Google_Meet" && $individual->is_instructor_joined == '0') {
                                                                                        updateLessonStatusMissed($individual->id,$individual->meeting_id,null);
                                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";  
                                                                            }
                                                                            else{
                                            
                                                                            echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>";    
                                                                            }
                                                                        }
                                                                    }
                                                                    }
                                                                    else if($curdate > $lsndate)
                                                                    {
                                                                        if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Zoom' && $individual->is_tutor_joined == '1' || $individual->is_tutor_joined == "1" || $individual->is_tutor_joined == 1 || $individual->is_tutor_joined)
                                                                        {	
                                                                            updateLessonStatusCompleteZoom($individual->id,$individual->meeting_id,null);
                                                                            echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                                        }
                                                                        else if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'BBB' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {	
                                                                                updateLessonStatusComplete($individual->id,$individual->meeting_id,null);
                                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                        } 
                                                                        else if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Google_Meet' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {	
                                                                                updateLessonStatusComplete($individual->id,$individual->meeting_id,null);
                                                                                echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                        } 
                                                                        else if($individual->status == "Completed")
                                                                        {
                                                                            echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                                        }
                                                                        else if(($individual->status == "Pending" || $individual->status == "Scheduled")){
                                                                            
                                                                            echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>";    
                                                                        }
                                                                        else
                                                                        {
                                                                                if($individual->status == "Missed"){
                                                
                                                                                    if(!empty($individual->meeting_id)){
                                                
                                                                                        $res = check_missed_status($individual->meeting_id);
                                                                                        if($res == 'Completed'){
                                                                                            echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                                                        }
                                                                                        // elseif($res == "Missed"){
                                                                                        else{
                                                
                                                                                            echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>";    
                                                                                        }  
                                                                                    }
                                                                                    else{
                                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>";    
                                                                                    }
                                                
                                                                                }
                                                                        }
                                                                    }
                                                                    else{
                                                                        if($individual->status == "Completed")
                                                                        {  
                                                                            echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                                        }
                                                                else
                                                                {
                                                                        if($individual->status == "Missed"){
                                        
                                                                            if(!empty($individual->meeting_id)){
                                        
                                                                                $res = check_missed_status($individual->meeting_id);
                                                                                if($res == 'Completed'){
                                                                                    echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                                                }  
                                                                                // elseif($res == "Missed"){
                                                                                else{
                                        
                                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>";    
                                                                                }
                                                                            }
                                                                            else{
                                                                                echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>";    
                                                                            }
                                        
                                                                        }
                                                                }
                                                                }?>
                                                            </td>
                                                            <td>
                                                                <div class="table-data-with-img d-flex align-items-center">
                                                                    <div class="flex-grow-1 table-data-course-name">{{ Str::limit($individual->course? $individual->course->title : null, 50) }}</div>
                                                                </div>
                                                            </td>
                                                            {{-- <td>{{ date('d-m-Y',strtotime($individual->class_date)).' 
                                                            '.$updated_time }}</td> --}}
                                                                <td>{{ date('d-m-Y',strtotime($individual->class_date)).'  '.date("D", strtotime($individual->class_date)).'  '.$updated_time }}</td>
                                                            {{-- <td>{{$individual->status }}</td> --}}
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            {{-- Classes for mobile view --}}
                                                <div class="courses-cards">
                                                    @php $i = 0; @endphp
                                                    @foreach($getAllClassesHistory as $individual)
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
                                            
                                            {{-- <div class="courses-cards">
                                                @php
                                                    $i = 0;
                                                @endphp
                                                @foreach($getAllClassesHistory as $individual)
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
                                                                <div class="flex-grow-1 table-data-course-name">{{ Str::limit($individual->course? $individual->course->title : null, 50) }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="font-15 color-heading">
                                                                <strong>Date Time:</strong>
                                                                {{ date('d-m-Y',strtotime($individual->class_date)).'  '.date("D", strtotime($individual->class_date)).'  '.$updated_time }}
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
                                                                $lsntime = strtotime($lesson_time) ;
                                                                $lesson_duration =$individual->class->duration;
                                                                $lsntimestamp = strtotime($lesson_time) + $lesson_duration*60;
                                                                $lsnendtime = $lsntimestamp ;
                                                           
                                                            @endphp
                                                            
                                                            <?php
                                                            if($curdate <  $lsndate)
                                                                    {?>
                                                                    <span  style="border:none;font-weight:bolder;background-color:#322740;color:white;padding:4px;" class="btn btn-warning m-1 py-1 px-2">
                                                                        Scheduled  
                                                            </span>
                                                            <?php }
                                                            else if($curdate == $lsndate)
                                                            {
                                                            
                                                            if($curtime < $lsntime)
                                                                {
                                                                    $id = 'lec_link1_'.$individual->id;
                                                                    echo "<a class='btn btn-warning m-1 lec_link1 py-1 px-2' id='$id'>".'Waiting'."</a>";
                                        
                                                                }

                                                                else if($curtime >= $lsntime && $curtime < $lsnendtime && $individual->meeting_id == NULL && $individual->class->learning_tool == 'Zoom')
                                                                {
                                                                    $id = 'lec_link1_'.$individual->id;
                                                                    echo "<a class='btn btn-warning m-1 lec_link1 py-1 px-2' id='$id'>".'Waiting'."</a>";
                                                                }
                                                                else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Zoom')
                                        
                                                                { 
                                                                    ?>
                                                                    <a href="javascript:void(0);" data-lesson_id ="<?php echo $individual->id ; ?>"  data-meeting_id ="<?php echo $individual->meeting_id ; ?>" data-user_type ="<?php echo "Student" ; ?>" data-user_name ="<?php echo $individual->student->first_name.' '.$individual->student->last_name ; ?>"  data-user_id ="<?php echo $individual->student_id ; ?>"  class="btn btn-info m-1 create_zoom_meeting_link px-2 py-1" style="background-color:#499619;color: #fff;padding:4px;" target='_blank'>Start Class  </a>
                                                                <?php
                                                                }
                                                                else if($curtime == $lsntime &&($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'Zoom')
                
                                                                { 
                                        
                                                                if($individual->status == "Completed" && $individual->is_moderator_left == '1'){
                                                                        echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                                    }
                                                                    else if($individual->status == "Missed" && $individual->is_moderator_left == '1'){
                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>"; 
                                                                    }
                                                                    ?>
                                                                <?php
                                    
                                
                                                                }
                                                                else if($curtime > $lsntime && $curtime < $lsnendtime &&$individual->status != "Completed"&&$individual->status != "Missed" && ($individual->meeting_id == NULL) && $individual->class->learning_tool == 'Zoom')
                                                                                        {
                                                                                        
                                                                                            ?>
                                                                                            <a href="javascript:void(0);" data-lesson_id ="<?php echo $individual->id ; ?>"  data-meeting_id ="<?php echo $individual->meeting_id ; ?>" data-user_type ="<?php echo "Student" ; ?>" data-user_name ="<?php echo $individual->student->first_name.' '.$individual->student->last_name ; ?>"  data-user_id ="<?php echo $individual->student_id ; ?>"  class="btn btn-info m-1 create_zoom_meeting_link px-2 py-1" style="background-color:#499619;color: #fff;padding:4px;" target='_blank'>Start Class  </a>
                                                                                            <?php 
                                                                                        }
                                                                                        
                                                                else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Zoom')
                                                                { 
                                                                
                                                                    ?>   
                                                                <a  href="javascript:void(0);"  id="lec_link" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>" data-user_type ="<?php echo "Student" ; ?>" data-user_name ="<?php echo $individual->student->first_name.' '.$individual->student->last_name ; ?>"  data-user_id ="<?php echo $individual->student_id ; ?>"   class="btn btn-info m-1 create_zoom_meeting_link  px-2 py-1" style="background-color:#499619;color: #fff;padding:4px;" target='_blank'>Start Class  </a>
                                                                <?php 
                                                                }
                                                                else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->class->learning_tool == 'Zoom')
                                                                {
                                                                    if($individual->status == "Completed" && $individual->is_moderator_left == '1'){
                                                                        echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                                    }
                                                                    else if($individual->status == "Missed" && $individual->is_moderator_left == '1'){
                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>"; 
                                                                    }
                                                                    else{
                                                                    ?>   
                                                                    <a  href="javascript:void(0);"  id="lec_link" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>" data-user_type ="<?php echo "Student" ; ?>" data-user_name ="<?php echo $individual->student->first_name.' '.$individual->student->last_name ; ?>"  data-user_id ="<?php echo $individual->student_id ; ?>"   class="btn btn-info m-1 create_zoom_meeting_link px-2 py-1" style="background-color:#499619;color: #fff;padding:4px;" target='_blank'>Start Class  </a>
                                                                    <?php 
                                                                    }
                                                                }
                                                                else if($curtime == $lsnendtime && $individual->class->learning_tool == 'Zoom')
                                                                {
                                                                    if(($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'Zoom' && ($individual->is_tutor_joined == '1' ||$individual->is_tutor_joined == "1" || $individual->is_tutor_joined == 1 || $individual->is_tutor_joined)){
                                                                        updateLessonStatusCompleteZoom($individual->id,$individual->meeting_id,null);
                                                                        echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                                        ?>   
                                                                    <?php 
                                                                    }
                                                                    else if(($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'Zoom' && $individual->is_tutor_joined == '0'){
                                                                        updateLessonStatusMissedZoom($individual->id,$individual->meeting_id,null);
                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>";  
                                                                    ?>   
                                                                <?php 
                                                                }
                                                                    else{
                                                                        if($individual->status == "Completed"){
                                                                            echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                                        }
                                                                        else{
                                                                            echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>";     
                                                                        }
                                                                    }
                                                                }
                                                                else if($curtime > $lsnendtime)
                                                                {
                                                                    if($individual->status == "Pending" && $individual->class->learning_tool == 'Zoom' && $individual->is_tutor_joined == '1' || $individual->is_tutor_joined == "1" || $individual->is_tutor_joined == 1 || $individual->is_tutor_joined)
                                                                                    {	
                                                                                        updateLessonStatusCompleteZoom($individual->id,$individual->meeting_id,null);
                                                                                        echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                                                    }
                                                                    else if($individual->status == "Completed"){
                                                                        echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                                    }
                                                                    else
                                                                    {
                                                                        if($individual->status == "Missed"){
                                                                            if(!empty($individual->meeting_id)){
                                                                                $res = check_missed_status($individual->meeting_id);
                                                                                if($res == 'Completed'){
                                                                                    echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                                                }
                                                                                else{
                                        
                                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>";    
                                                                                }  
                                                                            }
                                                                            else{
                                                                            echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>";    
                                                                            }
                                                                        }
                                                                        else if($individual->status == "Pending" && $individual->class->learning_tool == "Zoom" && $individual->is_tutor_joined == '0'){
                                                                                    updateLessonStatusMissedZoom($individual->id,$individual->meeting_id,null);
                                                                                
                                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>";  
                                                                                    ?>
                                                                            
                                                                                                            
                                                                        <?php }
                                                                        else{
                                        
                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>";    
                                                                        }
                                                                    }
                                                                }
                                                                }
                                                                else if($curdate > $lsndate)
                                                                {
                                                                    if($individual->status == "Pending" && $individual->class->learning_tool == 'Zoom' && $individual->is_tutor_joined == '1' || $individual->is_tutor_joined == "1" || $individual->is_tutor_joined == 1 || $individual->is_tutor_joined)
                                                                    {	
                                                                        updateLessonStatusCompleteZoom($individual->id,$individual->meeting_id,null);
                                                                        echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                                    }
                                                                    else if($individual->status == "Completed")
                                                                    {
                                                                        echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                                    }
                                                                    else if($individual->status == "Pending"){
                                                                        
                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>";    
                                                                    }
                                                                    else
                                                                    {
                                                                            if($individual->status == "Missed"){
                                            
                                                                                if(!empty($individual->meeting_id)){
                                            
                                                                                    $res = check_missed_status($individual->meeting_id);
                                                                                    if($res == 'Completed'){
                                                                                        echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                                                    }
                                                                                 
                                                                                    else{
                                            
                                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>";    
                                                                                    }  
                                                                                }
                                                                                else{
                                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>";    
                                                                                }
                                            
                                                                            }
                                                                    }
                                                                }
                                                                else{
                                                                    if($individual->status == "Completed")
                                                                    {  
                                                                        echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                                    }
                                                            else
                                                            {
                                                                    if($individual->status == "Missed"){
                                    
                                                                        if(!empty($individual->meeting_id)){
                                    
                                                                            $res = check_missed_status($individual->meeting_id);
                                                                            if($res == 'Completed'){
                                                                                echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                                            }  
                                                                        
                                                                            else{
                                    
                                                                                echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>";    
                                                                            }
                                                                        }
                                                                        else{
                                                                            echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>";    
                                                                        }
                                    
                                                                    }
                                                            }
                                                            }?>
                                                        </div>
                                                    
                                                    </div>
                                                @endforeach
                                            </div> --}}
                                            <!-- Pagination Start -->
                                            @if(@$getAllClassesHistory->hasPages())
                                                {{ @$getAllClassesHistory->links('frontend.paginate.paginate') }}
                                            @endif
                                            <!-- Pagination End -->
                                        @else
                                            <!-- If there is no data Show Empty Design Start -->
                                            <div class="empty-data">
                                                <img src="{{ asset('frontend/assets/img/empty-data-img.png') }}" alt="img" class="img-fluid">
                                                <h5 class="my-3">{{ __('No Individual Class History Available') }}</h5>
                                            </div>
                                            <!-- If there is no data Show Empty Design End -->
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>





















































            <div class="row">
                <div class="col-12">
                    <div class="row bg-white">
                        <div class="col-lg-12 p-0">
                            <div class="student-profile-right-part">
                                <h6>{{ __('Group Class History') }}</h6>
                                <hr>
                                <input type="hidden" name="base_url" id="base_url" value="{{$base_url}}">
                                {{-- <input type="hidden" id="base_url" value="{{url('/')}}"> --}}
                                <div class="row">
                                    <div class="col-12">
                                        @if(!$getAllClassesHistoryGroup->isEmpty())
                                            <div class="table-responsive table-responsive-xl courses-table">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">{{ __('No') }}</th>
                                                            <th scope="col">{{ __('Instructor') }}</th>
                                                            <th scope="col">{{ __('Type') }}</th>
                                                            <th scope="col">{{ __('Status') }}</th>
                                                            <th scope="col">{{ __('Course') }}</th>
                                                            <th scope="col">{{ __('Date Time') }}</th>
                                                            {{-- <th scope="col">{{ __('Action') }}</th> --}}
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $i = 0;
                                                        @endphp
                                                        @foreach($getAllClassesHistoryGroup as $individual)
                                                        <tr>
                                                            <td>{{ ++$i }}</td>
                                                            <td>
                                                                <div class="table-data-with-img d-flex align-items-center">
                                                                    <div class="flex-grow-1 table-data-course-name">{{ Str::limit($individual->instructor->first_name.' '.$individual->instructor->last_name, 50) }}</div>
                                                                </div>
                                                                <div>
                                                                
                                                                </div>
                                                            </td>
                                                            <td>
                                                              
                                                                <div class="table-data-with-img d-flex align-items-center">
                                                                    <div class="flex-grow-1 table-data-course-name">{{ Str::limit($individual->class?->type, 50) }}</div>
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
                                                                    $lsntime = strtotime($lesson_time) ;
                                                                    $lesson_duration =$individual->class->duration;
                                                                    $lsntimestamp = strtotime($lesson_time) + $lesson_duration*60;
                                                                    $lsnendtime = $lsntimestamp ;
                                                                
                                                                    // echo $updated_time.'-----'.$updated_cur_time ;
                                                                @endphp
                                                                
                                                               
                                                                <?php 
                                                                    if($curdate > $lsndate)
                                                                    {
                                                                        if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Zoom' && $individual->is_tutor_joined == '1' || $individual->is_tutor_joined == "1" || $individual->is_tutor_joined == 1 || $individual->is_tutor_joined)
                                                                        {	
                                                                            updateLessonStatusCompleteZoom($individual->id,$individual->meeting_id,$individual->group_id);
                                                                            echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;'>".'Completed'."</span>";
                                                                        }
                                                                        else if ($individual->class->learning_tool == 'BBB' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {
                                                                            updateLessonStatusComplete($individual->id, $individual->meeting_id, $individual->group_id);
                                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                                        }
                                                                        else if($individual->status == "Completed")
                                                                        {
                                                                            echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                                        }
                                                                        else if(($individual->status == "Pending" || $individual->status == "Scheduled")){
                                                                            
                                                                            echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>";    
                                                                        }
                                                                        else
                                                                        {
                                                                                if($individual->status == "Missed"){
                                                
                                                                                    if(!empty($individual->meeting_id)){
                                                
                                                                                        $res = check_missed_status($individual->meeting_id);
                                                                                        if($res == 'Completed'){
                                                                                            echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                                                        }
                                                                                        // elseif($res == "Missed"){
                                                                                        else{
                                                
                                                                                            echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>";    
                                                                                        }  
                                                                                    }
                                                                                    else{
                                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>";    
                                                                                    }
                                                
                                                                                }
                                                                        }
                                                                    }
                                                                    else{
                                                                        if($individual->status == "Completed")
                                                                        {  
                                                                            echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                                        }
                                                                else
                                                                {
                                                                        if($individual->status == "Missed"){
                                        
                                                                            if(!empty($individual->meeting_id)){
                                        
                                                                                $res = check_missed_status($individual->meeting_id);
                                                                                if($res == 'Completed'){
                                                                                    echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                                                }  
                                                                                // elseif($res == "Missed"){
                                                                                else{
                                        
                                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>";    
                                                                                }
                                                                            }
                                                                            else{
                                                                                echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>";    
                                                                            }
                                        
                                                                        }
                                                                }
                                                                }?>
                                                            </td>
                                                            <td>
                                                                <div class="table-data-with-img d-flex align-items-center">
                                                                    <div class="flex-grow-1 table-data-course-name">{{ Str::limit($individual->course? $individual->course->title : null, 50) }}</div>
                                                                </div>
                                                            </td>
                                                            {{-- <td>{{ date('d-m-Y',strtotime($individual->class_date)).' 
                                                            '.$updated_time }}</td> --}}
                                                                <td>{{ date('d-m-Y',strtotime($individual->class_date)).'  '.date("D", strtotime($individual->class_date)).'  '.$updated_time }}</td>
                                                            {{-- <td>{{$individual->status }}</td> --}}
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            
                                      
                                            <!-- Pagination Start -->
                                            @if(@$getAllClassesHistoryGroup->hasPages())
                                                {{ @$getAllClassesHistoryGroup->links('frontend.paginate.paginate') }}
                                            @endif
                                            <!-- Pagination End -->
                                        @else
                                            <!-- If there is no data Show Empty Design Start -->
                                            <div class="empty-data">
                                                <img src="{{ asset('frontend/assets/img/empty-data-img.png') }}" alt="img" class="img-fluid">
                                                <h5 class="my-3">{{ __('No Group Class History Available') }}</h5>
                                            </div>
                                            <!-- If there is no data Show Empty Design End -->
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
//  alert(res)
 userType = $(this).attr("data-user_type");
 var murl = base_url+'/'+'student/NewZoomMeeting/'+meetingId+'/'+userType+'/'+res+'/'+userId+'/'+lessonId;
 var currentHref = murl;
 window.open(currentHref, '_blank');
});






        function ajaxCallfirst1() 
    {
      var base_url = window.location.origin;
    	$.ajax({
            url: base_url+"/student/checkLessonStartTime",
            dataType: "json",
            success: (function (result) {
                if(result.l_id.length > 0)
                {
                	var lesson_id = JSON.parse(result.l_id);
                  $('#lesson_id').val(lesson_id);                       
              
                    if(result.learning_tool == 'Zoom'){
                    $('#lec_link1_'+lesson_id).attr('href', result.url); 
                  $('#lec_link1_'+lesson_id).attr('target', '_blank'); 
                  $('#lec_link1_'+lesson_id).html("Start Class"); 
                  $('#lec_link1_'+lesson_id).css("background-color", "#499619"); 
                  $('#lec_link1_'+lesson_id).css("color", "#fff"); 
                  $('#lec_link1_'+lesson_id).css("padding", "5px 10px");
                  $('#lec_link1_'+lesson_id).attr('data-lesson_id', lesson_id); 
                  $('#lec_link1_'+lesson_id).attr('data-meeting_id', result.meeting_id); 
                  $('#lec_link1_'+lesson_id).attr('data-user_name', result.user_name); 
                  $('#lec_link1_'+lesson_id).attr('data-user_id', result.user_id); 
                  $('#lec_link1_'+lesson_id).attr('data-user_type', result.user_type); 
                  $('#lec_link1_'+lesson_id).addClass('create_zoom_meeting_link');
                    }
                 
                    IsmeetingCreated = $('#lec_link1_'+lesson_id).attr('data-meeting_created'); 
                    if(IsmeetingCreated != 'Yes'){
                        var audioElement = document.createElement('audio');
                         audioElement.setAttribute('src', base_url+'/public/assets/notification_sound/Cool_Notification_2.mp3');
                            // audioElement.play();
                    }
                    $('#lec_link1_'+lesson_id).attr('data-meeting_created', 'Yes'); 

                
            }
            })
        })
    }

ajaxCallfirst1(); // To output when the page loads
setInterval(ajaxCallfirst1, (50 * 1000)); // x * 1000 to get it in seconds
  </script>



@push('style')
    <link rel="stylesheet" href="{{asset('admin/css/custom/image-preview.css')}}">
@endpush

@push('script')
    <script src="{{asset('admin/js/custom/image-preview.js')}}"></script>
    <script src="{{asset('frontend/assets/js/custom/student-profile.js')}}"></script>
@endpush


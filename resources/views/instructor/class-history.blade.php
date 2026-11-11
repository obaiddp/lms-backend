@extends('layouts.instructor')

@section('breadcrumb')
    <div class="page-banner-content text-center">
        <h3 class="page-banner-heading text-white pb-15"> {{ __('Class History') }} </h3>

        <!-- Breadcrumb Start-->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item font-14"><a href="{{route('instructor.dashboard')}}">{{__('Dashboard')}}</a></li>
                <li class="breadcrumb-item font-14 active" aria-current="page">{{ __('Class History') }}</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content3')
    <div class="instructor-profile-right-part">
        <div class="instructor-quiz-list-page instructor-live-class-page">

            <div class="instructor-my-courses-title d-flex justify-content-between align-items-center">
                <h6>{{ __('Individual Class History') }}</h6>
            </div>
            <input type="hidden" name="base_url" id="base_url" value="{{$base_url}}">
            <div class="row">
                <div class="col-12">
                    @if(!$getIndividualClassesHistory->isEmpty())
                        <div class="table-responsive table-responsive-xl courses-table">
                            <table class="table" id="individual_table">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __('No') }}</th>
                                        <th scope="col">{{ __('Student') }}</th>
                                        <th scope="col">{{ __('Status') }}</th>
                                        <th scope="col">{{ __('Course') }}</th>
                                        <th scope="col">{{ __('Learning Tool') }}</th>
                                        <th scope="col">{{ __('Date Time') }}</th>
                                        {{-- <th scope="col">{{ __('Action') }}</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 0;
                                    @endphp
                                    @foreach($getIndividualClassesHistory as $individual)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>
                                            @if ($individual->student)
                                                <div class="table-data-with-img d-flex align-items-center">
                                                    <div class="flex-grow-1 table-data-course-name">{{ Str::limit($individual->student->first_name.' '.$individual->student->last_name, 50) }}</div>
                                                </div> 
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $updated_time = changetimeByZone($individual->class_time,$individual->instructor_id,'instructor');
                                                $lesson_time = $updated_time; 

                                                    $curdate = strtotime(date("Y-m-d"));
                                                    $lsndate = strtotime($individual->class_date); 
                                                    $updated_cur_time = changetimeByZone(date("H:i"),$individual->instructor_id,'instructor');
                                                    $curtime = strtotime($updated_cur_time);
                                                    $lsntime = strtotime($lesson_time) ;
                                                    $lesson_duration =$individual->class->duration;
                                                    $lsntimestamp = strtotime($lesson_time) + $lesson_duration*60;
                                                    $lsnendtime = $lsntimestamp ;
                                                
                                                    // echo $updated_time.'-----'.$updated_cur_time ;
                                            @endphp
                                            
                                            <?php
                                                if($curdate <  $lsndate)
                                                        {?>
                                                        <span  style="border:none;font-weight:bolder;background-color:#322740;color:white;" class="btn btn-warning m-1 px-2 py-1">
                                                            Scheduled  
                                                </span>
                                                <?php }
                                            else if($curdate == $lsndate)
                                            {
                                            if($curtime < $lsntime)
                                                {
                                                    $id = 'lec_link1_'.$individual->id;
                                                    echo "<a class='btn btn-warning m-1 lec_link1 px-2 py-1' id='$id'>".'Waiting'."</a>";
                           
                                                }
                                                else if ($curtime >= $lsntime && $curtime < $lsnendtime && $individual->meeting_id == NULL && $individual->class->learning_tool == 'Zoom') {
                                                        $id = 'lec_link1_' . $individual->id;
                                                        echo "<a class='btn btn-warning m-1 lec_link1 px-2 py-1 small' id='$id'>" . 'Waiting' . "</a>";
                                                }
                                                else if ($curtime >= $lsntime && $curtime < $lsnendtime && $individual->InstructorURI == NULL && $individual->class->learning_tool == 'BBB') {
                                                        $id = 'lec_link1_' . $individual->id;
                                                        echo "<a class='btn btn-warning m-1 lec_link1 px-2 py-1 small' id='$id'>" . 'Waiting' . "</a>";
                                                } 
                                                else if ($curtime >= $lsntime && $curtime < $lsnendtime && $individual->meeting_id == NULL && $individual->class->learning_tool == 'Google_Meet') {
                                                        $id = 'lec_link1_' . $individual->id;
                                                        echo "<a class='btn btn-warning m-1 lec_link1 px-2 py-1 small' id='$id'>" . 'Waiting' . "</a>";
                                                } 

                                                else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Zoom')
                           
                                                { 
                                                    ?>
                                                    <a href="javascript:void(0);"  data-meeting_id ="<?php echo $individual->meeting_id ; ?>" data-user_type ="<?php echo "Tutor" ; ?>" data-user_name ="<?php echo $individual->instructor->first_name.' '.$individual->instructor->last_name ; ?>"  data-user_id ="<?php echo $individual->instructor_id ; ?>"  class="btn btn-info m-1 create_zoom_meeting_link py-1 px-2" style="background-color:#499619;color: #fff" target='_blank'>Start Class  </a>
                                                <?php
                                                }
                                                 else if($curtime == $lsntime &&($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'Zoom')
 
                                                { 
                         
                                                if($individual->status == "Completed" && $individual->is_moderator_left == '1'){
                                                        echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                    }
                                                    else if($individual->status == "Missed" && $individual->is_moderator_left == '1'){
                                                        echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                                                    }
                                                    ?>
                                                <?php
                     
                   
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


                                                else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Zoom')
                                                { 
                                                
                                                    ?>   
                                                <a  href="javascript:void(0);"  id="lec_link" data-meeting_id ="<?php echo $individual->meeting_id ; ?>" data-user_type ="<?php echo "Tutor" ; ?>" data-user_name ="<?php echo $individual->instructor->first_name.' '.$individual->instructor->last_name ; ?>"  data-user_id ="<?php echo $individual->instructor_id ; ?>"   class="btn btn-info m-1 create_zoom_meeting_link py-1 px-2" style="background-color:#499619;color: #fff" target='_blank'>Start Class  </a>
                                                <?php 
                                                }
                                                else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->class->learning_tool == 'Zoom')
                                                {
                                                    if($individual->status == "Completed" && $individual->is_moderator_left == '1'){
                                                        echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                    }
                                                    else if($individual->status == "Missed" && $individual->is_moderator_left == '1'){
                                                        echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                                                    }
                                                    else{
                                                    ?>   
                                                    <a  href="javascript:void(0);"  id="lec_link" data-meeting_id ="<?php echo $individual->meeting_id ; ?>" data-user_type ="<?php echo "Tutor" ; ?>" data-user_name ="<?php echo $individual->instructor->first_name.' '.$individual->instructor->last_name ; ?>"  data-user_id ="<?php echo $individual->instructor_id ; ?>"   class="btn btn-info m-1 create_zoom_meeting_link py-1 px-2" style="background-color:#499619;color: #fff" target='_blank'>Start Class  </a>
                                                    <?php 
                                                    }
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
                                                    if ($individual->status == "Completed" && $individual->meeting_id != null) {
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } else if ($individual->status == "Missed" && $individual->meeting_id != null) {
                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                    } else { ?>
                                                          <a  href="javascript:void(0);" data-tutor_uri ="<?php echo $individual->google_meet_link ; ?>" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>"  class='btn btn-info m-1 start_meet_individual py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                    <?php }
                                                }


                                                else if($curtime == $lsnendtime && $individual->class->learning_tool == 'Zoom')
                                                {
                                                    if(($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'Zoom' && ($individual->is_tutor_joined == '1' ||$individual->is_tutor_joined == "1" || $individual->is_tutor_joined == 1 || $individual->is_tutor_joined)){
                                                        updateLessonStatusCompleteZoom($individual->id,$individual->meeting_id,null);
                                                        echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                        ?>   
                                                    <?php 
                                                    }
                                                    else if(($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'Zoom' && $individual->is_tutor_joined == '0'){
                                                        updateLessonStatusMissedZoom($individual->id,$individual->meeting_id,null);
                                                        echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>";  
                                                    ?>   
                                                <?php 
                                                }
                                                    else{
                                                        if($individual->status == "Completed"){
                                                            echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                        }
                                                        else{
                                                            echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>";     
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

                                                else if($curtime > $lsnendtime)
                                                {
                                                    if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Zoom' && $individual->is_tutor_joined == '1' || $individual->is_tutor_joined == "1" || $individual->is_tutor_joined == 1 || $individual->is_tutor_joined)
                                                    {	
                                                        updateLessonStatusCompleteZoom($individual->id,$individual->meeting_id,null);
                                                        echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                    }
                                                    else if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'BBB' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {
                                                        updateLessonStatusComplete($individual->id, $individual->meeting_id, null);
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } 
                                                    else if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Google_Meet' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {
                                                        updateLessonStatusComplete($individual->id, $individual->meeting_id, null);
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } 
                                                    else if($individual->status == "Completed"){
                                                        echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                    }
                                                    else
                                                    {
                                                        if($individual->status == "Missed"){
                                                            if(!empty($individual->meeting_id)){
                                                                $res = check_missed_status($individual->meeting_id);
                                                                if($res == 'Completed'){
                                                                    echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                                }
                                                                else{
                        
                                                                    echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>";    
                                                                }  
                                                            }
                                                            else{
                                                            echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>";    
                                                            }
                                                        }
                                                        else if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == "Zoom" && $individual->is_tutor_joined == '0'){
                                                            updateLessonStatusMissedZoom($individual->id,$individual->meeting_id,null);
                                                        
                                                            echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>";  
                                                            ?>
                                                            
                                                                                            
                                                        <?php }
                                                         else if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == "BBB" && $individual->is_instructor_joined == '0') {
                                                             updateLessonStatusMissed($individual->id, $individual->meeting_id, null);
                                                             echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                         }
                                                         else if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == "Google_Meet" && $individual->is_instructor_joined == '0') {
                                                             updateLessonStatusMissed($individual->id, $individual->meeting_id, null);
                                                             echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                         }
                                                        else{
                                                        echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>";    
                                                        }
                                                    }
                                                }
                                            }
                                            else if($curdate > $lsndate)
                                            {
                                                if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Zoom' && $individual->is_tutor_joined == '1' || $individual->is_tutor_joined == "1" || $individual->is_tutor_joined == 1 || $individual->is_tutor_joined)
                                                {	
                                                    updateLessonStatusCompleteZoom($individual->id,$individual->meeting_id,null);
                                                    echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                }
                                                else if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'BBB' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {
                                                    updateLessonStatusComplete($individual->id, $individual->meeting_id, null);
                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                } 
                                                else if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Google_Meet' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined) {
                                                    updateLessonStatusComplete($individual->id, $individual->meeting_id, null);
                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                } 
                                                else if($individual->status == "Completed")
                                                {
                                                    echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                }
                                                else if(($individual->status == "Pending" || $individual->status == "Scheduled")){
                                                    
                                                    echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>";    
                                                }
                                                else
                                                {
                                                        if($individual->status == "Missed"){
                        
                                                            if(!empty($individual->meeting_id)){
                        
                                                                $res = check_missed_status($individual->meeting_id);
                                                                if($res == 'Completed'){
                                                                    echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                                }
                                                                // elseif($res == "Missed"){
                                                                else{
                        
                                                                    echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>";    
                                                                }  
                                                            }
                                                            else{
                                                                echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>";    
                                                            }
                        
                                                        }
                                                }
                                            }
                                            else{
                                                if($individual->status == "Completed")
                                                {  
                                                    echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                }
                                                else
                                                {
                                                        if($individual->status == "Missed"){
                        
                                                            if(!empty($individual->meeting_id)){
                        
                                                                $res = check_missed_status($individual->meeting_id);
                                                                if($res == 'Completed'){
                                                                    echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                                }  
                                                                // elseif($res == "Missed"){
                                                                else{
                        
                                                                    echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>";    
                                                                }
                                                            }
                                                            else{
                                                                echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>";    
                                                            }
                        
                                                        }
                                                }
                                            }?>
                                        </td>
                                        <td>
                                            <div class="table-data-with-img d-flex align-items-center">
                                                {{-- <div class="flex-grow-1 table-data-course-name">{{ Str::limit($individual->course->title, 50) }}</div> --}}
                                                <div class="flex-grow-1 table-data-course-name">{{ Str::limit($individual->course ? $individual->course->title : null , 50) }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="table-data-with-img d-flex align-items-center">
                                             
                                                <div class="flex-grow-1 table-data-course-name">{{ Str::limit($individual->class ? $individual->class->learning_tool : null , 50) }}</div>
                                            </div>
                                        </td>
                                        <td>{{ date('d-m-Y',strtotime($individual->class_date)).'  '.date("D", strtotime($individual->class_date)).'  '.$updated_time }}</td>
                                        {{-- <td>{{$individual->status }}</td> --}}
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- <div class="courses-cards">
                                @php
                                    $i = 0;
                                @endphp
                                @foreach($getIndividualClassesHistory as $individual)
                                <div class="course-card">
                                    <div class="font-15 color-heading">
                                        <strong>{{ __('Student Name') }}</strong>
                                        @if ($individual->student)
                                            <div class="table-data-with-img d-flex align-items-center">
                                                <div class="flex-grow-1 table-data-course-name">{{ Str::limit($individual->student->first_name.' '.$individual->student->last_name, 50) }}</div>
                                            </div> 
                                        @endif
                                    </div>
                                    <div class="font-15 color-heading">
                                        <strong>{{ __('Course') }}</strong>
                                        <div class="table-data-with-img d-flex align-items-center">
                                         
                                            <div class="flex-grow-1 table-data-course-name">{{ Str::limit($individual->course ? $individual->course->title : null , 50) }}</div>
                                        </div>
                                    </div>
                                    <div class="font-15 color-heading">
                                        <strong>{{ __('Date Time') }}</strong>
                                        {{ date('d-m-Y',strtotime($individual->class_date)).'  '.date("D", strtotime($individual->class_date)).'  '.$updated_time }}</div>
                                  
                                        <div class="font-15 color-heading">
                                        <strong>{{ __('Status') }}</strong>
                                        @php
                                            $updated_time = changetimeByZone($individual->class_time,$individual->instructor_id,'instructor');
                                            $lesson_time = $updated_time; 

                                                $curdate = strtotime(date("Y-m-d"));
                                                $lsndate = strtotime($individual->class_date); 
                                                $updated_cur_time = changetimeByZone(date("H:i"),$individual->instructor_id,'instructor');
                                                $curtime = strtotime($updated_cur_time);
                                                $lsntime = strtotime($lesson_time) ;
                                                $lesson_duration =$individual->class->duration;
                                                $lsntimestamp = strtotime($lesson_time) + $lesson_duration*60;
                                                $lsnendtime = $lsntimestamp ;
                                            
                                           
                                        @endphp
                                        
                                        <?php
                                            if($curdate <  $lsndate)
                                                    {?>
                                                    <span  style="border:none;font-weight:bolder;background-color:#322740;color:white;" class="btn btn-warning m-1 px-2 py-1">
                                                        Scheduled  
                                            </span>
                                            <?php }
                                        else if($curdate == $lsndate)
                                        {
                                        if($curtime < $lsntime)
                                            {
                                                $id = 'lec_link1_'.$individual->id;
                                                echo "<a class='btn btn-warning m-1 lec_link1 px-2 py-1' id='$id'>".'Waiting'."</a>";
                        
                                            }

                                            else if($curtime >= $lsntime && $curtime < $lsnendtime && $individual->meeting_id == NULL && $individual->class->learning_tool == 'Zoom')
                                            {
                                                $id = 'lec_link1_'.$individual->id;
                                                echo "<a class='btn btn-warning m-1 lec_link1 px-2 py-1' id='$id'>".'Waiting'."</a>";
                                            }
                                            else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Zoom')
                        
                                            { 
                                                ?>
                                                <a href="javascript:void(0);"  data-meeting_id ="<?php echo $individual->meeting_id ; ?>" data-user_type ="<?php echo "Tutor" ; ?>" data-user_name ="<?php echo $individual->instructor->first_name.' '.$individual->instructor->last_name ; ?>"  data-user_id ="<?php echo $individual->instructor_id ; ?>"  class="btn btn-info m-1 create_zoom_meeting_link py-1 px-2" style="background-color:#499619;color: #fff" target='_blank'>Start Class  </a>
                                            <?php
                                            }
                                                else if($curtime == $lsntime &&($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'Zoom')

                                            { 
                        
                                            if($individual->status == "Completed" && $individual->is_moderator_left == '1'){
                                                    echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                }
                                                else if($individual->status == "Missed" && $individual->is_moderator_left == '1'){
                                                    echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                                                }
                                                ?>
                                            <?php
                    
                
                                            }
                                            else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Zoom')
                                            { 
                                            
                                                ?>   
                                            <a  href="javascript:void(0);"  id="lec_link" data-meeting_id ="<?php echo $individual->meeting_id ; ?>" data-user_type ="<?php echo "Tutor" ; ?>" data-user_name ="<?php echo $individual->instructor->first_name.' '.$individual->instructor->last_name ; ?>"  data-user_id ="<?php echo $individual->instructor_id ; ?>"   class="btn btn-info m-1 create_zoom_meeting_link py-1 px-2" style="background-color:#499619;color: #fff" target='_blank'>Start Class  </a>
                                            <?php 
                                            }
                                            else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->class->learning_tool == 'Zoom')
                                            {
                                                if($individual->status == "Completed" && $individual->is_moderator_left == '1'){
                                                    echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                }
                                                else if($individual->status == "Missed" && $individual->is_moderator_left == '1'){
                                                    echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                                                }
                                                else{
                                                ?>   
                                                <a  href="javascript:void(0);"  id="lec_link" data-meeting_id ="<?php echo $individual->meeting_id ; ?>" data-user_type ="<?php echo "Tutor" ; ?>" data-user_name ="<?php echo $individual->instructor->first_name.' '.$individual->instructor->last_name ; ?>"  data-user_id ="<?php echo $individual->instructor_id ; ?>"   class="btn btn-info m-1 create_zoom_meeting_link py-1 px-2" style="background-color:#499619;color: #fff" target='_blank'>Start Class  </a>
                                                <?php 
                                                }
                                            }
                                            else if($curtime == $lsnendtime && $individual->class->learning_tool == 'Zoom')
                                            {
                                                if(($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'Zoom' && ($individual->is_tutor_joined == '1' ||$individual->is_tutor_joined == "1" || $individual->is_tutor_joined == 1 || $individual->is_tutor_joined)){
                                                    updateLessonStatusCompleteZoom($individual->id,$individual->meeting_id,null);
                                                    echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                    ?>   
                                                <?php 
                                                }
                                                else if(($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'Zoom' && $individual->is_tutor_joined == '0'){
                                                    updateLessonStatusMissedZoom($individual->id,$individual->meeting_id,null);
                                                    echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>";  
                                                ?>   
                                            <?php 
                                            }
                                                else{
                                                    if($individual->status == "Completed"){
                                                        echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                    }
                                                    else{
                                                        echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>";     
                                                    }
                                                }
                                            }
                                            else if($curtime > $lsnendtime)
                                            {
                                                if($individual->status == "Pending" && $individual->class->learning_tool == 'Zoom' && $individual->is_tutor_joined == '1' || $individual->is_tutor_joined == "1" || $individual->is_tutor_joined == 1 || $individual->is_tutor_joined)
                                                                {	
                                                                    updateLessonStatusCompleteZoom($individual->id,$individual->meeting_id,null);
                                                                    echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                                }
                                                else if($individual->status == "Completed"){
                                                    echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                }
                                                else
                                                {
                                                    if($individual->status == "Missed"){
                                                        if(!empty($individual->meeting_id)){
                                                            $res = check_missed_status($individual->meeting_id);
                                                            if($res == 'Completed'){
                                                                echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                            }
                                                            else{
                    
                                                                echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>";    
                                                            }  
                                                        }
                                                        else{
                                                        echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>";    
                                                        }
                                                    }
                                                    else if($individual->status == "Pending" && $individual->class->learning_tool == "Zoom" && $individual->is_tutor_joined == '0'){
                                                                updateLessonStatusMissedZoom($individual->id,$individual->meeting_id,null);
                                                            
                                                                echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>";  
                                                                ?>
                                                        
                                                                                        
                                                    <?php }
                                                    else{
                    
                                                    echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>";    
                                                    }
                                                }
                                            }
                                        }
                                        else if($curdate > $lsndate)
                                        {
                                            if($individual->status == "Pending" && $individual->class->learning_tool == 'Zoom' && $individual->is_tutor_joined == '1' || $individual->is_tutor_joined == "1" || $individual->is_tutor_joined == 1 || $individual->is_tutor_joined)
                                            {	
                                                updateLessonStatusCompleteZoom($individual->id,$individual->meeting_id,null);
                                                echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                            }
                                            else if($individual->status == "Completed")
                                            {
                                                echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                            }
                                            else if($individual->status == "Pending"){
                                                
                                                echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>";    
                                            }
                                            else
                                            {
                                                    if($individual->status == "Missed"){
                    
                                                        if(!empty($individual->meeting_id)){
                    
                                                            $res = check_missed_status($individual->meeting_id);
                                                            if($res == 'Completed'){
                                                                echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                            }
                                                            // elseif($res == "Missed"){
                                                            else{
                    
                                                                echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>";    
                                                            }  
                                                        }
                                                        else{
                                                            echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>";    
                                                        }
                    
                                                    }
                                            }
                                        }
                                        else{
                                            if($individual->status == "Completed")
                                            {  
                                                echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                            }
                                            else
                                            {
                                                    if($individual->status == "Missed"){
                    
                                                        if(!empty($individual->meeting_id)){
                    
                                                            $res = check_missed_status($individual->meeting_id);
                                                            if($res == 'Completed'){
                                                                echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                            }  
                                                        
                                                            else{
                    
                                                                echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>";    
                                                            }
                                                        }
                                                        else{
                                                            echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>";    
                                                        }
                    
                                                    }
                                            }
                                        }?>
                                    </div>
                                </div>
                            @endforeach
                        </div> --}}
                        <!-- Pagination Start -->
                        @if(@$getIndividualClassesHistory->hasPages())
                            {{ @$getIndividualClassesHistory->links('frontend.paginate.paginate') }}
                        @endif
                        <!-- Pagination End -->
                    @else
                        <!-- If there is no data Show Empty Design Start -->
                        <div class="empty-data">
                            <img src="{{ asset('frontend/assets/img/empty-data-img.png') }}" alt="img" class="img-fluid">
                            <h5 class="my-3">{{ __('No Individual Classes History Available') }}</h5>
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
                <h6>{{ __('Group Class History') }}</h6>
            </div>

            <div class="row">
                <div class="col-12">
                    @if(!$getGroupClassesHistory->isEmpty())
                        <div class="table-responsive table-responsive-xl courses-table">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">{{ __('No') }}</th>
                                    <th scope="col">{{ __('No. Of. Students') }}</th>
                                    <th scope="col">{{ __('Status') }}</th>
                                    <th scope="col">{{ __('Course') }}</th>
                                    <th scope="col">{{ __('Learning Tool') }}</th>
                                    <th scope="col">{{ __('Date Time') }}</th>
                               
                                    {{-- <th scope="col">{{ __('Action') }}</th> --}}
                                </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $j = 0;
                                @endphp
                                @foreach($getGroupClassesHistory as $group)
                                    <tr>
                                        <td>{{ ++$j }}</td>
                                        <td>
                                            <a class="total_students" class="btn" data-class_id ="<?php echo $group->class->id ; ?>" href="javascript::void(0);" data-toggle="modal" data-target="#showstudentnames">
                                            <div class="table-data-with-img d-flex align-items-center">
                                              <div class="flex-grow-1 table-data-course-name"><?php echo $group->class->no_of_students ; ?></div>
                                            </div>
                                            </a>&nbsp;
                                        </td>
                                        <td>
                                            @php
                                                $updated_time = changetimeByZone($group->class_time,$group->instructor_id,'instructor');
                                                $lesson_time = $updated_time; 

                                                $curdate = strtotime(date("d-m-Y"));
                                                $lsndate = strtotime($group->class_date); 
                                                $updated_cur_time = changetimeByZone(date("H:i"),$group->instructor_id,'instructor');
                                                $curtime = strtotime($updated_cur_time);
                                                $lsntime = strtotime($lesson_time) ;
                                                $lesson_duration =$group->class->duration;
                                                $lsntimestamp = strtotime($lesson_time) + $lesson_duration*60;
                                                $lsnendtime = $lsntimestamp ;
                                            
                                                
                                            @endphp

                                            <?php
                                            if($curdate <  $lsndate)
                                                {?>
                                                <span  style="border:none;font-weight:bolder;background-color:#322740;color:white;" class="btn btn-warning m-1 px-2 py-1">
                                                    Scheduled  
                                            </span>
                                            <?php }
                                            else if($curdate == $lsndate)
                                            {
                                                            if($curtime < $lsntime)
                                                                {
                                                                    $id = 'lec_link2_'.$group->group_id;
                                                                    echo "<a class='btn btn-warning m-1 lec_link2 px-2 py-1' id='$id'>".'Waiting'."</a>";
                                                                   
                                                                }
                                                            else if($curtime >= $lsntime && $curtime < $lsnendtime  && $group->meeting_id == NULL && $group->class->learning_tool == 'Zoom')
                                                                {
                                                                    $id = 'lec_link2_'.$group->group_id;
                                                                    echo "<a class='btn btn-warning m-1 lec_link2 px-2 py-1' id='$id'>".'Waiting'."</a>";
                                                                }
                                                            else if ($curtime >= $lsntime && $curtime < $lsnendtime && $group->InstructorURI == NULL && $group->class->learning_tool == 'BBB') {
                                                                 $id = 'lec_link2_' . $group->group_id;
                                                                 echo "<a class='btn btn-warning m-1 lec_link2 px-2 py-1 small' id='$id'>" . 'Waiting' . "</a>";
                                                            }
                                                            else if ($curtime >= $lsntime && $curtime < $lsnendtime && $group->meeting_id == NULL && $group->class->learning_tool == 'Google_Meet') {
                                                                 $id = 'lec_link2_' . $group->group_id;
                                                                 echo "<a class='btn btn-warning m-1 lec_link2 px-2 py-1 small' id='$id'>" . 'Waiting' . "</a>";
                                                            }

                                                            else if($curtime == $lsntime&& $group->status != "Completed"&& $group->status != "Missed" && $group->class->learning_tool == 'Zoom')
                                                                { ?>
                                                                    <a href="javascript:void(0);"  id="lec_lnk" data-group_id ="<?php echo $group->group_id ; ?>" data-meeting_id ="<?php echo $group->meeting_id ; ?>" data-user_type ="<?php echo "Tutor" ; ?>" data-user_name ="<?php echo $group->instructor->first_name.' '.$group->instructor->last_name ; ?>"  data-user_id ="<?php echo $group->instructor_id ; ?>"    class="btn btn-info m-1 create_zoom_meeting_link_2 py-1 px-2" style="background-color:#499619;color: #fff" target='_blank'>Start Class  </a>
                                                                    <?php
                                                                
                                                                }
                                                                else if ($curtime == $lsntime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'BBB') { ?>
                                                                     <a  href="javascript:void(0);" data-group_id ="<?php echo $group->group_id ; ?>" data-tutor_uri ="<?php echo $group->InstructorURI ; ?>" data-lesson_id ="<?php echo $group->id ; ?>" data-meeting_id ="<?php echo $group->meeting_id ; ?>"  class='btn btn-info m-1 start_bbb_group py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>                         
                                                                <?php } 
                                                                  else if ($curtime == $lsntime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'Google_Meet') { ?>
                                                                     <a  href="javascript:void(0);" data-group_id ="<?php echo $group->group_id ; ?>" data-tutor_uri ="<?php echo $group->google_meet_link ; ?>" data-lesson_id ="<?php echo $group->id ; ?>" data-meeting_id ="<?php echo $group->meeting_id ; ?>"  class='btn btn-info m-1 start_meet_group py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>                         
                                                                <?php } 

                                                            else if($curtime > $lsntime && $curtime < $lsnendtime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'Zoom')
                                                                {  
                                                                        ?>
                                                                        <a href="javascript:void(0);"  id="lec_lnk" data-group_id ="<?php echo $group->group_id ; ?>" data-meeting_id ="<?php echo $group->meeting_id ; ?>" data-user_type ="<?php echo "Tutor" ; ?>" data-user_name ="<?php echo $group->instructor->first_name.' '.$group->instructor->last_name ; ?>"  data-user_id ="<?php echo $group->instructor_id ; ?>"   class="btn btn-info m-1 create_zoom_meeting_link_2 py-1 px-2" style="background-color:#499619;color: #fff" target='_blank'>Start Class  </a>
                                                                    <?php 
                                                                }
                                                            else if ($curtime > $lsntime && $curtime < $lsnendtime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'BBB') { ?>
                                                                 <a  href="javascript:void(0);" data-group_id ="<?php echo $group->group_id ; ?>" data-tutor_uri ="<?php echo $group->InstructorURI ; ?>" data-lesson_id ="<?php echo $group->id ; ?>" data-meeting_id ="<?php echo $group->meeting_id ; ?>"  class='btn btn-info m-1 start_bbb_group py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>                         
                                                            <?php } 
                                                              else if ($curtime > $lsntime && $curtime < $lsnendtime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'Google_Meet') { ?>
                                                                 <a  href="javascript:void(0);" data-group_id ="<?php echo $group->group_id ; ?>" data-tutor_uri ="<?php echo $group->google_meet_link ; ?>" data-lesson_id ="<?php echo $group->id ; ?>" data-meeting_id ="<?php echo $group->meeting_id ; ?>"  class='btn btn-info m-1 start_meet_group py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>                         
                                                            <?php } 

                                                            else if($curtime > $lsntime && $curtime < $lsnendtime && $group->class->learning_tool == 'Zoom')
                                                                {
                                                                        if($group->status == "Completed" && $group->is_moderator_left == '1'){
                                                                            echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                                        }
                                                                        else if($group->status == "Missed" && $group->is_moderator_left == '1'){
                                                                            echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                                                                        }
                                                                        else{
                                                                            ?>   
                                                                        <a href="javascript:void(0);"  id="lec_lnk" data-group_id ="<?php echo $group->group_id ; ?>" data-meeting_id ="<?php echo $group->meeting_id ; ?>" data-user_type ="<?php echo "Tutor" ; ?>" data-user_name ="<?php echo $group->instructor->first_name.' '.$group->instructor->last_name ; ?>"  data-user_id ="<?php echo $group->instructor_id ; ?>"   class="btn btn-info m-1 create_zoom_meeting_link_2 py-1 px-2" style="background-color:#499619;color: #fff" target='_blank'>Start Class  </a>
                                                                        <?php 
                                                                        }
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
                                                                    if ($group->status == "Completed" && $group->meeting_id != null) {
                                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                                    } else if ($group->status == "Missed" && $group->meeting_id != null) {
                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                                    } else { ?>
                                                                        <a  href="javascript:void(0);" data-group_id ="<?php echo $group->group_id ; ?>" data-tutor_uri ="<?php echo $group->google_meet_link ; ?>" data-lesson_id ="<?php echo $group->id ; ?>" data-meeting_id ="<?php echo $group->meeting_id ; ?>"  class='btn btn-info m-1 start_meet_group py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>                         
                                                                    <?php }
                                                                }

                                                            else if($curtime == $lsnendtime && $group->class->learning_tool == 'Zoom')
                                                                {
                                                                        if(($group->status != "Completed" && $group->status != "Missed") && $group->class->learning_tool == 'Zoom' && ($group->is_tutor_joined == '1' || $group->is_tutor_joined == "1" || $group->is_tutor_joined == 1 || $group->is_tutor_joined)){
                                                                            updateLessonStatusCompleteZoom($group->id,$group->meeting_id,$group->group_id);
                                                                            echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                                            ?>   
                                                                        <?php 
                                                                        }
                                                                        if(($group->status != "Completed" && $group->status != "Missed") && $group->class->learning_tool == 'Zoom' && $group->is_tutor_joined == '0'){
                                                                            updateLessonStatusMissedZoom($group->id,$group->meeting_id,$group->group_id);
                                                                            echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                                                                            ?>   
                                                                        <?php 
                                                                        }
                                                                        else
                                                                        {
                                                                            if($group->status == "Completed"){
                                                                                echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                                            }
                                                                            else{
                                                                                echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                                                                                    
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

                                                                else if($curtime > $lsnendtime)
                                                                {
                                                                        if($group->class->learning_tool == 'Zoom' && $group->is_tutor_joined == '1' || $group->is_tutor_joined == "1" || $group->is_tutor_joined == 1 || $group->is_tutor_joined)
                                                                        {	
                                                                            updateLessonStatusCompleteZoom($group->id,$group->meeting_id,$group->group_id);
                                                                            echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                                        }
                                                                        else if ($group->class->learning_tool == 'BBB' && $group->is_instructor_joined == '1' || $group->is_instructor_joined == "1" || $group->is_instructor_joined == 1 || $group->is_instructor_joined) {
                                                                            updateLessonStatusComplete($group->id, $group->meeting_id, $group->group_id);
                                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                                        } 
                                                                        else if ($group->class->learning_tool == 'Google_Meet' && $group->is_instructor_joined == '1' || $group->is_instructor_joined == "1" || $group->is_instructor_joined == 1 || $group->is_instructor_joined) {
                                                                            updateLessonStatusComplete($group->id, $group->meeting_id, $group->group_id);
                                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                                        } 
                                                                        else if($group->status == "Completed"){
                                                                            echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                                        }
                                                                        else
                                                                        {
                                                                            if($group->status == "Missed"){
                                                                                if(!empty($group->meeting_id)){
                                                                                    $res = check_missed_status($group->meeting_id);
                                                                                    if($res == 'Completed'){
                                                                                        echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                                                    }
                                                                                    else{

                                                                                        echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                                                                                    }   
                                                                                }
                                                                                else{
                                                                                    echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                                                                                }
                                                                            }
                                                                            else if(($group->status == "Pending" || $group->status == "Scheduled") && $group->class->learning_tool == "Zoom" && $group->is_tutor_joined == '0'){
                                                                                            updateLessonStatusMissedZoom($group->id,$group->meeting_id,$group->group_id);
                                                                                            echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>";  
                                                                                            ?>                                        
                                                                            <?php }
                                                                            else if (($group->status == "Pending" || $group->status == "Scheduled") && $group->class->learning_tool == "BBB" && $group->is_instructor_joined == '0') {
                                                                                updateLessonStatusMissed($group->id, $group->meeting_id, $group->group_id);
                                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                                            } 
                                                                            else if (($group->status == "Pending" || $group->status == "Scheduled") && $group->class->learning_tool == "Google_Meet" && $group->is_instructor_joined == '0') {
                                                                                updateLessonStatusMissed($group->id, $group->meeting_id, $group->group_id);
                                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                                            } 
                                                                            else{
                                                                                    echo "<span class='lesson-status px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>";
                                                                            }
                                                                        }
                                                                }
                                            }
                                            else if($curdate > $lsndate)
                                            {
                                                                if($group->class->learning_tool == 'Zoom' && $group->is_tutor_joined == '1' || $group->is_tutor_joined == "1" || $group->is_tutor_joined == 1 || $group->is_tutor_joined)
                                                                    {	
                                                                        updateLessonStatusCompleteZoom($group->id,$group->meeting_id,$group->group_id);
                                                                            echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                                    }
                                                                else if ($group->class->learning_tool == 'BBB' && $group->is_instructor_joined == '1' || $group->is_instructor_joined == "1" || $group->is_instructor_joined == 1 || $group->is_instructor_joined) {
                                                                    updateLessonStatusComplete($group->id, $group->meeting_id, $group->group_id);
                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                                }
                                                                else if ($group->class->learning_tool == 'Google_Meet' && $group->is_instructor_joined == '1' || $group->is_instructor_joined == "1" || $group->is_instructor_joined == 1 || $group->is_instructor_joined) {
                                                                    updateLessonStatusComplete($group->id, $group->meeting_id, $group->group_id);
                                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                                }
                                                                else if($group->status == "Completed")
                                                                    {
                                                                        echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                                    }
                                                                else if(($group->status == "Pending" || $group->status == "Scheduled")){

                                                                        echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                                                                    }
                                                                else
                                                                    {
                                                                        if($group->status == "Missed"){

                                                                                if(!empty($group->meeting_id)){

                                                                                    $res = check_missed_status($group->meeting_id);
                                                                                    if($res == 'Completed'){
                                                                                        echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                                                    }
                                                                                    else{

                                                                                        echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                                                                                    }   
                                                                                }
                                                                                else{
                                                                                    echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                                                                                }

                                                                            }
                                                                            else{
                                                                                echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                                            }
                                                                    }
                                            }
                                            else
                                            {
                                                                if($group->status == "Completed")
                                                                    {   
                                                                        echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                                    }
                                                                else
                                                                    {
                                                                            if($group->status == "Missed"){

                                                                                if(!empty($group->meeting_id)){

                                                                                    $res = check_missed_status($group->meeting_id);
                                                                                    if($res == 'Completed'){
                                                                                        echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                                                    }   
                                                                                    else{

                                                                                        echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                                                                                    }
                                                                                }
                                                                                else{
                                                                                    echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                                                                                }

                                                                            }
                                                                            else{
                                                                                echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";    
                                                                            }
                                                                    }
                                            }
                                            ?>
                                         </td>


                                        <td>
                                            <div class="table-data-with-img d-flex align-items-center">
                                                <div class="flex-grow-1 table-data-course-name">{{ Str::limit($group->course? $group->course->title : null, 50) }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="table-data-with-img d-flex align-items-center">
                                                <div class="flex-grow-1 table-data-course-name">{{ Str::limit($group->class? $group->class->learning_tool : null, 50) }}</div>
                                            </div>
                                        </td>
                                        {{-- <td>{{ date('d-m-Y',strtotime($group->class_date)).' 
                                        '.$updated_time }}</td> --}}
                                        
                                        <td>{{ date('d-m-Y',strtotime($group->class_date)).'  '.date("D", strtotime($group->class_date)).'  '.$updated_time }}</td>
                                    
                                        {{-- <td>
                                            <div class="notice-board-action-btns">
                                                <a href="{{ route('live-class.create', [$course->uuid]) }}" class="theme-btn theme-button1 default-hover-btn">{{ __('Create Live Class') }}</a>
                                                <a href="{{ route('live-class.index', [$course->uuid]) }}" class="theme-btn theme-button1 green-theme-btn default-hover-btn">{{ __('View') }}
                                                    List</a>
                                            </div>
                                        </td> --}}
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="courses-cards">
                                    @php
                                    $j = 0;
                                @endphp
                                @foreach($getGroupClassesHistory as $group)
                                    <div class="course-card">
                                        <div class="font-15 color-heading">
                                            <strong>{{ __('No. Of. Students') }}</strong>
                                            <a class="total_students" class="btn" data-class_id ="<?php echo $group->class->id ; ?>" href="javascript::void(0);" data-toggle="modal" data-target="#showstudentnames">
                                            <div class="table-data-with-img d-flex align-items-center">
                                              <div class="flex-grow-1 table-data-course-name"><?php echo $group->class->no_of_students ; ?></div>
                                            </div>
                                            </a>&nbsp;
                                        </div>
                                        <div class="font-15 color-heading">
                                            <strong>{{ __('Course') }}</strong>
                                            <div class="table-data-with-img d-flex align-items-center">
                                                <div class="flex-grow-1 table-data-course-name">{{ Str::limit($group->course? $group->course->title : null, 50) }}</div>
                                            </div>
                                        </div>
                                        {{-- <td>{{ date('d-m-Y',strtotime($group->class_date)).' 
                                        '.$updated_time }}</td> --}}
                                        
                                        <div class="font-15 color-heading">
                                            <strong>{{ __('Date Time') }}</strong>
                                            {{ date('d-m-Y',strtotime($group->class_date)).'  '.date("D", strtotime($group->class_date)).'  '.$updated_time }}
                                        </div>
                                    
                                        {{-- <td>
                                            <div class="notice-board-action-btns">
                                                <a href="{{ route('live-class.create', [$course->uuid]) }}" class="theme-btn theme-button1 default-hover-btn">{{ __('Create Live Class') }}</a>
                                                <a href="{{ route('live-class.index', [$course->uuid]) }}" class="theme-btn theme-button1 green-theme-btn default-hover-btn">{{ __('View') }}
                                                    List</a>
                                            </div>
                                        </td> --}}
                                        <div class="font-15 color-heading">
                                            <strong>{{ __('Status') }}</strong>
                                            @php
                                                $updated_time = changetimeByZone($group->class_time,$group->instructor_id,'instructor');
                                                $lesson_time = $updated_time; 

                                                $curdate = strtotime(date("d-m-Y"));
                                                $lsndate = strtotime($group->class_date); 
                                                $updated_cur_time = changetimeByZone(date("H:i"),$group->instructor_id,'instructor');
                                                $curtime = strtotime($updated_cur_time);
                                                $lsntime = strtotime($lesson_time) ;
                                                $lesson_duration =$group->class->duration;
                                                $lsntimestamp = strtotime($lesson_time) + $lesson_duration*60;
                                                $lsnendtime = $lsntimestamp ;
                                            
                                                
                                            @endphp

                                            <?php
                                            if($curdate <  $lsndate)
                                                {?>
                                                <span  style="border:none;font-weight:bolder;background-color:#322740;color:white;" class="btn btn-warning m-1 px-2 py-1">
                                                    Scheduled  
                                            </span>
                                            <?php }
                                            else if($curdate == $lsndate)
                                            {
                                                            if($curtime < $lsntime)
                                                                {
                                                                    $id = 'lec_link2_'.$group->group_id;
                                                                    echo "<a class='btn btn-warning m-1 lec_link2 py-1 px-2' id='$id'>".'Waiting'."</a>";
                                                                    //echo "Waiting";
                                                                }
                                                            else if($curtime >= $lsntime && $curtime < $lsnendtime  && $group->meeting_id == NULL && $group->class->learning_tool == 'Zoom')
                                                                {
                                                                    $id = 'lec_link2_'.$group->group_id;
                                                                    echo "<a class='btn btn-warning m-1 lec_link2 py-1 px-2' id='$id'>".'Waiting'."</a>";
                                                                }
                                                                else if ($curtime >= $lsntime && $curtime < $lsnendtime && $group->InstructorURI == NULL && $group->class->learning_tool == 'BBB') {
                                                                    $id = 'lec_link2_' . $group->group_id;
                                                                    echo "<a class='btn btn-warning m-1 lec_link2 px-2 py-1 small' id='$id'>" . 'Waiting' . "</a>";
                                                                }
                                                            else if($curtime == $lsntime&& $group->status != "Completed"&& $group->status != "Missed" && $group->class->learning_tool == 'Zoom')
                                                                { ?>
                                                                    <a href="javascript:void(0);"  id="lec_lnk" data-group_id ="<?php echo $group->group_id ; ?>" data-meeting_id ="<?php echo $group->meeting_id ; ?>" data-user_type ="<?php echo "Tutor" ; ?>" data-user_name ="<?php echo $group->instructor->first_name.' '.$group->instructor->last_name ; ?>"  data-user_id ="<?php echo $group->instructor_id ; ?>"    class="btn btn-info m-1 create_zoom_meeting_link_2 py-1 px-2" style="background-color:#499619;color: #fff" target='_blank'>Start Class  </a>
                                                                    <?php
                                                                
                                                                }
                                                                else if ($curtime == $lsntime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'BBB') { ?>
                                                                     <a  href="javascript:void(0);" data-group_id ="<?php echo $group->group_id ; ?>" data-tutor_uri ="<?php echo $group->InstructorURI ; ?>" data-lesson_id ="<?php echo $group->id ; ?>" data-meeting_id ="<?php echo $group->meeting_id ; ?>"  class='btn btn-info m-1 start_bbb_group py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>                         
                                                                <?php } 
                                                            else if($curtime > $lsntime && $curtime < $lsnendtime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'Zoom')
                                                                {  
                                                                        ?>
                                                                        <a href="javascript:void(0);"  id="lec_lnk" data-group_id ="<?php echo $group->group_id ; ?>" data-meeting_id ="<?php echo $group->meeting_id ; ?>" data-user_type ="<?php echo "Tutor" ; ?>" data-user_name ="<?php echo $group->instructor->first_name.' '.$group->instructor->last_name ; ?>"  data-user_id ="<?php echo $group->instructor_id ; ?>"   class="btn btn-info m-1 create_zoom_meeting_link_2 py-1 px-2" style="background-color:#499619;color: #fff" target='_blank'>Start Class  </a>
                                                                    <?php 
                                                                }
                                                                else if ($curtime > $lsntime && $curtime < $lsnendtime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'BBB') { ?>
                                                                     <a  href="javascript:void(0);" data-group_id ="<?php echo $group->group_id ; ?>" data-tutor_uri ="<?php echo $group->InstructorURI ; ?>" data-lesson_id ="<?php echo $group->id ; ?>" data-meeting_id ="<?php echo $group->meeting_id ; ?>"  class='btn btn-info m-1 start_bbb_group py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>                         
                                                                <?php } 
                                                            else if($curtime > $lsntime && $curtime < $lsnendtime && $group->class->learning_tool == 'Zoom')
                                                                {
                                                                        if($group->status == "Completed" && $group->is_moderator_left == '1'){
                                                                            echo "<span class='btn btn-success m-1 px-2 py-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                                        }
                                                                        else if($group->status == "Missed" && $group->is_moderator_left == '1'){
                                                                            echo "<span class='btn btn-danger m-1 px-2 py-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                                                                        }
                                                                        else{
                                                                            ?>   
                                                                        <a href="javascript:void(0);"  id="lec_lnk" data-group_id ="<?php echo $group->group_id ; ?>" data-meeting_id ="<?php echo $group->meeting_id ; ?>" data-user_type ="<?php echo "Tutor" ; ?>" data-user_name ="<?php echo $group->instructor->first_name.' '.$group->instructor->last_name ; ?>"  data-user_id ="<?php echo $group->instructor_id ; ?>"   class="btn btn-info m-1 create_zoom_meeting_link_2 py-1 px-2" style="background-color:#499619;color: #fff" target='_blank'>Start Class  </a>
                                                                        <?php 
                                                                        }
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
                                                            else if($curtime == $lsnendtime && $group->class->learning_tool == 'Zoom')
                                                                {
                                                                        if(($group->status != "Completed" && $group->status != "Missed") && $group->class->learning_tool == 'Zoom' && ($group->is_tutor_joined == '1' || $group->is_tutor_joined == "1" || $group->is_tutor_joined == 1 || $group->is_tutor_joined)){
                                                                            updateLessonStatusCompleteZoom($group->id,$group->meeting_id,$group->group_id);
                                                                            echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;'>".'Completed'."</span>";
                                                                            ?>   
                                                                        <?php 
                                                                        }
                                                                        if(($group->status != "Completed" && $group->status != "Missed") && $group->class->learning_tool == 'Zoom' && $group->is_tutor_joined == '0'){
                                                                            updateLessonStatusMissedZoom($group->id,$group->meeting_id,$group->group_id);
                                                                            echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;'>".'Missed'."</span>"; 
                                                                            ?>   
                                                                        <?php 
                                                                        }
                                                                        else
                                                                        {
                                                                            if($group->status == "Completed"){
                                                                                echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;'>".'Completed'."</span>";
                                                                            }
                                                                            else{
                                                                                echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;'>".'Missed'."</span>"; 
                                                                                    
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
                                                                else if($curtime > $lsnendtime)
                                                                {
                                                                        if($group->class->learning_tool == 'Zoom' && $group->is_tutor_joined == '1' || $group->is_tutor_joined == "1" || $group->is_tutor_joined == 1 || $group->is_tutor_joined)
                                                                        {	
                                                                                updateLessonStatusCompleteZoom($group->id,$group->meeting_id,$group->group_id);
                                                                                echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;'>".'Completed'."</span>";
                                                                        }
                                                                        else if ($group->class->learning_tool == 'BBB' && $group->is_instructor_joined == '1' || $group->is_instructor_joined == "1" || $group->is_instructor_joined == 1 || $group->is_instructor_joined) {
                                                                            updateLessonStatusComplete($group->id, $group->meeting_id, $group->group_id);
                                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                                        } 
                                                                        else if($group->status == "Completed"){
                                                                            echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;'>".'Completed'."</span>";
                                                                        }
                                                                        else
                                                                        {
                                                                            if($group->status == "Missed"){
                                                                                if(!empty($group->meeting_id)){
                                                                                    $res = check_missed_status($group->meeting_id);
                                                                                    if($res == 'Completed'){
                                                                                        echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;'>".'Completed'."</span>";
                                                                                    }
                                                                                    else{

                                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;'>".'Missed'."</span>"; 
                                                                                    }   
                                                                                }
                                                                                else{
                                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;'>".'Missed'."</span>"; 
                                                                                }
                                                                            }
                                                                            else if(($group->status == "Pending" || $group->status == "Scheduled") && $group->class->learning_tool == "Zoom" && $group->is_tutor_joined == '0'){
                                                                                            updateLessonStatusMissedZoom($group->id,$group->meeting_id,$group->group_id);
                                                                                            echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;'>".'Missed'."</span>";  
                                                                                            ?>                                        
                                                                            <?php }
                                                                            else if (($group->status == "Pending" || $group->status == "Scheduled") && $group->class->learning_tool == "BBB" && $group->is_instructor_joined == '0') {
                                                                                updateLessonStatusMissed($group->id, $group->meeting_id, $group->group_id);
                                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>" . 'Missed' . "</span>";
                                                                            } 
                                                                            else{
                                                                                    echo "<span class='lesson-status py-1 px-2' style='background-color:#F00;'>".'Missed'."</span>";
                                                                            }
                                                                        }
                                                                }
                                            }
                                            else if($curdate > $lsndate)
                                            {
                                                                if($group->class->learning_tool == 'Zoom' && $group->is_tutor_joined == '1' || $group->is_tutor_joined == "1" || $group->is_tutor_joined == 1 || $group->is_tutor_joined)
                                                                    {	
                                                                        updateLessonStatusCompleteZoom($group->id,$group->meeting_id,$group->group_id);
                                                                            echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;'>".'Completed'."</span>";
                                                                    }
                                                                    else if ($group->class->learning_tool == 'BBB' && $group->is_instructor_joined == '1' || $group->is_instructor_joined == "1" || $group->is_instructor_joined == 1 || $group->is_instructor_joined) {
                                                                        updateLessonStatusComplete($group->id, $group->meeting_id, $group->group_id);
                                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                                    }
                                                                else if($group->status == "Completed")
                                                                    {
                                                                        echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;'>".'Completed'."</span>";
                                                                    }
                                                                else if(($group->status == "Pending" || $group->status == "Scheduled")){

                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;'>".'Missed'."</span>"; 
                                                                    }
                                                                else
                                                                    {
                                                                        if($group->status == "Missed"){

                                                                                if(!empty($group->meeting_id)){

                                                                                    $res = check_missed_status($group->meeting_id);
                                                                                    if($res == 'Completed'){
                                                                                        echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;'>".'Completed'."</span>";
                                                                                    }
                                                                                    else{

                                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;'>".'Missed'."</span>"; 
                                                                                    }   
                                                                                }
                                                                                else{
                                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;'>".'Missed'."</span>"; 
                                                                                }

                                                                            }
                                                                            else{
                                                                                echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;'>".'Completed'."</span>";
                                                                            }
                                                                    }
                                            }
                                            else
                                            {
                                                                if($group->status == "Completed")
                                                                    {   
                                                                        echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                                    }
                                                                else
                                                                    {
                                                                            if($group->status == "Missed"){

                                                                                if(!empty($group->meeting_id)){

                                                                                    $res = check_missed_status($group->meeting_id);
                                                                                    if($res == 'Completed'){
                                                                                        echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;'>".'Completed'."</span>";
                                                                                    }   
                                                                                    else{

                                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;'>".'Missed'."</span>"; 
                                                                                    }
                                                                                }
                                                                                else{
                                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;'>".'Missed'."</span>"; 
                                                                                }

                                                                            }
                                                                            else{
                                                                                echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;'>".'Completed'."</span>";    
                                                                            }
                                                                    }
                                            }
                                            ?>
                                         </div>


                                        
                                    </div>
                                @endforeach
                        </div>
                        <!-- Pagination Start -->
                        @if(@$getGroupClassesHistory->hasPages())
                            {{ @$getGroupClassesHistory->links('frontend.paginate.paginate') }}
                        @endif
                        <!-- Pagination End -->
                    @else
                        <!-- If there is no data Show Empty Design Start -->
                        <div class="empty-data">
                            <img src="{{ asset('frontend/assets/img/empty-data-img.png') }}" alt="img" class="img-fluid">
                            <h5 class="my-3">{{ __('No Group Classes History Available') }}</h5>
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
                <div id="student_names_box" class="modal-body">
               

                </div>
           
            </div>
        </div>
    </div>

  
@endsection

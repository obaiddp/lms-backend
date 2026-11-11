<div class="tab-pane fade" id="LiveClass" role="tabpanel" aria-labelledby="LiveClass-tab">
    <div class="row">
        <div class="col-12">
            <div class="after-purchase-course-watch-tab bg-white p-30">
                <!-- If there is no data Show Empty Design Start -->
                <div class="empty-data d-none">
                    <img src="{{ asset('frontend/assets/img/empty-data-img.png') }}" alt="img" class="img-fluid">
                    <h5 class="my-3">{{ __('Empty Live Class') }} </h5>
                </div>
                <!-- If there is no data Show Empty Design End -->

                <div class="course-watch-live-class-wrap instructor-quiz-list-page">
                    <ul class="nav nav-tabs assignment-nav-tabs live-class-list-nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button" role="tab"
                                    aria-controls="upcoming" aria-selected="true">{{ __('Upcoming') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="current-tab" data-bs-toggle="tab" data-bs-target="#current" type="button" role="tab"
                                    aria-controls="current" aria-selected="false">{{ __('Current') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="past-tab" data-bs-toggle="tab" data-bs-target="#past" type="button" role="tab"
                                    aria-controls="past" aria-selected="false">{{ __('Past') }}
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content live-class-list" id="myTabContent">
                        <div class="tab-pane fade show active" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
                            @if(count($upcoming_live_classes))
                            <div class="table-responsive table-responsive-xl">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">{{ __('Date & Time') }}</th>
                                        <th scope="col">{{ __('Time Duration') }}</th>
                                        <th scope="col">{{ __('Topic') }}</th>
                                        <th scope="col">{{ __('Learning Tool') }}</th>
                                        <th scope="col">{{ __('Status') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($upcoming_live_classes as $upcoming_live_class)
                                    <tr>
                                        <td>{{ $upcoming_live_class->date }}</td>
                                        <td>{{ $upcoming_live_class->duration }} {{ __('minutes') }}</td>
                                        <td><div class="course-watch-live-class-topic">{{ Str::limit($upcoming_live_class->class_topic, 50) }}</div></td>
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
                                                $updated_time = changetimeByZone($upcoming_live_class->time,$student_id,'student');
                                                $lesson_time = $updated_time; 
                                                $curdate = strtotime(date("Y-m-d"));
                                                $lsndate = strtotime($upcoming_live_class->date); 
                                                $updated_cur_time = changetimeByZone(date("H:i"),$student_id,'student');
                                                $curtime = strtotime($updated_cur_time);
                                                $lsntime = strtotime($lesson_time);
                                                $lesson_duration = $upcoming_live_class->duration;
                                                $lsntimestamp = strtotime($lesson_time) + $lesson_duration*60;
                                                $lsnendtime = $lsntimestamp;
                                            @endphp

                                            <?php
                                            if($curdate <  $lsndate) { ?>
                                                <span style="border:none;font-weight:bolder;background-color:#322740;color:white;" class="btn btn-warning m-1 py-1 px-2 small">Scheduled</span>
                                            <?php 
                                            }  
                                            else if($curdate == $lsndate) {
                                                if($curtime < $lsntime) {
                                                    $id = 'lec_link1_'.$upcoming_live_class->id;
                                                    echo "<a class='btn btn-warning m-1 lec_link1 py-1 px-2 small' id='$id'>Waiting</a>";
                                                } 
                                            }  
                                            ?>
                                        </td>
                                    </tr>
                                    @empty
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @else
                                <!-- If there is no data Show Empty Design Start -->
                                <div class="empty-data">
                                    <img src="{{ asset('frontend/assets/img/empty-data-img.png') }}" alt="img" class="img-fluid">
                                    <h5 class="my-3">{{ __('Empty Upcoming Class') }}</h5>
                                </div>
                                <!-- If there is no data Show Empty Design End -->
                            @endif
                        </div>
                        <div class="tab-pane fade" id="current" role="tabpanel" aria-labelledby="current-tab">
                            @if(count($current_live_classes))
                            <div class="table-responsive table-responsive-xl">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">{{ __('Date & Time') }}</th>
                                        <th scope="col">{{ __('Time Duration') }}</th>
                                        <th scope="col">{{ __('Topic') }}</th>
                                        <th scope="col">{{ __('Learning Tool') }}</th>
                                        <th scope="col">{{ __('Status') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($current_live_classes as $current_live_class)
                                    <tr>
                                        <td>{{ $current_live_class->date }}</td>
                                        <td>{{ $current_live_class->duration }} {{ __('minutes') }}</td>
                                        <td><div class="course-watch-live-class-topic">{{ Str::limit($current_live_class->class_topic, 50) }}</div></td>
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
                                                $updated_time = changetimeByZone($current_live_class->time,$student_id,'student');
                                                $lesson_time = $updated_time; 
                                                $curdate = strtotime(date("Y-m-d"));
                                                $lsndate = strtotime($current_live_class->date); 
                                                $updated_cur_time = changetimeByZone(date("H:i"),$student_id,'student');
                                                $curtime = strtotime($updated_cur_time);
                                                $lsntime = strtotime($lesson_time);
                                                $lesson_duration = $current_live_class->duration;
                                                $lsntimestamp = strtotime($lesson_time) + $lesson_duration*60;
                                                $lsnendtime = $lsntimestamp;
                                                $StudentURI = getStudentURI($current_live_class->id,$student_id)
                                            @endphp

                                            <?php 
                                            if ($curdate == $lsndate) {
                                                if($curtime >= $lsntime && $curtime < $lsnendtime && $current_live_class->meeting_id == NULL && $current_live_class->meeting_host_name == 'zoom') {
                                                            $id = 'lec_link1_'.$current_live_class->id;
                                                            echo "<a class='btn btn-warning m-1 lec_link1 py-1 px-2 small' id='$id'>Waiting</a>";
                                                }
                                                else if($curtime >= $lsntime && $curtime < $lsnendtime && $StudentURI ==NULL && $current_live_class->meeting_host_name == 'bbb') {
                                                                $id = 'lec_link1_'.$current_live_class->id;
                                                                echo "<a class='btn btn-warning m-1 lec_link1 py-1 px-2 small' id='$id'>Waiting>";
                                                }
                                                else if($curtime >= $lsntime && $curtime < $lsnendtime && $current_live_class->meeting_id == NULL && $current_live_class->meeting_host_name == 'google_meet') {
                                                                $id = 'lec_link1_'.$current_live_class->id;
                                                                echo "<a class='btn btn-warning m-1 lec_link1 py-1 px-2 small' id='$id'>Waiting>";
                                                }

                                                else if($curtime == $lsntime && $current_live_class->status != "Completed" && $current_live_class->status != "Missed" && $current_live_class->meeting_host_name == 'zoom') { ?>
                                                    <a href="javascript:void(0);" data-lesson_id ="<?php echo $current_live_class->id ; ?>" data-meeting_id ="<?php echo $current_live_class->meeting_id ; ?>" data-user_type ="<?php echo "Student" ; ?>" data-user_name ="<?php echo $student_name ; ?>" data-user_id ="<?php echo $student_id ; ?>" class="btn btn-info m-1 create_zoom_meeting_link_liveclass py-1 px-2 small" style="background-color:#499619;color: #fff" target='_self'>Start Class</a>
                                                <?php
                                                }
                                                else if($curtime == $lsntime && ($current_live_class->status == "Completed" || $current_live_class->status == "Missed") && $current_live_class->meeting_host_name == 'zoom') {
                                                    if($current_live_class->status == "Completed" && $current_live_class->is_moderator_left == 1) {
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                    } else if($current_live_class->status == "Missed" && $current_live_class->is_moderator_left == 1) {
                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>"; 
                                                    }  
                                                }
                                                else if($curtime == $lsntime && $current_live_class->status != "Completed" && $current_live_class->status != "Missed" && $current_live_class->meeting_host_name == 'bbb') { 
                                                    $url = $StudentURI;
                                                    ?>
                                                    <a href="<?php echo $url; ?>"  class='btn btn-info m-1 py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a> 
                                                    <?php }
                                                else if($curtime == $lsntime && ($current_live_class->status == "Completed" || $current_live_class->status == "Missed") && $current_live_class->meeting_host_name == 'bbb') {
                                                    if($current_live_class->status == "Completed" && $StudentURI != null) {
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                    } else if($current_live_class->status == "Missed" && $StudentURI != null) {
                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>"; 
                                                    }
                                                }
                                                else if($curtime == $lsntime && $current_live_class->status != "Completed" && $current_live_class->status != "Missed" && $current_live_class->meeting_host_name == 'google_meet') { 
                                                    $url = $StudentURI;
                                                    ?>
                                                    <a href="<?php echo $url; ?>"  class='btn btn-info m-1 py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a> 
                                                    <?php }
                                                else if($curtime == $lsntime && ($current_live_class->status == "Completed" || $current_live_class->status == "Missed") && $current_live_class->meeting_host_name == 'google_meet') {
                                                    if($current_live_class->status == "Completed" && $current_live_class->meeting_id != null) {
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                    } else if($current_live_class->status == "Missed" && $current_live_class->meeting_id != null) {
                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>"; 
                                                    }
                                                }

                                                
                                                else if($curtime > $lsntime && $curtime < $lsnendtime && $current_live_class->status != "Completed" && $current_live_class->status != "Missed" && $current_live_class->meeting_host_name == 'zoom') { ?>   
                                                <a href="javascript:void(0);" id="lec_link" data-lesson_id ="<?php echo $current_live_class->id ; ?>" data-meeting_id ="<?php echo $current_live_class->meeting_id ; ?>" data-user_type ="<?php echo "Student" ; ?>" data-user_name ="<?php echo $student_name ; ?>" data-user_id ="<?php echo $student_id ; ?>" class="btn btn-info m-1 create_zoom_meeting_link_liveclass py-1 px-2 small" style="background-color:#499619;color: #fff" target='_self'>Start Class</a>
                                                <?php 
                                                } 
                                                else if($curtime > $lsntime && $curtime < $lsnendtime && $current_live_class->meeting_host_name == 'zoom') {
                                                    if($current_live_class->status == "Completed" && $current_live_class->is_moderator_left == 1) {
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                    } else if($current_live_class->status == "Missed" && $current_live_class->is_moderator_left == 1) {
                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>"; 
                                                    } else { ?>   
                                                        <a href="javascript:void(0);" id="lec_link" data-lesson_id ="<?php echo $current_live_class->id ; ?>" data-meeting_id ="<?php echo $current_live_class->meeting_id ; ?>" data-user_type ="<?php echo "Student" ; ?>" data-user_name ="<?php echo $student_name ; ?>" data-user_id ="<?php echo $student_id ; ?>" class="btn btn-info m-1 create_zoom_meeting_link_liveclass py-1 px-2 small" style="background-color:#499619;color: #fff" target='_self'>Start Class</a>
                                                    <?php }
                                                } 

                                                else if($curtime > $lsntime && $curtime < $lsnendtime && $current_live_class->status != "Completed" && $current_live_class->status != "Missed" && $current_live_class->meeting_host_name == 'bbb') { ?>   
                                                    <?php
                                                    $url = $StudentURI;
                                                    ?>
                                                    <a href="<?php echo $url; ?>"  class='btn btn-info m-1 py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a> 
                                                    <?php 
                                                } 
                                                else if($curtime > $lsntime && $curtime < $lsnendtime && $current_live_class->meeting_host_name == 'bbb') {
                                                    if($current_live_class->status == "Completed" && $StudentURI != null) {
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                    } else if($current_live_class->status == "Missed" && $StudentURI != null) {
                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>"; 
                                                    } else { ?>   
                                                    <a href="<?php echo $StudentURI; ?>"  class='btn btn-info m-1 py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a> 
                                                    <?php }
                                                }
                                                else if($curtime > $lsntime && $curtime < $lsnendtime && $current_live_class->status != "Completed" && $current_live_class->status != "Missed" && $current_live_class->meeting_host_name == 'google_meet') { ?>   
                                                    <?php
                                                    $url = $StudentURI;
                                                    ?>
                                                    <a href="<?php echo $url; ?>"  class='btn btn-info m-1 py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a> 
                                                    <?php 
                                                } 
                                                else if($curtime > $lsntime && $curtime < $lsnendtime && $current_live_class->meeting_host_name == 'google_meet') {
                                                    if($current_live_class->status == "Completed" && $current_live_class->meeting_id != null) {
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                    } else if($current_live_class->status == "Missed" && $current_live_class->meeting_id != null) {
                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>"; 
                                                    } else { ?>   
                                                    <a href="<?php echo $StudentURI; ?>"  class='btn btn-info m-1 py-1 px-2' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a> 
                                                    <?php }
                                                }
                                                
                                                
                                                else if($curtime == $lsnendtime && $current_live_class->meeting_host_name == 'zoom') {
                                                    if(($current_live_class->status != "Completed" && $current_live_class->status != "Missed") && $current_live_class->meeting_host_name == 'zoom' && ($current_live_class->is_instructor_joined == '1' || $current_live_class->is_instructor_joined == "1" || $current_live_class->is_instructor_joined == 1 || $current_live_class->is_instructor_joined)) {
                                                        updateLessonStatusCompleteZoomInstructor($current_live_class->id,$current_live_class->meeting_id,null);
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                    } else if(($current_live_class->status != "Completed" && $current_live_class->status != "Missed") && $current_live_class->meeting_host_name == 'zoom' && $current_live_class->is_instructor_joined == 0) {
                                                        updateLessonStatusMissedZoomInstructor($current_live_class->id,$current_live_class->meeting_id,null);
                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";  
                                                    } else {
                                                        if($current_live_class->status == "Completed") {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                        } else {
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";     
                                                        }
                                                    }
                                                }

                                                else if($curtime == $lsnendtime && $current_live_class->meeting_host_name == 'bbb') {
                                                    if(($current_live_class->status != "Completed" && $current_live_class->status != "Missed") && $current_live_class->meeting_host_name == 'bbb' && ($current_live_class->is_instructor_joined == '1' || $current_live_class->is_instructor_joined == "1" || $current_live_class->is_instructor_joined == 1 || $current_live_class->is_instructor_joined)) {
                                                        updateLessonStatusCompleteZoomInstructor($current_live_class->id,$current_live_class->meeting_id,null);
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                    } else if(($current_live_class->status != "Completed" && $current_live_class->status != "Missed") && $current_live_class->meeting_host_name == 'bbb' && $current_live_class->is_instructor_joined == 0) {
                                                        updateLessonStatusMissedZoomInstructor($current_live_class->id,$current_live_class->meeting_id,null);
                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";  
                                                    } else {
                                                        if($current_live_class->status == "Completed") {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                        } else {
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";     
                                                        }
                                                    }
                                                }

                                                else if($curtime == $lsnendtime && $current_live_class->meeting_host_name == 'google_meet') {
                                                    if(($current_live_class->status != "Completed" && $current_live_class->status != "Missed") && $current_live_class->meeting_host_name == 'google_meet' && ($current_live_class->is_instructor_joined == '1' || $current_live_class->is_instructor_joined == "1" || $current_live_class->is_instructor_joined == 1 || $current_live_class->is_instructor_joined)) {
                                                        updateLessonStatusCompleteZoomInstructor($current_live_class->id,$current_live_class->meeting_id,null);
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                    } else if(($current_live_class->status != "Completed" && $current_live_class->status != "Missed") && $current_live_class->meeting_host_name == 'google_meet' && $current_live_class->is_instructor_joined == 0) {
                                                        updateLessonStatusMissedZoomInstructor($current_live_class->id,$current_live_class->meeting_id,null);
                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";  
                                                    } else {
                                                        if($current_live_class->status == "Completed") {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                        } else {
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";     
                                                        }
                                                    }
                                                }

                                            }
                                            ?>
                                        </td>
                                        {{-- <td>
                                            <div class="course-watch-meeting-link ">
                                                @if($current_live_class->meeting_host_name == 'zoom')
                                                    <a href="{{ $current_live_class->join_url }}" target="_blank" class="color-hover">{{ __('Go To Meeting') }}</a>
                                                    <span class="iconify copyZoomUrl" data-url="{{ $current_live_class->join_url }}" data-icon="akar-icons:copy"></span>
                                                @elseif($current_live_class->meeting_host_name == 'bbb')
                                                    <a href="{{ route('student.join-bbb-meeting', $current_live_class->id) }}" target="_blank" class="color-hover">{{ __('Go To Meeting') }}</a>
                                                @elseif($current_live_class->meeting_host_name == 'jitsi')
                                                    <a href="{{ route('join-jitsi-meeting', $current_live_class->uuid) }}" target="_blank"  class="color-hover">{{ __('Go To Meeting') }}</a>
                                                    Jitsi
                                                @elseif($current_live_class->meeting_host_name == 'gmeet')
                                                    <a href="{{ $current_live_class->join_url }}" target="_blank"  class="color-hover">{{ __('Go To Meeting') }}</a>
                                                    Gmeet
                                                @elseif($current_live_class->meeting_host_name == 'agora')
                                                    <a href="{{ $current_live_class->join_url }}" target="_blank"  class="color-hover">{{ __('Go To Meeting') }}</a>
                                                    Agora In App Video
                                                @endif
                                            </div>
                                        </td> --}}
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                                <!-- If there is no data Show Empty Design Start -->
                                <div class="empty-data">
                                    <img src="{{ asset('frontend/assets/img/empty-data-img.png') }}" alt="img" class="img-fluid">
                                    <h5 class="my-3">{{ __('Empty Past Class') }}</h5>
                                </div>
                                <!-- If there is no data Show Empty Design End -->
                            @endif
                        </div>
                        <div class="tab-pane fade" id="past" role="tabpanel" aria-labelledby="upcoming-tab">
                            @if(count($past_live_classes))
                            <div class="table-responsive table-responsive-xl">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">{{ __('Date & Time') }}</th>
                                        <th scope="col">{{ __('Time Duration') }}</th>
                                        <th scope="col">{{ __('Topic') }}</th>
                                        <th scope="col">{{ __('Learning Tool') }}</th>
                                        <th scope="col">{{ __('Status') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($past_live_classes as $past_live_class)
                                    <tr>
                                        <td>{{ $past_live_class->date }}</td>
                                        <td>{{ $past_live_class->duration }} {{ __('minutes') }}</td>
                                        <td><div class="course-watch-live-class-topic">{{ Str::limit($past_live_class->class_topic, 50) }}</div></td>
                                        <td>
                                            @if($past_live_class->meeting_host_name == 'zoom')
                                                Zoom
                                            @elseif($past_live_class->meeting_host_name == 'bbb')
                                                BigBlueButton
                                             @elseif($past_live_class->meeting_host_name == 'google_meet')
                                                Google Meet
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $updated_time = changetimeByZone($past_live_class->time,$student_id,'student');
                                                $lesson_time = $updated_time; 
                                                $curdate = strtotime(date("Y-m-d"));
                                                $lsndate = strtotime($past_live_class->date); 
                                                $updated_cur_time = changetimeByZone(date("H:i"),$student_id,'student');
                                                $curtime = strtotime($updated_cur_time);
                                                $lsntime = strtotime($lesson_time);
                                                $lesson_duration = $past_live_class->duration;
                                                $lsntimestamp = strtotime($lesson_time) + $lesson_duration*60;
                                                $lsnendtime = $lsntimestamp;
                                                $StudentURI = getStudentURI($past_live_class->id,$student_id)
                                            @endphp

                                            <?php 
                                            if ($curdate == $lsndate) {
                                                if($curtime == $lsnendtime && $past_live_class->meeting_host_name == 'zoom') {
                                                    if(($past_live_class->status != "Completed" && $past_live_class->status != "Missed") && $past_live_class->meeting_host_name == 'zoom' && ($past_live_class->is_instructor_joined == '1' || $past_live_class->is_instructor_joined == "1" || $past_live_class->is_instructor_joined == 1 || $past_live_class->is_instructor_joined)) {
                                                        updateLessonStatusCompleteZoomInstructor($past_live_class->id,$past_live_class->meeting_id,null);
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                    } else if(($past_live_class->status != "Completed" && $past_live_class->status != "Missed") && $past_live_class->meeting_host_name == 'zoom' && $past_live_class->is_instructor_joined == 0) {
                                                        updateLessonStatusMissedZoomInstructor($past_live_class->id,$past_live_class->meeting_id,null);
                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";  
                                                    } else {
                                                        if($past_live_class->status == "Completed") {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                        } else {
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";     
                                                        }
                                                    }
                                                }

                                                else if($curtime == $lsnendtime && $past_live_class->meeting_host_name == 'bbb') {
                                                    if(($past_live_class->status != "Completed" && $past_live_class->status != "Missed") && $past_live_class->meeting_host_name == 'bbb' && ($past_live_class->is_instructor_joined == '1' || $past_live_class->is_instructor_joined == "1" || $past_live_class->is_instructor_joined == 1 || $past_live_class->is_instructor_joined)) {
                                                        updateLessonStatusCompleteZoomInstructor($past_live_class->id,$past_live_class->meeting_id,null);
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                    } else if(($past_live_class->status != "Completed" && $past_live_class->status != "Missed") && $past_live_class->meeting_host_name == 'bbb' && $past_live_class->is_instructor_joined == 0) {
                                                        updateLessonStatusMissedZoomInstructor($past_live_class->id,$past_live_class->meeting_id,null);
                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";  
                                                    } else {
                                                        if($past_live_class->status == "Completed") {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                        } else {
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";     
                                                        }
                                                    }
                                                }
                                                else if($curtime == $lsnendtime && $past_live_class->meeting_host_name == 'google_meet') {
                                                    if(($past_live_class->status != "Completed" && $past_live_class->status != "Missed") && $past_live_class->meeting_host_name == 'google_meet' && ($past_live_class->is_instructor_joined == '1' || $past_live_class->is_instructor_joined == "1" || $past_live_class->is_instructor_joined == 1 || $past_live_class->is_instructor_joined)) {
                                                        updateLessonStatusCompleteZoomInstructor($past_live_class->id,$past_live_class->meeting_id,null);
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                    } else if(($past_live_class->status != "Completed" && $past_live_class->status != "Missed") && $past_live_class->meeting_host_name == 'google_meet' && $past_live_class->is_instructor_joined == 0) {
                                                        updateLessonStatusMissedZoomInstructor($past_live_class->id,$past_live_class->meeting_id,null);
                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";  
                                                    } else {
                                                        if($past_live_class->status == "Completed") {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                        } else {
                                                            echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";     
                                                        }
                                                    }
                                                }

                                                else if($curtime > $lsnendtime) {
                                                        if($past_live_class->status == "Pending" && $past_live_class->meeting_host_name == 'zoom' && $past_live_class->is_instructor_joined == '1' || $past_live_class->is_instructor_joined == "1" || $past_live_class->is_instructor_joined == 1 || $past_live_class->is_instructor_joined) {	
                                                            updateLessonStatusCompleteZoomInstructor($past_live_class->id,$past_live_class->meeting_id,null);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                        } 
                                                        else if($past_live_class->status == "Pending" && $past_live_class->meeting_host_name == 'bbb' && $past_live_class->is_instructor_joined == '1' || $past_live_class->is_instructor_joined == "1" || $past_live_class->is_instructor_joined == 1 || $past_live_class->is_instructor_joined) {	
                                                            updateLessonStatusCompleteInstructor($past_live_class->id,$past_live_class->meeting_id,null);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                        } 
                                                        else if($past_live_class->status == "Pending" && $past_live_class->meeting_host_name == 'google_meet' && $past_live_class->is_instructor_joined == '1' || $past_live_class->is_instructor_joined == "1" || $past_live_class->is_instructor_joined == 1 || $past_live_class->is_instructor_joined) {	
                                                            updateLessonStatusCompleteInstructor($past_live_class->id,$past_live_class->meeting_id,null);
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                        } 
                                                        else if($past_live_class->status == "Completed") {
                                                            echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                        } 
                                                        else {
                                                            if($past_live_class->status == "Missed") {
                                                                if(!empty($past_live_class->meeting_id)) {
                                                                    $res = check_missed_status_instructor($past_live_class->meeting_id);
                                                                    if($res == 'Completed') {
                                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                                    } else {
                                                                        echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";    
                                                                    }  
                                                                } else {
                                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";    
                                                                }
                                                            } 
                                                            else if($past_live_class->status == "Pending" && $past_live_class->meeting_host_name == "zoom" && $past_live_class->is_instructor_joined == 0) {
                                                                updateLessonStatusMissedZoomInstructor($past_live_class->id,$past_live_class->meeting_id,null);
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";  
                                                            }
                                                            else if($past_live_class->status == "Pending" && $past_live_class->meeting_host_name == "bbb" && $past_live_class->is_instructor_joined == 0) {
                                                                updateLessonStatusMissedZoomInstructor($past_live_class->id,$past_live_class->meeting_id,null);
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";  
                                                            }
                                                            else if($past_live_class->status == "Pending" && $past_live_class->meeting_host_name == "google_meet" && $past_live_class->is_instructor_joined == 0) {
                                                                updateLessonStatusMissedZoomInstructor($past_live_class->id,$past_live_class->meeting_id,null);
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";  
                                                            }
                                                             else {
                                                                echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";    
                                                            }
                                                        }
                                                }
                                            }
                                            else if($curdate > $lsndate) 
                                            {
                                                if($past_live_class->status == "Pending" && $past_live_class->meeting_host_name == 'zoom' && $past_live_class->is_instructor_joined == '1' || $past_live_class->is_instructor_joined == "1" || $past_live_class->is_instructor_joined == 1 || $past_live_class->is_instructor_joined) {	
                                                    updateLessonStatusCompleteZoomInstructor($past_live_class->id,$past_live_class->meeting_id,null);
                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                } 
                                                else if($past_live_class->status == "Pending" && $past_live_class->meeting_host_name == 'bbb' && $past_live_class->is_instructor_joined == '1' || $past_live_class->is_instructor_joined == "1" || $past_live_class->is_instructor_joined == 1 || $past_live_class->is_instructor_joined) {	
                                                    updateLessonStatusCompleteZoomInstructor($past_live_class->id,$past_live_class->meeting_id,null);
                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                } 
                                                else if($past_live_class->status == "Pending" && $past_live_class->meeting_host_name == 'google_meet' && $past_live_class->is_instructor_joined == '1' || $past_live_class->is_instructor_joined == "1" || $past_live_class->is_instructor_joined == 1 || $past_live_class->is_instructor_joined) {	
                                                    updateLessonStatusCompleteZoomInstructor($past_live_class->id,$past_live_class->meeting_id,null);
                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                } 
                                                else if($past_live_class->status == "Completed") {
                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                } 
                                                else if($past_live_class->status == "Pending") {
                                                    echo "<span class='btn btn-danger m-1 py-1 px-2 small' style='background-color:#F00;'>Missed</span>";    
                                                } 
                                                else {
                                                    if($past_live_class->status == "Missed") {
                                                        if(!empty($past_live_class->meeting_id)) {
                                                            $res = check_missed_status_instructor($past_live_class->meeting_id);
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
                                            else 
                                            {
                                                if($past_live_class->status == "Completed") {  
                                                    echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>Completed</span>";
                                                } else {
                                                    if($past_live_class->status == "Missed") {
                                                        if(!empty($past_live_class->meeting_id)) {
                                                            $res = check_missed_status_instructor($past_live_class->meeting_id);
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
                                            ?>
                                        </td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                                <!-- If there is no data Show Empty Design Start -->
                                <div class="empty-data">
                                    <img src="{{ asset('frontend/assets/img/empty-data-img.png') }}" alt="img" class="img-fluid">
                                    <h5 class="my-3">{{ __('Empty Past Class') }}</h5>
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

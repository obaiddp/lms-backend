@extends('layouts.coordinator')

@section('breadcrumb')
    <div class="page-banner-content text-center">
        <h3 class="page-banner-heading text-white pb-15"> {{ __('Class Schedule') }} </h3>

        <!-- Breadcrumb Start-->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item font-14"><a href="{{route('coordinator.dashboard')}}">{{__('Dashboard')}}</a></li>
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
<input type="hidden" name="base_url" id="base_url" value="{{$base_url}}">
            <div class="row">
                <div class="col-12">
                    @if(!$getIndividualClasses->isEmpty())
                        <div class="table-responsive table-responsive-xl">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">{{ __('No') }}</th>
                                    <th scope="col">{{ __('Instructor') }}</th>

                                    <th scope="col">{{ __('Status') }}</th>
                                    <th scope="col">{{ __('Student') }}</th>
                                    <th scope="col">{{ __('Course') }}</th>
                                    <th scope="col">{{ __('Date Time') }}</th>
                               
                                    {{-- <th scope="col">{{ __('Action') }}</th> --}}
                                </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 0;
                                    @endphp
                                @foreach($getIndividualClasses as $individual)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>
                                            <div class="table-data-with-img d-flex align-items-center">
                                                <div class="flex-grow-1 table-data-course-name">{{ Str::limit($individual->instructor->first_name.' '.$individual->instructor->last_name, 50) }}</div>
                                            </div>
                                           
                                        </td>

                                        <td>
                                            @php
                                               $updated_time = changetimeByZone($individual->class_time,$coordinator_id,'coordinator');
                                               $lesson_time = $updated_time; 

                                                 $curdate = strtotime(date("Y-m-d"));
                                                 $lsndate = strtotime($individual->class_date); 
                                                 $updated_cur_time = changetimeByZone(date("H:i"),$coordinator_id,'coordinator');
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
                                                <span  style="border:none;font-weight:bolder;background-color:#322740;color:white;" class="btn btn-warning m-1">
                                                    Scheduled  
                                        </span>
                                        <?php }
                                         else if($curdate == $lsndate)
                                        {
                                          
                                            if($curtime < $lsntime)
                                                {
                                                    $id = 'lec_link1_'.$individual->id;
                                                    echo "<a class='btn btn-warning m-1 lec_link1' id='$id'>".'Waiting'."</a>";
                           
                                                }

                                                else if($curtime >= $lsntime && $curtime < $lsnendtime && $individual->meeting_id == NULL && $individual->class->learning_tool == 'Zoom')
                                                {
                                                    $id = 'lec_link1_'.$individual->id;
                                                    echo "<a class='btn btn-warning m-1 lec_link1' id='$id'>".'Waiting'."</a>";
                                                }
                                                else if($curtime >= $lsntime && $curtime < $lsnendtime && $individual->AcademicURI == NULL && $individual->class->learning_tool == 'BBB')
                                                {
                                                    $id = 'lec_link1_'.$individual->id;
                                                    echo "<a class='btn btn-warning m-1 lec_link1' id='$id'>".'Waiting'."</a>";
                                                }


                                                else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Zoom')
                           
                                                { 
                                                    ?>
                                                    <a href="javascript:void(0);" id="lec_link"  data-meeting_id ="<?php echo $individual->meeting_id ; ?>" data-user_type ="<?php echo "Academic" ; ?>"  class="btn btn-info m-1 create_zoom_meeting_link" style="background-color:#499619;color: #fff" target='_self'>Start Class  </a>
                                                <?php
                                                }
                                                 else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'BBB')
                           
                                                { 
                                                    ?>
                                                    <a  href="javascript:void(0);" data-academic_uri ="<?php echo $individual->AcademicURI ; ?>" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>"  class='btn btn-info m-1 start_bbb_individual' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                                   {{-- <a href="javascript:void(0);"  class='btn btn-info m-1' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>  --}}
                                                <?php
                                                }


                                                 else if($curtime == $lsntime &&($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'Zoom')
 
                                                { 
                         
                                                if($individual->status == "Completed" && $individual->is_moderator_left == '1'){
                                                        echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                    }
                                                    else if($individual->status == "Missed" && $individual->is_moderator_left == '1'){
                                                        echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                                                    }
                                                    ?>
                                                <?php
                     
                   
                                                }
                                                 else if($curtime == $lsntime &&($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'BBB')
 
                                                { 
                         
                                                if($individual->status == "Completed" && $individual->AcademicURI != null){
                                                        echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                                                    }
                                                    else if($individual->status == "Missed" && $individual->AcademicURI != null){
                                                        echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                                                    }
                                                    ?>
                                                <?php
                     
                   
                                                }


                                else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Zoom')
                         { 
                           
                             ?>   
                       <a href="javascript:void(0);" id="lec_link"  data-meeting_id ="<?php echo $individual->meeting_id ; ?>" data-user_type ="<?php echo "Academic" ; ?>"  class="btn btn-info m-1 create_zoom_meeting_link" style="background-color:#499619;color: #fff" target='_self'>Start Class  </a>
                         <?php 
                         }
                         else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'BBB')
                         { 
                           
                             ?>   
                               <a  href="javascript:void(0);" data-academic_uri ="<?php echo $individual->AcademicURI ; ?>" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>"  class='btn btn-info m-1 start_bbb_individual' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                            {{-- <a href="javascript:void(0);"  class='btn btn-info m-1' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>  --}}
                         <?php 
                         }


                         else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->class->learning_tool == 'Zoom')
                         {
                            if($individual->status == "Completed" && $individual->is_moderator_left == '1'){
                                echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                            }
                            else if($individual->status == "Missed" && $individual->is_moderator_left == '1'){
                                echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                            }
                            else{
                            ?>   
                            <a href="javascript:void(0);" id="lec_link"  data-meeting_id ="<?php echo $individual->meeting_id ; ?>" data-user_type ="<?php echo "Academic" ; ?>"  class="btn btn-info m-1 create_zoom_meeting_link" style="background-color:#499619;color: #fff" target='_self'>Start Class  </a>
                            <?php 
                            }
                         }
                         else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->class->learning_tool == 'BBB')
                         {
                            if($individual->status == "Completed" && $individual->AcademicURI != null){
                                echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                            }
                            else if($individual->status == "Missed" && $individual->AcademicURI != null){
                                echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                            }
                            else{
                            ?>   
                                 <a  href="javascript:void(0);" data-academic_uri ="<?php echo $individual->AcademicURI ; ?>" data-lesson_id ="<?php echo $individual->id ; ?>" data-meeting_id ="<?php echo $individual->meeting_id ; ?>"  class='btn btn-info m-1 start_bbb_individual' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>
                                {{-- <a href="javascript:void(0);"  class='btn btn-info m-1' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>  --}}
                            <?php 
                            }
                         }


                         else if($curtime == $lsnendtime && $individual->class->learning_tool == 'Zoom')
                         {
                            if(($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'Zoom' && ($individual->is_instructor_joined == '1' ||$individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined)){
                                updateLessonStatusCompleteZoom($individual->id,$individual->meeting_id,null);
                                echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                                ?>   
                            <?php 
                            }
                            else if(($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'Zoom' && $individual->is_instructor_joined == '0'){
                                updateLessonStatusMissedZoom($individual->id,$individual->meeting_id,null);
                                echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>";  
                            ?>   
                        <?php 
                        }
                            else{
                                if($individual->status == "Completed"){
                                    echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                                }
                                else{
                                    echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>";     
                                }
                            }
                         }
                         else if($curtime == $lsnendtime && $individual->class->learning_tool == 'BBB')
                         {
                            if(($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'BBB' && ($individual->is_instructor_joined == '1' ||$individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined)){
                                updateLessonStatusComplete($individual->id,$individual->meeting_id,null);
                                echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                                ?>   
                            <?php 
                            }
                            else if(($individual->status != "Completed" && $individual->status != "Missed") && $individual->class->learning_tool == 'BBB' && $individual->is_instructor_joined == '0'){
                                updateLessonStatusMissed($individual->id,$individual->meeting_id,null);
                                echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>";  
                            ?>   
                        <?php 
                        }
                            else{
                                if($individual->status == "Completed"){
                                    echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                                }
                                else{
                                    echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>";     
                                }
                            }
                         }


                         else if($curtime > $lsnendtime)
                         {
                            if($individual->status == "Pending" && $individual->class->learning_tool == 'Zoom' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined)
											{	
                                                updateLessonStatusCompleteZoom($individual->id,$individual->meeting_id,null);
												echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
											}
                             else if($individual->status == "Pending" && $individual->class->learning_tool == 'BBB' && $individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined)
											{	
                                                updateLessonStatusComplete($individual->id,$individual->meeting_id,null);
												echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
											}
                             else if($individual->status == "Completed"){
                                 echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                             }
                             else
                             {
                                if($individual->status == "Missed"){
                                    if(!empty($individual->meeting_id)){
                                        $res = check_missed_status($individual->meeting_id);
                                         if($res == 'Completed'){
                                             echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                                         }
                                         else{
 
                                             echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>";    
                                         }  
                                    }
                                    else{
                                    echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>";    
                                    }
                                }
                                else if($individual->status == "Pending" && $individual->class->learning_tool == "Zoom" && $individual->is_instructor_joined == '0'){
                                            updateLessonStatusMissedZoom($individual->id,$individual->meeting_id,null);
                                         
                                            echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>";  
                                            ?>
                                       
                                                                       
                                <?php }
                                  else if($individual->status == "Pending" && $individual->class->learning_tool == "BBB" && $individual->is_instructor_joined == '0'){
                                            updateLessonStatusMissed($individual->id,$individual->meeting_id,null);
                                         
                                            echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>";  
                                            ?>
                                       
                                                                       
                                <?php }
                                 else{
 
                                echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>";    
                                }
                             }
                         }
                    }
                    else if($curdate > $lsndate)
                     {
                        if($individual->status == "Pending" && $individual->class->learning_tool == 'Zoom' && $$individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined)
                        {	
                             updateLessonStatusCompleteZoom($individual->id,$individual->meeting_id,null);
                             echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                        }
                        else if($individual->status == "Pending" && $individual->class->learning_tool == 'BBB' && $$individual->is_instructor_joined == '1' || $individual->is_instructor_joined == "1" || $individual->is_instructor_joined == 1 || $individual->is_instructor_joined)
                        {	
                             updateLessonStatusComplete($individual->id,$individual->meeting_id,null);
                             echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                        }
                        else if($individual->status == "Completed")
                         {
                             echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                         }
                         else if($individual->status == "Pending"){
                             
                             echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>";    
                         }
                         else
                         {
                                if($individual->status == "Missed"){
 
                                     if(!empty($individual->meeting_id)){
 
                                         $res = check_missed_status($individual->meeting_id);
                                         if($res == 'Completed'){
                                             echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                                         }
                                         // elseif($res == "Missed"){
                                         else{
 
                                             echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>";    
                                         }  
                                     }
                                     else{
                                         echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>";    
                                     }
 
                                }
                         }
                     }
                     else{
                        if($individual->status == "Completed")
                         {  
                             echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                         }
                         else
                         {
                                if($individual->status == "Missed"){
 
                                     if(!empty($individual->meeting_id)){
 
                                         $res = check_missed_status($individual->meeting_id);
                                         if($res == 'Completed'){
                                             echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                                         }  
                                         // elseif($res == "Missed"){
                                         else{
 
                                             echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>";    
                                         }
                                     }
                                     else{
                                         echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>";    
                                     }
 
                                }
                         }
                     }?>
                                        </td>



                                        <td>
                                            <div class="table-data-with-img d-flex align-items-center">
                                                <div class="flex-grow-1 table-data-course-name">{{ Str::limit($individual->student->first_name.' '.$individual->student->last_name, 50) }}</div>
                                            </div>
                                           </td>


                                        <td>
                                            <div class="table-data-with-img d-flex align-items-center">
                                                <div class="flex-grow-1 table-data-course-name">{{ Str::limit($individual->course->title, 50) }}</div>
                                            </div>
                                        </td>
                                        <td>{{ date('d-m-Y',strtotime($individual->class_date)).' 
                                        '.$updated_time }}</td>
                                        
                                        {{-- <td>{{$individual->status }}</td> --}}
                                  
                                     
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination Start -->
                        @if(@$getIndividualClasses->hasPages())
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
                    @if(!$getGroupClasses->isEmpty())
                        <div class="table-responsive table-responsive-xl">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">{{ __('No') }}</th>
                                    <th scope="col">{{ __('Instructor') }}</th>
                                    <th scope="col">{{ __('Status') }}</th>
                                    <th scope="col">{{ __('No. Of. Students') }}</th>
                                    <th scope="col">{{ __('Course') }}</th>
                                    <th scope="col">{{ __('Date Time') }}</th>
                               
                                    {{-- <th scope="col">{{ __('Action') }}</th> --}}
                                </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $j = 0;
                                @endphp
                                @foreach($getGroupClasses as $group)
                                    <tr>
                                        <td>{{ ++$j }}</td>
                                       <td>
                                        <div class="table-data-with-img d-flex align-items-center">
                                            <div class="flex-grow-1 table-data-course-name">{{ Str::limit($group->instructor->first_name.' '.$group->instructor->last_name, 50) }}</div>
                                        </div>
                                       </td>
                                        <td>
                                            @php
                                              $updated_time = changetimeByZone($group->class_time,$coordinator_id,'coordinator');
                                              $lesson_time = $updated_time; 

                                           $curdate = strtotime(date("d-m-Y"));
                                           $lsndate = strtotime($group->class_date); 
                                           $updated_cur_time = changetimeByZone(date("H:i"),$coordinator_id,'coordinator');
                                           $curtime = strtotime($updated_cur_time);
                                           $lsntime = strtotime($lesson_time) ;
                                           $lesson_duration =$group->class->duration;
                                           $lsntimestamp = strtotime($lesson_time) + $lesson_duration*60;
                                           $lsnendtime = $lsntimestamp ;
                                    
                                        
                                      @endphp

<?php
if($curdate <  $lsndate)
       {?>
       <span  style="border:none;font-weight:bolder;background-color:#322740;color:white;" class="btn btn-warning m-1">
           Scheduled  
</span>
<?php }
  else if($curdate == $lsndate)
  {
                if($curtime < $lsntime)
                    {
                        $id = 'lec_link2_'.$group->group_id;
                        echo "<a class='btn btn-warning m-1 lec_link2' id='$id'>".'Waiting'."</a>";
                        //echo "Waiting";
                    }
                else if($curtime >= $lsntime && $curtime < $lsnendtime  && $group->meeting_id == NULL && $group->class->learning_tool == 'Zoom')
                    {
                        $id = 'lec_link2_'.$group->group_id;
                        echo "<a class='btn btn-warning m-1 lec_link2' id='$id'>".'Waiting'."</a>";
                    }
                    else if($curtime >= $lsntime && $curtime < $lsnendtime  && $group->AcademicURI == NULL && $group->class->learning_tool == 'BBB')
                    {
                        $id = 'lec_link2_'.$group->group_id;
                        echo "<a class='btn btn-warning m-1 lec_link2' id='$id'>".'Waiting'."</a>";
                    }

                else if($curtime == $lsntime&& $group->status != "Completed"&& $group->status != "Missed" && $group->class->learning_tool == 'Zoom')
                    { ?>
                        <a href="javascript:void(0);"  id="lec_lnk" data-group_id ="<?php echo $group->group_id ; ?>" data-meeting_id ="<?php echo $group->meeting_id ; ?>" data-user_type ="<?php echo "Academic" ; ?>"    class="btn btn-info m-1 create_zoom_meeting_link_2" style="background-color:#499619;color: #fff" target='_self'>Start Class  </a>
                        <?php
                    
                    }
                    else if($curtime == $lsntime&& $group->status != "Completed"&& $group->status != "Missed" && $group->class->learning_tool == 'BBB')
                    { ?>
                          <a  href="javascript:void(0);" data-group_id ="<?php echo $group->group_id ; ?>" data-academic_uri ="<?php echo $group->AcademicURI ; ?>" data-lesson_id ="<?php echo $group->id ; ?>" data-meeting_id ="<?php echo $group->meeting_id ; ?>"  class='btn btn-info m-1 start_bbb_group' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>                         
                        {{-- <a href="javascript:void(0);"  class='btn btn-info m-1' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>  --}}
                        <?php
                    
                    }

                else if($curtime > $lsntime && $curtime < $lsnendtime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'Zoom')
                    {  
                            ?>
                    <a href="javascript:void(0);"  id="lec_lnk" data-group_id ="<?php echo $group->group_id ; ?>" data-meeting_id ="<?php echo $group->meeting_id ; ?>" data-user_type ="<?php echo "Academic" ; ?>"    class="btn btn-info m-1 create_zoom_meeting_link_2" style="background-color:#499619;color: #fff" target='_self'>Start Class  </a>
                        <?php 
                    }
                    else if($curtime > $lsntime && $curtime < $lsnendtime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'BBB')
                    {  
                            ?>
                              <a  href="javascript:void(0);" data-group_id ="<?php echo $group->group_id ; ?>" data-academic_uri ="<?php echo $group->AcademicURI ; ?>" data-lesson_id ="<?php echo $group->id ; ?>" data-meeting_id ="<?php echo $group->meeting_id ; ?>"  class='btn btn-info m-1 start_bbb_group' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>                         
                     {{-- <a href="javascript:void(0);"  class='btn btn-info m-1' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>  --}}
                        <?php 
                    }


                else if($curtime > $lsntime && $curtime < $lsnendtime && $group->class->learning_tool == 'Zoom')
                    {
                            if($group->status == "Completed" && $group->is_moderator_left == '1'){
                                echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                            }
                            else if($group->status == "Missed" && $group->is_moderator_left == '1'){
                                echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                            }
                            else{
                                ?>   
                                                <a href="javascript:void(0);"  id="lec_lnk" data-group_id ="<?php echo $group->group_id ; ?>" data-meeting_id ="<?php echo $group->meeting_id ; ?>" data-user_type ="<?php echo "Academic" ; ?>"    class="btn btn-info m-1 create_zoom_meeting_link_2" style="background-color:#499619;color: #fff" target='_self'>Start Class  </a>
                            <?php 
                            }
                    }
                    else if($curtime > $lsntime && $curtime < $lsnendtime && $group->class->learning_tool == 'BBB')
                    {
                            if($group->status == "Completed" && $group->AcademicURI != null){
                                echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                            }
                            else if($group->status == "Missed" && $group->AcademicURI != null){
                                echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                            }
                            else{
                                ?>   
                                    <a  href="javascript:void(0);" data-group_id ="<?php echo $group->group_id ; ?>" data-academic_uri ="<?php echo $group->AcademicURI ; ?>" data-lesson_id ="<?php echo $group->id ; ?>" data-meeting_id ="<?php echo $group->meeting_id ; ?>"  class='btn btn-info m-1 start_bbb_group' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>                         
                                {{-- <a href="javascript:void(0);"  class='btn btn-info m-1' style='background-color:#499619;color: #fff' target='_blank'>Start Class</a>  --}}
                            <?php 
                            }
                    }


                else if($curtime == $lsnendtime && $group->class->learning_tool == 'Zoom')
                    {
                            if(($group->status != "Completed" && $group->status != "Missed") && $group->class->learning_tool == 'Zoom' && ($group->is_instructor_joined == '1' || $group->is_instructor_joined == "1" || $group->is_instructor_joined == 1 || $group->is_instructor_joined)){
                                updateLessonStatusCompleteZoom($group->id,$group->meeting_id,$group->group_id);
                                echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                                ?>   
                            <?php 
                            }
                            if(($group->status != "Completed" && $group->status != "Missed") && $group->class->learning_tool == 'Zoom' && $group->is_instructor_joined == '0'){
                                updateLessonStatusMissedZoom($group->id,$group->meeting_id,$group->group_id);
                                echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                                ?>   
                            <?php 
                            }
                            else
                            {
                                if($group->status == "Completed"){
                                    echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                                }
                                else{
                                    echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                                          
                                }
                              
                                
                            }
                    }
                    else if($curtime == $lsnendtime && $group->class->learning_tool == 'BBB')
                    {
                            if(($group->status != "Completed" && $group->status != "Missed") && $group->class->learning_tool == 'BBB' && ($group->is_instructor_joined == '1' || $group->is_instructor_joined == "1" || $group->is_instructor_joined == 1 || $group->is_instructor_joined)){
                                updateLessonStatusComplete($group->id,$group->meeting_id,$group->group_id);
                                echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                                ?>   
                            <?php 
                            }
                            if(($group->status != "Completed" && $group->status != "Missed") && $group->class->learning_tool == 'BBB' && $group->is_instructor_joined == '0'){
                                updateLessonStatusMissed($group->id,$group->meeting_id,$group->group_id);
                                echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                                ?>   
                            <?php 
                            }
                            else
                            {
                                if($group->status == "Completed"){
                                    echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                                }
                                else{
                                    echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                                          
                                }
                              
                                
                            }
                    }


                    else if($curtime > $lsnendtime)
                    {
                            if($group->class->learning_tool == 'Zoom' && $group->is_instructor_joined == '1' || $group->is_instructor_joined == "1" || $group->is_instructor_joined == 1 || $group->is_instructor_joined)
                            {	
                                    updateLessonStatusCompleteZoom($group->id,$group->meeting_id,$group->group_id);
                                    echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                            }
                            else if($group->class->learning_tool == 'BBB' && $group->is_instructor_joined == '1' || $group->is_instructor_joined == "1" || $group->is_instructor_joined == 1 || $group->is_instructor_joined)
                            {	
                                    updateLessonStatusComplete($group->id,$group->meeting_id,$group->group_id);
                                    echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                            }
                            else if($group->status == "Completed"){
                                echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                            }
                            else
                            {
                                if($group->status == "Missed"){
                                    if(!empty($group->meeting_id)){
                                        $res = check_missed_status($group->meeting_id);
                                        if($res == 'Completed'){
                                            echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                                        }
                                        else{

                                            echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                                        }   
                                    }
                                    else{
                                        echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                                    }
                                }
                                else if($group->status == "Pending" && $group->class->learning_tool == "Zoom" && $group->is_instructor_joined == '0'){
                                                updateLessonStatusMissedZoom($group->id,$group->meeting_id,$group->group_id);
                                                echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>";  
                                                ?>                                        
                                <?php }
                                  else if($group->status == "Pending" && $group->class->learning_tool == "BBB" && $group->is_instructor_joined == '0'){
                                                updateLessonStatusMissed($group->id,$group->meeting_id,$group->group_id);
                                                echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>";  
                                                ?>                                        
                                <?php }
                                else{
                                         echo "<span class='lesson-status' style='background-color:#F00;'>".'Missed'."</span>";
                                }
                            }
                    }
  }
  else if($curdate > $lsndate)
  {
                    if($group->class->learning_tool == 'Zoom' && $group->is_instructor_joined == '1' || $group->is_instructor_joined == "1" || $group->is_instructor_joined == 1 || $group->is_instructor_joined)
                        {	
                            updateLessonStatusCompleteZoom($group->id,$group->meeting_id,$group->group_id);
                                echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                        }
                        else if($group->class->learning_tool == 'BBB' && $group->is_instructor_joined == '1' || $group->is_instructor_joined == "1" || $group->is_instructor_joined == 1 || $group->is_instructor_joined)
                        {	
                            updateLessonStatusComplete($group->id,$group->meeting_id,$group->group_id);
                                echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                        }
                    else if($group->status == "Completed")
                        {
                            echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                        }
                     else if($group->status == "Pending"){

                            echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                        }
                    else
                        {
                            if($group->status == "Missed"){

                                    if(!empty($group->meeting_id)){

                                        $res = check_missed_status($group->meeting_id);
                                        if($res == 'Completed'){
                                            echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                                        }
                                        else{

                                            echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                                        }   
                                    }
                                    else{
                                        echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                                    }

                                }
                                else{
                                    echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
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
                                            echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";
                                        }   
                                        else{

                                            echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                                        }
                                    }
                                    else{
                                        echo "<span class='btn btn-danger m-1' style='background-color:#F00;'>".'Missed'."</span>"; 
                                    }

                                }
                                else{
                                    echo "<span class='btn btn-success m-1' style='background-color:#499619;'>".'Completed'."</span>";    
                                }
                        }
  }
   ?>
                                      </td>


                                      <td>
                                        <a class="total_students" class="btn" data-class_id ="<?php echo $group->class->id ; ?>" href="javascript::void(0);" data-toggle="modal" data-target="#showstudentnames">
                                        <div class="table-data-with-img d-flex align-items-center">
                                          <div class="flex-grow-1 table-data-course-name"><?php echo $group->class->no_of_students ; ?></div>
                                        </div>
                                        </a>&nbsp;
                                    </td>

                                        <td>
                                            <div class="table-data-with-img d-flex align-items-center">
                                                <div class="flex-grow-1 table-data-course-name">{{ Str::limit($group->course->title, 50) }}</div>
                                            </div>
                                        </td>
                                        <td>{{ date('d-m-Y',strtotime($group->class_date)).' 
                                        '.$updated_time }}</td>
                                        
                                    
                                            
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
                        <!-- Pagination Start -->
                        @if(@$getGroupClasses->hasPages())
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
                <div id="student_names_box" class="modal-body">
               

                </div>
           
            </div>
        </div>
    </div>

  
@endsection

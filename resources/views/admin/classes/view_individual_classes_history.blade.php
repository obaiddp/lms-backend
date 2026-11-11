@extends('layouts.admin')

@section('content')
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2>{{__('Individual Classes History')}}</h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('Dashboard')}}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{__('Individual Classes History')}}</li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="customers__area bg-style mb-30">
                        <div class="item-title d-flex justify-content-between">
                            <h2>{{__('Individual Classes History')}}</h2>
                        </div>
                        <div class="customers__table">
                            <table id="classes-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th>{{ __('No') }}</th>
                                    <th>{{ __('Instructor') }}</th>
                                    <th>{{ __('Student') }}</th>
                                    <th>{{ __('Learning Tool') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Course') }}</th>
                                    <th>{{ __('Date Time') }}</th>
                                    <th>{{ __('Instructor ID') }}</th>
                                    <th>{{ __('Student ID') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $i = 0;
                                @endphp
                                @foreach($getAllClassesHistory as $class)
                                    <tr class="removable-item">
                                        <td>{{ ++$i }}</td>
                                        <td>
                                            @if (isset($class->instructor))
                                            <a href="{{route('instructor.view', [$class->instructor->uuid])}}"> 
                                                {{$class->instructor->first_name.' '.$class->instructor->last_name}}
                                            </a>
                                            @endif
                                        </td>
                                        <td>
                                            @if (isset($class->student))
                                            <a href="{{route('student.view', [$class->student->uuid])}}" class="btn-action mr-30" title="View Details">
                                            {{
                                                $class->student->first_name.' '.$class->student->last_name
                                            }}
                                            </a>
                                            @endif
                                        </td>
                                        <td>
                                           {{$class->class?->learning_tool}}
                                        </td>
                                        <td>    
                                            @php
                                                $updated_time = changetimeByZone($class->class_time,$class->instructor_id,'admin');
                                                $lesson_time = $updated_time; 

                                                $curdate = strtotime(date("Y-m-d"));
                                                $lsndate = strtotime($class->class_date); 
                                                $updated_cur_time = changetimeByZone(date("H:i"),$class->instructor_id,'admin');
                                                $curtime = strtotime($updated_cur_time);
                                                $lsntime = strtotime($lesson_time) ;
                                                $lesson_duration =$class->class?->duration;
                                                $lsntimestamp = strtotime($lesson_time) + $lesson_duration*60;
                                                $lsnendtime = $lsntimestamp ;
                                            
                                            @endphp
                                            <?php  
                                                 if($curdate > $lsndate)
                                                 {  
                                                    if (($class->status == "Pending" || $class->status == "Scheduled") && $class->class?->learning_tool == 'Zoom' && $class->is_instructor_joined == '1' || $class->is_instructor_joined == "1" || $class->is_instructor_joined == 1 || $class->is_instructor_joined) {
                                                        updateLessonStatusCompleteZoom($class->id, $class->meeting_id, null);
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } 
                                                    else if (($class->status == "Pending" || $class->status == "Scheduled") && $class->class?->learning_tool == 'BBB' && $class->is_instructor_joined == '1' || $class->is_instructor_joined == "1" || $class->is_instructor_joined == 1 || $class->is_instructor_joined) {
                                                        updateLessonStatusComplete($class->id, $class->meeting_id, null);
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } 
                                                    else if($class->status == "Completed")
                                                    {
                                                        echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                    }
                                                    else if(($class->status == "Pending" || $class->status == "Scheduled") && $class->class?->learning_tool == 'Zoom'){
                                                        
                                                        updateLessonStatusMissedZoom($class->id,$class->meeting_id,null);
                                                        echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>";    
                                                    }
                                                    else if(($class->status == "Pending" || $class->status == "Scheduled") && $class->class?->learning_tool == 'BBB'){
                                                        
                                                        updateLessonStatusMissed($class->id,$class->meeting_id,null);
                                                        echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>";    
                                                    }
                                                    else
                                                    {
                                                                if($class->status == "Missed"){
                                
                                                                    if(!empty($class->meeting_id)){
                                
                                                                        $res = check_missed_status($class->meeting_id);
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
                                                        if($class->status == "Completed")
                                                            {  
                                                                echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                            }
                                                        else
                                                        {
                                                            if($class->status == "Missed"){
                            
                                                                if(!empty($class->meeting_id)){
                            
                                                                    $res = check_missed_status($class->meeting_id);
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
                                            ?>
                                            
                                            {{-- {{$class->status}} --}}
                                        </td>

                                        <td>
                                            <div class="table-data-with-img d-flex align-items-center">
                                                <div class="flex-grow-1 table-data-course-name">{{ Str::limit($class->course? $class->course->title : null, 50) }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $updated_time = changetimeByZone($class->class_time,$class->instructor_id,'admin');
                                            @endphp
                                            {{ date('d-m-Y',strtotime($class->class_date)).'  '.date("D", strtotime($class->class_date)).'  '.$updated_time }}
                                        </td>
                                   
                                        <td>
                                            {{$class->instructor_id}}
                                        </td>
                                        <td>
                                            {{$class->student_id}}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="mt-3">
                                {{$getAllClassesHistory->links()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Page content area end -->
@endsection

@push('style')
    <link rel="stylesheet" href="{{asset('admin/css/jquery.dataTables.min.css')}}">
@endpush

@push('script')
    <script src="{{asset('admin/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('admin/js/custom/data-table-page.js')}}"></script>
    {{-- <script src="{{ asset('admin/js/custom/instructor-delete.js') }}"></script> --}}
    {{-- <script src="{{ asset('admin/js/custom/coordinator-delete.js') }}"></script> --}}
    <script src="{{ asset('admin/js/custom/class-delete.js') }}"></script>
    <script>
        'use strict'
        $(".change-status").change(function () {
            var id = $(this).closest('tr').find('#hidden_id').html();
            var status_value = $(this).val();
            Swal.fire({
                title: "{{ __('Are you sure to change status?') }}",
                text: "{{ __('You won`t be able to revert this!') }}",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "{{__('Yes, Change it!')}}",
                cancelButtonText: "{{__('No, cancel!')}}",
                reverseButtons: true
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        type: "POST",
                        url: "{{route('admin.coordinator.changeCoordinatorStatus')}}",
                        data: {"status": status_value, "id": id, "_token": "{{ csrf_token() }}",},
                        datatype: "json",
                        success: function (data) {
                            toastr.options.positionClass = 'toast-bottom-right';
                            toastr.success('', "{{ __('Coordinator status has been updated') }}");
                            location.reload();
                        },
                        error: function () {
                            alert("Error!");
                        },
                    });
                } else if (result.dismiss === "cancel") {
                    location.reload();
                }
            });
        });
        
        $(".change-auto-content").change(function () {
            var id = $(this).closest('tr').find('#hidden_id').html();
            var status_value = $(this).val();
            Swal.fire({
                title: "{{ __('Are you sure to change?') }}",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "{{__('Yes, Change it!')}}",
                cancelButtonText: "{{__('No, cancel!')}}",
                reverseButtons: true
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        type: "POST",
                        url: "{{route('admin.coordinator.changeAutoContentStatus')}}",
                        data: {"auto_content_approval": status_value, "id": id, "_token": "{{ csrf_token() }}",},
                        datatype: "json",
                        success: function (data) {
                            toastr.options.positionClass = 'toast-bottom-right';
                            toastr.success('', "{{ __('Auto content status has been updated') }}");
                            location.reload();
                        },
                        error: function () {
                            alert("Error!");
                        },
                    });
                } else if (result.dismiss === "cancel") {
                    location.reload();
                }
            });
        });
    </script>
@endpush

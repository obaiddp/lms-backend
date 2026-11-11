<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Group Classes History')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Group Classes History')); ?></li>
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
                            <h2><?php echo e(__('Group Classes History')); ?></h2>
                        </div>
                        <div class="customers__table">
                            <table id="classes-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('No')); ?></th>
                                    <th><?php echo e(__('Group')); ?></th>
                                    <th><?php echo e(__('Instructor')); ?></th>
                                    <th><?php echo e(__('No Of Students')); ?></th>
                                    <th><?php echo e(__('Learning Tool')); ?></th>
                                    <th><?php echo e(__('Status')); ?></th>
                                    <th><?php echo e(__('Course')); ?></th>
                                    <th><?php echo e(__('Date Time')); ?></th>
                                 
                                </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 0;
                                ?>
                                <?php $__currentLoopData = $getAllClassesHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="removable-item">
                                        <td><?php echo e(++$i); ?></td>
                                        <td><?php echo e($class->group_id); ?></td>
                                   
                                        <td>
                                            <?php if(isset($class->instructor)): ?>
                                            <a href="<?php echo e(route('instructor.view', [$class->instructor->uuid])); ?>"> 
                                                <?php echo e($class->instructor->first_name.' '.$class->instructor->last_name); ?>

                                            </a>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($class->class?->no_of_students); ?></td>
                                        <td>
                                           <?php echo e($class->class?->learning_tool); ?>

                                        </td>
                                        <td>    
                                            <?php
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
                                            
                                            ?>
                                            <?php  
                                                 if($curdate > $lsndate)
                                                 {  
                                                    if (($class->status == "Pending" || $class->status == "Scheduled") && $class->class?->learning_tool == 'Zoom' && $class->is_instructor_joined == '1' || $class->is_instructor_joined == "1" || $class->is_instructor_joined == 1 || $class->is_instructor_joined) {
                                                        updateLessonStatusCompleteZoom($class->id,$class->meeting_id,$class->group_id);
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } 
                                                    else if (($class->status == "Pending" || $class->status == "Scheduled") && $class->class?->learning_tool == 'BBB' && $class->is_instructor_joined == '1' || $class->is_instructor_joined == "1" || $class->is_instructor_joined == 1 || $class->is_instructor_joined) {
                                                        updateLessonStatusComplete($class->id, $class->meeting_id, $class->group_id);
                                                        echo "<span class='btn btn-success m-1 py-1 px-2 small' style='background-color:#499619;'>" . 'Completed' . "</span>";
                                                    } 
                                                    else if($class->status == "Completed")
                                                    {
                                                        echo "<span class='btn btn-success m-1 py-1 px-2' style='background-color:#499619;padding:4px;'>".'Completed'."</span>";
                                                    }
                                                    else if(($class->status == "Pending" || $class->status == "Scheduled") && $class->class?->learning_tool == 'Zoom'){
                                                        updateLessonStatusMissedZoom($class->id,$class->meeting_id,$class->group_id);
                                                        echo "<span class='btn btn-danger m-1 py-1 px-2' style='background-color:#F00;padding:4px;'>".'Missed'."</span>";    
                                                    }
                                                    else if(($class->status == "Pending" || $class->status == "Scheduled") && $class->class?->learning_tool == 'BBB'){
                                                        updateLessonStatusMissed($class->id,$class->meeting_id,$class->group_id);
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
                                            
                                            
                                        </td>

                                        <td>
                                            <div class="table-data-with-img d-flex align-items-center">
                                                <div class="flex-grow-1 table-data-course-name"><?php echo e(Str::limit($class->course? $class->course->title : null, 50)); ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                                $updated_time = changetimeByZone($class->class_time,$class->instructor_id,'admin');
                                            ?>
                                            <?php echo e(date('d-m-Y',strtotime($class->class_date)).'  '.date("D", strtotime($class->class_date)).'  '.$updated_time); ?>

                                        </td>
                                   
                                      
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <div class="mt-3">
                                <?php echo e($getAllClassesHistory->links()); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Page content area end -->
<?php $__env->stopSection(); ?>

<?php $__env->startPush('style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/jquery.dataTables.min.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script src="<?php echo e(asset('admin/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/custom/data-table-page.js')); ?>"></script>
    
    
    <script src="<?php echo e(asset('admin/js/custom/class-delete.js')); ?>"></script>
    <script>
        'use strict'
        $(".change-status").change(function () {
            var id = $(this).closest('tr').find('#hidden_id').html();
            var status_value = $(this).val();
            Swal.fire({
                title: "<?php echo e(__('Are you sure to change status?')); ?>",
                text: "<?php echo e(__('You won`t be able to revert this!')); ?>",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "<?php echo e(__('Yes, Change it!')); ?>",
                cancelButtonText: "<?php echo e(__('No, cancel!')); ?>",
                reverseButtons: true
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo e(route('admin.coordinator.changeCoordinatorStatus')); ?>",
                        data: {"status": status_value, "id": id, "_token": "<?php echo e(csrf_token()); ?>",},
                        datatype: "json",
                        success: function (data) {
                            toastr.options.positionClass = 'toast-bottom-right';
                            toastr.success('', "<?php echo e(__('Coordinator status has been updated')); ?>");
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
                title: "<?php echo e(__('Are you sure to change?')); ?>",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "<?php echo e(__('Yes, Change it!')); ?>",
                cancelButtonText: "<?php echo e(__('No, cancel!')); ?>",
                reverseButtons: true
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo e(route('admin.coordinator.changeAutoContentStatus')); ?>",
                        data: {"auto_content_approval": status_value, "id": id, "_token": "<?php echo e(csrf_token()); ?>",},
                        datatype: "json",
                        success: function (data) {
                            toastr.options.positionClass = 'toast-bottom-right';
                            toastr.success('', "<?php echo e(__('Auto content status has been updated')); ?>");
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/classes/view_group_classes_history.blade.php ENDPATH**/ ?>
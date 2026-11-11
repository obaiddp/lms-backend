<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Courses')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Approved Instructors')); ?></li>
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
                            <h2><?php echo e(__('All Courses')); ?></h2>
                        </div>
                        <div class="customers__table all-course">
                            <table id="customers-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('Image')); ?></th>
                                    <th><?php echo e(__('Title')); ?></th>
                                    <th><?php echo e(__('Course Type')); ?></th>
                                    
                                    <th><?php echo e(__('Category')); ?></th>
                                   <!-- <th><?php echo e(__('Subcategory')); ?></th>-->
                                    <th><?php echo e(__('Price')); ?></th>
                                    <th><?php echo e(__('Featured')); ?></th>
                                    <th><?php echo e(__('Status')); ?></th>
                                    <th><?php echo e(__('Action')); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="removable-item">
                                        <td>
                                            <a href="#"> <img src="<?php echo e(getImageFile($course->image_path)); ?>" width="80"> </a>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('admin.course.view', [$course->uuid])); ?>" class="btn-action mr-30" title="View Details">
                                            <?php echo e($course->title); ?>

                                            </a>
                                        </td>
                                        <td>
                                        <?php if($course->course_type == 1): ?>
                                        
                                        <?php echo e(__('General')); ?>

                                        <?php elseif($course->course_type == 2): ?>
                                        
                                        <?php echo e(__('Scorm')); ?>

                                        <?php elseif($course->course_type == 3): ?>
                                        
                                        <?php echo e(__('Live')); ?>

                                        <?php endif; ?>
                                        </td>

                                        
                                        <td>
                                            <?php echo e($course->category ? $course->category->name : ''); ?>

                                        </td>
                                      <!--  <td>
                                            <?php echo e($course->subcategory ? $course->subcategory->name : ''); ?>

                                        </td>-->
                                        <td>
                                            <?php if(get_currency_placement() == 'after'): ?>
                                                <?php echo e($course->price); ?> <?php echo e(get_currency_symbol()); ?>

                                            <?php else: ?>
                                                <?php echo e(get_currency_symbol()); ?> <?php echo e($course->price); ?>

                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($course->status == 1): ?>
                                            <span id="hidden_id" style="display: none"><?php echo e($course->id); ?></span>
                                            <select name="status" class="status change-feature label-inline font-weight-bolder mb-1 badge badge-info">
                                                <option value="1" <?php if($course->is_featured == 1): ?> selected <?php endif; ?>><?php echo e(__('YES')); ?></option>
                                                <option value="0" <?php if($course->is_featured == 0): ?> selected <?php endif; ?>><?php echo e(__('NO')); ?></option>
                                            </select>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($course->status == 1): ?>
                                            <span class="status active"><?php echo e(__('Published')); ?></span>
                                            <?php elseif($course->status == 2): ?>
                                                <span class="status active"><?php echo e(__('Waiting for Review')); ?></span>
                                            <?php elseif($course->status == 3): ?>
                                                <span class="status blocked"><?php echo e(__('Hold')); ?></span>
                                            <?php elseif($course->status == 4): ?>
                                                <span class="status "><?php echo e(__('Draft')); ?></span>
                                            <?php elseif($course->status == 6): ?>
                                                <span class="status pending"><?php echo e(__('Upcoming Pending')); ?></span>
                                            <?php elseif($course->status == 7): ?>
                                                <span class="status upcoming"><?php echo e(__('Upcoming')); ?></span>
                                            <?php else: ?>
                                                <span class="status "><?php echo e(__('Pending')); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>

                                            <div class="">
                                             
                                                <a href="<?php echo e(route('admin.course.assign_resources', [$course->id])); ?>" title="Resources" class="btn btn-primary"><i class="fa fa-book"></i></a>
                                             
                                                <?php if($course->status == 1): ?>
                                                <a href="<?php echo e(route('admin.course.status-change', [$course->uuid, 3])); ?>" class="btn-action hold-btn mr-30" title="Make as Hold">
                                                    <?php echo e(__('Hold')); ?>

                                                </a>
                                                <?php elseif($course->status == 3 || $course->status == 0): ?>
                                                <a href="<?php echo e(route('admin.course.status-change', [$course->uuid, 1])); ?>" class="btn-action approve-btn mr-30" title="Make as Active">
                                                    <?php echo e(__('Approve')); ?>

                                                </a>
                                                <?php endif; ?>

                                                <a href="<?php echo e(route('admin.course.view', [$course->uuid])); ?>" class="btn-action mr-30" title="View Details">
                                                    <img src="<?php echo e(asset('admin/images/icons/eye-2.svg')); ?>" alt="eye">
                                                </a>
                                                <?php if($course->course_type == 1|| $course->course_type == 2): ?>
                                                <a target="_blank" href="<?php echo e(route('admin.course.edit', [$course->uuid])); ?>" class="btn-action mr-30" title="Edit Details">
                                                    <img src="<?php echo e(asset('admin/images/icons/edit.svg')); ?>" alt="edit">
                                                </a>
                                                <?php elseif($course->course_type == 3): ?> 
                                                <a  href="<?php echo e(route('admin.course.edit_standard', [$course->uuid])); ?>" class="btn-action mr-30" title="Edit Details">
                                                    <img src="<?php echo e(asset('admin/images/icons/edit.svg')); ?>" alt="edit">
                                                </a>
                                                 <?php endif; ?>
                                                <button class="btn-action ms-2 deleteItem" data-formid="delete_row_form_<?php echo e($course->uuid); ?>">
                                                    <img src="<?php echo e(asset('admin/images/icons/trash-2.svg')); ?>" alt="trash">
                                                </button>
                                              
                                                <form action="<?php echo e(route('admin.course.delete', [$course->uuid])); ?>" method="get" id="delete_row_form_<?php echo e($course->uuid); ?>">

                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <div class="mt-3">
                                <?php echo e($courses->links()); ?>

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



    
    <script>
         $(".change-feature").change(function () {
            var id = $(this).closest('tr').find('#hidden_id').html();
            var status_value = $(this).closest('tr').find('.status option:selected').val();
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
                        url: "<?php echo e(route('admin.course.feature-change')); ?>",
                        data: {"status": status_value, "id": id, "_token": "<?php echo e(csrf_token()); ?>",},
                        datatype: "json",
                        success: function (data) {
                            toastr.options.positionClass = 'toast-bottom-right';
                            toastr.success('', "<?php echo e(__('Feature has been updated')); ?>");
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/course/index.blade.php ENDPATH**/ ?>
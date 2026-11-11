<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Blog Comment')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Blog Comment')); ?></li>
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
                            <h2><?php echo e(__('Blog Comment List')); ?></h2>
                        </div>
                        <div class="customers__table">
                            <table id="customers-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('Blog')); ?></th>
                                    <th><?php echo e(__('User')); ?></th>
                                    <th><?php echo e(__('Comment')); ?></th>
                                    <th><?php echo e(__('Status')); ?></th>
                                    <th><?php echo e(__('Action')); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="removable-item">
                                        <td>
                                            <?php echo e(@$comment->blog->title); ?>

                                        </td>
                                        <td>
                                            <div class="admin-dashboard-blog-list-img">
                                                <img src="<?php echo e(asset(@$comment->user->image_path)); ?>" class="radius-3">
                                            </div>
                                            <div class="mt-1"><?php echo e(@$comment->user->name); ?></div>
                                        </td>

                                        <td><?php echo e($comment->comment); ?></td>
                                        <td>
                                            <span id="hidden_id" style="display: none"><?php echo e($comment->id); ?></span>
                                            <select name="status" class="status label-inline font-weight-bolder mb-1 badge badge-info">
                                                <option value="1" <?php if($comment->status == 1): ?> selected <?php endif; ?>><?php echo e(__('Active')); ?></option>
                                                <option value="0" <?php if($comment->status != 1): ?> selected <?php endif; ?>><?php echo e(__('Disabled')); ?></option>
                                            </select>
                                        </td>

                                        <td>
                                            <div class="action__buttons">
                                                <a href="javascript:void(0);" data-url="<?php echo e(route('blog.blogComment.delete', [$comment->id])); ?>" title="Delete" class="btn-action delete">
                                                    <img src="<?php echo e(asset('admin/images/icons/trash-2.svg')); ?>" alt="trash">
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <div class="mt-3">
                                <?php echo e(@$comments->links()); ?>

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
        'use strict'
        $(".status").change(function () {
            var id = $(this).closest('tr').find('#hidden_id').html();
            var status_value = $(this).closest('tr').find('.status option:selected').val();
            console.log(id, status_value)
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
                        url: "<?php echo e(route('blog.changeBlogCommentStatus')); ?>",
                        data: {"status": status_value, "id": id, "_token": "<?php echo e(csrf_token()); ?>",},
                        datatype: "json",
                        success: function (data) {
                            console.log(data);
                            toastr.success('', "<?php echo e(__('Blog Comment status has been updated')); ?>");
                        },
                        error: function () {
                            alert("Error!");
                        },
                    });
                } else if (result.dismiss === "cancel") {
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/blog/comment-list.blade.php ENDPATH**/ ?>
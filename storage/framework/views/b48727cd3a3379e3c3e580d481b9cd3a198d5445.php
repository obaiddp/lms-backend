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
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Approved Courses')); ?></li>
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
                            <h2>Hold Courses</h2>
                        </div>
                        <div class="customers__table all-course">
                            <table id="customers-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('Image')); ?></th>
                                    <th><?php echo e(__('Title')); ?></th>
                                    
                                    <th><?php echo e(__('Category')); ?></th>
                                    <th><?php echo e(__('Subcategory')); ?></th>
                                    <th><?php echo e(__('Price')); ?></th>
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
                                            <?php echo e($course->category ? $course->category->name : ''); ?>

                                        </td>
                                        <td>
                                            <?php echo e($course->subcategory ? $course->subcategory->name : ''); ?>

                                        </td>
                                        <td>
                                            <?php if(get_currency_placement() == 'after'): ?>
                                                <?php echo e($course->price); ?> <?php echo e(get_currency_symbol()); ?>

                                            <?php else: ?>
                                                <?php echo e(get_currency_symbol()); ?> <?php echo e($course->price); ?>

                                            <?php endif; ?>
                                        </td>
                                        <td>

                                            <div class="action__buttons">

                                                <a href="<?php echo e(route('admin.course.status-change', [$course->uuid, 1])); ?>" class="btn-action approve-btn mr-30" title="Make as Active">
                                                    <?php echo e(__('Approve')); ?>

                                                </a>

                                                <a href="<?php echo e(route('admin.course.view', [$course->uuid])); ?>" target="_blank" class="btn-action mr-30" title="View Details">
                                                    <img src="<?php echo e(asset('admin/images/icons/eye-2.svg')); ?>" alt="eye">
                                                </a>

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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/course/hold.blade.php ENDPATH**/ ?>
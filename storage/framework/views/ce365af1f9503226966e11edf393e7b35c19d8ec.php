<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Blogs')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Blogs')); ?></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="customers__area bg-style mb-30 admin-dashboard-blog-list-page">
                        <div class="item-title d-flex justify-content-between">
                            <h2><?php echo e(__('Blog List')); ?></h2>
                            <a href="<?php echo e(route('blog.create')); ?>" class="btn btn-success btn-sm"> <i class="fa fa-plus"></i> <?php echo e(__('Add Blog')); ?> </a>
                        </div>
                        <div class="customers__table">
                            <table id="customers-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('Image')); ?></th>
                                    <th><?php echo e(__('Title')); ?></th>
                                    <th><?php echo e(__('Category')); ?></th>
                                    <th><?php echo e(__('Status')); ?></th>
                                    <th><?php echo e(__('Name')); ?></th>
                                    <th><?php echo e(__('Action')); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $blogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $blog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="removable-item">
                                        <td>
                                            <div class="admin-dashboard-blog-list-img">
                                                <img src="<?php echo e(getImageFile($blog->image_path)); ?>" alt="img">
                                            </div>
                                        </td>
                                        <td>
                                            <?php echo e($blog->title); ?>

                                        </td>
                                        <td>
                                            <?php echo e($blog->category ? $blog->category->name : ''); ?>

                                        </td>
                                        <td>
                                            <?php if($blog->status == 1): ?>
                                                <span class="status bg-green"><?php echo e(__('Published')); ?></span>
                                            <?php else: ?>
                                                <span class="status bg-red"><?php echo e(__('Unpublished')); ?></span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?php echo e($blog->user ? $blog->user->name : ''); ?>

                                        </td>
                                        <td>
                                            <div class="action__buttons">
                                                <a href="<?php echo e(route('blog.edit', [$blog->uuid])); ?>" title="Edit" class="btn-action">
                                                    <img src="<?php echo e(asset('admin/images/icons/edit-2.svg')); ?>" alt="edit">
                                                </a>
                                                <a href="javascript:void(0);" data-url="<?php echo e(route('blog.delete', [$blog->uuid])); ?>" title="Delete" class="btn-action delete">
                                                    <img src="<?php echo e(asset('admin/images/icons/trash-2.svg')); ?>" alt="trash">
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <div class="mt-3">
                                <?php echo e(@$blogs->links()); ?>

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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/blog/index.blade.php ENDPATH**/ ?>
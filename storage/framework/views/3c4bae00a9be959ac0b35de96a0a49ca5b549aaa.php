<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__($title)); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Manage Product')); ?></li>
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
                            <h2><?php echo e(__('Review Courses')); ?></h2>
                        </div>
                        <div class="customers__table">
                            <table id="customers-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('Image')); ?></th>
                                    <th><?php echo e(__('Title')); ?></th>
                                    <th><?php echo e(__('Type')); ?></th>
                                    <th><?php echo e(__('Author')); ?></th>
                                    <th><?php echo e(__('Category')); ?></th>
                                    <th><?php echo e(__('Price')); ?></th>
                                    <th><?php echo e(__('Status')); ?></th>
                                    <th><?php echo e(__('Action')); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="removable-item">
                                        <td>
                                            <a href="#"> <img src="<?php echo e(getImageFile($product->thumbnail_path)); ?>" width="80"> </a>
                                        </td>
                                        <td>
                                            <?php echo e($product->title); ?>

                                        </td>
                                        <td>
                                            <?php echo e(getProductByType($product->type)); ?>

                                        </td>

                                        <td>
                                            <?php echo e($product->user ? $product->user->name : ''); ?>

                                        </td>
                                        <td>
                                            <?php echo e($product->category ? $product->category->name : ''); ?>

                                        </td>
                                        <td>
                                            <?php if(get_currency_placement() == 'after'): ?>
                                                <?php echo e($product->current_price); ?> <?php echo e(get_currency_symbol()); ?>

                                            <?php else: ?>
                                                <?php echo e(get_currency_symbol()); ?> <?php echo e($product->current_price); ?>

                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($product->status == STATUS_UPCOMING_REQUEST): ?>
                                            <span class="status pending"><?php echo e(__('Pending')); ?></span>
                                            <?php elseif($product->status == STATUS_UPCOMING_APPROVED): ?>
                                                <span class="status active"><?php echo e(__('Approved')); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>

                                            <div class="">
                                                <?php if($product->status == STATUS_UPCOMING_REQUEST): ?>
                                                <a href="<?php echo e(route('lms_product.admin.product.status-change', [$product->slug, STATUS_UPCOMING_APPROVED])); ?>" class="btn-action approve-btn mr-30" title="Make as Approve">
                                                    <?php echo e(__('Approve')); ?>

                                                </a>
                                                <?php else: ?>
                                                <a href="<?php echo e(route('lms_product.admin.product.status-change', [$product->slug, STATUS_UPCOMING_REQUEST])); ?>" class="btn-action approve-btn mr-30" title="Make as Pending">
                                                    <?php echo e(__('Pending')); ?>

                                                </a>
                                                <?php endif; ?>
                                                <a href="<?php echo e(route('lms_product.frontend.view', [$product->slug])); ?>" target="_blank" class="btn-action mr-30" title="View Details">
                                                    <img src="<?php echo e(asset('admin/images/icons/eye-2.svg')); ?>" alt="eye">
                                                </a>

                                                <button class="btn-action ms-2 deleteItem" data-formid="delete_row_form_<?php echo e($product->slug); ?>">
                                                    <img src="<?php echo e(asset('admin/images/icons/trash-2.svg')); ?>" alt="trash">
                                                </button>

                                                <form action="<?php echo e(route('lms_product.admin.product.delete', [$product->slug])); ?>" method="get" id="delete_row_form_<?php echo e($product->slug); ?>">

                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <div class="mt-3">
                                <?php echo e($products->links()); ?>

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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/addon/product/admin/product/upcoming.blade.php ENDPATH**/ ?>
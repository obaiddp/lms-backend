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
                                        <li class="breadcrumb-item active" aria-current="page"><?php echo e(__($title)); ?></li>
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
                                <h2><?php echo e(__('Product Category List')); ?></h2>
                                <a href="<?php echo e(route('lms_product.admin.category.create')); ?>" class="btn btn-success btn-sm"> <i class="fa fa-plus"></i> <?php echo e(__('Add Category')); ?> </a>
                            </div>
                            <div class="customers__table">
                                <table id="customers-table" class="row-border data-table-filter table-style">
                                    <thead>
                                    <tr>
                                        <th><?php echo e(__('Image')); ?></th>
                                        <th><?php echo e(__('Name')); ?></th>
                                        <th><?php echo e(__('Feature')); ?></th>
                                        <th><?php echo e(__('Total Product')); ?></th>
                                        <th><?php echo e(__('Action')); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $__currentLoopData = $productCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $productCategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="removable-item">
                                        <td>
                                            <div class="user-info">
                                                <div class="user-info__img">
                                                    <img src="<?php echo e(getImageFile($productCategory->image_path)); ?>" alt="category">
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php echo e($productCategory->name); ?>

                                        </td>
                                        <td>
                                            <?php if($productCategory->is_feature == 'yes'): ?>
                                                <span class="status active"><?php echo e(__('Yes')); ?></span>
                                            <?php else: ?>
                                                <span class="status blocked"><?php echo e(__('No')); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo e($productCategory->products?->count()); ?>

                                        </td>
                                        <td>
                                            <div class="action__buttons">
                                                <a href="<?php echo e(route('lms_product.admin.category.edit', [$productCategory->slug])); ?>" class="btn-action" title="Edit">
                                                    <img src="<?php echo e(asset('admin/images/icons/edit-2.svg')); ?>" alt="edit">
                                                </a>
                                                <button class="btn-action ms-2 deleteItem" data-formid="delete_row_form_<?php echo e($productCategory->slug); ?>">
                                                    <img src="<?php echo e(asset('admin/images/icons/trash-2.svg')); ?>" alt="trash">
                                                </button>

                                                <form action="<?php echo e(route('lms_product.admin.category.delete', [$productCategory->slug])); ?>" method="get" id="delete_row_form_<?php echo e($productCategory->slug); ?>">

                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                                <div class="mt-3">
                                    <?php echo e($productCategories->links()); ?>

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
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/jquery.dataTables.min.css')); ?>" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script src="<?php echo e(asset('admin/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/custom/data-table-page.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/addon/product/admin/category/index.blade.php ENDPATH**/ ?>
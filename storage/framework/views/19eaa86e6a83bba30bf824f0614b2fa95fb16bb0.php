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
                            <h2><?php echo e(__($title)); ?></h2>
                            <a  href="<?php echo e(route('lms_product.admin.product.create',['step'=>'step-1'])); ?>" class="btn btn-blue mr-30" title="Add  Product">
                               <?php echo e(__('Add Product')); ?>

                            </a>
                        </div>
                        <div class="customers__table all-product">
                            <table id="customers-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('Image')); ?></th>
                                    <th><?php echo e(__('Title')); ?></th>
                                    <th><?php echo e(__('Type')); ?></th>
                                    <th><?php echo e(__('Author')); ?></th>
                                    <th><?php echo e(__('Category')); ?></th>
                                    <th><?php echo e(__('Price')); ?></th>
                                    <th><?php echo e(__('Featured')); ?></th>
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
                                            <a href="<?php echo e(route('lms_product.frontend.view', [$product->slug])); ?>" class="btn-action mr-30" title="View Details">     
                                            <?php echo e($product->title); ?>

                                            </a>
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
                                            <?php if($product->status == 1): ?>
                                            <span id="hidden_id" style="display: none"><?php echo e($product->id); ?></span>
                                            <select name="status" class="status change-feature label-inline font-weight-bolder mb-1 badge badge-info">
                                                <option value="1" <?php if($product->is_feature == 1): ?> selected <?php endif; ?>><?php echo e(__('YES')); ?></option>
                                                <option value="0" <?php if($product->is_feature == 0): ?> selected <?php endif; ?>><?php echo e(__('NO')); ?></option>
                                            </select>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($product->status == 1): ?>
                                            <span class="status active"><?php echo e(__('Published')); ?></span>
                                            <?php elseif($product->status == 2): ?>
                                                <span class="status active"><?php echo e(__('Waiting for Review')); ?></span>
                                            <?php elseif($product->status == 3): ?>
                                                <span class="status blocked"><?php echo e(__('Hold')); ?></span>
                                            <?php elseif($product->status == 4): ?>
                                                <span class="status "><?php echo e(__('Draft')); ?></span>
                                            <?php elseif($product->status == 6): ?>
                                                <span class="status pending"><?php echo e(__('Upcoming Pending')); ?></span>
                                            <?php elseif($product->status == 7): ?>
                                                <span class="status upcoming"><?php echo e(__('Upcoming')); ?></span>
                                            <?php else: ?>
                                                <span class="status "><?php echo e(__('Pending')); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>

                                            <div class="">
                                                <?php if($product->status == 1): ?>
                                                <a href="<?php echo e(route('lms_product.admin.product.status-change', [$product->slug, 3])); ?>" class="btn-action hold-btn mr-30" title="Make as Hold">
                                                    <?php echo e(__('Hold')); ?>

                                                </a>
                                                <?php elseif($product->status == 3): ?>
                                                <a href="<?php echo e(route('lms_product.admin.product.status-change', [$product->slug, 1])); ?>" class="btn-action approve-btn mr-30" title="Make as Active">
                                                    <?php echo e(__('Approve')); ?>

                                                </a>
                                                <?php endif; ?>

                                                <a href="<?php echo e(route('lms_product.frontend.view', [$product->slug])); ?>" class="btn-action mr-30" title="View Details">
                                                    <img src="<?php echo e(asset('admin/images/icons/eye-2.svg')); ?>" alt="eye">
                                                </a>
                                                <a href="<?php echo e(route('lms_product.admin.product.edit', [$product->slug])); ?>" class="btn-action mr-30" title="Edit Product">
                                                    <img src="<?php echo e(asset('admin/images/icons/edit.svg')); ?>" alt="edit">
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
    <script>
        (function ($) {
        "use strict";

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
                            url: "<?php echo e(route('lms_product.admin.product.feature-change')); ?>",
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
            
        })(jQuery)
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/addon/product/admin/product/index.blade.php ENDPATH**/ ?>
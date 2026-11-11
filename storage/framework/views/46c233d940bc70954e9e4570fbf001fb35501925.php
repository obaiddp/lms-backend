<?php $__env->startPush('style'); ?>
<link rel="stylesheet" href="<?php echo e(asset('addon/product/css/ecommerce-product.css')); ?>">
<?php $__env->stopPush(); ?>
<?php $__env->startSection('breadcrumb'); ?>
<div class="page-banner-content text-center">
    <h3 class="page-banner-heading text-white pb-15"> <?php echo e(__($title)); ?> </h3>

    <!-- Breadcrumb Start-->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb justify-content-center">
            <li class="breadcrumb-item font-14"><a href="<?php echo e(route('instructor.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a>
            </li>
            <li class="breadcrumb-item font-14 active" aria-current="page"><?php echo e(__($title)); ?></li>
        </ol>
    </nav>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content2'); ?>
<div>
    <div class="upload-product">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h6 class="dashboard-title"> <?php echo e(__('Order History')); ?></h6>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="menu-products table-responsive">
                        <table class="table table-size-2">
                            <thead>
                                <tr>
                                    <th scope="col"><?php echo e(__("Image")); ?></th>
                                    <th scope="col"><?php echo e(__("Product")); ?></th>
                                    <th scope="col"><?php echo e(__("Type")); ?></th>
                                    <th scope="col"><?php echo e(__("Category")); ?></th>
                                    <th scope="col"><?php echo e(__("Amount")); ?></th>
                                    <th scope="col"><?php echo e(__("Student")); ?></th>
                                    <th scope="col"><?php echo e(__("Status")); ?></th>
                                    <th scope="col"><?php echo e(__("Action")); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $orderItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $orderItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td scope="row">
                                        <img class="products-img-list" src="<?php echo e(getImageFile($orderItem->product->thumbnail_path)); ?>" alt="<?php echo e($orderItem->product->title); ?>">
                                    </td>
                                    <td><?php echo e($orderItem->product->title); ?></td>
                                    <td><?php echo e(getProductByType($orderItem->product->type)); ?></td>
                                    <td><?php echo e($orderItem->product->category->name); ?></td>
                                    <td>
                                        <?php if(get_currency_placement() == 'after'): ?>
                                            <?php echo e($orderItem->unit_price); ?> <?php echo e(get_currency_symbol()); ?>

                                        <?php else: ?>
                                            <?php echo e(get_currency_symbol()); ?> <?php echo e($orderItem->unit_price); ?>

                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo e($orderItem->user->name); ?>

                                    </td>
                                    <td>
                                        <?php if($orderItem->delivery_status == 0): ?>
                                        <span class="product-btu pending-status"><?php echo e(__('Pending')); ?></span>
                                        <?php elseif($orderItem->delivery_status == 1): ?>
                                        <span class="product-btu stock-btu"><?php echo e(__('Processing')); ?></span>
                                        <?php elseif($orderItem->delivery_status == 2): ?>
                                        <span class="product-btu stock-btu"><?php echo e(__('Shipped')); ?></span>
                                        <?php elseif($orderItem->delivery_status == 3): ?>
                                        <span class="product-btu stock-btu"><?php echo e(__('Delivered')); ?></span>
                                        <?php elseif($orderItem->delivery_status == 4): ?>
                                        <span class="product-btu out-stock-btu"><?php echo e(__('Cancelled')); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if(!in_array($orderItem->delivery_status, [3,4])): ?>
                                        <div class="mean-all-icon">
                                            <div class="icon-view-list status-parent">
                                                <select name="delivery_status" <?php echo e(in_array($orderItem->delivery_status, [3,4]) ? 'disabled' : ''); ?> data-id="<?php echo e($orderItem->id); ?>">
                                                    <option <?php echo e($orderItem->delivery_status == 0 ? 'selected' : ''); ?> value="0"><?php echo e(__('Pending')); ?></option>
                                                    <option <?php echo e($orderItem->delivery_status == 1 ? 'selected' : ''); ?> value="1"><?php echo e(__('Processing')); ?></option>
                                                    <option <?php echo e($orderItem->delivery_status == 2 ? 'selected' : ''); ?> value="2"><?php echo e(__('Shipped')); ?></option>
                                                    <option <?php echo e($orderItem->delivery_status == 3 ? 'selected' : ''); ?> value="3"><?php echo e(__('Delivered')); ?></option>
                                                    <option <?php echo e($orderItem->delivery_status == 4 ? 'selected' : ''); ?> value="4"><?php echo e(__('Cancelled')); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td class="text-center" colspan="7">
                                        <?php echo e(__('No Data Found')); ?>

                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-12">
                    <?php if(@$orderItems->hasPages()): ?>
                        <?php echo e(@$orderItems->links('frontend.paginate.paginate')); ?>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalStock" tabindex="-1" aria-labelledby="modalStockLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="model-area-box">
                <h4 class="model-title"><?php echo e(__('Add new Stock')); ?></h4>
                <form method="POST" action="<?php echo e(route('lms_product.instructor.product.add-stock')); ?>" enctype="multipart/form-data" data-handler="getStockResponse" class="ajax row g-3 needs-validation">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3 quantity-area">
                        <h6><?php echo e(__('Number of Quantity')); ?></h6>
                        <input type="hidden" name="slug" value="">
                        <input type="number" min=1 name="quantity">
                    </div>
                    <button type="submit" class="theme-btn theme-button2 load-more-btn"><?php echo e(__('Update')); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
<script src="<?php echo e(asset('common/js/select2.min.js')); ?>"></script>
<script>
    (function ($) {
        "use strict";
        $(document).on('change', ':input[name=delivery_status]', function() {
            var id = $(this).data('id');
            var delivery_status = $(this).val();
            Swal.fire({
                title: "<?php echo e(__('Are you sure?')); ?>",
                text: "<?php echo e(__('Student will get notified')); ?>",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, change it!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        type: 'POST',
                        data: {'id' : id, 'delivery_status' : delivery_status, "_token": "<?php echo e(csrf_token()); ?>",},
                        url: "<?php echo e(route('lms_product.admin.product.orders.status_change')); ?>",
                        success: function (response) {
                            toastr.success(response.message);
                            location.reload();
                        }
                    })
                }
            })
        });
    })(jQuery)

</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.instructor', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/addon/product/admin/product/orders.blade.php ENDPATH**/ ?>
<?php $__env->startPush('style'); ?>
<link rel="stylesheet" href="<?php echo e(asset('addon/product/css/ecommerce-product.css')); ?>">
<?php $__env->stopPush(); ?>
<?php $__env->startSection('breadcrumb'); ?>
<div class="page-banner-content text-center">
    <h3 class="page-banner-heading text-white pb-15"> <?php echo e(__($title)); ?> </h3>

    <!-- Breadcrumb Start-->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb justify-content-center">
            <li class="breadcrumb-item font-14"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a>
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
                    <h6 class="dashboard-title"> <?php echo e(__('Course Order History')); ?></h6>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="menu-products table-responsive">
                        <table class="table table-size-2">
                            <thead>
                                <tr>
                                    <th scope="col"><?php echo e(__("Image")); ?></th>
                                    <th scope="col"><?php echo e(__("Order No")); ?></th>
                                    <th scope="col"><?php echo e(__("Course")); ?></th>
                                    <th scope="col"><?php echo e(__("Student")); ?></th>
                                    <th scope="col"><?php echo e(__("Type")); ?></th>
                                    <th scope="col"><?php echo e(__("Amount")); ?></th>
                                
                                    <th scope="col"><?php echo e(__("Status")); ?></th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $orderItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $orderItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td scope="row">
                                        <img class="products-img-list" src="<?php echo e(getImageFile($orderItem->course?->image_path)); ?>" alt="<?php echo e($orderItem->course?->title); ?>">
                                    </td>
                                    <td><?php echo e($orderItem->order?->order_number); ?></td>
                                    <td>
                                        <?php if($orderItem->course): ?>
                                            <a href="<?php echo e(route('admin.course.view', [$orderItem->course->uuid])); ?>" class="btn-action mr-30" title="View Details">
                                            <?php echo e($orderItem->course?->title); ?>

                                            </a>
                                        <?php endif; ?>
                                     
                                    </td>
                                    <td>
                                        <?php echo e($orderItem->user->name); ?>

                                    </td>
                                    <td>
                                        <?php if($orderItem->course): ?>
                                            <?php if($orderItem->course->course_type = 1): ?>
                                                <?php echo e("General"); ?>

                                            <?php elseif($orderItem->course->course_type = 2): ?>
                                                <?php echo e("Scorm"); ?>

                                            <?php elseif($orderItem->course->course_type = 3): ?>
                                                <?php echo e("Live"); ?>

                                            <?php endif; ?>
                                        <?php endif; ?>
                                      
                                    </td>
                                    
                                    <td>
                                        <?php if(get_currency_placement() == 'after'): ?>
                                            <?php echo e($orderItem->unit_price); ?> <?php echo e(get_currency_symbol()); ?>

                                        <?php else: ?>
                                            <?php echo e(get_currency_symbol()); ?> <?php echo e($orderItem->unit_price); ?>

                                        <?php endif; ?>
                                    </td>
                              
                                    <td>
                                        
                                            
                                                  <?php if($orderItem->order?->payment_status == 'due'): ?>
                                                  <span class="product-btu pending-status"><?php echo e(__('Due')); ?></span>
                                                  <?php elseif($orderItem->order?->payment_status == 'paid'): ?>
                                                  <span class="product-btu stock-btu"><?php echo e(__('Paid')); ?></span>
                                                  <?php elseif($orderItem->order?->payment_status == 'free'): ?>
                                                  <span class="product-btu stock-btu"><?php echo e(__('Free')); ?></span>
                                              
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
<?php echo $__env->make('layouts.instructor', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/course/orders.blade.php ENDPATH**/ ?>
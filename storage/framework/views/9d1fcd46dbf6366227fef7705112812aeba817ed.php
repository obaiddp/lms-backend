<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Affiliation settings')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__(@$title)); ?></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="customers__area bg-style mb-30">

                        <!-- Affiliation Settings Content Start -->
                        <div class="add-product__area bg-style">
                            <form action="<?php echo e(route('affiliate.referral-settings.update')); ?>" method="post" class="form-horizontal">
                                <?php echo csrf_field(); ?>
                                <div class="row mb-45">
                                    <div class="add-product__title">
                                        <h2><?php echo e(__(@$title)); ?></h2>
                                    </div>

                                    <div class="col-md-6 col-lg-4">
                                        <div class="input__group mb-25">
                                            <label><?php echo e(__('Allow public affiliator')); ?></label>
                                            <select name="referral_status" id="allow_affiliator">
                                                <option value="1" <?php if(get_option('referral_status') == 1): ?> selected <?php endif; ?>><?php echo e(__('Yes')); ?></option>
                                                <option value="0" <?php if(get_option('referral_status') != 1): ?> selected <?php endif; ?>><?php echo e(__('No')); ?></option>
                                            </select>
                                        </div>
                                    </div>










                                    <div class="col-md-6 col-lg-4">
                                        <div class="input-group mb-3">
                                            <label><?php echo e(__('Affiliate commission percentage')); ?></label>
                                            <div class="input-group">
                                                <input  type="number" placeholder="7.5" min="0" step="0.01" title="Percentage" pattern="^\d+(?:\.\d{1,2})?$" name="referral_commission_percentage" value="<?php echo e(get_option('referral_commission_percentage')); ?>" class="form-control" required>
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="add-product__button mt-5">



                                            <button type="submit" class="btn btn-blue float-right"><?php echo e(__('Save changes')); ?></button>

                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- Affiliation Settings Content End -->

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


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/affiliate/affiliation-settings.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Withdraw Request')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Withdraw Request')); ?></li>
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
                            <h2><?php echo e(__('Withdraw Request')); ?></h2>
                        </div>
                        <div class="customers__table">
                            <table id="customers-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('Transaction ID')); ?></th>
                                    <th><?php echo e(__('User')); ?></th>
                                    <th><?php echo e(__('Payment Method')); ?></th>
                                    <th><?php echo e(__('Request Date')); ?></th>
                                    <th><?php echo e(__('Amount')); ?></th>
                                    <th><?php echo e(__('Action')); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $withdraws; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $withdraw): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($withdraw->user): ?>
                                        <tr class="removable-item">
                                        <td><?php echo e($withdraw->transection_id); ?></td>
                                        <td>
                                            <div class="finance-table-inner-item my-2">
                                                <span class="fw-bold mr-1"><?php echo e(__('Name')); ?></span>: <?php echo e(@$withdraw->user->student->name ?? @$withdraw->user->instructor->name); ?>

                                            </div>
                                            <div class="finance-table-inner-item my-2">
                                                <span class="fw-bold mr-1"><?php echo e(__('Email')); ?></span>: <?php echo e(@$withdraw->user->email); ?>

                                            </div>
                                            <div class="finance-table-inner-item my-2">
                                                <span class="fw-bold mr-1"><?php echo e(__('Phone')); ?></span>: <?php echo e(@$withdraw->user->student->phone_number ?? @$withdraw->user->instructor->phone_number); ?>

                                            </div>
                                            <div class="finance-table-inner-item my-2">
                                                <span class="fw-bold mr-1"><?php echo e(__('User Type')); ?></span>:
                                                <?php if(@$withdraw->user->role == 2 && @$withdraw->user->is_affiliator == 1): ?>
                                                    <?php echo e(__('Instructor & Affiliator')); ?>

                                                <?php elseif(@$withdraw->user->role == 3 && @$withdraw->user->is_affiliator == 1): ?>
                                                    <?php echo e(__('Student & Affiliator')); ?>

                                                <?php elseif(@$withdraw->user->role == 2): ?>
                                                    <?php echo e(__('Instructor')); ?>

                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php echo getBeneficiaryDetails($withdraw->beneficiary); ?>

                                        </td>
                                        <td>
                                            <?php echo e($withdraw->created_at->format(get_option('app_date_format'))); ?>

                                        </td>
                                        <td>
                                            <?php if(get_currency_placement() == 'after'): ?>
                                                <?php echo e($withdraw->amount); ?> <?php echo e(get_currency_symbol()); ?>

                                            <?php else: ?>
                                                <?php echo e(get_currency_symbol()); ?> <?php echo e($withdraw->amount); ?>

                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="action__buttons">
                                                <a href="javascript:void(0);" data-uuid="<?php echo e($withdraw->uuid); ?>" data-status="2" class="btn-action hold-btn mr-30 note" title="Make as Reject">
                                                    <?php echo e(__('Reject')); ?></span>
                                                </a>
                                                <a href="javascript:void(0);" data-uuid="<?php echo e($withdraw->uuid); ?>" data-status="1" class="btn-action approve-btn note" title="Make as Complete">
                                                    <?php echo e(__('Complete')); ?>

                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>

                        </div>
                        <?php echo e($withdraws->links()); ?>

                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Page content area end -->

    <!--Withdrawal Modal Start-->
    <div class="modal fade" id="withdrawalModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" >
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h4>Write Note</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="<?php echo e(route('payout.change-withdraw-status')); ?>" method="post" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="uuid" id="uuid">
                        <input type="hidden" name="status" id="status">
                        <div class="custom-form-group mb-3 row">
                            <div class="col-lg-12">
                                <textarea name="note" id="note" required class="form-control" placeholder="<?php echo e(__('Note')); ?>"></textarea>
                            </div>
                        </div>

                        <div class="custom-form-group mb-3 row">
                            <div class="col-lg-12 text-right">
                                <button type="submit" class="btn btn-blue mr-30"><?php echo e(__('Save')); ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Withdrawal Modal End-->

<?php $__env->stopSection(); ?>

<?php $__env->startPush('style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/jquery.dataTables.min.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script src="<?php echo e(asset('admin/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/custom/data-table-page.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/custom/withdraw.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/payout/new-withdraw.blade.php ENDPATH**/ ?>
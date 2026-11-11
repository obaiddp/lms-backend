<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Complete Withdrawal')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Complete Withdrawal')); ?></li>
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
                            <h2><?php echo e(__('Complete Withdrawal')); ?></h2>
                        </div>
                        <div class="customers__table">
                            <table id="customers-table" class="row-border data-table-filter table-style admin-complete-withdraw-table">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('Transaction ID')); ?></th>
                                    <th><?php echo e(__('User')); ?></th>
                                    <th><?php echo e(__('Payment Method')); ?></th>
                                    <th><?php echo e(__('Request Date')); ?></th>
                                    <th><?php echo e(__('Note')); ?></th>
                                    <th><?php echo e(__('Amount')); ?></th>
                                    <th><?php echo e(__('Status')); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $withdraws; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $withdraw): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($withdraw->user): ?>
                                        <tr class="removable-item">
                                            <td><?php echo e($withdraw->transection_id); ?></td>
                                            <td>
                                                <div class="finance-table-inner-item my-2">
                                                    <span class="fw-bold mr-1"><?php echo e(__('Name')); ?></span>: <?php echo e(@$withdraw->user->student->name ?? $withdraw->user->instructor->name); ?>

                                                </div>
                                                <div class="finance-table-inner-item my-2">
                                                    <span class="fw-bold mr-1"><?php echo e(__('Email')); ?></span>: <?php echo e(@$withdraw->user->email); ?>

                                                </div>
                                                <div class="finance-table-inner-item my-2">
                                                    <span class="fw-bold mr-1"><?php echo e(__('Phone')); ?></span>: <?php echo e(@$withdraw->user->student->phone_number ?? $withdraw->user->instructor->phone_number); ?>

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

                                                <?php if($withdraw->payment_method == 'buy'): ?>
                                                    <div class="finance-table-inner-item my-2">
                                                        <span class="fw-bold mr-1"><?php echo e(__('Payment Method')); ?></span>: <?php echo e(__('Buy Course')); ?>

                                                    </div>
                                                <?php else: ?>
                                                    <?php echo getBeneficiaryDetails($withdraw->beneficiary); ?>

                                                <?php endif; ?>

                                            </td>
                                            <td>
                                                <?php echo e($withdraw->created_at->format(get_option('app_date_format'))); ?>

                                            </td>
                                            <td class="complete-withdrawal-note-box">
                                                <?php echo e(\Illuminate\Support\Str::words($withdraw->note, 3)); ?><button type="button" class="mx-2 view-note" title="View Details" data-note="<?php echo e($withdraw->note); ?>" ><img src="<?php echo e(asset('admin/images/icons/eye-2.svg')); ?>" alt="eye"></button>
                                            </td>
                                            <td>
                                                <?php if(get_currency_placement() == 'after'): ?>
                                                    <?php echo e($withdraw->amount); ?> <?php echo e(get_currency_symbol()); ?>

                                                <?php else: ?>
                                                    <?php echo e(get_currency_symbol()); ?> <?php echo e($withdraw->amount); ?>

                                                <?php endif; ?>

                                            </td>
                                            <td>
                                                <span class="status active"><?php echo e(__('Complete')); ?></span>
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

    <div class="modal fade" id="rejectedNoteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h4><?php echo e(__('View Note')); ?></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-30 note"></p>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/jquery.dataTables.min.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script src="<?php echo e(asset('admin/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/custom/data-table-page.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/custom/withdraw.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/payout/complete-withdraw.blade.php ENDPATH**/ ?>
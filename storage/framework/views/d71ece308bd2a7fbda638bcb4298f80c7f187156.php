<?php $__env->startSection('content'); ?>
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Student Fee Ledger')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Student Fee Ledger')); ?></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="customers__area bg-style mb-30">
                        <div class="item-title d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h2><?php echo e(__('Student Fee Ledger')); ?></h2>
                            <?php if($user_id && $student): ?>
                                <a href="<?php echo e(route('report.student-fee-ledger.export', ['user_id' => $user_id, 'months' => $months])); ?>"
                                   class="btn btn-sm btn-success">
                                    <?php echo e(__('Export Excel')); ?>

                                </a>
                            <?php endif; ?>
                        </div>

                        <form method="get" action="<?php echo e(route('report.student-fee-ledger')); ?>" class="mb-4">
                            <div class="row align-items-end">
                                <div class="col-md-5">
                                    <div class="input__group mb-25">
                                        <label class="label-text-title color-heading font-medium font-14 mb-3">
                                            <?php echo e(__('Student')); ?> <span class="text-danger">*</span>
                                        </label>
                                        <select name="user_id" id="user_id" class="form-select" required>
                                            <option value=""><?php echo e(__('Select Student')); ?></option>
                                            <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $label = trim($s->name);
                                                    if ($s->user) {
                                                        $extra = collect([$s->user->email ?? null, $s->phone_number ?? $s->user->phone_number ?? null])
                                                            ->filter()
                                                            ->implode(' · ');
                                                        if ($extra) {
                                                            $label .= ' (' . $extra . ')';
                                                        }
                                                    }
                                                ?>
                                                <option value="<?php echo e($s->user_id); ?>" <?php echo e((int) $user_id === (int) $s->user_id ? 'selected' : ''); ?>>
                                                    <?php echo e($label); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input__group mb-25">
                                        <label class="label-text-title color-heading font-medium font-14 mb-3">
                                            <?php echo e(__('Past months')); ?>

                                        </label>
                                        <select name="months" id="months" class="form-select">
                                            <?php $__currentLoopData = [1, 3, 6, 12, 24]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($m); ?>" <?php echo e((int) $months === $m ? 'selected' : ''); ?>>
                                                    <?php echo e($m); ?> <?php echo e(__('months')); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 mb-25">
                                    <button type="submit" class="theme-btn theme-button1 default-hover-btn w-100">
                                        <?php echo e(__('Filter')); ?>

                                    </button>
                                </div>
                            </div>
                        </form>

                        <?php if($user_id && !$student): ?>
                            <div class="alert alert-warning"><?php echo e(__('Student not found.')); ?></div>
                        <?php elseif(!$user_id): ?>
                            <p class="text-muted mb-0"><?php echo e(__('Select a student and click Filter to view their fee ledger.')); ?></p>
                        <?php else: ?>
                            <div class="row mb-4">
                                <div class="col-lg-3 col-md-6 col-sm-6">
                                    <div class="status__box status__box__v3 bg-style">
                                        <div class="status__box__text">
                                            <h2 class="color-red"><?php echo e($totals['total_due']); ?></h2>
                                            <h3><?php echo e(__('Total Due')); ?></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-6">
                                    <div class="status__box status__box__v3 bg-style">
                                        <div class="status__box__text">
                                            <h2 class="color-green"><?php echo e($totals['total_paid']); ?></h2>
                                            <h3><?php echo e(__('Total Paid')); ?></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-6">
                                    <div class="status__box status__box__v3 bg-style">
                                        <div class="status__box__text">
                                            <h2 class="color-blue"><?php echo e($totals['total_rows']); ?></h2>
                                            <h3><?php echo e(__('Rows')); ?></h3>
                                        </div>
                                    </div>
                                </div>
                                <?php if($since): ?>
                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="status__box status__box__v3 bg-style">
                                            <div class="status__box__text">
                                                <h2 class="font-16"><?php echo e($since->format('d M Y')); ?></h2>
                                                <h3><?php echo e(__('From date')); ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if($student): ?>
                                <p class="mb-3">
                                    <strong><?php echo e(__('Student')); ?>:</strong>
                                    <?php echo e($student->name); ?>

                                    <?php if($student->email): ?> — <?php echo e($student->email); ?> <?php endif; ?>
                                </p>
                            <?php endif; ?>

                            <div class="table-responsive">
                                <table class="row-border data-table-filter table-style admin-order-report-table w-100">
                                    <thead>
                                        <tr>
                                            <th><?php echo e(__('Date')); ?></th>
                                            <th><?php echo e(__('Source')); ?></th>
                                            <th><?php echo e(__('Type')); ?></th>
                                            <th><?php echo e(__('Course')); ?></th>
                                            <th><?php echo e(__('Amount')); ?></th>
                                            <th><?php echo e(__('Status')); ?></th>
                                            <th><?php echo e(__('Method')); ?></th>
                                            <th><?php echo e(__('Order #')); ?></th>
                                            <th><?php echo e(__('Transaction #')); ?></th>
                                            <th><?php echo e(__('Period')); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr>
                                                <td><?php echo e($row->event_at ? \Carbon\Carbon::parse($row->event_at)->format('d-m-Y H:i') : '—'); ?></td>
                                                <td><?php echo e($row->ledger_source); ?></td>
                                                <td><?php echo e($row->fee_type); ?></td>
                                                <td><?php echo e($row->course_title ?: '—'); ?></td>
                                                <td><?php echo e($row->amount); ?></td>
                                                <td><?php echo e($row->payment_status); ?></td>
                                                <td><?php echo e($row->payment_method ?: '—'); ?></td>
                                                <td><?php echo e($row->order_number ?: '—'); ?></td>
                                                <td><?php echo e($row->transaction_number ?: '—'); ?></td>
                                                <td>
                                                    <?php if($row->month && $row->year): ?>
                                                        <?php echo e($row->month); ?>/<?php echo e($row->year); ?>

                                                    <?php else: ?>
                                                        —
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="10" class="text-center"><?php echo e(__('No fee records found for this period.')); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
    <script>
        $(document).ready(function () {
            $('#user_id').select2({ width: '100%', placeholder: "<?php echo e(__('Select Student')); ?>" });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/report/student-fee-ledger.blade.php ENDPATH**/ ?>
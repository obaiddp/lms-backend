<div class="customers__table">
    <table id="pending-payments-table" class="row-border data-table-filter table-style">
        <thead>
        <tr>
            <th><?php echo e(__('ID')); ?></th>
            <th><?php echo e(__('Student')); ?></th>
            <th><?php echo e(__('Course')); ?></th>
            <th><?php echo e(__('No. Of Students')); ?></th>
            <th><?php echo e(__('Amount')); ?></th>
            <th><?php echo e(__('Status')); ?></th>
            <th><?php echo e('Issue Date'); ?></th>
            <th><?php echo e(__('Month')); ?></th>
            <th><?php echo e(__('Year')); ?></th>
            <th><?php echo e(__('Action')); ?></th>
        </tr>
        </thead>
        <tbody>
            <?php
                $i = 0;
            ?>
        <?php $__currentLoopData = $pendingPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="removable-item">
                <td>
                    <?php echo e(++$i); ?>

                </td>
                <td>
                  
                    <?php echo e($payment->user?->name); ?>

                </td>
                <td>
                    <?php if($payment->course): ?>
                        <a href="<?php echo e(route('admin.course.view', [$payment->course?->uuid])); ?>" class="btn-action mr-30" title="View Details">
                        <?php echo e($payment->course->title); ?>

                        </a>
                    <?php endif; ?>
                </td>
                <td>
                    <?php echo e($payment->students_count); ?>

                </td>
                <td>
                    <?php echo e(get_currency_symbol()); ?> <?php echo e($payment->price); ?>

                </td>
              
                <td>
                    <button class="btn btn-danger btn-sm" title="Unpaid">UnPaid</button>
                    <a style="margin-top: 10px;" href="<?php echo e(route('student_payment.change_status', [$payment->id])); ?>" class="btn btn-info btn-sm" title="Change Status" ><?php echo e(__('Change Status')); ?> </a>
                </td>
                <td>
                    <?php echo e($payment->created_at ? $payment->created_at->toDateTimeString() : null); ?>

                </td>
                <td>
                    <?php
                    $monthName = \Carbon\Carbon::create()->month($payment->month)->format('F');
                    // or use 'M' for short month name like 'Aug'
                    $shortMonthName = \Carbon\Carbon::create()->month($payment->month)->format('M');
                ?>
                    <?php echo e($shortMonthName); ?>

                </td>
                <td>
                    <?php echo e($payment->year); ?>

                </td>
                <td>
                    <div class="action__buttons">
                        <a href="<?php echo e(route('student_payment.edit_payment', [$payment->id])); ?>" class="btn-action mr-30" title="Edit Details">
                            <img src="<?php echo e(asset('admin/images/icons/edit-2.svg')); ?>" alt="edit">
                        </a>
                        <button class="ms-3">
                            <span data-formid="delete_row_form_<?php echo e($payment->id); ?>" class="deleteItem">
                                <img src="<?php echo e(asset('admin/images/icons/trash-2.svg')); ?>" alt="trash">
                            </span>
                        </button>

                        <form action="<?php echo e(route('student_payment.delete_payment', [$payment->id])); ?>" method="post" id="delete_row_form_<?php echo e($payment->id); ?>">
                            <?php echo e(method_field('DELETE')); ?>

                            <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                        </form>


                     

                    </div>
                    <div class="action__buttons">
                           <?php if($payment->invoice_pdf_path): ?>
                            <a href="<?php echo e(route('admin.cart.download.voucher', $payment->id)); ?>"
                               class="btn btn-sm btn-success mt-1" target="_blank">
                                <?php echo e(__('Download PDF')); ?>

                            </a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <div class="mt-3">
        <?php echo e($pendingPayments->links()); ?>

    </div>
</div><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/student_payment/table-data.blade.php ENDPATH**/ ?>
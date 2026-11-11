<div class="customers__table">
    <table class="row-border table-style w-100">
        <thead>
            <tr>
                <th><?php echo e(__('Student')); ?></th>
                <th><?php echo e(__('Course')); ?></th>
                <th><?php echo e(__('Issue Date')); ?></th>
                <th><?php echo e(__('Days Overdue')); ?></th>
                <th><?php echo e(__('Amount')); ?></th>
                <th><?php echo e(__('Action')); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $overdues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($row['student_name']); ?></td>
                    <td><?php echo e($row['course_name']); ?></td>
                    <td><?php echo e($row['issue_date']); ?></td>
                    <td><?php echo e($row['days_overdue']); ?></td>
                    <td>
                        <?php if(get_currency_placement() == 'after'): ?>
                            <?php echo e(number_format($row['amount'], 0)); ?> <?php echo e(get_currency_symbol()); ?>

                        <?php else: ?>
                            <?php echo e(get_currency_symbol()); ?> <?php echo e(number_format($row['amount'], 0)); ?>

                        <?php endif; ?>
                    </td>
                    <td>
                        <a style="margin-top: 10px;" href="<?php echo e(route('student_payment.change_status_overdue', $row['id'])); ?>" class="btn btn-info btn-sm" title="Change Status" ><?php echo e(__('Change Status')); ?> </a>

                        <?php if($row['student_uuid']): ?>
                            <a href="<?php echo e(route('student.view', $row['student_uuid'])); ?>" class="btn btn-sm btn-outline-primary">
                                <?php echo e(__('View')); ?>

                            </a>
                        <?php endif; ?>

                        <a href="<?php echo e(route('student_payment.edit_payment_2', $row['id'])); ?>" class="btn-action mr-30" title="Edit Details">
                            <img src="<?php echo e(asset('admin/images/icons/edit-2.svg')); ?>" alt="edit">
                        </a>

                        <button class="ms-3">
                            <span data-formid="delete_row_form_<?php echo e($row['id']); ?>" class="deleteItem">
                                <img src="<?php echo e(asset('admin/images/icons/trash-2.svg')); ?>" alt="trash">
                            </span>
                        </button>

                        <form action="<?php echo e(route('student_payment.delete_payment', $row['id'])); ?>" method="post" id="delete_row_form_<?php echo e($row['id']); ?>">
                            <?php echo e(method_field('DELETE')); ?>

                            <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6"><?php echo e(__('No overdue payments found')); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="mt-3">
        <?php echo $overdues->links(); ?>

    </div>
</div><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/student_payment/_overdue_table.blade.php ENDPATH**/ ?>
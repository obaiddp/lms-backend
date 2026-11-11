<div class="customers__table">
    <table id="donation-payments-table" class="row-border data-table-filter table-style">
        <thead>
        <tr>
            <th><?php echo e(__('ID')); ?></th>
            <th><?php echo e(__('Student')); ?></th>
            <th><?php echo e(__('Amount')); ?></th>
            <th><?php echo e(__('Issue Date')); ?></th>
            
            <th><?php echo e(__('Action')); ?></th>
        </tr>
        </thead>
        <tbody>
            <?php
                $i = 0;
            ?>
        <?php $__currentLoopData = $donationPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="removable-item">
                <td>
                    <?php echo e(++$i); ?>

                </td>
                <td>
                  
                    <?php echo e($payment->user?->name); ?>

                </td>
                <td>
                    <?php echo e(get_currency_symbol()); ?> <?php echo e($payment->price); ?>

                </td>
              
                <td>
                     <?php echo e(date('d-m-Y h:i A',strtotime($payment->created_at))); ?>

                </td>
                
                <td>
                    <div class="action__buttons">
                         <a style="margin-top: 10px;" href="<?php echo e(route('student_payment.change_status_donation', [$payment->id])); ?>" class="btn btn-info btn-sm" title="Change Status" ><?php echo e(__('Change Status')); ?> </a>
                        <button class="ms-3">
                            <span data-formid="delete_row_form_<?php echo e($payment->id); ?>" class="deleteItem">
                                <img src="<?php echo e(asset('admin/images/icons/trash-2.svg')); ?>" alt="trash">
                            </span>
                        </button>

                        <form action="<?php echo e(route('student_payment.delete_donation_payment', [$payment->id])); ?>" method="post" id="delete_row_form_<?php echo e($payment->id); ?>">
                            <?php echo e(method_field('DELETE')); ?>

                            <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                        </form>


                     

                    </div>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <div class="mt-3">
        <?php echo e($donationPayments->links()); ?>

    </div>
</div><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/student_payment/table-data-donation.blade.php ENDPATH**/ ?>
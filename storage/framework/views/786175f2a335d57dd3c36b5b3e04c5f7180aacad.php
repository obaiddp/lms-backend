<div class="customers__table">
    <table id="completed-payments-table" class="row-border data-table-filter table-style">
        <thead>
        <tr>
            <th style="width:100px"><?php echo e(__('Payment ID')); ?></th>
            <th style="width:100px"><?php echo e(__('Student')); ?></th>
            
            <th style="width:100px"><?php echo e(__('Amount')); ?></th>
            <th style="width:100px"><?php echo e(__('Payment Method')); ?></th>
            <th style="width:100px"><?php echo e(__('Status')); ?></th>
            <th style="width:100px"><?php echo e(__('Transaction ID')); ?></th>
            <th style="width:100px"><?php echo e(__('Order No')); ?></th>
            <th style="width:100px"><?php echo e(__('Hash Original')); ?></th>
            <th style="width:100px"><?php echo e(__('Hash Calculated')); ?></th>
            <th style="width:100px"><?php echo e(__('Month')); ?></th>
            <th style="width:100px"><?php echo e(__('Year')); ?></th>
            <th style="width:100px"><?php echo e(__('Payment Date')); ?></th>
            <th style="width:100px"><?php echo e(__('Cart ID')); ?></th>
            <th style="width:100px"><?php echo e(__('Action')); ?></th>
        </tr>
        </thead>
        <tbody>
            <?php
                $i = 0;
            ?>
        <?php $__currentLoopData = $completedDonations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="removable-item">
                <td style="width:100px">
                    <?php echo e("LMS-P-".$payment->order?->id); ?>

                </td>
                <td style="width:100px">
                    <?php echo e($payment->order?->user?->name); ?>

                </td>
                
                <td>
                    
                     <?php echo e(get_currency_symbol()); ?> <?php echo e($payment->unit_price); ?>

                </td>
                <td>
                    <?php
                        if($payment->order?->payment_method == 'meezan_bank'){
                            // $paymentMethod = 'Meezan Bank';
                            $paymentMethod = 'meezan_bank';
                        }
                        else if($payment->order?->payment_method == 'faysal_bank'){
                            // $paymentMethod = 'Faysal Bank';
                            $paymentMethod = 'faysal_bank';
                        }
                        else{
                            $paymentMethod = $payment->order?->payment_method;
                        }
                    ?>
                    <?php echo e($paymentMethod); ?>

                </td>
                <td>
                    <button class="btn btn-success btn-sm" title="Paid">Paid</button>
                </td>

                <td style="width:100px">
                    <?php echo e($payment->order?->payment_id); ?>

                </td>


                <td style="width:100px">
                    <?php
                        if($payment->order?->payment_method == 'meezan_bank'){
                            $paymentOrderNo = $payment->order?->meezan_order_id;
                        }
                        else if($payment->order?->payment_method == 'faysal_bank'){
                            $paymentOrderNo = $payment->order?->faysal_order_id;
                        }
                        else{
                            $paymentOrderNo = null;
                        }
                    ?>
                    <?php echo e($paymentOrderNo); ?>

                </td>



                <td style="width:100px">
                    <?php
                        if($payment->order?->payment_method == 'meezan_bank'){
                            $hashOriginal = null;
                        }
                        else if($payment->order?->payment_method == 'faysal_bank'){
                            $hashOriginal = $payment->order?->faysal_hash_original;
                        }
                        else{
                            $hashOriginal = null;
                        }
                    ?>
                    <?php echo e($hashOriginal); ?>

                </td>


                <td style="width:100px">
                    <?php
                        if($payment->order?->payment_method == 'meezan_bank'){
                            $hashCalculated = null;
                        }
                        else if($payment->order?->payment_method == 'faysal_bank'){
                            $hashCalculated = $payment->order?->faysal_hash_calculated;
                        }
                        else{
                            $hashCalculated = null;
                        }
                    ?>
                    <?php echo e($hashCalculated); ?>

                </td>

                <td>
                    <?php
                        if($payment->month){
                            $monthName = \Carbon\Carbon::create()->month($payment->month)->format('F');
                            // or use 'M' for short month name like 'Aug'
                            // $shortMonthName = \Carbon\Carbon::create()->month($payment->order->month)->format('M');
                            $shortMonthName = \Carbon\Carbon::create()->month($payment->month)->format('M');
                        }
                        else if($payment->cart){
                             $shortMonthName = \Carbon\Carbon::create()->month($payment->cart->month)->format('M');
                        }
                        else{
                            $shortMonthName = null;
                        }
                    ?>
                    <?php echo e($shortMonthName); ?>

                </td>
                <td>
                    
                    <?php

                        if($payment->year){
                            $paymentYear = $payment->year;
                        }
                        else if($payment->cart){
                              $paymentYear = $payment->cart->year;
                        }
                        else{
                            $paymentYear = null;
                        }
                    ?>
                    <?php echo e($paymentYear); ?>


                    
                </td>
                <td>
                    <?php
                        if($payment->order?->payment_method == 'meezan_bank'){
                            if($payment->order?->meezan_payment_date){
                                $order_payment_date = date('d-m-Y h:i:s A',strtotime($payment->order->meezan_payment_date));
                            }
                            else{
                                 $order_payment_date = date('d-m-Y h:i:s A');
                            }
                        }
                        else if($payment->order->payment_method == 'faysal_bank'){
                            if($payment->order?->faysal_payment_date){
                                $order_payment_date = date('d-m-Y h:i:s A',strtotime($payment->order->faysal_payment_date));
                            }
                            else{
                            $order_payment_date = date('d-m-Y h:i:s A');
                            }
                        }
                        else{
                        $order_payment_date = date('d-m-Y h:i:s A');
                        }
                    ?>
                    <?php echo e($order_payment_date); ?>

                </td>
                <td>
                    <?php echo e($payment->cart_id); ?>

                </td>
                <td>
                    <?php if($payment->order): ?>
                        <div class="action__buttons">
                            <button class="ms-3">
                                <span data-formid="delete_row_form_<?php echo e($payment->order?->id); ?>" class="deletePaidPayment">
                                    <img src="<?php echo e(asset('admin/images/icons/trash-2.svg')); ?>" alt="trash">
                                </span>
                            </button>
                        
                            <form action="<?php echo e(route('student_payment.delete_completed_payment', [$payment->order?->id])); ?>" method="post" id="delete_row_form_<?php echo e($payment->order?->id); ?>">
                                <?php echo e(method_field('DELETE')); ?>

                                <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                            </form>
                        </div>
                    <?php endif; ?>
                   
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <div class="mt-3">
        <?php echo e($completedDonations->links()); ?>

    </div>
</div><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/student_payment/table-data-complete-donations.blade.php ENDPATH**/ ?>
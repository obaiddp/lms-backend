<div class="customers__table">
    <table id="completed-payments-table" class="row-border data-table-filter table-style">
        <thead>
        <tr>
            <th style="width:100px">{{__('Payment ID')}}</th>
            <th style="width:100px">{{__('Student')}}</th>
            <th style="width:30px">{{__('Course')}}</th>
            <th style="width:100px">{{__('Amount')}}</th>
            <th style="width:100px">{{__('Payment Method')}}</th>
            <th style="width:100px">{{__('Status')}}</th>
            <th style="width:100px">{{__('Transaction ID')}}</th>
            <th style="width:100px">{{__('Order No')}}</th>
            <th style="width:100px">{{__('Hash Original')}}</th>
            <th style="width:100px">{{__('Hash Calculated')}}</th>
            <th style="width:100px">{{__('Month')}}</th>
            <th style="width:100px">{{__('Year')}}</th>
            <th style="width:100px">{{__('Payment Date')}}</th>
            <th style="width:100px">{{__('Cart ID')}}</th>
            <th style="width:100px">{{__('Action')}}</th>
        </tr>
        </thead>
        <tbody>
            @php
                $i = 0;
            @endphp
        @foreach($completedPayments as $payment)
            <tr class="removable-item">
                <td style="width:100px">
                    {{ "LMS-P-".$payment->order?->id}}
                </td>
                <td style="width:100px">
                    {{ $payment->order?->user?->name}}
                </td>
                <td style="width:100px">
                    @if ($payment->course)
                        <a href="{{route('admin.course.view', [$payment->course->uuid])}}" class="btn-action mr-30" title="View Details">
                        {{ $payment->course->title}}
                        </a>
                    @elseif($payment->product)
                        {{ $payment->product?->title}}
                    @endif 
                </td>
                <td>
                    {{-- {{get_currency_symbol()}} {{ $payment->order?->grand_total}} --}}
                    {{get_currency_symbol()}} {{ $payment->unit_price}}
                </td>
                <td>
                    @php
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
                    @endphp
                    {{ $paymentMethod}}
                </td>
                <td>
                    <button class="btn btn-success btn-sm" title="Paid">Paid</button>
                </td>

                <td style="width:100px">
                    {{ $payment->order?->payment_id}}
                </td>


                <td style="width:100px">
                    @php
                        if($payment->order?->payment_method == 'meezan_bank'){
                            $paymentOrderNo = $payment->order?->meezan_order_id;
                        }
                        else if($payment->order?->payment_method == 'faysal_bank'){
                            $paymentOrderNo = $payment->order?->faysal_order_id;
                        }
                        else{
                            $paymentOrderNo = null;
                        }
                    @endphp
                    {{ $paymentOrderNo}}
                </td>



                <td style="width:100px">
                    @php
                        if($payment->order?->payment_method == 'meezan_bank'){
                            $hashOriginal = null;
                        }
                        else if($payment->order?->payment_method == 'faysal_bank'){
                            $hashOriginal = $payment->order?->faysal_hash_original;
                        }
                        else{
                            $hashOriginal = null;
                        }
                    @endphp
                    {{ $hashOriginal}}
                </td>


                <td style="width:100px">
                    @php
                        if($payment->order?->payment_method == 'meezan_bank'){
                            $hashCalculated = null;
                        }
                        else if($payment->order?->payment_method == 'faysal_bank'){
                            $hashCalculated = $payment->order?->faysal_hash_calculated;
                        }
                        else{
                            $hashCalculated = null;
                        }
                    @endphp
                    {{ $hashCalculated}}
                </td>

                <td>
                    @php
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
                    @endphp
                    {{$shortMonthName}}
                </td>
                <td>
                    {{-- {{ $payment->order->year}} --}}
                    @php

                        if($payment->year){
                            $paymentYear = $payment->year;
                        }
                        else if($payment->cart){
                              $paymentYear = $payment->cart->year;
                        }
                        else{
                            $paymentYear = null;
                        }
                    @endphp
                    {{ $paymentYear}}

                    {{-- date('d-m-Y h:i:sa',strtotime($data2['order_payment_date']) --}}
                </td>
                <td>
                    @php
                        if($payment->order?->payment_method == 'meezan_bank'){
                            if($payment->order?->meezan_payment_date){
                                $order_payment_date = date('d-m-Y h:i:s A',strtotime($payment->order->meezan_payment_date));
                            }
                            else{
                                // $order_payment_date = date('d-m-Y h:i:s A');
                                $order_payment_date = date('d-m-Y h:i:s A',strtotime($payment->order->updated_at));
                            }
                        }
                        else if($payment->order->payment_method == 'faysal_bank'){
                            if($payment->order?->faysal_payment_date){
                                $order_payment_date = date('d-m-Y h:i:s A',strtotime($payment->order->faysal_payment_date));
                            }
                            else{
                                // $order_payment_date = date('d-m-Y h:i:s A');
                                $order_payment_date = date('d-m-Y h:i:s A',strtotime($payment->order->updated_at));
                            }
                        }
                        else{
                            // $order_payment_date = date('d-m-Y h:i:s A');
                            $order_payment_date = date('d-m-Y h:i:s A',strtotime($payment->order->updated_at));
                        }
                    @endphp
                    {{$order_payment_date}}
                </td>
                <td>
                    {{$payment->cart_id}}
                </td>
                <td>
                    @if ($payment->order)
                        <div class="action__buttons">
                            <button class="ms-3">
                                <span data-formid="delete_row_form_{{$payment->order?->id}}" class="deletePaidPayment">
                                    <img src="{{asset('admin/images/icons/trash-2.svg')}}" alt="trash">
                                </span>
                            </button>
                        
                            <form action="{{route('student_payment.delete_completed_payment', [$payment->order?->id])}}" method="post" id="delete_row_form_{{ $payment->order?->id }}">
                                {{ method_field('DELETE') }}
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            </form>
                        </div>
                    @endif
                   
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="mt-3">
        {{$completedPayments->links()}}
    </div>
</div>
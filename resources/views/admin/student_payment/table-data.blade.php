<div class="customers__table">
    <table id="pending-payments-table" class="row-border data-table-filter table-style">
        <thead>
        <tr>
            <th>{{__('ID')}}</th>
            <th>{{__('Student')}}</th>
            <th>{{__('Course')}}</th>
            <th>{{__('No. Of Students')}}</th>
            <th>{{__('Amount')}}</th>
            <th>{{__('Status')}}</th>
            <th>{{'Issue Date'}}</th>
            <th>{{__('Month')}}</th>
            <th>{{__('Year')}}</th>
            <th>{{__('Action')}}</th>
        </tr>
        </thead>
        <tbody>
            @php
                $i = 0;
            @endphp
        @foreach($pendingPayments as $payment)
            <tr class="removable-item">
                <td>
                    {{ ++$i}}
                </td>
                <td>
                  
                    {{$payment->user?->name}}
                </td>
                <td>
                    @if ($payment->course)
                        <a href="{{route('admin.course.view', [$payment->course?->uuid])}}" class="btn-action mr-30" title="View Details">
                        {{$payment->course->title}}
                        </a>
                    @endif
                </td>
                <td>
                    {{$payment->students_count}}
                </td>
                <td>
                    {{get_currency_symbol()}} {{$payment->price}}
                </td>
              
                <td>
                    <button class="btn btn-danger btn-sm" title="Unpaid">UnPaid</button>
                    <a style="margin-top: 10px;" href="{{route('student_payment.change_status', [$payment->id])}}" class="btn btn-info btn-sm" title="Change Status" >{{__('Change Status')}} </a>
                </td>
                <td>
                    {{$payment->created_at ? $payment->created_at->toDateTimeString() : null}}
                </td>
                <td>
                    @php
                    $monthName = \Carbon\Carbon::create()->month($payment->month)->format('F');
                    // or use 'M' for short month name like 'Aug'
                    $shortMonthName = \Carbon\Carbon::create()->month($payment->month)->format('M');
                @endphp
                    {{$shortMonthName}}
                </td>
                <td>
                    {{$payment->year}}
                </td>
                <td>
                    <div class="action__buttons">
                        <a href="{{route('student_payment.edit_payment', [$payment->id])}}" class="btn-action mr-30" title="Edit Details">
                            <img src="{{asset('admin/images/icons/edit-2.svg')}}" alt="edit">
                        </a>
                        <button class="ms-3">
                            <span data-formid="delete_row_form_{{$payment->id}}" class="deleteItem">
                                <img src="{{asset('admin/images/icons/trash-2.svg')}}" alt="trash">
                            </span>
                        </button>

                        <form action="{{route('student_payment.delete_payment', [$payment->id])}}" method="post" id="delete_row_form_{{ $payment->id }}">
                            {{ method_field('DELETE') }}
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </form>


                     

                    </div>
                    <div class="action__buttons">
                           @if($payment->invoice_pdf_path)
                            <a href="{{ route('admin.cart.download.voucher', $payment->id) }}"
                               class="btn btn-sm btn-success mt-1" target="_blank">
                                {{ __('Download PDF') }}
                            </a>
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="mt-3">
        {{$pendingPayments->links()}}
    </div>
</div>
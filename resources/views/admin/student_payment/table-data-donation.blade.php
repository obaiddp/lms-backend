<div class="customers__table">
    <table id="donation-payments-table" class="row-border data-table-filter table-style">
        <thead>
        <tr>
            <th>{{__('ID')}}</th>
            <th>{{__('Student')}}</th>
            <th>{{__('Amount')}}</th>
            <th>{{__('Issue Date')}}</th>
            {{-- <th>{{__('Month')}}</th>
            <th>{{__('Year')}}</th> --}}
            <th>{{__('Action')}}</th>
        </tr>
        </thead>
        <tbody>
            @php
                $i = 0;
            @endphp
        @foreach($donationPayments as $payment)
            <tr class="removable-item">
                <td>
                    {{ ++$i}}
                </td>
                <td>
                  
                    {{$payment->user?->name}}
                </td>
                <td>
                    {{get_currency_symbol()}} {{$payment->price}}
                </td>
              
                <td>
                     {{date('d-m-Y h:i A',strtotime($payment->created_at))}}
                </td>
                {{-- <td>
                    @php
                    $monthName = \Carbon\Carbon::create()->month($payment->month)->format('F');
                    // or use 'M' for short month name like 'Aug'
                    $shortMonthName = \Carbon\Carbon::create()->month($payment->month)->format('M');
                @endphp
                    {{$shortMonthName}}
                </td>
                <td>
                    {{$payment->year}}
                </td> --}}
                <td>
                    <div class="action__buttons">
                         <a style="margin-top: 10px;" href="{{route('student_payment.change_status_donation', [$payment->id])}}" class="btn btn-info btn-sm" title="Change Status" >{{__('Change Status')}} </a>
                        <button class="ms-3">
                            <span data-formid="delete_row_form_{{$payment->id}}" class="deleteItem">
                                <img src="{{asset('admin/images/icons/trash-2.svg')}}" alt="trash">
                            </span>
                        </button>

                        <form action="{{route('student_payment.delete_donation_payment', [$payment->id])}}" method="post" id="delete_row_form_{{ $payment->id }}">
                            {{ method_field('DELETE') }}
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </form>


                     

                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="mt-3">
        {{$donationPayments->links()}}
    </div>
</div>
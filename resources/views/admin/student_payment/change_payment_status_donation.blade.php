@extends('layouts.admin')

@section('content')
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2>{{ __('Change Payment Status') }}</h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('Dashboard')}}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ __('Change Payment Status') }}</li>
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
                            <h2>{{ __('Change Payment Status') }}</h2>
                        </div>
                        <div class="customers__table">
                            <form action="{{route('student_payment.update_status_donation')}}" method="post" class="form-horizontal">
                                @csrf
                                <div class="row">
                                 
    
    
                                    <input type="hidden" name="paymentid" value="{{$getpayment->id}}" >
                                    <div class="col-md-12">
                                        <div class="input__group mb-25">
                                            <label>{{__('Transaction ID')}} <span class="text-danger">*</span></label>
                                            <input type="text" name="transaction_id" id="transaction_id" value="{{old('transaction_id')}}" placeholder="{{ __('Enter Transaction ID') }}" class="form-control" required>
                                            @if ($errors->has('transaction_id'))
                                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('transaction_id') }}</span>
                                            @endif
                                        </div>
                                    </div>
                   
                                </div>
    
                             
    
                         
                            <div class="hr-line-dashed"></div>
                                <div class="row mb-3">
                                    <div class="col-md-12 text-right">
                                        <a class="btn btn-danger" href="{{route('student_payment.donation')}}">{{__('Cancel')}}</a>
                                        <button class="btn btn-primary" type="submit">{{ __('Submit') }}</button>
                                    </div>
                                </div>
    
                            </form>




                    {{-- <table id="customers-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th>{{__('ID')}}</th>
                                    <th>{{__('Student')}}</th>
                                    <th>{{__('Course')}}</th>
                                    <th>{{__('Amount')}}</th>
                                    <th>{{__('Status')}}</th>
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
                                            {{get_currency_symbol()}} {{$payment->price}}
                                        </td>
                                      
                                        <td>
                                            <button class="btn btn-danger btn-sm" title="Unpaid">UnPaid</button>
                                            <a style="margin-top: 10px;" href="{{route('student_payment.change_status', [$payment->id])}}" class="btn btn-info btn-sm" title="Change Status" >{{__('Change Status')}} </a>
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
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                    </table> --}}
                            {{-- <div class="mt-3">
                                {{$pendingPayments->links()}}
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Page content area end -->
@endsection

@push('style')
    <link rel="stylesheet" href="{{asset('admin/css/jquery.dataTables.min.css')}}">
@endpush

@push('script')
    <script src="{{asset('admin/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('admin/js/custom/data-table-page.js')}}"></script>
    <script>
        'use strict'
        $(".status").change(function () {
            var id = $(this).closest('tr').find('#hidden_id').html();
            var status_value = $(this).closest('tr').find('.status option:selected').val();
            Swal.fire({
                title: "{{ __('Are you sure to change status?') }}",
                text: "{{ __('You won`t be able to revert this!') }}",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "{{__('Yes, Change it!')}}",
                cancelButtonText: "{{__('No, cancel!')}}",
                reverseButtons: true
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        type: "POST",
                        url: "{{route('admin.student.changeStudentStatus')}}",
                        data: {"status": status_value, "id": id, "_token": "{{ csrf_token() }}",},
                        datatype: "json",
                        success: function (data) {
                            toastr.options.positionClass = 'toast-bottom-right';
                            toastr.success('', "{{ __('Student status has been updated') }}");
                        },
                        error: function () {
                            alert("Error!");
                        },
                    });
                } else if (result.dismiss === "cancel") {
                }
            });
        });
    </script>
@endpush

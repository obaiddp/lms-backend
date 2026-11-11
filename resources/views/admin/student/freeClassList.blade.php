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
                                <h2>{{ __('New Enrollment Requests') }}</h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('Dashboard')}}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ __('All New Enrollment Requests') }}</li>
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
                            <h2>{{ __('All New Enrollment Requests') }}</h2>
                        </div>
                        <div class="customers__table">
                            <table id="free-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th>{{__('Sr No.')}}</th>
                                    <th>{{__('Student')}}</th>
                                    <th>{{__('Course')}}</th>
                                    <th>{{__('Preferred Date')}}</th>
                                    <th>{{__('Preferred Time')}}</th>
                                    <th>{{__('Enrollment Status')}}</th>
                                    <th>{{__('Request')}}</th> <!-- New Column -->
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $i = 1;
                                @endphp
                                @foreach($freeClasses as $freeClass)
                                    <tr class="removable-item">
                                        <td>
                                            @php
                                                $response = checkfreeRecordTime($freeClass->created_at)
                                            @endphp
                                              {{
                                              $i++
                                            }}
                                            <span style="color:white; font-size:12px;padding:4px" class="badge bg-primary">{{__('New')}}</span>
                                        </td>
                                        <td>
                                            @if ($freeClass->student)
                                            <a href="{{route('student.view', [$freeClass->student->uuid])}}" class="btn-action mr-30" title="View Details">
                                                {{$freeClass->student?->name}}<br>
                                            </a>
                                            @endif
                                         
                                        </td>
                                         <td>
                                            {{$freeClass->course?->title}}<br>
                                        </td>
                                        <td>
                                            {{date('d-m-Y',strtotime($freeClass->prefer_date))}}<br>
                                        </td>
                                        <td>
                                            {{date('h:i A',strtotime($freeClass->prefer_time))}}<br>
                                        </td>

                                        <td>
                                            @if($freeClass->student_enrolled)
                                                <span class="badge bg-success">{{ __('Enrolled') }}</span>
                                            @else
                                                <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if($freeClass->approved)
                                                <span class="badge bg-success">{{ __('Approved') }}</span>
                                            @else
                                                <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                                            @endif
                                        </td>

                                        <td>

                                       
                                            @if($freeClass->student)
                                            <a href="{{route('student.view', ['uuid'=>$freeClass->student?->uuid , 'trial_id' => $freeClass->id])}}" class="btn-action mr-30" title="View Free Class Details">
                                                <img src="{{asset('admin/images/icons/eye-2.svg')}}" alt="eye">
                                            </a>
                                            @endif

                                             {{-- Approve or Resend Button Based on Approval Status --}}
                                                @if(!$freeClass->approved)
                                                   
                                                 
                                                    <a href="{{ route('admin.trial.approve.form', $freeClass->id) }}" class="btn btn-sm btn-success mt-1">
                                                        {{ __('Approve') }}
                                                    </a>
                                                @else
                                                    @if(!$freeClass->student_enrolled)
                                                        <button type="button"
                                                                class="btn btn-sm btn-warning text-dark resend-approval mt-1"
                                                                data-id="{{ $freeClass->id }}">
                                                            {{ __('Resend Email') }}
                                                        </button>

                                                        @if($freeClass->invoice_pdf_path)
                                                            <a href="{{ route('admin.trial.download.voucher', $freeClass->id) }}"
                                                               class="btn btn-sm btn-success mt-1" target="_blank">
                                                                {{ __('Download PDF') }}
                                                            </a>
                                                        @else
                                                            {{-- <button class="btn btn-sm btn-secondary mt-1" disabled>
                                                                {{ __('No Voucher') }}
                                                            </button> --}}
                                                        @endif

                                                    @endif
                                                @endif


                                            @if($freeClass->approved)
                                                @if (!$freeClass->status)
                                                    @can('add_classes')    
                                                        <a style="margin-left:5px" class="btn-action mr-30" href="{{route('classes.create_free_class_individual',['id'=>$freeClass->id])}}" title="Schedule Free Class">
                                                            <img src="{{asset('admin/images/icons/edit-2.svg')}}" alt="edit">
                                                        </a>   
                                                    @endcan
                                                @endif
                                            @endif
                                        </td>
                                     
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="mt-3">
                                {{$freeClasses->links()}}
                            </div>
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

{{-- <script>
    'use strict';

    // Handle Approve Trial
    $(document).on('click', '.approve-trial', function () {
        const trialId = $(this).data('id');
       
        Swal.fire({
            title: "{{ __('Approve Request Enrollment?') }}",
            text: "{{ __('This will mark the enrollment request as approved and send an email to the student.') }}",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "{{ __('Yes, Approve') }}",
            cancelButtonText: "{{ __('Cancel') }}",
        }).then(function (result) {
           
             // ✅ This works with both old and new SweetAlert
            if (result.value || result.isConfirmed) {
              
                $.ajax({
                    url: "{{ route('admin.trial.approve') }}",
                    type: "POST",
                    data: {
                        id: trialId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message || "{{ __('Enrollment Request approved and email sent!') }}");
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            toastr.error(response.message || "{{ __('Failed to approve enrollment request.') }}");
                        }
                    },
                    error: function () {
                        toastr.error("{{ __('Something went wrong! Please try again.') }}");
                    }
                });
            }
        });
    });

    // Handle Resend Approval Email
    $(document).on('click', '.resend-approval', function () {
        const trialId = $(this).data('id');

        Swal.fire({
            title: "{{ __('Resend Approval Email?') }}",
            text: "{{ __('An approval email will be sent again to the student.') }}",
            icon: "info",
            showCancelButton: true,
            confirmButtonText: "{{ __('Yes, Resend') }}",
            cancelButtonText: "{{ __('Cancel') }}",
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.trial.resend') }}",
                    type: "POST",
                    data: {
                        id: trialId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message || "{{ __('Approval email resent successfully!') }}");
                        } else {
                            toastr.error(response.message || "{{ __('Failed to resend email.') }}");
                        }
                    },
                    error: function () {
                        toastr.error("{{ __('Request failed. Please check your connection.') }}");
                    }
                });
            }
        });
    });
</script> --}}




<script>
    'use strict';

    // Handle Approve Trial
    $(document).on('click', '.approve-trial', function () {
        const $button = $(this);
        const trialId = $button.data('id');

        Swal.fire({
            title: "{{ __('Approve Request Enrollment?') }}",
            text: "{{ __('This will mark the enrollment request as approved and send an email to the student.') }}",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "{{ __('Yes, Approve') }}",
            cancelButtonText: "{{ __('Cancel') }}",
            preConfirm: () => {
                // Start loading before AJAX
                $button.prop('disabled', true)
                       .text('{{ __('Approving...') }}')
                       .append(' <i class="spinner-border spinner-border-sm"></i>');
            }
        }).then((result) => {
            if (result.isConfirmed || result.value) {
                $.ajax({
                    url: "{{ route('admin.trial.approve') }}",
                    type: "POST",
                    data: {
                        id: trialId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        $button.prop('disabled', false)
                               .html('{{ __('Approve') }}'); // Reset button

                        if (response.success) {
                            toastr.success(response.message || "{{ __('Enrollment Request approved and email sent!') }}");
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            toastr.error(response.message || "{{ __('Failed to approve enrollment request.') }}");
                        }
                    },
                    error: function () {
                        $button.prop('disabled', false)
                               .html('{{ __('Approve') }}'); // Reset button

                        toastr.error("{{ __('Something went wrong! Please try again.') }}");
                    }
                });
            } else {
                // If canceled, ensure button is re-enabled
                $button.prop('disabled', false);
            }
        });
    });

    // Handle Resend Approval Email
    $(document).on('click', '.resend-approval', function () {
        const $button = $(this);
        const trialId = $button.data('id');

        Swal.fire({
            title: "{{ __('Resend Approval Email?') }}",
            text: "{{ __('An approval email will be sent again to the student.') }}",
            icon: "info",
            showCancelButton: true,
            confirmButtonText: "{{ __('Yes, Resend') }}",
            cancelButtonText: "{{ __('Cancel') }}",
            preConfirm: () => {
                $button.prop('disabled', true)
                       .text('{{ __('Resending...') }}')
                       .append(' <i class="spinner-border spinner-border-sm"></i>');
            }
        }).then((result) => {
            if (result.isConfirmed || result.value) {
                $.ajax({
                    url: "{{ route('admin.trial.resend') }}",
                    type: "POST",
                    data: {
                        id: trialId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        $button.prop('disabled', false)
                               .html('{{ __('Resend Email') }}'); // Reset button

                        if (response.success) {
                            toastr.success(response.message || "{{ __('Approval email resent successfully!') }}");
                        } else {
                            toastr.error(response.message || "{{ __('Failed to resend email.') }}");
                        }
                    },
                    error: function () {
                        $button.prop('disabled', false)
                               .html('{{ __('Resend Email') }}'); // Reset button

                        toastr.error("{{ __('Request failed. Please check your connection.') }}");
                    }
                });
            } else {
                $button.prop('disabled', false); // Ensure enabled if canceled
            }
        });
    });
</script>




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

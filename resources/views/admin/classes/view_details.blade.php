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
                                <h2>{{__('Class Details')}}</h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('Dashboard')}}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{__('Class Details')}}</li>
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
                            <h2>{{__('Class Details')}}</h2>
                        </div>
                        <div class="customers__table">
                            <table id="class-details-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th>{{__('Sr No.')}}</th>
                                    <th>{{__('Meeting ID')}}</th>
                                    <th>{{__('Student')}}</th>
                                    <th>{{__('Instructor')}}</th>
                                    <th>{{__('Course')}}</th>
                                    <th>{{__('Date')}}</th>
                                    <th>{{__('Time')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Meeting ID Classes')}}</th>
                                    <th>{{__('Google Meet URI')}}</th>
                                    <th>{{__('Instructor URI')}}</th>
                                    <th>{{__('Student URI')}}</th>
                                    <th>{{__('Coordinator URI')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $i = 0;
                                @endphp
                                @foreach($class_details as $detail)
                                    <tr class="removable-item">
                                        <td>
                                            {{++$i}}
                                        </td>
                                        <td>
                                            {{$detail->meeting_id?$detail->meeting_id:null}}
                                        </td>
                                        <td>
                                            <a href="{{route('student.view', [$detail->student?->uuid])}}" class="btn-action mr-30" title="View Details">
                                            {{$detail->student?->first_name .' '.$detail->student?->last_name}}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{route('instructor.view', [$detail->instructor?->uuid])}}"> 
                                            {{$detail->instructor?->first_name .' '.$detail->instructor?->last_name}}
                                            </a>
                                        </td>
                                        <td>
                                            {{$detail->course?->title}}
                                        </td>
                                        <td>
                                            {{date('d.m.Y',strtotime($detail->class_date))}}
                                        </td>
                                        <td>
                                            {{date('h:i a',strtotime($detail->class_time))}}
                                        </td>
                                        <td>
                                            {{$detail->status}}
                                        </td>
                                        <td>
                                            {{meeting_id_classes($detail->meeting_id)}}
                                        </td>
                                        <td class="url-cell">
                                            {{$detail->google_meet_link}}
                                        </td>
                                        <td class="url-cell">
                                            {{$detail->InstructorURI}}
                                        </td>
                                        <td class="url-cell">
                                            {{$detail->StudentURI}}
                                        </td>
                                        <td class="url-cell">
                                            {{$detail->AcademicURI}}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="mt-3">
                                {{$class_details->links()}}
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
    {{-- <script src="{{ asset('admin/js/custom/instructor-delete.js') }}"></script> --}}
    {{-- <script src="{{ asset('admin/js/custom/coordinator-delete.js') }}"></script> --}}
    <script src="{{ asset('admin/js/custom/class-delete.js') }}"></script>
    <script>
        'use strict'
        $(".change-status").change(function () {
            var id = $(this).closest('tr').find('#hidden_id').html();
            var status_value = $(this).val();
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
                        url: "{{route('admin.coordinator.changeCoordinatorStatus')}}",
                        data: {"status": status_value, "id": id, "_token": "{{ csrf_token() }}",},
                        datatype: "json",
                        success: function (data) {
                            toastr.options.positionClass = 'toast-bottom-right';
                            toastr.success('', "{{ __('Coordinator status has been updated') }}");
                            location.reload();
                        },
                        error: function () {
                            alert("Error!");
                        },
                    });
                } else if (result.dismiss === "cancel") {
                    location.reload();
                }
            });
        });
        
        $(".change-auto-content").change(function () {
            var id = $(this).closest('tr').find('#hidden_id').html();
            var status_value = $(this).val();
            Swal.fire({
                title: "{{ __('Are you sure to change?') }}",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "{{__('Yes, Change it!')}}",
                cancelButtonText: "{{__('No, cancel!')}}",
                reverseButtons: true
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        type: "POST",
                        url: "{{route('admin.coordinator.changeAutoContentStatus')}}",
                        data: {"auto_content_approval": status_value, "id": id, "_token": "{{ csrf_token() }}",},
                        datatype: "json",
                        success: function (data) {
                            toastr.options.positionClass = 'toast-bottom-right';
                            toastr.success('', "{{ __('Auto content status has been updated') }}");
                            location.reload();
                        },
                        error: function () {
                            alert("Error!");
                        },
                    });
                } else if (result.dismiss === "cancel") {
                    location.reload();
                }
            });
        });
    </script>
@endpush

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
                                <h2>{{__('Group Classes')}}</h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('Dashboard')}}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{__('All Group Classes')}}</li>
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
                            <h2>{{__('All Group Classes')}}</h2>
                        </div>
                        <div class="customers__table">
                            <table id="classes-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th>{{__('Instructor')}}</th>
                                    <th>{{__('Total Students')}}</th>
                                    <th>{{__('Total Classes')}}</th>
                                    <th>{{__('Duration')}}</th>
                                    <th>{{__('Learning Tool')}}</th>
                                    <th class="text-center">{{__('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($classes as $class)
                                    <tr class="removable-item">
                                        <td>
                                            @if (isset($class->instructor))
                                            <a href="{{route('instructor.view', [$class->instructor->uuid])}}"> 
                                            {{$class->instructor->first_name.' '.$class->instructor->last_name}}
                                            </a>
                                            @endif
                                        </td>
                                        <td>
                                            {{$class->no_of_students}}
                                        </td>

                                        <td>
                                            <a href="{{route('classes.view_detail', [$class->id])}}" class="btn-action mr-30" title="View Details">
                                            {{$class->no_of_classes}}
                                            </a>
                                        </td>
                                        <td>
                                            {{$class->duration}}
                                        </td>
                                   
                                 
                                        <td>
                                            {{$class->learning_tool}}
                                        </td>
                                        <td>
                                            <div class="action__buttons">
                                                <a href="{{route('classes.view_detail', [$class->id])}}" class="btn-action mr-30" title="View Details">
                                                    <img src="{{asset('admin/images/icons/eye-2.svg')}}" alt="eye">
                                                </a>
                                                <a href="{{route('classes.edit', [$class->id])}}" class="btn-action mr-30" title="Edit Class">
                                                    <img src="{{asset('admin/images/icons/edit-2.svg')}}" alt="edit">
                                                </a>
                                                {{-- <a href="javascript:void(0);" data-url="{{route('coordinator.delete', [$coordinator->uuid])}}" title="Delete" class="btn-action deleteBtn">
                                                    <img src="{{asset('admin/images/icons/trash-2.svg')}}" alt="trash">
                                                </a> --}}
                                                <a href="javascript:void(0);" data-url="{{route('classes.delete', [$class->id])}}" title="Delete" class="btn-action deleteBtn">
                                                    <img src="{{asset('admin/images/icons/trash-2.svg')}}" alt="trash">
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="mt-3">
                                {{$classes->links()}}
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

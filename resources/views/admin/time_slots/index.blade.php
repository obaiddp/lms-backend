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
                                <h2>{{__('TimeSlots')}}</h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('Dashboard')}}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{__('All TimeSlots')}}</li>
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
                            <h2>{{__('All TimeSlots')}}</h2>
                            <a style="background-color: #1E417D" href="{{ route('time_slots.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create New Slot
                            </a>
                        </div>
                     
                        <div class="customers__table">
                            <table id="customers-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th>{{__('Sr No.')}}</th>
                                    {{-- <th>{{__('Date')}}</th> --}}
                                    <th>{{__('Time')}}</th>
                                    <th>{{__('Status')}}</th>
                                    <th>{{__('Bookings')}}</th>
                                
                                    <th class="text-center">{{__('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($timeSlots as $key=> $slot)
                                    <tr class="removable-item">
                                        <td>
                                            {{$key+1}}
                                        </td>
                                        {{-- <td>{{ $slot->date->format('D, M j, Y') }}</td> --}}
                                        <td>{{ $slot->start_time->format('h:i A') }} - {{ $slot->end_time->format('h:i A') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $slot->is_active ? 'success' : 'danger' }}">
                                                {{ $slot->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>{{ $slot->bookings->count() }}</td>
                                     
                                        <td class="">
                                            <div class="action__buttons">
                                            <a style="margin-right: 10px" href="{{ route('time_slots.edit', $slot->id) }}" class="btn-action mr-50">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                                                 
                                          
                                            
                                            <button style="margin-right: 10px" class="btn-action mr-50 deleteItem" data-formid="delete_row_form_{{$slot->id}}">
                                                {{-- <img width="100px" src="{{asset('admin/images/icons/trash-2.svg')}}" alt="trash"> --}}
                                                <i class="fas fa-trash"></i>
                                            </button>
                                          
                                            <form action="{{route('time_slots.destroy', ['slot'=> $slot->id])}}" method="get" id="delete_row_form_{{$slot->id}}">

                                            </form>

                                            {{-- <form action="{{ route('time_slots.destroy', $slot) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button style="margin-right: 10px" type="submit" class="btn-action deleteBtn" onclick="return confirm('Are you sure?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form> --}}

                                        <form action="{{ route('time_slots.toggle_status',['slot'=> $slot->id]) }}" method="POST">
                                                @csrf
                                                <button style="margin-right: 10px" type="submit" class="btn btn-sm btn-{{ $slot->is_active ? 'warning' : 'success' }}">
                                                    <i class="fas fa-{{ $slot->is_active ? 'times' : 'check' }}"></i>
                                                </button>
                                            </form>
                                            
                                          </div>
                                        </td>
                                        
                                        {{-- <td>
                                            <div class="action__buttons">
                                                <a href="{{route('coordinator.view', [$coordinator->uuid])}}" class="btn-action mr-30" title="View Details">
                                                    <img src="{{asset('admin/images/icons/eye-2.svg')}}" alt="eye">
                                                </a>
                                                <a href="{{route('coordinator.edit', [$coordinator->uuid])}}" class="btn-action mr-30" title="Edit Details">
                                                    <img src="{{asset('admin/images/icons/edit-2.svg')}}" alt="edit">
                                                </a>
                                          
                                                <a href="javascript:void(0);" data-url="{{route('coordinator.delete', [$coordinator->id])}}" title="Delete" class="btn-action deleteBtn">
                                                    <img src="{{asset('admin/images/icons/trash-2.svg')}}" alt="trash">
                                                </a>
                                            </div>
                                        </td> --}}
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="mt-3">
                                {{$timeSlots->links()}}
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
    <script src="{{ asset('admin/js/custom/coordinator-delete.js') }}"></script>
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

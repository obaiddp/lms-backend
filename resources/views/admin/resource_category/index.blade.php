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
                                <h2>{{__('Resource Categories')}}</h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('Dashboard')}}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{__('Resource Categories')}}</li>
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
                            <h2>{{ __('All Resource Categories') }}</h2>
                        </div>
                        <div class="customers__table all-course">
                            <table id="customers-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th>{{__('Sr. #')}}</th>
                                    <th>{{__('Category ID')}}</th>
                                    <th>{{__('Category Name')}}</th>
                                    <th>{{__('Category Detail')}}</th>
                                    {{-- <th>{{__('Action')}}</th> --}}
                                </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 0
                                    @endphp
                                @foreach($categories as $category)
                                    <tr class="removable-item">
                                        <td>
                                           {{++$i}}
                                        </td>
                                        <td>
                                            {{'LMS-C-'}}{{$category->id}}
                                        </td>
                                        <td>
                                            {{$category->name}}
                                        </td>
                                        <td>
                                            {{$category->description}}
                                        </td>

                                        {{-- <td>

                                            <div class="action__buttons">
                                              

                                                <a  href="{{route('admin.resource.category.edit', [$category->id])}}" class="btn-action mr-30" title="Edit Details">
                                                    <img src="{{asset('admin/images/icons/edit.svg')}}" alt="edit">
                                                </a>
                                              
                                                <button class="btn-action ms-2 deleteItem" data-formid="delete_row_form_{{$category->id}}">
                                                    <img src="{{asset('admin/images/icons/trash-2.svg')}}" alt="trash">
                                                </button>
                                              
                                                <form action="{{route('admin.resource.category.delete', [$category->id])}}" method="post" id="delete_row_form_{{ $category->id }}">
                                                    @csrf
                                                </form>
                                            </div>
                                        </td> --}}
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="mt-3">
                                {{$categories->links()}}
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



    
    <script>
         $(".change-feature").change(function () {
            var id = $(this).closest('tr').find('#hidden_id').html();
            var status_value = $(this).closest('tr').find('.status option:selected').val();
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
                        url: "{{route('admin.course.feature-change')}}",
                        data: {"status": status_value, "id": id, "_token": "{{ csrf_token() }}",},
                        datatype: "json",
                        success: function (data) {
                            toastr.options.positionClass = 'toast-bottom-right';
                            toastr.success('', "{{ __('Feature has been updated') }}");
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

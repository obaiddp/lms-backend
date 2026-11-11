@extends('layouts.admin')

@section('content')
<!-- Page content area start -->
<div class="page-content">
    <div class="container-fluid">
        <!-- Breadcrumb Start -->
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb__content">
                    <div class="breadcrumb__content__left">
                        <div class="breadcrumb__title">
                            <h2>{{ __('Licenses') }}</h2>
                        </div>
                    </div>
                    <div class="breadcrumb__content__right">
                        <nav aria-label="breadcrumb">
                            <ul class="breadcrumb">
                                @canany(['manage_dashboard','all_dashboard'])
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                                    </li>
                                @endcanany
                                <li class="breadcrumb-item active" aria-current="page">{{ __('All Licenses') }}</li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- Breadcrumb End -->

        <!-- License Table Start -->
        <div class="row">
            <div class="col-md-12">
                <div class="customers__area bg-style mb-30">
                    <div class="item-title d-flex justify-content-between align-items-center">
                        <h2>{{ __('All Licenses') }}</h2>
                        <a href="{{ route('admin.license.create') }}" class="btn btn-success">
                            {{ __('Create New License') }}
                        </a>
                    </div>

                    <div class="customers__table">
                        <table id="license-table" class="row-border data-table-filter table-style">
                            <thead>
                                <tr>
                                    <th>{{ __('Sr No.') }}</th>
                                    <th>{{ __('Vault User') }}</th>
                                    <th>{{ __('License Key') }}</th>
                                    <th>{{ __('Courses Attached') }}</th>
                                    <th>{{ __('Number Of Devices') }}</th>
                                    <th>{{ __('Start Date') }}</th>
                                    <th>{{ __('Expiry Date') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1; @endphp
                                @foreach($licenses as $license)
                                    <tr class="removable-item">
                                        <td>{{ $i++ }}</td>

                                        <td>
                                            @if($license->vaultUser)
                                                {{ $license->vaultUser->name }}
                                                @if($license->vaultUser->email)
                                                    <br><small>{{ $license->vaultUser->email }}</small>
                                                @endif
                                            @elseif($license->user)
                                                {{ $license->user->name }}
                                                <br><small>{{ $license->user->email }}</small>
                                            @endif
                                        </td>

                                        <td><code>{{ $license->key }}</code></td>

                                        <td>
                                            @if($license->courses && count($license->courses) > 0)
                                                @foreach($license->courses as $course)
                                                    <span class="badge bg-info text-white mb-1">{{ $course->title }}</span><br>
                                                @endforeach
                                            @else
                                                <span class="badge bg-secondary">{{ __('No Courses') }}</span>
                                            @endif
                                        </td>

                                        <td>{{ $license->licenseDevices()->count() }}</td>

                                        <td>{{ date('d-m-Y', strtotime($license->start_date)) }}</td>

                                        <td>{{ date('d-m-Y', strtotime($license->end_date)) }}</td>

                                        <td>
                                            @if($license->status == 'active')
                                                <span class="badge bg-success">{{ __('Active') }}</span>
                                            @elseif($license->status == 'expired')
                                                <span class="badge bg-danger">{{ __('Expired') }}</span>
                                            @else
                                                <span class="badge bg-warning text-dark">{{ __('Inactive') }}</span>
                                            @endif
                                        </td>

                                        <td>
                                            {{-- <a href="{{ route('admin.license.show', $license->id) }}" class="btn-action mr-10" title="View Details">
                                                <img src="{{ asset('admin/images/icons/eye-2.svg') }}" alt="eye">
                                            </a> --}}

                                            <a href="{{ route('admin.license.usage-history', $license->id) }}" class="btn-action mr-10" title="{{ __('Usage history') }}">
                                                <img src="{{ asset('admin/images/icons/recent.svg') }}" alt="{{ __('Usage history') }}">
                                            </a>

                                            <a href="{{ route('admin.license.edit', $license->id) }}" class="btn-action mr-10" title="Edit License">
                                                <img src="{{ asset('admin/images/icons/edit-2.svg') }}" alt="edit">
                                            </a>

                                            <button class="ms-3">
                                                    <span data-formid="delete_row_form_{{$license->id}}" class="deleteItem">
                                                        <img src="{{asset('admin/images/icons/trash-2.svg')}}" alt="trash">
                                                    </span>
                                            </button>
                                            <form action="{{route('admin.license.delete', ['id'=>$license->id])}}" method="post" id="delete_row_form_{{ $license->id }}">
                                                {{ method_field('DELETE') }}
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            </form>

                                        
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-3">
                            {{ $licenses->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- License Table End -->
    </div>
</div>
<!-- Page content area end -->
@endsection

@push('style')
<link rel="stylesheet" href="{{ asset('admin/css/jquery.dataTables.min.css') }}">
@endpush

@push('script')
<script src="{{ asset('admin/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/js/custom/data-table-page.js') }}"></script>

<script>
'use strict';

</script>
@endpush
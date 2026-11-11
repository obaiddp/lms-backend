@extends('layouts.admin')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb__content">
                    <div class="breadcrumb__content__left">
                        <div class="breadcrumb__title">
                            <h2>{{ __('Edit License') }}</h2>
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
                                <li class="breadcrumb-item active" aria-current="page">{{ __('Edit License') }}</li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        {{-- FORM START --}}
        <div class="row">
            <div class="col-md-12">
                <div class="customers__area bg-style mb-30">
                    <div class="item-title d-flex justify-content-between">
                        <h2>{{ __('Edit License') }}</h2>
                    </div>

                    <form action="{{ route('admin.license.update', $license->id) }}" method="POST" id="licenseForm" class="form-horizontal">
                        @csrf
                        @method('POST')

                        <div class="row">

                            {{-- Vault User --}}
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{ __('Vault User') }} <span class="text-danger">*</span></label>
                                    <select name="vault_user_id" id="vault_user_id" class="form-select" required>
                                        <option value="">{{ __('Select Vault User') }}</option>
                                        @foreach($vaultUsers as $vaultUser)
                                            <option value="{{ $vaultUser->id }}" {{ $license->vault_user_id == $vaultUser->id ? 'selected' : '' }}>
                                                {{ $vaultUser->name }}@if($vaultUser->email) ({{ $vaultUser->email }})@endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{-- License Key --}}
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label>{{ __('License Key') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="license_key" id="license_key" class="form-control" value="{{ $license->key }}" required readonly>
                                </div>
                            </div>

                            {{-- Start Date --}}
                            <div class="col-md-3">
                                <div class="input__group mb-25">
                                    <label>{{ __('Start Date') }} <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}" required>
                                </div>
                            </div>

                            {{-- Expiry Date --}}
                            <div class="col-md-3">
                                <div class="input__group mb-25">
                                    <label>{{ __('Expiry Date') }} <span class="text-danger">*</span></label>
                                    <input type="date" name="expiry_date" id="expiry_date" class="form-control" value="{{ $endDate }}" required>
                                </div>
                            </div>

                            {{-- Courses --}}
                            <div class="col-md-12">
                                <div class="input__group mb-25">
                                    <label>{{ __('Select Courses for License') }} <span class="text-danger">*</span></label>
                                    <select name="courses[]" id="courses" class="form-select" multiple required>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" {{ in_array($course->id, $selected_courses) ? 'selected' : '' }}>
                                                {{ $course->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Hold CTRL to select multiple courses</small>
                                </div>
                            </div>

                            {{-- Physical Devices --}}
                            <div class="col-md-12">
                                <div class="input__group mb-25">
                                    <label>{{ __('Physical Devices (Manual Entry)') }}</label>
                                    <div id="device-wrapper">
                                        @forelse($license->licenseDevices as $index => $device)
                                            <div class="row device-item mb-2">
                                                <div class="col-md-5">
                                                    <input type="text" name="devices[{{ $index }}][name]" class="form-control" value="{{ $device->name }}" placeholder="Device Name">
                                                </div>
                                                <div class="col-md-5">
                                                    <input type="text" name="devices[{{ $index }}][k_sign]" class="form-control k-sign" value="{{ $device->k_sign }}" placeholder="+Sign Number">
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-danger remove-device">−</button>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="row device-item mb-2">
                                                <div class="col-md-5">
                                                    <input type="text" name="devices[0][name]" class="form-control" placeholder="Device Name">
                                                </div>
                                                <div class="col-md-5">
                                                    <input type="text" name="devices[0][k_sign]" class="form-control k-sign" placeholder="Device ID Number">
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-success add-device">+</button>
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>
                                    <button type="button" class="btn btn-success add-device">+</button>
                                </div>
                            </div>

                        </div>

                        {{-- Submit --}}
                        <div class="row mb-3">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-primary">{{ __('Update License') }}</button>
                                <a href="{{ route('admin.license.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- FORM END --}}
    </div>
</div>
@endsection

@push('script')
<script>
$(document).ready(function() {
    $("#vault_user_id, #courses").select2({ width: '100%' });

    // Add / Remove Device Inputs
    let deviceIndex = {{ $license->licenseDevices->count() ?? 1 }};
    $(document).on('click', '.add-device', function() {
        const html = `
            <div class="row device-item mb-2">
                <div class="col-md-5">
                    <input type="text" name="devices[${deviceIndex}][name]" class="form-control" placeholder="Device Name">
                </div>
                <div class="col-md-5">
                    <input type="text" name="devices[${deviceIndex}][k_sign]" class="form-control k-sign" placeholder="Device ID Number">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger remove-device">−</button>
                </div>
            </div>`;
        $('#device-wrapper').append(html);
        deviceIndex++;
    });

    $(document).on('click', '.remove-device', function() {
        $(this).closest('.device-item').remove();
    });

    // Enforce + sign prefix
    // $(document).on('input', '.k-sign', function() {
    //     if (!$(this).val().startsWith('+')) {
    //         $(this).val('+' + $(this).val().replace(/^\+/, ''));
    //     }
    // });

    // Validate date difference
    $('#licenseForm').on('submit', function(e) {
        const start = new Date($('#start_date').val());
        const end = new Date($('#expiry_date').val());

        if (start >= end) {
            e.preventDefault();
            alert('Expiry date must be greater than Start date.');
            return false;
        }
    });
});
</script>
@endpush
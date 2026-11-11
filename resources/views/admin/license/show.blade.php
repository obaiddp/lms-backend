@extends('layouts.admin')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb__content">
                    <div class="breadcrumb__content__left">
                        <div class="breadcrumb__title">
                            <h2>{{ __('License details') }}</h2>
                        </div>
                    </div>
                    <div class="breadcrumb__content__right">
                        <nav aria-label="breadcrumb">
                            <ul class="breadcrumb">
                                @canany(['manage_dashboard','all_dashboard'])
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                @endcanany
                                <li class="breadcrumb-item"><a href="{{ route('admin.license.index') }}">{{ __('Licenses') }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ __('Details') }}</li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="customers__area bg-style mb-30">
                    <p><strong>{{ __('License key') }}:</strong> <code>{{ $license->key }}</code></p>
                    @if($license->vaultUser)
                        <p><strong>{{ __('Vault User') }}:</strong> {{ $license->vaultUser->name }}@if($license->vaultUser->email) ({{ $license->vaultUser->email }})@endif</p>
                    @elseif($license->user)
                        <p><strong>{{ __('Legacy Student') }}:</strong> {{ $license->user->name }} ({{ $license->user->email }})</p>
                    @endif
                    <p><strong>{{ __('Devices') }}:</strong> {{ $license->licenseDevices->count() }}</p>
                    <a href="{{ route('admin.license.usage-history', $license->id) }}" class="btn btn-primary">{{ __('Usage history') }}</a>
                    <a href="{{ route('admin.license.edit', $license->id) }}" class="btn btn-outline-secondary">{{ __('Edit') }}</a>
                    <a href="{{ route('admin.license.index') }}" class="btn btn-link">{{ __('Back') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb__content">
                    <div class="breadcrumb__content__left">
                        <div class="breadcrumb__title">
                            <h2>{{ __('License usage history') }}</h2>
                        </div>
                    </div>
                    <div class="breadcrumb__content__right">
                        <nav aria-label="breadcrumb">
                            <ul class="breadcrumb">
                                @canany(['manage_dashboard','all_dashboard'])
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                @endcanany
                                <li class="breadcrumb-item"><a href="{{ route('admin.license.index') }}">{{ __('Licenses') }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ __('Usage history') }}</li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <a href="{{ route('admin.license.index') }}" class="btn btn-outline-secondary btn-sm">&larr; {{ __('Back to licenses') }}</a>
                <a href="{{ route('admin.license.edit', $license->id) }}" class="btn btn-outline-primary btn-sm ms-2">{{ __('Edit license') }}</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="customers__area bg-style mb-30">
                    <div class="item-title">
                        <h2>{{ __('License') }}: <code>{{ $license->key }}</code></h2>
                        @if($license->vaultUser)
                            <p class="mb-0 text-muted">{{ __('Vault User') }}: {{ $license->vaultUser->name }}@if($license->vaultUser->email) &mdash; {{ $license->vaultUser->email }}@endif</p>
                        @elseif($license->user)
                            <p class="mb-0 text-muted">{{ __('Legacy Student') }}: {{ $license->user->name }} &mdash; {{ $license->user->email }}</p>
                        @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <div class="customers__area bg-style p-3 text-center">
                    <div class="text-muted small">{{ __('Registered devices') }}</div>
                    <div class="h3 mb-0">{{ $devices->count() }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="customers__area bg-style p-3 text-center">
                    <div class="text-muted small">{{ __('Active vault sessions') }}</div>
                    <div class="h3 mb-0 text-success">{{ $activeActivations }}</div>
                    <div class="small text-muted">{{ __('Activated and not revoked') }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="customers__area bg-style p-3 text-center">
                    <div class="text-muted small">{{ __('Revoked devices') }}</div>
                    <div class="h3 mb-0 text-danger">{{ $revokedDevices }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="customers__area bg-style p-3 text-center">
                    <div class="text-muted small">{{ __('License status') }}</div>
                    <div class="h3 mb-0">
                        @if(($license->status ?? 'active') === 'active')
                            <span class="badge bg-success">{{ __('Active') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ e($license->status) }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="customers__area bg-style mb-30">
                    <div class="item-title">
                        <h2>{{ __('Devices (current snapshot)') }}</h2>
                        <p class="text-muted small mb-0">{{ __('Dates stored on each device row; use the event log below for activation, logout, and revoke actions recorded by the app.') }}</p>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-style">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Device key') }}</th>
                                    <th>{{ __('Registered at') }}</th>
                                    <th>{{ __('Vault activated at') }}</th>
                                    <th>{{ __('Revoked at') }}</th>
                                    <th>{{ __('Current state') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($devices as $d)
                                    <tr>
                                        <td>{{ $d->name ?? '—' }}</td>
                                        <td><code style="word-break: break-all;">{{ $d->k_sign }}</code></td>
                                        <td>{{ $d->registered_at ? $d->registered_at->timezone(config('app.timezone'))->format('Y-m-d H:i') : '—' }}</td>
                                        <td>{{ $d->vault_activated_at ? $d->vault_activated_at->timezone(config('app.timezone'))->format('Y-m-d H:i') : '—' }}</td>
                                        <td>{{ $d->revoked_at ? $d->revoked_at->timezone(config('app.timezone'))->format('Y-m-d H:i') : '—' }}</td>
                                        <td>
                                            @if($d->revoked_at)
                                                <span class="badge bg-danger">{{ __('Revoked') }}</span>
                                                @if($d->revoke_reason)
                                                    <br><small class="text-muted">{{ Str::limit($d->revoke_reason, 120) }}</small>
                                                @endif
                                            @elseif($d->vault_activated_at)
                                                <span class="badge bg-success">{{ __('Access granted') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ __('Not activated') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted">{{ __('No devices registered for this license.') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="customers__area bg-style mb-30">
                    <div class="item-title">
                        <h2>{{ __('Event log') }}</h2>
                        <p class="text-muted small mb-0">{{ __('Recorded when admins register devices, when the Vault app activates, logs out, or when access is revoked (e.g. security event).') }}</p>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-style">
                            <thead>
                                <tr>
                                    <th>{{ __('When') }}</th>
                                    <th>{{ __('Event') }}</th>
                                    <th>{{ __('Device') }}</th>
                                    <th>{{ __('Detail') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($events as $ev)
                                    @php
                                        $labels = [
                                            'device_registered_admin' => __('Device registered (admin)'),
                                            'vault_access_granted' => __('Vault access granted'),
                                            'vault_logout' => __('Vault logout (activation cleared)'),
                                            'access_revoked' => __('Access revoked'),
                                        ];
                                        $label = $labels[$ev->event] ?? $ev->event;
                                        $dev = $ev->licenseDevice;
                                        $devLabel = $dev ? ($dev->name.' / '.Str::limit($dev->k_sign, 32)) : Str::limit($ev->device_key ?? '', 40);
                                    @endphp
                                    <tr>
                                        <td>{{ $ev->created_at->timezone(config('app.timezone'))->format('Y-m-d H:i:s') }}</td>
                                        <td><span class="badge bg-info text-dark">{{ $label }}</span></td>
                                        <td><small><code>{{ $devLabel ?: '—' }}</code></small></td>
                                        <td><small class="text-muted">{{ $ev->detail ? Str::limit($ev->detail, 200) : '—' }}</small></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted">{{ __('No events recorded yet. Events appear after the next activation, logout, revoke, or admin device change.') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $events->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

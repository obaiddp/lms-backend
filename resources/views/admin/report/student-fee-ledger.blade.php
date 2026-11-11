@extends('layouts.admin')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2>{{ __('Student Fee Ledger') }}</h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ __('Student Fee Ledger') }}</li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="customers__area bg-style mb-30">
                        <div class="item-title d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h2>{{ __('Student Fee Ledger') }}</h2>
                            @if($user_id && $student)
                                <a href="{{ route('report.student-fee-ledger.export', ['user_id' => $user_id, 'months' => $months]) }}"
                                   class="btn btn-sm btn-success">
                                    {{ __('Export Excel') }}
                                </a>
                            @endif
                        </div>

                        <form method="get" action="{{ route('report.student-fee-ledger') }}" class="mb-4">
                            <div class="row align-items-end">
                                <div class="col-md-5">
                                    <div class="input__group mb-25">
                                        <label class="label-text-title color-heading font-medium font-14 mb-3">
                                            {{ __('Student') }} <span class="text-danger">*</span>
                                        </label>
                                        <select name="user_id" id="user_id" class="form-select" required>
                                            <option value="">{{ __('Select Student') }}</option>
                                            @foreach($students as $s)
                                                @php
                                                    $label = trim($s->name);
                                                    if ($s->user) {
                                                        $extra = collect([$s->user->email ?? null, $s->phone_number ?? $s->user->phone_number ?? null])
                                                            ->filter()
                                                            ->implode(' · ');
                                                        if ($extra) {
                                                            $label .= ' (' . $extra . ')';
                                                        }
                                                    }
                                                @endphp
                                                <option value="{{ $s->user_id }}" {{ (int) $user_id === (int) $s->user_id ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input__group mb-25">
                                        <label class="label-text-title color-heading font-medium font-14 mb-3">
                                            {{ __('Past months') }}
                                        </label>
                                        <select name="months" id="months" class="form-select">
                                            @foreach([1, 3, 6, 12, 24] as $m)
                                                <option value="{{ $m }}" {{ (int) $months === $m ? 'selected' : '' }}>
                                                    {{ $m }} {{ __('months') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 mb-25">
                                    <button type="submit" class="theme-btn theme-button1 default-hover-btn w-100">
                                        {{ __('Filter') }}
                                    </button>
                                </div>
                            </div>
                        </form>

                        @if($user_id && !$student)
                            <div class="alert alert-warning">{{ __('Student not found.') }}</div>
                        @elseif(!$user_id)
                            <p class="text-muted mb-0">{{ __('Select a student and click Filter to view their fee ledger.') }}</p>
                        @else
                            <div class="row mb-4">
                                <div class="col-lg-3 col-md-6 col-sm-6">
                                    <div class="status__box status__box__v3 bg-style">
                                        <div class="status__box__text">
                                            <h2 class="color-red">{{ $totals['total_due'] }}</h2>
                                            <h3>{{ __('Total Due') }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-6">
                                    <div class="status__box status__box__v3 bg-style">
                                        <div class="status__box__text">
                                            <h2 class="color-green">{{ $totals['total_paid'] }}</h2>
                                            <h3>{{ __('Total Paid') }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-6">
                                    <div class="status__box status__box__v3 bg-style">
                                        <div class="status__box__text">
                                            <h2 class="color-blue">{{ $totals['total_rows'] }}</h2>
                                            <h3>{{ __('Rows') }}</h3>
                                        </div>
                                    </div>
                                </div>
                                @if($since)
                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="status__box status__box__v3 bg-style">
                                            <div class="status__box__text">
                                                <h2 class="font-16">{{ $since->format('d M Y') }}</h2>
                                                <h3>{{ __('From date') }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if($student)
                                <p class="mb-3">
                                    <strong>{{ __('Student') }}:</strong>
                                    {{ $student->name }}
                                    @if($student->email) — {{ $student->email }} @endif
                                </p>
                            @endif

                            <div class="table-responsive">
                                <table class="row-border data-table-filter table-style admin-order-report-table w-100">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('Source') }}</th>
                                            <th>{{ __('Type') }}</th>
                                            <th>{{ __('Course') }}</th>
                                            <th>{{ __('Amount') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Method') }}</th>
                                            <th>{{ __('Order #') }}</th>
                                            <th>{{ __('Transaction #') }}</th>
                                            <th>{{ __('Period') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($rows as $row)
                                            <tr>
                                                <td>{{ $row->event_at ? \Carbon\Carbon::parse($row->event_at)->format('d-m-Y H:i') : '—' }}</td>
                                                <td>{{ $row->ledger_source }}</td>
                                                <td>{{ $row->fee_type }}</td>
                                                <td>{{ $row->course_title ?: '—' }}</td>
                                                <td>{{ $row->amount }}</td>
                                                <td>{{ $row->payment_status }}</td>
                                                <td>{{ $row->payment_method ?: '—' }}</td>
                                                <td>{{ $row->order_number ?: '—' }}</td>
                                                <td>{{ $row->transaction_number ?: '—' }}</td>
                                                <td>
                                                    @if($row->month && $row->year)
                                                        {{ $row->month }}/{{ $row->year }}
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">{{ __('No fee records found for this period.') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function () {
            $('#user_id').select2({ width: '100%', placeholder: "{{ __('Select Student') }}" });
        });
    </script>
@endpush

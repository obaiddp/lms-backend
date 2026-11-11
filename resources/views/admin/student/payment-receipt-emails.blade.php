@extends('layouts.admin')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb__content">
                    <div class="breadcrumb__content__left">
                        <div class="breadcrumb__title">
                            <h2>{{ __('Payment Receipt Emails') }}</h2>
                        </div>
                    </div>
                    <div class="breadcrumb__content__right">
                        <nav aria-label="breadcrumb">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ __('Payment Receipt Emails') }}</li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="customers__area bg-style mb-30 p-4">
                    <p class="text-muted mb-3">
                        {{ __('Resend the post-payment email (Payment Received | CEF Online). Preview is required before queueing.') }}
                        {{ __('Default CC = Admin Email + Financial Email + Other Emails.') }}
                        <a href="{{ route('settings.mail-configuration') }}">{{ __('Mail Configuration') }}</a>
                    </p>

                    <form id="pre-form" class="mb-4">
                        @csrf
                        <div class="row">
                            <div class="col-md-2 mb-3">
                                <label class="form-label">{{ __('Month') }} <span class="text-danger">*</span></label>
                                <select name="month" id="pre-month" class="form-control" required>
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" @if($m === $defaultMonth) selected @endif>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">{{ __('Year') }} <span class="text-danger">*</span></label>
                                <select name="year" id="pre-year" class="form-control" required>
                                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                        <option value="{{ $y }}" @if($y === $defaultYear) selected @endif>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">{{ __('Course') }}</label>
                                <select name="course_id" id="pre-course" class="form-control">
                                    <option value="">{{ __('All live courses') }}</option>
                                    @foreach($allCourses as $course)
                                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">{{ __('Student search') }}</label>
                                <input type="text" name="student_search" id="pre-student-search" class="form-control" placeholder="{{ __('Name or email') }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">{{ __('Payment method') }}</label>
                                <select name="payment_method" id="pre-payment-method" class="form-control">
                                    <option value="">{{ __('All') }}</option>
                                    <option value="meezan_bank">meezan_bank</option>
                                    <option value="faysal_bank">faysal_bank</option>
                                    <option value="bank">bank</option>
                                    <option value="cash">cash</option>
                                </select>
                            </div>
                        </div>

                        <hr>
                        <h5>{{ __('Recipients for this run') }}</h5>
                        <div class="row align-items-center mb-3">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="pre-include-student" name="include_student" value="1" @if($sendToStudentDefault) checked @endif>
                                    <label class="form-check-label" for="pre-include-student">{{ __('Student (To)') }}</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="pre-include-cc" name="include_cc" value="1" checked>
                                    <label class="form-check-label" for="pre-include-cc">{{ __('Settings CC') }}</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="pre-include-bcc" name="include_bcc" value="1" checked>
                                    <label class="form-check-label" for="pre-include-bcc">{{ __('Settings BCC') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('CC override (optional)') }}</label>
                                <textarea name="cc_override" id="pre-cc-override" class="form-control" rows="2" placeholder="{{ $settingsCc ?: __('Leave empty to use admin/financial/other emails') }}"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('BCC override (optional)') }}</label>
                                <textarea name="bcc_override" id="pre-bcc-override" class="form-control" rows="2" placeholder="{{ $settingsBcc ?: __('Leave empty to use payment_receipt_bcc') }}"></textarea>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" id="pre-preview-btn" class="btn btn-primary">{{ __('Preview (dry-run)') }}</button>
                            <button type="button" id="pre-resend-btn" class="btn btn-success" disabled>{{ __('Queue send for these N') }}</button>
                        </div>
                    </form>

                    <div id="pre-summary" class="alert alert-info d-none"></div>
                    <div id="pre-message" class="alert d-none"></div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="pre-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Order') }}</th>
                                    <th>{{ __('Student') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Course') }}</th>
                                    <th>{{ __('Period') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Method') }}</th>
                                    <th>{{ __('To / CC / BCC') }}</th>
                                </tr>
                            </thead>
                            <tbody id="pre-tbody">
                                <tr>
                                    <td colspan="8" class="text-center text-muted">{{ __('Run Preview to list matching paid orders.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
(function () {
    const previewUrl = @json(route('admin.payment-receipt-emails.preview'));
    const resendUrl = @json(route('admin.payment-receipt-emails.resend'));
    const csrf = document.querySelector('#pre-form input[name="_token"]').value;

    let lastPreview = null;

    function formPayload() {
        return {
            month: document.getElementById('pre-month').value,
            year: document.getElementById('pre-year').value,
            course_id: document.getElementById('pre-course').value || null,
            student_search: document.getElementById('pre-student-search').value || null,
            payment_method: document.getElementById('pre-payment-method').value || null,
            include_student: document.getElementById('pre-include-student').checked ? 1 : 0,
            include_cc: document.getElementById('pre-include-cc').checked ? 1 : 0,
            include_bcc: document.getElementById('pre-include-bcc').checked ? 1 : 0,
            cc_override: document.getElementById('pre-cc-override').value,
            bcc_override: document.getElementById('pre-bcc-override').value,
        };
    }

    function showMessage(text, type) {
        const el = document.getElementById('pre-message');
        el.className = 'alert alert-' + (type || 'info');
        el.textContent = text;
        el.classList.remove('d-none');
    }

    function invalidatePreview() {
        lastPreview = null;
        document.getElementById('pre-resend-btn').disabled = true;
        document.getElementById('pre-summary').classList.add('d-none');
    }

    ['pre-month','pre-year','pre-course','pre-student-search','pre-payment-method',
     'pre-include-student','pre-include-cc','pre-include-bcc','pre-cc-override','pre-bcc-override'
    ].forEach(function (id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.addEventListener('change', invalidatePreview);
        el.addEventListener('input', invalidatePreview);
    });

    document.getElementById('pre-preview-btn').addEventListener('click', function () {
        const btn = this;
        btn.disabled = true;
        showMessage('{{ __("Running dry-run preview…") }}', 'info');

        fetch(previewUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify(formPayload()),
        })
        .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
        .then(function (res) {
            btn.disabled = false;
            if (!res.ok || !res.j.success) {
                lastPreview = null;
                document.getElementById('pre-resend-btn').disabled = true;
                showMessage((res.j && res.j.message) || '{{ __("Preview failed") }}', 'danger');
                return;
            }

            lastPreview = res.j.data;
            const data = lastPreview;
            const summary = document.getElementById('pre-summary');
            summary.classList.remove('d-none');
            summary.innerHTML =
                '<strong>' + data.count + '</strong> {{ __("paid order(s) match") }}. ' +
                '{{ __("Batches") }}: ~' + data.batch_estimate +
                ' ({{ __("size") }} ' + data.batch_size + ', {{ __("delay") }} ' + data.batch_delay_minutes + ' {{ __("min") }}). ' +
                '{{ __("CC") }}: ' + (data.recipients_summary.cc.join(', ') || '(none)') + '. ' +
                '{{ __("BCC") }}: ' + (data.recipients_summary.bcc.join(', ') || '(none)') + '.';

            const tbody = document.getElementById('pre-tbody');
            tbody.innerHTML = '';
            if (!data.rows.length) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">{{ __("No matching paid orders.") }}</td></tr>';
                document.getElementById('pre-resend-btn').disabled = true;
            } else {
                data.rows.forEach(function (row) {
                    const tr = document.createElement('tr');
                    tr.innerHTML =
                        '<td>' + row.order_id + '</td>' +
                        '<td>' + (row.student_name || '') + '</td>' +
                        '<td>' + (row.student_email || '') + '</td>' +
                        '<td>' + (row.course_title || '') + '</td>' +
                        '<td>' + (row.month || '') + '/' + (row.year || '') + '</td>' +
                        '<td>' + row.amount + '</td>' +
                        '<td>' + (row.payment_method || '') + '</td>' +
                        '<td><small>To: ' + (row.to.join(', ') || '-') +
                        '<br>CC: ' + (row.cc.join(', ') || '-') +
                        '<br>BCC: ' + (row.bcc.join(', ') || '-') + '</small></td>';
                    tbody.appendChild(tr);
                });
                document.getElementById('pre-resend-btn').disabled = false;
                document.getElementById('pre-resend-btn').textContent =
                    '{{ __("Queue send for these") }} ' + data.count;
            }

            showMessage(res.j.message || '{{ __("Preview ready") }}', 'success');
        })
        .catch(function (err) {
            btn.disabled = false;
            showMessage(err.message || '{{ __("Preview request failed") }}', 'danger');
        });
    });

    document.getElementById('pre-resend-btn').addEventListener('click', function () {
        if (!lastPreview || !lastPreview.order_ids || !lastPreview.order_ids.length) {
            showMessage('{{ __("Run Preview first.") }}', 'warning');
            return;
        }

        if (!window.confirm('{{ __("Queue payment receipt email send for") }} ' + lastPreview.count + ' {{ __("order(s)?") }}')) {
            return;
        }

        const btn = this;
        btn.disabled = true;
        const payload = formPayload();
        payload.order_ids = lastPreview.order_ids;
        payload.confirm_token = lastPreview.confirm_token;

        fetch(resendUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify(payload),
        })
        .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
        .then(function (res) {
            if (!res.ok || !res.j.success) {
                btn.disabled = false;
                showMessage((res.j && res.j.message) || '{{ __("Queue failed") }}', 'danger');
                return;
            }
            showMessage(res.j.message || '{{ __("Queued successfully") }}', 'success');
            invalidatePreview();
            btn.textContent = '{{ __("Queue send for these N") }}';
        })
        .catch(function (err) {
            btn.disabled = false;
            showMessage(err.message || '{{ __("Queue request failed") }}', 'danger');
        });
    });
})();
</script>
@endpush

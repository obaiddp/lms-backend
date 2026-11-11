@extends('layouts.admin')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb__content">
                    <div class="breadcrumb__content__left">
                        <div class="breadcrumb__title">
                            <h2>{{ __('Monthly Fee Invoice Emails') }}</h2>
                        </div>
                    </div>
                    <div class="breadcrumb__content__right">
                        <nav aria-label="breadcrumb">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ __('Fee Invoice Emails') }}</li>
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
                        {{ __('Preview is required before queueing. No emails are sent on Preview. Queue Send uses the exact cart IDs from the last preview (confirm token).') }}
                        {{ __('One SMTP message per invoice includes To + CC + BCC together.') }}
                        <a href="{{ route('settings.mail-configuration') }}">{{ __('Edit default CC/BCC in Mail Configuration') }}</a>
                    </p>

                    <form id="mfi-form" class="mb-4">
                        @csrf
                        <div class="row">
                            <div class="col-md-2 mb-3">
                                <label class="form-label">{{ __('Month') }} <span class="text-danger">*</span></label>
                                <select name="month" id="mfi-month" class="form-control" required>
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" @if($m === $defaultMonth) selected @endif>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">{{ __('Year') }} <span class="text-danger">*</span></label>
                                <select name="year" id="mfi-year" class="form-control" required>
                                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                        <option value="{{ $y }}" @if($y === $defaultYear) selected @endif>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">{{ __('Course') }}</label>
                                <select name="course_id" id="mfi-course" class="form-control">
                                    <option value="">{{ __('All live courses') }}</option>
                                    @foreach($allCourses as $course)
                                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">{{ __('Student search') }}</label>
                                <input type="text" name="student_search" id="mfi-student-search" class="form-control" placeholder="{{ __('Name or email') }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">{{ __('Payment status') }}</label>
                                <select name="payment_status" id="mfi-payment-status" class="form-control">
                                    <option value="">{{ __('All') }}</option>
                                    <option value="unpaid">{{ __('Unpaid') }}</option>
                                    <option value="paid">{{ __('Paid') }}</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">{{ __('Has PDF') }}</label>
                                <select name="has_pdf" id="mfi-has-pdf" class="form-control">
                                    <option value="">{{ __('Any') }}</option>
                                    <option value="1">{{ __('Yes') }}</option>
                                    <option value="0">{{ __('No') }}</option>
                                </select>
                            </div>
                        </div>

                        <hr>
                        <h5>{{ __('Recipients for this run') }}</h5>
                        <div class="row align-items-center mb-3">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="mfi-include-student" name="include_student" value="1" @if($sendToStudentDefault) checked @endif>
                                    <label class="form-check-label" for="mfi-include-student">{{ __('Student (To)') }}</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="mfi-include-cc" name="include_cc" value="1" checked>
                                    <label class="form-check-label" for="mfi-include-cc">{{ __('Settings CC') }}</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="mfi-include-bcc" name="include_bcc" value="1" checked>
                                    <label class="form-check-label" for="mfi-include-bcc">{{ __('Settings BCC') }}</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="mfi-include-zero" name="include_zero_price" value="1">
                                    <label class="form-check-label" for="mfi-include-zero">{{ __('Include zero-price') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('CC override (optional)') }}</label>
                                <textarea name="cc_override" id="mfi-cc-override" class="form-control" rows="2" placeholder="{{ $settingsCc ?: __('Leave empty to use settings CC') }}"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('BCC override (optional)') }}</label>
                                <textarea name="bcc_override" id="mfi-bcc-override" class="form-control" rows="2" placeholder="{{ $settingsBcc ?: __('Leave empty to use settings BCC') }}"></textarea>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" id="mfi-preview-btn" class="btn btn-primary">{{ __('Preview (dry-run)') }}</button>
                            <button type="button" id="mfi-resend-btn" class="btn btn-success" disabled>{{ __('Queue send for these N') }}</button>
                        </div>
                    </form>

                    <div id="mfi-summary" class="alert alert-info d-none"></div>
                    <div id="mfi-message" class="alert d-none"></div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="mfi-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Cart') }}</th>
                                    <th>{{ __('Student') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Course') }}</th>
                                    <th>{{ __('Period') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('PDF') }}</th>
                                    <th>{{ __('Paid') }}</th>
                                    <th>{{ __('To / CC / BCC') }}</th>
                                </tr>
                            </thead>
                            <tbody id="mfi-tbody">
                                <tr>
                                    <td colspan="9" class="text-center text-muted">{{ __('Run Preview to list matching invoices.') }}</td>
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
    const previewUrl = @json(route('admin.monthly-fee-invoices.preview'));
    const resendUrl = @json(route('admin.monthly-fee-invoices.resend'));
    const csrf = document.querySelector('#mfi-form input[name="_token"]').value;

    let lastPreview = null;

    function formPayload() {
        const payload = {
            month: document.getElementById('mfi-month').value,
            year: document.getElementById('mfi-year').value,
            course_id: document.getElementById('mfi-course').value || null,
            student_search: document.getElementById('mfi-student-search').value || null,
            payment_status: document.getElementById('mfi-payment-status').value || null,
            has_pdf: document.getElementById('mfi-has-pdf').value || null,
            include_student: document.getElementById('mfi-include-student').checked ? 1 : 0,
            include_cc: document.getElementById('mfi-include-cc').checked ? 1 : 0,
            include_bcc: document.getElementById('mfi-include-bcc').checked ? 1 : 0,
            include_zero_price: document.getElementById('mfi-include-zero').checked ? 1 : 0,
            cc_override: document.getElementById('mfi-cc-override').value,
            bcc_override: document.getElementById('mfi-bcc-override').value,
        };
        return payload;
    }

    function showMessage(text, type) {
        const el = document.getElementById('mfi-message');
        el.className = 'alert alert-' + (type || 'info');
        el.textContent = text;
        el.classList.remove('d-none');
    }

    function invalidatePreview() {
        lastPreview = null;
        document.getElementById('mfi-resend-btn').disabled = true;
        document.getElementById('mfi-summary').classList.add('d-none');
    }

    ['mfi-month','mfi-year','mfi-course','mfi-student-search','mfi-payment-status','mfi-has-pdf',
     'mfi-include-student','mfi-include-cc','mfi-include-bcc','mfi-include-zero','mfi-cc-override','mfi-bcc-override'
    ].forEach(function (id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.addEventListener('change', invalidatePreview);
        el.addEventListener('input', invalidatePreview);
    });

    document.getElementById('mfi-preview-btn').addEventListener('click', function () {
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
                document.getElementById('mfi-resend-btn').disabled = true;
                showMessage((res.j && res.j.message) || '{{ __("Preview failed") }}', 'danger');
                return;
            }

            lastPreview = res.j.data;
            const data = lastPreview;
            const summary = document.getElementById('mfi-summary');
            summary.classList.remove('d-none');
            summary.innerHTML =
                '<strong>' + data.count + '</strong> {{ __("invoice(s) match") }}. ' +
                '{{ __("Batches") }}: ~' + data.batch_estimate +
                ' ({{ __("size") }} ' + data.batch_size + ', {{ __("delay") }} ' + data.batch_delay_minutes + ' {{ __("min") }}). ' +
                '{{ __("CC") }}: ' + (data.recipients_summary.cc.join(', ') || '(none)') + '. ' +
                '{{ __("BCC") }}: ' + (data.recipients_summary.bcc.join(', ') || '(none)') + '.';

            const tbody = document.getElementById('mfi-tbody');
            tbody.innerHTML = '';
            if (!data.rows.length) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">{{ __("No matching invoices.") }}</td></tr>';
                document.getElementById('mfi-resend-btn').disabled = true;
            } else {
                data.rows.forEach(function (row) {
                    const tr = document.createElement('tr');
                    tr.innerHTML =
                        '<td>' + row.cart_id + '</td>' +
                        '<td>' + (row.student_name || '') + '</td>' +
                        '<td>' + (row.student_email || '') + '</td>' +
                        '<td>' + (row.course_title || '') + '</td>' +
                        '<td>' + (row.month || '') + '/' + (row.year || '') + '</td>' +
                        '<td>' + row.amount + '</td>' +
                        '<td>' + (row.pdf_exists ? 'yes' : 'no') + '</td>' +
                        '<td>' + (row.paid ? 'yes' : 'no') + '</td>' +
                        '<td><small>To: ' + (row.to.join(', ') || '-') +
                        '<br>CC: ' + (row.cc.join(', ') || '-') +
                        '<br>BCC: ' + (row.bcc.join(', ') || '-') + '</small></td>';
                    tbody.appendChild(tr);
                });
                document.getElementById('mfi-resend-btn').disabled = false;
                document.getElementById('mfi-resend-btn').textContent =
                    '{{ __("Queue send for these") }} ' + data.count;
            }

            showMessage(res.j.message || '{{ __("Preview ready") }}', 'success');
        })
        .catch(function (err) {
            btn.disabled = false;
            showMessage(err.message || '{{ __("Preview request failed") }}', 'danger');
        });
    });

    document.getElementById('mfi-resend-btn').addEventListener('click', function () {
        if (!lastPreview || !lastPreview.cart_ids || !lastPreview.cart_ids.length) {
            showMessage('{{ __("Run Preview first.") }}', 'warning');
            return;
        }

        if (!window.confirm('{{ __("Queue email send for") }} ' + lastPreview.count + ' {{ __("invoice(s)? This cannot be undone easily.") }}')) {
            return;
        }

        const btn = this;
        btn.disabled = true;
        const payload = formPayload();
        payload.cart_ids = lastPreview.cart_ids;
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

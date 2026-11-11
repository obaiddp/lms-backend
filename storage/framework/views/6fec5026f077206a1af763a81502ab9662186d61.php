<?php $__env->startSection('content'); ?>
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb__content">
                    <div class="breadcrumb__content__left">
                        <div class="breadcrumb__title">
                            <h2><?php echo e(__('Monthly Fee Invoice Emails')); ?></h2>
                        </div>
                    </div>
                    <div class="breadcrumb__content__right">
                        <nav aria-label="breadcrumb">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Fee Invoice Emails')); ?></li>
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
                        <?php echo e(__('Preview is required before queueing. No emails are sent on Preview. Queue Send uses the exact cart IDs from the last preview (confirm token).')); ?>

                        <?php echo e(__('One SMTP message per invoice includes To + CC + BCC together.')); ?>

                        <a href="<?php echo e(route('settings.mail-configuration')); ?>"><?php echo e(__('Edit default CC/BCC in Mail Configuration')); ?></a>
                    </p>

                    <form id="mfi-form" class="mb-4">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-2 mb-3">
                                <label class="form-label"><?php echo e(__('Month')); ?> <span class="text-danger">*</span></label>
                                <select name="month" id="mfi-month" class="form-control" required>
                                    <?php for($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?php echo e($m); ?>" <?php if($m === $defaultMonth): ?> selected <?php endif; ?>><?php echo e(date('F', mktime(0,0,0,$m,1))); ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label"><?php echo e(__('Year')); ?> <span class="text-danger">*</span></label>
                                <select name="year" id="mfi-year" class="form-control" required>
                                    <?php for($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                                        <option value="<?php echo e($y); ?>" <?php if($y === $defaultYear): ?> selected <?php endif; ?>><?php echo e($y); ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label"><?php echo e(__('Course')); ?></label>
                                <select name="course_id" id="mfi-course" class="form-control">
                                    <option value=""><?php echo e(__('All live courses')); ?></option>
                                    <?php $__currentLoopData = $allCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($course->id); ?>"><?php echo e($course->title); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label"><?php echo e(__('Student search')); ?></label>
                                <input type="text" name="student_search" id="mfi-student-search" class="form-control" placeholder="<?php echo e(__('Name or email')); ?>">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label"><?php echo e(__('Payment status')); ?></label>
                                <select name="payment_status" id="mfi-payment-status" class="form-control">
                                    <option value=""><?php echo e(__('All')); ?></option>
                                    <option value="unpaid"><?php echo e(__('Unpaid')); ?></option>
                                    <option value="paid"><?php echo e(__('Paid')); ?></option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label"><?php echo e(__('Has PDF')); ?></label>
                                <select name="has_pdf" id="mfi-has-pdf" class="form-control">
                                    <option value=""><?php echo e(__('Any')); ?></option>
                                    <option value="1"><?php echo e(__('Yes')); ?></option>
                                    <option value="0"><?php echo e(__('No')); ?></option>
                                </select>
                            </div>
                        </div>

                        <hr>
                        <h5><?php echo e(__('Recipients for this run')); ?></h5>
                        <div class="row align-items-center mb-3">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="mfi-include-student" name="include_student" value="1" <?php if($sendToStudentDefault): ?> checked <?php endif; ?>>
                                    <label class="form-check-label" for="mfi-include-student"><?php echo e(__('Student (To)')); ?></label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="mfi-include-cc" name="include_cc" value="1" checked>
                                    <label class="form-check-label" for="mfi-include-cc"><?php echo e(__('Settings CC')); ?></label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="mfi-include-bcc" name="include_bcc" value="1" checked>
                                    <label class="form-check-label" for="mfi-include-bcc"><?php echo e(__('Settings BCC')); ?></label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="mfi-include-zero" name="include_zero_price" value="1">
                                    <label class="form-check-label" for="mfi-include-zero"><?php echo e(__('Include zero-price')); ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><?php echo e(__('CC override (optional)')); ?></label>
                                <textarea name="cc_override" id="mfi-cc-override" class="form-control" rows="2" placeholder="<?php echo e($settingsCc ?: __('Leave empty to use settings CC')); ?>"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><?php echo e(__('BCC override (optional)')); ?></label>
                                <textarea name="bcc_override" id="mfi-bcc-override" class="form-control" rows="2" placeholder="<?php echo e($settingsBcc ?: __('Leave empty to use settings BCC')); ?>"></textarea>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" id="mfi-preview-btn" class="btn btn-primary"><?php echo e(__('Preview (dry-run)')); ?></button>
                            <button type="button" id="mfi-resend-btn" class="btn btn-success" disabled><?php echo e(__('Queue send for these N')); ?></button>
                        </div>
                    </form>

                    <div id="mfi-summary" class="alert alert-info d-none"></div>
                    <div id="mfi-message" class="alert d-none"></div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="mfi-table">
                            <thead>
                                <tr>
                                    <th><?php echo e(__('Cart')); ?></th>
                                    <th><?php echo e(__('Student')); ?></th>
                                    <th><?php echo e(__('Email')); ?></th>
                                    <th><?php echo e(__('Course')); ?></th>
                                    <th><?php echo e(__('Period')); ?></th>
                                    <th><?php echo e(__('Amount')); ?></th>
                                    <th><?php echo e(__('PDF')); ?></th>
                                    <th><?php echo e(__('Paid')); ?></th>
                                    <th><?php echo e(__('To / CC / BCC')); ?></th>
                                </tr>
                            </thead>
                            <tbody id="mfi-tbody">
                                <tr>
                                    <td colspan="9" class="text-center text-muted"><?php echo e(__('Run Preview to list matching invoices.')); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
<script>
(function () {
    const previewUrl = <?php echo json_encode(route('admin.monthly-fee-invoices.preview'), 15, 512) ?>;
    const resendUrl = <?php echo json_encode(route('admin.monthly-fee-invoices.resend'), 15, 512) ?>;
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
        showMessage('<?php echo e(__("Running dry-run preview…")); ?>', 'info');

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
                showMessage((res.j && res.j.message) || '<?php echo e(__("Preview failed")); ?>', 'danger');
                return;
            }

            lastPreview = res.j.data;
            const data = lastPreview;
            const summary = document.getElementById('mfi-summary');
            summary.classList.remove('d-none');
            summary.innerHTML =
                '<strong>' + data.count + '</strong> <?php echo e(__("invoice(s) match")); ?>. ' +
                '<?php echo e(__("Batches")); ?>: ~' + data.batch_estimate +
                ' (<?php echo e(__("size")); ?> ' + data.batch_size + ', <?php echo e(__("delay")); ?> ' + data.batch_delay_minutes + ' <?php echo e(__("min")); ?>). ' +
                '<?php echo e(__("CC")); ?>: ' + (data.recipients_summary.cc.join(', ') || '(none)') + '. ' +
                '<?php echo e(__("BCC")); ?>: ' + (data.recipients_summary.bcc.join(', ') || '(none)') + '.';

            const tbody = document.getElementById('mfi-tbody');
            tbody.innerHTML = '';
            if (!data.rows.length) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted"><?php echo e(__("No matching invoices.")); ?></td></tr>';
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
                    '<?php echo e(__("Queue send for these")); ?> ' + data.count;
            }

            showMessage(res.j.message || '<?php echo e(__("Preview ready")); ?>', 'success');
        })
        .catch(function (err) {
            btn.disabled = false;
            showMessage(err.message || '<?php echo e(__("Preview request failed")); ?>', 'danger');
        });
    });

    document.getElementById('mfi-resend-btn').addEventListener('click', function () {
        if (!lastPreview || !lastPreview.cart_ids || !lastPreview.cart_ids.length) {
            showMessage('<?php echo e(__("Run Preview first.")); ?>', 'warning');
            return;
        }

        if (!window.confirm('<?php echo e(__("Queue email send for")); ?> ' + lastPreview.count + ' <?php echo e(__("invoice(s)? This cannot be undone easily.")); ?>')) {
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
                showMessage((res.j && res.j.message) || '<?php echo e(__("Queue failed")); ?>', 'danger');
                return;
            }
            showMessage(res.j.message || '<?php echo e(__("Queued successfully")); ?>', 'success');
            invalidatePreview();
            btn.textContent = '<?php echo e(__("Queue send for these N")); ?>';
        })
        .catch(function (err) {
            btn.disabled = false;
            showMessage(err.message || '<?php echo e(__("Queue request failed")); ?>', 'danger');
        });
    });
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/student/monthly-fee-invoices.blade.php ENDPATH**/ ?>
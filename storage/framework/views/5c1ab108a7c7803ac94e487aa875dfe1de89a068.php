<?php $__env->startSection('content'); ?>
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb__content">
                    <div class="breadcrumb__content__left">
                        <div class="breadcrumb__title">
                            <h2><?php echo e(__('Payment Receipt Emails')); ?></h2>
                        </div>
                    </div>
                    <div class="breadcrumb__content__right">
                        <nav aria-label="breadcrumb">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Payment Receipt Emails')); ?></li>
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
                        <?php echo e(__('Resend the post-payment email (Payment Received | CEF Online). Preview is required before queueing.')); ?>

                        <?php echo e(__('Default CC = Admin Email + Financial Email + Other Emails.')); ?>

                        <a href="<?php echo e(route('settings.mail-configuration')); ?>"><?php echo e(__('Mail Configuration')); ?></a>
                    </p>

                    <form id="pre-form" class="mb-4">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-2 mb-3">
                                <label class="form-label"><?php echo e(__('Month')); ?> <span class="text-danger">*</span></label>
                                <select name="month" id="pre-month" class="form-control" required>
                                    <?php for($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?php echo e($m); ?>" <?php if($m === $defaultMonth): ?> selected <?php endif; ?>><?php echo e(date('F', mktime(0,0,0,$m,1))); ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label"><?php echo e(__('Year')); ?> <span class="text-danger">*</span></label>
                                <select name="year" id="pre-year" class="form-control" required>
                                    <?php for($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                                        <option value="<?php echo e($y); ?>" <?php if($y === $defaultYear): ?> selected <?php endif; ?>><?php echo e($y); ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label"><?php echo e(__('Course')); ?></label>
                                <select name="course_id" id="pre-course" class="form-control">
                                    <option value=""><?php echo e(__('All live courses')); ?></option>
                                    <?php $__currentLoopData = $allCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($course->id); ?>"><?php echo e($course->title); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label"><?php echo e(__('Student search')); ?></label>
                                <input type="text" name="student_search" id="pre-student-search" class="form-control" placeholder="<?php echo e(__('Name or email')); ?>">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label"><?php echo e(__('Payment method')); ?></label>
                                <select name="payment_method" id="pre-payment-method" class="form-control">
                                    <option value=""><?php echo e(__('All')); ?></option>
                                    <option value="meezan_bank">meezan_bank</option>
                                    <option value="faysal_bank">faysal_bank</option>
                                    <option value="bank">bank</option>
                                    <option value="cash">cash</option>
                                </select>
                            </div>
                        </div>

                        <hr>
                        <h5><?php echo e(__('Recipients for this run')); ?></h5>
                        <div class="row align-items-center mb-3">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="pre-include-student" name="include_student" value="1" <?php if($sendToStudentDefault): ?> checked <?php endif; ?>>
                                    <label class="form-check-label" for="pre-include-student"><?php echo e(__('Student (To)')); ?></label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="pre-include-cc" name="include_cc" value="1" checked>
                                    <label class="form-check-label" for="pre-include-cc"><?php echo e(__('Settings CC')); ?></label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="pre-include-bcc" name="include_bcc" value="1" checked>
                                    <label class="form-check-label" for="pre-include-bcc"><?php echo e(__('Settings BCC')); ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><?php echo e(__('CC override (optional)')); ?></label>
                                <textarea name="cc_override" id="pre-cc-override" class="form-control" rows="2" placeholder="<?php echo e($settingsCc ?: __('Leave empty to use admin/financial/other emails')); ?>"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><?php echo e(__('BCC override (optional)')); ?></label>
                                <textarea name="bcc_override" id="pre-bcc-override" class="form-control" rows="2" placeholder="<?php echo e($settingsBcc ?: __('Leave empty to use payment_receipt_bcc')); ?>"></textarea>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" id="pre-preview-btn" class="btn btn-primary"><?php echo e(__('Preview (dry-run)')); ?></button>
                            <button type="button" id="pre-resend-btn" class="btn btn-success" disabled><?php echo e(__('Queue send for these N')); ?></button>
                        </div>
                    </form>

                    <div id="pre-summary" class="alert alert-info d-none"></div>
                    <div id="pre-message" class="alert d-none"></div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="pre-table">
                            <thead>
                                <tr>
                                    <th><?php echo e(__('Order')); ?></th>
                                    <th><?php echo e(__('Student')); ?></th>
                                    <th><?php echo e(__('Email')); ?></th>
                                    <th><?php echo e(__('Course')); ?></th>
                                    <th><?php echo e(__('Period')); ?></th>
                                    <th><?php echo e(__('Amount')); ?></th>
                                    <th><?php echo e(__('Method')); ?></th>
                                    <th><?php echo e(__('To / CC / BCC')); ?></th>
                                </tr>
                            </thead>
                            <tbody id="pre-tbody">
                                <tr>
                                    <td colspan="8" class="text-center text-muted"><?php echo e(__('Run Preview to list matching paid orders.')); ?></td>
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
    const previewUrl = <?php echo json_encode(route('admin.payment-receipt-emails.preview'), 15, 512) ?>;
    const resendUrl = <?php echo json_encode(route('admin.payment-receipt-emails.resend'), 15, 512) ?>;
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
                document.getElementById('pre-resend-btn').disabled = true;
                showMessage((res.j && res.j.message) || '<?php echo e(__("Preview failed")); ?>', 'danger');
                return;
            }

            lastPreview = res.j.data;
            const data = lastPreview;
            const summary = document.getElementById('pre-summary');
            summary.classList.remove('d-none');
            summary.innerHTML =
                '<strong>' + data.count + '</strong> <?php echo e(__("paid order(s) match")); ?>. ' +
                '<?php echo e(__("Batches")); ?>: ~' + data.batch_estimate +
                ' (<?php echo e(__("size")); ?> ' + data.batch_size + ', <?php echo e(__("delay")); ?> ' + data.batch_delay_minutes + ' <?php echo e(__("min")); ?>). ' +
                '<?php echo e(__("CC")); ?>: ' + (data.recipients_summary.cc.join(', ') || '(none)') + '. ' +
                '<?php echo e(__("BCC")); ?>: ' + (data.recipients_summary.bcc.join(', ') || '(none)') + '.';

            const tbody = document.getElementById('pre-tbody');
            tbody.innerHTML = '';
            if (!data.rows.length) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted"><?php echo e(__("No matching paid orders.")); ?></td></tr>';
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
                    '<?php echo e(__("Queue send for these")); ?> ' + data.count;
            }

            showMessage(res.j.message || '<?php echo e(__("Preview ready")); ?>', 'success');
        })
        .catch(function (err) {
            btn.disabled = false;
            showMessage(err.message || '<?php echo e(__("Preview request failed")); ?>', 'danger');
        });
    });

    document.getElementById('pre-resend-btn').addEventListener('click', function () {
        if (!lastPreview || !lastPreview.order_ids || !lastPreview.order_ids.length) {
            showMessage('<?php echo e(__("Run Preview first.")); ?>', 'warning');
            return;
        }

        if (!window.confirm('<?php echo e(__("Queue payment receipt email send for")); ?> ' + lastPreview.count + ' <?php echo e(__("order(s)?")); ?>')) {
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/student/payment-receipt-emails.blade.php ENDPATH**/ ?>
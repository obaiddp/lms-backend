<?php $__env->startSection('content'); ?>
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb__content">
                    <div class="breadcrumb__content__left">
                        <div class="breadcrumb__title">
                            <h2><?php echo e(__('Add License')); ?></h2>
                        </div>
                    </div>
                    <div class="breadcrumb__content__right">
                        <nav aria-label="breadcrumb">
                            <ul class="breadcrumb">
                                 <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['manage_dashboard','all_dashboard'])): ?>
                                <li class="breadcrumb-item">
                                    <a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a>
                                </li>
                                <?php endif; ?>
                                <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Add License')); ?></li>
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
                        <h2><?php echo e(__('Add License')); ?></h2>
                    </div>

                    <form action="<?php echo e(route('admin.license.store')); ?>" method="POST" id="licenseForm" class="form-horizontal">
                        <?php echo csrf_field(); ?>
                        <div class="row">

                            
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label><?php echo e(__('Vault User')); ?> <span class="text-danger">*</span></label>
                                    <select name="vault_user_id" id="vault_user_id" class="form-select" required>
                                        <option value=""><?php echo e(__('Select Vault User')); ?></option>
                                        <?php $__currentLoopData = $vaultUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vaultUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($vaultUser->id); ?>"><?php echo e($vaultUser->name); ?><?php if($vaultUser->email): ?> (<?php echo e($vaultUser->email); ?>)<?php endif; ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>

                            
                            <div class="col-md-6">
                                <div class="input__group mb-25">
                                    <label><?php echo e(__('License Key')); ?> <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" name="license_key" id="license_key" class="form-control" placeholder="Generate or Enter License Key" required>
                                        <button type="button" class="btn btn-secondary" id="generate_key_btn">Generate</button>
                                    </div>
                                </div>
                            </div>

                      

                            
                            <div class="col-md-3">
                                <div class="input__group mb-25">
                                    <label><?php echo e(__('Start Date')); ?> <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" required>
                                </div>
                            </div>

                            
                            <div class="col-md-3">
                                <div class="input__group mb-25">
                                    <label><?php echo e(__('Expiry Date')); ?> <span class="text-danger">*</span></label>
                                    <input type="date" name="expiry_date" id="expiry_date" class="form-control" required>
                                </div>
                            </div>

                            
                            <div class="col-md-12">
                                <div class="input__group mb-25">
                                    <label><?php echo e(__('Select Courses for License')); ?> <span class="text-danger">*</span></label>
                                    <select name="courses[]" id="courses" class="form-select" multiple required>
                                        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($course->id); ?>"><?php echo e($course->title); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <small class="text-muted">Hold CTRL to select multiple courses</small>
                                </div>
                            </div>

                            
                            <div class="col-md-12">
                                <div class="input__group mb-25">
                                    <label><?php echo e(__('Physical Devices (Manual Entry)')); ?></label>
                                    <div id="device-wrapper">
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
                                    </div>
                                </div>
                            </div>

                        </div>

                        
                        <div class="row mb-3">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-primary"><?php echo e(__('Save License')); ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
<script>
$(document).ready(function() {
    $("#vault_user_id, #courses").select2({ width: '100%' });

    // Generate random license key
    $("#generate_key_btn").click(function() {
        const key = 'LIC-' + Math.random().toString(36).substring(2, 10).toUpperCase() + '-' + Date.now().toString().slice(-4);
        $("#license_key").val(key);
    });

    // Add / Remove Device Inputs
    let deviceIndex = 1;
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

    // // Enforce + sign prefix in K-Sign input
    // $(document).on('input', '.k-sign', function() {
    //     if (!$(this).val().startsWith('+')) {
    //         $(this).val('+' + $(this).val().replace(/^\+/, ''));
    //     }
    // });

    // Validate date difference before submission
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
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/license/create.blade.php ENDPATH**/ ?>
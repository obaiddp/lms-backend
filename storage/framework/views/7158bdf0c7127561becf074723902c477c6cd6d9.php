<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Application Settings')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__(@$title)); ?></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-4">
                    <?php echo $__env->make('admin.application_settings.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
                <div class="col-lg-9 col-md-8">
                    <div class="customers__area bg-style mb-30">
                        <div class="item-title d-flex justify-content-between">
                            <h2><?php echo e(__(@$title)); ?></h2>
                            <button class="btn btn-success btn-sm" type="button" data-bs-toggle="modal"
                                data-bs-target="#add-todo-modal">
                                <i class="fa fa-plus"></i> <?php echo e(__('Add Bank')); ?>

                            </button>
                        </div>

                        <form action="<?php echo e(route('settings.save.setting')); ?>" class="form-horizontal" method="post">
                            <?php echo csrf_field(); ?>
                            <div class="row">
                                <div class="col-4">
                                    <div class="input__group mb-25">
                                        <label><?php echo e(__('Currency ISO Code')); ?> </label>
                                        <input type="text" name="bank_currency"
                                            value="<?php echo e(get_option('bank_currency')); ?>"
                                            class="form-control bank_currency">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <label><?php echo e(__('Conversion Rate')); ?> </label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text"><?php echo e('1 ' . get_currency_symbol() . ' = '); ?></span>
                                        <input type="number" step="any" min="0" name="bank_conversion_rate"
                                            value="<?php echo e(get_option('bank_conversion_rate') ? get_option('bank_conversion_rate') : 1); ?>"
                                            class="form-control">
                                        <span class="input-group-text bank_append_currency"></span>
                                    </div>
                                </div>

                                <div class="col-4">
                                    <div class="input__group mb-25">
                                        <label for="bank_status"><?php echo e(__('Status')); ?> </label>
                                        <div class="input-group mb-3">
                                            <select name="bank_status" id="bank_status" class="form-control">
                                                <option value="1" <?php echo e(get_option('bank_status') == 1 ? 'selected' : ''); ?>><?php echo e(__('Enable')); ?> </option>
                                                <option value="0" <?php echo e(get_option('bank_status') == '0' ? 'selected' : ''); ?>><?php echo e(__('Disable')); ?> </option>
                                            </select>
                                            <button class="btn btn-blue input-group-text" id="basic-addon2"><?php echo e(__('Update')); ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="customers__table">
                            <table id="customers-table" class="row-border data-table-filter table-style">
                                <thead>
                                    <tr>
                                        <th><?php echo e(__('SL')); ?></th>
                                        <th><?php echo e(__('Name')); ?></th>
                                        <th><?php echo e(__('Account Name')); ?></th>
                                        <th><?php echo e(__('Account Number')); ?></th>
                                        <th><?php echo e(__('Status')); ?></th>
                                        <th><?php echo e(__('Action')); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $banks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="removable-item">
                                            <td><?php echo e($loop->iteration); ?></td>
                                            <td><?php echo e($bank->name); ?></td>
                                            <td><?php echo e($bank->account_name); ?></td>
                                            <td><?php echo e($bank->account_number); ?></td>
                                            <td>
                                                <a href="<?php echo e(route('settings.bank.status', $bank->id)); ?>"> <span
                                                        class="status <?php echo e($bank->status == 1 ? 'active' : 'blocked'); ?>"><?php echo e($bank->status == 1 ? 'Active' : 'Inactive'); ?></span></a>
                                            </td>
                                            <td>
                                                <div class="action__buttons">
                                                    <a href="<?php echo e(route('settings.bank.edit', [$bank->id])); ?>"
                                                        class=" btn-action mr-1 edit" data-toggle="tooltip" title="Edit">
                                                        <img src="<?php echo e(asset('admin/images/icons/edit-2.svg')); ?>"
                                                            alt="edit">
                                                    </a>
                                                    <button class="btn-action ms-2 deleteItem"
                                                        data-formid="delete_row_form_<?php echo e($bank->id); ?>">
                                                        <img src="<?php echo e(asset('admin/images/icons/trash-2.svg')); ?>"
                                                            alt="trash">
                                                    </button>

                                                    <form action="<?php echo e(route('settings.bank.delete', $bank->id)); ?>"
                                                        method="post" id="delete_row_form_<?php echo e($bank->id); ?>">
                                                        <?php echo e(method_field('DELETE')); ?>

                                                        <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <div class="mt-3">
                                <?php echo e($banks->links()); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page content area end -->

    <!-- Add Modal section start -->
    <div class="modal fade" id="add-todo-modal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo e(__('Add Bank')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo e(route('settings.bank.store')); ?>" method="post" autocomplete="off">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="input__group mb-25">
                                    <label for="name"><?php echo e(__('Name')); ?></label>
                                    <input type="text" name="name" id="name" placeholder="<?php echo e(__('Type Bank Name')); ?>"
                                        value="<?php echo e(old('name')); ?>" required>
                                    <?php if($errors->has('name')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i>
                                            <?php echo e($errors->first('name')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input__group mb-25">
                                    <label for="account_name"><?php echo e(__('Account Name')); ?></label>
                                    <input type="text" name="account_name" id="account_name"
                                        placeholder="<?php echo e(__('Type Bank Account Name')); ?>" value="<?php echo e(old('account_name')); ?>"
                                        required>
                                    <?php if($errors->has('account_name')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i>
                                            <?php echo e($errors->first('account_name')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input__group mb-25">
                                    <label for="account_number"><?php echo e(__('Account Number')); ?></label>
                                    <input type="text" name="account_number" id="account_number"
                                        placeholder="<?php echo e(__('Type Bank Account Number')); ?>" value="<?php echo e(old('account_number')); ?>"
                                        required>
                                    <?php if($errors->has('account_number')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i>
                                            <?php echo e($errors->first('account_number')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input__group mb-25">
                                    <label for="status"><?php echo e(__('Status')); ?></label>
                                    <select name="status" id="status">
                                        <option value="1"><?php echo e(__('Active')); ?></option>
                                        <option value="0"><?php echo e(__('Inactive')); ?></option>
                                    </select>

                                    <?php if($errors->has('status')): ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i>
                                            <?php echo e($errors->first('status')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-purple"><?php echo e(__('Update')); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add Modal section end -->
<?php $__env->stopSection(); ?>

<?php $__env->startPush('style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/jquery.dataTables.min.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script src="<?php echo e(asset('admin/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/custom/data-table-page.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/custom/payment-method.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/bank/index.blade.php ENDPATH**/ ?>
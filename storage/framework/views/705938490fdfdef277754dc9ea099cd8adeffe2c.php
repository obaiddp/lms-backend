<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Email Notification Template')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a
                                            href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active"
                                        aria-current="page"><?php echo e(__('Email Notification Template')); ?></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="customers__area bg-style mb-30">
                        <div class="customers__table">
                            <table id="customers-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('Name')); ?></th>
                                    <th><?php echo e(__('Subject')); ?></th>
                                    <th><?php echo e(__('Status')); ?></th>
                                    <th><?php echo e(__('Action')); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $email_templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="removable-item">
                                        <td>
                                            <?php echo e($template->category); ?>

                                        </td>
                                        <td>
                                            <?php echo e($template->subject); ?>

                                        </td>
                                        <td>
                                            <?php if($template->status == 1): ?>
                                                Active
                                            <?php else: ?>
                                                Deactiavte
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="action__buttons">
                                                <a  href="<?php echo e(route('email-notification.edit', $template->id)); ?>" class="btn-action mr-30" title="Edit Details">
                                                    <img src="<?php echo e(asset('admin/images/icons/edit-2.svg')); ?>" alt="edit">
                                                </a>
                                                <a href="javascript:void(0);" onclick="getEditModal('<?php echo e(route('email-notification.view', $template->id)); ?>', '#page-modal')" title="Preview" class="btn-action">
                                                    <img src="<?php echo e(asset('admin/images/icons/eye-2.svg')); ?>" alt="Preview">
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <div class="mt-3">
                                <?php echo e($email_templates->links()); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Page content area end -->

    <!-- Edit Modal section start -->
    <div class="modal fade" id="page-modal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

            </div>
        </div>
    </div>
    <!-- Edit Modal section end -->

<?php $__env->stopSection(); ?>

<?php $__env->startPush('style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/jquery.dataTables.min.css')); ?>"/>
<?php $__env->startPush('script'); ?>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
    <script src="<?php echo e(asset('common/js/common_helper.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/custom/data-table-page.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/email_notification_templates/index.blade.php ENDPATH**/ ?>
<?php $__env->startPush('style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/jquery.dataTables.min.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__(@$title)); ?></h2>
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
                <div class="col-md-12">
                    <div class="customers__area bg-style mb-30">
                        <div class="item-title d-flex justify-content-between">
                            <h2><?php echo e(__('Contact Issue List')); ?></h2>
                            <a href="<?php echo e(route('contact.issue.create')); ?>" class="btn btn-success btn-sm"> <i class="fa fa-plus"></i> <?php echo e(__('Add Contact Issue')); ?> </a>
                        </div>
                        <div class="customers__table">
                            <table id="customers-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('SL')); ?></th>
                                    <th><?php echo e(__('Name')); ?></th>
                                    <th><?php echo e(__('Status')); ?></th>
                                    <th><?php echo e(__('Action')); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $contactUsIssues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $issue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="removable-item">
                                        <td><?php echo e(@$loop->iteration); ?></td>
                                        <td><?php echo e($issue->name); ?></td>
                                        <td>
                                            <?php if($issue->status == 1): ?>
                                                <span class="status bg-green"><?php echo e(__('Active')); ?></span>
                                            <?php else: ?>
                                                <span class="status bg-red"><?php echo e(__('Deactivated')); ?></span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <div class="action__buttons">
                                                <a href="<?php echo e(route('contact.issue.edit', [$issue->uuid])); ?>" class="btn-action" title="Edit">
                                                    <img src="<?php echo e(asset('admin/images/icons/edit-2.svg')); ?>" alt="edit">
                                                </a>
                                                <button class="ms-3">
                                                    <span data-formid="delete_row_form_<?php echo e($issue->uuid); ?>" class="deleteItem" title="Delete">
                                                        <img src="<?php echo e(asset('admin/images/icons/trash-2.svg')); ?>" alt="trash">
                                                    </span>
                                                </button>

                                                <form action="<?php echo e(route('contact.issue.destroy', $issue->uuid)); ?>" method="post" id="delete_row_form_<?php echo e($issue->uuid); ?>">
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
                                <?php echo e($contactUsIssues->links()); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Page content area end -->
<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
    <script src="<?php echo e(asset('admin/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/custom/data-table-page.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/contact/issue/index.blade.php ENDPATH**/ ?>
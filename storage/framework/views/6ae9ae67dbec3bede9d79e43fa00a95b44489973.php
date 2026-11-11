<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Settings')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Language Settings')); ?></li>
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
                            <h2><?php echo e(__('Language Settings')); ?></h2>
                            <a href="<?php echo e(route('language.create')); ?>" class="btn btn-success btn-sm"> <i class="fa fa-plus"></i> <?php echo e(__('Add Language')); ?> </a>
                        </div>

                        <div class="customers__table">
                            <table id="customers-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('Flag')); ?></th>
                                    <th><?php echo e(__('Language')); ?></th>
                                    <th><?php echo e(__('ISO Code')); ?></th>
                                    <th><?php echo e(__('RTL')); ?></th>
                                    <th><?php echo e(__('Action')); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="removable-item">
                                        <td>
                                            <img src="<?php echo e(getImageFile($language->flag)); ?>" height="50">
                                        </td>
                                        <td><?php echo e($language->language); ?></td>
                                        <td><?php echo e($language->iso_code); ?></td>
                                        <td><?php echo e($language->rtl == 1 ? 'Yes' : 'No'); ?></td>
                                        <td>
                                            <div class="action__buttons">
                                                <a href="<?php echo e(route('language.edit', [$language->id, $language->iso_code])); ?>" class="btn-action" title="Edit">
                                                    <img src="<?php echo e(asset('admin/images/icons/edit-2.svg')); ?>" alt="edit">
                                                </a>
                                                <?php if($language->id != 1): ?>
                                                    <a href="javascript:void(0);" data-url="<?php echo e(route('language.delete', [$language->id])); ?>" class="btn-action delete" title="Delete">
                                                        <img src="<?php echo e(asset('admin/images/icons/trash-2.svg')); ?>" alt="trash">
                                                    </a>
                                                <?php endif; ?>
                                                <a href="<?php echo e(route('language.translate', [$language->id])); ?>" class="btn-action" title="Edit">
                                                    <span class="status edit"> <?php echo e(__('Translator')); ?></span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <div class="mt-3">
                                <?php echo e($languages->links()); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page content area end -->
<?php $__env->stopSection(); ?>

<?php $__env->startPush('style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/jquery.dataTables.min.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script src="<?php echo e(asset('admin/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/custom/data-table-page.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/language/index.blade.php ENDPATH**/ ?>
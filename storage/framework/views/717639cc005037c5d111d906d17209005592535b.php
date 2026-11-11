<?php $__env->startSection('content'); ?>
<!-- Page content area start -->
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb__content">
                    <div class="breadcrumb__content__left">
                        <div class="breadcrumb__title">
                            <h2><?php echo e(__('Special Promotion Tag')); ?></h2>
                        </div>
                    </div>
                    <div class="breadcrumb__content__right">
                        <nav aria-label="breadcrumb">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                <li class="breadcrumb-item"><?php echo e(__('Course')); ?></li>
                                <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Special Promotion Tag List')); ?></li>
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
                        <h2><?php echo e(__('Special Promotion Tag List')); ?></h2>
                        <button class="btn btn-success btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#add-todo-modal">
                            <i class="fa fa-plus"></i> <?php echo e(__('Add')); ?>

                        </button>
                    </div>
                    <div class="customers__table">
                        <table id="customers-table" class="row-border data-table-filter table-style">
                            <thead>
                            <tr>
                                <th><?php echo e(__('SL')); ?>.</th>
                                <th><?php echo e(__('Name')); ?></th>
                                <th><?php echo e(__('Action')); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $specials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $special): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="removable-item">
                                <td><?php echo e(@$loop->iteration); ?></td>
                                <td><?php echo e($special->name); ?></td>
                                <td>
                                    <div class="action__buttons">
                                        <a href="<?php echo e(route('special_promotional_tag.editSpecialPromotionCourse', $special->uuid)); ?>" class="btn-action mr-1" title="View promotion details">
                                            <button class="btn btn-primary btn-sm">+/- <?php echo e(__('Course')); ?></button>
                                        </a>
                                        <a class=" btn-action mr-1 edit" data-item="<?php echo e($special); ?>" data-updateurl="<?php echo e(route('special_promotional_tag.update', @$special->uuid)); ?>"
                                           data-toggle="tooltip"
                                           title="Edit">
                                            <img src="<?php echo e(asset('admin/images/icons/edit-2.svg')); ?>" alt="edit">
                                        </a>
                                        <button class="btn-action ms-2 deleteItem" data-formid="delete_row_form_<?php echo e($special->uuid); ?>">
                                            <img src="<?php echo e(asset('admin/images/icons/trash-2.svg')); ?>" alt="trash">
                                        </button>

                                        <form action="<?php echo e(route('special_promotional_tag.delete', [$special->uuid])); ?>" method="post" id="delete_row_form_<?php echo e($special->uuid); ?>">
                                            <?php echo e(method_field('DELETE')); ?>

                                            <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- Page content area end -->

<!-- Add Modal section start -->
<div class="modal fade" id="add-todo-modal" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header border-0">
                <h5><?php echo e(__('Add Special Promotion Tag')); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?php echo e(route('special_promotional_tag.store')); ?>" method="post">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="input__group mb-25">
                        <label for="name"><?php echo e(__('Name')); ?></label>
                        <input type="text" name="name" id="name" placeholder="<?php echo e(__('Type name')); ?>" value="" required>
                        <?php if($errors->has('name')): ?>
                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('name')); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-purple"><?php echo e(__('Save')); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Add Modal section end -->

<!-- Edit Modal section start -->
<div class="modal fade edit_modal" id="add-todo-modal" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5><?php echo e(__('Edit Special Promotion Tag')); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" id="updateEditModal" method="post">
                <?php echo csrf_field(); ?>
                <?php echo method_field('patch'); ?>
                <div class="modal-body">
                    <?php echo csrf_field(); ?>
                    <div class="input__group mb-25">
                        <label for="name"><?php echo e(__('Name')); ?></label>
                        <input type="text" name="name" id="name" placeholder="<?php echo e(__('Type name')); ?>" value="" required>
                        <?php if($errors->has('name')): ?>
                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('name')); ?></span>
                        <?php endif; ?>
                    </div>

                </div>
                <div class="modal-footer">
                    <?php echo $__env->make('layouts.element.form.update-button', [], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Edit Modal section end -->

<?php $__env->stopSection(); ?>

<?php $__env->startPush('style'); ?>
<link rel="stylesheet" href="<?php echo e(asset('admin/css/jquery.dataTables.min.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
<script src="<?php echo e(asset('admin/js/jquery.dataTables.min.js')); ?>"></script>
<script src="<?php echo e(asset('admin/js/custom/data-table-page.js')); ?>"></script>

<script>
    $(function(){
        'use strict'
        $('.edit').on('click', function(e){
            e.preventDefault();
            const modal = $('.edit_modal');
            modal.find('input[name=name]').val($(this).data('item').name)
            let route = $(this).data('updateurl');
            $('#updateEditModal').attr("action", route)
            modal.modal('show')
        })
    })
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/course/special_promotion_tag/index.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Menus')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Menu')); ?></li>
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
                            <h2><?php echo e(__('Footer Left Menu List')); ?></h2>
                            <button class="btn btn-success btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#add-todo-modal">
                                <i class="fa fa-plus"></i> <?php echo e(__('Add Menu')); ?>

                            </button>
                        </div>
                        <div class="customers__table">
                            <table id="customers-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('Title')); ?></th>
                                    <th><?php echo e(__('URL')); ?></th>
                                    <th><?php echo e(__('Status')); ?></th>
                                    <th><?php echo e(__('Action')); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="removable-item">
                                        <td><?php echo e($menu->name); ?></td>
                                        <td><?php echo e($menu->url); ?></td>
                                        <td>
                                            <?php if($menu->status == 1): ?> <?php echo e(__('Active')); ?> <?php endif; ?>
                                            <?php if($menu->status == 2): ?> <?php echo e(__('Deactivated')); ?> <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="action__buttons">
                                                <a class=" btn-action mr-1 edit" data-item="<?php echo e($menu); ?>" data-updateurl="<?php echo e(route('menu.footer-company.update', @$menu->id)); ?>"
                                                   data-toggle="tooltip"
                                                   title="Edit">
                                                    <img src="<?php echo e(asset('admin/images/icons/edit-2.svg')); ?>" alt="edit">
                                                </a>
                                                <a href="javascript:void(0);" data-url="<?php echo e(route('menu.footer-company.delete', [$menu->id])); ?>" class="btn-action delete" title="Delete">
                                                    <img src="<?php echo e(asset('admin/images/icons/trash-2.svg')); ?>" alt="trash">
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <div class="mt-3">
                                <?php echo e($menus->links()); ?>

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
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5><?php echo e(__('Add Menu')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="<?php echo e(route('menu.footer-company.store')); ?>" method="post">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="input__group mb-25">
                            <label for="name"><?php echo e(__('Name')); ?></label>
                            <input type="text" name="name" id="name" placeholder="<?php echo e(__('Type name')); ?>" value="" required>
                            <?php if($errors->has('name')): ?>
                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('name')); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="input__group mb-25">
                            <label for="url"><?php echo e(__('URL')); ?></label>
                            <input type="text" name="url" id="url" placeholder="<?php echo e(__('Type Url')); ?>" value="" required>
                            <?php if($errors->has('url')): ?>
                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('url')); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="input__group">
                            <label for="status" class="text-lg-right text-black"> <?php echo e(__('Status')); ?> </label>
                            <select name="status" id="status" class="form-control">
                                <option value="">--<?php echo e(__('Select Option')); ?>--</option>
                                <option value="1"><?php echo e(__('Active')); ?></option>
                                <option value="2" ><?php echo e(__('Deactivated')); ?></option>
                            </select>
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
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo e(__('Edit Menu')); ?></h5>
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

                        <div class="input__group mb-25">
                            <label for="url"><?php echo e(__('URL')); ?></label>
                            <input type="text" name="url" id="url" placeholder="<?php echo e(__('Type Url')); ?>" value="" required>
                            <?php if($errors->has('url')): ?>
                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('url')); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="input__group">
                            <label for="status" class="text-lg-right text-black"> <?php echo e(__('Status')); ?> </label>
                            <select name="status" id="status" class="form-control">
                                <option value="">--<?php echo e(__('Select Option')); ?>--</option>
                                <option value="1"><?php echo e(__('Active')); ?></option>
                                <option value="2" ><?php echo e(__('Deactivated')); ?></option>
                            </select>
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
                modal.find('select[name=status]').val($(this).data('item').status)
                modal.find('input[name=url]').val($(this).data('item').url)
                let route = $(this).data('updateurl');
                $('#updateEditModal').attr("action", route)
                modal.modal('show')
            })
        })
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/menu/footer-company-menu-list.blade.php ENDPATH**/ ?>
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
                            <h2><?php echo e(__('Forum Category')); ?></h2>
                            <button class="btn btn-success btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#add-todo-modal">
                                <i class="fa fa-plus"></i> <?php echo e(__('Add Forum Category')); ?>

                            </button>
                        </div>
                        <div class="customers__table">
                            <table id="customers-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('SL')); ?></th>
                                    <th><?php echo e(__('Logo')); ?></th>
                                    <th><?php echo e(__('Title')); ?></th>
                                    <th><?php echo e(__('Subtitle')); ?></th>
                                    <th><?php echo e(__('Status')); ?></th>
                                    <th><?php echo e(__('Action')); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $forumCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="removable-item">
                                        <td><?php echo e($loop->iteration); ?></td>
                                        <td>
                                            <div class="user-info">
                                                <div class="user-info__img">
                                                    <img src="<?php echo e(getImageFile($category->logo)); ?>" alt="logo">
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo e(Str::limit($category->title, 20)); ?></td>
                                        <td><?php echo e(Str::limit($category->subtitle, 60)); ?></td>
                                        <td>
                                            <?php if($category->status == 1): ?>
                                                <span class="status bg-green"><?php echo e(__('Active')); ?></span>
                                            <?php else: ?>
                                                <span class="status bg-red"><?php echo e(__('Disable')); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="action__buttons">
                                                <a class=" btn-action mr-1 edit" data-logo="<?php echo e(getImageFile($category->logo)); ?>" data-item="<?php echo e($category); ?>"
                                                   data-updateurl="<?php echo e(route('admin.forum.category.update', @$category->uuid)); ?>" data-toggle="tooltip" title="Edit">
                                                    <img src="<?php echo e(asset('admin/images/icons/edit-2.svg')); ?>" alt="edit">
                                                </a>
                                                <a href="javascript:void(0);" data-url="<?php echo e(route('admin.forum.category.delete', [$category->uuid])); ?>" class="btn-action delete"
                                                   title="Delete">
                                                    <img src="<?php echo e(asset('admin/images/icons/trash-2.svg')); ?>" alt="trash">
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            <div class="mt-3">
                                <?php echo e($forumCategories->links()); ?>

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
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo e(__('Add Forum Category')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo e(route('admin.forum.category.store')); ?>" method="post" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="input__group mb-30">
                            <label for="name"><?php echo e(__('Title')); ?></label>
                            <input type="text" name="title" id="title" placeholder="<?php echo e(__('Type title')); ?>" value="" required>
                            <?php if($errors->has('title')): ?>
                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('title')); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="input__group mb-30">
                            <label for="name"><?php echo e(__('Subtitle')); ?></label>
                            <input type="text" name="subtitle" id="subtitle" placeholder="<?php echo e(__('Type title')); ?>" value="" required>
                            <?php if($errors->has('subtitle')): ?>
                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('subtitle')); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="input__group mb-30">
                            <label for="status" class="text-lg-right text-black"> <?php echo e(__('Status')); ?> </label>
                            <select name="status" id="status" class="form-control">
                                <option value="">--<?php echo e(__('Select Option')); ?>--</option>
                                <option value="1"><?php echo e(__('Active')); ?></option>
                                <option value="0"><?php echo e(__('Disable')); ?></option>
                            </select>
                        </div>
                        <div class="input__group mb-30">
                            <label for="status" class="text-lg-right text-black"> <?php echo e(__('Logo')); ?> </label>
                            <div class="upload-img-box mb-25">
                                <img src="">
                                <input type="file" name="logo" id="logo" accept="image/*" onchange="previewFile(this)">
                                <div class="upload-img-box-icon">
                                    <i class="fa fa-camera"></i>
                                </div>
                            </div>
                            <?php if($errors->has('logo')): ?>
                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('logo')); ?></span>
                            <?php endif; ?>
                            <p><?php echo e(__('Accepted Image Files')); ?>: PNG <br> <?php echo e(__('Accepted Size')); ?>: 60 x 60 (1MB)</p>

                        </div>

                        <div>
                            <button type="submit" class="btn btn-purple"><?php echo e(__('Save')); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add Modal section end -->

    <!-- Edit Modal section start -->
    <div class="modal fade edit_modal" id="add-todo-modal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo e(__('Edit Forum Category')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" id="updateEditModal" method="post" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('patch'); ?>
                    <div class="modal-body">
                        <?php echo csrf_field(); ?>
                        <div class="input__group mb-30">
                            <label for="name"><?php echo e(__('Title')); ?></label>
                            <input type="text" name="title" id="title" placeholder="<?php echo e(__('Type title')); ?>" value="" required>
                            <?php if($errors->has('title')): ?>
                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('title')); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="input__group mb-30">
                            <label for="name"><?php echo e(__('Subtitle')); ?></label>
                            <input type="text" name="subtitle" id="subtitle" placeholder="<?php echo e(__('Type title')); ?>" value="" required>
                            <?php if($errors->has('subtitle')): ?>
                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('subtitle')); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="input__group mb-30">
                            <label for="status" class="text-lg-right text-black"> <?php echo e(__('Status')); ?> </label>
                            <select name="status" id="status" class="form-control">
                                <option value="">--<?php echo e(__('Select Option')); ?>--</option>
                                <option value="1"><?php echo e(__('Active')); ?></option>
                                <option value="0"><?php echo e(__('Disable')); ?></option>
                            </select>
                        </div>
                        <div class="input__group mb-30">
                            <label for="status" class="text-lg-right text-black"> <?php echo e(__('Logo')); ?> </label>
                            <div class="upload-img-box mb-25">
                                <img id="logoSrc" src="">
                                <input type="file" name="logo" id="logo" accept="image/*" onchange="previewFile(this)">
                                <div class="upload-img-box-icon">
                                    <i class="fa fa-camera"></i>
                                </div>
                            </div>
                            <?php if($errors->has('logo')): ?>
                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('logo')); ?></span>
                            <?php endif; ?>
                            <p><?php echo e(__('Accepted Image Files')); ?>: PNG <br> <?php echo e(__('Accepted Size')); ?>: 60 x 60 (1MB)</p>

                        </div>
                        <div>
                            <?php echo $__env->make('layouts.element.form.update-button', [], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Modal section end -->

<?php $__env->stopSection(); ?>

<?php $__env->startPush('style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/jquery.dataTables.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/custom/image-preview.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script src="<?php echo e(asset('admin/js/custom/image-preview.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/js/custom/data-table-page.js')); ?>"></script>

    <script>
        $(function () {
            'use strict'
            $('.edit').on('click', function (e) {
                e.preventDefault();
                const modal = $('.edit_modal');
                modal.find('input[name=title]').val($(this).data('item').title)
                modal.find('input[name=subtitle]').val($(this).data('item').subtitle)
                modal.find('select[name=status]').val($(this).data('item').status)
                $("#logoSrc").attr("src", $(this).data('logo'));
                let route = $(this).data('updateurl');
                $('#updateEditModal').attr("action", route)
                modal.modal('show')
            })
        })
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/forum/category-list.blade.php ENDPATH**/ ?>
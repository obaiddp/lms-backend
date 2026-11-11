<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Badge')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a
                                            href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Badge')); ?></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="customers__area bg-style mb-30">
                        <div class="orders__top affiliate-tab-list-top">
                            <div class="item">
                                <div class="sort-by">
                                    <ul class="nav nav-tabs affiliate-tab-list" id="pills-tab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="pills-registration_date-tab"
                                                data-bs-toggle="pill" data-bs-target="#pills-registration_date"
                                                type="button" role="tab" aria-controls="pills-registration_date"
                                                aria-selected="true"><?php echo e(__('Membership')); ?></button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="pills-earning-tab" data-bs-toggle="pill"
                                                data-bs-target="#pills-earning" type="button" role="tab"
                                                aria-controls="pills-earning"
                                                aria-selected="true"><?php echo e(__('Author Level')); ?></button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="pills-courses_count-tab" data-bs-toggle="pill"
                                                data-bs-target="#pills-courses_count" type="button" role="tab"
                                                aria-controls="pills-courses_count"
                                                aria-selected="true"><?php echo e(__('Courses Count')); ?></button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="pills-students_count-tab" data-bs-toggle="pill"
                                                data-bs-target="#pills-students_count" type="button" role="tab"
                                                aria-controls="pills-students_count"
                                                aria-selected="true"><?php echo e(__('Students Count')); ?></button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="pills-courses_sale_count-tab"
                                                data-bs-toggle="pill" data-bs-target="#pills-courses_sale_count"
                                                type="button" role="tab" aria-controls="pills-courses_sale_count"
                                                aria-selected="true"><?php echo e(__('Courses Sale Count')); ?></button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="item">
                                <form method="POST" action="<?php echo e(route('ranking.reset_badge')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-send p-1"><?php echo e(__('Refresh user badge')); ?></button>
                                </form>
                            </div>
                        </div>
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-registration_date" role="tabpanel"
                                aria-labelledby="pills-registration_date-tab">
                                <form action="<?php echo e(route('ranking.store')); ?>" method="post"
                                    enctype="multipart/form-data">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="type" value="1">
                                    <div class="row mb-5">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="input__group mb-25">
                                                        <label for="name"><?php echo e(__('Name')); ?></label>
                                                        <input type="text" name="name" id="name"
                                                            placeholder="<?php echo e(__('Type name')); ?>"
                                                            value="<?php echo e(old('name')); ?>" required>
                                                        <?php if($errors->has('name')): ?>
                                                            <span class="text-danger"><i
                                                                    class="fas fa-exclamation-triangle"></i>
                                                                <?php echo e($errors->first('name')); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <label for=""><?php echo e(__('Rule')); ?></label>
                                                    <div class="input-group mb-25">
                                                        <span class="input-group-text"><?php echo e(__('From')); ?></span>
                                                        <input type="number" name="from" step="any"
                                                            value="0" class="form-control">
                                                        <span class="input-group-text"><?php echo e(__('To')); ?></span>
                                                        <input type="number" name="to" step="0"
                                                            value="0" class="form-control">
                                                        <span class="input-group-text"><?php echo e(getBadgeButtonName(RANKING_LEVEL_REGISTRATION)); ?></span>
                                                    </div>
                                                    <span><?php echo e(__('Create your rules carefully. So that all the possible ')); ?></span>
                                                </div>
                                                <div class="col-12">
                                                    <label for=""><?php echo e(__('Description')); ?></label>
                                                    <div class="input-group mb-25">
                                                        <textarea name="description" placeholder="<?php echo e(__('Description')); ?>" class="form-control" rows="5"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="input__group mb-25">
                                                        <label for="badge_image"><?php echo e(__('Badge Image')); ?></label>
                                                        <div class="">
                                                            <div class="upload-img-box">
                                                                <img src="">
                                                                <input type="file" name="badge_image" id="badge_image"
                                                                    accept="image/*" onchange="previewFile(this)">
                                                                <div class="upload-img-box-icon">
                                                                    <i class="fa fa-camera"></i>
                                                                    <p class="m-0"><?php echo e(__('Badge Image')); ?></p>
                                                                </div>
                                                            </div>
                                                            <?php if($errors->has('badge_image')): ?>
                                                                <span class="text-danger"><i
                                                                        class="fas fa-exclamation-triangle"></i>
                                                                    <?php echo e($errors->first('badge_image')); ?></span>
                                                            <?php endif; ?>
                                                            <p class="mb-0 mt-1"><?php echo e(__('Accepted')); ?>: PNG, JPG <br>
                                                                <?php echo e(__('Accepted Size')); ?>: 30 x 30 (100KB)</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-purple"><?php echo e(__('Save')); ?></button>
                                        </div>
                                    </div>
                                    <div class="row ">
                                        <div class="col-md-12 mt-5">
                                            <div class="customers__table">
                                                <table id="" class="row-border data-table-filter table-style">
                                                    <thead>
                                                        <tr>
                                                            <th><?php echo e(__('Image')); ?></th>
                                                            <th><?php echo e(__('Name')); ?></th>
                                                            <th><?php echo e(__('From')); ?></th>
                                                            <th><?php echo e(__('To')); ?></th>
                                                            <th><?php echo e(__('Descripiton')); ?></th>
                                                            <th><?php echo e(__('Action')); ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $__currentLoopData = $levels->where('type', RANKING_LEVEL_REGISTRATION); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <tr class="removable-item">
                                                                <td>
                                                                    <div class="admin-dashboard-blog-list-img">
                                                                        <img style="width:30px" src="<?php echo e(getImageFile($level->image_path)); ?>">
                                                                    </div>
                                                                </td>
                                                                <td><?php echo e($level->name); ?></td>
                                                                <td>
                                                                    <?php echo e(number_format($level->from)); ?>

                                                                </td>
                                                                <td><?php echo e(number_format($level->to)); ?></td>
                                                                <td><?php echo e($level->description); ?></td>
                                                                <td>
                                                                    <div class="action__buttons">
                                                                        <a href="<?php echo e(route('ranking.edit', $level->uuid)); ?>"
                                                                            class=" btn-action mr-1 edit"
                                                                            data-toggle="tooltip" title="Edit">
                                                                            <img style="width:30px" src="<?php echo e(asset('admin/images/icons/edit-2.svg')); ?>"
                                                                                alt="edit">
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            data-url="<?php echo e(route('ranking.delete', [$level->uuid])); ?>"
                                                                            class="btn-action delete" title="Delete">
                                                                            <img style="width:30px" src="<?php echo e(asset('admin/images/icons/trash-2.svg')); ?>"
                                                                                alt="trash">
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </tbody>
                                                </table>
                                                <div class="mt-3">
                                                    <?php echo e($levels->links()); ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="pills-earning" role="tabpanel"
                                aria-labelledby="pills-earning-tab">
                                <form action="<?php echo e(route('ranking.store')); ?>" method="post"
                                    enctype="multipart/form-data">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="type" value="2">
                                    <div class="row mb-5">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="input__group mb-25">
                                                        <label for="name"><?php echo e(__('Name')); ?></label>
                                                        <input type="text" name="name" id="name"
                                                            placeholder="<?php echo e(__('Type name')); ?>"
                                                            value="<?php echo e(old('name')); ?>" required>
                                                        <?php if($errors->has('name')): ?>
                                                            <span class="text-danger"><i
                                                                    class="fas fa-exclamation-triangle"></i>
                                                                <?php echo e($errors->first('name')); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <label for=""><?php echo e(__('Rule')); ?></label>
                                                    <div class="input-group mb-25">
                                                        <span class="input-group-text"><?php echo e(__('From')); ?></span>
                                                        <input type="number" name="from" step="any"
                                                            value="0" class="form-control">
                                                        <span class="input-group-text"><?php echo e(__('To')); ?></span>
                                                        <input type="number" name="to" step="0"
                                                            value="0" class="form-control">
                                                        <span class="input-group-text"><?php echo e(getBadgeButtonName(RANKING_LEVEL_EARNING)); ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <label for=""><?php echo e(__('Description')); ?></label>
                                                    <div class="input-group mb-25">
                                                        <textarea name="description" placeholder="<?php echo e(__('Description')); ?>" class="form-control" rows="5"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="input__group mb-25">
                                                        <label for="badge_image"><?php echo e(__('Badge Image')); ?></label>
                                                        <div class="">
                                                            <div class="upload-img-box">
                                                                <img src="">
                                                                <input type="file" name="badge_image" id="badge_image"
                                                                    accept="image/*" onchange="previewFile(this)">
                                                                <div class="upload-img-box-icon">
                                                                    <i class="fa fa-camera"></i>
                                                                    <p class="m-0"><?php echo e(__('Badge Image')); ?></p>
                                                                </div>
                                                            </div>
                                                            <?php if($errors->has('badge_image')): ?>
                                                                <span class="text-danger"><i
                                                                        class="fas fa-exclamation-triangle"></i>
                                                                    <?php echo e($errors->first('badge_image')); ?></span>
                                                            <?php endif; ?>
                                                            <p class="mb-0 mt-1"><?php echo e(__('Accepted')); ?>: PNG, JPG <br>
                                                                <?php echo e(__('Accepted Size')); ?>: 30 x 30 (100KB)</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-purple"><?php echo e(__('Save')); ?></button>
                                        </div>
                                    </div>
                                    <div class="row ">
                                        <div class="col-md-12 mt-5">
                                            <div class="customers__table">
                                                <table id="" class="row-border data-table-filter table-style">
                                                    <thead>
                                                        <tr>
                                                            <th><?php echo e(__('Image')); ?></th>
                                                            <th><?php echo e(__('Name')); ?></th>
                                                            <th><?php echo e(__('From')); ?></th>
                                                            <th><?php echo e(__('To')); ?></th>
                                                            <th><?php echo e(__('Descripiton')); ?></th>
                                                            <th><?php echo e(__('Action')); ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $__currentLoopData = $levels->where('type', RANKING_LEVEL_EARNING); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <tr class="removable-item">
                                                                <td>
                                                                    <div class="admin-dashboard-blog-list-img">
                                                                        <img style="width:30px" src="<?php echo e(getImageFile($level->image_path)); ?>">
                                                                    </div>
                                                                </td>
                                                                <td><?php echo e($level->name); ?></td>
                                                                <td>
                                                                    <?php echo e($level->from); ?>

                                                                </td>
                                                                <td><?php echo e($level->to); ?></td>
                                                                <td><?php echo e($level->description); ?></td>
                                                                <td>
                                                                    <div class="action__buttons">
                                                                        <a href="<?php echo e(route('ranking.edit', $level->uuid)); ?>"
                                                                            class=" btn-action mr-1 edit"
                                                                            data-toggle="tooltip" title="Edit">
                                                                            <img style="width:30px" src="<?php echo e(asset('admin/images/icons/edit-2.svg')); ?>"
                                                                                alt="edit">
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            data-url="<?php echo e(route('ranking.delete', [$level->uuid])); ?>"
                                                                            class="btn-action delete" title="Delete">
                                                                            <img style="width:30px" src="<?php echo e(asset('admin/images/icons/trash-2.svg')); ?>"
                                                                                alt="trash">
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </tbody>
                                                </table>
                                                <div class="mt-3">
                                                    <?php echo e($levels->links()); ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="pills-courses_count" role="tabpanel"
                                aria-labelledby="pills-courses_count-tab">
                                <form action="<?php echo e(route('ranking.store')); ?>" method="post"
                                    enctype="multipart/form-data">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="type" value="3">
                                    <div class="row mb-5">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="input__group mb-25">
                                                        <label for="name"><?php echo e(__('Name')); ?></label>
                                                        <input type="text" name="name" id="name"
                                                            placeholder="<?php echo e(__('Type name')); ?>"
                                                            value="<?php echo e(old('name')); ?>" required>
                                                        <?php if($errors->has('name')): ?>
                                                            <span class="text-danger"><i
                                                                    class="fas fa-exclamation-triangle"></i>
                                                                <?php echo e($errors->first('name')); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <label for=""><?php echo e(__('Rule')); ?></label>
                                                    <div class="input-group mb-25">
                                                        <span class="input-group-text"><?php echo e(__('From')); ?></span>
                                                        <input type="number" name="from" step="any"
                                                            value="0" class="form-control">
                                                        <span class="input-group-text"><?php echo e(__('To')); ?></span>
                                                        <input type="number" name="to" step="0"
                                                            value="0" class="form-control">
                                                        <span class="input-group-text"><?php echo e(getBadgeButtonName(RANKING_LEVEL_COURSES_COUNT)); ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <label for=""><?php echo e(__('Description')); ?></label>
                                                    <div class="input-group mb-25">
                                                        <textarea name="description" placeholder="<?php echo e(__('Description')); ?>" class="form-control" rows="5"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="input__group mb-25">
                                                        <label for="badge_image"><?php echo e(__('Badge Image')); ?></label>
                                                        <div class="">
                                                            <div class="upload-img-box">
                                                                <img src="">
                                                                <input type="file" name="badge_image" id="badge_image"
                                                                    accept="image/*" onchange="previewFile(this)">
                                                                <div class="upload-img-box-icon">
                                                                    <i class="fa fa-camera"></i>
                                                                    <p class="m-0"><?php echo e(__('Badge Image')); ?></p>
                                                                </div>
                                                            </div>
                                                            <?php if($errors->has('badge_image')): ?>
                                                                <span class="text-danger"><i
                                                                        class="fas fa-exclamation-triangle"></i>
                                                                    <?php echo e($errors->first('badge_image')); ?></span>
                                                            <?php endif; ?>
                                                            <p class="mb-0 mt-1"><?php echo e(__('Accepted')); ?>: PNG, JPG <br>
                                                                <?php echo e(__('Accepted Size')); ?>: 30 x 30 (100KB)</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-purple"><?php echo e(__('Save')); ?></button>
                                        </div>
                                    </div>
                                    <div class="row ">
                                        <div class="col-md-12 mt-5">
                                            <div class="customers__table">
                                                <table id="" class="row-border data-table-filter table-style">
                                                    <thead>
                                                        <tr>
                                                            <th><?php echo e(__('Image')); ?></th>
                                                            <th><?php echo e(__('Name')); ?></th>
                                                            <th><?php echo e(__('From')); ?></th>
                                                            <th><?php echo e(__('To')); ?></th>
                                                            <th><?php echo e(__('Descripiton')); ?></th>
                                                            <th><?php echo e(__('Action')); ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $__currentLoopData = $levels->where('type', RANKING_LEVEL_COURSES_COUNT); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <tr class="removable-item">
                                                                <td>
                                                                    <div class="admin-dashboard-blog-list-img">
                                                                        <img
                                                                            style="width:30px" src="<?php echo e(getImageFile($level->image_path)); ?>">
                                                                    </div>
                                                                </td>
                                                                <td><?php echo e($level->name); ?></td>
                                                                <td>
                                                                    <?php echo e(number_format($level->from)); ?>

                                                                </td>
                                                                <td><?php echo e(number_format($level->to)); ?></td>
                                                                <td><?php echo e($level->description); ?></td>
                                                                <td>
                                                                    <div class="action__buttons">
                                                                        <a href="<?php echo e(route('ranking.edit', $level->uuid)); ?>"
                                                                            class=" btn-action mr-1 edit"
                                                                            data-toggle="tooltip" title="Edit">
                                                                            <img style="width:30px" src="<?php echo e(asset('admin/images/icons/edit-2.svg')); ?>"
                                                                                alt="edit">
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            data-url="<?php echo e(route('ranking.delete', [$level->uuid])); ?>"
                                                                            class="btn-action delete" title="Delete">
                                                                            <img style="width:30px" src="<?php echo e(asset('admin/images/icons/trash-2.svg')); ?>"
                                                                                alt="trash">
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </tbody>
                                                </table>
                                                <div class="mt-3">
                                                    <?php echo e($levels->links()); ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="pills-students_count" role="tabpanel"
                                aria-labelledby="pills-students_count-tab">
                                <form action="<?php echo e(route('ranking.store')); ?>" method="post"
                                    enctype="multipart/form-data">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="type" value="4">
                                    <div class="row mb-5">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="input__group mb-25">
                                                        <label for="name"><?php echo e(__('Name')); ?></label>
                                                        <input type="text" name="name" id="name"
                                                            placeholder="<?php echo e(__('Type name')); ?>"
                                                            value="<?php echo e(old('name')); ?>" required>
                                                        <?php if($errors->has('name')): ?>
                                                            <span class="text-danger"><i
                                                                    class="fas fa-exclamation-triangle"></i>
                                                                <?php echo e($errors->first('name')); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <label for=""><?php echo e(__('Rule')); ?></label>
                                                    <div class="input-group mb-25">
                                                        <span class="input-group-text"><?php echo e(__('From')); ?></span>
                                                        <input type="number" name="from" step="any"
                                                            value="0" class="form-control">
                                                        <span class="input-group-text"><?php echo e(__('To')); ?></span>
                                                        <input type="number" name="to" step="0"
                                                            value="0" class="form-control">
                                                        <span class="input-group-text"><?php echo e(getBadgeButtonName(RANKING_LEVEL_STUDENTS_COUNT)); ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <label for=""><?php echo e(__('Description')); ?></label>
                                                    <div class="input-group mb-25">
                                                        <textarea name="description" placeholder="<?php echo e(__('Description')); ?>" class="form-control" rows="5"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="input__group mb-25">
                                                        <label for="badge_image"><?php echo e(__('Badge Image')); ?></label>
                                                        <div class="">
                                                            <div class="upload-img-box">
                                                                <img src="">
                                                                <input type="file" name="badge_image" id="badge_image"
                                                                    accept="image/*" onchange="previewFile(this)">
                                                                <div class="upload-img-box-icon">
                                                                    <i class="fa fa-camera"></i>
                                                                    <p class="m-0"><?php echo e(__('Badge Image')); ?></p>
                                                                </div>
                                                            </div>
                                                            <?php if($errors->has('badge_image')): ?>
                                                                <span class="text-danger"><i
                                                                        class="fas fa-exclamation-triangle"></i>
                                                                    <?php echo e($errors->first('badge_image')); ?></span>
                                                            <?php endif; ?>
                                                            <p class="mb-0 mt-1"><?php echo e(__('Accepted')); ?>: PNG, JPG <br>
                                                                <?php echo e(__('Accepted Size')); ?>: 30 x 30 (100KB)</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-purple"><?php echo e(__('Save')); ?></button>
                                        </div>
                                    </div>
                                    <div class="row ">
                                        <div class="col-md-12 mt-5">
                                            <div class="customers__table">
                                                <table id="" class="row-border data-table-filter table-style">
                                                    <thead>
                                                        <tr>
                                                            <th><?php echo e(__('Image')); ?></th>
                                                            <th><?php echo e(__('Name')); ?></th>
                                                            <th><?php echo e(__('From')); ?></th>
                                                            <th><?php echo e(__('To')); ?></th>
                                                            <th><?php echo e(__('Descripiton')); ?></th>
                                                            <th><?php echo e(__('Action')); ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $__currentLoopData = $levels->where('type', RANKING_LEVEL_STUDENTS_COUNT); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <tr class="removable-item">
                                                                <td>
                                                                    <div class="admin-dashboard-blog-list-img">
                                                                        <img
                                                                            style="width:30px" src="<?php echo e(getImageFile($level->image_path)); ?>">
                                                                    </div>
                                                                </td>
                                                                <td><?php echo e($level->name); ?></td>
                                                                <td>
                                                                    <?php echo e(number_format($level->from)); ?>

                                                                </td>
                                                                <td><?php echo e(number_format($level->to)); ?></td>
                                                                <td><?php echo e($level->description); ?></td>
                                                                <td>
                                                                    <div class="action__buttons">
                                                                        <a href="<?php echo e(route('ranking.edit', $level->uuid)); ?>"
                                                                            class=" btn-action mr-1 edit"
                                                                            data-toggle="tooltip" title="Edit">
                                                                            <img style="width:30px" src="<?php echo e(asset('admin/images/icons/edit-2.svg')); ?>"
                                                                                alt="edit">
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            data-url="<?php echo e(route('ranking.delete', [$level->uuid])); ?>"
                                                                            class="btn-action delete" title="Delete">
                                                                            <img style="width:30px" src="<?php echo e(asset('admin/images/icons/trash-2.svg')); ?>"
                                                                                alt="trash">
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </tbody>
                                                </table>
                                                <div class="mt-3">
                                                    <?php echo e($levels->links()); ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="pills-courses_sale_count" role="tabpanel"
                                aria-labelledby="pills-courses_sale_count-tab">
                                <form action="<?php echo e(route('ranking.store')); ?>" method="post"
                                    enctype="multipart/form-data">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="type" value="5">
                                    <div class="row mb-5">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="input__group mb-25">
                                                        <label for="name"><?php echo e(__('Name')); ?></label>
                                                        <input type="text" name="name" id="name"
                                                            placeholder="<?php echo e(__('Type name')); ?>"
                                                            value="<?php echo e(old('name')); ?>" required>
                                                        <?php if($errors->has('name')): ?>
                                                            <span class="text-danger"><i
                                                                    class="fas fa-exclamation-triangle"></i>
                                                                <?php echo e($errors->first('name')); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <label for=""><?php echo e(__('Rule')); ?></label>
                                                    <div class="input-group mb-25">
                                                        <span class="input-group-text"><?php echo e(__('From')); ?></span>
                                                        <input type="number" name="from" step="any"
                                                            value="0" class="form-control">
                                                        <span class="input-group-text"><?php echo e(__('To')); ?></span>
                                                        <input type="number" name="to" step="0"
                                                            value="0" class="form-control">
                                                        <span class="input-group-text"><?php echo e(getBadgeButtonName(RANKING_LEVEL_COURSES_SALE_COUNT)); ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <label for=""><?php echo e(__('Description')); ?></label>
                                                    <div class="input-group mb-25">
                                                        <textarea name="description" placeholder="<?php echo e(__('Description')); ?>" class="form-control" rows="5"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="input__group mb-25">
                                                        <label for="badge_image"><?php echo e(__('Badge Image')); ?></label>
                                                        <div class="">
                                                            <div class="upload-img-box">
                                                                <img src="">
                                                                <input type="file" name="badge_image" id="badge_image"
                                                                    accept="image/*" onchange="previewFile(this)">
                                                                <div class="upload-img-box-icon">
                                                                    <i class="fa fa-camera"></i>
                                                                    <p class="m-0"><?php echo e(__('Badge Image')); ?></p>
                                                                </div>
                                                            </div>
                                                            <?php if($errors->has('badge_image')): ?>
                                                                <span class="text-danger"><i
                                                                        class="fas fa-exclamation-triangle"></i>
                                                                    <?php echo e($errors->first('badge_image')); ?></span>
                                                            <?php endif; ?>
                                                            <p class="mb-0 mt-1"><?php echo e(__('Accepted')); ?>: PNG, JPG <br>
                                                                <?php echo e(__('Accepted Size')); ?>: 30 x 30 (100KB)</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-purple"><?php echo e(__('Save')); ?></button>
                                        </div>
                                    </div>
                                    <div class="row ">
                                        <div class="col-md-12 mt-5">
                                            <div class="customers__table">
                                                <table id="" class="row-border data-table-filter table-style">
                                                    <thead>
                                                        <tr>
                                                            <th><?php echo e(__('Image')); ?></th>
                                                            <th><?php echo e(__('Name')); ?></th>
                                                            <th><?php echo e(__('From')); ?></th>
                                                            <th><?php echo e(__('To')); ?></th>
                                                            <th><?php echo e(__('Descripiton')); ?></th>
                                                            <th><?php echo e(__('Action')); ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $__currentLoopData = $levels->where('type', RANKING_LEVEL_COURSES_SALE_COUNT); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <tr class="removable-item">
                                                                <td>
                                                                    <div class="admin-dashboard-blog-list-img">
                                                                        <img
                                                                            style="width:30px" src="<?php echo e(getImageFile($level->image_path)); ?>">
                                                                    </div>
                                                                </td>
                                                                <td><?php echo e($level->name); ?></td>
                                                                <td>
                                                                    <?php echo e(number_format($level->from)); ?>

                                                                </td>
                                                                <td><?php echo e(number_format($level->to)); ?></td>
                                                                <td><?php echo e($level->description); ?></td>
                                                                <td>
                                                                    <div class="action__buttons">
                                                                        <a href="<?php echo e(route('ranking.edit', $level->uuid)); ?>"
                                                                            class=" btn-action mr-1 edit"
                                                                            data-toggle="tooltip" title="Edit">
                                                                            <img style="width:30px" src="<?php echo e(asset('admin/images/icons/edit-2.svg')); ?>"
                                                                                alt="edit">
                                                                        </a>
                                                                        <a href="javascript:void(0);"
                                                                            data-url="<?php echo e(route('ranking.delete', [$level->uuid])); ?>"
                                                                            class="btn-action delete" title="Delete">
                                                                            <img style="width:30px" src="<?php echo e(asset('admin/images/icons/trash-2.svg')); ?>"
                                                                                alt="trash">
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </tbody>
                                                </table>
                                                <div class="mt-3">
                                                    <?php echo e($levels->links()); ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
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
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/custom/image-preview.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script style="width:30px" src="<?php echo e(asset('admin/js/custom/image-preview.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/ranking/index.blade.php ENDPATH**/ ?>
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
                    <div class="email-inbox__area bg-style admin-about-us-client">
                        <div class="item-top mb-30"><h2><?php echo e(__(@$title)); ?></h2></div>
                        <form action="<?php echo e(route('settings.about.client.update')); ?>" method="post" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <div id="add_repeater" class="mb-3">
                                <div data-repeater-list="client_details" class="">
                                    <?php if($clients->count() > 0): ?>
                                        <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div data-repeater-item="" class="form-group row">
                                                <input type="hidden" name="id" value="<?php echo e(@$client['id']); ?>"/>
                                                <div class="custom-form-group mb-3 col-md-12 col-lg-3 col-xl-3 col-xxl-2">
                                                    <label for="image_<?php echo e(@$client['id']); ?>" class=" text-lg-right text-black"> <?php echo e(__('Client Logo')); ?> </label>
                                                    <div class="upload-img-box">
                                                        <?php if($client->logo): ?>
                                                            <img src="<?php echo e(getImageFile($client->image_path)); ?>">
                                                        <?php else: ?>
                                                            <img src="">
                                                        <?php endif; ?>
                                                        <input type="file" name="logo" id="image_<?php echo e(@$client['id']); ?>" accept="image/*" onchange="preview12041DimensionFile(this)">
                                                        <div class="upload-img-box-icon">
                                                            <i class="fa fa-camera"></i>
                                                        </div>
                                                    </div>
                                                    <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> PNG <br> <span class="text-black"><?php echo e(__('Accepted Size')); ?>:</span> 120 x 41</p>
                                                </div>
                                                <div class="custom-form-group mb-3 col-md-12 col-lg-8 col-xl-8 col-xxl-9">
                                                    <label for="name_<?php echo e(@$client['id']); ?>" class="text-lg-right text-black"> <?php echo e(__('Client Name')); ?> </label>
                                                    <div class="">
                                                        <input type="text" name="name" id="name_<?php echo e(@$client['id']); ?>" value="<?php echo e($client->name); ?>" class="form-control" placeholder="<?php echo e(__('Type name')); ?>" required>
                                                    </div>
                                                </div>

                                                <div class="col-lg-1 mb-3 removeClass">
                                                    <label class="text-lg-right text-black opacity-0"><?php echo e(__('Remove')); ?></label>
                                                    <a href="javascript:;" data-repeater-delete=""
                                                       class="btn btn-icon-remove btn-danger">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                </div>

                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <div data-repeater-item="" class="form-group row">
                                            <div class="custom-form-group mb-3 col-md-12 col-lg-3 col-xl-3 col-xxl-2">
                                                <label for="image" class=" text-lg-right text-black"> <?php echo e(__('Client Logo')); ?> </label>
                                                <div class="upload-img-box">
                                                    <img src="">
                                                    <input type="file" name="logo" id="image" accept="image/*"  onchange="preview12041DimensionFile(this)">
                                                    <div class="upload-img-box-icon">
                                                        <i class="fa fa-camera"></i>
                                                        <p class="m-0"><?php echo e(__('Image')); ?></p>
                                                    </div>
                                                </div>
                                                <p><span class="text-black"><?php echo e(__('Accepted Files')); ?>:</span> <?php echo e(__('PNG')); ?> <br> <span class="text-black"><?php echo e(__('Accepted Size')); ?>:</span> 120 x 41</p>
                                            </div>
                                            <div class="custom-form-group mb-3 col-md-12 col-lg-8 col-xl-8 col-xxl-9">
                                                <label for="name" class="text-lg-right text-black"> <?php echo e(__('Client Name')); ?></label>
                                                <div class="">
                                                    <input type="text" name="name" id="name" value="" class="form-control" placeholder="<?php echo e(__('Type name')); ?>" required>
                                                </div>
                                            </div>

                                            <div class="col-lg-1 mb-3 removeClass">
                                                <label class="text-lg-right text-black opacity-0"><?php echo e(__('Remove')); ?></label>
                                                <a href="javascript:;" data-repeater-delete=""
                                                   class="btn btn-icon-remove btn-danger">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            </div>

                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-lg-2">
                                    <a id="add" href="javascript:;" data-repeater-create=""
                                       class="btn btn-blue">
                                        <i class="fas fa-plus"></i> <?php echo e(__('Add')); ?>

                                    </a>
                                </div>

                            </div>

                            <div class="row justify-content-end">
                                <div class="col-md-2 text-right">
                                    <?php echo $__env->make('layouts.element.form.update-button', [], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </div>
                            </div>

                        </form>
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
    <script src="<?php echo e(asset('admin/js/custom/image-preview.js')); ?>"></script>
    <script src="<?php echo e(asset('common/js/jquery.repeater.min.js')); ?>"></script>
    <script src="<?php echo e(asset('common/js/add-repeater.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/about/client.blade.php ENDPATH**/ ?>
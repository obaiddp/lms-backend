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
                    <div class="email-inbox__area  bg-style">
                        <div class="item-top mb-30"><h2><?php echo e(__(@$title)); ?></h2></div>
                        <form action="<?php echo e(route('settings.support-ticket.question.update')); ?>" method="post">
                            <?php echo csrf_field(); ?>
                            <div id="add_repeater" class="mb-3">
                                <div data-repeater-list="question_answers" class="">
                                    <?php if($supportTickets->count() > 0): ?>
                                        <?php $__currentLoopData = $supportTickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div data-repeater-item="" class="form-group row ">
                                                <input type="hidden" name="id" value="<?php echo e(@$question['id']); ?>"/>
                                                <div class="custom-form-group mb-3 col-lg-5">
                                                    <label for="question_<?php echo e(@$question['id']); ?>" class="text-lg-right text-black"> <?php echo e(__('Question')); ?> </label>
                                                    <div class="">
                                                        <input type="text" name="question" id="question_<?php echo e(@$question['id']); ?>" value="<?php echo e($question->question); ?>"
                                                               class="form-control" placeholder="<?php echo e(__('Type question')); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="custom-form-group mb-3 col-lg-6">
                                                    <label for="answer_<?php echo e(@$question['id']); ?>" class="text-lg-right text-black"> <?php echo e(__('Answer')); ?> </label>
                                                    <div class="">
                                                        <textarea name="answer" id="answer_<?php echo e(@$question['id']); ?>" rows="5"
                                                                  class="form-control" required><?php echo e($question->answer); ?></textarea>
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
                                            <div class="custom-form-group mb-3 col-lg-5">
                                                <label for="question" class="text-lg-right text-black"><?php echo e(__('Question')); ?></label>
                                                <input type="text" name="question" id="question" value="" class="form-control" placeholder="<?php echo e(__('Type question')); ?>" required>
                                            </div>
                                            <div class="custom-form-group mb-3 col-lg-6">
                                                <label for="answer" class="text-lg-right text-black"><?php echo e(__('Answer')); ?></label>
                                                <textarea name="answer" id="answer" class="form-control" rows="5" required></textarea>
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/support_ticket/question.blade.php ENDPATH**/ ?>
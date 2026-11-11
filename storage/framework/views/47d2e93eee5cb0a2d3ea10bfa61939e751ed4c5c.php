<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Application Setting')); ?></h2>
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
                    <div class="email-inbox__area bg-style">
                        <div class="item-top mb-30"><h2><?php echo e(__(@$title)); ?></h2></div>
                        <div class="bg-dark-primary-soft-varient p-4 border-1">
                            <h2><?php echo e(__('SaaS Mode')); ?>: </h2>
                            <p><?php echo e(__('If SaaS mode is enable then all instructor and organization have to use any of the SaaS package.')); ?></p>
                        </div>
                        <br>
                        <form action="<?php echo e(route('settings.saas_mode.change')); ?>" method="post" class="form-horizontal">
                            <?php echo csrf_field(); ?>

                            <div class="form-group text-black row mb-3">
                                <label class="col-lg-3"><?php echo e(__('SaaS')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <div class="col-lg-9">
                                        <select name="saas_mode" required class="form-control saas_mode">
                                            <option value="0" <?php if(get_option('saas_mode') != 1): ?> selected <?php endif; ?>><?php echo e(__('No')); ?></option>
                                            <option value="1" <?php if(get_option('saas_mode') == 1): ?> selected <?php endif; ?>><?php echo e(__('Yes')); ?></option>
                                        </select>
                                        <?php if($errors->has('saas_mode')): ?>
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('saas_mode')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label class="col-lg-3"><?php echo e(__('Default SAAS for Instructor')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <div class="col-lg-9">
                                        <select name="default_saas_for_ins" class="form-control saas_mode">
                                            <?php if(is_null($saas_ins->where('id', get_option('default_saas_for_ins'))->first())): ?>
                                            <option value="" selected><?php echo e(__("Select default saas package")); ?></option>
                                            <?php endif; ?>
                                            <?php $__currentLoopData = $saas_ins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($s->id); ?>" <?php if(get_option('default_saas_for_ins') == $s->id): ?> selected <?php endif; ?>><?php echo e(__($s->title)); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label class="col-lg-3"><?php echo e(__('Default SAAS for Organization')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <div class="col-lg-9">
                                        <select name="default_saas_for_org" class="form-control saas_mode">
                                            <?php if(is_null($saas_org->where('id', get_option('default_saas_for_org'))->first())): ?>
                                            <option value="" selected><?php echo e(__("Select default saas package")); ?></option>
                                            <?php endif; ?>
                                            <?php $__currentLoopData = $saas_org; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($s->id); ?>" <?php if(get_option('default_saas_for_org') == $s->id): ?> selected <?php endif; ?>><?php echo e(__($s->title)); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label class="col-lg-3"><?php echo e(__('Default SAAS Type Instructor')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <div class="col-lg-9">
                                        <select name="saas_ins_default_package_type" class="form-control saas_mode">
                                            <option value="monthly" <?php if(get_option('saas_ins_default_package_type') == 'monthly'): ?> selected <?php endif; ?>><?php echo e(__('Monthly')); ?></option>
                                            <option value="yearly" <?php if(get_option('saas_ins_default_package_type') == 'yearly'): ?> selected <?php endif; ?>><?php echo e(__('Yearly')); ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group text-black row mb-3">
                                <label class="col-lg-3"><?php echo e(__('Default SAAS Type Organization')); ?> <span class="text-danger">*</span></label>
                                <div class="col-lg-9">
                                    <div class="col-lg-9">
                                        <select name="saas_org_default_package_type" class="form-control saas_mode">
                                            <option value="monthly" <?php if(get_option('saas_org_default_package_type') == 'monthly'): ?> selected <?php endif; ?>><?php echo e(__('Monthly')); ?></option>
                                            <option value="yearly" <?php if(get_option('saas_org_default_package_type') == 'yearly'): ?> selected <?php endif; ?>><?php echo e(__('Yearly')); ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-20 row text-end">
                                <div class="col">
                                    <button type="submit" class="btn btn-blue float-right"><?php echo e(__('Update')); ?></button>
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/saas.blade.php ENDPATH**/ ?>
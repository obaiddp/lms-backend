<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Home Settings')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                   <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item"><a><?php echo e(__('Application Setting')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__(@$title)); ?></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-4">
                    <?php echo $__env->make('admin.application_settings.home-sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
                <div class="col-lg-9 col-md-8">
                    <div class="customers__area bg-style mb-30">
                        <div class="item-title d-flex justify-content-between">
                            <h2><?php echo e(__('Section Settings')); ?></h2>
                        </div>
                        <div class="customers__table">
                            <table id="customers-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('SL')); ?></th>
                                    <th><?php echo e(__('Section Name')); ?></th>
                                    <th><?php echo e(__('Status')); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr class="removable-item">
                                        <td>1</td>
                                        <td><?php echo e(__('Special Feature Area')); ?></td>
                                        <td>
                                            <span id="hidden_attribute_name" style="display: none">special_feature_area</span>
                                            <select name="status" class="status label-inline font-weight-bolder mb-1 badge badge-info">
                                                <option value="1" <?php if($home->special_feature_area == 1): ?> selected <?php endif; ?>><?php echo e(__('Active')); ?></option>
                                                <option value="0" <?php if($home->special_feature_area != 1): ?> selected <?php endif; ?>><?php echo e(__('Disable')); ?></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr class="removable-item">
                                        <td>2</td>
                                        <td><?php echo e(__('Courses Area')); ?></td>
                                        <td>
                                            <span id="hidden_attribute_name" style="display: none">courses_area</span>
                                            <select name="status" class="status label-inline font-weight-bolder mb-1 badge badge-info">
                                                <option value="1" <?php if($home->courses_area == 1): ?> selected <?php endif; ?>><?php echo e(__('Active')); ?></option>
                                                <option value="0" <?php if($home->courses_area != 1): ?> selected <?php endif; ?>><?php echo e(__('Disable')); ?></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr class="removable-item">
                                        <td>3</td>
                                        <td><?php echo e(__('Category Courses Area')); ?></td>
                                        <td>
                                            <span id="hidden_attribute_name" style="display: none">category_courses_area</span>
                                            <select name="status" class="status label-inline font-weight-bolder mb-1 badge badge-info">
                                                <option value="1" <?php if($home->category_courses_area == 1): ?> selected <?php endif; ?>><?php echo e(__('Active')); ?></option>
                                                <option value="0" <?php if($home->category_courses_area != 1): ?> selected <?php endif; ?>><?php echo e(__('Disable')); ?></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <?php if(isAddonInstalled('LMSZAIPRODUCT')): ?>
                                    <tr class="removable-item">
                                        <td>3</td>
                                        <td><?php echo e(__('Product Area')); ?></td>
                                        <td>
                                            <span id="hidden_attribute_name" style="display: none">product_area</span>
                                            <select name="status" class="status label-inline font-weight-bolder mb-1 badge badge-info">
                                                <option value="1" <?php if($home->product_area == 1): ?> selected <?php endif; ?>><?php echo e(__('Active')); ?></option>
                                                <option value="0" <?php if($home->product_area != 1): ?> selected <?php endif; ?>><?php echo e(__('Disable')); ?></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr class="removable-item">
                                        <td>4</td>
                                        <td><?php echo e(__('Upcoming Courses Area')); ?></td>
                                        <td>
                                            <span id="hidden_attribute_name" style="display: none">upcoming_courses_area</span>
                                            <select name="status" class="status label-inline font-weight-bolder mb-1 badge badge-info">
                                                <option value="1" <?php if($home->upcoming_courses_area == 1): ?> selected <?php endif; ?>><?php echo e(__('Active')); ?></option>
                                                <option value="0" <?php if($home->upcoming_courses_area != 1): ?> selected <?php endif; ?>><?php echo e(__('Disable')); ?></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr class="removable-item">
                                        <td>5</td>
                                        <td><?php echo e(__('Bundle Area')); ?></td>
                                        <td>
                                            <span id="hidden_attribute_name" style="display: none">bundle_area</span>
                                            <select name="status" class="status label-inline font-weight-bolder mb-1 badge badge-info">
                                                <option value="1" <?php if($home->bundle_area == 1): ?> selected <?php endif; ?>><?php echo e(__('Active')); ?></option>
                                                <option value="0" <?php if($home->bundle_area != 1): ?> selected <?php endif; ?>><?php echo e(__('Disable')); ?></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr class="removable-item">
                                        <td>6</td>
                                        <td><?php echo e(__('Top Category Area')); ?></td>
                                        <td>
                                            <span id="hidden_attribute_name" style="display: none">top_category_area</span>
                                            <select name="status" class="status label-inline font-weight-bolder mb-1 badge badge-info">
                                                <option value="1" <?php if($home->top_category_area == 1): ?> selected <?php endif; ?>><?php echo e(__('Active')); ?></option>
                                                <option value="0" <?php if($home->top_category_area != 1): ?> selected <?php endif; ?>><?php echo e(__('Disable')); ?></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr class="removable-item">
                                        <td>7</td>
                                        <td><?php echo e(__('Consultation Area')); ?></td>
                                        <td>
                                            <span id="hidden_attribute_name" style="display: none">consultation_area</span>
                                            <select name="status" class="status label-inline font-weight-bolder mb-1 badge badge-info">
                                                <option value="1" <?php if($home->consultation_area == 1): ?> selected <?php endif; ?>><?php echo e(__('Active')); ?></option>
                                                <option value="0" <?php if($home->consultation_area != 1): ?> selected <?php endif; ?>><?php echo e(__('Disable')); ?></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr class="removable-item">
                                        <td>8</td>
                                        <td><?php echo e(__('Instructor Area')); ?></td>
                                        <td>
                                            <span id="hidden_attribute_name" style="display: none">instructor_area</span>
                                            <select name="status" class="status label-inline font-weight-bolder mb-1 badge badge-info">
                                                <option value="1" <?php if($home->instructor_area == 1): ?> selected <?php endif; ?>><?php echo e(__('Active')); ?></option>
                                                <option value="0" <?php if($home->instructor_area != 1): ?> selected <?php endif; ?>><?php echo e(__('Disable')); ?></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr class="removable-item">
                                        <td>9</td>
                                        <td><?php echo e(__('Video Area')); ?></td>
                                        <td>
                                            <span id="hidden_attribute_name" style="display: none">video_area</span>
                                            <select name="status" class="status label-inline font-weight-bolder mb-1 badge badge-info">
                                                <option value="1" <?php if($home->video_area == 1): ?> selected <?php endif; ?>><?php echo e(__('Active')); ?></option>
                                                <option value="0" <?php if($home->video_area != 1): ?> selected <?php endif; ?>><?php echo e(__('Disable')); ?></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr class="removable-item">
                                        <td>10</td>
                                        <td><?php echo e(__('Customer Says Area')); ?></td>
                                        <td>
                                            <span id="hidden_attribute_name" style="display: none">customer_says_area</span>
                                            <select name="status" class="status label-inline font-weight-bolder mb-1 badge badge-info">
                                                <option value="1" <?php if($home->customer_says_area == 1): ?> selected <?php endif; ?>><?php echo e(__('Active')); ?></option>
                                                <option value="0" <?php if($home->customer_says_area != 1): ?> selected <?php endif; ?>><?php echo e(__('Disable')); ?></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr class="removable-item">
                                        <td>11</td>
                                        <td><?php echo e(__('Achievement Area')); ?></td>
                                        <td>
                                            <span id="hidden_attribute_name" style="display: none">achievement_area</span>
                                            <select name="status" class="status label-inline font-weight-bolder mb-1 badge badge-info">
                                                <option value="1" <?php if($home->achievement_area == 1): ?> selected <?php endif; ?>><?php echo e(__('Active')); ?></option>
                                                <option value="0" <?php if($home->achievement_area != 1): ?> selected <?php endif; ?>><?php echo e(__('Disable')); ?></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr class="removable-item">
                                        <td>12</td>
                                        <td><?php echo e(__('FAQ Area')); ?></td>
                                        <td>
                                            <span id="hidden_attribute_name" style="display: none">faq_area</span>
                                            <select name="status" class="status label-inline font-weight-bolder mb-1 badge badge-info">
                                                <option value="1" <?php if($home->faq_area == 1): ?> selected <?php endif; ?>><?php echo e(__('Active')); ?></option>
                                                <option value="0" <?php if($home->faq_area != 1): ?> selected <?php endif; ?>><?php echo e(__('Disable')); ?></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr class="removable-item">
                                        <td>13</td>
                                        <td><?php echo e(__('Instructor Support Area')); ?></td>
                                        <td>
                                            <span id="hidden_attribute_name" style="display: none">instructor_support_area</span>
                                            <select name="status" class="status label-inline font-weight-bolder mb-1 badge badge-info">
                                                <option value="1" <?php if($home->instructor_support_area == 1): ?> selected <?php endif; ?>><?php echo e(__('Active')); ?></option>
                                                <option value="0" <?php if($home->instructor_support_area != 1): ?> selected <?php endif; ?>><?php echo e(__('Disable')); ?></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr class="removable-item">
                                        <td>14</td>
                                        <td><?php echo e(__('Subscription list area')); ?></td>
                                        <td>
                                            <span id="hidden_attribute_name" style="display: none">subscription_show</span>
                                            <select name="status" class="status label-inline font-weight-bolder mb-1 badge badge-info">
                                                <option value="1" <?php if($home->subscription_show == 1): ?> selected <?php endif; ?>><?php echo e(__('Active')); ?></option>
                                                <option value="0" <?php if($home->subscription_show != 1): ?> selected <?php endif; ?>><?php echo e(__('Disable')); ?></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr class="removable-item">
                                        <td>15</td>
                                        <td><?php echo e(__('SaaS List area')); ?></td>
                                        <td>
                                            <span id="hidden_attribute_name" style="display: none">saas_show</span>
                                            <select name="status" class="status label-inline font-weight-bolder mb-1 badge badge-info">
                                                <option value="1" <?php if($home->saas_show == 1): ?> selected <?php endif; ?>><?php echo e(__('Active')); ?></option>
                                                <option value="0" <?php if($home->saas_show != 1): ?> selected <?php endif; ?>><?php echo e(__('Disable')); ?></option>
                                            </select>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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

    <script>
        'use strict'
        $(".status").change(function () {
            var attribute_name = $(this).closest('tr').find('#hidden_attribute_name').html();
            var status = $(this).closest('tr').find('.status option:selected').val();
            console.log(attribute_name, status)
            Swal.fire({
                title: "<?php echo e(__('Are you sure to change status?')); ?>",
                text: "<?php echo e(__('You won`t be able to revert this!')); ?>",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "<?php echo e(__('Yes, Change it!')); ?>",
                cancelButtonText: "<?php echo e(__('No, cancel!')); ?>",
                reverseButtons: true
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        type: "POST",
                        url: "<?php echo e(route('settings.sectionSettingsStatusChange')); ?>",
                        data: {"attribute_name": attribute_name, "status": status,  "_token": "<?php echo e(csrf_token()); ?>",},
                        datatype: "json",
                        success: function (data) {
                            toastr.success('', "<?php echo e(__('Section status has been changed')); ?>");
                        },
                        error: function () {
                            alert("Error!");
                        },
                    });
                } else if (result.dismiss === "cancel") {
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/home/section-settings.blade.php ENDPATH**/ ?>
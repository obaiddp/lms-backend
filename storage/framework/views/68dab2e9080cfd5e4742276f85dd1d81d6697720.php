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
                    <div class="email-inbox__area bg-style">
                        <div class="item-top mb-30"><h2><?php echo e(__(@$title)); ?></h2></div>
                        <form action="<?php echo e(route('settings.general_setting.cms.update')); ?>" method="post" class="form-horizontal">
                            <?php echo csrf_field(); ?>

                            <div class="row">
                                <div class="col-lg-3">
                                    <label><?php echo e(__('Design')); ?> <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-lg-9 mb-15">
                                    <input type="radio" id="default" name="app_color_design_type" value="1"
                                           <?php echo e((empty(get_option('app_color_design_type')) || get_option('app_color_design_type')) ? 'checked' : ''); ?> required>
                                    <label for="default"><?php echo e(__('Default')); ?></label><br>
                                    <input type="radio" id="custom" name="app_color_design_type" value="2" <?php echo e(get_option('app_color_design_type') == 2 ? 'checked' : ''); ?>>
                                    <label for="custom"><?php echo e(__('Custom')); ?></label><br>
                                </div>
                            </div>
                            <div class="customDiv">
                                <div class="row">
                                    <div class="col-lg-3"><label><?php echo e(__('Theme Color')); ?> <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-9 mb-15">
                                         <span class="color-picker">
                                            <label for="colorPicker1" class="mb-0">
                                                <input type="color" name="app_theme_color"
                                                       value="<?php echo e(empty(get_option('app_theme_color')) ? '#5e3fd7' : get_option('app_theme_color')); ?>" id="colorPicker1">
                                            </label>
                                        </span>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3">
                                        <label><?php echo e(__('Navbar Background Color')); ?> <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-9 mb-15">
                                         <span class="color-picker">
                                            <label for="colorPicker2" class="mb-0">
                                                <input type="color" name="app_navbar_background_color"
                                                       value="<?php echo e(empty(get_option('app_navbar_background_color')) ? '#030060' : get_option('app_navbar_background_color')); ?>"
                                                       id="colorPicker2">
                                            </label>
                                        </span>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3">
                                        <label><?php echo e(__('Body Font Color')); ?> <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-9 mb-15">
                                         <span class="color-picker">
                                            <label for="colorPicker3" class="mb-0">
                                                <input type="color" name="app_body_font_color"
                                                       value="<?php echo e(empty(get_option('app_body_font_color')) ? '#52526C' : get_option('app_body_font_color')); ?>"
                                                       id="colorPicker3">
                                            </label>
                                        </span>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3">
                                        <label><?php echo e(__('Heading Color')); ?> <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-9 mb-15">
                                         <span class="color-picker">
                                            <label for="colorPicker4" class="mb-0">
                                                <input type="color" name="app_heading_color"
                                                       value="<?php echo e(empty(get_option('app_heading_color')) ? '#040453' : get_option('app_heading_color')); ?>" id="colorPicker4">
                                            </label>
                                        </span>
                                    </div>
                                </div>

                                <div class="row <?php echo e(get_option('theme', THEME_DEFAULT) == THEME_DEFAULT ? '' : 'd-none'); ?>">
                                    <div class="col-lg-3">
                                        <label><?php echo e(__('Gradiant Banner Color')); ?> <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-9 mb-15">
                                        <span class="color-picker d-flex flex-wrap">
                                            <label for="colorPicker8" class="mb-0 me-3">
                                                <input class="color1" type="color" name="app_gradiant_banner_color1" value="<?php echo e(get_option('app_gradiant_banner_color1')); ?>"
                                                       id="colorPicker8">
                                            </label>
                                            <label for="colorPicker9" class="mb-0 me-3">
                                                <input class="color2" type="color" name="app_gradiant_banner_color2" value="<?php echo e(get_option('app_gradiant_banner_color2')); ?>"
                                                       id="colorPicker9">
                                            </label>
                                        </span>

                                        <div id="gradient" class="p-5">
                                            <input class="app_gradiant_banner_color" type="hidden" name="app_gradiant_banner_color"
                                                   value="<?php echo e(get_option('app_gradiant_banner_color')); ?>">
                                            <h2 class="text-white"><?php echo e(__('Current CSS Background')); ?></h2>
                                            <h3 id="textContent" class="text-white"></h3>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3">
                                        <?php if(get_option('theme', THEME_DEFAULT) == THEME_DEFAULT): ?>
                                        <label><?php echo e(__('Gradiant Footer Color')); ?> <span class="text-danger">*</span></label>
                                        <?php else: ?>
                                        <label><?php echo e(__('Gradiant section Color')); ?> <span class="text-danger">*</span></label>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-lg-9 mb-15">
                                        <span class="color-picker d-flex flex-wrap">
                                            <label for="colorPicker12" class="mb-0 me-3">
                                                <input class="color5" type="color" name="app_gradiant_footer_color1" value="<?php echo e(get_option('app_gradiant_footer_color1')); ?>"
                                                       id="colorPicker12">
                                            </label>
                                            <label for="colorPicker13" class="mb-0 me-3">
                                                <input class="color6" type="color" name="app_gradiant_footer_color2" value="<?php echo e(get_option('app_gradiant_footer_color2')); ?>"
                                                       id="colorPicker13">
                                            </label>
                                        </span>

                                        <div id="gradiant3" class="p-5">
                                            <input class="app_gradiant_footer_color" type="hidden" name="app_gradiant_footer_color"
                                                   value="<?php echo e(get_option('app_gradiant_footer_color')); ?>">
                                            <h2 class="text-white"><?php echo e(__('Current CSS Background')); ?></h2>
                                            <h3 id="textContent3" class="text-white"></h3>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3">
                                        <?php if(get_option('theme', THEME_DEFAULT) == THEME_DEFAULT): ?>
                                        <label><?php echo e(__('Gradiant Overlay Background Color Opacity')); ?> <span class="text-danger">*</span></label>
                                        <?php else: ?>
                                        <label><?php echo e(__('Gradiant Section Background Color Opacity')); ?> <span class="text-danger">*</span></label>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-lg-9 mb-15">
                                        <select name="app_gradiant_overlay_background_color_opacity" class="form-control">
                                            <option value="0" <?php echo e(get_option('app_gradiant_overlay_background_color_opacity') == 0 ? 'selected' : null); ?>>0</option>
                                            <option value="0.1" <?php echo e(get_option('app_gradiant_overlay_background_color_opacity') == 0.1 ? 'selected' : null); ?>>0.1</option>
                                            <option value="0.2" <?php echo e(get_option('app_gradiant_overlay_background_color_opacity') == 0.2 ? 'selected' : null); ?>>0.2</option>
                                            <option value="0.3" <?php echo e(get_option('app_gradiant_overlay_background_color_opacity') == 0.3 ? 'selected' : null); ?>>0.3</option>
                                            <option value="0.4" <?php echo e(get_option('app_gradiant_overlay_background_color_opacity') == 0.4 ? 'selected' : null); ?>>0.4</option>
                                            <option value="0.5" <?php echo e(get_option('app_gradiant_overlay_background_color_opacity') == 0.5 ? 'selected' : null); ?>>0.5</option>
                                            <option value="0.6" <?php echo e(get_option('app_gradiant_overlay_background_color_opacity') == 0.6 ? 'selected' : null); ?>>0.6</option>
                                            <option value="0.7" <?php echo e(get_option('app_gradiant_overlay_background_color_opacity') == 0.7 ? 'selected' : null); ?>>0.7</option>
                                            <option value="0.8" <?php echo e(get_option('app_gradiant_overlay_background_color_opacity') == 0.8 ? 'selected' : null); ?>>0.8</option>
                                            <option value="0.9" <?php echo e(get_option('app_gradiant_overlay_background_color_opacity') == 0.9 ? 'selected' : null); ?>>0.9</option>
                                            <option value="1" <?php echo e(get_option('app_gradiant_overlay_background_color_opacity') == 1 ? 'selected' : null); ?>>1</option>
                                        </select>
                                    </div>
                                </div>

                            </div>



                            <div class="row">
                                <div class="col-12">
                                    <div class="input__group general-settings-btn">
                                        <button type="submit" class="btn btn-blue float-right"><?php echo e(__('Update')); ?></button>
                                    </div>
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
    <link rel="stylesheet" href="<?php echo e(asset('frontend/assets/css/for-certificate.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>

    <script src="<?php echo e(asset('frontend/assets/js/color.js')); ?>"></script>

    <script>
        "use strict"
        $(function () {
            var css = document.getElementById("textContent");
            var color1 = document.querySelector(".color1");
            var color2 = document.querySelector(".color2");
            var div = document.getElementById("gradient");

            var css3= document.getElementById("textContent3");
            var color5 = document.querySelector(".color5");
            var color6 = document.querySelector(".color6");
            var div3 = document.getElementById("gradiant3");

            setGradient()
            setGradient3()

            function setGradient() {
                var textContent = "linear-gradient(to right, " + color1.value + ", " + color2.value + ")"
                div.style.background = textContent;
                css.textContent = div.style.background + ";"
                $('.app_gradiant_banner_color').val(textContent);
            }

            function setGradient3() {
                var textContent3 = "linear-gradient(180deg, " + color5.value + ", " + color6.value + ")"
                div3.style.background = textContent3;
                css3.textContent3 = div3.style.background + ";"
                $('.app_gradiant_footer_color').val(textContent3);
            }

            color1.addEventListener("input", setGradient);
            color2.addEventListener("input", setGradient);

            color5.addEventListener("input", setGradient3);
            color6.addEventListener("input", setGradient3);
        })

        $(function () {
            var app_color_design_type = "<?php echo e(empty(get_option('app_color_design_type')) ? 1 : get_option('app_color_design_type')); ?>";
            appDesignType(app_color_design_type)
        })
        $("input[name='app_color_design_type']").click(function () {
            var app_design_type = $("input[name='app_color_design_type']:checked").val();
            appDesignType(app_design_type)
        });

        function appDesignType(app_color_design_type) {
            if (app_color_design_type == 1) {
                $('.customDiv').addClass('d-none')
            } else {
                $('.customDiv').removeClass('d-none')
            }
        }

    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/application_settings/general/color-settings.blade.php ENDPATH**/ ?>
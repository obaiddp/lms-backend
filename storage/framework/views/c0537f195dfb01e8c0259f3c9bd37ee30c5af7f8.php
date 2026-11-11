<?php $__env->startSection('content'); ?>
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2><?php echo e(__('Add Student Payment')); ?></h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Add Student Payment')); ?></li>
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
                            <h2><?php echo e(__('Add Student Payment')); ?></h2>
                        </div>
                        <form action="<?php echo e(route('student_payment.store_payment')); ?>" method="post" class="form-horizontal">
                            <?php echo csrf_field(); ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label class="label-text-title color-heading font-medium font-14 mb-3">
                                            <?php echo e(__('Student')); ?> <span class="text-danger">*</span>
                                        </label>
                                        <select name="student_id" id="student_id" class="form-select" required>
                                            <option value=""><?php echo e(__('Select Student')); ?></option>
                                            <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($student->id); ?>" <?php echo e(old('student_id')==$student->id ? 'selected' : ''); ?>><?php echo e($student->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <?php if($errors->has('student_id')): ?>
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('student_id')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            
                                <div class="col-md-12">
                                    <div class="input__group mb-25">
                                        <label class="label-text-title color-heading font-medium font-14 mb-3">
                                            <?php echo e(__('Course')); ?> <span class="text-danger">*</span>
                                        </label>
                                        <select name="course_id" id="course_id" class="form-select" required>
                                            <option value=""><?php echo e(__('Select Course')); ?></option>
                                            <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($course->id); ?>" <?php echo e(old('course_id')==$course->id ? 'selected' : ''); ?>><?php echo e($course->title); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <?php if($errors->has('course_id')): ?>
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo e($errors->first('course_id')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            
                                
                                <div class="col-md-3">
                                    <div class="input__group mb-25">
                                        <label><?php echo e(__('Course Price')); ?> (<?php echo e(get_currency_code()); ?>) <span class="text-danger">*</span></label>
                                        
                                        <input type="text" id="course_price_display" value="<?php echo e(old('price', number_format(0,2))); ?>" class="form-control" readonly>
                                        
                                        <input type="hidden" id="course_price" name="price" value="<?php echo e(old('price', 0)); ?>">
                                    </div>
                                </div>
                            

                                
                                <div class="col-md-2">
                                    <div class="input__group mb-25">
                                        <label><?php echo e(__('Number of Students')); ?></label>
                                        <input type="number" name="students_count" id="students_count" class="form-control" value="<?php echo e(old('students_count', 1)); ?>" min="1" step="1" required>
                                    </div>
                                </div>

                                
                                <div class="col-md-3">
                                    <div class="input__group mb-25">
                                        <label><?php echo e(__('Discount Course Price')); ?></label>
                                        <input type="number" step="0.01" min="0" name="discount_course_price" id="discount_course_price" class="form-control" value="<?php echo e(old('discount_course_price', 0)); ?>">
                                    </div>
                                </div>
                            
                                <div class="col-md-2">
                                    <div class="input__group mb-25">
                                        <label><?php echo e(__('Discount Type')); ?></label>
                                        <select name="discount_type_course_price" id="discount_type_course_price" class="form-control">
                                            <option value="0" <?php echo e(old('discount_type_course_price','0')=='0'? 'selected':''); ?>>Amount</option>
                                            <option value="1" <?php echo e(old('discount_type_course_price')=='1'? 'selected':''); ?>>Percentage (%)</option>
                                        </select>
                                    </div>
                                </div>
                            
                                <div class="col-md-3">
                                    <div class="input__group mb-25">
                                        <label><?php echo e(__('Net Course Price')); ?></label>
                                        <input type="text" class="form-control" id="net_course_price_display" readonly>
                                        <input type="hidden" name="net_course_price" id="net_course_price" value="0">
                                    </div>
                                </div>
                            
                                
                                <div id="admission_block" class="w-100">
                                    <div class="row">
                                        
                                    
                                        
                                        <div class="col-md-3">
                                            <div class="input__group mb-25">
                                                <label><?php echo e(__('Admission Price')); ?> (<?php echo e(get_currency_code()); ?>) <span class="text-danger">*</span></label>
                                            
                                                
                                                <input type="text" id="admission_fee_display" 
                                                       value="<?php echo e(number_format((float)$admissionFee,2)); ?>"
                                                       class="form-control" readonly>
                                            
                                                
                                                <input type="hidden" id="admission_fee" name="admission_fee" 
                                                       value="<?php echo e((float)$admissionFee); ?>">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="input__group mb-25">
                                                <label><?php echo e(__('Discount Admission Fee')); ?></label>
                                                <input type="number" step="0.01" min="0" name="discount_admission_fee" id="discount_admission_fee" class="form-control" value="<?php echo e(old('discount_admission_fee', 0)); ?>">
                                            </div>
                                        </div>
                                    
                                        <div class="col-md-2">
                                            <div class="input__group mb-25">
                                                <label><?php echo e(__('Discount Type')); ?></label>
                                                <select name="discount_type_admission_fee" id="discount_type_admission_fee" class="form-control">
                                                    <option value="0" <?php echo e(old('discount_type_admission_fee','0')=='0'? 'selected':''); ?>>Amount</option>
                                                    <option value="1" <?php echo e(old('discount_type_admission_fee')=='1'? 'selected':''); ?>>Percentage (%)</option>
                                                </select>
                                            </div>
                                        </div>
                                    
                                        <div class="col-md-3">
                                            <div class="input__group mb-25">
                                                <label><?php echo e(__('Net Admission Fee')); ?></label>
                                                <input type="text" class="form-control" id="net_admission_fee_display" readonly>
                                                <input type="hidden" name="net_admission_fee" id="net_admission_fee" value="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                                
                                <div class="col-md-4 offset-md-8">
                                    <div class="input__group mb-25">
                                        <label class="fw-bold"><?php echo e(__('Total Net Price')); ?></label>
                                        <input type="text" class="form-control fw-bold" id="net_price_display" readonly>
                                        <input type="hidden" name="net_price" id="net_price" value="0">
                                    </div>
                                </div>
                            
                                
                                <div class="col-md-6">
                                    <div class="input__group mb-25">
                                        <label class="label-text-title color-heading font-medium font-14 mb-3">
                                            <?php echo e(__('Month')); ?> <span class="text-danger">*</span>
                                        </label>
                                        <select name="month" id="month" class="form-select" required>
                                            <option value=""><?php echo e(__('Select Month')); ?></option>
                                            <option value='1'><?php echo e(__('January')); ?></option>
                                            <option value='2'><?php echo e(__('Febuary')); ?></option>
                                            <option value='3'><?php echo e(__('March')); ?></option>
                                            <option value='4'><?php echo e(__('April')); ?></option>
                                            <option value='5'><?php echo e(__('May')); ?></option>
                                            <option value='6'><?php echo e(__('June')); ?></option>
                                            <option value='7'><?php echo e(__('July')); ?></option>
                                            <option value='8'><?php echo e(__('August')); ?></option>
                                            <option value='9'><?php echo e(__('September')); ?></option>
                                            <option value='10'><?php echo e(__('October')); ?></option>
                                            <option value='11'><?php echo e(__('November')); ?></option>
                                            <option value='12'><?php echo e(__('December')); ?></option>
                                        </select>
                                    </div>
                                </div>
                            
                                <div class="col-md-6">
                                    <div class="input__group mb-25">
                                        <label class="label-text-title color-heading font-medium font-14 mb-3">
                                            <?php echo e(__('Year')); ?> <span class="text-danger">*</span>
                                        </label>
                                        <select name="year" id="year" class="form-select" required>
                                            <option value=""><?php echo e(__('Select Year')); ?></option>
                                            <?php for($year = \Carbon\Carbon::now()->format('Y'); $year < \Carbon\Carbon::now()->
                                            addYear(10)->format('Y');
                                            $year++): ?>
                                            <option value="<?php echo e($year); ?>"><?php echo e($year); ?></option>
                                             <?php endfor; ?>
                                            
                                        </select>
                                    </div>
                                </div>
                            
                                
                                <input type="hidden" id="admission_applicable" name="admission_applicable" value="1">
                            
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-12 text-right">
                                    <button class="btn btn-primary" type="submit"><?php echo e(__('Save')); ?></button>
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
    <script src="<?php echo e(asset('admin/js/custom/admin-profile.js')); ?>"></script>
    <script>
    $(document).ready(function () {
    // select2 for student
    $("#student_id").select2({ width: '100%', placeholder: "Select Student" });

    function parseFloatSafe(v){ return isNaN(parseFloat(v)) ? 0 : parseFloat(v); }
    function parseIntSafe(v){ return isNaN(parseInt(v)) ? 1 : parseInt(v); }

    // initial values from blade
    var admissionFeePerStudent = parseFloat($("#admission_fee").val() || 0);

    function recalc() {
        const studentsCount = parseIntSafe($("#students_count").val());
        const coursePricePerUnit = parseFloatSafe($("#course_price").val()); // per student
        // Updated (total) course price = per-unit * students_count
        const coursePriceTotal = coursePricePerUnit * studentsCount;

        const discountCourse = parseFloatSafe($("#discount_course_price").val());
        const discountTypeCourse = $("#discount_type_course_price").val();
        let discountAmountCourse = 0;
        if (discountTypeCourse === '0') {
            discountAmountCourse = discountCourse;
        } else {
            discountAmountCourse = (coursePriceTotal * discountCourse) / 100;
        }
        if (discountAmountCourse > coursePriceTotal) discountAmountCourse = coursePriceTotal;
        const netCourse = Math.max(coursePriceTotal - discountAmountCourse, 0);

        $("#course_price_display").val(coursePriceTotal.toFixed(2));
        // keep hidden course_price as per-unit price (so backend can recalc and know per-unit)
        // but we will also update a hidden field net_course_price etc.
        $("#net_course_price_display").val(netCourse.toFixed(2));
        $("#net_course_price").val(netCourse.toFixed(2));

        // Admission only if admission_applicable hidden field is 1
        const admissionApplicable = $("#admission_applicable").val() === '1';
        let netAdmission = 0;
        if (admissionApplicable) {
            const admissionPerUnit = parseFloatSafe($("#admission_fee").val()); // per student
            const admissionTotal = admissionPerUnit * studentsCount;

            // Show total (before discount)
            $("#admission_fee_display").val(admissionTotal.toFixed(2));

            const discountAdmission = parseFloatSafe($("#discount_admission_fee").val());
            const discountTypeAdmission = $("#discount_type_admission_fee").val();
            let discountAmountAdmission = 0;
            if (discountTypeAdmission === '0') {
                discountAmountAdmission = discountAdmission;
            } else {
                discountAmountAdmission = (admissionTotal * discountAdmission) / 100;
            }
            if (discountAmountAdmission > admissionTotal) discountAmountAdmission = admissionTotal;
            netAdmission = Math.max(admissionTotal - discountAmountAdmission, 0);
        } else {
            netAdmission = 0;
        }

        $("#net_admission_fee_display").val(netAdmission.toFixed(2));
        $("#net_admission_fee").val(netAdmission.toFixed(2));

    
        const totalNet = netCourse + netAdmission;
        $("#net_price_display").val(totalNet.toFixed(2));
        $("#net_price").val(totalNet.toFixed(2));
    }

    // bind recalculation events
    ['discount_course_price','discount_type_course_price','discount_admission_fee','discount_type_admission_fee','students_count']
        .forEach(function(id) {
            $("#"+id).on('input change', recalc);
        });

    // When course changes: get per-student course price via AJAX and then recalc
    $('#course_id').change(function (e) {
        e.preventDefault();
        var course_id = $(this).val();
        if(!course_id) {
            $("#course_price_display").val((0).toFixed(2));
            $("#course_price").val(0);
            recalc();
            return;
        }
        $.ajax({
            type: "POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: '<?php echo e(route('course.get_price')); ?>',
            data: {course_id:course_id},
            dataType: "JSON",
            success: function (response) {
                // response expected to be numeric per-student price
                var perUnitPrice = parseFloat(response) || 0;
                // keep hidden value as per-unit price (submitted as 'price')
                $("#course_price").val(perUnitPrice);
                // display will be perUnitPrice * students_count (recalc handles that)
                recalc();
            },
            error: function(){
                $("#course_price_display").val((0).toFixed(2));
                $("#course_price").val(0);
                recalc();
            }
        });

        // Also check if admission fee should be shown/hidden for this student-course combo
        checkAdmissionApplicability();
    });

    // When student selection changes, re-check applicability (if a course is selected)
    $('#student_id').change(function(){
        checkAdmissionApplicability();
    });

    function checkAdmissionApplicability() {
        var student_id = $('#student_id').val();
        var course_id = $('#course_id').val();
        if(!student_id || !course_id) {
            // default: show admission block and set applicable = 1
            $("#admission_block").show();
            $("#admission_applicable").val('1');
            recalc();
            return;
        }
        $.ajax({
            type: "POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: '<?php echo e(route("admin.student.check_enrollment")); ?>',
            data: { student_id: student_id, course_id: course_id },
            dataType: "JSON",
            success: function(res) {
                if (res.enrolled === true) {
                    $("#admission_block").hide();
                    $("#admission_applicable").val('0');
                } else {
                    $("#admission_block").show();
                    $("#admission_applicable").val('1');
                }
                recalc();
            },
            error: function(){
                $("#admission_block").show();
                $("#admission_applicable").val('1');
                recalc();
            }
        });
    }

    // initial recalc
    recalc();
    });
    </script>
<?php $__env->stopPush(); ?>





<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/obaid/lms/cef-lms-backend/resources/views/admin/student_payment/add.blade.php ENDPATH**/ ?>
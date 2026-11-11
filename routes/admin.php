<?php

use App\Http\Controllers\AddonUpdateController;
use App\Http\Controllers\Admin\AboutUsController;
use App\Http\Controllers\Admin\AffiliateController;
use App\Http\Controllers\Admin\BankController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\ContactUsController;
use App\Http\Controllers\Admin\ContactUsIssueController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\CourseLanguageController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\ZoomAccountController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\difficultyLevelController;
use App\Http\Controllers\Admin\EmailNotificationTemplateController;
use App\Http\Controllers\Admin\ForumCategoryController;
use App\Http\Controllers\Admin\HomeSettingController;
use App\Http\Controllers\Admin\InstructorController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\ResourcesController;
use App\Http\Controllers\Admin\ResourceCategoryController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\MonthlyFeeInvoiceController;
use App\Http\Controllers\Admin\PaymentReceiptEmailController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PayoutController;
use App\Http\Controllers\Admin\LicenseController;
use App\Http\Controllers\Admin\PolicyController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\RankingLevelController;
use App\Http\Controllers\Admin\ResourceController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ScheduleJobController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TimeSlotController;
use App\Http\Controllers\Admin\SpecialPromotionTagController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Admin\SupportTicketController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\SkillController;
use App\Http\Controllers\Admin\SaasController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\WalletRechargeController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\VersionUpdateController;




use App\Http\Controllers\Instructor\CourseController as CourseController2;
use App\Http\Controllers\Instructor\LessonController;
use App\Http\Controllers\Instructor\ScormController;
use Illuminate\Support\Facades\Route;


// Route::middleware(['web','auth'])->group(function () {
//     Route::get('/overdue', [OverdueController::class, 'index'])->name('overdue.index');
//     Route::post('/overdue/send-reminder/{id}', [OverdueController::class, 'sendReminder'])
//         ->name('overdue.sendReminder');
// });

Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard');

Route::get('test_function', [DashboardController::class, 'testFunction'])->name('admin.test_function');
Route::get('test_zoom_shift', [ScheduleJobController::class, 'check_and_activate_zoom_account'])->name('admin.test_zoom_shift');
Route::get('test_zoom_update', [ScheduleJobController::class, 'check_and_update_zoom_account_minutes'])->name('admin.test_zoom_update');


Route::get('getZoomAccountData', [DashboardController::class, 'getZoomAccountData'])->name('admin.get_zoom_account_data');

Route::group(['prefix' => 'payout', 'as' => 'payout.'], function () {
    Route::get('new-withdraw', [PayoutController::class, 'newWithdraw'])->name('new-withdraw');
    Route::get('complete-withdraw', [PayoutController::class, 'completeWithdraw'])->name('complete-withdraw');
    Route::get('rejected-withdraw', [PayoutController::class, 'rejectedWithdraw'])->name('rejected-withdraw');
    Route::post('change-withdraw-status', [PayoutController::class, 'changeWithdrawStatus'])->name('change-withdraw-status');
    Route::get('distribute-subscriptions', [PayoutController::class, 'distributeSubscription'])->name('distribute.subscriptions');
    Route::get('earning-management-calculation', [PayoutController::class, 'earningManagementCalculation'])->name('distribute.earning.management.calculation');
    Route::post('store-distribute-subscription-calculation', [PayoutController::class, 'sendMoneyStore'])->name('store.distribute.subscription.calculation');
});

// email template

Route::group(['prefix' => 'email-template', 'as' => 'email-template.'], function () {
    Route::get('/', [EmailTemplateController::class, 'index'])->name('index');
    Route::get('create', [EmailTemplateController::class, 'create'])->name('create');
    Route::post('store', [EmailTemplateController::class, 'store'])->name('store');
    Route::get('edit/{uuid}', [EmailTemplateController::class, 'edit'])->name('edit');
    Route::post('update/{uuid}', [EmailTemplateController::class, 'update'])->name('update');
    Route::get('delete/{uuid}', [EmailTemplateController::class, 'delete'])->name('delete');

    Route::get('send-email', [EmailTemplateController::class, 'sendEmail'])->name('send-email');
    Route::post('send-email-to-user', [EmailTemplateController::class, 'sendEmailToUser'])->name('send-email-to-user');
});

// email notification
Route::group(['prefix' => 'email-notification', 'as' => 'email-notification.'], function () {
    Route::get('/', [EmailNotificationTemplateController::class, 'index'])->name('index');
    Route::get('edit/{slug}', [EmailNotificationTemplateController::class, 'edit'])->name('edit');
    Route::get('view/{slug}', [EmailNotificationTemplateController::class, 'view'])->name('view');
    Route::post('update/{slug}', [EmailNotificationTemplateController::class, 'update'])->name('update');
    Route::post('send-test-mail/{slug}', [EmailNotificationTemplateController::class, 'sendTestMail'])->name('send-test-mail');
});

// Start:: user management
Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('create', [UserController::class, 'create'])->name('create');
    Route::post('store', [UserController::class, 'store'])->name('store');
    Route::get('edit/{id}', [UserController::class, 'edit'])->name('edit');
    Route::post('update/{id}', [UserController::class, 'update'])->name('update');
    Route::get('delete/{id}', [UserController::class, 'delete'])->name('delete');
});

Route::group(['prefix' => 'role', 'as' => 'role.'], function () {
    Route::get('/', [RoleController::class, 'index'])->name('index');
    Route::get('create', [RoleController::class, 'create'])->name('create');
    Route::post('store', [RoleController::class, 'store'])->name('store');
    Route::get('edit/{id}', [RoleController::class, 'edit'])->name('edit');
    Route::post('update/{id}', [RoleController::class, 'update'])->name('update');
    Route::get('delete/{id}', [RoleController::class, 'delete'])->name('delete');
});

// End:: user management

Route::group(['prefix' => 'promotions', 'as' => 'promotion.'], function () {
    Route::get('/', [PromotionController::class, 'index'])->name('index');
    Route::get('create', [PromotionController::class, 'create'])->name('create');
    Route::post('store', [PromotionController::class, 'store'])->name('store');
    Route::get('edit-promotion-course/{uuid}', [PromotionController::class, 'editPromotionCourse'])->name('editPromotionCourse');
    Route::get('show/{uuid}', [PromotionController::class, 'show'])->name('show');
    Route::get('edit/{uuid}', [PromotionController::class, 'edit'])->name('edit');
    Route::put('update/{uuid}', [PromotionController::class, 'update'])->name('update');
    Route::delete('delete/{uuid}', [PromotionController::class, 'delete'])->name('delete');
    Route::post('change-promotion-status', [PromotionController::class, 'changePromotionStatus'])->name('changePromotionStatus');

    Route::get('add-promotion-course-list', [PromotionController::class, 'addPromotionCourseList'])->name('addPromotionCourseList');
    Route::get('remove-promotion-course-list', [PromotionController::class, 'removePromotionCourseList'])->name('removePromotionCourseList');
});

// Route::group(['prefix' => 'resource'], function () {
//     Route::get('index/{course_uuid}', [ResourceController::class, 'index'])->name('admin.course.resource.index');
//     Route::get('create/{course_uuid}', [ResourceController::class, 'create'])->name('admin.course.resource.create');
//     Route::post('store/{course_uuid}', [ResourceController::class, 'store'])->name('admin.course.resource.store');
//     Route::get('delete/{uuid}/{course_uuid?}', [ResourceController::class, 'delete'])->name('admin.course.resource.delete');
// });

Route::get('course/assign_resources/{id?}', [CourseController::class, 'AssignResources'])->name('admin.course.assign_resources');
Route::post('course/store_assigned_resources', [CourseController::class, 'StoreAssignedResources'])->name('admin.course.store_assigned_resources');

Route::group(['prefix' => 'resource'], function () {
    // Route::get('getData', [ResourcesController::class, 'getData'])->name('admin.resource.getData');
    Route::get('category/view', [ResourceCategoryController::class, 'index'])->name('admin.resource.category.index');
    Route::get('category/add', [ResourceCategoryController::class, 'add'])->name('admin.resource.category.add');
    Route::post('category/store', [ResourceCategoryController::class, 'store'])->name('admin.resource.category.store');
    Route::get('category/edit/{id}', [ResourceCategoryController::class, 'edit'])->name('admin.resource.category.edit');
    Route::post('category/update/{id}', [ResourceCategoryController::class, 'update'])->name('admin.resource.category.update');
    Route::post('category/delete/{id}', [ResourceCategoryController::class, 'delete'])->name('admin.resource.category.delete');
    Route::post('get_resource_category_types', [ResourcesController::class, 'getCategoryTypes'])->name('admin.resource.get_category_types');
    Route::get('view', [ResourcesController::class, 'index'])->name('admin.resource.index');
    Route::get('add', [ResourcesController::class, 'add'])->name('admin.resource.add');
    Route::post('store', [ResourcesController::class, 'store'])->name('admin.resource.store');
    Route::get('edit/{id}', [ResourcesController::class, 'edit'])->name('admin.resource.edit');
    Route::get('viewfull/{file_ext}/{file_path}', [ResourcesController::class, 'viewFullResource'])->name('admin.resource.viewfull');
    Route::get('viewfullother/{resource_id}', [ResourcesController::class, 'viewFullResourceOther'])->name('admin.resource.viewfullother');
    Route::post('update/{id}', [ResourcesController::class, 'update'])->name('admin.resource.update');
    Route::post('delete/{id}', [ResourcesController::class, 'delete'])->name('admin.resource.delete');
});



Route::prefix('exam')->group(function () {
    Route::get('index', [ExamController::class, 'index'])->name('admin.exam.index');
    Route::get('create/{course_uuid}', [ExamController::class, 'create'])->name('admin.exam.create');
    Route::post('store/{course_uuid}', [ExamController::class, 'store'])->name('admin.exam.store');
    Route::get('edit/{uuid}', [ExamController::class, 'edit'])->name('admin.exam.edit');
    Route::post('update/{uuid}', [ExamController::class, 'update'])->name('admin.exam.update');
    Route::get('view/{uuid}', [ExamController::class, 'view'])->name('admin.exam.view');
    Route::get('delete/{uuid}', [ExamController::class, 'delete'])->name('admin.exam.delete');
    Route::get('status-change/{uuid}/{status}', [ExamController::class, 'statusChange'])->name('admin.exam.status-change');
    Route::get('edit-mcq/{question_uuid}', [ExamController::class, 'editMcq'])->name('admin.exam.edit-mcq');
    Route::get('question/{uuid}/{course_uuid}', [ExamController::class, 'question'])->name('admin.exam.question');
    Route::post('save-mcq-question/{uuid}', [ExamController::class, 'saveMcqQuestion'])->name('admin.exam.save-mcq-question');
    Route::post('bulk-upload-mcq/{uuid}', [ExamController::class, 'bulkUploadMcq'])->name('admin.exam.bulk-upload-mcq');
    Route::post('update-mcq-question/{question_uuid}', [ExamController::class, 'updateMcqQuestion'])->name('admin.exam.update-mcq-question');
    Route::post('save-true-false-question/{uuid}', [ExamController::class, 'saveTrueFalseQuestion'])->name('admin.exam.save-true-false-question');
    Route::post('bulk-upload-true-false/{uuid}', [ExamController::class, 'bulkUploadTrueFalse'])->name('admin.exam.bulk-upload-true-false');
    Route::get('edit-true-false/{question_uuid}', [ExamController::class, 'editTrueFalse'])->name('admin.exam.edit-true-false');
    Route::post('update-true-false-question/{question_uuid}', [ExamController::class, 'updateTrueFalseQuestion'])->name('admin.exam.update-true-false-question');
    Route::get('delete-question/{question_uuid}', [ExamController::class, 'deleteQuestion'])->name('admin.exam.delete-question');
});



Route::group(['prefix' => 'assignments', 'as' => 'admin.assignment.'], function () {
    Route::get('index/{course_uuid}', [AssignmentController::class, 'index'])->name('index');
    Route::get('create/{course_uuid}', [AssignmentController::class, 'create'])->name('create');
    Route::post('store/{course_uuid}', [AssignmentController::class, 'store'])->name('store');
    Route::get('edit/{course_uuid}/{uuid}', [AssignmentController::class, 'edit'])->name('edit');
    Route::post('update/{course_uuid}/{uuid}', [AssignmentController::class, 'update'])->name('update');
    Route::get('delete/{uuid}', [AssignmentController::class, 'delete'])->name('delete');

    Route::group(['prefix' => 'assessments', 'as' => 'assessment.'], function () {
        Route::get('index/{course_uuid}/{assignment_uuid}', [AssignmentController::class, 'assessmentIndex'])->name('index');
        Route::post('update/{assignment_submit_uuid}', [AssignmentController::class, 'assessmentSubmitMarkUpdate'])->name('update');
        Route::get('download', [AssignmentController::class, 'studentAssignmentDownload'])->name('download');
    });
});


Route::prefix('course')->group(function () {
    Route::get('/', [CourseController::class, 'index'])->name('admin.course.index');
    Route::get('view/{uuid}', [CourseController::class, 'view'])->name('admin.course.view');
    Route::get('approved', [CourseController::class, 'approved'])->name('admin.course.approved');
    Route::get('review-pending', [CourseController::class, 'reviewPending'])->name('admin.course.review_pending');
    Route::get('review-upcoming', [CourseController::class, 'reviewUpcoming'])->name('admin.course.review_upcoming');
    Route::get('hold', [CourseController::class, 'hold'])->name('admin.course.hold');
    Route::get('status-change/{uuid}/{status}', [CourseController::class, 'statusChange'])->name('admin.course.status-change');
    Route::post('feature-change', [CourseController::class, 'featureChange'])->name('admin.course.feature-change');
    Route::get('delete/{uuid}', [CourseController::class, 'delete'])->name('admin.course.delete');
    Route::get('enroll', [CourseController::class, 'courseEnroll'])->name('admin.course.enroll');
    Route::post('enroll', [CourseController::class, 'courseEnrollStore'])->name('admin.course.enroll.store');
   //maria
   Route::get('add-course-standard', [CourseController::class, 'create'])->name('admin.course.create_standard');
   Route::post('store-course-standard', [CourseController::class, 'store'])->name('admin.course.store_standard');
   Route::post('get_price', [CourseController::class, 'getCoursePrice'])->name('course.get_price');
   Route::get('edit-course-standard/{uuid}', [CourseController::class, 'edit'])->name('admin.course.edit_standard');
   Route::post('update-course-standard/{uuid}', [CourseController::class, 'update'])->name('admin.course.update_standard');
   
   Route::get('add-course', [CourseController2::class, 'create'])->name('admin.course.create');
    Route::post('store', [CourseController2::class, 'store'])->name('admin.course.store');
    Route::get('edit/{uuid}', [CourseController2::class, 'edit'])->name('admin.course.edit');
    Route::post('update-overview/{uuid}', [CourseController2::class, 'updateOverview'])->name('admin.course.update.overview');
    Route::post('update-category/{uuid}', [CourseController2::class, 'updateCategory'])->name('admin.course.update.category');
    Route::post('update-lecture/{lecture_uuid}/{course_uuid}/{lesson_uuid}', [LessonController::class, 'updateLecture'])->name('admin.update.lecture');
    Route::get('upload-finished/{uuid}', [CourseController2::class, 'uploadFinished'])->name('admin.course.upload-finished');
    Route::post('store-instructor/{course_uuid}', [CourseController2::class, 'storeInstructor'])->name('admin.course.store.instructor');
    
    Route::get('orders', [CourseController::class, 'courseOrders'])->name('admin.course.orders');
    Route::post('orders/status-change', [CourseController::class, 'statusChangeCourseOrder'])->name('admin.course.orders.status_change');

});
Route::prefix('lesson')->group(function () {
    Route::post('get_lesson_category_resources/{category_type}', [LessonController::class, 'getLessonCategoryResources'])->name('admin.get_lesson_category_resources');
    Route::post('store/{course_uuid}', [LessonController::class, 'store'])->name('admin.lesson.store');
    Route::post('update-lesson/{course_uuid}/{lesson_id}', [LessonController::class, 'updateLesson'])->name('admin.lesson.update');
    Route::get('upload-lecture/{course_uuid}/{lesson_uuid}', [LessonController::class, 'uploadLecture'])->name('admin.upload.lecture');
    Route::post('store-lecture/{course_uuid}/{lesson_uuid}', [LessonController::class, 'storeLecture'])->name('admin.store.lecture');
    Route::get('edit-lecture/{course_uuid}/{lesson_uuid}/{lecture_uuid}/{lecture_resouceid?}', [LessonController::class, 'editLecture'])->name('admin.edit.lecture');
    Route::get('delete-lecture/{course_uuid}/{lecture_uuid}', [LessonController::class, 'deleteLecture'])->name('admin.delete.lecture');
    Route::delete('delete-lesson/{lesson_id}', [LessonController::class, 'deleteLesson'])->name('admin.lesson.delete');


    // NEW ROUTES ADDED FOR QUIZ AND ASSIGNMENT SELECTION PROCESS IN LESSON TAB UPLOADING STEP
    Route::get('upload-quiz-lecture/{course_uuid}/{lesson_uuid}', [LessonController::class, 'uploadQuizLecture'])->name('admin.upload.quiz.lecture');
    Route::post('store-quiz-lecture/{course_uuid}/{lesson_uuid}', [LessonController::class, 'storeQuizLecture'])->name('admin.store.quiz.lecture');
    Route::get('edit-quiz-lecture/{course_uuid}/{lesson_uuid}/{lecture_uuid}/{lecture_quizid?}', [LessonController::class, 'editQuizLecture'])->name('admin.edit.quiz.lecture');
    Route::post('update-quiz-lecture/{lecture_uuid}/{course_uuid}/{lesson_uuid}', [LessonController::class, 'updateQuizLecture'])->name('admin.update.quiz.lecture');

    Route::get('upload-assignment-lecture/{course_uuid}/{lesson_uuid}', [LessonController::class, 'uploadAssignmentLecture'])->name('admin.upload.assignment.lecture');
    Route::post('store-assignment-lecture/{course_uuid}/{lesson_uuid}', [LessonController::class, 'storeAssignmentLecture'])->name('admin.store.assignment.lecture');
    Route::get('edit-assignment-lecture/{course_uuid}/{lesson_uuid}/{lecture_uuid}/{lecture_assignmentid?}', [LessonController::class, 'editAssignmentLecture'])->name('admin.edit.assignment.lecture');
    Route::post('update-assignment-lecture/{lecture_uuid}/{course_uuid}/{lesson_uuid}', [LessonController::class, 'updateAssignmentLecture'])->name('admin.update.assignment.lecture');
});


Route::prefix('scorm')->group(function () {
    Route::post('{course_uuid}', [ScormController::class, 'store'])->name('admin.scorm.store');
});
Route::get('course-upload-rules', [CourseController::class, 'courseUploadRuleIndex'])->name('course-rules.index');
Route::post('course-upload-rules/store', [CourseController::class, 'courseUploadRuleStore'])->name('course-rules.store');


Route::prefix('instructor')->group(function () {
    Route::get('/', [InstructorController::class, 'index'])->name('instructor.index');
    Route::get('remove_authorization/{user_id}', [InstructorController::class, 'removeGoogleAuthorization'])->name('instructor.remove_authorization');
    Route::get('view/{uuid}', [InstructorController::class, 'view'])->name('instructor.view');
    Route::get('edit/{uuid}', [InstructorController::class, 'edit'])->name('instructor.edit');
    Route::post('update/{uuid}', [InstructorController::class, 'update'])->name('instructor.update');
    Route::get('pending', [InstructorController::class, 'pending'])->name('instructor.pending');
    Route::get('approved', [InstructorController::class, 'approved'])->name('instructor.approved');
    Route::get('blocked', [InstructorController::class, 'blocked'])->name('instructor.blocked');
    Route::get('change-status/{uuid}/{status}', [InstructorController::class, 'changeStatus'])->name('instructor.status-change');
    Route::post('change-instructor-status', [InstructorController::class, 'changeInstructorStatus'])->name('admin.instructor.changeInstructorStatus');
    Route::post('change-auto-content-status', [InstructorController::class, 'changeAutoContentStatus'])->name('admin.instructor.changeAutoContentStatus');
    Route::get('delete/{uuid}', [InstructorController::class, 'delete'])->name('instructor.delete');

    Route::get('get-state-by-country/{country_id}', [InstructorController::class, 'getStateByCountry']);
    Route::get('get-city-by-state/{state_id}', [InstructorController::class, 'getCityByState']);

    Route::get('create', [InstructorController::class, 'create'])->name('instructor.create');
    Route::post('store', [InstructorController::class, 'store'])->name('instructor.store');
});



Route::prefix('coordinator')->group(function () {
    Route::get('/', [CoordinatorController::class, 'index'])->name('coordinator.index');
    Route::get('view/{uuid}', [CoordinatorController::class, 'view'])->name('coordinator.view');
    Route::get('edit/{uuid}', [CoordinatorController::class, 'edit'])->name('coordinator.edit');
    Route::post('update/{uuid}', [CoordinatorController::class, 'update'])->name('coordinator.update');
    Route::get('blocked', [CoordinatorController::class, 'blocked'])->name('coordinator.blocked');
    Route::get('change-status/{uuid}/{status}', [CoordinatorController::class, 'changeStatus'])->name('coordinator.status-change');
    Route::post('change-coordinator-status', [CoordinatorController::class, 'changeCoordinatorStatus'])->name('admin.coordinator.changeCoordinatorStatus');
    Route::post('change-auto-content-status', [CoordinatorController::class, 'changeAutoContentStatus'])->name('admin.coordinator.changeAutoContentStatus');
    Route::get('delete/{uuid}', [CoordinatorController::class, 'delete'])->name('coordinator.delete');

    Route::get('get-state-by-country/{country_id}', [CoordinatorController::class, 'getStateByCountry']);
    Route::get('get-city-by-state/{state_id}', [CoordinatorController::class, 'getCityByState']);

    Route::get('create', [CoordinatorController::class, 'create'])->name('coordinator.create');
    Route::post('store', [CoordinatorController::class, 'store'])->name('coordinator.store');
});












Route::prefix('time_slots')->group(function () {
    Route::get('/', [TimeSlotController::class, 'index'])->name('time_slots.index');
    Route::get('create', [TimeSlotController::class, 'create'])->name('time_slots.create');
    Route::post('store', [TimeSlotController::class, 'store'])->name('time_slots.store');
    Route::get('{slot}/edit', [TimeSlotController::class, 'edit'])->name('time_slots.edit');
    Route::post('{slot}/update', [TimeSlotController::class, 'update'])->name('time_slots.update');
    Route::post('{slot}/toggle_status', [TimeSlotController::class, 'toggleStatus'])->name('time_slots.toggle_status');
    Route::get('{slot}/destroy', [TimeSlotController::class, 'destroy'])->name('time_slots.destroy');
    // Route::delete('{slot}/destroy', [TimeSlotController::class, 'destroy'])->name('time_slots.destroy');
 
     // Bulk create routes
     Route::get('bulk-create', [TimeSlotController::class, 'bulkCreate'])->name('time_slots.bulk_create');
     Route::post('bulk-store', [TimeSlotController::class, 'bulkStore'])->name('time_slots.bulk_store');
 

    // Route::get('view/{uuid}', [CoordinatorController::class, 'view'])->name('coordinator.view');
    // Route::get('edit/{uuid}', [CoordinatorController::class, 'edit'])->name('coordinator.edit');
    // Route::post('update/{uuid}', [CoordinatorController::class, 'update'])->name('coordinator.update');
    // Route::get('blocked', [CoordinatorController::class, 'blocked'])->name('coordinator.blocked');
    // Route::get('change-status/{uuid}/{status}', [CoordinatorController::class, 'changeStatus'])->name('coordinator.status-change');
    // Route::post('change-coordinator-status', [CoordinatorController::class, 'changeCoordinatorStatus'])->name('admin.coordinator.changeCoordinatorStatus');
    // Route::post('change-auto-content-status', [CoordinatorController::class, 'changeAutoContentStatus'])->name('admin.coordinator.changeAutoContentStatus');
    // Route::get('delete/{uuid}', [CoordinatorController::class, 'delete'])->name('coordinator.delete');

    // Route::get('get-state-by-country/{country_id}', [CoordinatorController::class, 'getStateByCountry']);
    // Route::get('get-city-by-state/{state_id}', [CoordinatorController::class, 'getCityByState']);

    // Route::get('create', [CoordinatorController::class, 'create'])->name('coordinator.create');
    // Route::post('store', [CoordinatorController::class, 'store'])->name('coordinator.store');
});






Route::prefix('classes')->group(function () {
    Route::get('/', [ClassController::class, 'index'])->name('classes.index');
    Route::get('view_individual_classes', [ClassController::class, 'viewIndividualClasses'])->name('classes.view_individual_classes');
    Route::get('view_group_classes', [ClassController::class, 'viewGroupClasses'])->name('classes.view_group_classes');


    Route::get('view_individual_classes_history', [ClassController::class, 'viewIndividualClassesHistory'])->name('classes.view_individual_classes_history');
    Route::get('view_group_classes_history', [ClassController::class, 'viewGroupClassesHistory'])->name('classes.view_group_classes_history');




    
    Route::get('create_individual', [ClassController::class, 'createIndividual'])->name('classes.create_individual');
    Route::post('store_individual', [ClassController::class, 'storeIndividual'])->name('classes.store_individual');
    Route::get('create_group', [ClassController::class, 'createGroup'])->name('classes.create_group');
    Route::post('store_group', [ClassController::class, 'storeGroup'])->name('classes.store_group');
    Route::get('view_detail/{id}', [ClassController::class, 'viewDetail'])->name('classes.view_detail');
    Route::get('edit/{id}', [ClassController::class, 'edit'])->name('classes.edit');
    Route::post('update_individual/{id}', [ClassController::class, 'updateIndividual'])->name('classes.update_individual');
    Route::post('update_group/{id}', [ClassController::class, 'updateGroup'])->name('classes.update_group');
    Route::get('delete/{id}', [ClassController::class, 'delete'])->name('classes.delete');
    Route::post('get_tutor_courses', [ClassController::class, 'getTutorCourses'])->name('classes.get_tutor_courses');
    Route::post('get_category_subcategories', [ClassController::class, 'getCategorySubCategories'])->name('classes.get_category_subcategories');

    Route::get('create_free_class_individual/{id}', [ClassController::class, 'createFreeClassIndividual'])->name('classes.create_free_class_individual');
    Route::post('store_free_class_individual', [ClassController::class, 'storeFreeClassIndividual'])->name('classes.store_free_class_individual');
    Route::get('edit_free_class/{id}', [ClassController::class, 'editFreeClass'])->name('classes.edit_free_class');
    Route::post('update_free_class_individual/{id}', [ClassController::class, 'updateFreeClassIndividual'])->name('classes.update_free_class_individual');
    Route::get('view_free_class_individual/{student_id}/{trial_id}', [ClassController::class, 'viewFreeClassIndividual'])->name('classes.view_free_class_individual');

    // Route::get('blocked', [CoordinatorController::class, 'blocked'])->name('coordinator.blocked');
    // Route::get('change-status/{uuid}/{status}', [CoordinatorController::class, 'changeStatus'])->name('coordinator.status-change');
    // Route::post('change-coordinator-status', [CoordinatorController::class, 'changeCoordinatorStatus'])->name('admin.coordinator.changeCoordinatorStatus');
    // Route::post('change-auto-content-status', [CoordinatorController::class, 'changeAutoContentStatus'])->name('admin.coordinator.changeAutoContentStatus');


    // Route::get('get-state-by-country/{country_id}', [CoordinatorController::class, 'getStateByCountry']);
    // Route::get('get-city-by-state/{state_id}', [CoordinatorController::class, 'getCityByState']);

   
});



Route::prefix('organizations')->as('organizations.')->group(function () {
    Route::get('/', [OrganizationController::class, 'index'])->name('index');
    Route::get('view/{uuid}', [OrganizationController::class, 'view'])->name('view');
    Route::get('edit/{uuid}', [OrganizationController::class, 'edit'])->name('edit');
    Route::post('update/{uuid}', [OrganizationController::class, 'update'])->name('update');
    Route::get('pending', [OrganizationController::class, 'pending'])->name('pending');
    Route::get('approved', [OrganizationController::class, 'approved'])->name('approved');
    Route::get('blocked', [OrganizationController::class, 'blocked'])->name('blocked');
    Route::post('change-organization-status', [OrganizationController::class, 'changeOrganizationStatus'])->name('changeOrganizationStatus');
    Route::post('change-auto-content-status', [OrganizationController::class, 'changeAutoContentStatus'])->name('changeAutoContentStatus');
    Route::get('delete/{uuid}', [OrganizationController::class, 'delete'])->name('delete');
    Route::get('create', [OrganizationController::class, 'create'])->name('create');
    Route::post('store', [OrganizationController::class, 'store'])->name('store');
});

Route::prefix('admin/trial')->middleware(['auth', 'admin'])->group(function () {
    Route::post('/approve', [StudentController::class, 'approve'])->name('admin.trial.approve');
    Route::post('/resend', [StudentController::class, 'resendApprovalEmail'])->name('admin.trial.resend');

    Route::get('/download/{id}', [StudentController::class, 'downloadVoucher'])
    ->name('admin.trial.download.voucher');

    Route::get('/{id}/approve-form', [StudentController::class, 'showApproveForm'])->name('admin.trial.approve.form');
    Route::post('/{id}/approve-submit', [StudentController::class, 'submitApproveForm'])->name('admin.trial.approve.submit');
});


// Route::get('admin/trial/{id}/approve-form', [TrialController::class, 'showApproveForm'])->name('admin.trial.approve.form');
// Route::post('admin/trial/{id}/approve-submit', [TrialController::class, 'submitApproveForm'])->name('admin.trial.approve.submit');

Route::prefix('admin/cart')->middleware(['auth', 'admin'])->group(function () {
 Route::get('/download-invoice/{id}', [StudentController::class, 'downloadCartVoucher'])
    ->name('admin.cart.download.voucher');
});


Route::prefix('student')->group(function () {
    // web.php (or admin routes group)
    Route::post('/check-enrollment', [StudentController::class, 'checkStudentEnrollment'])
    ->name('admin.student.check_enrollment');
    Route::get('getData', [StudentController::class, 'getData'])->name('student.getData');
    Route::get('/', [StudentController::class, 'index'])->name('student.index');
    Route::get('/free_classes', [StudentController::class, 'freeClassList'])->name('student.free_classes');
    Route::get('pending', [StudentController::class, 'pending_list'])->name('student.pending_list');
    Route::get('create', [StudentController::class, 'create'])->name('student.create');
    Route::post('store', [StudentController::class, 'store'])->name('student.store');
    // Route::get('view/{uuid}', [StudentController::class, 'view'])->name('student.view');
    Route::get('view/{uuid}/{trial_id?}', [StudentController::class, 'view'])->name('student.view');
    Route::get('view2/{uuid}/{trial_id?}', [StudentController::class, 'view2'])->name('student.view2');
    Route::get('edit/{uuid}', [StudentController::class, 'edit'])->name('student.edit');
    Route::post('update/{uuid}', [StudentController::class, 'update'])->name('student.update');
    Route::delete('delete/{uuid}', [StudentController::class, 'delete'])->name('student.delete');
    Route::post('change-student-status', [StudentController::class, 'changeStudentStatus'])->name('admin.student.changeStudentStatus');
    Route::post('change-enrollment-status', [StudentController::class, 'changeEnrollmentStatus'])->name('admin.student.changeEnrollmentStatus');

    Route::post('generateInvoice', [StudentController::class, 'generateInvoice'])->name('admin.student.generateInvoice');

});

Route::prefix('monthly-fee-invoices')->group(function () {
    Route::get('/', [MonthlyFeeInvoiceController::class, 'index'])->name('admin.monthly-fee-invoices.index');
    Route::post('/preview', [MonthlyFeeInvoiceController::class, 'preview'])->name('admin.monthly-fee-invoices.preview');
    Route::post('/resend', [MonthlyFeeInvoiceController::class, 'resend'])->name('admin.monthly-fee-invoices.resend');
});

Route::prefix('payment-receipt-emails')->group(function () {
    Route::get('/', [PaymentReceiptEmailController::class, 'index'])->name('admin.payment-receipt-emails.index');
    Route::post('/preview', [PaymentReceiptEmailController::class, 'preview'])->name('admin.payment-receipt-emails.preview');
    Route::post('/resend', [PaymentReceiptEmailController::class, 'resend'])->name('admin.payment-receipt-emails.resend');
});


Route::prefix('license')->group(function () {
    // web.php (or admin routes group)
    Route::get('/', [LicenseController::class, 'index'])->name('admin.license.index');
    Route::get('create', [LicenseController::class, 'create'])->name('admin.license.create');
    Route::post('store', [LicenseController::class, 'store'])->name('admin.license.store');
    // Route::post('delete', [LicenseController::class, 'destroy'])->name('admin.license.delete');
    Route::delete('delete/{id}', [LicenseController::class, 'destroy'])->name('admin.license.delete');
    // Route::get('view/{uuid}', [StudentController::class, 'view'])->name('student.view');
     Route::get('show/{id}', [LicenseController::class, 'show'])->name('admin.license.show');
    Route::get('usage-history/{id}', [LicenseController::class, 'usageHistory'])->name('admin.license.usage-history');
    Route::get('edit/{id}', [LicenseController::class, 'edit'])->name('admin.license.edit');
    Route::post('update/{id}', [LicenseController::class, 'update'])->name('admin.license.update');

  
});




Route::prefix('student_payment')->group(function () {
    Route::get('/donation', [StudentController::class, 'donationPayments'])->name('student_payment.donation');
    Route::get('/completed_donation', [StudentController::class, 'completedDonations'])->name('student_payment.completed_donation');
    Route::get('/pending', [StudentController::class, 'pendingPayments'])->name('student_payment.pending');

    Route::get('/overdue', [StudentController::class, 'overDuePayments'])->name('student_payment.overdue');

    Route::get('/completed', [StudentController::class, 'completedPayments'])->name('student_payment.completed');
    Route::get('/change_status/{id}', [StudentController::class, 'changePaymentStatus'])->name('student_payment.change_status');
    Route::post('/update_status', [StudentController::class, 'updatePaymentStatus'])->name('student_payment.update_status');
   

    Route::get('/change_status_overdue/{id}', [StudentController::class, 'changePaymentStatusOverDue'])->name('student_payment.change_status_overdue');
    Route::post('/update_status_overdue', [StudentController::class, 'updatePaymentStatusOverDue'])->name('student_payment.update_status_overdue');



    Route::get('/change_status_donation/{id}', [StudentController::class, 'changePaymentStatusDonation'])->name('student_payment.change_status_donation');
    Route::post('/update_status_donation', [StudentController::class, 'updatePaymentStatusDonation'])->name('student_payment.update_status_donation');


    
    Route::get('/add_payment', [StudentController::class, 'addPayment'])->name('student_payment.add_payment');
    Route::get('/edit_payment/{id}', [StudentController::class, 'editPayment'])->name('student_payment.edit_payment');
    Route::post('/update_payment/{id}', [StudentController::class, 'updatePayment'])->name('student_payment.update_payment');

    Route::get('/edit_payment_2/{id}', [StudentController::class, 'editPayment2'])->name('student_payment.edit_payment_2');
    Route::post('/update_payment_2/{id}', [StudentController::class, 'updatePayment2'])->name('student_payment.update_payment_2');


      Route::get('/edit_payment_3/{id}', [StudentController::class, 'editPayment3'])->name('student_payment.edit_payment_3');
    Route::post('/update_payment_3/{id}', [StudentController::class, 'updatePayment3'])->name('student_payment.update_payment_3');


    Route::delete('/delete_payment/{id}', [StudentController::class, 'deletePayment'])->name('student_payment.delete_payment');
      Route::delete('/delete_payment_enrollment/{id}', [StudentController::class, 'deletePaymentEnrollment'])->name('student_payment.delete_payment_enrollment');
     Route::delete('/delete_donation_payment/{id}', [StudentController::class, 'deleteDonationPayment'])->name('student_payment.delete_donation_payment');
    Route::delete('/delete_completed_payment/{id}', [StudentController::class, 'deleteCompletedPayment'])->name('student_payment.delete_completed_payment');
    Route::post('/store-payment', [StudentController::class, 'storePayment'])->name('student_payment.store_payment');
});

Route::as('admin')->resource('saas', SaasController::class);
Route::post('change-saas-status', [SaasController::class, 'changeStatus'])->name('admin.saas.changeStatus');
Route::get('saas-purchase-list', [SaasController::class, 'purchaseList'])->name('admin.saas.purchase_list');
Route::get('saas-purchase-pending', [SaasController::class, 'pendingPurchaseList'])->name('admin.saas.purchase_pending_list');
Route::post('change-purchase-saas-status', [SaasController::class, 'changePurchaseStatus'])->name('admin.saas.purchase.changeStatus');

Route::as('admin')->resource('subscriptions', SubscriptionController::class);
Route::post('change-subscriptions-status', [SubscriptionController::class, 'changeStatus'])->name('admin.subscriptions.changeStatus');
Route::get('subscriptions-purchase-list', [SubscriptionController::class, 'purchaseList'])->name('admin.subscriptions.purchase_list');
Route::get('subscriptions-purchase-pending', [SubscriptionController::class, 'pendingPurchaseList'])->name('admin.subscriptions.purchase_pending_list');
Route::post('change-purchase-subscriptions-status', [SubscriptionController::class, 'changePurchaseStatus'])->name('admin.subscriptions.purchase.changeStatus');

Route::get('wallet-recharge-list', [WalletRechargeController::class, 'list'])->name('admin.wallet_recharge.list');
Route::get('wallet-recharge-pending', [WalletRechargeController::class, 'pendingList'])->name('admin.wallet_recharge.pending_list');
Route::post('change-wallet-recharge-status', [WalletRechargeController::class, 'changeStatus'])->name('admin.wallet_recharge.changeStatus');

Route::prefix('report')->group(function () {
    Route::get('course-revenue-report', [ReportController::class, 'revenueReportCoursesIndex'])->name('course-report.revenue-report');
    Route::get('bundle-revenue-report', [ReportController::class, 'revenueReportBundlesIndex'])->name('bundle-report.revenue-report');
    Route::get('consultation-revenue-report', [ReportController::class, 'revenueReportConsultationIndex'])->name('consultation-report.revenue-report');
    Route::get('order-report', [ReportController::class, 'orderReportIndex'])->name('report.order-report');
    Route::get('order-pending', [ReportController::class, 'orderReportPending'])->name('report.order-pending');
    Route::get('order-cancelled', [ReportController::class, 'orderReportCancelled'])->name('report.order-cancelled');
    Route::get('order-paid/{uuid}/{status}', [ReportController::class, 'orderReportPaidStatus'])->name('report.order-paid');
    Route::get('cancel-consultation-list', [ReportController::class, 'cancelConsultationList'])->name('report.cancel-consultation-list');
    Route::post('change-consultation-status', [ReportController::class, 'changeConsultationStatus'])->name('report.changeConsultationStatus');
    Route::get('student-fee-ledger', [ReportController::class, 'studentFeeLedgerIndex'])->name('report.student-fee-ledger');
    Route::get('student-fee-ledger/export', [ReportController::class, 'studentFeeLedgerExport'])->name('report.student-fee-ledger.export');
});

Route::prefix('category')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('category.index');
    Route::get('create', [CategoryController::class, 'create'])->name('category.create');
    Route::post('store', [CategoryController::class, 'store'])->name('category.store');
    Route::get('edit/{uuid}', [CategoryController::class, 'edit'])->name('category.edit');
    Route::post('update/{uuid}', [CategoryController::class, 'update'])->name('category.update');
    Route::get('delete/{uuid}', [CategoryController::class, 'delete'])->name('category.delete');
});

Route::prefix('subcategory')->group(function () {
    Route::get('/', [SubcategoryController::class, 'index'])->name('subcategory.index');
    Route::get('create', [SubcategoryController::class, 'create'])->name('subcategory.create');
    Route::post('store', [SubcategoryController::class, 'store'])->name('subcategory.store');
    Route::get('edit/{uuid}', [SubcategoryController::class, 'edit'])->name('subcategory.edit');
    Route::post('update/{uuid}', [SubcategoryController::class, 'update'])->name('subcategory.update');
    Route::get('delete/{uuid}', [SubcategoryController::class, 'delete'])->name('subcategory.delete');
});

Route::prefix('tag')->group(function () {
    Route::get('/', [TagController::class, 'index'])->name('tag.index');
    Route::get('create', [TagController::class, 'create'])->name('tag.create');
    Route::post('store', [TagController::class, 'store'])->name('tag.store');
    Route::get('edit/{uuid}', [TagController::class, 'edit'])->name('tag.edit');
    Route::patch('update/{uuid}', [TagController::class, 'update'])->name('tag.update');
    Route::get('delete/{uuid}', [TagController::class, 'delete'])->name('tag.delete');
});

Route::prefix('course-language')->group(function () {
    Route::get('/', [CourseLanguageController::class, 'index'])->name('course-language.index');
    Route::get('create', [CourseLanguageController::class, 'create'])->name('course-language.create');
    Route::post('store', [CourseLanguageController::class, 'store'])->name('course-language.store');
    Route::get('edit/{uuid}', [CourseLanguageController::class, 'edit'])->name('course-language.edit');
    Route::patch('update/{uuid}', [CourseLanguageController::class, 'update'])->name('course-language.update');
    Route::get('delete/{uuid}', [CourseLanguageController::class, 'delete'])->name('course-language.delete');
});

Route::prefix('difficulty-level')->group(function () {
    Route::get('/', [difficultyLevelController::class, 'index'])->name('difficulty-level.index');
    Route::get('create', [difficultyLevelController::class, 'create'])->name('difficulty-level.create');
    Route::post('store', [difficultyLevelController::class, 'store'])->name('difficulty-level.store');
    Route::get('edit/{uuid}', [difficultyLevelController::class, 'edit'])->name('difficulty-level.edit');
    Route::patch('update/{uuid}', [difficultyLevelController::class, 'update'])->name('difficulty-level.update');
    Route::get('delete/{uuid}', [difficultyLevelController::class, 'delete'])->name('difficulty-level.delete');
});

Route::group(['prefix' => 'special-promotional-tag', 'as' => 'special_promotional_tag.'], function () {
    Route::get('/', [SpecialPromotionTagController::class, 'index'])->name('index');
    Route::post('store', [SpecialPromotionTagController::class, 'store'])->name('store');
    Route::patch('update/{uuid}', [SpecialPromotionTagController::class, 'update'])->name('update');
    Route::delete('delete/{uuid}', [SpecialPromotionTagController::class, 'delete'])->name('delete');
    Route::get('edit-special-promotion-course/{uuid}', [SpecialPromotionTagController::class, 'editSpecialPromotionCourse'])->name('editSpecialPromotionCourse');

    Route::get('add-promotion-tag-course-list', [SpecialPromotionTagController::class, 'addPromotionCourseList'])->name('addPromotionTagCourseList');
    Route::get('remove-promotion-tag-course-list', [SpecialPromotionTagController::class, 'removePromotionCourseList'])->name('removePromotionTagCourseList');
});

Route::group(['prefix' => 'coupon', 'as' => 'coupon.'], function () {
    Route::get('/', [CouponController::class, 'index'])->name('index');
    Route::get('create', [CouponController::class, 'create'])->name('create');
    Route::post('store', [CouponController::class, 'store'])->name('store');
    Route::get('edit/{uuid}', [CouponController::class, 'edit'])->name('edit');
    Route::post('update/{uuid}', [CouponController::class, 'update'])->name('update');
    Route::get('delete/{uuid}', [CouponController::class, 'delete'])->name('delete');
});

Route::group(['prefix' => 'blog', 'as' => 'blog.'], function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('create', [BlogController::class, 'create'])->name('create');
    Route::post('store', [BlogController::class, 'store'])->name('store');
    Route::get('edit/{uuid}', [BlogController::class, 'edit'])->name('edit');
    Route::post('update/{uuid}', [BlogController::class, 'update'])->name('update');
    Route::get('delete/{uuid}', [BlogController::class, 'delete'])->name('delete');
    Route::get('blog-comment-list', [BlogController::class, 'blogCommentList'])->name('blog-comment-list');
    Route::post('change-blog-comment-status', [BlogController::class, 'changeBlogCommentStatus'])->name('changeBlogCommentStatus');
    Route::get('blog-comment-delete/{id}', [BlogController::class, 'blogCommentDelete'])->name('blogComment.delete');

    Route::get('blog-category-index', [BlogCategoryController::class, 'index'])->name('blog-category.index');
    Route::post('blog-category-store', [BlogCategoryController::class, 'store'])->name('blog-category.store');
    Route::patch('blog-category-update/{uuid}', [BlogCategoryController::class, 'update'])->name('blog-category.update');
    Route::get('blog-category-delete/{uuid}', [BlogCategoryController::class, 'delete'])->name('blog-category.delete');
});


Route::group(['prefix' => 'certificate', 'as' => 'certificate.'], function () {
    Route::get('/', [CertificateController::class, 'index'])->name('index');
    Route::get('create', [CertificateController::class, 'create'])->name('create');
    Route::post('store', [CertificateController::class, 'store'])->name('store');
    Route::get('edit/{uuid}', [CertificateController::class, 'edit'])->name('edit');
    Route::post('update/{uuid}', [CertificateController::class, 'update'])->name('update');
    Route::get('delete/{uuid}', [CertificateController::class, 'delete'])->name('delete');
});

Route::group(['prefix' => 'badge', 'as' => 'ranking.'], function () {
    Route::get('index', [RankingLevelController::class, 'index'])->name('index');
    Route::post('store', [RankingLevelController::class, 'store'])->name('store');
    Route::get('edit/{uuid}', [RankingLevelController::class, 'edit'])->name('edit');
    Route::patch('update/{badge:uuid}', [RankingLevelController::class, 'update'])->name('update');
    Route::get('delete/{uuid}', [RankingLevelController::class, 'delete'])->name('delete');
    Route::post('refresh/distribute', [RankingLevelController::class, 'resetBadge'])->name('reset_badge');
});

Route::group(['prefix'=>'skills'],function(){
    Route::get('index', [SkillController::class, 'index'])->name('skill.index');
    Route::post('store', [SkillController::class, 'store'])->name('skill.store');
    Route::patch('update/{id}', [SkillController::class, 'update'])->name('skill.update');
    Route::get('delete/{id}', [SkillController::class, 'delete'])->name('skill.delete');
});

Route::prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('admin.profile');
    Route::get('change-password', [ProfileController::class, 'changePassword'])->name('admin.change-password');
    Route::post('change-password', [ProfileController::class, 'changePasswordUpdate'])->name('admin.change-password.update');
    Route::post('update', [ProfileController::class, 'update'])->name('admin.profile.update');
});


Route::prefix('language')->group(function () {
    Route::get('/', [LanguageController::class, 'index'])->name('language.index');
    Route::get('create', [LanguageController::class, 'create'])->name('language.create');
    Route::post('store', [LanguageController::class, 'store'])->name('language.store');
    Route::get('edit/{id}/{iso_code?}', [LanguageController::class, 'edit'])->name('language.edit');
    Route::post('update/{id}', [LanguageController::class, 'update'])->name('language.update');
    Route::get('translate/{id}', [LanguageController::class, 'translateLanguage'])->name('language.translate');
    Route::post('update-translate/{id}', [LanguageController::class, 'updateTranslate'])->name('update.translate');
    Route::get('delete/{id}', [LanguageController::class, 'delete'])->name('language.delete');
    Route::post('import',[LanguageController::class, 'import'])->name('language.import');
    Route::post('update-language/{id}',[LanguageController::class, 'updateLanguage'])->name('update-language');
});

Route::group(['prefix' => 'contact', 'as' => 'contact.'], function () {
    Route::get('contact-us-list', [ContactUsController::class, 'contactUsIndex'])->name('index');
    Route::delete('contact-us-delete/{id}', [ContactUsController::class, 'contactUsDelete'])->name('delete');
    Route::resource('issue', ContactUsIssueController::class);
});

Route::group(['prefix' => 'support-ticket', 'as' => 'support-ticket.'], function () {
    Route::get('index', [SupportTicketController::class, 'ticketIndex'])->name('admin.index');
    Route::get('open', [SupportTicketController::class, 'ticketOpen'])->name('admin.open');
    Route::get('show/{uuid}', [SupportTicketController::class, 'ticketShow'])->name('admin.show');
    Route::get('delete/{uuid}', [SupportTicketController::class, 'ticketDelete'])->name('admin.delete');
    Route::post('change-ticket-status', [SupportTicketController::class, 'changeTicketStatus'])->name('admin.changeTicketStatus');
    Route::post('message-store', [SupportTicketController::class, 'messageStore'])->name('admin.messageStore');
});

//Start:: Page
Route::group(['prefix' => 'pages', 'as' => 'page.'], function () {
    Route::get('/', [PageController::class, 'index'])->name('index');
    Route::get('create', [PageController::class, 'create'])->name('create');
    Route::post('store', [PageController::class, 'store'])->name('store');
    Route::get('edit/{uuid}', [PageController::class, 'edit'])->name('edit');
    Route::post('update/{uuid}', [PageController::class, 'update'])->name('update');
    Route::get('delete/{uuid}', [PageController::class, 'delete'])->name('delete');
});
//End:: Page

//Start:: Menu
Route::group(['prefix' => 'menus', 'as' => 'menu.'], function () {
    Route::get('static-menu', [MenuController::class, 'staticMenu'])->name('static');
    Route::patch('static-menu/{slug}', [MenuController::class, 'staticMenuUpdate'])->name('static.update');
    Route::get('dynamic-menu', [MenuController::class, 'dynamicMenu'])->name('dynamic');
    Route::post('dynamic-menu-store', [MenuController::class, 'dynamicMenuStore'])->name('dynamic.store');
    Route::patch('dynamic-menu-update/{id}', [MenuController::class, 'dynamicMenuUpdate'])->name('dynamic.update');
    Route::get('dynamic-menu-delete/{id}', [MenuController::class, 'dynamicMenuDelete'])->name('dynamic.delete');
    Route::get('footer-company-menu', [MenuController::class, 'footerCompanyMenu'])->name('footer-company');
    Route::post('footer-company-menu-store', [MenuController::class, 'footerCompanyMenuStore'])->name('footer-company.store');
    Route::patch('footer-company-menu-update/{id}', [MenuController::class, 'footerCompanyMenuUpdate'])->name('footer-company.update');
    Route::get('footer-company-menu-delete/{id}', [MenuController::class, 'footerCompanyMenuDelete'])->name('footer-company.delete');
    Route::get('footer-support-menu', [MenuController::class, 'footerSupportMenu'])->name('footer-support');
    Route::post('footer-support-menu-store', [MenuController::class, 'footerSupportMenuStore'])->name('footer-support.store');
    Route::patch('footer-support-menu-update/{id}', [MenuController::class, 'footerSupportMenuUpdate'])->name('footer-support.update');
    Route::get('footer-support-menu-delete/{id}', [MenuController::class, 'footerSupportMenuDelete'])->name('footer-support.delete');
});
//End:: Menu

Route::name('admin.')->group(function (){
    Route::group(['prefix' => 'forum', 'as' => 'forum.'], function (){
        Route::get('category-index', [ForumCategoryController::class, 'index'])->name('category.index');
        Route::post('category-store', [ForumCategoryController::class, 'store'])->name('category.store');
        Route::patch('category-update/{uuid}', [ForumCategoryController::class, 'update'])->name('category.update');
        Route::get('category-delete/{uuid}', [ForumCategoryController::class, 'delete'])->name('category.delete');
    });
});

Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
    //Start:: General Settings
    Route::get('general-settings', [SettingController::class, 'GeneralSetting'])->name('general_setting');
    Route::post('general-settings-update', [SettingController::class, 'GeneralSettingUpdate'])->name('general_setting.cms.update');

    Route::get('metas', [SettingController::class, 'metaIndex'])->name('meta.index');
    Route::get('meta/edit/{uuid}', [SettingController::class, 'editMeta'])->name('meta.edit');
    Route::post('meta/update/{uuid}', [SettingController::class, 'updateMeta'])->name('meta.update');

    // Route::get('site-share-content', [SettingController::class, 'siteShareContent'])->name('site-share-content');
    Route::get('map-api-key', [SettingController::class, 'mapApiKey'])->name('map-api-key')->middleware('isDemo');
    Route::get('re-captcha-key', [SettingController::class, 'reCaptchaKey'])->name('re-captcha-key')->middleware('isDemo');
    Route::get('google-analytics', [SettingController::class, 'googleAnalytics'])->name('google-analytics')->middleware('isDemo');
    Route::get('generate-site-map', [SettingController::class, 'generateSiteMap'])->name('generate.site_map')->middleware('isDemo');
    Route::get('color-settings', [SettingController::class, 'colorSettings'])->name('color-settings');
    Route::get('font-settings', [SettingController::class, 'fontSettings'])->name('font-settings');
    //End:: General Settings

    //Start:: Maintenance Mode
    Route::get('maintenance-mode-changes', [SettingController::class, 'maintenanceMode'])->name('maintenance');
    Route::post('maintenance-mode-changes', [SettingController::class, 'maintenanceModeChange'])->name('maintenance.change')->middleware('isDemo');
    //End:: Maintenance Mode

    //Start:: Coming Soon Mode
    Route::get('coming-soon-mode-changes', [SettingController::class, 'comingSoonMode'])->name('coming-soon');
    Route::post('coming-soon-mode-changes', [SettingController::class, 'comingSoonModeChange'])->name('coming-soon.change')->middleware('isDemo');
    //End:: Coming Soon Mode

    //Start:: Device Control Mode
    Route::get('device-control-changes', [SettingController::class, 'deviceControl'])->name('device_control');
    Route::post('device-control-changes', [SettingController::class, 'deviceControlChange'])->name('device_control.change');
    //End:: Device Control Mode

    //Start:: Device Control Mode
    Route::get('private-mode-changes', [SettingController::class, 'privateMode'])->name('private_mode')->middleware('isDemo');
    Route::post('private-mode-changes', [SettingController::class, 'privateModeChange'])->name('private_mode.change')->middleware('isDemo');
    //End:: Device Control Mode

    //Start:: subscription Mode
    Route::get('subscription-mode-changes', [SettingController::class, 'subscriptionMode'])->name('subscription_mode');
    Route::post('subscription-mode-changes', [SettingController::class, 'subscriptionModeChange'])->name('subscription_mode.change');
    //End:: subscription Mode

    //Start:: saas Mode
    Route::get('saas-mode-changes', [SettingController::class, 'saasMode'])->name('saas_mode');
    Route::post('saas-mode-changes', [SettingController::class, 'saasModeChange'])->name('saas_mode.change');
    //End:: saas Mode

    //Start:: reward_points
    // Route::get('reward-points', [SettingController::class, 'rewardPoints'])->name('reward_points');
    Route::get('registration_bonus', [SettingController::class, 'registrationBonus'])->name('registration_bonus');
    Route::get('refund-system', [SettingController::class, 'refundSystem'])->name('refund_system');
    Route::get('course-gift-system', [SettingController::class, 'courseGiftSystem'])->name('course_gift_system');
    Route::get('wallet-checkout-enable', [SettingController::class, 'walletCheckoutSystem'])->name('wallet_checkout_system');
    Route::get('wallet-recharge-system', [SettingController::class, 'walletRechargeSystem'])->name('wallet_recharge_system');
    Route::get('chat-student-instructor', [SettingController::class, 'chatSystem'])->name('chat-student-instructor');
    Route::get('cashback_settings', [SettingController::class, 'cashbackSettings'])->name('cashback_settings');
    //End:: reward_points

    //Start:: BigBlueButton Settings
    Route::get('bbb-settings', [SettingController::class, 'BBBSettings'])->name('bbb-settings');
    Route::post('bbb-settings-update', [SettingController::class, 'BBBSettingsUpdate'])->name('bbb-settings.update');

    //Start:: Jitsi Settings
    Route::get('jitsi-settings', [SettingController::class, 'JitsiSettings'])->name('jitsi-settings');
    Route::post('jitsi-settings-update', [SettingController::class, 'JitsiSettingsUpdate'])->name('jitsi-settings.update');

    //Start:: GMeet Settings
    Route::get('gmeet-settings', [SettingController::class, 'gMeetSettings'])->name('gmeet_settings')->middleware('isDemo');
    Route::post('gmeet-settings-update', [SettingController::class, 'gMeetSettingsUpdate'])->name('gmeet_settings.update')->middleware('isDemo');

 //Start:: Zoom Settings
 Route::get('gzoom-settings', [SettingController::class, 'gZoomSettings'])->name('gzoom_settings')->middleware('isDemo');
 Route::post('gzoom-settings-update', [SettingController::class, 'gZoomSettingsUpdate'])->name('gzoom_settings.update')->middleware('isDemo');





    //Start:: Agora Settings
    Route::get('agora-settings', [SettingController::class, 'agoraSettings'])->name('agora_settings')->middleware('isDemo');
    Route::post('agora-settings-update', [SettingController::class, 'agoraSettingsUpdate'])->name('agora_settings.update')->middleware('isDemo');

    //Start:: Social Login Settings
    Route::get('social-login-settings', [SettingController::class, 'socialLoginSettings'])->name('social-login-settings')->middleware('isDemo');;
    Route::post('social-settings-update', [SettingController::class, 'socialLoginSettingsUpdate'])->name('social-login-settings.update')->middleware('isDemo');;

    //Start:: Cookie Settings
    Route::get('cookie-settings', [SettingController::class, 'cookieSettings'])->name('cookie-settings');
    Route::post('cookie-settings-update', [SettingController::class, 'cookieSettingsUpdate'])->name('cookie-settings.update');

    //Start:: AWS S3 Settings
    Route::get('storage-settings', [SettingController::class, 'storageSettings'])->name('storage-settings')->middleware('isDemo');
    Route::post('storage-settings-update', [SettingController::class, 'storageSettingsUpdate'])->name('storage-settings.update')->middleware('isDemo');

    //Start:: Vimeo Settings
    Route::get('vimeo-settings', [SettingController::class, 'vimeoSettings'])->name('vimeo-settings')->middleware('isDemo');
    Route::post('vimeo-settings-update', [SettingController::class, 'vimeoSettingsUpdate'])->name('vimeo-settings.update');

    //Start:: Currency Settings
    Route::group(['prefix' => 'currency', 'as' => 'currency.'], function () {
        Route::get('', [CurrencyController::class, 'index'])->name('index');
        Route::post('currency', [CurrencyController::class, 'store'])->name('store');
        Route::get('edit/{id}/{slug?}', [CurrencyController::class, 'edit'])->name('edit');
        Route::patch('update/{id}', [CurrencyController::class, 'update'])->name('update');
        Route::delete('delete/{id}', [CurrencyController::class, 'delete'])->name('delete');
    });



        //Start:: Zoom Accounts Setting
        Route::group(['prefix' => 'zoom_account', 'as' => 'zoom_account.'], function () {
            Route::get('', [ZoomAccountController::class, 'index'])->name('index');
            Route::get('status_change/{id}', [ZoomAccountController::class, 'statusChange'])->name('status_change');
            Route::post('zoom', [ZoomAccountController::class, 'store'])->name('store');
            // Route::get('edit/{id}/{slug?}', [ZoomAccountController::class, 'edit'])->name('edit');
            Route::get('edit/{id}', [ZoomAccountController::class, 'edit'])->name('edit');
            Route::patch('update/{id}', [ZoomAccountController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [ZoomAccountController::class, 'delete'])->name('delete');
        });


    //Start:: Home Settings
    Route::get('theme-settings', [HomeSettingController::class, 'themeSettings'])->name('theme-setting');
    Route::get('section-settings', [HomeSettingController::class, 'sectionSettings'])->name('section-settings');
    Route::post('sectionSettingsStatusChange', [HomeSettingController::class, 'sectionSettingsStatusChange'])->name('sectionSettingsStatusChange');
    Route::get('banner-section-settings', [HomeSettingController::class, 'bannerSection'])->name('banner-section');
    Route::post('banner-section-settings', [HomeSettingController::class, 'bannerSectionUpdate'])->name('banner-section.update');
    Route::get('special-feature-section-settings', [HomeSettingController::class, 'specialFeatureSection'])->name('special-feature-section');
    Route::get('course-section-settings', [HomeSettingController::class, 'courseSection'])->name('course-section');
    Route::get('category-course-section-settings', [HomeSettingController::class, 'categoryCourseSection'])->name('category-course-section');
    Route::get('upcoming-course-section-settings', [HomeSettingController::class, 'upcomingCourseSection'])->name('upcoming-course-section');
    Route::get('product-section-settings', [HomeSettingController::class, 'productSection'])->name('product-section');
    Route::get('bundle-course-section-settings', [HomeSettingController::class, 'bundleCourseSection'])->name('bundle-course-section');
    Route::get('top-category-section-settings', [HomeSettingController::class, 'topCategorySection'])->name('top-category-section');
    Route::get('top-instructor-section-settings', [HomeSettingController::class, 'topInstructorSection'])->name('top-instructor-section');
    Route::get('become-instructor-video-section-settings', [HomeSettingController::class, 'becomeInstructorVideoSection'])->name('become-instructor-video-section');
    Route::get('customer-say-section-settings', [HomeSettingController::class, 'customerSaySection'])->name('customer-say-section');
    Route::get('achievement-section-settings', [HomeSettingController::class, 'achievementSection'])->name('achievement-section');
    //End:: Home Settings

    //Start:: Mail Config
    Route::get('mail-configuration', [SettingController::class, 'mailConfiguration'])->name('mail-configuration')->middleware('isDemo');
    Route::post('send-test-mail', [SettingController::class, 'sendTestMail'])->name('send.test.mail')->middleware('isDemo');
    Route::post('save-setting', [SettingController::class, 'saveSetting'])->name('save.setting')->middleware('isDemo');
    //End:: Mail Config

    //Start:: Become an Instructor
    Route::get('instructor-feature-settings', [SettingController::class, 'instructorFeatureSetting'])->name('instructor-feature');
    Route::post('instructor-feature-settings', [SettingController::class, 'instructorFeatureSettingUpdate'])->name('instructor-feature.update');

    Route::get('instructor-procedure-settings', [SettingController::class, 'instructorProcedureSetting'])->name('instructor-procedure');
    Route::post('instructor-procedure-settings', [SettingController::class, 'instructorProcedureSettingUpdate'])->name('instructor-procedure.update');
    Route::get('instructor-cms', [SettingController::class, 'instructorCMSSetting'])->name('instructor.cms');
    //End:: Become an Instructor

    //Start:: FAQ Question & Answer
    Route::get('faq-settings', [SettingController::class, 'faqCMS'])->name('faq.cms');
    Route::get('faq-tab', [SettingController::class, 'faqTab'])->name('faq.tab');
    Route::get('faq-question-settings', [SettingController::class, 'faqQuestion'])->name('faq.question');
    Route::post('faq-question-settings', [SettingController::class, 'faqQuestionUpdate'])->name('faq.question.update');
    //End:: FAQ Question & Answer

    //Start:: Support Ticket
    Route::group(['prefix' => 'support-ticket', 'as' => 'support-ticket.'], function () {
        Route::get('/', [SettingController::class, 'supportTicketCMS'])->name('cms');
        Route::get('question-answer', [SettingController::class, 'supportTicketQuesAns'])->name('question');
        Route::post('question-answer', [SettingController::class, 'supportTicketQuesAnsUpdate'])->name('question.update');

        Route::get('department', [SupportTicketController::class, 'Department'])->name('department');
        Route::post('department', [SupportTicketController::class, 'DepartmentStore'])->name('department.store');
        Route::delete('department-delete/{uuid}', [SupportTicketController::class, 'departmentDelete'])->name('department.delete');

        Route::get('priority', [SupportTicketController::class, 'Priority'])->name('priority');
        Route::post('priority', [SupportTicketController::class, 'PriorityStore'])->name('priority.store');
        Route::delete('priorities-delete/{uuid}', [SupportTicketController::class, 'priorityDelete'])->name('priority.delete');

        Route::get('services', [SupportTicketController::class, 'RelatedService'])->name('services');
        Route::post('services', [SupportTicketController::class, 'RelatedServiceStore'])->name('services.store');
        Route::delete('services-delete/{uuid}', [SupportTicketController::class, 'relatedServiceDelete'])->name('services.delete');
    });
    //End:: Support Ticket

    // Start:: Contact Us
    Route::get('contact-us-cms', [ContactUsController::class, 'contactUsCMS'])->name('contact.cms');
    // End:: Contact Us

    Route::get('payment-method', [SettingController::class, 'paymentMethod'])->name('payment_method_settings')->middleware('isDemo');

    //start:: Bank
    Route::group(['prefix' => 'bank'], function () {
        Route::get('/', [BankController::class, 'index'])->name('bank.index');
        Route::post('/store', [BankController::class, 'store'])->name('bank.store');
        Route::get('/edit/{id}', [BankController::class, 'edit'])->name('bank.edit');
        Route::patch('/update/{id}', [BankController::class, 'update'])->name('bank.update');
        Route::get('/status/{id}', [BankController::class, 'status'])->name('bank.status');
        Route::delete('delete/{id}', [BankController::class, 'delete'])->name('bank.delete');
    });

    // Start:: About Us
    Route::group(['prefix' => 'about', 'as' => 'about.'], function () {
        Route::get('about-us-gallery-area', [AboutUsController::class, 'galleryArea'])->name('gallery-area');
        Route::post('about-us-gallery-area', [AboutUsController::class, 'galleryAreaUpdate'])->name('gallery-area.update');

        Route::get('about-us-our-history', [AboutUsController::class, 'ourHistory'])->name('our-history');
        Route::post('about-us-our-history', [AboutUsController::class, 'ourHistoryUpdate'])->name('our-history.update');

        Route::get('about-us-upgrade-skill', [AboutUsController::class, 'upgradeSkill'])->name('upgrade-skill');
        Route::post('about-us-upgrade-skill', [AboutUsController::class, 'upgradeSkillUpdate'])->name('upgrade-skill.update');

        Route::get('about-us-team-member', [AboutUsController::class, 'teamMember'])->name('team-member');
        Route::post('about-us-team-member', [AboutUsController::class, 'teamMemberUpdate'])->name('team-member.update');

        Route::get('about-us-instructor-support', [AboutUsController::class, 'instructorSupport'])->name('instructor-support');
        Route::post('about-us-instructor-support', [AboutUsController::class, 'instructorSupportUpdate'])->name('instructor-support.update');

        Route::get('about-us-client', [AboutUsController::class, 'client'])->name('client');
        Route::post('about-us-client', [AboutUsController::class, 'clientUpdate'])->name('client.update');
    });
    // End:: About Us
    Route::group(['prefix' => 'locations', 'as' => 'location.'], function () {
        Route::get('country', [LocationController::class, 'countryIndex'])->name('country.index');
        Route::post('country', [LocationController::class, 'countryStore'])->name('country.store');
        Route::get('country/{id}/{slug?}', [LocationController::class, 'countryEdit'])->name('country.edit');
        Route::patch('country/{id}', [LocationController::class, 'countryUpdate'])->name('country.update');
        Route::delete('country/delete/{id}', [LocationController::class, 'countryDelete'])->name('country.delete');

        Route::get('state', [LocationController::class, 'stateIndex'])->name('state.index');
        Route::post('state', [LocationController::class, 'stateStore'])->name('state.store');
        Route::get('state/{id}/{slug?}', [LocationController::class, 'stateEdit'])->name('state.edit');
        Route::patch('state/{id}', [LocationController::class, 'stateUpdate'])->name('state.update');
        Route::delete('state/delete/{id}', [LocationController::class, 'stateDelete'])->name('state.delete');

        Route::get('city', [LocationController::class, 'cityIndex'])->name('city.index');
        Route::post('city', [LocationController::class, 'cityStore'])->name('city.store');
        Route::get('city/{id}/{slug?}', [LocationController::class, 'cityEdit'])->name('city.edit');
        Route::patch('city/{id}', [LocationController::class, 'cityUpdate'])->name('city.update');
        Route::delete('city/delete/{id}', [LocationController::class, 'cityDelete'])->name('city.delete');
    });

    //Migrate, Cache
    Route::get('cache-settings', [SettingController::class, 'cacheSettings'])->name('cache-settings')->middleware('isDemo');
    Route::get('cache-update/{id}', [SettingController::class, 'cacheUpdate'])->name('cache-update')->middleware('isDemo');
    Route::get('migrate-settings', [SettingController::class, 'migrateSettings'])->name('migrate-settings')->middleware('isDemo');
    Route::get('migrate-update', [SettingController::class, 'migrateUpdate'])->name('migrate-update')->middleware('isDemo');

    Route::get('version-update', [VersionUpdateController::class, 'versionFileUpdate'])->name('file-version-update')->middleware('isDemo');
    Route::post('version-update', [VersionUpdateController::class, 'versionFileUpdateStore'])->name('file-version-update-store')->middleware('isDemo');
    Route::get('version-update-execute', [VersionUpdateController::class, 'versionUpdateExecute'])->name('file-version-update-execute')->middleware('isDemo');
    Route::get('version-delete', [VersionUpdateController::class, 'versionFileUpdateDelete'])->name('file-version-delete')->middleware('isDemo');


});

Route::group(['prefix' => 'addon', 'as' => 'admin.addon.'], function () {
    Route::get('details/{code}', [AddonUpdateController::class, 'addonDetails'])->name('details')->withoutMiddleware(['addon']);
    Route::post('store', [AddonUpdateController::class, 'addonFileStore'])->name('store')->withoutMiddleware(['addon']);
    Route::post('execute', [AddonUpdateController::class, 'addonFileExecute'])->name('execute')->withoutMiddleware(['addon']);
    Route::get('delete/{code}', [AddonUpdateController::class, 'addonFileDelete'])->name('delete')->withoutMiddleware(['addon']);
});

Route::get('privacy-policy', [PolicyController::class, 'privacyPolicy'])->name('admin.privacy-policy');
Route::post('privacy-policy', [PolicyController::class, 'privacyPolicyStore'])->name('admin.privacy-policy.store');

Route::get('terms-conditions', [PolicyController::class, 'termConditions'])->name('admin.terms-conditions');
Route::post('terms-conditions', [PolicyController::class, 'termConditionsStore'])->name('admin.terms-conditions.store');

Route::get('cookie-policy', [PolicyController::class, 'cookiePolicy'])->name('admin.cookie-policy');
Route::post('cookie-policy', [PolicyController::class, 'cookiePolicyStore'])->name('admin.cookie-policy.store');

Route::get('refund-policy', [PolicyController::class, 'refundPolicy'])->name('admin.refund-policy');
Route::post('refund-policy', [PolicyController::class, 'refundPolicyStore'])->name('admin.refund-policy.store');


Route::group(['prefix' => 'affiliate','as' => 'affiliate.'], function () {

    // TODO:don't delete,  this will need
//    Route::get('/affiliator-history', function () {
//        return view('admin.affiliate.affiliator-history');
//    })->name('affiliator-history');

//    Route::get('/payouts', function () {
//        return view('admin.affiliate.payouts');
//    })->name('payouts');

    Route::get('affiliation-settings', [SettingController::class, 'referralSettings'])->name('affiliation-settings');
    Route::post('referral-settings-update', [SettingController::class, 'referralSettingsUpdate'])->name('referral-settings.update');

    Route::get('affiliate-request-list', [AffiliateController::class, 'affiliateRequestList'])->name('affiliate-request-list');
    Route::post('affiliate-request-status-change', [AffiliateController::class, 'affiliateRequestStatusChange'])->name('affiliate-request-status-change');

    Route::get('affiliate-history', [AffiliateController::class, 'affiliateHistory'])->name('affiliate-history');
    Route::get('affiliate-history-date', [AffiliateController::class, 'allAffiliates'])->name('affiliate-history-data');
});

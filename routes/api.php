<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Frontend\RegistrationController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Admin\TimeSlotController;
use App\Http\Controllers\Api\Frontend\HomeController as ApiHomeController;
use App\Http\Controllers\Api\Instructor\DashboardController as InstructorDashboardController;
use App\Http\Controllers\Api\Instructor\InstructorProfileController;
use App\Http\Controllers\Api\Instructor\LiveClassController;
use App\Http\Controllers\Api\Instructor\NoticeBoardApiController;
use App\Http\Controllers\Api\Instructor\StudentController;
use App\Http\Controllers\Api\DonationWebhookLogController;
use App\Http\Controllers\Api\DonationWebhookPreviewController;
use App\Http\Controllers\Api\DonationWebhookResendController;
use App\Http\Controllers\Api\DonationReceiptEmailLogController;
use App\Http\Controllers\Api\DonationReceiptEmailPreviewController;
use App\Http\Controllers\Api\DonationReceiptEmailResendController;
use App\Http\Controllers\Api\Frontend\AcademyHomeController;
use App\Http\Controllers\Api\Student\CartManagementController;
use App\Http\Controllers\Api\Student\VaultStreamController;
use App\Http\Controllers\Frontend\CourseController;
use App\Services\GoogleOAuthService;
use Illuminate\Http\JsonResponse;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



Route::post('login', [LoginController::class, 'userLoginApi'])->name('login');
Route::post('logoutApi', [LoginController::class, 'logoutApiPost'])->name('logout');


Route::post('forgot-password', [LoginController::class, 'sendResetLinkEmail']);
Route::post('reset-password', [LoginController::class, 'resetPasswordApi']);

Route::get('coordinator/students-list', [InstructorDashboardController::class, 'allStudentsList'])->name('coordinator-students-list');
Route::get('coordinator/instructors-list', [InstructorDashboardController::class, 'allInstructorsList'])->name('coordinator-instructors-list');


Route::post('get_pending_orders', [ApiHomeController::class, 'getPendingOrders'])
  ->name('get_pending_orders');


  Route::post('get_class_detail', [ApiHomeController::class, 'getClassDetails'])
  ->name('get_class_detail');


  
  Route::post('send_payment_email_with_transaction_id', [ApiHomeController::class, 'sendPaymentEmailWithTransactionID'])
  ->name('send_payment_email_with_transaction_id');



   Route::post('send_payment_email_with_order_id', [ApiHomeController::class, 'sendPaymentEmailWithOrderID'])
  ->name('send_payment_email_with_order_id');


Route::post('checkout_faysal_ipn_api/{user_id?}/{payment_type?}/{cart_id?}/{live_enrollment?}/{trial_id?}', [DashboardController::class, 'checkoutFaysalIPNAPI'])->name('checkout_faysal_ipn_api');



Route::post('meezan-ipn', [DashboardController::class, 'meezanIPN'])->name('meezan-ipn');



//Instructor Routes
Route::post('sign-up-as-instructor', [RegistrationController::class, 'storeSignUpAsInstructorApi'])->name('sign-up-as-instructor');
//Student Register
Route::post('register', [RegistrationController::class, 'storeSignUpAsStudentApi'])->name('sign-up-as-student');
Route::get('frontend/get-all-timezones', [ApiHomeController::class, 'getAllTimeZonesApi'])->name('frontend-local-get-all-timezones');


Route::get('frontend/get-courses-by-type/{type_id?}', [ApiHomeController::class, 'getAllCoursesByTypeApi'])->name('frontend-local-get-courses-by-type');
Route::get('frontend/get-countries-list', [ApiHomeController::class, 'getAllCountriesListsApi'])->name('frontend-local-get-countries-list');

Route::get('frontend/get-time-slots', [ApiHomeController::class, 'getTimeSlotsApi'])->name('frontend-local-get-time-slots');

// API Route for fetching time slots
Route::get('time-slots', [TimeSlotController::class, 'indexApi'])->name('time_slots.index_api');







  Route::get('student/successful_faysal_api/{user_id?}/{cart_id?}', [CartManagementController::class, 'successfulFaysalApi'])
  ->name('student.successfulFaysalApi')
  ->middleware('optional.auth');

   Route::get('student/failure_faysal_api', [CartManagementController::class, 'failureFaysalApi'])
  ->name('student.failureFaysalApi')
  ->middleware('optional.auth');






   Route::get('student/failure_faysal_api_all', [CartManagementController::class, 'failureFaysalApiAll'])
  ->name('student.failureFaysalApiAll')
  ->middleware('optional.auth');



  Route::get('student/checkout_faysal_api/{user_id?}/{cart_id?}', [CartManagementController::class, 'checkoutFaysalApi'])
  ->name('student.checkoutFaysalApi')
  ->middleware('optional.auth');



Route::get('frontend/home', [ApiHomeController::class, 'homeApi'])->name('frontend-home')
  ->middleware('optional.auth');

Route::get('frontend/all-products', [ApiHomeController::class, 'allProductsApi'])->name('frontend-all-products')
  ->middleware('optional.auth');
Route::post('frontend/product-detail-view', [ApiHomeController::class, 'productDetailViewApi'])->name('frontend-product-detail-view')
  ->middleware('optional.auth');
Route::get('frontend/all-courses', [ApiHomeController::class, 'allCoursesApi'])->name('frontend-all-courses')
  ->middleware('optional.auth');
Route::post('frontend/all-categorywise-courses', [ApiHomeController::class, 'allCategoryWiseCoursesApi'])->name('frontend-all-categorywise-courses')
  ->middleware('optional.auth');
Route::post('frontend/all-subcategorywise-courses', [ApiHomeController::class, 'allSubCategoryWiseCoursesApi'])->name('frontend-all-subcategorywise-courses')
  ->middleware('optional.auth');


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
Route::get('frontend/course/detail/{slug}', [ApiHomeController::class, 'courseDetailApi'])->name('frontend-course-detail')
  ->middleware('optional.auth');

// Route::post('frontend/course/detail', [ApiHomeController::class, 'courseDetailApi2'])->name('frontend-course-detail2')
// ->middleware('optional.auth');
///////////////////////////////////////////////////////////////////////////////////////////////////////////////

Route::get('frontend/all-categories-with-names', [ApiHomeController::class, 'allCategoriesWithNamesApi'])->name('frontend-all-categories-with-names')
  ->middleware('optional.auth');

Route::get('frontend/all-categories-with-subcategories', [ApiHomeController::class, 'allCategoriesWithResourcesApi'])->name('frontend-all-categories-with-subcategories')
  ->middleware('optional.auth');
Route::post('frontend/course/search', [ApiHomeController::class, 'courseSearchApi'])->name('frontend-course-search')
  ->middleware('optional.auth');
Route::get('frontend/local/get-all-languages', [ApiHomeController::class, 'getAllLanguagesApi'])->name('frontend-local-get-all-languages')
  ->middleware('optional.auth');
Route::post('frontend/local/update-language', [ApiHomeController::class, 'updateLanguageApi'])->name('frontend-local-update-language')
  ->middleware('optional.auth');
Route::post('frontend/course/instructor-profile', [ApiHomeController::class, 'courseInstructorProfileApi'])->name('frontend-course-instructor-profile')
  ->middleware('optional.auth');



// Academy Homepage API endpoints (public, no auth required)
Route::get('academy/courses', [AcademyHomeController::class, 'courses'])->name('academy-courses');
Route::get('academy/instructors', [AcademyHomeController::class, 'instructors'])->name('academy-instructors');
Route::get('academy/blogs', [AcademyHomeController::class, 'blogs'])->name('academy-blogs');
Route::get('academy/blog/{slug}', [AcademyHomeController::class, 'blogBySlug'])->name('academy-blog-by-slug');
Route::get('academy/products', [AcademyHomeController::class, 'products'])->name('academy-products');
Route::get('academy/product-categories', [AcademyHomeController::class, 'productCategories'])->name('academy-product-categories');
Route::get('academy/products/list', [AcademyHomeController::class, 'productList'])->name('academy-products-list');
Route::get('academy/search', [AcademyHomeController::class, 'search'])->name('academy-search');
Route::get('academy/categories', [AcademyHomeController::class, 'categories'])->name('academy-categories');
Route::get('academy/subcategories', [AcademyHomeController::class, 'subcategories'])->name('academy-subcategories');
Route::get('academy/category-courses', [AcademyHomeController::class, 'categoryCourses'])->name('academy-category-courses');

 Route::post('student/validate_license', [DashboardController::class, 'validateLicense'])->name('validateLicense');

Route::post('student/sync_license', [DashboardController::class, 'syncLicense'])->name('syncLicense');

Route::post('student/vault_security_event', [DashboardController::class, 'vaultSecurityEvent'])->name('vaultSecurityEvent');

Route::post('student/vault_app_logout', [DashboardController::class, 'vaultAppLogout'])->name('vaultAppLogout');

Route::post('student/get_license_resources', [DashboardController::class, 'getLicenseResources'])->name('getLicenseResources');

Route::get('vault/stream-video/{resourceId}', [VaultStreamController::class, 'streamVideo'])->name('vault.stream-video');



Route::get('donation-view-cart/{session_id}', [CartManagementController::class, 'donationViewCart'])->name('donation-view-cart');
Route::post('donation-add-to-cart', [CartManagementController::class, 'donationAddToCart'])->name('donation-add-to-cart');
Route::post('donation-update-cart', [CartManagementController::class, 'donationUpdateCart'])->name('donation-update-cart');
Route::post('donation-delete-cart-item', [CartManagementController::class, 'donationDeleteCartItem'])->name('donation-delete-cart-item');
Route::post('donation-delete-cart', [CartManagementController::class, 'donationDeleteCart'])->name('donation-delete-cart');
Route::post('donation-pay-checkout-all', [CartManagementController::class, 'donationPayCheckoutAll'])->name('donation-pay-checkout-all');



Route::post('donation-checkout-faysal-ipn-api/{session_id?}/{user_id?}/{payment_type?}/{cart_id?}/{live_enrollment?}/{trial_id?}', [DashboardController::class, 'donationCheckoutFaysalIPNAPI'])->name('donation-checkout-faysal-ipn-api');

  Route::get('donation-failure-faysal-api-all/{session_id}', [CartManagementController::class, 'donationFailureFaysalApiAll'])
  ->name('donation-failure-faysal-api-all');


    Route::get('donation-successful-faysal-api-all/{session_id}', [CartManagementController::class, 'donationSuccessfulFaysalApiAll'])
  ->name('donation-successful-faysal-api-all');


    Route::post('donation-meezan-api-all', [DashboardController::class, 'donationSuccessfulMeezanApiAll'])
  ->name('donation-meezan-api-all');

    Route::get('donations-data', [CartManagementController::class, 'getGroupedDonations'])
  ->name('donations-data');

    Route::get('donation-webhook-logs', [DonationWebhookLogController::class, 'index'])
  ->name('donation-webhook-logs');

    Route::get('donation-webhook-preview', DonationWebhookPreviewController::class)
  ->name('donation-webhook-preview');

    Route::post('donation-resend-webhook', DonationWebhookResendController::class)
  ->name('donation-resend-webhook');

    Route::get('donation-receipt-email-logs', [DonationReceiptEmailLogController::class, 'index'])
  ->name('donation-receipt-email-logs');
    Route::get('donation-receipt-email-preview', DonationReceiptEmailPreviewController::class)
  ->name('donation-receipt-email-preview');
    Route::post('donation-resend-receipt-email', DonationReceiptEmailResendController::class)
  ->name('donation-resend-receipt-email');

      Route::get('pending-carts-data', [CartManagementController::class, 'getPendingCartsData'])
  ->name('pending-carts-data');





Route::get('me', [LoginController::class, 'meApi'])->name('me');
Route::post('live-enrollment-request', [CourseController::class, 'saveLiveEnrollmentRequestApi'])->name('live-enrollment-request-api');

Route::middleware(['auth:sanctum'])->group(function () {

  Route::post('student/generate-secure-checkout-url', [DashboardController::class, 'generateSecureCheckoutUrl'])->name('generate-secure-checkout-url');


  Route::get('student/get-course-assets', [DashboardController::class, 'getStudentCourseAssets'])->name('get-course-assets');
  Route::get('student/get_course_resources', [DashboardController::class, 'getStudentCourseResources'])->name('get-course-resources');

  Route::get('student/latestResources', [DashboardController::class, 'latestResources'])->name('student-latest-resources');
  Route::get('student/latestBlogs', [DashboardController::class, 'latestBlogs'])->name('latest-blogs-student');
  Route::get('student/latestLeaderBoard', [DashboardController::class, 'latestLeaderBoard'])->name('latest-leaderboard-student');
  Route::get('student/TodayLiveClass', [DashboardController::class, 'TodayLiveClass'])->name('today-live-class-student');
  Route::get('student/latestEnrolledCourses', [DashboardController::class, 'latestEnrolledCourses'])->name('latest-enrolled-courses-student');
  Route::get('student/monthlyClasses', [DashboardController::class, 'monthlyClasses'])->name('monthly-classes-student');
  Route::get('student/my-learning', [DashboardController::class, 'myLearningCoursesList'])->name('my-learning-student');
  Route::get('student/my-course/{slug}/{action_type?}/{quiz_uuid?}/{answer_id?}', [DashboardController::class, 'myCourseShowApi'])->name('my-course-show-student');
  Route::get('student/my-schedule' , [DashboardController::class, 'mySchedule'])->name('my-schedule-student');
  Route::get('student/billing-history', [DashboardController::class, 'billingHistory'])->name('billing-history-student');
  Route::get('student/paid-history', [DashboardController::class, 'paidHistory'])->name('paid-history-student');
 Route::post('student/pending-payment/pay-checkout', [DashboardController::class, 'studentPendingPaymentPayCheckout'])->name('student-pending-payment-pay-checkout');
 Route::post('student/pending-payment/pay-checkout-all', [DashboardController::class, 'studentPendingPaymentPayCheckoutAll'])->name('student-pending-payment-pay-checkout-all');
 Route::post('student/checkout-free', [DashboardController::class, 'studentCheckoutFreeAll'])->name('student-checkout-free');

  Route::post('student/course/click-lecture', [DashboardController::class, 'clickLectureApi'])->name('click-lecture-course');
  Route::post('student/make-donation', [DashboardController::class, 'makeDonation'])->name('make-donation');
  Route::post('student/delete-donation', [DashboardController::class, 'deleteDonation'])->name('delete-donation');
  Route::post('student/delete-cart-type-payment', [DashboardController::class, 'deleteCartTypePayment'])->name('delete-cart-type-payment');




  Route::post('student/course/start-quiz', [DashboardController::class, 'startQuizApi'])->name('start-quiz-course');
  Route::post('student/course/submit-quiz-answer', [DashboardController::class, 'submitQuizAnswerApi'])->name('submit-quiz-answer');
  Route::post('student/course/submit-quiz-answer-api', [DashboardController::class, 'submitQuizAnswerApiVersion'])->name('submit-quiz-answer-api');
  Route::post('student/course/quiz-result', [DashboardController::class, 'quizResultApi'])->name('quiz-result-course');
  Route::post('student/course/quiz-leaderboard', [DashboardController::class, 'quizLeaderBoardApi'])->name('quiz-leaderboard-course');
  Route::post('student/check-quiz-completion', [DashboardController::class, 'checkQuizCompletionInfo'])->name('check-quiz-completion');
  Route::post('student/course/view-assignments-list', [DashboardController::class, 'viewAssignmentsListApi'])->name('course-viewassignmentslist-student');
  Route::post('student/course/assignment-detail', [DashboardController::class, 'assignmentDetailApi'])->name('assignment-detail-course');
  Route::post('student/course/assignment-result', [DashboardController::class, 'assignmentResultApi'])->name('assignment-result-course');
  Route::post('student/course/submit-assignment-store', [DashboardController::class, 'assignmentSubmitStoreApi'])->name('course-submit-assignment-store-student');

  Route::post('student/course/review-create', [DashboardController::class, 'reviewCreateApi'])->name('review-create-student');

  Route::get('student/course/discussion-list/{course_id}', [DashboardController::class, 'discussionListApi'])->name('discussion-list-course');
  Route::get('student/course/review-list/{course_id}', [DashboardController::class, 'reviewListApi'])->name('reviews-list-course');

  Route::get('student/course/quiz-list/{course_id}', [DashboardController::class, 'quizListApi'])->name('quiz-list-course');


  Route::post('student/course/create-discussion', [DashboardController::class, 'createDiscussionApi'])->name('create-discussion-course');
  Route::post('student/course/create-discussion-reply', [DashboardController::class, 'createDiscussionReplyApi'])->name('create-discussion-reply-course');

  Route::get('student/resources/{course_uuid}', [DashboardController::class, 'resourceList'])->name('resources-show-student');
  Route::get('student/courses/{courseId}/categories/{categoryId}/resources', [DashboardController::class, 'getResources']);

  Route::get('student/courses/{courseId}/categories/{categoryId}/getresources', [DashboardController::class, 'getResourcesApi']);
  Route::get('stream-resource/{id}', [DashboardController::class , 'stream'])->name('resource.stream');

  Route::get('student/product/purchase-list', [DashboardController::class, 'productPurchaseList'])->name('product-purchase-list');
  Route::get('student/product/invoice-detail/{item_id}', [DashboardController::class, 'productInvoiceDetail'])->name('product-invoice-detail');
  Route::get('student/product/view/{slug}', [DashboardController::class, 'productView'])->name('product-view');
  Route::post('student/product/review-create', [DashboardController::class, 'productReviewCreate'])->name('product-review-create');

  Route::get('student/class-schedule', [DashboardController::class, 'studentClassSchedule'])->name('student-class-schedule-index');
  Route::post('student/start-zoom-class', [DashboardController::class, 'startZoomClass'])->name('student-start-zoom-class');
  Route::post('student/update-zoom-signature', [DashboardController::class, 'UpdateMeetingSignatureApi'])->name('update-meeting-signature-api');
  Route::post('student/generate_zoom_token', [DashboardController::class, 'generateZoomToken'])->name('student-generate-zoom-token');


    Route::post('student/start-zoom-class-live', [DashboardController::class, 'startZoomClassLive'])->name('student-start-zoom-class-live');
  Route::post('student/update-zoom-signature-live', [DashboardController::class, 'UpdateMeetingSignatureApiLive'])->name('update-meeting-signature-api-live');

  
  Route::post('student/get-zoom-current-time', [DashboardController::class, 'GetCurrentTimeZoomStudentApi'])->name('get-current-time-zoom-student-api');
  Route::post('student/update-student-current-time', [DashboardController::class, 'UpdateUrlZoomStudentApi'])->name('update-url-zoom-student-api');
  Route::post('student/check-instructor-left', [DashboardController::class, 'CheckModeratorLeftZoomStudentApi'])->name('check-moderator-student-api');

  Route::get('student/class-history', [DashboardController::class, 'studentClassHistory'])->name('student-class-history-index');

  Route::get('student/pending-payments', [DashboardController::class, 'studentPendingPayments'])->name('student-pending-payments-index');
  Route::post('student/pending-payment/checkout-details', [DashboardController::class, 'studentPendingPaymentCheckoutDetail'])->name('student-pending-payment-checkout-details');
 

  

  Route::post('student/successful_meezan_api', [DashboardController::class, 'successfulMeezanApi'])
  ->name('student.successfulMeezanApi');

 

    Route::post('student/successful_meezan_api_all', [DashboardController::class, 'successfulMeezanApiAll'])
  ->name('student.successfulMeezanApiAll');



  // Route::get('student/successful_faysal_api_all/{user_id?}', [CartManagementController::class, 'successfulFaysalApiAll'])
  // ->name('student.successfulFaysalApiAll');
  // // ->middleware('optional.auth');


   Route::get('student/successful_faysal_api_all', [CartManagementController::class, 'successfulFaysalApiAll'])
  ->name('student.successfulFaysalApiAll');
  // ->middleware('optional.auth');


  Route::post('student/checkLessonStartTime', [DashboardController::class, 'studentCheckLessonStartTime'])->name('student-check-lesson-time');
  Route::post('student/checkLessonStartTimeLiveClass', [DashboardController::class, 'studentCheckLessonStartTimeLiveClass'])->name('student-check-lesson-time-liveclass');

  Route::post('student/update-profile', [DashboardController::class, 'studentUpdateProfile'])->name('student-update-profile');
  Route::get('student/update-address-location', [DashboardController::class, 'getstudentAddressLocation'])->name('student-update-address-location');
  Route::post('student/update-address-location', [DashboardController::class, 'studentUpdateAddressLocation'])->name('student-update-address-location');
  Route::post('student/update-password', [DashboardController::class, 'studentUpdatePassword'])->name('student-update-password');


  Route::get('student/chat/index', [DashboardController::class, 'chatIndex'])->name('student-chat-index');
  Route::post('student/chat/get-existing-messages', [DashboardController::class, 'studentChatGetMessages'])->name('student-chat-get-messages');
  Route::post('student/chat/send-message', [DashboardController::class, 'studentSendMessage'])->name('student-send-message');

  Route::get('student/wishlist/index', [DashboardController::class, 'wishlistIndex'])->name('student-wishlist-index');
  Route::post('student/wishlist/delete', [DashboardController::class, 'wishlistDelete'])->name('student-wishlist-delete');


  Route::post('student/course/add-to-wishlist', [DashboardController::class, 'addToWishList'])->name('student-course-add-to-wishlist');
  Route::post('student/course/share', [DashboardController::class, 'shareCourse'])->name('student-course-share');

  Route::get('student/support/faqs', [DashboardController::class, 'supportFAQs'])->name('support-faqs');
  Route::get('student/support/tickets/index', [DashboardController::class, 'supportTicketIndex'])->name('support-tickets-index');
  Route::post('student/support/ticket/create', [DashboardController::class, 'supportTicketCreate'])->name('support-ticket-create');
  Route::post('student/support/ticket/view', [DashboardController::class, 'supportTicketView'])->name('support-ticket-view');
  Route::post('student/support/ticket/send-reply', [DashboardController::class, 'supportTicketSendReply'])->name('support-ticket-send-reply');

  // Cart Api Routes
  Route::post('student/cart/add', [CartManagementController::class, 'addToCart'])->name('student-cart-add');
  Route::post('student/enroll-course', [DashboardController::class, 'studentEnrollCourse'])->name('student-enroll-course');
  
    Route::post('student/buy-course', [DashboardController::class, 'studentBuyCourse'])->name('student-buy-course');
   
  Route::get('student/cartList', [CartManagementController::class, 'cartList'])->name('student-cart-list');

  Route::post('student/update-cart', [CartManagementController::class, 'updateCartQuantity'])->name('updateCartQuantity');
  Route::post('student/apply-coupon', [CartManagementController::class, 'applyCoupon'])->name('applyCoupon');
  Route::post('student/remove-coupon', [CartManagementController::class, 'removeCoupon'])->name('removeCoupon');
  Route::delete('student/cart-delete/{id}', [CartManagementController::class, 'cartDelete'])->name('cartDelete');
  Route::get('student/cart-count', [CartManagementController::class, 'cartCount'])->name('cartCount');
  // Route::get('student/checkout/{type?}', [CartManagementController::class, 'checkout'])->name('checkout');
    Route::get('student/checkout/{type?}/{cart_id?}', [CartManagementController::class, 'checkout'])->name('checkout');
  Route::post('student/pay', [CartManagementController::class, 'pay'])->name('pay');



  Route::get('live-course-enrollment-form/{type?}/{slug?}/{user_id?}', [CourseController::class, 'liveCourseEnrollmentForm'])->name('live-course-enrollment-form');

  Route::get('student/get_edit_enrollment_form_data/{course_id?}', [DashboardController::class, 'getEditEnrollmentFormData'])->name('student-get-edit-enrollment-form-data');
   Route::post('student/submit_course_request', [DashboardController::class, 'studentRequestEnrollment'])->name('student-submit-course-request');

  Route::get('student/get-address-location', [CartManagementController::class, 'getAddressLocation'])->name('get-address-location');

  
  // Instructor Api Routes
  Route::get('instructor/dashboard', [InstructorDashboardController::class, 'instructorDashboardData'])->name('instructor-dashboard');
  Route::get('instructor/classes-schedule', [InstructorDashboardController::class, 'classScheduleApi'])->name('instructor-classes-schedule');
  Route::get('instructor/class-history', [InstructorDashboardController::class, 'classHistoryApi'])->name('instructor-class-history');
   Route::get('instructor/my-schedule' , [InstructorDashboardController::class, 'mySchedule'])->name('my-schedule-instructor');

  Route::post('instructor/update-class-status', [InstructorDashboardController::class, 'UpdateInstructorMeetingStatus'])->name('update-instructor-meeting-status');
  Route::post('instructor/update-live-class-status', [InstructorDashboardController::class, 'UpdateInstructorLiveMeetingStatus'])->name('update-instructor-live-meeting-status');


  Route::post('instructor/checkLessonStartTime', [InstructorDashboardController::class, 'instructorCheckLessonStartTime'])->name('instructor-check-lesson-time');
  Route::post('instructor/checkLessonStartTimeLiveClass', [InstructorDashboardController::class, 'instructrCheckLessonStartTimeLiveClass'])->name('instructor-check-lesson-time-liveclass');


  Route::get('instructor/meet-redirect', function (Request $request, GoogleOAuthService $googleOAuthService) {
    $user    = $request->user();            // authenticated via API token/cookie
    $authUrl = $googleOAuthService->getAuthUrlFor($user);
    return response()->json(['auth_url' => $authUrl]);
});

Route::get('instructor/check_authorization', [InstructorDashboardController::class, 'checkAuthorization'])->name('check-authorization');

  // Instructor Course Api Routes
  Route::get('instructor/my-courses', [InstructorDashboardController::class, 'myCourses'])->name('instructor-course-index');
  Route::get('instructor/my-course/{slug}/{action_type?}/{quiz_uuid?}/{answer_id?}', [InstructorDashboardController::class, 'myCourseShowApi'])->name('my-course-show-instructor');

  // Instructor Students Api Routes
  Route::get('instructor/all-students', [StudentController::class, 'allStudentsApi'])->name('instructor-all-students');

  // Instuctor Notice Board Api Routes
  Route::get('instructor/notice-board', [NoticeBoardApiController::class, 'courseNoticeIndexApi'])->name('instructor-notice-board-index');
  Route::get('instructor/notice-board-list/{course_uuid}', [NoticeBoardApiController::class, 'noticeIndexApi'])->name('noticeIndexApi');
  Route::get('instructor/notice/create/{course_uuid}', [NoticeBoardApiController::class, 'createNoticeApi'])->name('createNoticeApi');
  Route::post('instructor/notice/{course_uuid}/store', [NoticeBoardApiController::class, 'storeNoticeApi']);
  Route::get('instructor/notice/{course_uuid}/{uuid}/view', [NoticeBoardApiController::class, 'viewNoticeApi']);
  Route::get('instructor/notice/{course_uuid}/{uuid}/edit', [NoticeBoardApiController::class, 'editNoticeApi']);
  Route::post('instructor/notice/{course_uuid}/{uuid}/update', [NoticeBoardApiController::class, 'updateNoticeApi']);
  Route::delete('instructor/notice/{uuid}/delete', [NoticeBoardApiController::class, 'deleteNoticeApi']);

  Route::get('instructor/live-class/index', [LiveClassController::class, 'courseLiveClassIndexApi'])->name('instructor-live-class-index');
  Route::get('instructor/live-class-list/{course_uuid}', [LiveClassController::class, 'liveClassIndexApi'])->name('index');
  Route::get('instructor/live-class/create/{course_uuid}', [LiveClassController::class, 'createLiveClassApi'])->name('createLiveClassApi');
  Route::post('instructor/live-class/store/{course_uuid}', [LiveClassController::class, 'storeLiveClassApi'])->name('storeLiveClassApi');
  Route::delete('instructor/live-class/{uuid}/delete', [LiveClassController::class, 'destroy'])->name('deleteLiveClassApi');

  // Instructor Discussion Api Routes
  Route::get('instructor/discussion/index', [InstructorDashboardController::class, 'discussionIndex'])->name('instructor-discussion-index');
  Route::get('instructor/discussion/course-discussion-list/{course_id}', [InstructorDashboardController::class, 'courseDiscussionListApi'])->name('instructor-course-discussion-list');
  Route::post('instructor/discussion-reply/{discussion_id}', [InstructorDashboardController::class, 'instructorCourseDiscussionReplyApi'])->name('instructor-course-discussion-reply');

  // Profile and Location Api
  Route::get('instructor/profile', [InstructorProfileController::class, 'profileApi'])->name('instructor-profile');
  Route::post('instructor/profile/update/{uuid}', [InstructorProfileController::class, 'saveProfileApi'])->name('instructor-profile-update');
  Route::get('instructor/address', [InstructorProfileController::class, 'getAddress']);
  Route::post('instructor/address', [InstructorProfileController::class, 'updateAddress']);
  Route::get('instructor/states/{country_id}', [InstructorProfileController::class, 'getStateByCountry']);
  Route::get('instructor/cities/{state_id}', [InstructorProfileController::class, 'getCityByState']);


Route::post('instructor/daily_attendance_form', [InstructorDashboardController::class, 'dailyAttendanceForm'])->name('daily-attendance-form');
Route::get('instructor/get-students', [StudentController::class, 'getStudentsApi'])->name('instructor-get-students');
Route::get('instructor/get-tqc-lessons', [InstructorDashboardController::class, 'getTQCLessons'])->name('instructor-get-tqc-lessons');
Route::get('instructor/get-nq-lessons', [InstructorDashboardController::class, 'getNQLessons'])->name('instructor-get-nq-lessons');
Route::get('instructor/get-uqc-lessons', [InstructorDashboardController::class, 'getUQCLessons'])->name('instructor-get-uqc-lessons');
Route::get('instructor/get-hifz-lessons', [InstructorDashboardController::class, 'getHifzLessons'])->name('instructor-get-hifz-lessons');




Route::get('coordinator/teacher_attendance_report', [InstructorDashboardController::class, 'getTeacherAttendanceReport'])->name('coordinator-teacher-attendance-report');



});

use App\Http\Controllers\Api\VaultAdmin\CourseController as VaultAdminCourseController;
use App\Http\Controllers\Api\VaultAdmin\DashboardController as VaultAdminDashboardController;
use App\Http\Controllers\Api\VaultAdmin\LessonController as VaultAdminLessonController;
use App\Http\Controllers\Api\VaultAdmin\LicenseController as VaultAdminLicenseController;
use App\Http\Controllers\Api\VaultAdmin\OfflinePackController as VaultAdminOfflinePackController;
use App\Http\Controllers\Api\VaultAdmin\PaymentController as VaultAdminPaymentController;
use App\Http\Controllers\Api\VaultAdmin\PaymentLinkController as VaultAdminPaymentLinkController;
use App\Http\Controllers\Api\VaultAdmin\ResourceController as VaultAdminResourceController;
use App\Http\Controllers\Api\VaultAdmin\SubscriptionController as VaultAdminSubscriptionController;
use App\Http\Controllers\Api\VaultAdmin\VaultUserController as VaultAdminVaultUserController;
use App\Http\Controllers\Api\VaultPublicPaymentController;

Route::get('vault-pay/download/{downloadToken}', [VaultPublicPaymentController::class, 'download']);
Route::get('vault-pay/{token}', [VaultPublicPaymentController::class, 'show']);
Route::get('vault-pay/{token}/status', [VaultPublicPaymentController::class, 'status']);
Route::post('vault-pay/{token}/checkout', [VaultPublicPaymentController::class, 'checkout']);
Route::post('vault-pay/{token}/meezan-complete', [VaultPublicPaymentController::class, 'meezanComplete']);
Route::post('checkout_faysal_ipn_vault/{link_id}', [VaultPublicPaymentController::class, 'faysalIpn'])
    ->name('checkout_faysal_ipn_vault');

Route::prefix('vault-admin')->middleware(['auth:sanctum', 'vault-admin'])->group(function () {
    Route::get('dashboard', [VaultAdminDashboardController::class, 'index']);

    Route::get('vault-users/{id}/billing', [VaultAdminVaultUserController::class, 'billing']);
    Route::get('vault-users', [VaultAdminVaultUserController::class, 'index']);
    Route::post('vault-users', [VaultAdminVaultUserController::class, 'store']);
    Route::get('vault-users/{id}', [VaultAdminVaultUserController::class, 'show']);
    Route::put('vault-users/{id}', [VaultAdminVaultUserController::class, 'update']);
    Route::delete('vault-users/{id}', [VaultAdminVaultUserController::class, 'destroy']);

    Route::get('licenses/{id}/billing', [VaultAdminLicenseController::class, 'billing']);
    Route::get('licenses', [VaultAdminLicenseController::class, 'index']);
    Route::post('licenses', [VaultAdminLicenseController::class, 'store']);
    Route::get('licenses/{id}', [VaultAdminLicenseController::class, 'show']);
    Route::put('licenses/{id}', [VaultAdminLicenseController::class, 'update']);
    Route::patch('licenses/{id}/status', [VaultAdminLicenseController::class, 'updateStatus']);
    Route::delete('licenses/{id}', [VaultAdminLicenseController::class, 'destroy']);
    Route::get('licenses/{id}/usage', [VaultAdminLicenseController::class, 'usage']);
    Route::get('licenses/{id}/offline-packs', [VaultAdminOfflinePackController::class, 'index']);
    Route::post('licenses/{id}/offline-packs', [VaultAdminOfflinePackController::class, 'store']);
    Route::get('licenses/{id}/offline-packs/{packId}/download', [VaultAdminOfflinePackController::class, 'download']);
    Route::delete('licenses/{id}/offline-packs/{packId}', [VaultAdminOfflinePackController::class, 'destroy']);

    Route::get('licenses/{id}/payment-links', [VaultAdminPaymentLinkController::class, 'index']);
    Route::post('licenses/{id}/payment-links', [VaultAdminPaymentLinkController::class, 'store']);
    Route::post('payment-links/{id}/revoke', [VaultAdminPaymentLinkController::class, 'revoke']);

    Route::get('subscriptions', [VaultAdminSubscriptionController::class, 'index']);
    Route::post('subscriptions', [VaultAdminSubscriptionController::class, 'store']);
    Route::get('subscriptions/{id}', [VaultAdminSubscriptionController::class, 'show']);
    Route::put('subscriptions/{id}', [VaultAdminSubscriptionController::class, 'update']);
    Route::patch('subscriptions/{id}/status', [VaultAdminSubscriptionController::class, 'updateStatus']);
    Route::delete('subscriptions/{id}', [VaultAdminSubscriptionController::class, 'destroy']);

    Route::get('payments', [VaultAdminPaymentController::class, 'index']);
    Route::post('payments', [VaultAdminPaymentController::class, 'store']);
    Route::get('payments/{id}', [VaultAdminPaymentController::class, 'show']);
    // POST accepted for multipart receipt updates (PHP cannot parse multipart on real PUT).
    Route::match(['put', 'post'], 'payments/{id}', [VaultAdminPaymentController::class, 'update']);
    Route::patch('payments/{id}/status', [VaultAdminPaymentController::class, 'updateStatus']);
    Route::delete('payments/{id}', [VaultAdminPaymentController::class, 'destroy']);
    Route::get('payments/{id}/receipt', [VaultAdminPaymentController::class, 'receipt']);

    Route::get('courses/{id}/curriculum', [VaultAdminLessonController::class, 'curriculum']);
    Route::patch('courses/{id}/lessons/reorder', [VaultAdminLessonController::class, 'reorderLessons']);
    Route::post('courses/{id}/lessons', [VaultAdminLessonController::class, 'storeLesson']);
    Route::put('courses/{id}/lessons/{lessonId}', [VaultAdminLessonController::class, 'updateLesson']);
    Route::delete('courses/{id}/lessons/{lessonId}', [VaultAdminLessonController::class, 'destroyLesson']);
    Route::patch('courses/{id}/lessons/{lessonId}/lectures/reorder', [VaultAdminLessonController::class, 'reorderLectures']);
    Route::post('courses/{id}/lessons/{lessonId}/lectures', [VaultAdminLessonController::class, 'storeLecture']);
    Route::put('courses/{id}/lessons/{lessonId}/lectures/{lectureId}', [VaultAdminLessonController::class, 'updateLecture']);
    Route::delete('courses/{id}/lessons/{lessonId}/lectures/{lectureId}', [VaultAdminLessonController::class, 'destroyLecture']);
    Route::get('courses', [VaultAdminCourseController::class, 'index']);
    Route::post('courses', [VaultAdminCourseController::class, 'store']);
    Route::get('courses/{id}', [VaultAdminCourseController::class, 'show']);
    Route::post('courses/{id}/manifest/rebuild', [VaultAdminCourseController::class, 'rebuildManifest']);
    // POST accepted for multipart updates (PHP cannot parse multipart on real PUT).
    Route::match(['put', 'post'], 'courses/{id}', [VaultAdminCourseController::class, 'update']);
    Route::delete('courses/{id}', [VaultAdminCourseController::class, 'destroy']);
    Route::get('courses/{id}/resources', [VaultAdminCourseController::class, 'resources']);
    Route::delete('courses/{id}/resources/{resourceId}', [VaultAdminCourseController::class, 'detachResource']);

    Route::get('resource-categories', [VaultAdminResourceController::class, 'categories']);
    Route::get('resources', [VaultAdminResourceController::class, 'index']);
    Route::post('resources', [VaultAdminResourceController::class, 'store']);
    Route::get('resources/{id}/preview', [VaultAdminResourceController::class, 'preview']);
    Route::post('resources/{id}/retry-vault-download', [VaultAdminResourceController::class, 'retryVaultDownload']);
    Route::get('resources/{id}', [VaultAdminResourceController::class, 'show']);
    // POST accepted for multipart updates (PHP cannot parse multipart on real PUT).
    Route::match(['put', 'post'], 'resources/{id}', [VaultAdminResourceController::class, 'update']);
    Route::delete('resources/{id}', [VaultAdminResourceController::class, 'destroy']);
});

<?php

use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\BundleCourseController;
use App\Http\Controllers\Frontend\ConsultationController;
use App\Http\Controllers\Instructor\ConsultationController as InstructorConsultationController;
use App\Http\Controllers\Frontend\CourseController;
use App\Http\Controllers\Frontend\ForumController;
use App\Http\Controllers\Frontend\MainIndexController;
use App\Http\Controllers\Frontend\RegistrationController;
use App\Http\Controllers\Frontend\SupportTicketController;
use App\Http\Controllers\Instructor\LiveClassController;
use App\Http\Controllers\Instructor\SaasController;
use App\Http\Controllers\Student\MyCourseController;
use App\Http\Controllers\Student\SubscriptionController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'guest'], function () {
    Route::get('sign-up', [RegistrationController::class, 'signUp'])->name('sign-up');
    Route::get('sign-up-as-instructor', [RegistrationController::class, 'signUpAsInstructor'])->name('sign-up-as-instructor');
    Route::get('sign-up-as-student', [RegistrationController::class, 'signUpAsStudent'])->name('sign-up-as-student');
    Route::get('sign-up-as-student/{type?}/{slug?}', [RegistrationController::class, 'signUpAsStudent'])->name('sign-up-as-student');
    Route::get('sign-up-as-student-modal/{type?}/{slug?}', [RegistrationController::class, 'signUpAsStudent'])->name('sign-up-as-student-modal');
    Route::get('sign-up-as-student-backup', [RegistrationController::class, 'signUpAsStudentBackUp'])->name('sign-up-as-student-backup');
    Route::get('sign-up-as-student-live/{type?}/{slug?}', [RegistrationController::class, 'signUpAsStudentLive'])->name('sign-up-as-student-live');


    



    Route::post('sign-up-as-student-home', [RegistrationController::class, 'signUpAsStudentHome'])->name('sign-up-as-student-home');


    Route::post('register-as-student', [RegistrationController::class, 'storeSignUpAsStudent'])->name('register-as-student');

    Route::post('store-sign-up', [RegistrationController::class, 'storeSignUp'])->name('store.sign-up');
    Route::post('check-student-email', [RegistrationController::class, 'checkStudentEmail'])->name('check-student-email');
    Route::post('check-student-phone', [RegistrationController::class, 'checkStudentPhone'])->name('check-student-phone');
    Route::post('check-student-email-update', [RegistrationController::class, 'checkStudentEmailUpdate'])->name('check-student-email-update');
    Route::post('check-student-phone-update', [RegistrationController::class, 'checkStudentPhoneUpdate'])->name('check-student-phone-update');

    Route::post('store-sign-up-as-instructor', [RegistrationController::class, 'storeSignUpAsInstructor'])->name('store.sign-up-as-instructor');
    
    Route::post('store-sign-up-as-student-step1', [RegistrationController::class, 'storeSignUpAsStudentStep1'])->name('store.sign-up-as-student-step1');
    Route::post('check-student-phone', [RegistrationController::class, 'checkStudentPhone'])->name('check-student-phone');
    Route::post('update-sign-up-as-student-step1', [RegistrationController::class, 'updateSignUpAsStudentStep1'])->name('update.sign-up-as-student-step1');
    Route::post('update-sign-up-as-student-step2', [RegistrationController::class, 'updateSignUpAsStudentStep2'])->name('update.sign-up-as-student-step2');
    Route::post('update-sign-up-as-student-step4', [RegistrationController::class, 'updateSignUpAsStudentStep4'])->name('update.sign-up-as-student-step4');
    Route::get('get-thank-you-page', [RegistrationController::class, 'getThankYouPage'])->name('get-thank-you-page');




});


    // Google login
    Route::get('login_with_google', [RegistrationController::class, 'redirectToGoogleLogin'])->name('google_login');
    Route::get('google_callback', [RegistrationController::class, 'handleGoogleCallback']);
    
    // Facebook login
    Route::get('login_with_facebook', [RegistrationController::class, 'redirectToFacebookLogin'])->name('facebook_login');
    Route::get('login-facebook-callback', [RegistrationController::class, 'handleFacebookCallback'])->name('login-facebook-callback');
    
    // Twitter login
    Route::get('login_with_twitter', [RegistrationController::class, 'redirectToTwitterLogin'])->name('twitter_login');
    Route::get('login-twitter-callback', [RegistrationController::class, 'handleTwitterCallback'])->name('login-twitter-callback');
    



Route::post('update-sign-up-as-student-live-step4', [RegistrationController::class, 'updateSignUpAsStudentLiveStep4'])->name('update.sign-up-as-student-live-step4');
Route::post('update-sign-up-as-student-live-step4-with-user', [RegistrationController::class, 'updateSignUpAsStudentLiveStep4WithUser'])->name('update.sign-up-as-student-live-step4-with-user');


Route::get('login', [LoginController::class, 'showLoginForm'])->name('showLoginForm');
Route::get('forget-password', [LoginController::class, 'forgetPassword'])->name('forget-password');
Route::post('forget-password', [LoginController::class, 'forgetPasswordEmail'])->name('forget-password.email');
Route::get('reset-password', [LoginController::class, 'resetPassword'])->name('reset-password');
Route::post('reset-password', [LoginController::class, 'resetPasswordCheck'])->name('reset-password.check');

Route::get('user/email/verify/{token}', [RegistrationController::class, 'emailVerification'])->name('user.email.verification');



Route::group(['middleware' => 'private.mode'], function () {

    // Home (/) is now the admin login; see web.php. Frontend homepage was here as main.index.
    // Route::get('/', [MainIndexController::class, 'index'])->name('main.index');

    Route::get('/test_function_enrollment', [MainIndexController::class, 'testFunctionEnrollment'])->name('test_function_enrollment');

    Route::get('about-us', [MainIndexController::class, 'aboutUs'])->name('about');
    Route::get('contact-us', [MainIndexController::class, 'contactUs'])->name('contact');
    Route::post('contact-store', [MainIndexController::class, 'contactUsStore'])->name('contact.store');
    Route::get('faq', [MainIndexController::class, 'faq'])->name('faq');
    Route::get('users/{user}/profile', [MainIndexController::class, 'userProfile'])->name('userProfile');
    Route::post('instructor-course-paginate/{id}', [MainIndexController::class, 'instructorCoursePaginate'])->name('instructorCoursePaginate');
    Route::post('organization-instructor-paginate/{user}', [MainIndexController::class, 'organizationInstructorPaginate'])->name('organizationInstructorPaginate');

    // Route::get('all-instructor', [MainIndexController::class, 'allInstructor'])->name('allInstructor');
    Route::get('instructor', [MainIndexController::class, 'instructor'])->name('instructor');
    Route::get('filter-instructor', [MainIndexController::class, 'filterInstructor'])->name('filter.instructor');
    Route::get('instructor-more', [MainIndexController::class, 'instructorMore'])->name('instructor_more');

    // Start:: Course
    Route::get('courses', [CourseController::class, 'allCourses'])->name('courses');
    Route::get('course-details/{slug}', [CourseController::class, 'courseDetails'])->name('course-details');


    Route::get('live-course-enrollment-form/{type?}/{slug?}/{user_id?}', [CourseController::class, 'liveCourseEnrollmentForm'])->name('live-course-enrollment-form');
    Route::post('save-live-request-enrollment', [CourseController::class, 'saveLiveRequestEnrollment'])->name('save-live-request-enrollment');

    Route::get('category/courses/{slug}', [CourseController::class, 'categoryCourses'])->name('category-courses');
    Route::get('subcategory/courses/{slug}', [CourseController::class, 'subCategoryCourses'])->name('subcategory-courses');

    Route::get('get-sub-category-courses/fetch-data', [CourseController::class, 'paginationFetchData'])->name('course.fetch-data');
    Route::get('get-filter-courses', [CourseController::class, 'getFilterCourse'])->name('getFilterCourse');
    Route::post('review-paginate/{courseId}', [CourseController::class, 'reviewPaginate'])->name('frontend.reviewPaginate');

    Route::get('search-course-list', [CourseController::class, 'searchCourseList'])->name('search-course.list');
    // End:: Course

    //Start:: Bundle
    Route::get('bundles', [BundleCourseController::class, 'allBundles'])->name('bundles');
    Route::get('bundle-details/{slug?}', [BundleCourseController::class, 'bundleDetails'])->name('bundle-details');

    Route::get('get-bundle-courses/fetch-data', [BundleCourseController::class, 'paginationFetchData'])->name('bundle-course.fetch-data');
    Route::get('get-filter-bundle-courses', [BundleCourseController::class, 'getFilterBundleCourse'])->name('getFilterBundleCourse');
    //End:: Bundle

    // Start:: Blog & Comment
    Route::get('blogs', [BlogController::class, 'blogAll'])->name('blogs');
    Route::get('blog-details/{slug}', [BlogController::class, 'blogDetails'])->name('blog-details');

    Route::get('category-blogs/{slug}', [BlogController::class, 'categoryBlogs'])->name('categoryBlogs');

    Route::post('blog-comment', [BlogController::class, 'blogCommentStore'])->name('blog-comment.store');
    Route::post('blog-comment-reply', [BlogController::class, 'blogCommentReplyStore'])->name('blog-comment-reply.store');

    Route::get('search-blog-list', [BlogController::class, 'searchBlogList'])->name('search-blog.list');
    // End:: Blog & Comment


    Route::get('terms-conditions', [MainIndexController::class, 'termConditions'])->name('terms-conditions')->withoutMiddleware('private.mode');
    Route::get('privacy-policy', [MainIndexController::class, 'privacyPolicy'])->name('privacy-policy')->withoutMiddleware('private.mode');
    Route::get('cookie-policy', [MainIndexController::class, 'cookiePolicy'])->name('cookie-policy')->withoutMiddleware('private.mode');
    Route::get('refund-policy', [MainIndexController::class, 'refundPolicy'])->name('refund-policy')->withoutMiddleware('private.mode');

    Route::get('support-ticket-faq', [SupportTicketController::class, 'supportTicketFAQ'])->name('support-ticket-faq');

    Route::get('student-join-bbb-meeting/{live_class_id}', [MyCourseController::class, 'bigBlueButtonJoinMeeting'])->name('student.join-bbb-meeting');
    Route::get('instructor-join-bbb-meeting/{live_class_id}', [LiveClassController::class, 'bigBlueButtonJoinMeeting'])->name('instructor.join-bbb-meeting');
    Route::get('join-jitsi-meeting/{live_class_uuid}', [LiveClassController::class, 'jitsiJoinMeeting'])->name('join-jitsi-meeting');

    // Consultation Online Meet
    Route::get('student-consultation-join-bbb-meeting/{live_class_id}', [InstructorConsultationController::class, 'studentBigBlueButtonJoinMeeting'])->name('student.consultation.join-bbb-meeting');
    Route::get('instructor-consultation-join-bbb-meeting/{live_class_id}', [InstructorConsultationController::class, 'instructorBigBlueButtonJoinMeeting'])->name('instructor.consultation.join-bbb-meeting');
    Route::get('consultation-join-jitsi-meeting/{uuid}', [InstructorConsultationController::class, 'jitsiJoinMeeting'])->name('consultation.join-jitsi-meeting');
    // Consultation Online Meet

    Route::get('subscription-list', [SubscriptionController::class, 'subscriptionList'])->name('subscription_panel');
    Route::get('saas-list', [SaasController::class, 'saasList'])->name('saas_panel');

    Route::get('migrate', function () {
        Artisan::call('migrate');
        return redirect()->back();
    });

    Route::get('get-instructor-booking-times', [ConsultationController::class, 'getInstructorBookingTime'])->name('getInstructorBookingTime');
    Route::get('consultation-instructor-list', [ConsultationController::class, 'consultationInstructorList'])->name('consultationInstructorList');

    Route::get('get-consultation-instructor/fetch-data', [ConsultationController::class, 'paginationFetchData'])->name('consultation-instructor.fetch-data');
    Route::get('get-filter-consultation-instructor', [ConsultationController::class, 'getFilterConsultationInstructor'])->name('getFilterConsultationInstructor');
    Route::get('get-off-days/{user_id}', [ConsultationController::class, 'getOffDays'])->name('getOffDays');

    Route::group(['prefix' => 'forum', 'as' => 'forum.'], function () {
        Route::get('/', [ForumController::class, 'index'])->name('index');
        Route::get('ask-a-question', [ForumController::class, 'askQuestion'])->name('askQuestion');
        Route::post('ask-a-question-store', [ForumController::class, 'questionStore'])->name('question.store');
        Route::get('forum-post-details/{uuid}', [ForumController::class, 'forumPostDetails'])->name('forumPostDetails');
        Route::post('forum-post-comment-store', [ForumController::class, 'forumPostCommentStore'])->name('forumPostComment.store');
        Route::post('forum-post-comment-reply-store', [ForumController::class, 'forumPostCommentReplyStore'])->name('forumPostCommentReply.store');
        Route::get('render-forum-category-posts', [ForumController::class, 'renderForumCategoryPosts'])->name('renderForumCategoryPosts');
        Route::get('forum-category-posts/{uuid}', [ForumController::class, 'forumCategoryPosts'])->name('forumCategoryPosts');
        Route::get('forum-leaderboard', [ForumController::class, 'forumLeaderboard'])->name('forumLeaderboard');
        Route::get('search-forum-list', [ForumController::class, 'searchForumList'])->name('search-forum.list');
    });

    Route::get('get-user-info', [MainIndexController::class, 'getUserProfile'])->name('get-user-info');
});

Route::get('page/{slug?}', [PageController::class, 'pageShow'])->name('page');




// Donation dummy route

Route::get('donation', function () {
    return view('frontend.donation.index');
})->name('donation');

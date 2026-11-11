<?php

use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CertificateVerifyController;
use App\Http\Controllers\Common\WalletRechargeController;
use App\Http\Controllers\Frontend\CourseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\InstallerController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\Instructor\GmeetSettingController;
use App\Http\Controllers\Student\SubscriptionController as StudentSubscriptionController;
use App\Http\Controllers\VersionUpdateController;
use App\Http\Controllers\ScheduleJobController;
use App\Models\Language;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use App\Services\GoogleOAuthService;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Home = admin login (must be first so it overrides frontend's /)
Route::get('/', [LoginController::class, 'showAdminLoginForm'])->name('home')->middleware('version.update');

// Frontend home URL (redirects to FRONTEND_URL from .env); used by admin "Visit Site" and route('main.index')
Route::get('site', function () {
    return redirect()->away(rtrim(config('services.frontend_url'), '/'));
})->name('main.index');

Route::get('/get-course-details/{slug}', [CourseController::class, 'getCourseDetails'])->name('get-course-details');

Route::get('check-timezones-data', [HomeController::class, 'checkTimeZones'])->name('check_timezones_data');


Route::get('check-lesson-test', [HomeController::class, 'checkLessons'])->name('check_lesson_test');


Route::get('test_email', [StudentController::class, 'TestEmail'])->name('test_email');


Route::get('update-order-items', [StudentController::class, 'updateOrderItems'])->name('update-order-items');



Route::get('update-class-statuses', [ScheduleJobController::class, 'check_and_update_class_statuses'])->name('check_and_update_class_statuses');

// Disabled: duplicate monthly fee generation — use `php artisan process-trial-enrollments` instead.
// Route::get('process_trial_enrollments', [ScheduleJobController::class, 'processTrialEnrollments'])->name('process_trial_enrollments');


Route::get('send_reminder_email_enrollments', [ScheduleJobController::class, 'sendReminderEmailEnrollments'])->name('send_reminder_email_enrollments');


Route::get('update-invoice-email-templates', [ScheduleJobController::class, 'updateInvoiceEmailTemplates'])
    ->name('update.email.templates');



Route::get('delete-job-table', [ScheduleJobController::class, 'deleteAllJobs'])
    ->name('delete.job.table');


Route::get('download_challan_pdf/{cart_id?}', [ScheduleJobController::class, 'downloadPDFChallan'])
    ->name('download.challan.pdf');



Route::get('regenerate_donation_pdf_challans', [ScheduleJobController::class, 'regenerateDonationPDFChallans'])
    ->name('regenerate_donation_pdf_challans');



Route::get('regenerate_manual_pdf_challans', [ScheduleJobController::class, 'regenerateManualPDFChallans'])
    ->name('regenerate_manual_pdf_challans');


Route::get('regenerate_overdue_pdf_challans', [ScheduleJobController::class, 'regenerateOverDuePDFChallans'])
    ->name('regenerate_overdue_pdf_challans');


Route::get('seed-permissions', [ScheduleJobController::class, 'seedPermissions'])->name('seed.permissions');



// Route::get('test_payment_email/{payment_id?}/{payment_order_id?}', [DashboardController::class, 'sendPaymentEmail'])
//     ->name('test.payment.email');



Route::get('test_payment_email/{payment_id?}/{payment_order_id?}', [DashboardController::class, 'sendTestPaymentEmail'])
    ->name('test.payment.email');


Route::get('delete-cart-management-website-table', [ScheduleJobController::class, 'deleteAllCartManagementWebsite'])
    ->name('delete-cart-management-website-table');



Route::get('delete-donation-order-items-table', [ScheduleJobController::class, 'deleteAllDonationOrderItem'])
    ->name('delete-donation-order-items-table');



    
Route::get('delete-donation-orders-table', [ScheduleJobController::class, 'deleteAllDonationOrder'])
    ->name('delete-donation-orders-table');




Route::get('/meet-redirect', function (GoogleOAuthService $googleOAuthService) {
    return $googleOAuthService->redirectToGoogle();
})->name('meet_redirect');

// Route::get('/meet-callback', function (Request $request, GoogleOAuthService $googleOAuthService) {
//     if ($request->has('code')) {
//         $googleOAuthService->handleCallback($request->input('code'));
//         return redirect()->to('https://portal.cefonlineacademy.com/tutor')->with('success', 'Google Meet connected successfully.');
//         // return redirect()->route('instructor.dashboard')->with('success', 'Google Meet connected successfully.');
//         // return redirect()->route('classes.create_individual')->with('success', 'Google Meet connected successfully.');
//     }

//      return redirect()->to('https://portal.cefonlineacademy.com/tutor')->with('success', 'Google Meet connected successfully.');
//     // return redirect()->route('instructor.dashboard')->with('success', 'Google Meet connected successfully.');
//     // return redirect()->route('classes.create_individual')->with('error', 'Google Meet authentication failed.');
// });




Route::get('/meet-callback', function (Request $request, GoogleOAuthService $googleOAuthService) {

    if ($request->filled('code') && $request->filled('state')) {
        try {
            $googleOAuthService->handleCallback(
                $request->input('code'),
                $request->input('state')
            );
            $status = '1';
        } catch (\Exception $e) {
            \Log::error('Google callback error: '.$e->getMessage());
            $status = '0';
        }
    } else {
        $status = '0';
    }

    return redirect()->to('https://portal.cefonlineacademy.com/tutor')->with('success', 'Google Meet connected successfully.');
})->name('meet_callback');




Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('registration');
Route::get('thank-you', [RegisterController::class, 'showThankYouPage'])->name('thankyou');


Route::group(['middleware' => ['version.update']], function () {

    Route::get('/local/{ln}', function ($ln) {
        $language = Language::where('iso_code', $ln)->first();
        if (!$language){
            $language = Language::find(1);
            if ($language){
                $ln = $language->iso_code;
                session(['local' => $ln]);
                App::setLocale(session()->get('local'));
            }
        }

        session(['local' => $ln]);
        App::setLocale(session()->get('local'));
        return redirect()->back();
    });

    Auth::routes(['register' => false]);
    Route::get('login/{id?}/{slug?}', [LoginController::class ,'showLoginForm'])->name('login');

    Route::get('manager', [LoginController::class ,'showAdminLoginForm'])->name('manager');
     Route::get('student-login/{id?}/{slug?}', [LoginController::class ,'showStudentLoginForm'])->name('student-login');

    Route::get('notification-url/{uuid}', [InstallerController::class, 'notificationUrl'])->name('notification.url');
    Route::post('login-modal', [LoginController::class ,'loginWithModal'])->name('login-modal');
    Route::post('mark-all-as-read', [InstallerController::class, 'markAllAsReadNotification'])->name('notification.all-read');

    Route::group(['middleware' => ['auth']], function () {
        Route::get('/logout', [LoginController::class, 'logout']);
        Route::get('/home', [HomeController::class, 'index'])->name('home');
    });
});

Route::get('version-update', [VersionUpdateController::class, 'versionUpdate'])->name('version-update');
Route::post('process-update', [VersionUpdateController::class, 'processUpdate'])->name('process-update');



Route::get('run-migrations',function () {
    $exitCode = 
    Artisan::call('migrate', [
                    '--force' => true
    ]);
    print_r('Migration Command Executed Successfully');
    print_r('<br>');
    return $exitCode;
})->name('run-migrations');



Route::get('/secure-checkout/{token}', [
    DashboardController::class, 'handleSecureCheckout'
])->name('secure.checkout');



Route::get('/get_latest_faysal_cart', [
    DashboardController::class, 'getLatestFaysalCart'
])->name('get_latest_faysal_cart');




Route::get('/get_latest_faysal_order', [
    DashboardController::class, 'getLatestFaysalOrder'
])->name('get_latest_faysal_order');



Route::get('/get_faysal_setting', [
    DashboardController::class, 'getFaysalSetting'
])->name('get_faysal_setting');




Route::get('/calculate_hash', [
    DashboardController::class, 'calculateHash'
])->name('calculate_hash');

Route::get('run-migrations2',function () {
    $exitCode = 
    Artisan::call('migrate');
    print_r('Migration Command Executed Successfully2');
    print_r('<br>');
    return $exitCode;
})->name('run-migrations2');




Route::post('checkout_faysal_ipn/{user_id?}/{payment_type?}/{cart_id?}', [DashboardController::class, 'checkoutFaysalIPN'])->name('checkout_faysal_ipn');



Route::get('/linkstorage', function () {
    Artisan::call('storage:link');
});


Route::get('/linkstorage', function () {
    Artisan::call('storage:link');
});



Route::get('/clear-cache', function () {
    Artisan::call('cache:clear-all');

    return 'DONE CLEARING CONFIG AND CACHE';
})->middleware(['auth', 'admin'])->name('clear-cache');



Route::match(array('GET','POST'), 'verify-certificate', [CertificateVerifyController::class, 'verifyCertificate'])->name('verify_certificate');
Route::match(array('GET','POST'),'/payment-notify/{id}', [PaymentApiController::class, 'paymentNotifier'])->name('paymentNotify');
Route::match(array('GET','POST'),'/payment-notify-subscription/{id}', [PaymentApiController::class, 'paymentSubscriptionNotifier'])->name('paymentNotify.subscription');
Route::match(array('GET','POST'),'/payment-notify-wallet-recharge/{id}', [PaymentApiController::class, 'paymentWalletRechargeNotifier'])->name('paymentNotify.wallet_recharge');

Route::match(array('GET','POST'),'payment-cancel/{id}', [PaymentApiController::class, 'paymentCancel'])->name('paymentCancel');

Route::get('subscription/thank-you', [StudentSubscriptionController::class, 'thankYou'])->name('subscription.thank-you');
Route::get('wallet-recharge/thank-you', [WalletRechargeController::class, 'thankYou'])->name('wallet_recharge.thank-you');
Route::get('instructor/gmeet-callback', [GmeetSettingController::class, 'gMeetCallback'])->name('instructor.gmeet_callback');







//Academic 
Route::get('academic-coordinator/UpdateCoordinatorMeetingStatusIndividual/{meeting_id?}/{lesson_id?}', [CoordinatorController::class, 'UpdateCoordinatorMeetingStatusIndividual'])->name('bbb-meeting.index');
Route::get('academic-coordinator/UpdateCoordinatorMeetingStatusGroup/{group_id?}/{meeting_id?}/{lesson_id?}', [CoordinatorController::class, 'UpdateCoordinatorMeetingStatusGroup'])->name('bbb-group-meeting.index');

Route::get('academic-coordinator/dashboard', [CoordinatorController::class, 'dashboard'])->name('coordinator.dashboard');
Route::get('academic-coordinator/home', [CoordinatorController::class, 'home'])->name('coordinator.home');

Route::get('academic-coordinator/latestBlogs', [CoordinatorController::class, 'latestBlogs'])->name('latest-blogs-coordinator');
Route::get('academic-coordinator/monthlyClasses', [CoordinatorController::class, 'monthlyClasses'])->name('monthly-classes-coordinator');
Route::get('academic-coordinator/TodayLiveClass', [CoordinatorController::class, 'TodayLiveClass'])->name('today-live-class-coordinator');
Route::get('academic-coordinator/class-schedule', [CoordinatorController::class, 'classSchedule'])->name('class-schedule.index');
Route::get('academic-coordinator/class-history', [CoordinatorController::class, 'classHistory'])->name('class-history.index');
Route::get('academic-coordinator/NewZoomMeeting/{meeting_id?}/{user_type?}', [CoordinatorController::class, 'createNewZoomMeetingCoordinator'])->name('zoom-meeting.index');
Route::get('academic-coordinator/NewZoomGroupMeeting/{meeting_id?}/{user_type?}/{group_id?}', [CoordinatorController::class, 'createNewZoomGroupMeetingCoordinator'])->name('zoom-group-meeting.index');
Route::post('academic-coordinator/checkLessonStartTime', [CoordinatorController::class, 'CheckLessonStartTimeCoordinator'])->name('check-lesson-time-coordinator');
Route::get('academic-coordinator/GetCurrentTimeZoom/{meeting_id?}', [CoordinatorController::class, 'GetCurrentTimeZoomCoordinator'])->name('get-current-time-zoom-coordinator');
Route::get('academic-coordinator/UpdateUrlZoom/{meeting_id?}/{current_time?}', [CoordinatorController::class, 'UpdateUrlZoomCoordinator'])->name('update-url-zoom-coordinator');

Route::get('academic-coordinator/UpdateJoinedStatus/{meeting_id?}/{group_id?}', [CoordinatorController::class, 'UpdateJoinedStatusZoomCoordinator'])->name('update-joined-status-zoom-coordinator');


Route::get('academic-coordinator/UpdateUrlGroupZoom/{meeting_id?}/{group_id?}/{current_time?}', [CoordinatorController::class, 'UpdateUrlGroupZoomCoordinator'])->name('update-url-group-zoom-coordinator');
Route::get('CheckModeratorLeftZoom/{meeting_id?}', [CoordinatorController::class, 'CheckModeratorLeftZoomCoordinator'])->name('check-moderator-coordinator');
Route::get('UpdateMeetingSignature/{meeting_id?}/{group_id?}', [CoordinatorController::class, 'UpdateMeetingSignature'])->name('update-meeting-signature');
Route::get('CheckModeratorLeftGroupZoom/{meeting_id?}', [CoordinatorController::class, 'CheckModeratorLeftGroupZoomCoordinator'])->name('check-moderator-group-coordinator');
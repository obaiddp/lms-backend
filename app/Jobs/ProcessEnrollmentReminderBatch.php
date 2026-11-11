<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\CartManagement;
use App\Models\User;
use App\Models\Course;
use Carbon\Carbon;
use Exception;

class ProcessEnrollmentReminderBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1200; // 20 minutes - adjust as needed
    public $tries = 3;

    protected $ids;

    /**
     * Create a new job instance.
     *
     * @param array $ids
     */
    public function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Log::info('[ScheduleJob] ProcessEnrollmentReminderBatch started for ' . count($this->ids) . ' items at ' . now());

        foreach ($this->ids as $key => $id) {
            try {
                $pending = CartManagement::find($id);

                if (!$pending) {
                    Log::warning("[ScheduleJob] Pending Request Enrollment #{$id} not found. Skipping.");
                    continue;
                }

                $daysPassed = Carbon::parse($pending->created_at)->diffInDays(Carbon::now());
                Log::info("[ScheduleJob] Pending Request Enrollment #{$pending->id} was created {$daysPassed} days ago.");

                if ($daysPassed < 2) {
                    Log::info("[ScheduleJob] Skipping Pending Request Enrollment #{$pending->id}, only {$daysPassed} days old.");
                    continue;
                }

                $getUser = User::find($pending->user_id);
                if (!$getUser) {
                    Log::warning("[ScheduleJob] User not found for pending enrollment #{$pending->id}. Skipping.");
                    continue;
                }

                $getCourse = Course::find($pending->course_id);
                if (!$getCourse) {
                    Log::warning("[ScheduleJob] Course not found for pending enrollment #{$pending->id}. Skipping.");
                    continue;
                }

                if($pending->main_price == 0.00){
                    Log::warning("[ScheduleJob] Price 0.00 for pending enrollment #{$pending->id}. Skipping.");
                    continue;
                }
                $template_name = 'pending-enrollment-request-reminder';
                $checkoutLink = route('student.checkout', ['type' => 1, 'location' => 1]);

                Log::info("[ScheduleJob] Sending email for pending #{$pending->id} to {$getUser->email} (user: {$getUser->name}).");

                // $checkResponse = sendEmailNotification([
                //     '{{USER}}' => $getUser->name,
                //     '{{COURSE}}' => $getCourse->title ?? '',
                //     '{{FEE}}' => (int)$pending->main_price,
                //     '{{CHECKOUT}}' => $checkoutLink,
                // ], 'nouman.hmnt@gmail.com', $template_name);


                $checkResponse = sendEmailNotification([
                    '{{USER}}' => $getUser->name,
                    '{{COURSE}}' => $getCourse->title ?? '',
                    '{{FEE}}' => (int)$pending->main_price,
                    '{{CHECKOUT}}' => $checkoutLink,
                ], $getUser->email, $template_name);


                if ($checkResponse) {
                    Log::info("[ScheduleJob] Pending Request Enrollment #{$pending->id} email resent successfully.");
                } else {
                    Log::info("[ScheduleJob] Pending Request Enrollment #{$pending->id} email failed to send.");
                }

                // Small pause between single sends to avoid bursts in the same job
                sleep(180); // 3 min - reduce/increase depending on SMTP tolerance

            } catch (Exception $e) {
                Log::error("[ScheduleJob] Exception while processing pending #{$id}: " . $e->getMessage());
                // Let job retry if needed; do not stop other items
            }
        }

        Log::info('[ScheduleJob] ProcessEnrollmentReminderBatch completed at ' . now());
    }

    public function failed(Exception $exception)
    {
        Log::error('[ScheduleJob] ProcessEnrollmentReminderBatch failed: ' . $exception->getMessage());
    }
}
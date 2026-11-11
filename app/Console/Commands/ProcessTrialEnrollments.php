<?php

namespace App\Console\Commands;

use App\Jobs\ProcessTrialEnrollmentBatch;
use App\Models\NotificationEmailTemplate;
use App\Services\MonthlyFeeBillingService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessTrialEnrollments extends Command
{
    protected $signature = 'process-trial-enrollments
                            {--dry-run : Preview billing actions without dispatching jobs}';

    protected $description = 'Process Trial Enrollments';

    public function handle(MonthlyFeeBillingService $billingService): int
    {
        if ($this->option('dry-run')) {
            return $this->call('monthly-fees:preview');
        }

        $batchSize = 5;
        $delayBetweenBatchesInMinutes = 15;

        Log::info('[ScheduleJob] process-trial-enrollments started at ' . now());

        $now = Carbon::now();
        $payments = $billingService->eligibleEnrollments($now)->pluck('id')->toArray();

        if (empty($payments)) {
            Log::info('[ScheduleJob] No trial enrollment requests to queue.');
            $this->info('No eligible enrollments to process.');
            return 0;
        }

        Log::info('[ScheduleJob] Found ' . count($payments) . ' deduped trial enrollment(s). Preparing batches.');
        $this->info('Dispatching ' . count($payments) . ' deduped enrollment(s) in batches of ' . $batchSize . '.');

        $checkTemplate = NotificationEmailTemplate::where(['slug' => 'payment-create-student'])
            ->where('status', 1)
            ->first();

        if ($checkTemplate == null) {
            Log::info("[ScheduleJob] process-trial-enrollments exited because template 'payment-create-student' does not exist or inactive.");
            $this->error("Email template 'payment-create-student' is missing or inactive.");
            return 1;
        }

        $chunks = array_chunk($payments, $batchSize);

        foreach ($chunks as $index => $chunk) {
            $delayMinutes = $index * $delayBetweenBatchesInMinutes;
            $delayUntil = Carbon::now()->addSeconds($delayMinutes * 60);

            ProcessTrialEnrollmentBatch::dispatch($chunk)
                ->delay($delayUntil)
                ->onQueue('default');

            Log::info(sprintf(
                '[ScheduleJob] Dispatched batch #%d (%d items) scheduled at %s (delay %d minutes).',
                $index + 1,
                count($chunk),
                $delayUntil->toDateTimeString(),
                $delayMinutes
            ));
        }

        Log::info('[ScheduleJob] All batches dispatched.');
        $this->info('All batches dispatched.');

        return 0;
    }
}

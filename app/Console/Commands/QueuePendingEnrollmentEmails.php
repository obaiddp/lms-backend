<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessEnrollmentReminderBatch;
use Carbon\Carbon;
use App\Models\CartManagement;

class QueuePendingEnrollmentEmails extends Command
{
    protected $signature = 'emails:queue-pending-enrollments';
    protected $description = 'Queue pending enrollment reminder emails in timed batches to avoid SMTP rate limits.';

    public function handle()
    {
        // Configurable
        $batchSize = 5; // pick 4 or 5 entries per batch
        $delayBetweenBatchesInMinutes = 15; // wait this many minutes between batches

        Log::info('[ScheduleJob] emails:queue-pending-enrollments started at ' . now());

        // Get pending enrollments (IDs only)
        // $pendingIds = CartManagement::where('is_donation', 0)
        //     ->where('request_enrollment', 1)
        //     ->orderBy('id', 'ASC')
        //     ->pluck('id')
        //     ->toArray();



        $pendingIds = CartManagement::where('created_by_type',2)
            ->orderBy('id', 'ASC')
            ->pluck('id')
            ->toArray();




        if (empty($pendingIds)) {
            Log::info('[ScheduleJob] No pending enrollment requests to queue.');
            return 0;
        }

        Log::info('[ScheduleJob] Found ' . count($pendingIds) . ' pending enrollment(s). Preparing batches.');

        $chunks = array_chunk($pendingIds, $batchSize);

        foreach ($chunks as $index => $chunk) {
            $delayMinutes = $index * $delayBetweenBatchesInMinutes;
           // compute explicit DateTime to avoid integer-seconds ambiguity
            $delayUntil = Carbon::now()->addSeconds($delayMinutes * 60);
            // dispatch using the DateTime
            ProcessEnrollmentReminderBatch::dispatch($chunk)
                ->delay($delayUntil)
                ->onQueue('default');

            Log::info(sprintf(
                '[ScheduleJob] Dispatched batch #%d (%d items) scheduled at %s (delay %d minutes).',
                $index + 1,
                count($chunk),
                $delayUntil->toDateTimeString(),
                $delayMinutes
            ));

            // ProcessEnrollmentReminderBatch::dispatch($chunk)
            //     ->delay(now()->addMinutes($delayMinutes));

            // Log::info(sprintf(
            //     '[ScheduleJob] Dispatched batch #%d (%d items) with %d minute(s) delay.',
            //     $index + 1,
            //     count($chunk),
            //     $delayMinutes
            // ));
            
        }

        Log::info('[ScheduleJob] All batches dispatched.');

        return 0;
    }
}
<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\InstructorGoogleAuthService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ClearExpiredInstructorGoogleTokensCommand extends Command
{
    protected $signature = 'google:clear-expired-instructor-tokens {--dry-run : List users without clearing tokens}';

    protected $description = 'Clear Google Meet OAuth tokens for instructors whose token expired or expires today';

    public function handle(InstructorGoogleAuthService $authService): int
    {
        $tz = config('app.timezone', 'Asia/Karachi');
        $cutoff = Carbon::now($tz)->endOfDay();

        $users = User::query()
            ->whereNotNull('instructor_google_access_token')
            ->where(function ($q) use ($cutoff) {
                $q->whereNull('instructor_google_token_expires_at')
                    ->orWhere('instructor_google_token_expires_at', '<=', $cutoff);
            })
            ->get();

        if ($users->isEmpty()) {
            $this->info('No expired or expiring Google Meet tokens to clear.');
            Log::info('[ScheduleJob] google:clear-expired-instructor-tokens: none due at '.now());

            return self::SUCCESS;
        }

        $dryRun = (bool) $this->option('dry-run');

        foreach ($users as $user) {
            $expires = $user->instructor_google_token_expires_at ?? 'null';
            $this->line("User #{$user->id} {$user->email} expires_at={$expires}");

            if (! $dryRun) {
                $authService->clearForUser($user);
            }
        }

        $verb = $dryRun ? 'Would clear' : 'Cleared';
        $count = $users->count();
        Log::info("[ScheduleJob] google:clear-expired-instructor-tokens {$verb} {$count} user(s) at ".now());
        $this->info("{$verb} {$count} user(s).");

        return self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Jobs\BuildCourseVaultManifestJob;
use App\Models\Course;
use Illuminate\Console\Command;

class BuildStaleVaultManifestsCommand extends Command
{
    protected $signature = 'vault:build-stale-manifests {--limit=50 : Max courses to queue per run}';

    protected $description = 'Dispatch manifest rebuild jobs for courses that need a vault payload (missing or not ready).';

    public function handle(): int
    {
        $limit = max(1, (int) $this->option('limit'));

        $courseIds = Course::query()
            ->whereHas('resources')
            ->where(function ($q) {
                $q->whereDoesntHave('vaultManifest')
                    ->orWhereRelation('vaultManifest', 'is_ready', false);
            })
            ->orderBy('id')
            ->limit($limit)
            ->pluck('id');

        foreach ($courseIds as $courseId) {
            BuildCourseVaultManifestJob::dispatch((int) $courseId);
        }

        $this->info('Queued '.$courseIds->count().' BuildCourseVaultManifestJob(s).');

        return self::SUCCESS;
    }
}

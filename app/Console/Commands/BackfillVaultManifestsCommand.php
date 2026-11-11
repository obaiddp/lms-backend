<?php

namespace App\Console\Commands;

use App\Jobs\BuildCourseVaultManifestJob;
use App\Models\Course;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillVaultManifestsCommand extends Command
{
    protected $signature = 'vault:backfill-manifests {--chunk=100 : Course IDs per dispatch loop}';

    protected $description = 'Queue BuildCourseVaultManifestJob for every course_id that appears in course_resources.';

    public function handle(): int
    {
        $chunk = max(1, (int) $this->option('chunk'));
        $total = 0;

        $courseIds = DB::table('course_resources')
            ->distinct()
            ->orderBy('course_id')
            ->pluck('course_id')
            ->filter(fn ($id) => (int) $id > 0)
            ->values();

        foreach ($courseIds->chunk($chunk) as $chunkIds) {
            foreach ($chunkIds as $cid) {
                $cid = (int) $cid;
                if ($cid <= 0 || ! Course::query()->whereKey($cid)->exists()) {
                    continue;
                }
                BuildCourseVaultManifestJob::dispatch($cid);
                $total++;
            }
        }

        $this->info("Queued {$total} manifest job(s).");

        return self::SUCCESS;
    }
}

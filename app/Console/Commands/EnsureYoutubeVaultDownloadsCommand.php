<?php

namespace App\Console\Commands;

use App\Jobs\DownloadYoutubeForVaultJob;
use App\Models\Resource;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class EnsureYoutubeVaultDownloadsCommand extends Command
{
    protected $signature = 'vault:ensure-youtube-downloads {--limit=20 : Max download jobs to dispatch per run}';

    protected $description = 'Dispatch vault YouTube download jobs for resources missing a local vault file (capped).';

    public function handle(): int
    {
        $limit = max(1, (int) $this->option('limit'));
        $disk = Storage::disk('local');
        $dispatched = 0;

        Resource::query()
            ->whereRaw('LOWER(type) = ?', ['youtube'])
            ->orderBy('id')
            ->chunkById(100, function ($chunk) use ($disk, $limit, &$dispatched) {
                foreach ($chunk as $resource) {
                    if ($dispatched >= $limit) {
                        return false;
                    }
                    $path = $resource->vault_video_path;
                    if ($path && $disk->exists($path)) {
                        continue;
                    }
                    DownloadYoutubeForVaultJob::dispatch($resource->id);
                    $dispatched++;
                }

                return true;
            });

        $this->info("Dispatched {$dispatched} DownloadYoutubeForVaultJob(s).");

        return self::SUCCESS;
    }
}

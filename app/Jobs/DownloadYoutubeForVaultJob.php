<?php

namespace App\Jobs;

use App\Models\Resource;
use App\Services\CourseVaultManifestService;
use App\Services\VaultResourceDownloadStatusService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Throwable;

class DownloadYoutubeForVaultJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 1800;

    public function __construct(
        public int $resourceId
    ) {}

    /**
     * Get YouTube watch URL from resource youtube_url_path (ID or full URL).
     */
    private function getYouTubeWatchUrl(Resource $resource): ?string
    {
        $url = trim((string) ($resource->youtube_url_path ?? ''));
        if ($url === '') {
            return null;
        }
        if (preg_match('/([a-zA-Z0-9_-]{11})(?:\?|&|$)/', $url, $m) && preg_match('/^[a-zA-Z0-9_-]{11}$/', $m[1])) {
            return 'https://www.youtube.com/watch?v='.$m[1];
        }
        if (preg_match('#(?:v=|youtu\.be/|embed/)([a-zA-Z0-9_-]{11})#', $url, $m)) {
            return 'https://www.youtube.com/watch?v='.$m[1];
        }
        if (str_starts_with($url, 'http')) {
            return $url;
        }

        return null;
    }

    public function handle(
        CourseVaultManifestService $manifestService,
        VaultResourceDownloadStatusService $statusService
    ): void {
        $resource = Resource::find($this->resourceId);
        if (! $resource || strtolower((string) $resource->type) !== 'youtube') {
            return;
        }

        $statusService->markPending($resource);
        $resource->refresh();

        $disk = Storage::disk('local');
        $dir = 'vault_videos';
        $relativePath = $dir.'/'.$resource->id.'.mp4';

        if ($resource->vault_video_path) {
            $existing = $disk->exists($resource->vault_video_path);
            if ($existing) {
                Log::info("Vault YouTube already exists for resource {$resource->id}");
                $statusService->markReady($resource, $resource->vault_video_path);
                $manifestService->markStaleAndRebuildForResource($resource->id);

                return;
            }
        }

        $watchUrl = $this->getYouTubeWatchUrl($resource);
        if (! $watchUrl) {
            $message = 'Missing or invalid YouTube URL';
            Log::warning("Vault YouTube: no watch URL for resource {$resource->id}");
            $statusService->markFailed($resource, $message);

            return;
        }

        $disk->makeDirectory($dir);
        $outTemplate = $dir.'/'.$resource->id.'.%(ext)s';
        $fullOutPath = $disk->path($outTemplate);

        $process = new Process([
            'yt-dlp',
            '-f', 'best[ext=mp4]/best',
            '--no-playlist',
            '-o', $fullOutPath,
            $watchUrl,
        ]);
        $process->setTimeout($this->timeout - 60);
        $process->run();

        if (! $process->isSuccessful()) {
            $error = trim($process->getErrorOutput()) ?: trim($process->getOutput()) ?: 'yt-dlp download failed';
            Log::error("Vault YouTube download failed for resource {$resource->id}: ".$error);
            $statusService->markFailed($resource, $error);

            return;
        }

        $basePath = $disk->path($dir.'/'.$resource->id);
        $found = null;
        foreach (['.mp4', '.webm', '.mkv', '.m4a'] as $ext) {
            $p = $basePath.$ext;
            if (file_exists($p) && filesize($p) > 0) {
                $found = $dir.'/'.$resource->id.$ext;
                break;
            }
        }

        if (! $found) {
            $message = 'Download completed but output file is missing or empty';
            Log::error("Vault YouTube file missing or empty for resource {$resource->id}");
            $statusService->markFailed($resource, $message);

            return;
        }

        $statusService->markReady($resource, $found);
        Log::info("Vault YouTube downloaded for resource {$resource->id}: {$found}");

        $manifestService->markStaleAndRebuildForResource($resource->id);
    }

    public function failed(?Throwable $exception): void
    {
        $resource = Resource::find($this->resourceId);
        if (! $resource) {
            return;
        }

        app(VaultResourceDownloadStatusService::class)->markFailed(
            $resource,
            $exception?->getMessage() ?? 'Job failed unexpectedly'
        );
    }
}

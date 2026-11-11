<?php

namespace App\Services;

use App\Models\Resource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VaultResourceDownloadStatusService
{
    public const STATUS_READY = 'ready';

    public const STATUS_PENDING = 'pending';

    public const STATUS_FAILED = 'failed';

    public const STATUS_NOT_READY = 'not_ready';

    public const STATUS_NOT_APPLICABLE = 'not_applicable';

    public const STATUS_MISSING_FILE = 'missing_file';

    /**
     * @return array<string, mixed>
     */
    public function resolve(Resource $resource, bool $syncDb = true): array
    {
        $type = strtolower(trim((string) ($resource->type ?? '')));

        $result = match ($type) {
            'youtube' => $this->resolveYoutube($resource),
            'text' => $this->baseResult(self::STATUS_NOT_APPLICABLE, true, null, $resource),
            'slide document' => $this->resolveSlide($resource),
            'video', 'pdf', 'image', 'audio' => $this->resolvePublicFile($resource),
            default => $this->resolvePublicFile($resource),
        };

        if ($syncDb && $result['vault_download_status'] === self::STATUS_READY) {
            $this->syncReadyToDatabase($resource);
        }

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    public function summarizeCourseResources(int $courseId): array
    {
        $rows = Resource::query()
            ->whereIn('id', function ($q) use ($courseId) {
                $q->select('resource_id')
                    ->from('course_resources')
                    ->where('course_id', $courseId)
                    ->whereNotNull('resource_id');
            })
            ->get();

        $summary = [
            'total' => $rows->count(),
            'ready' => 0,
            'pending' => 0,
            'failed' => 0,
            'not_ready' => 0,
            'missing_file' => 0,
            'not_applicable' => 0,
        ];

        foreach ($rows as $resource) {
            $status = $this->resolve($resource, syncDb: false)['vault_download_status'];
            if (isset($summary[$status])) {
                $summary[$status]++;
            }
        }

        return $summary;
    }

    public function markPending(Resource $resource): void
    {
        $resource->vault_download_status = self::STATUS_PENDING;
        $resource->vault_download_error = null;
        $resource->vault_download_attempted_at = now();
        $resource->save();
    }

    public function markFailed(Resource $resource, string $message): void
    {
        $resource->vault_download_status = self::STATUS_FAILED;
        $resource->vault_download_error = $this->truncateError($message);
        $resource->vault_download_attempted_at = now();
        $resource->save();
    }

    public function markReady(Resource $resource, ?string $vaultVideoPath = null): void
    {
        if ($vaultVideoPath !== null) {
            $resource->vault_video_path = $vaultVideoPath;
        }
        $resource->vault_download_status = self::STATUS_READY;
        $resource->vault_download_error = null;
        $resource->vault_download_attempted_at = now();
        $resource->save();
    }

    public function youtubeEmbedUrl(Resource $resource): ?string
    {
        $key = $this->normalizeYoutubeKey((string) ($resource->youtube_url_path ?? ''));
        if ($key === '') {
            return null;
        }

        return 'https://www.youtube.com/embed/'.$key;
    }

    public function normalizeYoutubeKey(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        if (Str::contains($value, 'youtube.com/embed/')) {
            return Str::afterLast(Str::before($value, '?'), '/');
        }
        if (Str::contains($value, 'youtube.com/watch')) {
            parse_str(parse_url($value, PHP_URL_QUERY) ?? '', $params);

            return $params['v'] ?? $value;
        }
        if (Str::contains($value, 'youtu.be/')) {
            return Str::afterLast(rtrim($value, '/'), '/');
        }
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $value)) {
            return $value;
        }

        return $value;
    }

    /**
     * @return array<string, mixed>
     */
    private function resolveYoutube(Resource $resource): array
    {
        $disk = Storage::disk('local');
        $path = $resource->vault_video_path;
        $fileExists = $path && $disk->exists($path);

        if ($fileExists) {
            return $this->baseResult(self::STATUS_READY, true, null, $resource, $path, true);
        }

        $youtubeKey = $this->normalizeYoutubeKey((string) ($resource->youtube_url_path ?? ''));
        if ($youtubeKey === '') {
            return $this->baseResult(
                self::STATUS_FAILED,
                false,
                $resource->vault_download_error ?: 'Missing YouTube URL',
                $resource,
                $path,
                false
            );
        }

        $dbStatus = $resource->vault_download_status ?: self::STATUS_NOT_READY;

        return $this->baseResult(
            $dbStatus,
            false,
            $resource->vault_download_error,
            $resource,
            $path,
            false
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function resolvePublicFile(Resource $resource): array
    {
        if (empty($resource->file_name)) {
            return $this->baseResult(self::STATUS_MISSING_FILE, false, 'No file name configured', $resource, null, false);
        }

        $exists = file_exists(public_path('uploads/course_resource/'.$resource->file_name));

        return $this->baseResult(
            $exists ? self::STATUS_READY : self::STATUS_MISSING_FILE,
            $exists,
            $exists ? null : 'File not found on server',
            $resource,
            null,
            $exists
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function resolveSlide(Resource $resource): array
    {
        if (empty($resource->file_name)) {
            return $this->baseResult(self::STATUS_MISSING_FILE, false, 'No slide folder configured', $resource, null, false);
        }

        $folder = public_path('uploads/course_resource/'.$resource->file_name);
        $zip = public_path('uploads/course_resource/'.$resource->file_name.'.zip');
        $exists = (is_dir($folder) && file_exists($folder.'/index.html')) || file_exists($zip);

        return $this->baseResult(
            $exists ? self::STATUS_READY : self::STATUS_MISSING_FILE,
            $exists,
            $exists ? null : 'Slide package not found on server',
            $resource,
            null,
            $exists
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function baseResult(
        string $status,
        bool $ready,
        ?string $error,
        Resource $resource,
        ?string $vaultVideoPath = null,
        ?bool $fileExists = null
    ): array {
        return [
            'vault_download_status' => $status,
            'vault_download_ready' => $ready,
            'vault_download_error' => $error,
            'vault_download_attempted_at' => $resource->vault_download_attempted_at?->toIso8601String(),
            'vault_video_path' => $vaultVideoPath ?? $resource->vault_video_path,
            'file_exists' => $fileExists ?? $ready,
        ];
    }

    private function syncReadyToDatabase(Resource $resource): void
    {
        if ($resource->vault_download_status === self::STATUS_READY && $resource->vault_download_error === null) {
            return;
        }

        $resource->vault_download_status = self::STATUS_READY;
        $resource->vault_download_error = null;
        $resource->save();
    }

    private function truncateError(string $message): string
    {
        $message = trim($message);

        return mb_strlen($message) > 2000 ? mb_substr($message, 0, 2000).'…' : $message;
    }
}

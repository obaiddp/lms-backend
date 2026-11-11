<?php

namespace App\Jobs;

use App\Models\Course;
use App\Models\CourseVaultManifest;
use App\Services\VaultManifestBuilder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BuildCourseVaultManifestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600;

    public function __construct(
        public int $courseId
    ) {}

    public function handle(VaultManifestBuilder $builder): void
    {
        if (! Course::query()->whereKey($this->courseId)->exists()) {
            Log::warning('BuildCourseVaultManifestJob skipped: course does not exist', [
                'course_id' => $this->courseId,
            ]);
            CourseVaultManifest::query()->where('course_id', $this->courseId)->delete();

            return;
        }

        try {
            $payload = $builder->buildCoursePayload($this->courseId);

            $existing = CourseVaultManifest::query()->where('course_id', $this->courseId)->first();
            $version = (int) ($existing?->manifest_version ?? 0) + 1;

            CourseVaultManifest::query()->updateOrCreate(
                ['course_id' => $this->courseId],
                [
                    'payload' => $payload,
                    'is_ready' => true,
                    'manifest_version' => $version,
                    'last_built_at' => now(),
                    'last_error' => null,
                ]
            );
        } catch (\Throwable $e) {
            Log::error('BuildCourseVaultManifestJob failed', [
                'course_id' => $this->courseId,
                'message' => $e->getMessage(),
            ]);

            if (Course::query()->whereKey($this->courseId)->exists()) {
                CourseVaultManifest::query()->updateOrCreate(
                    ['course_id' => $this->courseId],
                    [
                        'is_ready' => false,
                        'last_built_at' => now(),
                        'last_error' => $e->getMessage(),
                    ]
                );
            }

            throw $e;
        }
    }
}

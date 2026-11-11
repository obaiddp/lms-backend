<?php

namespace App\Services;

use App\Jobs\BuildCourseVaultManifestJob;
use App\Models\Course;
use App\Models\CourseResource;
use App\Models\CourseVaultManifest;
use Illuminate\Support\Facades\Log;

class CourseVaultManifestService
{
    public function markStaleAndRebuild(int $courseId): void
    {
        if ($courseId <= 0) {
            return;
        }

        if (! Course::query()->whereKey($courseId)->exists()) {
            Log::warning('Vault manifest: skip stale mark — course does not exist', ['course_id' => $courseId]);
            CourseVaultManifest::query()->where('course_id', $courseId)->delete();

            return;
        }

        CourseVaultManifest::query()->updateOrCreate(
            ['course_id' => $courseId],
            ['is_ready' => false]
        );

        BuildCourseVaultManifestJob::dispatch($courseId);
    }

    /**
     * @param  array<int>  $courseIds
     */
    public function markStaleAndRebuildCourseIds(array $courseIds): void
    {
        foreach (array_unique(array_filter($courseIds)) as $cid) {
            $this->markStaleAndRebuild((int) $cid);
        }
    }

    public function markStaleAndRebuildForResource(int $resourceId): void
    {
        $ids = CourseResource::query()
            ->where('resource_id', $resourceId)
            ->pluck('course_id')
            ->unique()
            ->all();

        $this->markStaleAndRebuildCourseIds($ids);
    }
}

<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Course_lesson;
use App\Models\CourseResource;
use App\Models\Resource;
use App\Models\ResourceCategory;
use Illuminate\Support\Str;

class VaultCurriculumBuilder
{
    /**
     * @return array<string, mixed>
     */
    public function build(int $courseId): array
    {
        $course = Course::query()->whereKey($courseId)->first();
        if (! $course) {
            return $this->emptyResult();
        }

        $courseType = (int) $course->course_type;

        if ($courseType === COURSE_TYPE_VAULT) {
            $sections = $this->buildLessonSections($courseId);
            $layout = 'lessons';
            if ($sections === []) {
                $sections = $this->buildCategorySections($courseId);
                $layout = 'categories';
            }
        } elseif ($courseType === COURSE_TYPE_LIVE_CLASS) {
            $sections = $this->buildCategorySections($courseId);
            $layout = 'categories';
        } else {
            $sections = $this->buildLessonSections($courseId);
            $layout = 'lessons';
        }

        if ($sections === []) {
            $sections = $this->buildFallbackSections($courseId);
        }

        $featured = $this->resolveFeatured($course, $sections);

        return [
            'course_type' => $this->courseTypeLabel($courseType),
            'content_layout' => $layout,
            'content_sections' => $sections,
            'featured_type' => $featured['featured_type'],
            'featured_resource_id' => $featured['featured_resource_id'],
            'total_sections' => count($sections),
            'total_playable_items' => $this->countPlayableItems($sections),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildLessonSections(int $courseId): array
    {
        $lessons = Course_lesson::query()
            ->where('course_id', $courseId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->with(['lectures' => fn ($q) => $q->orderBy('sort_order')->orderBy('id')])
            ->get();

        $sections = [];

        foreach ($lessons as $lesson) {
            $items = [];

            foreach ($lesson->lectures as $lecture) {
                $itemKind = $this->lectureItemKind($lecture);
                $item = [
                    'item_id' => $lecture->id,
                    'item_title' => $lecture->title,
                    'item_kind' => $itemKind,
                ];

                if ($itemKind === 'Resource' && $lecture->resource_id) {
                    $item['resource_id'] = (int) $lecture->resource_id;
                    $item['resource_type'] = $lecture->resource?->type;
                }

                $items[] = $item;
            }

            if ($items !== []) {
                $sections[] = [
                    'section_id' => $lesson->id,
                    'section_name' => $lesson->name,
                    'items' => $items,
                ];
            }
        }

        return $sections;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildCategorySections(int $courseId): array
    {
        $resourceIds = CourseResource::query()
            ->where('course_id', $courseId)
            ->whereNotNull('resource_id')
            ->pluck('resource_id')
            ->unique()
            ->values()
            ->all();

        if ($resourceIds === []) {
            return [];
        }

        $categoryIds = Resource::query()
            ->whereIn('id', $resourceIds)
            ->pluck('category_id')
            ->unique()
            ->filter()
            ->values()
            ->all();

        if ($categoryIds === []) {
            return [];
        }

        $sections = [];

        foreach (ResourceCategory::query()->whereIn('id', $categoryIds)->orderBy('id')->get() as $category) {
            $courseResources = CourseResource::query()
                ->where('course_id', $courseId)
                ->whereHas('resource', fn ($q) => $q->where('category_id', $category->id))
                ->with('resource')
                ->get()
                ->sortBy(fn ($cr) => $cr->resource?->sort_number ?? 0)
                ->values();

            $items = [];

            foreach ($courseResources as $courseResource) {
                $resource = $courseResource->resource;
                if (! $resource) {
                    continue;
                }

                $items[] = [
                    'item_id' => (int) $resource->id,
                    'item_title' => $resource->name ?? '',
                    'item_kind' => 'Resource',
                    'resource_id' => (int) $resource->id,
                    'resource_type' => $resource->type,
                ];
            }

            if ($items !== []) {
                $sections[] = [
                    'section_id' => $category->id,
                    'section_name' => $category->name,
                    'items' => $items,
                ];
            }
        }

        return $sections;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildFallbackSections(int $courseId): array
    {
        $resources = Resource::query()
            ->join('course_resources as cr', 'cr.resource_id', '=', 'resources.id')
            ->where('cr.course_id', $courseId)
            ->select('resources.*')
            ->orderBy('resources.sort_number')
            ->get();

        if ($resources->isEmpty()) {
            return [];
        }

        $items = [];

        foreach ($resources as $resource) {
            $items[] = [
                'item_id' => (int) $resource->id,
                'item_title' => $resource->name ?? '',
                'item_kind' => 'Resource',
                'resource_id' => (int) $resource->id,
                'resource_type' => $resource->type,
            ];
        }

        return [
            [
                'section_id' => 0,
                'section_name' => __('Course content'),
                'items' => $items,
            ],
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $sections
     * @return array{featured_type: int, featured_resource_id: ?int}
     */
    private function resolveFeatured(Course $course, array $sections): array
    {
        if ((int) $course->intro_video_check === 1 && getVideoFile($course->video)) {
            $resourceId = $this->findIntroVideoResourceId($course);

            return [
                'featured_type' => 1,
                'featured_resource_id' => $resourceId,
            ];
        }

        if ((int) $course->intro_video_check === 2 && $course->youtube_video_id) {
            return [
                'featured_type' => 2,
                'featured_resource_id' => $this->findYoutubeFeaturedResourceId($course, $sections),
            ];
        }

        return [
            'featured_type' => 3,
            'featured_resource_id' => null,
        ];
    }

    private function findIntroVideoResourceId(Course $course): ?int
    {
        $introPath = (string) $course->video;
        if ($introPath === '') {
            return null;
        }

        $introBasename = basename($introPath);

        $resourceIds = CourseResource::query()
            ->where('course_id', $course->id)
            ->whereNotNull('resource_id')
            ->pluck('resource_id');

        $match = Resource::query()
            ->whereIn('id', $resourceIds)
            ->where(function ($q) use ($introPath, $introBasename) {
                $q->where('file_name', $introBasename)
                    ->orWhere('file_name', 'like', '%'.$introBasename);
            })
            ->first();

        return $match ? (int) $match->id : null;
    }

    /**
     * @param  array<int, array<string, mixed>>  $sections
     */
    private function findYoutubeFeaturedResourceId(Course $course, array $sections): ?int
    {
        $courseYoutube = $this->normalizeYoutubeKey((string) $course->youtube_video_id);

        $resourceIds = CourseResource::query()
            ->where('course_id', $course->id)
            ->whereNotNull('resource_id')
            ->pluck('resource_id');

        $youtubeResources = Resource::query()
            ->whereIn('id', $resourceIds)
            ->where(function ($q) {
                $q->where('type', 'Youtube')->orWhere('type', 'youtube');
            })
            ->get();

        foreach ($youtubeResources as $resource) {
            if ($this->normalizeYoutubeKey((string) $resource->youtube_url_path) === $courseYoutube) {
                return (int) $resource->id;
            }
        }

        return $this->firstResourceIdInSections($sections, ['Youtube', 'youtube']);
    }

    /**
     * @param  array<int, array<string, mixed>>  $sections
     * @param  array<int, string>  $types
     */
    private function firstResourceIdInSections(array $sections, array $types): ?int
    {
        foreach ($sections as $section) {
            foreach ($section['items'] ?? [] as $item) {
                if (($item['item_kind'] ?? '') !== 'Resource') {
                    continue;
                }
                $resourceType = $item['resource_type'] ?? '';
                if (in_array($resourceType, $types, true)) {
                    return isset($item['resource_id']) ? (int) $item['resource_id'] : null;
                }
            }
        }

        return null;
    }

    private function normalizeYoutubeKey(string $value): string
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

        return $value;
    }

    private function lectureItemKind(object $lecture): string
    {
        if ($lecture->quiz_id != null) {
            return 'Quiz';
        }
        if ($lecture->assignment_id != null) {
            return 'Assignment';
        }
        if ($lecture->resource_id != null) {
            return 'Resource';
        }

        return $lecture->type ?? 'Other';
    }

    /**
     * @param  array<int, array<string, mixed>>  $sections
     */
    private function countPlayableItems(array $sections): int
    {
        $count = 0;

        foreach ($sections as $section) {
            foreach ($section['items'] ?? [] as $item) {
                if (($item['item_kind'] ?? '') === 'Resource' && ! empty($item['resource_id'])) {
                    $count++;
                }
            }
        }

        return $count;
    }

    private function courseTypeLabel(int $courseType): string
    {
        return match ($courseType) {
            COURSE_TYPE_GENERAL => 'general',
            COURSE_TYPE_SCORM => 'scorm',
            COURSE_TYPE_LIVE_CLASS => 'live',
            COURSE_TYPE_VAULT => 'vault',
            default => 'general',
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function emptyResult(): array
    {
        return [
            'course_type' => 'general',
            'content_layout' => 'lessons',
            'content_sections' => [],
            'featured_type' => 3,
            'featured_resource_id' => null,
            'total_sections' => 0,
            'total_playable_items' => 0,
        ];
    }
}

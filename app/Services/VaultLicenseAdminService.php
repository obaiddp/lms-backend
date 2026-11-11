<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Course_lecture;
use App\Models\Course_lesson;
use App\Models\CourseResource;
use App\Models\CourseVaultManifest;
use App\Models\License;
use App\Models\LicenseCourse;
use App\Models\LicenseDevice;
use App\Models\LicenseDeviceUsageEvent;
use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Models\VaultPayment;
use App\Models\VaultUser;
use App\Jobs\DownloadYoutubeForVaultJob;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class VaultLicenseAdminService
{
    public function __construct(
        protected VaultResourceDownloadStatusService $downloadStatusService
    ) {}

    public const LICENSE_PERMISSIONS = [
        'all_licenses',
        'add_license',
        'update_license',
        'delete_license',
    ];

    public function dashboardStats(): array
    {
        $now = Carbon::now();
        $licenses = License::query()->get(['id', 'status', 'end_date']);

        $total = $licenses->count();
        $active = $licenses->where('status', 'active')->count();
        $revoked = $licenses->where('status', 'revoked')->count();
        $expired = $licenses->filter(fn ($l) => $l->end_date && Carbon::parse($l->end_date)->lt($now))->count();

        $devices = LicenseDevice::query()->get(['id', 'vault_activated_at', 'revoked_at']);
        $deviceTotal = $devices->count();
        $activeActivations = $devices->filter(fn ($d) => $d->vault_activated_at !== null && $d->revoked_at === null)->count();
        $revokedDevices = $devices->filter(fn ($d) => $d->revoked_at !== null)->count();

        $eventsLast7 = LicenseDeviceUsageEvent::where('created_at', '>=', $now->copy()->subDays(7))->count();
        $eventsLast30 = LicenseDeviceUsageEvent::where('created_at', '>=', $now->copy()->subDays(30))->count();

        $eventsByType = LicenseDeviceUsageEvent::query()
            ->select('event', DB::raw('count(*) as total'))
            ->where('created_at', '>=', $now->copy()->subDays(30))
            ->groupBy('event')
            ->pluck('total', 'event');

        $eventsByDay = LicenseDeviceUsageEvent::query()
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', $now->copy()->subDays(14))
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $topCourses = LicenseCourse::query()
            ->select('course_id', DB::raw('count(*) as license_count'))
            ->groupBy('course_id')
            ->orderByDesc('license_count')
            ->limit(5)
            ->get()
            ->map(function ($row) {
                $course = Course::find($row->course_id);

                return [
                    'course_id' => (int) $row->course_id,
                    'course_name' => $course?->title ?? 'Unknown',
                    'license_count' => (int) $row->license_count,
                ];
            });

        $expiringSoon = License::with('vaultUser')
            ->where('status', 'active')
            ->whereBetween('end_date', [$now->toDateString(), $now->copy()->addDays(30)->toDateString()])
            ->orderBy('end_date')
            ->limit(10)
            ->get()
            ->map(fn (License $l) => $this->serializeLicenseSummary($l));

        return [
            'licenses' => [
                'total' => $total,
                'active' => $active,
                'expired' => $expired,
                'revoked' => $revoked,
            ],
            'vault_users_count' => VaultUser::count(),
            'devices' => [
                'total' => $deviceTotal,
                'active_activations' => $activeActivations,
                'revoked' => $revokedDevices,
            ],
            'usage_events' => [
                'last_7_days' => $eventsLast7,
                'last_30_days' => $eventsLast30,
                'by_type' => $eventsByType,
                'by_day' => $eventsByDay,
            ],
            'top_courses' => $topCourses,
            'expiring_soon' => $expiringSoon,
        ];
    }

    public function paginateVaultUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $q = VaultUser::query()->orderByDesc('id');
        if (! empty($filters['search'])) {
            $s = $filters['search'];
            $q->where(function ($query) use ($s) {
                $query->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        return $q->paginate($perPage);
    }

    public function createVaultUser(array $data): VaultUser
    {
        return VaultUser::create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function updateVaultUser(VaultUser $vaultUser, array $data): VaultUser
    {
        $vaultUser->update([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return $vaultUser->fresh();
    }

    public function paginateLicenses(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $q = License::with(['vaultUser', 'courses'])->orderByDesc('id');

        if (! empty($filters['status'])) {
            $q->where('status', $filters['status']);
        }
        if (! empty($filters['search'])) {
            $s = $filters['search'];
            $q->where(function ($query) use ($s) {
                $query->where('key', 'like', "%{$s}%")
                    ->orWhereHas('vaultUser', fn ($vu) => $vu->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
            });
        }

        return $q->paginate($perPage);
    }

    public function createLicense(array $data): License
    {
        return DB::transaction(function () use ($data) {
            $this->assertVaultCourseIds($data['courses'] ?? []);

            // New licenses start inactive; activation requires a completed vault_payment.
            $license = License::create([
                'vault_user_id' => $data['vault_user_id'],
                'key' => strtoupper($data['license_key']),
                'start_date' => $data['start_date'],
                'end_date' => $data['expiry_date'],
                'status' => 'inactive',
                'notes' => $data['notes'] ?? null,
            ]);

            $this->syncLicenseCourses($license, $data['courses'] ?? []);
            $this->syncLicenseDevices($license, $data['devices'] ?? []);

            return $license->load(['vaultUser', 'courses', 'licenseDevices']);
        });
    }

    public function updateLicense(License $license, array $data): License
    {
        return DB::transaction(function () use ($license, $data) {
            $existingCourseIds = $license->courses()->pluck('courses.id')->all();
            $newCourseIds = $data['courses'] ?? [];
            $addedIds = array_diff($newCourseIds, $existingCourseIds);
            $this->assertVaultCourseIds($addedIds);

            $nextStatus = $data['status'] ?? $license->status;
            if ($nextStatus === 'active' && $license->status !== 'active') {
                $this->assertCanActivateViaPayment($license);
            }

            $license->update([
                'vault_user_id' => $data['vault_user_id'],
                'key' => strtoupper($data['license_key']),
                'start_date' => $data['start_date'],
                'end_date' => $data['expiry_date'],
                'status' => $nextStatus,
                'notes' => $data['notes'] ?? $license->notes,
            ]);

            LicenseCourse::where('license_id', $license->id)->delete();
            $this->syncLicenseCourses($license, $data['courses'] ?? []);

            // Upsert devices by k_sign so vault_activated_at / revoked_at survive course edits.
            $this->replaceLicenseDevices($license, $data['devices'] ?? []);

            return $license->fresh(['vaultUser', 'courses', 'licenseDevices']);
        });
    }

    public function deleteLicense(License $license): void
    {
        DB::transaction(function () use ($license) {
            $license->licenseDevices()->delete();
            $license->licenseCourses()->delete();
            $license->delete();
        });
    }

    public function updateLicenseStatus(License $license, string $status): License
    {
        if (! in_array($status, ['active', 'inactive', 'revoked'], true)) {
            throw new \InvalidArgumentException('Invalid license status.');
        }

        if ($status === 'active' && $license->status !== 'active') {
            throw new \InvalidArgumentException(
                'Licenses can only be activated by a completed payment (record an offline payment or use an online payment link).'
            );
        }

        $previous = $license->status;
        $license->update(['status' => $status]);

        $event = $status === 'active'
            ? LicenseUsageLogger::EVENT_LICENSE_ACTIVATED
            : LicenseUsageLogger::EVENT_LICENSE_DEACTIVATED;

        LicenseUsageLogger::record(
            $license->id,
            null,
            null,
            $event,
            "Admin changed license status from {$previous} to {$status}"
        );

        return $license->fresh(['vaultUser', 'courses', 'licenseDevices']);
    }

    /**
     * Allow setting status=active on update only when a completed payment already exists
     * (e.g. correcting status after payment). New activations must go through payments.
     */
    protected function assertCanActivateViaPayment(License $license): void
    {
        $hasCompleted = VaultPayment::query()
            ->where('status', 'completed')
            ->where(function ($q) use ($license) {
                $q->where('license_id', $license->id)
                    ->orWhereIn('vault_subscription_id', function ($sub) use ($license) {
                        $sub->select('id')
                            ->from('vault_subscriptions')
                            ->where('license_id', $license->id);
                    });
            })
            ->exists();

        if (! $hasCompleted) {
            throw new \InvalidArgumentException(
                'Licenses can only be activated by a completed payment (record an offline payment or use an online payment link).'
            );
        }
    }

    public function licenseUsage(License $license, int $perPage = 40): array
    {
        $license->load(['vaultUser', 'licenseDevices']);
        $devices = $license->licenseDevices()->orderBy('id')->get();

        return [
            'license' => $this->serializeLicenseDetail($license),
            'devices' => $devices,
            'active_activations' => $devices->filter(fn ($d) => $d->vault_activated_at !== null && $d->revoked_at === null)->count(),
            'revoked_devices' => $devices->filter(fn ($d) => $d->revoked_at !== null)->count(),
            'events' => $license->deviceUsageEvents()->with('licenseDevice')->orderByDesc('id')->paginate($perPage),
        ];
    }

    public function paginateCourses(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->paginateVaultCourses($filters, $perPage);
    }

    public function paginateVaultCourses(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $q = Course::query()
            ->where('course_type', COURSE_TYPE_VAULT)
            ->orderByDesc('id');

        if (! empty($filters['search'])) {
            $s = $filters['search'];
            $q->where(function ($query) use ($s) {
                $query->where('title', 'like', "%{$s}%")
                    ->orWhere('slug', 'like', "%{$s}%");
            });
        }

        return $q->paginate($perPage);
    }

    /**
     * Vault courses for license picker; includes legacy assigned courses when editing.
     *
     * @param  int[]  $includeCourseIds
     */
    public function coursesForLicensePicker(array $includeCourseIds = []): array
    {
        $vaultCourses = Course::query()
            ->where('course_type', COURSE_TYPE_VAULT)
            ->orderBy('title')
            ->get();

        $included = collect();
        if ($includeCourseIds !== []) {
            $included = Course::query()
                ->whereIn('id', $includeCourseIds)
                ->where('course_type', '!=', COURSE_TYPE_VAULT)
                ->orderBy('title')
                ->get();
        }

        return $vaultCourses->merge($included)
            ->unique('id')
            ->values()
            ->map(fn (Course $c) => $this->serializeCourse($c))
            ->all();
    }

    public function createVaultCourse(array $data, ?UploadedFile $image): Course
    {
        if (! $image) {
            throw new \InvalidArgumentException('Course image is required.');
        }

        return DB::transaction(function () use ($data, $image) {
            $course = new Course();
            $course->title = $data['title'];
            $course->slug = $this->uniqueCourseSlug($data['title']);
            $course->description = $data['description'] ?? '';
            $course->subtitle = $data['subtitle'] ?? '';
            $course->course_type = COURSE_TYPE_VAULT;
            $course->private_mode = 1;
            $course->status = 1;
            $course->learner_accessibility = 'free';
            $course->price = 0;
            $course->intro_video_check = (int) ($data['intro_video_check'] ?? 0);
            $course->youtube_video_id = $data['youtube_video_id'] ?? null;
            $course->image = $this->saveCourseImage($image);
            $course->save();

            if (! empty($data['resource_ids'])) {
                $this->syncCourseResources($course->id, $data['resource_ids']);
            }

            app(CourseVaultManifestService::class)->markStaleAndRebuild((int) $course->id);

            return $course->fresh();
        });
    }

    public function updateVaultCourse(Course $course, array $data, ?UploadedFile $image = null): Course
    {
        $this->assertVaultCourse($course);

        return DB::transaction(function () use ($course, $data, $image) {
            $course->title = $data['title'];
            if (! empty($data['slug'])) {
                $course->slug = $data['slug'];
            }
            $course->description = $data['description'] ?? $course->description;
            $course->subtitle = $data['subtitle'] ?? $course->subtitle;
            $course->intro_video_check = (int) ($data['intro_video_check'] ?? 0);
            $course->youtube_video_id = $data['youtube_video_id'] ?? null;

            if ($image) {
                $course->image = $this->saveCourseImage($image);
            }

            $course->save();

            if (array_key_exists('resource_ids', $data)) {
                $this->syncCourseResources($course->id, $data['resource_ids'] ?? []);
            }

            app(CourseVaultManifestService::class)->markStaleAndRebuild((int) $course->id);

            return $course->fresh();
        });
    }

    public function deleteVaultCourse(Course $course): void
    {
        $this->assertVaultCourse($course);

        if (LicenseCourse::where('course_id', $course->id)->exists()) {
            throw new \InvalidArgumentException('Cannot delete a vault course assigned to one or more licenses.');
        }

        DB::transaction(function () use ($course) {
            Course_lecture::where('course_id', $course->id)->delete();
            Course_lesson::where('course_id', $course->id)->delete();
            CourseResource::where('course_id', $course->id)->delete();
            CourseVaultManifest::where('course_id', $course->id)->delete();
            $course->delete();
        });
    }

    public function syncCourseResources(int $courseId, array $resourceIds): void
    {
        Course::findOrFail($courseId);
        $resourceIds = array_values(array_unique(array_map('intval', $resourceIds)));

        CourseResource::where('course_id', $courseId)->delete();

        foreach ($resourceIds as $resourceId) {
            $resource = Resource::find($resourceId);
            if (! $resource) {
                continue;
            }
            $courseResource = new CourseResource();
            $courseResource->course_id = $courseId;
            $courseResource->resource_id = $resourceId;
            if ($resource->file_name) {
                $courseResource->original_filename = $resource->file_name;
                $courseResource->file = 'uploads/course_resource/'.$resource->file_name;
            }
            $courseResource->save();
        }
    }

    public function detachResourceFromCourse(int $courseId, int $resourceId): void
    {
        $this->assertVaultCourse(Course::findOrFail($courseId));

        CourseResource::where('course_id', $courseId)
            ->where('resource_id', $resourceId)
            ->delete();

        app(CourseVaultManifestService::class)->markStaleAndRebuild($courseId);
    }

    public function listResourceCategories(): array
    {
        return ResourceCategory::query()
            ->orderBy('name')
            ->get(['id', 'name', 'category_types'])
            ->map(fn (ResourceCategory $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'category_types' => $c->category_types,
            ])
            ->all();
    }

    public function paginateResources(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $q = Resource::with('category')->orderBy('sort_number')->orderByDesc('id');

        if (! empty($filters['search'])) {
            $s = $filters['search'];
            $q->where(function ($query) use ($s) {
                $query->where('name', 'like', "%{$s}%")
                    ->orWhere('type', 'like', "%{$s}%")
                    ->orWhereHas('category', fn ($cat) => $cat->where('name', 'like', "%{$s}%"));
            });
        }

        if (! empty($filters['category_id'])) {
            $q->where('category_id', (int) $filters['category_id']);
        }

        return $q->paginate($perPage);
    }

    public function createVaultResource(array $data, array $files, ?int $courseId = null): Resource
    {
        return DB::transaction(function () use ($data, $files, $courseId) {
            $category = ResourceCategory::findOrFail($data['category_id']);
            $categoryName = $category->name;

            $resource = new Resource();
            $resource->name = $data['name'];
            $resource->type = $categoryName;
            $resource->category_id = $category->id;
            $resource->created_by = auth()->id();
            $resource->sort_number = $data['sort_number'] ?? null;

            $this->applyResourcePayload($resource, $categoryName, $data, $files);
            $resource->save();

            $courseIds = $courseId ? [$courseId] : ($data['course_ids'] ?? []);
            $this->assignResourceToCourses($resource, $courseIds);

            if ($categoryName === 'Youtube') {
                DownloadYoutubeForVaultJob::dispatch($resource->id);
            }

            return $resource->fresh(['category']);
        });
    }

    public function updateVaultResource(Resource $resource, array $data, array $files): Resource
    {
        return DB::transaction(function () use ($resource, $data, $files) {
            $category = ResourceCategory::findOrFail($data['category_id']);
            $categoryName = $category->name;
            $wasYoutube = strtolower((string) $resource->type) === 'youtube';

            $resource->name = $data['name'];
            $resource->type = $categoryName;
            $resource->category_id = $category->id;
            $resource->updated_by = auth()->id();

            if (isset($data['sort_number'])) {
                $resource->sort_number = $data['sort_number'];
            }

            $this->applyResourcePayload($resource, $categoryName, $data, $files, isUpdate: true);
            $resource->save();

            if (! empty($data['course_ids'])) {
                CourseResource::where('resource_id', $resource->id)->delete();
                $this->assignResourceToCourses($resource, $data['course_ids']);
            } elseif (! empty($data['course_id'])) {
                $existing = CourseResource::where('resource_id', $resource->id)
                    ->where('course_id', (int) $data['course_id'])
                    ->exists();
                if (! $existing) {
                    $this->assignResourceToCourses($resource, [(int) $data['course_id']]);
                } else {
                    app(CourseVaultManifestService::class)->markStaleAndRebuildForResource($resource->id);
                }
            } else {
                app(CourseVaultManifestService::class)->markStaleAndRebuildForResource($resource->id);
            }

            if ($categoryName === 'Youtube' && (! $wasYoutube || ! empty($data['youtube_url_path']))) {
                DownloadYoutubeForVaultJob::dispatch($resource->id);
            }

            return $resource->fresh(['category']);
        });
    }

    public function deleteVaultResource(Resource $resource): void
    {
        $courseIdsAffected = CourseResource::where('resource_id', $resource->id)->pluck('course_id')->unique()->all();
        $filename = $resource->file_name;
        $fileExt = $resource->file_extension;

        DB::transaction(function () use ($resource, $courseIdsAffected, $filename, $fileExt) {
            CourseResource::where('resource_id', $resource->id)->delete();
            $resource->delete();

            if ($filename && $fileExt) {
                if ($fileExt === 'zip') {
                    $dir = public_path('uploads/course_resource/'.$filename);
                    if (is_dir($dir)) {
                        $filesystem = new Filesystem();
                        $filesystem->cleanDirectory($dir);
                        @rmdir($dir);
                    }
                } elseif (is_file(public_path('uploads/course_resource/'.$filename))) {
                    @unlink(public_path('uploads/course_resource/'.$filename));
                }
            }
        });

        app(CourseVaultManifestService::class)->markStaleAndRebuildCourseIds(array_map('intval', $courseIdsAffected));
    }

    public function serializeResourceListItem(Resource $resource): array
    {
        return $this->serializeResource($resource->loadMissing('category'));
    }

    public function courseDetail(int $courseId): array
    {
        $course = Course::findOrFail($courseId);
        $manifest = CourseVaultManifest::query()->where('course_id', $courseId)->first();
        $resourceIds = CourseResource::where('course_id', $courseId)->pluck('resource_id')->all();

        return [
            'course' => $this->serializeCourseDetail($course, $resourceIds),
            'vault_manifest' => $this->serializeVaultManifestSummary($manifest),
            'vault_download_summary' => $this->downloadStatusService->summarizeCourseResources($courseId),
        ];
    }

    /**
     * Queue a vault catalog rebuild for a course (async job).
     *
     * @return array{is_ready: bool, manifest_version: int, last_built_at: ?string, last_error: ?string}
     */
    public function rebuildVaultCourseManifest(int $courseId): array
    {
        $course = Course::findOrFail($courseId);
        $this->assertVaultCourse($course);

        app(CourseVaultManifestService::class)->markStaleAndRebuild($courseId);

        $manifest = CourseVaultManifest::query()->where('course_id', $courseId)->first();

        return $this->serializeVaultManifestSummary($manifest) ?? [
            'is_ready' => false,
            'manifest_version' => 0,
            'last_built_at' => null,
            'last_error' => null,
        ];
    }

    /**
     * @return array{is_ready: bool, manifest_version: int, last_built_at: ?string, last_error: ?string, updated_at?: mixed}|null
     */
    public function serializeVaultManifestSummary(?CourseVaultManifest $manifest): ?array
    {
        if (! $manifest) {
            return null;
        }

        return [
            'is_ready' => (bool) $manifest->is_ready,
            'manifest_version' => (int) ($manifest->manifest_version ?? 0),
            'last_built_at' => $manifest->last_built_at?->toIso8601String(),
            'last_error' => $manifest->last_error,
            'updated_at' => $manifest->updated_at,
        ];
    }

    public function courseResources(int $courseId): array
    {
        Course::findOrFail($courseId);

        $rows = CourseResource::with(['resource.category'])
            ->where('course_id', $courseId)
            ->orderBy('id')
            ->get();

        $grouped = [];
        foreach ($rows as $cr) {
            $resource = $cr->resource;
            if (! $resource) {
                continue;
            }
            $catId = $resource->category_id ?? 0;
            $catName = $resource->category?->name ?? 'Uncategorized';
            if (! isset($grouped[$catId])) {
                $grouped[$catId] = [
                    'category_id' => $catId,
                    'category_name' => $catName,
                    'resources' => [],
                    'download_summary' => [
                        'total' => 0,
                        'ready' => 0,
                        'pending' => 0,
                        'failed' => 0,
                        'not_ready' => 0,
                        'missing_file' => 0,
                        'not_applicable' => 0,
                    ],
                ];
            }
            $serialized = $this->serializeResource($resource, $cr);
            $grouped[$catId]['resources'][] = $serialized;
            $grouped[$catId]['download_summary']['total']++;
            $status = $serialized['vault_download_status'];
            if (isset($grouped[$catId]['download_summary'][$status])) {
                $grouped[$catId]['download_summary'][$status]++;
            }
        }

        return array_values($grouped);
    }

    public function courseCurriculum(int $courseId): array
    {
        $course = Course::findOrFail($courseId);
        $this->assertVaultCourse($course);

        $lessons = Course_lesson::query()
            ->where('course_id', $courseId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->with(['lectures' => fn ($q) => $q->orderBy('sort_order')->orderBy('id')->with('resource.category')])
            ->get();

        $usesLessons = $lessons->isNotEmpty();
        $layout = $usesLessons ? 'lessons' : 'categories';

        return [
            'course_id' => $courseId,
            'content_layout' => $layout,
            'lessons' => $lessons->map(fn (Course_lesson $lesson) => $this->serializeLesson($lesson))->all(),
            'vault_manifest' => $this->serializeVaultManifestSummary(
                CourseVaultManifest::query()->where('course_id', $courseId)->first()
            ),
            'vault_download_summary' => $this->downloadStatusService->summarizeCourseResources($courseId),
        ];
    }

    public function createLesson(int $courseId, array $data): array
    {
        $course = Course::findOrFail($courseId);
        $this->assertVaultCourse($course);

        $maxSort = (int) Course_lesson::where('course_id', $courseId)->max('sort_order');

        $lesson = Course_lesson::create([
            'course_id' => $courseId,
            'name' => $data['name'],
            'short_description' => $data['short_description'] ?? null,
            'sort_order' => $maxSort + 1,
        ]);

        app(CourseVaultManifestService::class)->markStaleAndRebuild($courseId);

        return $this->serializeLesson($lesson->fresh(['lectures.resource.category']));
    }

    public function updateLesson(int $courseId, int $lessonId, array $data): array
    {
        $lesson = $this->findVaultLesson($courseId, $lessonId);

        $lesson->update([
            'name' => $data['name'] ?? $lesson->name,
            'short_description' => array_key_exists('short_description', $data)
                ? $data['short_description']
                : $lesson->short_description,
        ]);

        app(CourseVaultManifestService::class)->markStaleAndRebuild($courseId);

        return $this->serializeLesson($lesson->fresh(['lectures.resource.category']));
    }

    public function deleteLesson(int $courseId, int $lessonId): void
    {
        $lesson = $this->findVaultLesson($courseId, $lessonId);

        DB::transaction(function () use ($lesson, $courseId) {
            Course_lecture::where('lesson_id', $lesson->id)->delete();
            $lesson->delete();
            $this->syncCourseResourcesFromLectures($courseId);
        });

        app(CourseVaultManifestService::class)->markStaleAndRebuild($courseId);
    }

    /**
     * @param  int[]  $orderedLessonIds
     */
    public function reorderLessons(int $courseId, array $orderedLessonIds): void
    {
        $course = Course::findOrFail($courseId);
        $this->assertVaultCourse($course);

        $orderedLessonIds = array_values(array_map('intval', $orderedLessonIds));
        $existing = Course_lesson::where('course_id', $courseId)->pluck('id')->all();

        if (count($orderedLessonIds) !== count($existing) || array_diff($orderedLessonIds, $existing) !== []) {
            throw new \InvalidArgumentException('Invalid lesson order payload.');
        }

        foreach ($orderedLessonIds as $index => $lessonId) {
            Course_lesson::where('id', $lessonId)->update(['sort_order' => $index + 1]);
        }

        app(CourseVaultManifestService::class)->markStaleAndRebuild($courseId);
    }

    public function createLecture(int $courseId, int $lessonId, array $data): array
    {
        $lesson = $this->findVaultLesson($courseId, $lessonId);
        $resource = Resource::findOrFail($data['resource_id']);

        $maxSort = (int) Course_lecture::where('lesson_id', $lessonId)->max('sort_order');

        $lecture = Course_lecture::create([
            'course_id' => $courseId,
            'lesson_id' => $lessonId,
            'title' => $data['title'] ?? $resource->name,
            'resource_id' => $resource->id,
            'type' => 'resource',
            'lecture_type' => 1,
            'sort_order' => $maxSort + 1,
        ]);

        $this->ensureResourceOnCourse($courseId, (int) $resource->id);
        $this->syncCourseResourcesFromLectures($courseId);

        return $this->serializeLecture($lecture->fresh(['resource.category']));
    }

    public function updateLecture(int $courseId, int $lessonId, int $lectureId, array $data): array
    {
        $lecture = $this->findVaultLecture($courseId, $lessonId, $lectureId);

        $updates = [];
        if (isset($data['title'])) {
            $updates['title'] = $data['title'];
        }
        if (isset($data['resource_id'])) {
            Resource::findOrFail($data['resource_id']);
            $updates['resource_id'] = (int) $data['resource_id'];
        }

        $lecture->update($updates);

        if (isset($data['resource_id'])) {
            $this->ensureResourceOnCourse($courseId, (int) $data['resource_id']);
            $this->syncCourseResourcesFromLectures($courseId);
        } else {
            app(CourseVaultManifestService::class)->markStaleAndRebuild($courseId);
        }

        return $this->serializeLecture($lecture->fresh(['resource.category']));
    }

    public function deleteLecture(int $courseId, int $lessonId, int $lectureId): void
    {
        $lecture = $this->findVaultLecture($courseId, $lessonId, $lectureId);

        DB::transaction(function () use ($lecture, $courseId) {
            $lecture->delete();
            $this->syncCourseResourcesFromLectures($courseId);
        });

        app(CourseVaultManifestService::class)->markStaleAndRebuild($courseId);
    }

    /**
     * @param  int[]  $orderedLectureIds
     */
    public function reorderLectures(int $courseId, int $lessonId, array $orderedLectureIds): void
    {
        $this->findVaultLesson($courseId, $lessonId);

        $orderedLectureIds = array_values(array_map('intval', $orderedLectureIds));
        $existing = Course_lecture::where('lesson_id', $lessonId)->pluck('id')->all();

        if (count($orderedLectureIds) !== count($existing) || array_diff($orderedLectureIds, $existing) !== []) {
            throw new \InvalidArgumentException('Invalid lecture order payload.');
        }

        foreach ($orderedLectureIds as $index => $lectureId) {
            Course_lecture::where('id', $lectureId)->update(['sort_order' => $index + 1]);
        }

        app(CourseVaultManifestService::class)->markStaleAndRebuild($courseId);
    }

    public function ensureResourceOnCourse(int $courseId, int $resourceId): void
    {
        $exists = CourseResource::where('course_id', $courseId)
            ->where('resource_id', $resourceId)
            ->exists();

        if ($exists) {
            return;
        }

        $resource = Resource::findOrFail($resourceId);
        $courseResource = new CourseResource();
        $courseResource->course_id = $courseId;
        $courseResource->resource_id = $resourceId;
        if ($resource->file_name) {
            $courseResource->original_filename = $resource->file_name;
            $courseResource->file = 'uploads/course_resource/'.$resource->file_name;
        }
        $courseResource->save();
    }

    public function syncCourseResourcesFromLectures(int $courseId): void
    {
        Course::findOrFail($courseId);

        $resourceIds = Course_lecture::query()
            ->where('course_id', $courseId)
            ->whereNotNull('resource_id')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->pluck('resource_id')
            ->unique()
            ->values()
            ->all();

        if ($resourceIds === []) {
            CourseResource::where('course_id', $courseId)->delete();

            return;
        }

        $this->syncCourseResources($courseId, $resourceIds);
    }

    public function retryVaultDownload(int $resourceId): array
    {
        $resource = Resource::with('category')->findOrFail($resourceId);

        if (strtolower((string) $resource->type) !== 'youtube') {
            throw new \InvalidArgumentException('Retry only applies to YouTube vault downloads.');
        }

        $this->downloadStatusService->markPending($resource);
        DownloadYoutubeForVaultJob::dispatch($resource->id);

        $resource->refresh();

        return $this->serializeResource($resource);
    }

    public function resourcePreview(int $resourceId): array
    {
        $resource = Resource::with('category')->findOrFail($resourceId);

        return $this->serializeResourcePreview($resource);
    }

    protected function syncLicenseCourses(License $license, array $courseIds): void
    {
        foreach ($courseIds as $courseId) {
            LicenseCourse::create([
                'license_id' => $license->id,
                'course_id' => $courseId,
            ]);
        }
    }

    protected function syncLicenseDevices(License $license, array $devices): void
    {
        foreach ($devices as $device) {
            if (empty($device['name']) || empty($device['k_sign'])) {
                continue;
            }
            $created = LicenseDevice::create([
                'license_id' => $license->id,
                'name' => $device['name'],
                'k_sign' => $device['k_sign'],
                'registered_at' => now(),
            ]);
            LicenseUsageLogger::record(
                $license->id,
                $created->id,
                $created->k_sign,
                LicenseUsageLogger::EVENT_DEVICE_REGISTERED_ADMIN,
                'Admin registered device: '.$device['name']
            );
        }
    }

    /**
     * Replace the license device list without wiping activation/revocation on matching k_sign.
     * Prevents Sync Content failures after admin edits courses (or other license fields).
     *
     * @param  array<int, array{name?: string, k_sign?: string}>  $devices
     */
    protected function replaceLicenseDevices(License $license, array $devices): void
    {
        $incoming = [];
        foreach ($devices as $device) {
            if (empty($device['name']) || empty($device['k_sign'])) {
                continue;
            }
            $normalized = strtolower(trim((string) $device['k_sign']));
            if ($normalized === '') {
                continue;
            }
            $incoming[$normalized] = [
                'name' => (string) $device['name'],
                'k_sign' => (string) $device['k_sign'],
            ];
        }

        $existing = LicenseDevice::where('license_id', $license->id)->get()->keyBy(
            fn (LicenseDevice $row) => strtolower(trim((string) $row->k_sign))
        );

        foreach ($existing as $key => $row) {
            if (! isset($incoming[$key])) {
                $row->delete();
            }
        }

        foreach ($incoming as $key => $payload) {
            if (isset($existing[$key])) {
                /** @var LicenseDevice $row */
                $row = $existing[$key];
                if ($row->name !== $payload['name'] || $row->k_sign !== $payload['k_sign']) {
                    $row->name = $payload['name'];
                    $row->k_sign = $payload['k_sign'];
                    $row->save();
                }
                continue;
            }

            $created = LicenseDevice::create([
                'license_id' => $license->id,
                'name' => $payload['name'],
                'k_sign' => $payload['k_sign'],
                'registered_at' => now(),
            ]);
            LicenseUsageLogger::record(
                $license->id,
                $created->id,
                $created->k_sign,
                LicenseUsageLogger::EVENT_DEVICE_REGISTERED_ADMIN,
                'Admin registered device: '.$payload['name']
            );
        }
    }

    public function serializeLicenseSummary(License $license): array
    {
        return [
            'id' => $license->id,
            'key' => $license->key,
            'status' => $license->status,
            'start_date' => $license->start_date,
            'end_date' => $license->end_date,
            'vault_user' => $license->vaultUser ? [
                'id' => $license->vaultUser->id,
                'name' => $license->vaultUser->name,
                'email' => $license->vaultUser->email,
            ] : null,
            'courses_count' => $license->courses?->count() ?? $license->courses()->count(),
        ];
    }

    public function serializeLicenseDetail(License $license): array
    {
        $license->loadMissing(['vaultUser', 'courses', 'licenseDevices']);

        return [
            ...$this->serializeLicenseSummary($license),
            'notes' => $license->notes,
            'courses' => $license->courses->map(fn (Course $c) => $this->serializeCourse($c)),
            'devices' => $license->licenseDevices,
        ];
    }

    public function serializeCourse(Course $course): array
    {
        return [
            'id' => $course->id,
            'title' => $course->title,
            'slug' => $course->slug,
            'course_type' => $course->course_type,
            'status' => $course->status,
            'image_url' => $course->image_url ?? getImageFile($course->image),
            'is_vault' => (int) $course->course_type === COURSE_TYPE_VAULT,
        ];
    }

    /**
     * @param  int[]  $resourceIds
     */
    public function serializeCourseDetail(Course $course, array $resourceIds = []): array
    {
        return [
            ...$this->serializeCourse($course),
            'description' => $course->description,
            'subtitle' => $course->subtitle,
            'intro_video_check' => (int) ($course->intro_video_check ?? 0),
            'youtube_video_id' => $course->youtube_video_id,
            'resource_ids' => $resourceIds,
        ];
    }

    protected function serializeResource(Resource $resource, ?CourseResource $cr = null): array
    {
        return array_merge([
            'id' => $resource->id,
            'name' => $resource->name,
            'type' => $resource->type,
            'category_id' => $resource->category_id,
            'category_name' => $resource->category?->name,
            'sort_number' => $resource->sort_number,
            'file' => $cr?->file,
        ], $this->downloadStatusService->resolve($resource));
    }

    protected function serializeResourcePreview(Resource $resource): array
    {
        $baseUrl = rtrim(config('app.url'), '/');
        $previewUrl = null;
        $embedUrl = null;

        switch ($resource->type) {
            case 'Youtube':
                $embedUrl = $this->downloadStatusService->youtubeEmbedUrl($resource);
                $previewUrl = $embedUrl;
                break;
            case 'PDF':
            case 'Video':
            case 'Image':
            case 'Audio':
                if ($resource->file_name) {
                    $previewUrl = $baseUrl.'/uploads/course_resource/'.$resource->file_name;
                }
                break;
            case 'Text':
                $previewUrl = null;
                break;
            case 'Slide Document':
                if ($resource->file_name) {
                    $previewUrl = $baseUrl.'/uploads/course_resource/'.$resource->file_name;
                }
                break;
        }

        return array_merge([
            'id' => $resource->id,
            'name' => $resource->name,
            'type' => $resource->type,
            'preview_url' => $previewUrl,
            'embed_url' => $embedUrl,
            'text' => $resource->type === 'Text' ? $resource->text : null,
            'category_name' => $resource->category?->name,
        ], $this->downloadStatusService->resolve($resource, syncDb: false));
    }

    protected function assertVaultCourse(Course $course): void
    {
        if ((int) $course->course_type !== COURSE_TYPE_VAULT) {
            throw new \InvalidArgumentException('This action applies to vault courses only.');
        }
    }

    protected function findVaultLesson(int $courseId, int $lessonId): Course_lesson
    {
        $course = Course::findOrFail($courseId);
        $this->assertVaultCourse($course);

        return Course_lesson::where('course_id', $courseId)->where('id', $lessonId)->firstOrFail();
    }

    protected function findVaultLecture(int $courseId, int $lessonId, int $lectureId): Course_lecture
    {
        $this->findVaultLesson($courseId, $lessonId);

        return Course_lecture::where('course_id', $courseId)
            ->where('lesson_id', $lessonId)
            ->where('id', $lectureId)
            ->firstOrFail();
    }

    public function serializeLesson(Course_lesson $lesson): array
    {
        return [
            'id' => $lesson->id,
            'uuid' => $lesson->uuid,
            'name' => $lesson->name,
            'short_description' => $lesson->short_description,
            'sort_order' => (int) ($lesson->sort_order ?? 0),
            'lectures' => $lesson->lectures->map(fn (Course_lecture $lecture) => $this->serializeLecture($lecture))->all(),
        ];
    }

    public function serializeLecture(Course_lecture $lecture): array
    {
        $resource = $lecture->resource;

        return [
            'id' => $lecture->id,
            'uuid' => $lecture->uuid,
            'title' => $lecture->title,
            'sort_order' => (int) ($lecture->sort_order ?? 0),
            'resource_id' => $lecture->resource_id,
            'resource' => $resource ? $this->serializeResource($resource) : null,
        ];
    }

    /**
     * @param  int[]  $courseIds
     */
    protected function assertVaultCourseIds(array $courseIds): void
    {
        if ($courseIds === []) {
            return;
        }

        $invalid = Course::query()
            ->whereIn('id', $courseIds)
            ->where('course_type', '!=', COURSE_TYPE_VAULT)
            ->pluck('id')
            ->all();

        if ($invalid !== []) {
            throw new \InvalidArgumentException('Licenses may only include vault courses (type 4). Invalid IDs: '.implode(', ', $invalid));
        }
    }

    protected function uniqueCourseSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;

        if (Course::where('slug', $slug)->exists()) {
            $slug = $base.'-'.rand(100000, 999999);
        }

        return $slug;
    }

    protected function saveCourseImage(UploadedFile $file): string
    {
        $result = saveUpload('course', $file, null, null);

        return 'uploads/course/'.$result['fileName'];
    }

    /**
     * @param  int[]  $courseIds
     */
    protected function assignResourceToCourses(Resource $resource, array $courseIds): void
    {
        $courseIds = array_values(array_unique(array_map('intval', $courseIds)));
        $manifestService = app(CourseVaultManifestService::class);

        foreach ($courseIds as $courseId) {
            $courseResource = new CourseResource();
            $courseResource->course_id = $courseId;
            $courseResource->resource_id = $resource->id;
            if ($resource->file_name) {
                $courseResource->original_filename = $resource->file_name;
                $courseResource->file = 'uploads/course_resource/'.$resource->file_name;
            }
            $courseResource->save();
            $manifestService->markStaleAndRebuild($courseId);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, UploadedFile|null>  $files
     */
    protected function applyResourcePayload(
        Resource $resource,
        string $categoryName,
        array $data,
        array $files,
        bool $isUpdate = false
    ): void {
        switch ($categoryName) {
            case 'Video':
                if (! empty($files['video_file'])) {
                    if ($isUpdate && $resource->file_name) {
                        $this->deleteResourceFile($resource->file_name, $resource->file_extension);
                    }
                    $dataResult = saveUpload('course_resource', $files['video_file'], null, null);
                    $resource->file_name = $dataResult['fileName'];
                    $resource->file_extension = $dataResult['fileExt'];
                }
                break;
            case 'Audio':
                if (! empty($files['audio'])) {
                    if ($isUpdate && $resource->file_name) {
                        $this->deleteResourceFile($resource->file_name, $resource->file_extension);
                    }
                    $dataResult = saveUpload('course_resource', $files['audio'], null, null);
                    $resource->file_name = $dataResult['fileName'];
                    $resource->file_extension = $dataResult['fileExt'];
                }
                break;
            case 'Image':
                if (! empty($files['image'])) {
                    if ($isUpdate && $resource->file_name) {
                        $this->deleteResourceFile($resource->file_name, $resource->file_extension);
                    }
                    $dataResult = saveUpload('course_resource', $files['image'], null, null);
                    $resource->file_name = $dataResult['fileName'];
                    $resource->file_extension = $dataResult['fileExt'];
                }
                break;
            case 'PDF':
                if (! empty($files['pdf'])) {
                    if ($isUpdate && $resource->file_name) {
                        $this->deleteResourceFile($resource->file_name, $resource->file_extension);
                    }
                    $dataResult = saveUpload('course_resource', $files['pdf'], null, null);
                    $resource->file_name = $dataResult['fileName'];
                    $resource->file_extension = $dataResult['fileExt'];
                }
                break;
            case 'Slide Document':
                if (! empty($files['slide_document'])) {
                    if ($isUpdate && $resource->file_name) {
                        $this->deleteResourceFile($resource->file_name, $resource->file_extension);
                    }
                    $dataResult = saveUpload('course_resource', $files['slide_document'], null, null);
                    $resource->file_name = $dataResult['fileName'];
                    $resource->file_extension = $dataResult['fileExt'];
                }
                break;
            case 'Text':
                $resource->text = $data['text'] ?? $data['text_description'] ?? '';
                break;
            case 'Youtube':
                if (! empty($data['youtube_url_path'])) {
                    $resource->youtube_url_path = $data['youtube_url_path'];
                }
                if (! empty($data['youtube_file_duration'])) {
                    $resource->youtube_video_duration = $data['youtube_file_duration'];
                    $resource->youtube_video_duration_second = $this->timeToSeconds($data['youtube_file_duration']);
                }
                break;
        }
    }

    protected function deleteResourceFile(?string $filename, ?string $fileExt): void
    {
        if (! $filename) {
            return;
        }

        if ($fileExt === 'zip') {
            $dir = public_path('uploads/course_resource/'.$filename);
            if (is_dir($dir)) {
                $filesystem = new Filesystem();
                $filesystem->cleanDirectory($dir);
                @rmdir($dir);
            }
        } elseif (is_file(public_path('uploads/course_resource/'.$filename))) {
            @unlink(public_path('uploads/course_resource/'.$filename));
        }
    }

    protected function timeToSeconds(string $time): int
    {
        $arr = explode(':', $time);
        if (count($arr) === 3) {
            return (int) $arr[0] * 3600 + (int) $arr[1] * 60 + (int) $arr[2];
        }

        return (int) $arr[0] * 60 + (int) ($arr[1] ?? 0);
    }
}

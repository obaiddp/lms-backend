<?php

namespace App\Services;

use App\Models\Course;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VaultManifestBuilder
{
    public function __construct(
        private VaultPayloadCrypto $crypto,
        private SlideZipPackagingService $slideZipPackagingService,
        private VaultCurriculumBuilder $curriculumBuilder
    ) {}

    /**
     * @return array<string, mixed> Single course block matching validate_license client shape.
     */
    public function buildCoursePayload(int $courseId): array
    {
        $resources = DB::table('course_resources as cr')
            ->join('resources as r', 'cr.resource_id', '=', 'r.id')
            ->join('courses as c', 'cr.course_id', '=', 'c.id')
            ->where('cr.course_id', $courseId)
            ->select(
                'r.*',
                'cr.course_id',
                'c.title as course_title',
                'c.slug as course_slug',
                'c.image as course_image',
                'c.description as course_description'
            )
            ->orderBy('r.sort_number')
            ->get();

        if ($resources->isEmpty()) {
            $course = Course::query()->whereKey($courseId)->first();
            if (! $course) {
                throw new \InvalidArgumentException("Course {$courseId} not found.");
            }

            return array_merge([
                'course_id' => $courseId,
                'course_slug' => $course->slug,
                'course_image' => getImageFile($course->image),
                'course_name' => $course->title,
                'course_description' => (string) ($course->description ?? ''),
                'resources_by_id' => [],
            ], $this->curriculumBuilder->build($courseId));
        }

        $first = $resources->first();
        $courseMap = [
            'course_id' => $courseId,
            'course_slug' => $first->course_slug,
            'course_image' => getImageFile($first->course_image),
            'course_name' => $first->course_title ?? ('Course '.$courseId),
            'course_description' => (string) ($first->course_description ?? ''),
        ];

        $resourcesById = [];

        foreach ($resources as $resource) {
            $type = $resource->type ?? 'Other';
            $actualAsset = null;
            $encryptedData = null;
            $actualAssetIndexFile = null;
            $vaultVideoReady = false;

            switch ($type) {
                case 'Youtube':
                    $encryptedData = null;
                    if (! empty($resource->vault_video_path) && Storage::disk('local')->exists($resource->vault_video_path)) {
                        $streamUrl = url()->route('vault.stream-video', ['resourceId' => $resource->id]);
                        $encryptedData = $this->crypto->encrypt($streamUrl);
                        $vaultVideoReady = true;
                    }
                    break;

                case 'Text':
                    $actualAsset = $resource->text ?? '';
                    $encryptedData = $this->crypto->encrypt((string) $actualAsset);
                    break;

                case 'Slide Document':
                    $folderFs = public_path('uploads/course_resource/'.$resource->file_name);
                    $zipFs = public_path('uploads/course_resource/'.$resource->file_name.'.zip');
                    $zipUrl = asset('uploads/course_resource').'/'.$resource->file_name.'.zip';

                    $zipped = false;
                    try {
                        $zipped = $this->slideZipPackagingService->zipSlideFolder($folderFs, $zipFs);
                    } catch (\Throwable $ex) {
                        Log::warning('Vault manifest: slide zip failed', [
                            'course_id' => $courseId,
                            'resource_id' => $resource->id,
                            'message' => $ex->getMessage(),
                        ]);
                        $zipped = false;
                    }

                    if ($zipped && file_exists($zipFs)) {
                        $actualAsset = $zipUrl;
                        $encryptedData = $this->crypto->encrypt($zipUrl);
                        $actualAssetIndexFile = $this->buildFileName($resource);
                    } else {
                        $actualAsset = asset('uploads/course_resource').'/'.$resource->file_name;
                        $encryptedData = $this->crypto->encrypt((string) $actualAsset);
                        $actualAssetIndexFile = $this->buildFileName($resource);
                    }
                    break;

                default:
                    $assetPath = $this->buildFileName($resource);
                    $actualAsset = $assetPath;
                    $encryptedData = $this->crypto->encrypt((string) $assetPath);
                    break;
            }

            if (! isset($courseMap[$type])) {
                $courseMap[$type] = [];
            }

            if ($type === 'Slide Document') {
                $item = [
                    'id' => $resource->id,
                    'name' => $resource->name ?? null,
                    'encrypted_asset' => $encryptedData,
                    'actual_asset' => $actualAsset,
                    'actual_index_file_url' => $actualAssetIndexFile,
                    'resource_type' => $type,
                ];
            } else {
                $item = [
                    'id' => $resource->id,
                    'name' => $resource->name ?? null,
                    'encrypted_asset' => $encryptedData,
                    'actual_asset' => $actualAsset,
                    'resource_type' => $type,
                ];
                if ($type === 'Youtube') {
                    $item['vault_video_ready'] = $vaultVideoReady;
                }
            }

            $courseMap[$type][] = $item;
            $resourcesById[(string) $resource->id] = $item;
        }

        return array_merge($courseMap, [
            'resources_by_id' => $resourcesById,
        ], $this->curriculumBuilder->build($courseId));
    }

    private function buildFileName(object $resource): string
    {
        if (($resource->type ?? '') === 'Slide Document') {
            return asset('uploads/course_resource').'/'.$resource->file_name.'/index.html';
        }

        return asset('uploads/course_resource').'/'.$resource->file_name;
    }
}

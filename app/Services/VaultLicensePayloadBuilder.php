<?php

namespace App\Services;

use App\Models\CourseVaultManifest;
use App\Models\License;
use App\Models\LicenseDevice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Shared license payload shape for validate_license / sync_license / offline packs.
 */
class VaultLicensePayloadBuilder
{
    /**
     * @return array<string, mixed>
     */
    public function build(License $license, ?string $licenseKey = null): array
    {
        $license->loadMissing(['vaultUser', 'user', 'courses']);
        $licenseKey = $licenseKey !== null && $licenseKey !== ''
            ? strtoupper(trim($licenseKey))
            : strtoupper(trim((string) $license->key));
        $endDateField = $license->end_date;

        $holderName = $license->holderName();
        $holderId = $license->holderId();
        $devices = [];
        try {
            $licenseDevices = LicenseDevice::where('license_id', $license->id)->get();
            foreach ($licenseDevices as $d) {
                $devices[] = [
                    'id' => $d->id,
                    'name' => $d->name,
                    'device_id' => $d->k_sign,
                ];
            }
        } catch (\Throwable $e) {
            $devices = [];
        }

        $resourcesArray = [];

        try {
            $courses = $license->courses ?? [];
            $seenCourse = [];
            foreach ($courses as $course) {
                $cid = (int) $course->id;
                if ($cid <= 0 || isset($seenCourse[$cid])) {
                    continue;
                }
                $seenCourse[$cid] = true;

                $manifest = CourseVaultManifest::query()->where('course_id', $cid)->first();
                if ($manifest && $manifest->is_ready && is_array($manifest->payload)) {
                    $resourcesArray[] = array_merge($manifest->payload, [
                        'manifest_version' => (int) ($manifest->manifest_version ?? 0),
                        'manifest_built_at' => $manifest->last_built_at?->toIso8601String(),
                    ]);
                } else {
                $resourcesArray[] = [
                    'course_id' => $cid,
                    'course_slug' => $course->slug,
                    'course_image' => getImageFile($course->image),
                    'course_name' => $course->title,
                    'course_description' => (string) ($course->description ?? ''),
                    'vault_manifest_pending' => true,
                ];
                }
            }
        } catch (\Throwable $th) {
            Log::error('VaultLicensePayloadBuilder failed', [
                'license_id' => $license->id,
                'message' => $th->getMessage(),
            ]);
            $resourcesArray = [];
        }

        return [
            'success' => true,
            'user_name' => $holderName,
            'user_id' => $holderId,
            'license_id' => (int) $license->id,
            'license' => $licenseKey,
            'license_expiry_time' => Carbon::parse($endDateField)->format('Y-m-d H:i:s'),
            'license_devices' => $devices,
            'resources' => $resourcesArray,
        ];
    }
}

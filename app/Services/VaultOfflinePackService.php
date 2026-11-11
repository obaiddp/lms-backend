<?php

namespace App\Services;

use App\Jobs\BuildVaultOfflinePackJob;
use App\Models\CourseVaultManifest;
use App\Models\License;
use App\Models\LicenseDevice;
use App\Models\VaultOfflinePack;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class VaultOfflinePackService
{
    public const FORMAT_VERSION = 'cef-vault-pack-v1';

    public function __construct(
        protected VaultLicensePayloadBuilder $payloadBuilder,
        protected VaultOfflineGrantService $grantService,
        protected VaultOfflinePackCrypto $packCrypto,
        protected SlideZipPackagingService $slideZipPackagingService
    ) {}

    /**
     * Queue a new offline pack build.
     *
     * @param  array{validity_mode: string, duration_days?: int|null}  $options
     */
    public function queuePack(License $license, array $options, ?int $createdBy = null): VaultOfflinePack
    {
        $mode = $options['validity_mode'] ?? '';
        if (! in_array($mode, ['absolute', 'duration'], true)) {
            throw new \InvalidArgumentException('validity_mode must be absolute or duration.');
        }

        $durationSeconds = null;
        if ($mode === 'duration') {
            $days = (int) ($options['duration_days'] ?? 0);
            if ($days < 1 || $days > 3650) {
                throw new \InvalidArgumentException('duration_days must be between 1 and 3650.');
            }
            $durationSeconds = $days * 86400;
        }

        $this->assertCanBuild($license);

        $pack = VaultOfflinePack::query()->create([
            'license_id' => $license->id,
            'pack_id' => (string) Str::uuid(),
            'validity_mode' => $mode,
            'duration_seconds' => $durationSeconds,
            'status' => 'pending',
            'created_by' => $createdBy,
        ]);

        BuildVaultOfflinePackJob::dispatch($pack->id);

        return $pack;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listPacks(License $license): array
    {
        return VaultOfflinePack::query()
            ->where('license_id', $license->id)
            ->orderByDesc('id')
            ->get()
            ->map(fn (VaultOfflinePack $p) => $this->serializePack($p))
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function serializePack(VaultOfflinePack $pack): array
    {
        return [
            'id' => $pack->id,
            'pack_id' => $pack->pack_id,
            'validity_mode' => $pack->validity_mode,
            'duration_seconds' => $pack->duration_seconds,
            'duration_days' => $pack->duration_seconds !== null
                ? (int) round($pack->duration_seconds / 86400)
                : null,
            'status' => $pack->status,
            'byte_size' => $pack->byte_size,
            'error' => $pack->error,
            'created_at' => $pack->created_at?->toIso8601String(),
            'updated_at' => $pack->updated_at?->toIso8601String(),
            'downloadable' => $pack->status === 'ready' && $pack->storage_path
                && Storage::disk('local')->exists($pack->storage_path),
        ];
    }

    public function absoluteStoragePath(VaultOfflinePack $pack): ?string
    {
        if (! $pack->storage_path || ! Storage::disk('local')->exists($pack->storage_path)) {
            return null;
        }

        return Storage::disk('local')->path($pack->storage_path);
    }

    public function deletePack(VaultOfflinePack $pack): void
    {
        if ($pack->storage_path && Storage::disk('local')->exists($pack->storage_path)) {
            Storage::disk('local')->delete($pack->storage_path);
        }
        $pack->delete();
    }

    public function assertCanBuild(License $license): void
    {
        if (strtolower((string) $license->status) !== 'active') {
            throw new \InvalidArgumentException('License must be active to generate an offline pack.');
        }

        $devices = LicenseDevice::query()
            ->where('license_id', $license->id)
            ->whereNull('revoked_at')
            ->get();
        if ($devices->isEmpty()) {
            throw new \InvalidArgumentException('License needs at least one non-revoked registered device.');
        }

        $license->loadMissing('courses');
        $courses = $license->courses;
        if ($courses->isEmpty()) {
            throw new \InvalidArgumentException('License has no assigned courses.');
        }

        $pending = [];
        $youtubePending = [];
        foreach ($courses as $course) {
            $cid = (int) $course->id;
            $manifest = CourseVaultManifest::query()->where('course_id', $cid)->first();
            if (! $manifest || ! $manifest->is_ready) {
                $pending[] = $course->title ?? ('Course '.$cid);
            }

            $resources = DB::table('course_resources as cr')
                ->join('resources as r', 'cr.resource_id', '=', 'r.id')
                ->where('cr.course_id', $cid)
                ->whereRaw('LOWER(r.type) = ?', ['youtube'])
                ->select('r.id', 'r.name', 'r.vault_video_path')
                ->get();
            foreach ($resources as $r) {
                $path = $r->vault_video_path ?? null;
                if (empty($path) || ! Storage::disk('local')->exists($path)) {
                    $youtubePending[] = ($r->name ?? 'YouTube').' (#'.$r->id.')';
                }
            }
        }

        if ($pending !== []) {
            throw new \InvalidArgumentException(
                'Course catalog not ready for: '.implode(', ', $pending).'. Rebuild manifests first.'
            );
        }
        if ($youtubePending !== []) {
            throw new \InvalidArgumentException(
                'YouTube vault videos not ready: '.implode(', ', $youtubePending)
            );
        }
    }

    /**
     * Build ZIP on disk (called from queue job).
     */
    public function buildPack(VaultOfflinePack $pack): void
    {
        $license = License::with(['courses', 'vaultUser', 'user'])->find($pack->license_id);
        if (! $license) {
            throw new \RuntimeException('License not found for pack '.$pack->id);
        }

        $this->assertCanBuild($license);

        $packId = $pack->pack_id;
        $mediaKey = random_bytes(32);
        $mediaKeyWrap = $this->packCrypto->wrapMediaKey($mediaKey, (int) $license->id, $packId);

        $payload = $this->payloadBuilder->build($license);
        // Strip accidental success wrapper keys if any; keep client shape.
        unset($payload['message']);

        $devices = LicenseDevice::query()
            ->where('license_id', $license->id)
            ->whereNull('revoked_at')
            ->get();

        $grants = [];
        foreach ($devices as $device) {
            $ksig = strtolower(trim((string) $device->k_sign));
            $jwt = $this->grantService->issuePackGrant(
                $license,
                $device,
                $ksig,
                $packId,
                $pack->validity_mode,
                $pack->duration_seconds
            );
            if ($jwt === null) {
                throw new \RuntimeException('Could not sign offline pack grants. Configure VAULT_OFFLINE_JWT private key.');
            }
            $grants[$ksig] = $jwt;
        }

        $validity = [
            'mode' => $pack->validity_mode,
            'duration_seconds' => $pack->duration_seconds,
            'license_start' => Carbon::parse($license->start_date)->utc()->toIso8601String(),
            'license_end' => Carbon::parse($license->end_date)->utc()->toIso8601String(),
        ];

        $workDir = storage_path('app/vault_offline_packs_tmp/'.$packId);
        if (is_dir($workDir)) {
            $this->rrmdir($workDir);
        }
        mkdir($workDir.'/media', 0755, true);
        mkdir($workDir.'/seed', 0755, true);

        $mediaIndex = $this->collectAndEncryptMedia($license, $mediaKey, $workDir.'/media');

        $keyHint = (string) $license->key;
        $keyHint = strlen($keyHint) > 4 ? substr($keyHint, -4) : $keyHint;

        $manifest = [
            'format_version' => self::FORMAT_VERSION,
            'pack_id' => $packId,
            'license_id' => (int) $license->id,
            'license_key_hint' => $keyHint,
            'created_at' => Carbon::now()->utc()->toIso8601String(),
            'course_ids' => $license->courses->pluck('id')->map(fn ($id) => (int) $id)->values()->all(),
            'device_count' => count($grants),
            'validity' => $validity,
            'media_key_wrap' => $mediaKeyWrap,
        ];

        file_put_contents($workDir.'/manifest.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        file_put_contents($workDir.'/seed/license_payload.json', json_encode($payload, JSON_UNESCAPED_SLASHES));
        file_put_contents($workDir.'/seed/grants.json', json_encode($grants, JSON_UNESCAPED_SLASHES));
        file_put_contents($workDir.'/seed/validity.json', json_encode($validity, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        file_put_contents($workDir.'/media_index.json', json_encode($mediaIndex, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $relativeZip = 'vault_offline_packs/'.$license->id.'/'.$packId.'.zip';
        $absoluteZip = Storage::disk('local')->path($relativeZip);
        $zipDir = dirname($absoluteZip);
        if (! is_dir($zipDir)) {
            mkdir($zipDir, 0755, true);
        }

        $zip = new ZipArchive;
        if ($zip->open($absoluteZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Could not create offline pack ZIP.');
        }
        $this->addDirToZip($zip, $workDir, '');
        $zip->close();

        $byteSize = filesize($absoluteZip) ?: 0;
        $this->rrmdir($workDir);

        DB::transaction(function () use ($pack, $relativeZip, $byteSize, $license, $devices, $packId) {
            $pack->refresh();
            $pack->status = 'ready';
            $pack->storage_path = $relativeZip;
            $pack->byte_size = $byteSize;
            $pack->error = null;
            $pack->save();

            foreach ($devices as $device) {
                if ($device->vault_activated_at === null) {
                    $device->vault_activated_at = now();
                    $device->save();
                    LicenseUsageLogger::record(
                        $license->id,
                        $device->id,
                        $device->k_sign,
                        LicenseUsageLogger::EVENT_VAULT_ACCESS_GRANTED,
                        'offline_pack:'.$packId
                    );
                }
                LicenseUsageLogger::record(
                    $license->id,
                    $device->id,
                    $device->k_sign,
                    LicenseUsageLogger::EVENT_OFFLINE_PACK_ISSUED,
                    'pack_id='.$packId.';mode='.$pack->validity_mode
                );
            }
        });
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function collectAndEncryptMedia(License $license, string $mediaKey, string $mediaRoot): array
    {
        $index = [];
        $license->loadMissing('courses');

        foreach ($license->courses as $course) {
            $courseId = (int) $course->id;
            $rows = DB::table('course_resources as cr')
                ->join('resources as r', 'cr.resource_id', '=', 'r.id')
                ->where('cr.course_id', $courseId)
                ->select('r.*', 'cr.course_id')
                ->get();

            foreach ($rows as $resource) {
                $type = (string) ($resource->type ?? 'Other');
                if (strcasecmp($type, 'Text') === 0) {
                    continue;
                }

                $entries = $this->resolveResourceFiles($resource, $type, $courseId);
                foreach ($entries as $entry) {
                    $rel = $entry['relative'];
                    $absSource = $entry['absolute'];
                    if (! is_file($absSource)) {
                        throw new \RuntimeException("Missing media file for resource {$resource->id}: {$absSource}");
                    }
                    $plain = file_get_contents($absSource);
                    if ($plain === false) {
                        throw new \RuntimeException("Could not read media for resource {$resource->id}");
                    }
                    $encrypted = $this->packCrypto->encryptMediaFile($plain, $mediaKey);
                    $dest = $mediaRoot.'/'.$rel;
                    $destDir = dirname($dest);
                    if (! is_dir($destDir)) {
                        mkdir($destDir, 0755, true);
                    }
                    file_put_contents($dest, $encrypted);

                    $index[] = [
                        'course_id' => $courseId,
                        'resource_id' => (int) $resource->id,
                        'name' => $resource->name ?? ('resource_'.$resource->id),
                        'type' => $type,
                        'relative_path' => 'media/'.$rel,
                        'file_name' => $entry['file_name'],
                        'is_slide' => (bool) ($entry['is_slide'] ?? false),
                        'size_bytes' => strlen($encrypted),
                        'sha256' => hash('sha256', $encrypted),
                    ];
                }
            }
        }

        return $index;
    }

    /**
     * @param  object  $resource
     * @return list<array{absolute: string, relative: string, file_name: string, is_slide?: bool}>
     */
    private function resolveResourceFiles(object $resource, string $type, int $courseId): array
    {
        $rid = (int) $resource->id;

        if (strcasecmp($type, 'Youtube') === 0) {
            $path = $resource->vault_video_path ?? null;
            if (empty($path) || ! Storage::disk('local')->exists($path)) {
                throw new \RuntimeException("YouTube vault file missing for resource {$rid}");
            }
            $abs = Storage::disk('local')->path($path);
            $ext = pathinfo($abs, PATHINFO_EXTENSION) ?: 'mp4';
            $fileName = $rid.'_video.'.$ext;

            return [[
                'absolute' => $abs,
                'relative' => 'course_'.$courseId.'/resources/'.$fileName,
                'file_name' => $fileName,
                'is_slide' => false,
            ]];
        }

        if (strcasecmp($type, 'Slide Document') === 0) {
            $folderFs = public_path('uploads/course_resource/'.$resource->file_name);
            $zipFs = public_path('uploads/course_resource/'.$resource->file_name.'.zip');
            try {
                $this->slideZipPackagingService->zipSlideFolder($folderFs, $zipFs);
            } catch (\Throwable $e) {
                Log::warning('Offline pack slide zip failed', ['resource_id' => $rid, 'message' => $e->getMessage()]);
            }
            if (is_file($zipFs)) {
                $fileName = $rid.'_slides.zip';

                return [[
                    'absolute' => $zipFs,
                    'relative' => 'course_'.$courseId.'/resources/'.$fileName,
                    'file_name' => $fileName,
                    'is_slide' => true,
                ]];
            }
            if (is_dir($folderFs)) {
                $out = [];
                $realFolder = realpath($folderFs);
                if ($realFolder === false) {
                    throw new \RuntimeException("Slide folder missing for resource {$rid}");
                }
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($folderFs, \RecursiveDirectoryIterator::SKIP_DOTS)
                );
                foreach ($files as $file) {
                    if (! $file->isFile()) {
                        continue;
                    }
                    $relInside = str_replace('\\', '/', substr($file->getPathname(), strlen($realFolder) + 1));
                    $out[] = [
                        'absolute' => $file->getPathname(),
                        'relative' => 'course_'.$courseId.'/slides/slide_'.$rid.'/'.$relInside,
                        'file_name' => basename($relInside),
                        'is_slide' => true,
                    ];
                }

                return $out;
            }
            throw new \RuntimeException("Slide media missing for resource {$rid}");
        }

        $fileName = $resource->file_name ?? null;
        if (empty($fileName)) {
            throw new \RuntimeException("No file_name for resource {$rid}");
        }
        $abs = public_path('uploads/course_resource/'.$fileName);
        if (! is_file($abs)) {
            throw new \RuntimeException("Upload missing for resource {$rid}: {$fileName}");
        }
        $safe = $rid.'_'.basename($fileName);

        return [[
            'absolute' => $abs,
            'relative' => 'course_'.$courseId.'/resources/'.$safe,
            'file_name' => $safe,
            'is_slide' => false,
        ]];
    }

    private function addDirToZip(ZipArchive $zip, string $dir, string $prefix): void
    {
        $items = scandir($dir) ?: [];
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir.DIRECTORY_SEPARATOR.$item;
            $zipPath = $prefix === '' ? $item : $prefix.'/'.$item;
            if (is_dir($path)) {
                $zip->addEmptyDir($zipPath);
                $this->addDirToZip($zip, $path, $zipPath);
            } else {
                $zip->addFile($path, $zipPath);
            }
        }
    }

    private function rrmdir(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }
        $items = scandir($dir) ?: [];
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir.DIRECTORY_SEPARATOR.$item;
            if (is_dir($path)) {
                $this->rrmdir($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }
}

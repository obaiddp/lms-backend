<?php

namespace App\Services;

use App\Models\License;
use App\Models\LicenseDevice;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VaultOfflineGrantService
{
    /**
     * Build a signed RS256 JWT for offline entitlement (online activate/sync), or null if signing is not configured.
     */
    public function issueGrant(
        License $license,
        LicenseDevice $device,
        string $normalizedDeviceKSign
    ): ?string {
        $privatePem = $this->loadPrivateKeyPem();
        if ($privatePem === null || $privatePem === '') {
            Log::warning('VaultOfflineGrantService: no private key configured; vault_offline_grant omitted.');

            return null;
        }

        $licenseEnd = Carbon::parse($license->end_date)->utc();
        $serverNow = Carbon::now()->utc();
        $maxOffline = (int) config('vault.offline_max_seconds', 60 * 60 * 24 * 60);
        $offlineCap = $serverNow->copy()->addSeconds($maxOffline);
        $offlineValidNotAfter = $licenseEnd->lt($offlineCap) ? $licenseEnd : $offlineCap;

        $nowTs = $serverNow->getTimestamp();
        $payload = [
            'iss' => (string) config('app.url', 'cef'),
            'iat' => $nowTs,
            'nbf' => $nowTs - 120,
            'exp' => $offlineValidNotAfter->getTimestamp(),
            'jti' => (string) Str::uuid(),
            'lid' => (int) $license->id,
            'ldid' => (int) $device->id,
            'ksig' => $normalizedDeviceKSign,
            'lend' => $licenseEnd->getTimestamp(),
            'srv' => $nowTs,
            'ovnfa' => $offlineValidNotAfter->getTimestamp(),
            'gv' => 1,
        ];

        return $this->encode($payload, $privatePem);
    }

    /**
     * Offline content pack grant: exp/ovnfa = license end; optional duration-from-activation claims.
     *
     * @param  'absolute'|'duration'  $validityMode
     */
    public function issuePackGrant(
        License $license,
        LicenseDevice $device,
        string $normalizedDeviceKSign,
        string $packId,
        string $validityMode,
        ?int $durationSeconds = null
    ): ?string {
        $privatePem = $this->loadPrivateKeyPem();
        if ($privatePem === null || $privatePem === '') {
            Log::warning('VaultOfflineGrantService: no private key configured; pack grant omitted.');

            return null;
        }

        $licenseStart = Carbon::parse($license->start_date)->utc();
        $licenseEnd = Carbon::parse($license->end_date)->utc();
        $serverNow = Carbon::now()->utc();
        $nowTs = $serverNow->getTimestamp();
        $lendTs = $licenseEnd->getTimestamp();

        $nbfTs = max($licenseStart->getTimestamp(), $nowTs - 120);

        $payload = [
            'iss' => (string) config('app.url', 'cef'),
            'iat' => $nowTs,
            'nbf' => $nbfTs,
            'exp' => $lendTs,
            'jti' => (string) Str::uuid(),
            'lid' => (int) $license->id,
            'ldid' => (int) $device->id,
            'ksig' => $normalizedDeviceKSign,
            'lend' => $lendTs,
            'srv' => $nowTs,
            'ovnfa' => $lendTs,
            'gv' => 2,
            'vm' => $validityMode,
            'pack' => $packId,
        ];

        if ($validityMode === 'duration' && $durationSeconds !== null && $durationSeconds > 0) {
            $payload['adur'] = (int) $durationSeconds;
        }

        return $this->encode($payload, $privatePem);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function encode(array $payload, string $privatePem): ?string
    {
        try {
            JWT::$leeway = 120;

            return JWT::encode($payload, $privatePem, 'RS256', (string) config('vault.offline_jwt_kid', 'vault-1'));
        } catch (\Throwable $e) {
            Log::error('VaultOfflineGrantService: encode failed', ['message' => $e->getMessage()]);

            return null;
        }
    }

    private function loadPrivateKeyPem(): ?string
    {
        $file = (string) config('vault.offline_jwt_private_key_file', '');
        if ($file !== '') {
            $path = $file;
            if (! is_file($path)) {
                $path = base_path($file);
            }
            if (is_file($path) && is_readable($path)) {
                $contents = file_get_contents($path);

                return $contents !== false ? $contents : null;
            }
        }

        $inline = (string) config('vault.offline_jwt_private_key', '');
        if ($inline !== '') {
            return str_replace('\\n', "\n", $inline);
        }

        return null;
    }
}

<?php

namespace App\Services;

use App\Models\LicenseDeviceUsageEvent;
use Illuminate\Support\Facades\Log;

class LicenseUsageLogger
{
    public const EVENT_DEVICE_REGISTERED_ADMIN = 'device_registered_admin';

    public const EVENT_VAULT_ACCESS_GRANTED = 'vault_access_granted';

    public const EVENT_VAULT_LOGOUT = 'vault_logout';

    public const EVENT_ACCESS_REVOKED = 'access_revoked';

    public const EVENT_LICENSE_ACTIVATED = 'license_activated';

    public const EVENT_LICENSE_DEACTIVATED = 'license_deactivated';

    public const EVENT_OFFLINE_PACK_ISSUED = 'offline_pack_issued';

    public static function record(
        int $licenseId,
        ?int $licenseDeviceId,
        ?string $deviceKey,
        string $event,
        ?string $detail = null
    ): void {
        try {
            LicenseDeviceUsageEvent::query()->create([
                'license_id' => $licenseId,
                'license_device_id' => $licenseDeviceId,
                'device_key' => $deviceKey !== null && $deviceKey !== '' ? substr($deviceKey, 0, 512) : null,
                'event' => substr($event, 0, 64),
                'detail' => $detail !== null && $detail !== '' ? substr($detail, 0, 10000) : null,
            ]);
        } catch (\Throwable $e) {
            Log::warning('LicenseUsageLogger: '.$e->getMessage());
        }
    }
}

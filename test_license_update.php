<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$license = App\Models\License::find(7);
if (! $license) {
    echo "license 7 not found\n";
    exit(1);
}

$service = app(App\Services\VaultLicenseAdminService::class);
$data = [
    'vault_user_id' => 1,
    'license_key' => 'LIC-MUUREDCU-8896',
    'start_date' => '2026-06-25',
    'expiry_date' => '2026-07-26',
    'status' => 'active',
    'notes' => null,
    'courses' => [43],
    'devices' => [['name' => 'Codir Test', 'k_sign' => '{D4EB62C0-DED8-459E-AB4A-13CFB6C5581D}']],
];

try {
    $result = $service->updateLicense($license, $data);
    echo 'OK id='.$result->id."\n";
} catch (Throwable $e) {
    echo get_class($e).': '.$e->getMessage()."\n";
    echo $e->getTraceAsString()."\n";
    exit(1);
}

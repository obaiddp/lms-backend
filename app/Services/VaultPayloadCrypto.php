<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;

class VaultPayloadCrypto
{
    public function encryptionKeyBinary(): string
    {
        $key = Config::get('app.asset_encryption_key');
        if ($key === null || $key === '') {
            throw new \RuntimeException('app.asset_encryption_key is not configured.');
        }

        return base64_decode((string) $key, true) ?: throw new \RuntimeException('app.asset_encryption_key must be valid base64.');
    }

    public function encrypt(string $data): string
    {
        $key = $this->encryptionKeyBinary();
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        if ($encrypted === false) {
            throw new \RuntimeException('openssl_encrypt failed for vault payload.');
        }

        return base64_encode($iv . $encrypted);
    }
}

<?php

namespace App\Services;

/**
 * Pack media key wrap + CEFP file encryption for offline content packs.
 */
class VaultOfflinePackCrypto
{
    public const MEDIA_MAGIC = 'CEFP';

    public const WRAP_SALT = 'cef-vault-pack-wrap-v1';

    public function __construct(
        protected VaultPayloadCrypto $payloadCrypto
    ) {}

    public function deriveWrapKey(int $licenseId, string $packId): string
    {
        $ikm = $this->payloadCrypto->encryptionKeyBinary();

        return hash_hkdf(
            'sha256',
            $ikm,
            32,
            'license:'.$licenseId.':pack:'.$packId,
            self::WRAP_SALT
        );
    }

    public function wrapMediaKey(string $mediaKey32, int $licenseId, string $packId): string
    {
        $wrapKey = $this->deriveWrapKey($licenseId, $packId);
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($mediaKey32, 'aes-256-cbc', $wrapKey, OPENSSL_RAW_DATA, $iv);
        if ($encrypted === false) {
            throw new \RuntimeException('Failed to wrap pack media key.');
        }

        return base64_encode($iv.$encrypted);
    }

    public function encryptMediaFile(string $plaintext, string $mediaKey32): string
    {
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($plaintext, 'aes-256-cbc', $mediaKey32, OPENSSL_RAW_DATA, $iv);
        if ($encrypted === false) {
            throw new \RuntimeException('Failed to encrypt pack media file.');
        }

        return self::MEDIA_MAGIC.$iv.$encrypted;
    }
}

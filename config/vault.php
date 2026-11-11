<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Max offline interval (seconds)
    |--------------------------------------------------------------------------
    | Upper bound for vault_offline_grant "offline_valid_not_after" after server_time.
    | Capped by license end date.
    */
    'offline_max_seconds' => (int) env('VAULT_OFFLINE_MAX_SECONDS', 60 * 60 * 24 * 60),

    /*
    |--------------------------------------------------------------------------
    | RSA private key PEM (RS256) for signing vault_offline_grant JWTs.
    | Prefer env VAULT_OFFLINE_JWT_PRIVATE_KEY_FILE pointing to a PEM file path,
    | or set VAULT_OFFLINE_JWT_PRIVATE_KEY to the literal PEM (use \n for newlines).
    */
    'offline_jwt_private_key_file' => env(
        'VAULT_OFFLINE_JWT_PRIVATE_KEY_FILE',
        storage_path('app/keys/vault-offline-jwt-rsa-private.pem')
    ),
    'offline_jwt_private_key' => env('VAULT_OFFLINE_JWT_PRIVATE_KEY', ''),

    'offline_jwt_kid' => env('VAULT_OFFLINE_JWT_KID', 'vault-1'),
];

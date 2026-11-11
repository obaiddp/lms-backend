<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],


    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URL'),
        'scopes' => [
            'https://www.googleapis.com/auth/calendar',
            'https://www.googleapis.com/auth/calendar.events',
        ],
        'access_type' => 'offline', // 👈 Forces Google to send a refresh token
        'prompt' => 'consent', // 👈 Forces re-authentication to ensure refresh token is sent
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URL'),
    ],

    'twitter' => [
        'client_id' => env('TWITTER_CLIENT_ID'),
        'client_secret' => env('TWITTER_CLIENT_SECRET'),
        'redirect' => env('TWITTER_REDIRECT_URL'),
    ],

    'frontend_url' => env('FRONTEND_URL', env('APP_URL', '/')),

    'donation_frontend_url' => env('DONATION_FRONTEND_URL', 'https://cef.org.pk'),

    'donation_faysal' => [
        'success_url' => env('DONATION_FAYSAL_SUCCESS_URL') ?? (rtrim(env('DONATION_FRONTEND_URL', 'https://cef.org.pk'), '/') . '/payment-status/success/{session_id}'),
        'failure_url' => env('DONATION_FAYSAL_FAILURE_URL') ?? (rtrim(env('DONATION_FRONTEND_URL', 'https://cef.org.pk'), '/') . '/payment-status/fail/{session_id}'),
    ],

    'donation_webhook' => [
        'donation_created_url' => env('DONATION_CREATED_WEBHOOK_URL', ''),
        'regular_giving_created_url' => env('REGULAR_GIVING_CREATED_WEBHOOK_URL', ''),
    ],
];

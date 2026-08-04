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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    | The People-ERP beneficiary portal. "url" is where the public Apply Now
    | button sends applicants; "secret" is the shared key the portal signs
    | its pushes to /api/v1/bridge/* with, and must match LEGACY_BRIDGE_SECRET
    | on the portal's API. Leaving the secret unset disables those endpoints.
    */
    'portal' => [
        'url' => env('PORTAL_URL', 'https://peoples-foundation.netlify.app/login'),
    ],

    'bridge' => [
        'secret' => env('BRIDGE_SHARED_SECRET'),
    ],

];

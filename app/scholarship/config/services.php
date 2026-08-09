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
    | Where the site's Apply Now button sends applicants: the login page of the
    | People's Foundation beneficiary portal, which is where applications are
    | now submitted and worked. Read here rather than called from the view,
    | because deployment runs config:cache and env() returns null outside a
    | config file once that cache exists. No default — the value belongs to the
    | environment, so a missing one should be obvious rather than silently
    | replaced by a stale URL baked into the code.
    */
    'portal' => [
        'url' => env('PORTAL_URL'),
    ],

];

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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'hotmart' => [
        'api_base_url' => env('HOTMART_API_BASE_URL', 'https://developers.hotmart.com'),
        'auth_base_url' => env('HOTMART_AUTH_BASE_URL', 'https://api-sec-vlc.hotmart.com'),
        'client_id' => env('HOTMART_CLIENT_ID'),
        'client_secret' => env('HOTMART_CLIENT_SECRET'),
        'basic_token' => env('HOTMART_BASIC_TOKEN'),
        'hottok' => env('HOTMART_HOTTOK', env('Hotmart_Hottok')),
        'timeout' => env('HOTMART_TIMEOUT', 30),
    ],

    'botconversa' => [
        'verification_webhook_url' => env('BOTCONVERSA_VERIFICATION_WEBHOOK_URL'),
        'verification_request_phone' => env('BOTCONVERSA_VERIFICATION_REQUEST_PHONE'),
    ],

    'adsense' => [
        'account_id' => env('GOOGLE_ADSENSE_ACCOUNT_ID', 'ca-pub-9796869151117705'),
    ],

    'typebot' => [
        'course_popup_id' => env('TYPEBOT_COURSE_POPUP_ID', 'my-typebot-t0kedpk'),
        'api_host' => env('TYPEBOT_API_HOST', 'https://typebot.3f7.org'),
    ],

];

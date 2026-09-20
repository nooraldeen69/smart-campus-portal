<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services (Laravel defaults - keep or remove as needed)
    |--------------------------------------------------------------------------
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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
    |--------------------------------------------------------------------------
    | Smart Campus Portal
    |--------------------------------------------------------------------------
    */

    // University Identity Provider (OAuth2 Authorization Code + PKCE) - WBS 1.2.1
    'sso' => [
        'client_id'     => env('SSO_CLIENT_ID'),
        'client_secret' => env('SSO_CLIENT_SECRET'),
        'redirect'      => env('SSO_REDIRECT_URI'),
        'authorize_url' => env('SSO_AUTHORIZE_URL'),
        'token_url'     => env('SSO_TOKEN_URL'),
        'userinfo_url'  => env('SSO_USERINFO_URL'),
        'logout_url'    => env('SSO_LOGOUT_URL'),
        'scopes'        => env('SSO_SCOPES', 'openid profile email'),
    ],

    // Legacy back-end systems behind the API gateway - WBS 1.3.2
    'sis'     => ['endpoint' => env('SIS_API_ENDPOINT')],
    'lms'     => ['endpoint' => env('LMS_API_ENDPOINT')],
    'library' => ['endpoint' => env('LIBRARY_API_ENDPOINT')],

    'campus' => [
        'cache_ttl'    => (int) env('CAMPUS_CACHE_TTL', 3600), // seconds
        'http_timeout' => (int) env('CAMPUS_HTTP_TIMEOUT', 2), // seconds (OBJ-2)
    ],

];

<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Allowed Referers
    |--------------------------------------------------------------------------
    |
    | This configuration defines which domains are allowed to embed your
    | magazine subscription widget. Requests from domains not in this list
    | will be rejected with a 403 Forbidden response.
    |
    | Supported formats:
    | - Exact domain: 'example.com'
    | - Wildcard subdomain: '*.example.com'
    | - Development domains: 'localhost', '127.0.0.1'
    |
    */

    'allowed_referers' => array_filter(
        array_map('trim', explode(',', env('ALLOWED_REFERERS', ''))),
        function ($domain) {
            return !empty($domain);
        }
    ),

    /*
    |--------------------------------------------------------------------------
    | Development Mode
    |--------------------------------------------------------------------------
    |
    | When in development mode (APP_ENV=local), referrer validation can be
    | bypassed for easier testing. Set to false to enforce validation
    | even in development.
    |
    */

    'bypass_in_development' => env('EMBED_BYPASS_IN_DEV', env('APP_ENV') === 'local'),

    /*
    |--------------------------------------------------------------------------
    | Log Security Violations
    |--------------------------------------------------------------------------
    |
    | Enable logging of failed referrer validation attempts for security
    | monitoring and debugging purposes.
    |
    */

    'log_violations' => env('APP_ENV') !== 'local',
];
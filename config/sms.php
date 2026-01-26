<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SMS Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for SMS service integration
    |
    */

    'enabled' => env('SMS_ENABLED', false),
    'api_key' => env('SMS_NET_BD_API_KEY'),
    'api_url' => env('SMS_API_URL', 'https://api.sms.net.bd'),
];

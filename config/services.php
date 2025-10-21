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
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'novocom' => [
        'base_url' => env('NOVOCOM_BASE_URL', 'https://sms.novocom-bd.com/api/v2'),
        'api_key' => env('NOVOCOM_API_KEY'),
        'client_id' => env('NOVOCOM_CLIENT_ID'),
        'sender_id' => env('NOVOCOM_SENDER_ID'),
        'timeout' => env('NOVOCOM_TIMEOUT', 30),
    ],

];

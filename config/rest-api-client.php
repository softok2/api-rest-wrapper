<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Rest API  Url
    |--------------------------------------------------------------------------
    |
    | This value is the url of your rest api.
    |
    */

    'url' => env('REST_API_URL', 'http://localhost:8000/api/v1/'),

    /*
     |--------------------------------------------------------------------------
     | Rest API  Secret token to perform requests
     |--------------------------------------------------------------------------
     |
     | This value is the secret token of your rest api.
     |
     */

    'secret' => env('REST_API_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Rest API  timeout requests in seconds
    |--------------------------------------------------------------------------
    |
    | This value represent  the timeout in seconds  used to every request.
    |
    */

    'timeout' => (int) env('REST_API_TIMEOUT', 300),

    /*
    |--------------------------------------------------------------------------
    | Rest API  resources path
    |--------------------------------------------------------------------------
    |
    | This value represent the path to put the generated resources api classes.
    |
    */
    'resources_path' => app_path('Services/API'),

    /*
    |--------------------------------------------------------------------------
    | Rest API  resources namespace
    |--------------------------------------------------------------------------
    |
    | This value represent the namespace for the generated resources api classes.
    |
    */
    'namespace' => 'App\\Services\\API\\',
];

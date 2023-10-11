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

    'url' => (string) env('REST_API_URL', 'localhost:8000'),

    /*
     |--------------------------------------------------------------------------
     | Rest API auth handler (jwt, basic or null)
     |--------------------------------------------------------------------------
     |
     | This value is the secret token of your rest api.
     |
     */

    'auth_handler' => (string) env('REST_API_AUTH_HANDLER'),

    /*
    |--------------------------------------------------------------------------
    | Rest API  timeout requests in seconds
    |--------------------------------------------------------------------------
    |
    | This value represent  the timeout in seconds  used to every request.
    |
    */

    'timeout' => env('REST_API_TIMEOUT', 300),

    /*
    |--------------------------------------------------------------------------
    | Rest API  resources path
    |--------------------------------------------------------------------------
    |
    | This value represent the path to put the generated resources api classes.
    |
    */
    'resources_path' => app_path('Services/ApiResources'),

    /*
    |--------------------------------------------------------------------------
    | Rest API  resources namespace
    |--------------------------------------------------------------------------
    |
    | This value represent the namespace for the generated resources api classes.
    |
    */
    'namespace' => 'App\\Services\\ApiResources\\',

    /*
      |--------------------------------------------------------------------------
      | Rest API  default exception key
      |--------------------------------------------------------------------------
      |
      | This value represent the default exception key.
      | This key is used to get the exception message from the response.
      |
      */
    'default_exception_key' => 'message',

    /*
      |--------------------------------------------------------------------------
      | Auth handlers configurations ...
      |--------------------------------------------------------------------------
      |
      | Only support jwt and basic auth options.
      | This value represent the configurations for the auth handlers.
      |
      */
    'auth_handler_options' => [
        'jwt' => [
            'token' => env('REST_API_TOKEN', ''),
        ],
        'basic' => [
            'username' => env('REST_API_BASIC_USERNAME', 'username'),
            'password' => env('REST_API_BASIC_PASSWORD', 'password'),
        ],
        null => [],
    ],
];

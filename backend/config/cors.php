<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Paths
    |--------------------------------------------------------------------------
    |
    | Specify the paths that should respond to CORS requests. For example,
    | you might want to allow CORS requests to all routes starting with
    | "api/" and the "sanctum/csrf-cookie" endpoint for Sanctum.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Methods
    |--------------------------------------------------------------------------
    |
    | Specify the HTTP methods that are allowed during CORS requests.
    | '*' means all methods are allowed. For better security, you
    | should specify only the methods you actually use.
    |
    */

    'allowed_methods' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    |
    | Specify the origins that are allowed to make requests to your
    | application. You can also use '*' to allow all origins.
    |
    */

    'allowed_origins' => ['http://localhost:3000'], // Replace with your frontend's URL

    /*
    |--------------------------------------------------------------------------
    | Allowed Headers
    |--------------------------------------------------------------------------
    |
    | Specify the headers that are allowed during a CORS request.
    | '*' means all headers are allowed.
    |
    */

    'allowed_headers' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Exposed Headers
    |--------------------------------------------------------------------------
    |
    | Specify the headers that should be exposed in the response.
    |
    */

    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | Max Age
    |--------------------------------------------------------------------------
    |
    | Specify how long the results of a preflight request can be cached.
    |
    */

    'max_age' => 0,

    /*
    |--------------------------------------------------------------------------
    | Supports Credentials
    |--------------------------------------------------------------------------
    |
    | Indicate whether cookies should be sent with CORS requests.
    | Set this to true if you're using Sanctum with stateful requests.
    |
    */

    'supports_credentials' => true,

];

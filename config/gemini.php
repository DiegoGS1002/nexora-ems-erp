<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Gemini API Key
    |--------------------------------------------------------------------------
    |
    | Here you may specify your Gemini API Key. This will be used to authenticate
    | with the Gemini API - you can find your API key on your Google AI Studio.
    |
    */

    'api_key' => env('GEMINI_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Default Model
    |--------------------------------------------------------------------------
    |
    | The default Gemini model to use for AI interactions.
    |
    */

    'model' => env('GEMINI_MODEL', 'gemini-2.0-flash'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout may be used to specify the maximum number of seconds to wait
    | for a response. By default, the client will time out after 60 seconds.
    |
    */

    'request_timeout' => env('GEMINI_REQUEST_TIMEOUT', 60),

    /*
    |--------------------------------------------------------------------------
    | Temperature
    |--------------------------------------------------------------------------
    |
    | Controls randomness in the model's responses. Lower values (0.1-0.3) make
    | responses more deterministic, while higher values (0.7-1.0) increase creativity.
    |
    */

    'temperature' => env('GEMINI_TEMPERATURE', 0.7),

    /*
    |--------------------------------------------------------------------------
    | Max Tokens
    |--------------------------------------------------------------------------
    |
    | The maximum number of tokens to generate in the completion.
    |
    */

    'max_tokens' => env('GEMINI_MAX_TOKENS', 2048),

];


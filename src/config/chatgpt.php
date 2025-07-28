<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OpenAI API Key
    |--------------------------------------------------------------------------
    |
    | Your OpenAI API key. You can get it from https://platform.openai.com/api-keys
    |
    */
    'api_key' => env('OPENAI_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for OpenAI API requests.
    |
    */
    'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),

    /*
    |--------------------------------------------------------------------------
    | Default Model
    |--------------------------------------------------------------------------
    |
    | The default model to use for requests.
    | Available: gpt-3.5-turbo, gpt-4, gpt-4-turbo, gpt-4-vision-preview
    |
    */
    'default_model' => env('OPENAI_DEFAULT_MODEL', 'gpt-3.5-turbo'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout for API requests in seconds.
    |
    */
    'timeout' => env('OPENAI_TIMEOUT', 60),

    /*
    |--------------------------------------------------------------------------
    | Max Tokens
    |--------------------------------------------------------------------------
    |
    | The maximum number of tokens to generate in the completion.
    |
    */
    'max_tokens' => env('OPENAI_MAX_TOKENS', 1000),

    /*
    |--------------------------------------------------------------------------
    | Temperature
    |--------------------------------------------------------------------------
    |
    | Controls randomness. Lower values make the model more deterministic.
    | Range: 0.0 to 2.0
    |
    */
    'temperature' => env('OPENAI_TEMPERATURE', 0.7),

    /*
    |--------------------------------------------------------------------------
    | Top P
    |--------------------------------------------------------------------------
    |
    | An alternative to sampling with temperature.
    | Range: 0.0 to 1.0
    |
    */
    'top_p' => env('OPENAI_TOP_P', 1.0),

    /*
    |--------------------------------------------------------------------------
    | Frequency Penalty
    |--------------------------------------------------------------------------
    |
    | Reduces the likelihood of repeating the same line.
    | Range: -2.0 to 2.0
    |
    */
    'frequency_penalty' => env('OPENAI_FREQUENCY_PENALTY', 0.0),

    /*
    |--------------------------------------------------------------------------
    | Presence Penalty
    |--------------------------------------------------------------------------
    |
    | Reduces the likelihood of repeating any token.
    | Range: -2.0 to 2.0
    |
    */
    'presence_penalty' => env('OPENAI_PRESENCE_PENALTY', 0.0),

    /*
    |--------------------------------------------------------------------------
    | Retry Attempts
    |--------------------------------------------------------------------------
    |
    | Number of retry attempts in case of failure.
    | Set to 0 to disable retries.
    |
    */
    'retry_attempts' => env('OPENAI_RETRY_ATTEMPTS', 2),

    /*
    |--------------------------------------------------------------------------
    | Retry Delay
    |--------------------------------------------------------------------------
    |
    | Delay between retry attempts in seconds.
    |
    */
    'retry_delay' => env('OPENAI_RETRY_DELAY', 1),

    /*
    |--------------------------------------------------------------------------
    | Rate Limit
    |--------------------------------------------------------------------------
    |
    | Maximum number of requests per minute.
    | Set to 0 for unlimited requests.
    |
    */
    'rate_limit' => env('OPENAI_RATE_LIMIT', 60),

    /*
    |--------------------------------------------------------------------------
    | User Agent
    |--------------------------------------------------------------------------
    |
    | The user agent string for API requests.
    |
    */
    'user_agent' => env('OPENAI_USER_AGENT', 'Sysborg-ChatGPT-Laravel/1.0'),
];
<?php

return [
    'newsapi' => [
        'key' => env('NEWS_API_KEY'),
        'base_url' => rtrim(env('NEWS_API_BASE_URL', ''), '/'),
    ],
    'theguardian' => [
        'key' => env('THE_GUARDIAN_API_KEY'),
        'base_url' => rtrim(env('THE_GUARDIAN_API_BASE_URL', ''), '/'),
    ],
    'nytimes' => [
        'key' => env('NYTIMES_API_KEY'),
        'base_url' => rtrim(env('NYTIMES_API_BASE_URL', ''), '/'),
    ],
];

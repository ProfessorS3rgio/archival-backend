<?php

return [
    'enabled' => env('ARCHIVE_CHAT_ENABLED', false),
    'public_enabled' => env('ARCHIVE_CHAT_PUBLIC_ENABLED', false),
    'public_hosts' => array_values(array_filter(array_map('trim', explode(',', env('ARCHIVE_CHAT_PUBLIC_HOSTS', 'localhost,127.0.0.1'))))),
    'public_rate_limit' => (int) env('ARCHIVE_CHAT_PUBLIC_RATE_LIMIT', 30),
    'source' => env('ARCHIVE_CHAT_SOURCE', 'api'),
    'source_url' => env('ARCHIVE_CHAT_SOURCE_URL'),
    'source_token' => env('ARCHIVE_CHAT_SOURCE_TOKEN'),
    'index_path' => storage_path('app/archive-chat'),
];

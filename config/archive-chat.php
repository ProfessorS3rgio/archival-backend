<?php

return [
    'enabled' => env('ARCHIVE_CHAT_ENABLED', false),
    'source' => env('ARCHIVE_CHAT_SOURCE', 'api'),
    'source_url' => env('ARCHIVE_CHAT_SOURCE_URL'),
    'source_token' => env('ARCHIVE_CHAT_SOURCE_TOKEN'),
    'index_path' => storage_path('app/archive-chat'),
];

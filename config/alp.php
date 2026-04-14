<?php

declare(strict_types=1);
use App\Pipelines\Steps\DetectTables;
use App\Pipelines\Steps\ExtractText;
use App\Pipelines\Steps\StoreDocument;

return [
    'default_pipeline' => 'extract-basic',
    'queue' => env('ALP_QUEUE', 'default'),
    'ai' => [
        'default' => env('ALP_AI_PROVIDER', 'local'),
        'providers' => [
            'local' => true,
        ],
    ],
    'pipelines' => [
        'extract-basic' => [
            ExtractText::class,
            DetectTables::class,
            StoreDocument::class,
        ],
    ],
    'storage' => [
        'base_path' => env('ALP_STORAGE_PATH', '/tmp/alp'),
        'raw_disk' => env('ALP_RAW_DISK', 'local'),
        'processed_disk' => env('ALP_PROCESSED_DISK', 'local'),
    ],
];

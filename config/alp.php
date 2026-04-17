<?php

declare(strict_types=1);
use App\Pipelines\Steps\DetectTables;
use App\Pipelines\Steps\ExtractText;
use App\Pipelines\Steps\StoreDocument;

return [
    'default_pipeline' => 'extract-basic',
    'queue' => env('ALP_QUEUE', 'default'),
    'queues' => [
        'high' => env('ALP_QUEUE_HIGH', 'alp-high'),
        'default' => env('ALP_QUEUE_DEFAULT', 'alp-default'),
        'ai' => env('ALP_QUEUE_AI', 'alp-ai'),
        'index' => env('ALP_QUEUE_INDEX', 'alp-index'),
        'pipelines' => env('ALP_QUEUE_PIPELINES', env('ALP_QUEUE', 'alp-default')),
    ],
    'pipeline' => [
        'execution_mode' => env('ALP_PIPELINE_EXECUTION_MODE', 'sync'),
    ],
    'ai' => [
        'default' => env('ALP_AI_PROVIDER', 'local'),
        'providers' => [
            'local' => true,
            'openai' => true,
            'anthropic' => true,
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

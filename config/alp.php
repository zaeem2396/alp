<?php

declare(strict_types=1);

return [
    'default_pipeline' => 'extract-basic',
    'queue' => env('ALP_QUEUE', 'default'),
];

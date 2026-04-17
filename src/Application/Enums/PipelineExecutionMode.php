<?php

declare(strict_types=1);

namespace App\Application\Enums;

enum PipelineExecutionMode: string
{
    case Sync = 'sync';
    case Queue = 'queue';
    case Auto = 'auto';
}

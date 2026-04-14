<?php

declare(strict_types=1);

namespace App\Facades;

use App\Pipelines\PipelineManager;
use Illuminate\Support\Facades\Facade;

final class Pipeline extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return PipelineManager::class;
    }
}

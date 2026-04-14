<?php

declare(strict_types=1);

namespace App\Pipelines\Steps;

use App\Pipelines\Contracts\PipelineStepInterface;

final class DetectTables implements PipelineStepInterface
{
    public function handle(array $context): array
    {
        $context['tables'] = [];

        return $context;
    }
}

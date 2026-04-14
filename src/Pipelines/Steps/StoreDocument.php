<?php

declare(strict_types=1);

namespace App\Pipelines\Steps;

use App\Pipelines\Contracts\PipelineStepInterface;

final class StoreDocument implements PipelineStepInterface
{
    public function handle(array $context): array
    {
        $context['stored'] = true;

        return $context;
    }
}

<?php

declare(strict_types=1);

namespace App\Pipelines;

use App\Pipelines\Contracts\PipelineStepInterface;

final class PipelineManager
{
    /**
     * @param  list<PipelineStepInterface>  $steps
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public function run(array $steps, array $context = []): array
    {
        foreach ($steps as $step) {
            $context = $step->handle($context);
        }

        return $context;
    }
}

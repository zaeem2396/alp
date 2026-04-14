<?php

declare(strict_types=1);

namespace App\Pipelines\Steps;

use App\Pipelines\Contracts\PipelineStepInterface;

final class AISummarize implements PipelineStepInterface
{
    public function handle(array $context): array
    {
        $context['summary'] = 'summary placeholder';

        return $context;
    }
}

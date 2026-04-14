<?php

declare(strict_types=1);

namespace App\Pipelines\Steps;

use App\Pipelines\Contracts\PipelineStepInterface;

final class ExtractText implements PipelineStepInterface
{
    public function handle(array $context): array
    {
        $context['text'] = 'text extraction placeholder';

        return $context;
    }
}

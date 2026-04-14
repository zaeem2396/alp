<?php

declare(strict_types=1);

namespace App\Pipelines\Contracts;

interface PipelineStepInterface
{
    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public function handle(array $context): array;
}

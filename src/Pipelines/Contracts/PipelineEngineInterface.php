<?php

declare(strict_types=1);

namespace App\Pipelines\Contracts;

interface PipelineEngineInterface
{
    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public function run(string $pipelineName, array $context = []): array;
}

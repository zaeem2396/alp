<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Pipeline;
use App\Pipelines\PipelineManager;

final class PipelineService
{
    public function __construct(private readonly PipelineManager $pipelineManager) {}

    /**
     * @param  list<string>  $steps
     */
    public function define(string $name, array $steps): Pipeline
    {
        return new Pipeline($name, $steps);
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public function run(string $pipelineName, array $context = []): array
    {
        return $this->pipelineManager->runNamed($pipelineName, $context);
    }
}

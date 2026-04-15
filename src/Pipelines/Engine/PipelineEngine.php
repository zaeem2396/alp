<?php

declare(strict_types=1);

namespace App\Pipelines\Engine;

use App\Pipelines\Contracts\PipelineEngineInterface;
use App\Services\PipelineService;

final class PipelineEngine implements PipelineEngineInterface
{
    public function __construct(private readonly PipelineService $pipelineService) {}

    public function run(string $pipelineName, array $context = []): array
    {
        return $this->pipelineService->run($pipelineName, $context);
    }
}

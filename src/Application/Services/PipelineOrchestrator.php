<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Services\PipelineService;

final class PipelineOrchestrator
{
    public function __construct(private readonly PipelineService $pipelineService) {}

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public function run(string $pipelineName, array $context = []): array
    {
        return $this->pipelineService->run($pipelineName, $context);
    }
}

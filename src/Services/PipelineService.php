<?php

declare(strict_types=1);

namespace App\Services;

use App\Application\Contracts\PipelineExecutorInterface;
use App\Core\Pipeline;

final class PipelineService
{
    public function __construct(private readonly PipelineExecutorInterface $pipelineExecutor) {}

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
        return $this->pipelineExecutor->execute($pipelineName, $context);
    }
}

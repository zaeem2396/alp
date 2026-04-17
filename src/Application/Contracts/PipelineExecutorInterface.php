<?php

declare(strict_types=1);

namespace App\Application\Contracts;

interface PipelineExecutorInterface
{
    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public function execute(string $pipelineName, array $context = []): array;
}

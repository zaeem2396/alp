<?php

declare(strict_types=1);

namespace App\Application\Contracts;

interface PipelineRunStoreInterface
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function openRun(string $pipelineName, array $context, ?string $correlationId = null): string;

    public function finalizeSuccessfulRun(string $runId): void;

    /**
     * @param  array<string, mixed>  $context
     */
    public function recordStepFinished(
        string $runId,
        int $stepIndex,
        string $stepClass,
        int $durationNs,
        array $context,
    ): void;

    public function finalizeFailedRun(
        string $runId,
        int $failedStepIndex,
        string $stepClass,
        string $errorMessage,
    ): void;
}

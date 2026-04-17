<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Application\Contracts\PipelineRunStoreInterface;
use Illuminate\Support\Str;

final class NullPipelineRunStore implements PipelineRunStoreInterface
{
    public function openRun(string $pipelineName, array $context, ?string $correlationId = null): string
    {
        return (string) Str::uuid();
    }

    public function finalizeSuccessfulRun(string $runId): void {}

    /**
     * @param  array<string, mixed>  $context
     */
    public function recordStepFinished(
        string $runId,
        int $stepIndex,
        string $stepClass,
        int $durationNs,
        array $context,
    ): void {}

    public function finalizeFailedRun(
        string $runId,
        int $failedStepIndex,
        string $stepClass,
        string $errorMessage,
    ): void {}
}

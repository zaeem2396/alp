<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\Contracts\PipelineRunStoreInterface;
use App\Application\Events\PipelineFailed;
use App\Contracts\AlpEventBusInterface;
use App\Domain\Pipeline\Contracts\NonRetryablePipelineFailure;
use Throwable;

final class PipelineFailureService
{
    public function __construct(
        private readonly AlpEventBusInterface $eventBus,
        private readonly PipelineRunStoreInterface $runStore,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public function notifyFailure(
        string $runId,
        string $pipelineName,
        int $failedStepIndex,
        string $stepClass,
        Throwable $failure,
        array $context,
    ): void {
        $message = $failure->getMessage();
        $retryable = $this->isRetryable($failure);

        $this->runStore->finalizeFailedRun($runId, $failedStepIndex, $stepClass, $message);
        $this->eventBus->publish(new PipelineFailed(
            $runId,
            $pipelineName,
            $failedStepIndex,
            $stepClass,
            $message,
            $context,
            $retryable,
        ));
    }

    private function isRetryable(Throwable $failure): bool
    {
        return ! ($failure instanceof NonRetryablePipelineFailure);
    }
}

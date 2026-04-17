<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\Contracts\PipelineExecutorInterface;
use App\Application\Contracts\PipelineRunStoreInterface;
use App\Application\Events\PipelineCompleted;
use App\Application\Events\PipelineStarted;
use App\Application\Events\PipelineStepCompleted;
use App\Contracts\AlpEventBusInterface;
use App\Pipelines\PipelineManager;

final class PipelineExecutorService implements PipelineExecutorInterface
{
    public function __construct(
        private readonly PipelineManager $pipelineManager,
        private readonly AlpEventBusInterface $eventBus,
        private readonly PipelineRunStoreInterface $runStore,
        private readonly PipelineFailureService $failureService,
    ) {}

    public function execute(string $pipelineName, array $context = []): array
    {
        $correlationId = isset($context['_correlation_id']) && is_string($context['_correlation_id'])
            ? $context['_correlation_id']
            : null;

        $runId = $this->runStore->openRun($pipelineName, $context, $correlationId);

        $this->eventBus->publish(new PipelineStarted($runId, $pipelineName, $correlationId, $context));

        $result = $this->pipelineManager->runNamed(
            $pipelineName,
            $context,
            null,
            function (int $index, string $stepClass, array $ctx, int $started, int $ended) use ($runId, $pipelineName): void {
                $durationNs = $ended - $started;
                $this->runStore->recordStepFinished($runId, $index, $stepClass, $durationNs, $ctx);
                $this->eventBus->publish(new PipelineStepCompleted($pipelineName, $stepClass, $ctx));
            },
            function (int $index, string $stepClass, \Throwable $e, array $ctx) use ($runId, $pipelineName): void {
                $this->failureService->notifyFailure(
                    $runId,
                    $pipelineName,
                    $index,
                    $stepClass,
                    $e->getMessage(),
                    $ctx,
                );
            },
        );

        $this->runStore->finalizeSuccessfulRun($runId);
        $this->eventBus->publish(new PipelineCompleted($runId, $pipelineName, $result));

        return $result;
    }
}

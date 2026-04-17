<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\Contracts\PipelineExecutorInterface;
use App\Application\Enums\PipelineExecutionMode;
use App\Jobs\RunPipelineJob;
use Illuminate\Contracts\Bus\Dispatcher as BusDispatcher;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

final class PipelineDispatcher
{
    public function __construct(
        private readonly BusDispatcher $bus,
        private readonly PipelineExecutorInterface $executor,
        private readonly ConfigRepository $config,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>|array{queued: true, pipeline: string, queue: string}
     */
    public function dispatch(string $pipelineName, array $context = [], ?PipelineExecutionMode $mode = null): array
    {
        $resolved = $mode ?? PipelineExecutionMode::tryFrom(
            (string) $this->config->get('alp.pipeline.execution_mode', PipelineExecutionMode::Sync->value),
        ) ?? PipelineExecutionMode::Sync;

        if ($resolved === PipelineExecutionMode::Auto) {
            $resolved = (($context['_async'] ?? false) === true)
                ? PipelineExecutionMode::Queue
                : PipelineExecutionMode::Sync;
        }

        if ($resolved === PipelineExecutionMode::Queue) {
            $queue = (string) $this->config->get('alp.queues.pipelines', $this->config->get('alp.queue', 'default'));
            $this->bus->dispatch((new RunPipelineJob($pipelineName, $context))->onQueue($queue));

            return [
                'queued' => true,
                'pipeline' => $pipelineName,
                'queue' => $queue,
            ];
        }

        return $this->executor->execute($pipelineName, $context);
    }
}

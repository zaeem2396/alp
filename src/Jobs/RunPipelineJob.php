<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Application\Contracts\PipelineExecutorInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use JsonException;

final class RunPipelineJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public int $tries = 3;

    /**
     * @var list<int>
     */
    public array $backoff = [10, 30, 90];

    public int $uniqueFor = 3600;

    /**
     * @param  array<string, mixed>  $context
     */
    public function __construct(
        public readonly string $pipelineName,
        public readonly array $context,
    ) {}

    public function uniqueId(): string
    {
        if (isset($this->context['_unique_lock']) && is_string($this->context['_unique_lock']) && $this->context['_unique_lock'] !== '') {
            return $this->pipelineName.':'.$this->context['_unique_lock'];
        }

        if (isset($this->context['_correlation_id']) && is_string($this->context['_correlation_id']) && $this->context['_correlation_id'] !== '') {
            return $this->pipelineName.':'.$this->context['_correlation_id'];
        }

        try {
            $payload = json_encode($this->context, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            $payload = serialize($this->context);
        }

        return $this->pipelineName.':'.hash('sha256', $payload);
    }

    public function handle(PipelineExecutorInterface $executor): void
    {
        $executor->execute($this->pipelineName, $this->context);
    }
}

<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Application\Contracts\PipelineExecutorInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

final class RunPipelineJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public int $tries = 3;

    /**
     * @var list<int>
     */
    public array $backoff = [10, 30, 90];

    /**
     * @param  array<string, mixed>  $context
     */
    public function __construct(
        public readonly string $pipelineName,
        public readonly array $context,
    ) {}

    public function handle(PipelineExecutorInterface $executor): void
    {
        $executor->execute($this->pipelineName, $this->context);
    }
}

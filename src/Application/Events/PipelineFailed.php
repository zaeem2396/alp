<?php

declare(strict_types=1);

namespace App\Application\Events;

final readonly class PipelineFailed
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function __construct(
        public string $runId,
        public string $pipeline,
        public int $failedStepIndex,
        public string $stepClass,
        public string $message,
        public array $context,
    ) {}
}

<?php

declare(strict_types=1);

namespace App\Application\Events;

final readonly class PipelineStarted
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function __construct(
        public string $runId,
        public string $pipeline,
        public ?string $correlationId,
        public array $context,
    ) {}
}

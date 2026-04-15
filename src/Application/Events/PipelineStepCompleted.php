<?php

declare(strict_types=1);

namespace App\Application\Events;

final class PipelineStepCompleted
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function __construct(
        public readonly string $pipeline,
        public readonly string $step,
        public readonly array $context
    ) {}
}

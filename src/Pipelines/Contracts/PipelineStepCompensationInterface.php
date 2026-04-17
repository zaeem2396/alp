<?php

declare(strict_types=1);

namespace App\Pipelines\Contracts;

interface PipelineStepCompensationInterface
{
    /**
     * Undo side effects for this step using the context snapshot from immediately before the step ran.
     *
     * @param  array<string, mixed>  $contextBeforeStep
     */
    public function compensate(array $contextBeforeStep): void;
}

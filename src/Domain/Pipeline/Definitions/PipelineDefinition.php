<?php

declare(strict_types=1);

namespace App\Domain\Pipeline\Definitions;

use App\Domain\Pipeline\Contracts\PipelineStepInterface;

final class PipelineDefinition
{
    /**
     * @param  list<class-string<PipelineStepInterface>>  $steps
     */
    public function __construct(
        public readonly string $name,
        public readonly array $steps
    ) {}
}

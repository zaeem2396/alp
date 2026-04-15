<?php

declare(strict_types=1);

namespace App\Domain\Pipeline\Contracts;

interface PipelineStepInterface
{
    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public function handle(array $context): array;
}

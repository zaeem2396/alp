<?php

declare(strict_types=1);

namespace App\Domain\Pipeline\Definitions;

use App\Domain\Pipeline\Contracts\PipelineStepInterface;
use InvalidArgumentException;

final class PipelineDefinitionRegistry
{
    /**
     * @param  array<string, list<class-string<PipelineStepInterface>>>  $pipelines
     */
    public function __construct(
        private readonly array $pipelines,
    ) {}

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->pipelines);
    }

    /**
     * @return list<class-string<PipelineStepInterface>>
     */
    public function stepClassesFor(string $name): array
    {
        if (! $this->has($name)) {
            throw new InvalidArgumentException(sprintf('Pipeline [%s] is not defined.', $name));
        }

        /** @var list<class-string<PipelineStepInterface>> */
        return array_values($this->pipelines[$name]);
    }

    public function definitionFor(string $name): PipelineDefinition
    {
        return new PipelineDefinition($name, $this->stepClassesFor($name));
    }
}

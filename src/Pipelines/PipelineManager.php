<?php

declare(strict_types=1);

namespace App\Pipelines;

use App\Pipelines\Contracts\PipelineStepInterface;
use Closure;
use InvalidArgumentException;

final class PipelineManager
{
    /**
     * @param  array<string, list<class-string<PipelineStepInterface>>>  $namedPipelines
     */
    public function __construct(
        private readonly array $namedPipelines = [],
        private readonly ?Closure $resolver = null
    ) {}

    /**
     * @param  list<PipelineStepInterface>  $steps
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public function run(array $steps, array $context = []): array
    {
        foreach ($steps as $step) {
            $context = $step->handle($context);
        }

        return $context;
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public function runNamed(string $name, array $context = []): array
    {
        if (! array_key_exists($name, $this->namedPipelines)) {
            throw new InvalidArgumentException(sprintf('Pipeline [%s] is not defined.', $name));
        }

        $steps = [];
        foreach ($this->namedPipelines[$name] as $stepClass) {
            $steps[] = $this->resolver instanceof Closure
                ? ($this->resolver)($stepClass)
                : new $stepClass;
        }

        return $this->run($steps, $context);
    }
}

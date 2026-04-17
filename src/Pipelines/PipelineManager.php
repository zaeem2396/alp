<?php

declare(strict_types=1);

namespace App\Pipelines;

use App\Pipelines\Contracts\PipelineStepCompensationInterface;
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
     * @param  (Closure(int, class-string<PipelineStepInterface>, array<string, mixed>): void)|null  $beforeStep
     * @param  (Closure(int, class-string<PipelineStepInterface>, array<string, mixed>, int, int): void)|null  $afterStep
     * @param  (Closure(int, class-string<PipelineStepInterface>, \Throwable, array<string, mixed>): void)|null  $onFailure
     * @return array<string, mixed>
     */
    public function runNamed(
        string $name,
        array $context = [],
        ?Closure $beforeStep = null,
        ?Closure $afterStep = null,
        ?Closure $onFailure = null,
    ): array {
        if (! array_key_exists($name, $this->namedPipelines)) {
            throw new InvalidArgumentException(sprintf('Pipeline [%s] is not defined.', $name));
        }

        /** @var list<array{0: PipelineStepInterface, 1: array<string, mixed>}> $completedForCompensation */
        $completedForCompensation = [];

        foreach ($this->namedPipelines[$name] as $index => $stepClass) {
            $step = $this->resolver instanceof Closure
                ? ($this->resolver)($stepClass)
                : new $stepClass;

            if ($beforeStep instanceof Closure) {
                $beforeStep($index, $stepClass, $context);
            }

            $started = hrtime(true);
            $contextBeforeStep = $context;
            try {
                $context = $step->handle($context);
            } catch (\Throwable $e) {
                $this->compensateCompletedSteps($completedForCompensation);

                if ($onFailure instanceof Closure) {
                    $onFailure($index, $stepClass, $e, $context);
                }

                throw $e;
            }
            $completedForCompensation[] = [$step, $contextBeforeStep];
            $ended = hrtime(true);

            if ($afterStep instanceof Closure) {
                $afterStep($index, $stepClass, $context, $started, $ended);
            }
        }

        return $context;
    }

    /**
     * @param  list<array{0: PipelineStepInterface, 1: array<string, mixed>}>  $completed
     */
    private function compensateCompletedSteps(array $completed): void
    {
        while ($completed !== []) {
            /** @var array{0: PipelineStepInterface, 1: array<string, mixed>} $pair */
            $pair = array_pop($completed);
            [$step, $contextBeforeStep] = $pair;

            if (! $step instanceof PipelineStepCompensationInterface) {
                continue;
            }

            try {
                $step->compensate($contextBeforeStep);
            } catch (\Throwable) {
            }
        }
    }
}

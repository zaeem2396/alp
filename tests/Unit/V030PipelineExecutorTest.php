<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\Events\PipelineCompleted;
use App\Application\Events\PipelineFailed;
use App\Application\Events\PipelineStarted;
use App\Application\Services\PipelineExecutorService;
use App\Application\Services\PipelineFailureService;
use App\Contracts\TextExtractorInterface;
use App\Domain\Pipeline\Contracts\NonRetryablePipelineFailure;
use App\Infrastructure\Events\LaravelAlpEventBus;
use App\Infrastructure\Persistence\NullPipelineRunStore;
use App\Pipelines\Contracts\PipelineStepCompensationInterface;
use App\Pipelines\Contracts\PipelineStepInterface;
use App\Pipelines\PipelineManager;
use App\Pipelines\Steps\DetectTables;
use App\Pipelines\Steps\ExtractText;
use App\Pipelines\Steps\StoreDocument;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Support\InMemoryEventDispatcher;

final class V030PipelineExecutorTest extends TestCase
{
    public function test_emits_started_and_completed_events(): void
    {
        $extractor = new class implements TextExtractorInterface
        {
            public function extract(string $filePath): string
            {
                return 'ok';
            }
        };

        $manager = new PipelineManager([
            'extract-basic' => [
                ExtractText::class,
                DetectTables::class,
                StoreDocument::class,
            ],
        ], static fn (string $stepClass): object => match ($stepClass) {
            ExtractText::class => new ExtractText($extractor),
            DetectTables::class => new DetectTables,
            StoreDocument::class => new StoreDocument,
            default => throw new \InvalidArgumentException(sprintf('Unknown step [%s].', $stepClass)),
        });

        $events = new InMemoryEventDispatcher;
        $eventBus = new LaravelAlpEventBus($events);
        $runStore = new NullPipelineRunStore;
        $failureService = new PipelineFailureService($eventBus, $runStore);
        $executor = new PipelineExecutorService($manager, $eventBus, $runStore, $failureService);

        $executor->execute('extract-basic', ['file' => '/tmp/x.pdf']);

        $started = null;
        $completed = null;
        foreach ($events->events as $event) {
            if ($event instanceof PipelineStarted) {
                $started = $event;
            }
            if ($event instanceof PipelineCompleted) {
                $completed = $event;
            }
        }

        self::assertInstanceOf(PipelineStarted::class, $started);
        self::assertInstanceOf(PipelineCompleted::class, $completed);
        self::assertSame('extract-basic', $started->pipeline);
        self::assertSame($started->runId, $completed->runId);
    }

    public function test_step_failure_dispatches_pipeline_failed(): void
    {
        $badStep = new class implements PipelineStepInterface
        {
            public function handle(array $context): array
            {
                throw new \RuntimeException('boom');
            }
        };

        $badClass = $badStep::class;

        $manager = new PipelineManager([
            'failing' => [
                $badClass,
            ],
        ], static function (string $stepClass) use ($badStep, $badClass): PipelineStepInterface {
            if ($stepClass !== $badClass) {
                throw new \InvalidArgumentException(sprintf('Unknown step [%s].', $stepClass));
            }

            return $badStep;
        });

        $events = new InMemoryEventDispatcher;
        $eventBus = new LaravelAlpEventBus($events);
        $runStore = new NullPipelineRunStore;
        $failureService = new PipelineFailureService($eventBus, $runStore);
        $executor = new PipelineExecutorService($manager, $eventBus, $runStore, $failureService);

        try {
            $executor->execute('failing', []);
            self::fail('Expected exception');
        } catch (\RuntimeException) {
        }

        $failed = null;
        foreach ($events->events as $event) {
            if ($event instanceof PipelineFailed) {
                $failed = $event;
            }
        }

        self::assertInstanceOf(PipelineFailed::class, $failed);
        self::assertSame('boom', $failed->message);
        self::assertTrue($failed->retryable);
    }

    public function test_non_retryable_exception_marks_pipeline_failed_as_not_retryable(): void
    {
        $badStep = new class implements PipelineStepInterface
        {
            public function handle(array $context): array
            {
                throw new class('permanent') extends \RuntimeException implements NonRetryablePipelineFailure {};
            }
        };

        $badClass = $badStep::class;

        $manager = new PipelineManager([
            'failing' => [
                $badClass,
            ],
        ], static function (string $stepClass) use ($badStep, $badClass): PipelineStepInterface {
            if ($stepClass !== $badClass) {
                throw new \InvalidArgumentException(sprintf('Unknown step [%s].', $stepClass));
            }

            return $badStep;
        });

        $events = new InMemoryEventDispatcher;
        $eventBus = new LaravelAlpEventBus($events);
        $runStore = new NullPipelineRunStore;
        $failureService = new PipelineFailureService($eventBus, $runStore);
        $executor = new PipelineExecutorService($manager, $eventBus, $runStore, $failureService);

        try {
            $executor->execute('failing', []);
        } catch (\RuntimeException) {
        }

        $failed = null;
        foreach ($events->events as $event) {
            if ($event instanceof PipelineFailed) {
                $failed = $event;
            }
        }

        self::assertInstanceOf(PipelineFailed::class, $failed);
        self::assertFalse($failed->retryable);
    }

    public function test_compensates_completed_steps_in_reverse_order_on_failure(): void
    {
        $trace = new PipelineCompensationTrace;

        $first = new class($trace) implements PipelineStepCompensationInterface, PipelineStepInterface
        {
            public function __construct(private PipelineCompensationTrace $trace) {}

            public function handle(array $context): array
            {
                $this->trace->entries[] = 'first-handle';

                return $context + ['first' => true];
            }

            public function compensate(array $contextBeforeStep): void
            {
                $this->trace->entries[] = 'first-compensate';
            }
        };

        $second = new class($trace) implements PipelineStepInterface
        {
            public function __construct(private PipelineCompensationTrace $trace) {}

            public function handle(array $context): array
            {
                $this->trace->entries[] = 'second-handle';
                throw new \RuntimeException('second-failed');
            }
        };

        $cFirst = $first::class;
        $cSecond = $second::class;

        $manager = new PipelineManager([
            'two-step' => [
                $cFirst,
                $cSecond,
            ],
        ], static function (string $stepClass) use ($first, $second, $cFirst, $cSecond): PipelineStepInterface {
            return match ($stepClass) {
                $cFirst => $first,
                $cSecond => $second,
                default => throw new \InvalidArgumentException(sprintf('Unknown step [%s].', $stepClass)),
            };
        });

        $events = new InMemoryEventDispatcher;
        $eventBus = new LaravelAlpEventBus($events);
        $runStore = new NullPipelineRunStore;
        $failureService = new PipelineFailureService($eventBus, $runStore);
        $executor = new PipelineExecutorService($manager, $eventBus, $runStore, $failureService);

        try {
            $executor->execute('two-step', []);
        } catch (\RuntimeException) {
        }

        self::assertSame([
            'first-handle',
            'second-handle',
            'first-compensate',
        ], $trace->entries);
    }
}

final class PipelineCompensationTrace
{
    /** @var list<string> */
    public array $entries = [];
}

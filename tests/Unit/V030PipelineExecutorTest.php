<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\Events\PipelineCompleted;
use App\Application\Events\PipelineFailed;
use App\Application\Events\PipelineStarted;
use App\Application\Services\PipelineExecutorService;
use App\Application\Services\PipelineFailureService;
use App\Contracts\TextExtractorInterface;
use App\Infrastructure\Events\LaravelAlpEventBus;
use App\Infrastructure\Persistence\NullPipelineRunStore;
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
    }
}

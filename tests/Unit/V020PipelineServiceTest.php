<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\Services\PipelineExecutorService;
use App\Application\Services\PipelineFailureService;
use App\Contracts\TextExtractorInterface;
use App\Infrastructure\Events\LaravelAlpEventBus;
use App\Infrastructure\Persistence\NullPipelineRunStore;
use App\Pipelines\PipelineManager;
use App\Pipelines\Steps\DetectTables;
use App\Pipelines\Steps\ExtractText;
use App\Pipelines\Steps\StoreDocument;
use App\Services\PipelineService;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Support\InMemoryEventDispatcher;

final class V020PipelineServiceTest extends TestCase
{
    public function test_runs_named_pipeline_with_context(): void
    {
        $extractor = new class implements TextExtractorInterface
        {
            public function extract(string $filePath): string
            {
                return sprintf('extracted:%s', $filePath);
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
        $service = new PipelineService($executor);

        $result = $service->run('extract-basic', ['document_id' => 'doc-1', 'file' => '/tmp/doc-1.pdf']);

        self::assertArrayHasKey('stored', $result);
        self::assertSame('extracted:/tmp/doc-1.pdf', $result['text']);
        self::assertTrue($result['stored']);
    }
}

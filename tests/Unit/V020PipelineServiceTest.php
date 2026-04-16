<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Contracts\TextExtractorInterface;
use App\Pipelines\PipelineManager;
use App\Pipelines\Steps\DetectTables;
use App\Pipelines\Steps\ExtractText;
use App\Pipelines\Steps\StoreDocument;
use App\Services\PipelineService;
use PHPUnit\Framework\TestCase;

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
        $service = new PipelineService($manager);

        $result = $service->run('extract-basic', ['document_id' => 'doc-1', 'file' => '/tmp/doc-1.pdf']);

        self::assertArrayHasKey('stored', $result);
        self::assertSame('extracted:/tmp/doc-1.pdf', $result['text']);
        self::assertTrue($result['stored']);
    }
}

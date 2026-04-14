<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Pipelines\PipelineManager;
use App\Services\PipelineService;
use PHPUnit\Framework\TestCase;

final class V020PipelineServiceTest extends TestCase
{
    public function test_runs_named_pipeline_with_context(): void
    {
        $manager = new PipelineManager([
            'extract-basic' => [
                \App\Pipelines\Steps\ExtractText::class,
                \App\Pipelines\Steps\DetectTables::class,
                \App\Pipelines\Steps\StoreDocument::class,
            ],
        ]);
        $service = new PipelineService($manager);

        $result = $service->run('extract-basic', ['document_id' => 'doc-1']);

        self::assertArrayHasKey('stored', $result);
        self::assertTrue($result['stored']);
    }
}

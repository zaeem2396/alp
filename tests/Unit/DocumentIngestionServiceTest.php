<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Events\DocumentNormalized;
use App\Events\DocumentUploaded;
use App\Normalizers\DocxNormalizer;
use App\Normalizers\PdfNormalizer;
use App\Repositories\DocumentRepository;
use App\Services\DocumentIngestionService;
use App\Services\DocumentNormalizerService;
use App\Services\DocumentStorageService;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Support\InMemoryEventDispatcher;

final class DocumentIngestionServiceTest extends TestCase
{
    public function test_it_ingests_and_dispatches_events(): void
    {
        $dispatcher = new InMemoryEventDispatcher;
        $service = new DocumentIngestionService(
            new DocumentNormalizerService([new PdfNormalizer, new DocxNormalizer]),
            new DocumentStorageService('/tmp/alp-tests'),
            new DocumentRepository,
            $dispatcher
        );

        $document = $service->ingest('invoice-001', 'sample content', 'docx');

        self::assertSame('invoice-001', $document->name);
        self::assertNotNull($document->rawPath);
        self::assertNotNull($document->processedPath);
        self::assertInstanceOf(DocumentUploaded::class, $dispatcher->events[0]);
        self::assertInstanceOf(DocumentNormalized::class, $dispatcher->events[1]);
    }
}

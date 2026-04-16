<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Events\MetadataExtracted;
use App\Infrastructure\Apryse\ApryseTextExtractor;
use App\Services\AprysePhpClient;
use App\Services\MetadataExtractionService;
use App\Services\TextExtractionService;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Support\InMemoryEventDispatcher;

final class TextAndMetadataExtractionTest extends TestCase
{
    public function test_text_extraction_returns_plain_text(): void
    {
        $filePath = '/tmp/alp-tests-extract.txt';
        file_put_contents($filePath, 'hello from alp');

        $service = new TextExtractionService(new ApryseTextExtractor(new AprysePhpClient));
        $payload = $service->extract($filePath);

        self::assertSame('hello from alp', $payload);
    }

    public function test_metadata_extraction_dispatches_event(): void
    {
        $filePath = '/tmp/alp-tests-metadata.txt';
        file_put_contents($filePath, 'meta payload');
        $dispatcher = new InMemoryEventDispatcher;

        $service = new MetadataExtractionService(new AprysePhpClient, $dispatcher);
        $metadata = $service->extract('doc-1', $filePath);

        self::assertSame('alp-tests-metadata.txt', $metadata['filename']);
        self::assertInstanceOf(MetadataExtracted::class, $dispatcher->events[0]);
    }
}

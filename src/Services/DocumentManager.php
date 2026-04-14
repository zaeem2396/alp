<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Document;

final class DocumentManager
{
    public function __construct(
        private readonly DocumentIngestionService $ingestion,
        private readonly TextExtractionService $textExtraction,
        private readonly MetadataExtractionService $metadataExtraction
    ) {}

    public function ingest(string $name, string $content, string $extension): Document
    {
        return $this->ingestion->ingest($name, $content, $extension);
    }

    /**
     * @return array{pages: list<array{number:int,text:string}>, blocks: list<array{page:int,text:string}>}
     */
    public function extractText(string $filePath): array
    {
        return $this->textExtraction->extract($filePath);
    }

    /**
     * @return array<string, scalar|null>
     */
    public function extractMetadata(string $documentId, string $filePath): array
    {
        return $this->metadataExtraction->extract($documentId, $filePath);
    }
}

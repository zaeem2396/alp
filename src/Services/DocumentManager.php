<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Document;
use App\Services\AI\DocumentQaService;
use App\Services\AI\DocumentSummarizationService;
use App\Services\AI\EntityExtractionService;

final class DocumentManager
{
    public function __construct(
        private readonly DocumentIngestionService $ingestion,
        private readonly TextExtractionService $textExtraction,
        private readonly MetadataExtractionService $metadataExtraction,
        private readonly TableDetectionService $tableDetection,
        private readonly LayoutParsingService $layoutParsing,
        private readonly DocumentSummarizationService $summarization,
        private readonly EntityExtractionService $entityExtraction,
        private readonly DocumentQaService $qa
    ) {}

    public function ingest(string $name, string $content, string $extension): Document
    {
        return $this->ingestion->ingest($name, $content, $extension);
    }

    public function extractText(string $filePath): string
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

    /**
     * @return array{tables:list<array{table_id:string,cells:list<array{row:int,col:int,text:string}>,confidence:float}>}
     */
    public function detectTables(string $text): array
    {
        return $this->tableDetection->detect($text);
    }

    /**
     * @return array{tables:list<array{table_id:string,cells:list<array{row:int,col:int,text:string}>,confidence:float}>}
     */
    public function detectTablesForDocument(string $documentId, string $text): array
    {
        return $this->tableDetection->detectForDocument($documentId, $text);
    }

    /**
     * @return array{zones:list<array{type:string,text:string,page:int}>,pages:int}
     */
    public function parseLayout(string $text): array
    {
        return $this->layoutParsing->parse($text);
    }

    /**
     * @return array{zones:list<array{type:string,text:string,page:int}>,pages:int}
     */
    public function parseLayoutForDocument(string $documentId, string $text): array
    {
        return $this->layoutParsing->parseForDocument($documentId, $text);
    }

    public function summarize(string $documentId, string $text): string
    {
        return $this->summarization->summarize($documentId, $text);
    }

    /**
     * @param  array<string, string>  $schema
     * @return array<string, mixed>
     */
    public function extractEntities(string $documentId, string $text, array $schema): array
    {
        return $this->entityExtraction->extract($documentId, $text, $schema);
    }

    /**
     * @param  array<int, string>  $chunks
     * @return array{answer:string,citations:list<int>}
     */
    public function ask(string $question, array $chunks): array
    {
        return $this->qa->ask($question, $chunks);
    }
}

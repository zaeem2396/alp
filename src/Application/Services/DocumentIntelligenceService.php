<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\DTOs\QueryAnswerData;
use App\Services\AI\DocumentQaService;
use App\Services\AI\DocumentSummarizationService;
use App\Services\AI\EntityExtractionService;
use App\Services\LayoutParsingService;
use App\Services\TableDetectionService;

final class DocumentIntelligenceService
{
    public function __construct(
        private readonly TableDetectionService $tables,
        private readonly LayoutParsingService $layout,
        private readonly DocumentSummarizationService $summary,
        private readonly EntityExtractionService $entities,
        private readonly DocumentQaService $qa
    ) {}

    /**
     * @return array{tables:list<array{table_id:string,cells:list<array{row:int,col:int,text:string}>,confidence:float}>}
     */
    public function detectTables(string $documentId, string $text): array
    {
        return $this->tables->detectForDocument($documentId, $text);
    }

    /**
     * @return array{zones:list<array{type:string,text:string,page:int}>,pages:int}
     */
    public function parseLayout(string $documentId, string $text): array
    {
        return $this->layout->parseForDocument($documentId, $text);
    }

    public function summarize(string $documentId, string $text): string
    {
        return $this->summary->summarize($documentId, $text);
    }

    /**
     * @param  array<string, string>  $schema
     * @return array<string, mixed>
     */
    public function extractEntities(string $documentId, string $text, array $schema): array
    {
        return $this->entities->extract($documentId, $text, $schema);
    }

    /**
     * @param  array<int, string>  $chunks
     */
    public function ask(string $question, array $chunks): QueryAnswerData
    {
        $result = $this->qa->ask($question, $chunks);

        return new QueryAnswerData($result['answer'], $result['citations']);
    }
}

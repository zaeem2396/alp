<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Contracts\SummarizerInterface;
use App\Services\StructuredDocumentService;

final class DocumentSummarizationService
{
    public function __construct(
        private readonly SummarizerInterface $summarizer,
        private readonly StructuredDocumentService $structuredDocuments
    ) {}

    public function summarize(string $documentId, string $text, ?string $provider = null): string
    {
        $summary = $this->summarizer->summarize($text);
        $this->structuredDocuments->store($documentId, 'summary', ['summary' => $summary], 1);

        return $summary;
    }
}

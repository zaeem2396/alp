<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Services\StructuredDocumentService;

final class DocumentSummarizationService
{
    public function __construct(
        private readonly AiManager $aiManager,
        private readonly StructuredDocumentService $structuredDocuments
    ) {}

    public function summarize(string $documentId, string $text, ?string $provider = null): string
    {
        $summary = $this->aiManager->provider($provider)->summarize($text, ['limit' => 220]);
        $this->structuredDocuments->store($documentId, 'summary', ['summary' => $summary], 1);

        return $summary;
    }
}

<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Services\StructuredDocumentService;

final class EntityExtractionService
{
    public function __construct(
        private readonly AiManager $aiManager,
        private readonly StructuredDocumentService $structuredDocuments
    ) {}

    /**
     * @param  array<string, string>  $schema
     * @return array<string, mixed>
     */
    public function extract(
        string $documentId,
        string $text,
        array $schema = [],
        ?string $provider = null
    ): array {
        $entities = $this->aiManager->provider($provider)->extractEntities($text, $schema);
        $this->structuredDocuments->store($documentId, 'entities', ['entities' => $entities], 1);

        return $entities;
    }
}

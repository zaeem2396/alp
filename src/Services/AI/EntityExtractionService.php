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
        $validatedSchema = [];
        foreach ($schema as $field => $pattern) {
            if (! is_string($pattern) || @preg_match($pattern, '') === false) {
                continue;
            }

            $validatedSchema[$field] = $pattern;
        }

        $entities = $this->aiManager->provider($provider)->extractEntities($text, $validatedSchema);
        foreach ($entities as $field => $entity) {
            if (! is_array($entity)) {
                continue;
            }

            $entities[$field]['provenance'] = [
                'source' => 'text',
                'document_id' => $documentId,
            ];
        }
        $this->structuredDocuments->store($documentId, 'entities', ['entities' => $entities], 1);

        return $entities;
    }
}

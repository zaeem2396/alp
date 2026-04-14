<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\StructuredDocumentRepositoryInterface;

final class StructuredDocumentRepository implements StructuredDocumentRepositoryInterface
{
    /**
     * @var array<string, array<string, mixed>>
     */
    private array $store = [];

    public function save(string $documentId, string $schema, array $payload, int $version = 1): void
    {
        $this->store[$documentId] = [
            'document_id' => $documentId,
            'schema' => $schema,
            'payload' => $payload,
            'version' => $version,
            'indexed_at' => date(DATE_ATOM),
        ];
    }

    public function find(string $documentId): ?array
    {
        return $this->store[$documentId] ?? null;
    }
}

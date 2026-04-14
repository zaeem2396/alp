<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\StructuredDocumentRepositoryInterface;

final class StructuredDocumentService
{
    public function __construct(private readonly StructuredDocumentRepositoryInterface $repository) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public function store(string $documentId, string $schema, array $payload, int $version = 1): void
    {
        $this->repository->save($documentId, $schema, $payload, $version);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function retrieve(string $documentId): ?array
    {
        return $this->repository->find($documentId);
    }
}

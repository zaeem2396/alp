<?php

declare(strict_types=1);

namespace App\Contracts;

interface StructuredDocumentRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function save(string $documentId, string $schema, array $payload, int $version = 1): void;

    /**
     * @return array<string, mixed>|null
     */
    public function find(string $documentId): ?array;
}

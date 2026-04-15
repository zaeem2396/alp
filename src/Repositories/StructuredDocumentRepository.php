<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\StructuredDocumentRepositoryInterface;

final class StructuredDocumentRepository implements StructuredDocumentRepositoryInterface
{
    /**
     * @var array<string, array<string, mixed>>
     */
    private array $store;

    public function __construct(private readonly string $storagePath = '/tmp/alp/structured_documents.json')
    {
        $this->store = $this->load();
    }

    public function save(string $documentId, string $schema, array $payload, int $version = 1): void
    {
        $this->store[$documentId] = [
            'document_id' => $documentId,
            'schema' => $schema,
            'payload' => $payload,
            'version' => $version,
            'indexed_at' => date(DATE_ATOM),
        ];
        $this->persist();
    }

    public function find(string $documentId): ?array
    {
        return $this->store[$documentId] ?? null;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function load(): array
    {
        if (! file_exists($this->storagePath)) {
            return [];
        }

        $raw = file_get_contents($this->storagePath);
        if (! is_string($raw) || $raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function persist(): void
    {
        $directory = dirname($this->storagePath);
        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($this->storagePath, (string) json_encode($this->store, JSON_PRETTY_PRINT));
    }
}

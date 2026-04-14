<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\DocumentRepositoryInterface;
use App\Core\Document;

final class DocumentRepository implements DocumentRepositoryInterface
{
    /**
     * @var array<string, Document>
     */
    private array $store = [];

    public function save(Document $document): Document
    {
        $this->store[$document->id] = $document;

        return $document;
    }

    public function find(string $id): ?Document
    {
        return $this->store[$id] ?? null;
    }
}

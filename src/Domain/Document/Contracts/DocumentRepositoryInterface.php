<?php

declare(strict_types=1);

namespace App\Domain\Document\Contracts;

use App\Domain\Document\Models\Document;

interface DocumentRepositoryInterface
{
    public function save(Document $document): Document;

    public function find(string $id): ?Document;
}

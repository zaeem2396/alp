<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Core\Document;

interface DocumentRepositoryInterface
{
    public function save(Document $document): Document;

    public function find(string $id): ?Document;
}

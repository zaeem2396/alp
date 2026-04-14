<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Document;

final class DocumentRepository
{
    public function save(Document $document): Document
    {
        // Placeholder persistence hook for initial project setup.
        return $document;
    }
}

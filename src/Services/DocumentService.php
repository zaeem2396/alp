<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Document;

final class DocumentService
{
    public function create(string $id, string $name): Document
    {
        return new Document($id, $name, 'uploaded');
    }
}

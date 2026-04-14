<?php

declare(strict_types=1);

namespace App\Events;

final class DocumentProcessed
{
    public function __construct(public readonly string $documentId) {}
}

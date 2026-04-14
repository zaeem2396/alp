<?php

declare(strict_types=1);

namespace App\Events;

final class DocumentFailed
{
    public function __construct(
        public readonly string $documentId,
        public readonly string $reason
    ) {}
}

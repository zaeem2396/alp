<?php

declare(strict_types=1);

namespace App\Core;

final class Document
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $status,
        public readonly ?string $rawPath = null,
        public readonly ?string $processedPath = null
    ) {}
}

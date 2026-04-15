<?php

declare(strict_types=1);

namespace App\Application\DTOs;

final class DocumentRequestData
{
    public function __construct(
        public readonly string $name,
        public readonly string $content,
        public readonly string $extension
    ) {}
}

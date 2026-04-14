<?php

declare(strict_types=1);

namespace App\Models;

final class Document
{
    public function __construct(
        public readonly string $id,
        public readonly string $path
    ) {}
}

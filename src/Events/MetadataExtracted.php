<?php

declare(strict_types=1);

namespace App\Events;

final class MetadataExtracted
{
    /**
     * @param  array<string, scalar|null>  $metadata
     */
    public function __construct(
        public readonly string $documentId,
        public readonly array $metadata
    ) {}
}

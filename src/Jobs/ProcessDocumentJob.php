<?php

declare(strict_types=1);

namespace App\Jobs;

final class ProcessDocumentJob
{
    public function __construct(private readonly string $documentId) {}

    public function handle(): void
    {
        // Queue handling placeholder.
        $documentId = $this->documentId;

        unset($documentId);
    }
}

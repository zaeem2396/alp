<?php

declare(strict_types=1);

namespace App\Contracts;

interface DocumentStorageInterface
{
    public function storeRaw(string $documentId, string $content, string $extension): string;

    public function storeProcessed(string $documentId, string $content, string $extension): string;
}

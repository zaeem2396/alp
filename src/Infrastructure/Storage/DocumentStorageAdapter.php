<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

use App\Contracts\DocumentStorageInterface;
use App\Services\DocumentStorageService;

final class DocumentStorageAdapter implements DocumentStorageInterface
{
    public function __construct(private readonly DocumentStorageService $service) {}

    public function storeRaw(string $documentId, string $content, string $extension): string
    {
        return $this->service->storeRaw($documentId, $content, $extension);
    }

    public function storeProcessed(string $documentId, string $content, string $extension): string
    {
        return $this->service->storeProcessed($documentId, $content, $extension);
    }
}

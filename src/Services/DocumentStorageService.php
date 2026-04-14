<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\DocumentStorageInterface;

final class DocumentStorageService implements DocumentStorageInterface
{
    public function __construct(private readonly string $basePath = '/tmp/alp') {}

    public function storeRaw(string $documentId, string $content, string $extension): string
    {
        return $this->store($documentId, $content, $extension, 'raw');
    }

    public function storeProcessed(string $documentId, string $content, string $extension): string
    {
        return $this->store($documentId, $content, $extension, 'processed');
    }

    private function store(string $documentId, string $content, string $extension, string $type): string
    {
        $dir = sprintf('%s/%s', rtrim($this->basePath, '/'), $type);
        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $path = sprintf('%s/%s.%s', $dir, $documentId, strtolower($extension));
        file_put_contents($path, $content);

        return $path;
    }
}

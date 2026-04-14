<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ApryseClientInterface;

final class AprysePhpClient implements ApryseClientInterface
{
    public function extractText(string $filePath): array
    {
        $content = file_exists($filePath) ? (string) file_get_contents($filePath) : '';

        return [
            'pages' => [
                ['number' => 1, 'text' => $content],
            ],
            'blocks' => [
                ['page' => 1, 'text' => $content],
            ],
        ];
    }

    public function extractMetadata(string $filePath): array
    {
        return [
            'filename' => basename($filePath),
            'extension' => pathinfo($filePath, PATHINFO_EXTENSION),
            'size' => file_exists($filePath) ? filesize($filePath) : null,
        ];
    }
}

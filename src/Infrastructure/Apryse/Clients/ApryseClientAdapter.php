<?php

declare(strict_types=1);

namespace App\Infrastructure\Apryse\Clients;

use App\Contracts\ApryseClientInterface;
use App\Services\AprysePhpClient;

final class ApryseClientAdapter implements ApryseClientInterface
{
    public function __construct(private readonly AprysePhpClient $client) {}

    public function extractText(string $filePath): array
    {
        return $this->client->extractText($filePath);
    }

    public function extractMetadata(string $filePath): array
    {
        return $this->client->extractMetadata($filePath);
    }
}

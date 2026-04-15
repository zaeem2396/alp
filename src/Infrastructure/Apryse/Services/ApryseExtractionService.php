<?php

declare(strict_types=1);

namespace App\Infrastructure\Apryse\Services;

use App\Contracts\ApryseClientInterface;

final class ApryseExtractionService
{
    public function __construct(private readonly ApryseClientInterface $client) {}

    /**
     * @return array{pages: list<array{number:int,text:string}>, blocks: list<array{page:int,text:string}>}
     */
    public function text(string $filePath): array
    {
        return $this->client->extractText($filePath);
    }

    /**
     * @return array<string, scalar|null>
     */
    public function metadata(string $filePath): array
    {
        return $this->client->extractMetadata($filePath);
    }
}

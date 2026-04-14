<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ApryseClientInterface;

final class ApryseService
{
    public function __construct(private readonly ApryseClientInterface $client) {}

    /**
     * @return array{pages: list<array{number:int,text:string}>, blocks: list<array{page:int,text:string}>}
     */
    public function extractText(string $path): array
    {
        return $this->client->extractText($path);
    }

    /**
     * @return array<string, scalar|null>
     */
    public function extractMetadata(string $path): array
    {
        return $this->client->extractMetadata($path);
    }
}

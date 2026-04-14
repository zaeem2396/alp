<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ApryseClientInterface;

final class TextExtractionService
{
    public function __construct(private readonly ApryseClientInterface $apryseClient) {}

    /**
     * @return array{pages: list<array{number:int,text:string}>, blocks: list<array{page:int,text:string}>}
     */
    public function extract(string $filePath): array
    {
        return $this->apryseClient->extractText($filePath);
    }
}

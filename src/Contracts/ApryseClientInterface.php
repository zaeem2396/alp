<?php

declare(strict_types=1);

namespace App\Contracts;

interface ApryseClientInterface
{
    /**
     * @return array{pages: list<array{number:int,text:string}>, blocks: list<array{page:int,text:string}>}
     */
    public function extractText(string $filePath): array;

    /**
     * @return array<string, scalar|null>
     */
    public function extractMetadata(string $filePath): array;
}

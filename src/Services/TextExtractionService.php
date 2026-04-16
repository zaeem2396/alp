<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\TextExtractorInterface;

final class TextExtractionService
{
    public function __construct(private readonly TextExtractorInterface $extractor) {}

    public function extract(string $filePath): string
    {
        return $this->extractor->extract($filePath);
    }
}

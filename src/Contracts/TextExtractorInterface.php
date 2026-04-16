<?php

declare(strict_types=1);

namespace App\Contracts;

interface TextExtractorInterface
{
    public function extract(string $filePath): string;
}

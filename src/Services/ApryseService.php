<?php

declare(strict_types=1);

namespace App\Services;

final class ApryseService
{
    public function extractText(string $path): string
    {
        return sprintf('Extracted text placeholder for %s', $path);
    }
}

<?php

declare(strict_types=1);

namespace App\Normalizers;

use App\Contracts\NormalizerInterface;

final class PdfNormalizer implements NormalizerInterface
{
    public function supports(string $extension): bool
    {
        return strtolower($extension) === 'pdf';
    }

    public function normalize(string $content, string $extension): array
    {
        return [
            'content' => $content,
            'extension' => 'pdf',
        ];
    }
}

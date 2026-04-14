<?php

declare(strict_types=1);

namespace App\Services;

final class AIService
{
    public function summarize(string $text): string
    {
        return mb_strimwidth($text, 0, 120, '...');
    }
}

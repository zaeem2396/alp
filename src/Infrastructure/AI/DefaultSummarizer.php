<?php

declare(strict_types=1);

namespace App\Infrastructure\AI;

use App\Contracts\SummarizerInterface;

final class DefaultSummarizer implements SummarizerInterface
{
    public function summarize(string $text): string
    {
        return mb_strimwidth(trim($text), 0, 220, '...');
    }
}

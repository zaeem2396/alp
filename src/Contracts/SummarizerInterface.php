<?php

declare(strict_types=1);

namespace App\Contracts;

interface SummarizerInterface
{
    public function summarize(string $text): string;
}

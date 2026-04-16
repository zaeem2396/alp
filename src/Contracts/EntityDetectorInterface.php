<?php

declare(strict_types=1);

namespace App\Contracts;

interface EntityDetectorInterface
{
    /**
     * @return array<string, mixed>
     */
    public function detect(string $text): array;
}

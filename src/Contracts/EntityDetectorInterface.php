<?php

declare(strict_types=1);

namespace App\Contracts;

interface EntityDetectorInterface
{
    /**
     * @param  array<string, string>  $schema
     * @return array<string, mixed>
     */
    public function detect(string $text, array $schema = []): array;
}

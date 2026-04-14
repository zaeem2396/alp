<?php

declare(strict_types=1);

namespace App\Contracts;

interface NormalizerInterface
{
    public function supports(string $extension): bool;

    /**
     * @return array{content:string,extension:string}
     */
    public function normalize(string $content, string $extension): array;
}

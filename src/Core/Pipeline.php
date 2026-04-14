<?php

declare(strict_types=1);

namespace App\Core;

final class Pipeline
{
    /**
     * @param  list<string>  $steps
     */
    public function __construct(
        public readonly string $name,
        public readonly array $steps
    ) {}
}

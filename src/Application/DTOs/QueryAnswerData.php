<?php

declare(strict_types=1);

namespace App\Application\DTOs;

final class QueryAnswerData
{
    /**
     * @param  list<int>  $citations
     */
    public function __construct(
        public readonly string $answer,
        public readonly array $citations
    ) {}
}

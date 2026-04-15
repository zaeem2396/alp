<?php

declare(strict_types=1);

namespace App\Domain\Document\ValueObjects;

use InvalidArgumentException;

final class DocumentId
{
    public function __construct(public readonly string $value)
    {
        if (trim($this->value) === '') {
            throw new InvalidArgumentException('DocumentId cannot be empty.');
        }
    }

    public static function generate(): self
    {
        return new self(uniqid('doc_', true));
    }
}

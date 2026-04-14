<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

final class DocumentNormalizationException extends RuntimeException
{
    public function __construct(string $message, public readonly bool $recoverable = false)
    {
        parent::__construct($message);
    }
}

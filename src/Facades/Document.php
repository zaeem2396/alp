<?php

declare(strict_types=1);

namespace App\Facades;

final class Document
{
    public static function upload(string $path): string
    {
        return sprintf('Uploaded: %s', $path);
    }
}

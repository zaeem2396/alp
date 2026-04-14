<?php

declare(strict_types=1);

namespace App\Services\Layout;

use App\Contracts\LayoutParserInterface;

final class DefaultLayoutParser implements LayoutParserInterface
{
    public function parse(string $text): array
    {
        $lines = preg_split('/\R/', trim($text)) ?: [];
        $zones = [];
        foreach ($lines as $index => $line) {
            if (trim($line) === '') {
                continue;
            }

            $zones[] = [
                'type' => $index === 0 ? 'header' : 'paragraph',
                'text' => $line,
                'page' => 1,
            ];
        }

        return [
            'zones' => $zones,
            'pages' => 1,
        ];
    }
}

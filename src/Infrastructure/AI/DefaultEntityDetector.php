<?php

declare(strict_types=1);

namespace App\Infrastructure\AI;

use App\Contracts\EntityDetectorInterface;

final class DefaultEntityDetector implements EntityDetectorInterface
{
    public function detect(string $text): array
    {
        preg_match('/\b\d{4}-\d{2}-\d{2}\b/', $text, $dateMatches);
        preg_match('/\b\d+(?:\.\d{2})\b/', $text, $amountMatches);
        preg_match('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', $text, $emailMatches);

        return [
            'date' => [
                'value' => $dateMatches[0] ?? null,
                'confidence' => ($dateMatches[0] ?? null) === null ? 0.0 : 0.8,
            ],
            'amount' => [
                'value' => $amountMatches[0] ?? null,
                'confidence' => ($amountMatches[0] ?? null) === null ? 0.0 : 0.8,
            ],
            'email' => [
                'value' => $emailMatches[0] ?? null,
                'confidence' => ($emailMatches[0] ?? null) === null ? 0.0 : 0.8,
            ],
        ];
    }
}

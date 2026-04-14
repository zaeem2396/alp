<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Contracts\AiProviderInterface;

final class LocalAiProvider implements AiProviderInterface
{
    public function summarize(string $text, array $context = []): string
    {
        $limit = (int) ($context['limit'] ?? 160);

        return mb_strimwidth(trim($text), 0, $limit, '...');
    }

    public function extractEntities(string $text, array $schema = [], array $context = []): array
    {
        $entities = [];
        foreach ($schema as $field => $pattern) {
            if (! is_string($pattern)) {
                continue;
            }

            preg_match($pattern, $text, $matches);
            $entities[$field] = [
                'value' => $matches[0] ?? null,
                'confidence' => $matches !== [] ? 0.8 : 0.0,
            ];
        }

        return $entities;
    }

    public function answerQuestion(string $question, array $chunks, array $context = []): array
    {
        $firstChunk = $chunks[0] ?? '';
        $hasUsableContext = trim($firstChunk) !== '';
        $answer = $hasUsableContext
            ? sprintf('Answer based on chunk: %s', mb_strimwidth($firstChunk, 0, 120, '...'))
            : sprintf('No context available to answer: %s', $question);

        return [
            'answer' => $answer,
            'citations' => $hasUsableContext ? [0] : [],
        ];
    }
}

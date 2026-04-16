<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Contracts\AiProviderInterface;

final class OpenAiProvider implements AiProviderInterface
{
    public function summarize(string $text, array $context = []): string
    {
        $limit = (int) ($context['limit'] ?? 220);

        return sprintf('[openai] %s', mb_strimwidth(trim($text), 0, $limit, '...'));
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
                'confidence' => $matches === [] ? 0.0 : 0.92,
                'model' => 'openai',
            ];
        }

        return $entities;
    }

    public function answerQuestion(string $question, array $chunks, array $context = []): array
    {
        $firstChunk = trim($chunks[0] ?? '');

        if ($firstChunk === '') {
            return [
                'answer' => sprintf('[openai] No context for: %s', $question),
                'citations' => [],
            ];
        }

        return [
            'answer' => sprintf('[openai] %s', mb_strimwidth($firstChunk, 0, 160, '...')),
            'citations' => [0],
        ];
    }
}

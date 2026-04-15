<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Contracts\AiProviderInterface;

final class AnthropicProvider implements AiProviderInterface
{
    public function summarize(string $text, array $context = []): string
    {
        $limit = (int) ($context['limit'] ?? 220);

        return sprintf('[anthropic] %s', mb_strimwidth(trim($text), 0, $limit, '...'));
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
                'confidence' => $matches === [] ? 0.0 : 0.9,
                'model' => 'anthropic-placeholder',
            ];
        }

        return $entities;
    }

    public function answerQuestion(string $question, array $chunks, array $context = []): array
    {
        $firstChunk = trim($chunks[0] ?? '');

        if ($firstChunk === '') {
            return [
                'answer' => sprintf('[anthropic] No context for: %s', $question),
                'citations' => [],
            ];
        }

        return [
            'answer' => sprintf('[anthropic] %s', mb_strimwidth($firstChunk, 0, 160, '...')),
            'citations' => [0],
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\AI\Providers;

use App\Contracts\AiProviderInterface;
use App\Services\AI\LocalAiProvider;

final class LocalProvider implements AiProviderInterface
{
    public function __construct(private readonly LocalAiProvider $provider) {}

    public function summarize(string $text, array $context = []): string
    {
        return $this->provider->summarize($text, $context);
    }

    public function extractEntities(string $text, array $schema = [], array $context = []): array
    {
        return $this->provider->extractEntities($text, $schema, $context);
    }

    public function answerQuestion(string $question, array $chunks, array $context = []): array
    {
        return $this->provider->answerQuestion($question, $chunks, $context);
    }
}

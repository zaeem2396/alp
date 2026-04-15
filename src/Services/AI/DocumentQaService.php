<?php

declare(strict_types=1);

namespace App\Services\AI;

final class DocumentQaService
{
    /**
     * @var array<string, array{answer:string,citations:list<int>}>
     */
    private array $cache = [];

    public function __construct(private readonly AiManager $aiManager) {}

    /**
     * @param  array<int, string>  $chunks
     * @return array{answer:string,citations:list<int>}
     */
    public function ask(string $question, array $chunks, ?string $provider = null): array
    {
        $cacheKey = sha1(sprintf('%s|%s|%s', $provider ?? 'default', $question, implode('|', $chunks)));
        if (array_key_exists($cacheKey, $this->cache)) {
            return $this->cache[$cacheKey];
        }

        $answer = $this->aiManager->provider($provider)->answerQuestion($question, $chunks);
        $this->cache[$cacheKey] = $answer;

        return $answer;
    }
}

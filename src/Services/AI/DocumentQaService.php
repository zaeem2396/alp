<?php

declare(strict_types=1);

namespace App\Services\AI;

final class DocumentQaService
{
    public function __construct(private readonly AiManager $aiManager) {}

    /**
     * @param  array<int, string>  $chunks
     * @return array{answer:string,citations:list<int>}
     */
    public function ask(string $question, array $chunks, ?string $provider = null): array
    {
        return $this->aiManager->provider($provider)->answerQuestion($question, $chunks);
    }
}

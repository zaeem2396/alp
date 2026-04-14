<?php

declare(strict_types=1);

namespace App\Contracts;

interface AiProviderInterface
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function summarize(string $text, array $context = []): string;

    /**
     * @param  array<string, mixed>  $schema
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public function extractEntities(string $text, array $schema = [], array $context = []): array;

    /**
     * @param  array<int, string>  $chunks
     * @param  array<string, mixed>  $context
     * @return array{answer:string,citations:list<int>}
     */
    public function answerQuestion(string $question, array $chunks, array $context = []): array;
}

<?php

declare(strict_types=1);

namespace App\Pipelines\Steps;

use App\Contracts\SummarizerInterface;
use App\Pipelines\Contracts\PipelineStepInterface;
use InvalidArgumentException;

final class AISummarize implements PipelineStepInterface
{
    public function __construct(private readonly SummarizerInterface $summarizer) {}

    public function handle(array $context): array
    {
        $text = $context['text'] ?? null;
        if (! is_string($text) || trim($text) === '') {
            throw new InvalidArgumentException('Pipeline context must contain extracted text before summarization.');
        }

        $context['summary'] = $this->summarizer->summarize($text);

        return $context;
    }
}

<?php

declare(strict_types=1);

namespace App\Pipelines\Steps;

use App\Contracts\TextExtractorInterface;
use App\Pipelines\Contracts\PipelineStepInterface;
use InvalidArgumentException;

final class ExtractText implements PipelineStepInterface
{
    public function __construct(private readonly TextExtractorInterface $extractor) {}

    public function handle(array $context): array
    {
        $filePath = $context['file'] ?? $context['file_path'] ?? null;
        if (! is_string($filePath) || $filePath === '') {
            throw new InvalidArgumentException('Pipeline context must contain a non-empty file path.');
        }

        $context['text'] = $this->extractor->extract($filePath);

        return $context;
    }
}

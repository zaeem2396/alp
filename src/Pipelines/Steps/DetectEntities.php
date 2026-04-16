<?php

declare(strict_types=1);

namespace App\Pipelines\Steps;

use App\Contracts\EntityDetectorInterface;
use App\Pipelines\Contracts\PipelineStepInterface;
use InvalidArgumentException;

final class DetectEntities implements PipelineStepInterface
{
    public function __construct(private readonly EntityDetectorInterface $detector) {}

    public function handle(array $context): array
    {
        $text = $context['text'] ?? null;
        if (! is_string($text) || trim($text) === '') {
            throw new InvalidArgumentException('Pipeline context must contain extracted text before entity detection.');
        }

        $schema = $context['schema'] ?? [];
        if (! is_array($schema)) {
            throw new InvalidArgumentException('Pipeline context schema must be an array of field patterns.');
        }

        $context['entities'] = $this->detector->detect($text, $schema);

        return $context;
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Pipeline\ValueObjects;

final readonly class PipelineContext
{
    /**
     * @param  array<string, mixed>  $payload
     */
    private function __construct(private array $payload) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self($payload);
    }

    /**
     * @param  array<string, mixed>  $delta
     */
    public function mergedWith(array $delta): self
    {
        return new self(array_replace($this->payload, $delta));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->payload;
    }
}

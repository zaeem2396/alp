<?php

declare(strict_types=1);

namespace App\Infrastructure\Events;

final readonly class EventEnvelope
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public string $eventId,
        public string $eventName,
        public string $occurredAt,
        public ?string $correlationId,
        public array $payload,
    ) {}
}

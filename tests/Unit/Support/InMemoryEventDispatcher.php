<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use Illuminate\Contracts\Events\Dispatcher;

final class InMemoryEventDispatcher implements Dispatcher
{
    /**
     * @var list<object>
     */
    public array $events = [];

    /**
     * @param  array<int, string>|string  $events
     * @param  array<int, mixed>|callable|string|null  $listener
     */
    public function listen($events, $listener = null): void {}

    public function hasListeners($eventName): bool
    {
        return false;
    }

    public function subscribe($subscriber): void {}

    /**
     * @param  array<int, mixed>  $payload
     */
    public function until($event, $payload = []): mixed
    {
        return null;
    }

    /**
     * @param  array<int, mixed>  $payload
     * @return array<int, mixed>|null
     */
    public function dispatch($event, $payload = [], $halt = false): ?array
    {
        if (is_object($event)) {
            $this->events[] = $event;
        }

        return null;
    }

    /**
     * @param  array<int, mixed>  $payload
     */
    public function push($event, $payload = []): void {}

    public function flush($event): void {}

    public function forget($event): void {}

    public function forgetPushed(): void {}
}

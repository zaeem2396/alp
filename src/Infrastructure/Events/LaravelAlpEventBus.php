<?php

declare(strict_types=1);

namespace App\Infrastructure\Events;

use App\Contracts\AlpEventBusInterface;
use Illuminate\Contracts\Events\Dispatcher;

final class LaravelAlpEventBus implements AlpEventBusInterface
{
    public function __construct(private readonly Dispatcher $dispatcher) {}

    public function publish(object $event): void
    {
        $this->dispatcher->dispatch($event);
    }
}

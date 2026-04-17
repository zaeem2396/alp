<?php

declare(strict_types=1);

namespace App\Contracts;

interface AlpEventBusInterface
{
    public function publish(object $event): void;
}

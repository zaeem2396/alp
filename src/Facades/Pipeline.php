<?php

declare(strict_types=1);

namespace App\Facades;

final class Pipeline
{
    public static function run(string $pipelineName): string
    {
        return sprintf('Pipeline ran: %s', $pipelineName);
    }
}

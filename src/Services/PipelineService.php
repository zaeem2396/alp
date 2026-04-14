<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Pipeline;

final class PipelineService
{
    /**
     * @param  list<string>  $steps
     */
    public function define(string $name, array $steps): Pipeline
    {
        return new Pipeline($name, $steps);
    }
}

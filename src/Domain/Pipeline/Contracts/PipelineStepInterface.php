<?php

declare(strict_types=1);

namespace App\Domain\Pipeline\Contracts;

use App\Pipelines\Contracts\PipelineStepInterface as PortablePipelineStepInterface;

/**
 * Domain alias for {@see PortablePipelineStepInterface} so pipeline definitions
 * stay aligned with the portable pipeline layer.
 */
interface PipelineStepInterface extends PortablePipelineStepInterface {}

<?php

declare(strict_types=1);

namespace App\Domain\Pipeline\Contracts;

/**
 * Marker for domain or step failures that should not trigger further queue retries.
 */
interface NonRetryablePipelineFailure {}

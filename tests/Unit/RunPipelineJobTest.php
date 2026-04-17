<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Jobs\RunPipelineJob;
use PHPUnit\Framework\TestCase;

final class RunPipelineJobTest extends TestCase
{
    public function test_unique_id_prefers_explicit_lock_then_correlation(): void
    {
        $lockJob = new RunPipelineJob('extract-basic', [
            '_unique_lock' => 'doc-9',
            '_correlation_id' => 'corr-1',
        ]);

        self::assertSame('extract-basic:doc-9', $lockJob->uniqueId());

        $corrJob = new RunPipelineJob('extract-basic', [
            '_correlation_id' => 'corr-2',
        ]);

        self::assertSame('extract-basic:corr-2', $corrJob->uniqueId());
    }
}

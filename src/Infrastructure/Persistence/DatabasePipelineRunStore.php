<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Application\Contracts\PipelineRunStoreInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class DatabasePipelineRunStore implements PipelineRunStoreInterface
{
    public function openRun(string $pipelineName, array $context, ?string $correlationId = null): string
    {
        $id = (string) Str::uuid();
        $now = now();

        DB::table('pipeline_runs')->insert([
            'id' => $id,
            'pipeline_name' => $pipelineName,
            'status' => 'running',
            'correlation_id' => $correlationId,
            'error_message' => null,
            'started_at' => $now,
            'completed_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $id;
    }

    public function finalizeSuccessfulRun(string $runId): void
    {
        $now = now();

        DB::table('pipeline_runs')->where('id', $runId)->update([
            'status' => 'completed',
            'completed_at' => $now,
            'updated_at' => $now,
        ]);
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public function recordStepFinished(
        string $runId,
        int $stepIndex,
        string $stepClass,
        int $durationNs,
        array $context,
    ): void {
        $now = now();
        $durationMs = max(0, intdiv($durationNs, 1_000_000));

        DB::table('pipeline_run_steps')->upsert(
            [[
                'id' => (string) Str::uuid(),
                'pipeline_run_id' => $runId,
                'step_index' => $stepIndex,
                'step_class' => $stepClass,
                'status' => 'completed',
                'duration_ms' => $durationMs,
                'error_message' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]],
            ['pipeline_run_id', 'step_index'],
            ['step_class', 'status', 'duration_ms', 'error_message', 'updated_at'],
        );
    }

    public function finalizeFailedRun(
        string $runId,
        int $failedStepIndex,
        string $stepClass,
        string $errorMessage,
    ): void {
        $now = now();

        DB::table('pipeline_runs')->where('id', $runId)->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'completed_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('pipeline_run_steps')->upsert(
            [[
                'id' => (string) Str::uuid(),
                'pipeline_run_id' => $runId,
                'step_index' => $failedStepIndex,
                'step_class' => $stepClass,
                'status' => 'failed',
                'duration_ms' => null,
                'error_message' => $errorMessage,
                'created_at' => $now,
                'updated_at' => $now,
            ]],
            ['pipeline_run_id', 'step_index'],
            ['step_class', 'status', 'duration_ms', 'error_message', 'updated_at'],
        );
    }
}

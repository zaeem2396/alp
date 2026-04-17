<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pipeline_run_steps', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('pipeline_run_id')->constrained('pipeline_runs')->cascadeOnDelete();
            $table->unsignedInteger('step_index');
            $table->string('step_class');
            $table->string('status')->default('completed');
            $table->unsignedInteger('duration_ms')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->unique(['pipeline_run_id', 'step_index']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pipeline_run_steps');
    }
};

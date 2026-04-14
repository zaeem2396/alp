<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('structured_documents', function (Blueprint $table): void {
            $table->string('document_id')->primary();
            $table->string('schema');
            $table->json('payload');
            $table->unsignedInteger('version')->default(1);
            $table->timestamp('indexed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('structured_documents');
    }
};

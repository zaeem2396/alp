<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Events\DocumentFailed;
use App\Events\DocumentProcessed;
use App\Services\TextExtractionService;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;

final class ProcessDocumentJob implements ShouldQueue
{
    public function __construct(
        private readonly string $documentId,
        private readonly string $filePath
    ) {}

    public function handle(TextExtractionService $textExtraction, Dispatcher $events): void
    {
        try {
            $textExtraction->extract($this->filePath);
            $events->dispatch(new DocumentProcessed($this->documentId, $this->filePath));
        } catch (\Throwable $throwable) {
            $events->dispatch(new DocumentFailed($this->documentId, $throwable->getMessage()));
            throw $throwable;
        }
    }
}

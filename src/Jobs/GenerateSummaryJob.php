<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\AI\DocumentSummarizationService;
use Illuminate\Contracts\Queue\ShouldQueue;

final class GenerateSummaryJob implements ShouldQueue
{
    public function __construct(
        private readonly string $documentId,
        private readonly string $text
    ) {}

    public function handle(DocumentSummarizationService $summarizationService): string
    {
        return $summarizationService->summarize($this->documentId, $this->text);
    }
}

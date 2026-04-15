<?php

declare(strict_types=1);

namespace App\Application\Jobs;

use App\Services\TableDetectionService;
use Illuminate\Contracts\Queue\ShouldQueue;

final class ExtractTablesJob implements ShouldQueue
{
    public function __construct(
        private readonly string $documentId,
        private readonly string $text
    ) {}

    /**
     * @return array{tables:list<array{table_id:string,cells:list<array{row:int,col:int,text:string}>,confidence:float}>}
     */
    public function handle(TableDetectionService $service): array
    {
        return $service->detectForDocument($this->documentId, $this->text);
    }
}

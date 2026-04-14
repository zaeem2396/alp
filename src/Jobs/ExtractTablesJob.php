<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\TableDetectionService;
use Illuminate\Contracts\Queue\ShouldQueue;

final class ExtractTablesJob implements ShouldQueue
{
    public function __construct(
        private readonly string $documentId,
        private readonly string $text
    ) {}

    /**
     * @return array{document_id:string,tables:list<array{table_id:string,cells:list<array{row:int,col:int,text:string}>,confidence:float}>}
     */
    public function handle(TableDetectionService $tableDetection): array
    {
        $tables = $tableDetection->detect($this->text);

        return [
            'document_id' => $this->documentId,
            'tables' => $tables['tables'],
        ];
    }
}

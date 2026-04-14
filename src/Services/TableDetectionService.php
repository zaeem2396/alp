<?php

declare(strict_types=1);

namespace App\Services;

final class TableDetectionService
{
    /**
     * @return array{tables:list<array{table_id:string,cells:list<array{row:int,col:int,text:string}>,confidence:float}>}
     */
    public function detect(string $text): array
    {
        $lines = preg_split('/\R/', trim($text)) ?: [];
        $rows = [];
        foreach ($lines as $rowIndex => $line) {
            if (! str_contains($line, ',')) {
                continue;
            }

            $columns = array_map('trim', explode(',', $line));
            foreach ($columns as $columnIndex => $column) {
                $rows[] = [
                    'row' => $rowIndex,
                    'col' => $columnIndex,
                    'text' => $column,
                ];
            }
        }

        if ($rows === []) {
            return ['tables' => []];
        }

        return [
            'tables' => [
                [
                    'table_id' => uniqid('tbl_', true),
                    'cells' => $rows,
                    'confidence' => 0.7,
                ],
            ],
        ];
    }
}

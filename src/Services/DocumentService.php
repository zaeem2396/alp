<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\DocumentRepositoryInterface;
use App\Core\Document;

final class DocumentService
{
    public function __construct(
        private readonly DocumentIngestionService $ingestionService,
        private readonly DocumentRepositoryInterface $repository
    ) {}

    public function upload(string $name, string $content, string $extension): Document
    {
        return $this->ingestionService->ingest($name, $content, $extension);
    }

    public function find(string $id): ?Document
    {
        return $this->repository->find($id);
    }
}

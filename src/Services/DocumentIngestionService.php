<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\DocumentRepositoryInterface;
use App\Contracts\DocumentStorageInterface;
use App\Core\Document;
use App\Events\DocumentNormalized;
use App\Events\DocumentUploaded;
use Illuminate\Contracts\Events\Dispatcher;

final class DocumentIngestionService
{
    public function __construct(
        private readonly DocumentNormalizerService $normalizer,
        private readonly DocumentStorageInterface $storage,
        private readonly DocumentRepositoryInterface $repository,
        private readonly Dispatcher $events
    ) {}

    public function ingest(string $name, string $content, string $extension): Document
    {
        $documentId = uniqid('doc_', true);
        $rawPath = $this->storage->storeRaw($documentId, $content, $extension);
        $this->events->dispatch(new DocumentUploaded($documentId, $rawPath));

        $normalized = $this->normalizer->normalize($content, $extension);
        $processedPath = $this->storage->storeProcessed(
            $documentId,
            $normalized['content'],
            $normalized['extension']
        );
        $this->events->dispatch(new DocumentNormalized($documentId, $processedPath));

        $document = new Document($documentId, $name, 'uploaded', $rawPath, $processedPath);

        return $this->repository->save($document);
    }
}

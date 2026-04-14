<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Services\DocumentService;
use Illuminate\Http\JsonResponse;

final class DocumentController
{
    public function __construct(private readonly DocumentService $documentService) {}

    public function store(StoreDocumentRequest $request): JsonResponse
    {
        $document = $this->documentService->upload(
            (string) $request->input('name'),
            (string) $request->input('content'),
            (string) $request->input('extension')
        );

        return new JsonResponse([
            'id' => $document->id,
            'name' => $document->name,
            'status' => $document->status,
        ], JsonResponse::HTTP_CREATED);
    }
}

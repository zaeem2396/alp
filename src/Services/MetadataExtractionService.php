<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ApryseClientInterface;
use App\Events\MetadataExtracted;
use Illuminate\Contracts\Events\Dispatcher;

final class MetadataExtractionService
{
    public function __construct(
        private readonly ApryseClientInterface $apryseClient,
        private readonly Dispatcher $events
    ) {}

    /**
     * @return array<string, scalar|null>
     */
    public function extract(string $documentId, string $filePath): array
    {
        $metadata = $this->apryseClient->extractMetadata($filePath);
        $this->events->dispatch(new MetadataExtracted($documentId, $metadata));

        return $metadata;
    }
}

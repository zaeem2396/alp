<?php

declare(strict_types=1);

namespace App\Infrastructure\Apryse;

use App\Contracts\ApryseClientInterface;
use App\Contracts\TextExtractorInterface;

final class ApryseTextExtractor implements TextExtractorInterface
{
    public function __construct(private readonly ApryseClientInterface $client) {}

    public function extract(string $filePath): string
    {
        $payload = $this->client->extractText($filePath);
        $pages = $payload['pages'];

        return implode("\n", array_map(
            static fn (array $page): string => $page['text'],
            $pages
        ));
    }
}

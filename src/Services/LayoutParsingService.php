<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\LayoutParserInterface;

final class LayoutParsingService
{
    public function __construct(
        private readonly LayoutParserInterface $layoutParser,
        private readonly StructuredDocumentService $structuredDocuments
    ) {}

    /**
     * @return array{zones:list<array{type:string,text:string,page:int}>,pages:int}
     */
    public function parse(string $text): array
    {
        return $this->layoutParser->parse($text);
    }

    /**
     * @return array{zones:list<array{type:string,text:string,page:int}>,pages:int}
     */
    public function parseForDocument(string $documentId, string $text): array
    {
        $layout = $this->parse($text);
        $this->structuredDocuments->store($documentId, 'layout_v1', ['layout' => $layout], 1);

        return $layout;
    }
}

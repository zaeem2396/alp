<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\LayoutParserInterface;

final class LayoutParsingService
{
    public function __construct(private readonly LayoutParserInterface $layoutParser) {}

    /**
     * @return array{zones:list<array{type:string,text:string,page:int}>,pages:int}
     */
    public function parse(string $text): array
    {
        return $this->layoutParser->parse($text);
    }
}

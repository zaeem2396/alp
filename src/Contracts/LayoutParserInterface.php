<?php

declare(strict_types=1);

namespace App\Contracts;

interface LayoutParserInterface
{
    /**
     * @return array{zones:list<array{type:string,text:string,page:int}>,pages:int}
     */
    public function parse(string $text): array;
}

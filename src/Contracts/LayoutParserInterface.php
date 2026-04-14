<?php

declare(strict_types=1);

namespace App\Contracts;

interface LayoutParserInterface
{
    /**
     * @param  string  $text
     * @return array{zones:list<array{type:string,text:string,page:int}>,pages:int}
     */
    public function parse(string $text): array;
}

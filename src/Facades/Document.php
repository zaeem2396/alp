<?php

declare(strict_types=1);

namespace App\Facades;

use App\Services\DocumentManager;
use Illuminate\Support\Facades\Facade;

final class Document extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return DocumentManager::class;
    }
}

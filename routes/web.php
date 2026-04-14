<?php

declare(strict_types=1);

use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

Route::post('/documents', [DocumentController::class, 'store']);

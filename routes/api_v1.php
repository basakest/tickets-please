<?php

use App\Http\Controllers\Api\V1\AuthorTicketsController;
use App\Http\Controllers\Api\V1\TicketController;
use App\Http\Controllers\Api\V1\AuthorsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::apiResource('tickets', TicketController::class);
    Route::put('tickets/{ticket}', [TicketController::class, 'replace']);
    Route::apiResource('authors', AuthorsController::class);
    Route::apiResource('authors.tickets', AuthorTicketsController::class);
    Route::put('authors/{author}/tickets/{ticket}', [AuthorTicketsController::class, 'replace']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
})->middleware('auth:sanctum');

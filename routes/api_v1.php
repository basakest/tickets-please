<?php

use App\Http\Controllers\Api\V1\AuthorTicketsController;
use App\Http\Controllers\Api\V1\TicketController;
use App\Http\Controllers\Api\V1\AuthorsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::apiResource('tickets', TicketController::class)->middleware('auth:sanctum');
    Route::apiResource('authors', AuthorsController::class)->middleware('auth:sanctum');
    Route::apiResource('authors.tickets', AuthorTicketsController::class)->middleware('auth:sanctum');
});

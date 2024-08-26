<?php

use App\Http\Controllers\Api\V1\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::apiResource('tickets', TicketController::class)->middleware('auth:sanctum');
});

// Route::get('/tickets', function () {
//     return Ticket::all();
// });

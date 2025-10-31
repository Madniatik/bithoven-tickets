<?php

use Illuminate\Support\Facades\Route;
use Bithoven\Tickets\Http\Controllers\Api\TicketApiController;

Route::middleware(['api', 'auth:sanctum'])->prefix('api/v1')->group(function () {
    Route::apiResource('tickets', TicketApiController::class);
    
    // Ticket actions
    Route::post('tickets/{ticket}/assign', [TicketApiController::class, 'assign']);
    Route::post('tickets/{ticket}/comments', [TicketApiController::class, 'addComment']);
    Route::post('tickets/{ticket}/close', [TicketApiController::class, 'close']);
    Route::post('tickets/{ticket}/reopen', [TicketApiController::class, 'reopen']);
});

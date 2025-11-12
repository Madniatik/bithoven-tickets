<?php

use Illuminate\Support\Facades\Route;
use Bithoven\Tickets\Http\Controllers\TicketController;
use Bithoven\Tickets\Http\Controllers\TicketCategoryController;

Route::middleware(['web', 'extension.enabled:tickets', 'auth'])->group(function () {
    // Tickets
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', [TicketController::class, 'index'])->name('index');
        Route::get('/create', [TicketController::class, 'create'])->name('create');
        Route::post('/', [TicketController::class, 'store'])->name('store');
        Route::get('/{ticket}', [TicketController::class, 'show'])->name('show');
        // Route::get('/{ticket}/edit', [TicketController::class, 'edit'])->name('edit'); // Eliminada - no necesaria
        Route::put('/{ticket}', [TicketController::class, 'update'])->name('update'); // Solo para cambios de status vía AJAX
        Route::delete('/{ticket}', [TicketController::class, 'destroy'])->name('destroy');
        
        // Ticket actions
        Route::post('/{ticket}/assign', [TicketController::class, 'assign'])->name('assign');
        Route::post('/{ticket}/comments', [TicketController::class, 'addComment'])->name('comments.store');
        Route::post('/{ticket}/close', [TicketController::class, 'close'])->name('close');
        Route::post('/{ticket}/reopen', [TicketController::class, 'reopen'])->name('reopen');
    });

    // Ticket Categories (Admin only)
    Route::prefix('admin/ticket-categories')->name('ticket-categories.')->middleware('permission:manage-ticket-categories')->group(function () {
        Route::get('/', [TicketCategoryController::class, 'index'])->name('index');
        Route::get('/create', [TicketCategoryController::class, 'create'])->name('create');
        Route::post('/', [TicketCategoryController::class, 'store'])->name('store');
        Route::get('/{category}/edit', [TicketCategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [TicketCategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [TicketCategoryController::class, 'destroy'])->name('destroy');
    });
});

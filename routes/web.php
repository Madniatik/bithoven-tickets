<?php

use Illuminate\Support\Facades\Route;
use Bithoven\Tickets\Http\Controllers\TicketController;
use Bithoven\Tickets\Http\Controllers\TicketCategoryController;
use Bithoven\Tickets\Http\Controllers\TicketDashboardController;
use Bithoven\Tickets\Http\Controllers\TicketTemplateController;
use Bithoven\Tickets\Http\Controllers\CannedResponseController;
use Bithoven\Tickets\Http\Controllers\TicketAutomationRuleController;

Route::middleware(['web', 'extension.enabled:tickets', 'auth'])->group(function () {
    // Tickets Dashboard
    Route::get('/tickets/dashboard', [TicketDashboardController::class, 'index'])->name('tickets.dashboard');
    Route::post('/tickets/dashboard/refresh', [TicketDashboardController::class, 'refresh'])->name('tickets.dashboard.refresh');

    // Tickets
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', [TicketController::class, 'index'])->name('index');
        Route::get('/create', [TicketController::class, 'create'])->name('create');
        Route::post('/', [TicketController::class, 'store'])->name('store');
        Route::get('/{ticket}', [TicketController::class, 'show'])->name('show');
        Route::put('/{ticket}', [TicketController::class, 'update'])->name('update');
        Route::delete('/{ticket}', [TicketController::class, 'destroy'])->name('destroy');
        
        // Ticket actions
        Route::patch('/{ticket}/category', [TicketController::class, 'updateCategory'])->name('update-category');
        Route::patch('/{ticket}/priority', [TicketController::class, 'updatePriority'])->name('update-priority');
        Route::post('/{ticket}/assign', [TicketController::class, 'assign'])->name('assign');
        Route::post('/{ticket}/comments', [TicketController::class, 'addComment'])->name('comments.store');
        Route::delete('/{ticket}/comments/{comment}', [TicketController::class, 'deleteComment'])->name('comments.destroy');
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

    // Ticket Templates - Public endpoint for loading templates (available to all ticket creators)
    Route::get('ticket-templates/{template}', [TicketTemplateController::class, 'show'])
        ->name('tickets.templates.show')
        ->middleware('permission:create-tickets');

    // Ticket Templates (Admin only)
    Route::prefix('admin/ticket-templates')->name('tickets.templates.')->middleware('permission:manage-ticket-categories')->group(function () {
        Route::get('/', [TicketTemplateController::class, 'index'])->name('index');
        Route::get('/create', [TicketTemplateController::class, 'create'])->name('create');
        Route::post('/', [TicketTemplateController::class, 'store'])->name('store');
        Route::get('/{template}/edit', [TicketTemplateController::class, 'edit'])->name('edit');
        Route::put('/{template}', [TicketTemplateController::class, 'update'])->name('update');
        Route::delete('/{template}', [TicketTemplateController::class, 'destroy'])->name('destroy');
    });

    // Canned Responses - Public endpoint for searching responses (available to agents)
    Route::get('canned-responses/search', [CannedResponseController::class, 'search'])
        ->name('tickets.responses.search')
        ->middleware('permission:edit-tickets');

    // Canned Responses (Admin only)
    Route::prefix('admin/canned-responses')->name('tickets.responses.')->middleware('permission:manage-ticket-categories')->group(function () {
        Route::get('/', [CannedResponseController::class, 'index'])->name('index');
        Route::get('/create', [CannedResponseController::class, 'create'])->name('create');
        Route::post('/', [CannedResponseController::class, 'store'])->name('store');
        Route::get('/{response}', [CannedResponseController::class, 'show'])->name('show');
        Route::get('/{response}/edit', [CannedResponseController::class, 'edit'])->name('edit');
        Route::put('/{response}', [CannedResponseController::class, 'update'])->name('update');
        Route::delete('/{response}', [CannedResponseController::class, 'destroy'])->name('destroy');
    });

    // Automation Rules (Admin only)
    Route::prefix('admin/automation-rules')->name('tickets.automation.')->middleware('permission:manage-ticket-categories')->group(function () {
        Route::get('/', [TicketAutomationRuleController::class, 'index'])->name('index');
        Route::get('/create', [TicketAutomationRuleController::class, 'create'])->name('create');
        Route::post('/', [TicketAutomationRuleController::class, 'store'])->name('store');
        Route::get('/{automation}/edit', [TicketAutomationRuleController::class, 'edit'])->name('edit');
        Route::put('/{automation}', [TicketAutomationRuleController::class, 'update'])->name('update');
        Route::delete('/{automation}', [TicketAutomationRuleController::class, 'destroy'])->name('destroy');
        Route::post('/{automation}/toggle', [TicketAutomationRuleController::class, 'toggleActive'])->name('toggle');
        Route::get('/{automation}/logs', [TicketAutomationRuleController::class, 'logs'])->name('logs');
    });
});

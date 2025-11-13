<?php

namespace Bithoven\Tickets\Http\Controllers;

use Bithoven\Tickets\DataTables\CannedResponsesDataTable;
use Bithoven\Tickets\Models\CannedResponse;
use Bithoven\Tickets\Models\TicketCategory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class CannedResponseController extends Controller
{
    /**
     * Display a listing of the canned responses.
     */
    public function index(CannedResponsesDataTable $dataTable)
    {
        Gate::authorize('manage-ticket-categories'); // Reusing existing permission

        return $dataTable->render('tickets::responses.index');
    }

    /**
     * Show the form for creating a new canned response.
     */
    public function create()
    {
        Gate::authorize('manage-ticket-categories');

        $categories = TicketCategory::all();

        return view('tickets::responses.create', compact('categories'));
    }

    /**
     * Store a newly created canned response in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('manage-ticket-categories');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'shortcut' => 'required|string|max:50|unique:canned_responses,shortcut|regex:/^\/[a-z0-9-]+$/',
            'content' => 'required|string',
            'category_id' => 'nullable|exists:ticket_categories,id',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['is_public'] = $request->has('is_public') ? 1 : 0;

        CannedResponse::create($validated);

        return redirect()
            ->route('tickets.responses.index')
            ->with('success', 'Respuesta rápida creada exitosamente.');
    }

    /**
     * Show the form for editing the specified canned response.
     */
    public function edit(CannedResponse $response)
    {
        Gate::authorize('manage-ticket-categories');

        $categories = TicketCategory::all();

        return view('tickets::responses.edit', compact('response', 'categories'));
    }

    /**
     * Update the specified canned response in storage.
     */
    public function update(Request $request, CannedResponse $response)
    {
        Gate::authorize('manage-ticket-categories');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'shortcut' => 'required|string|max:50|regex:/^\/[a-z0-9-]+$/|unique:canned_responses,shortcut,' . $response->id,
            'content' => 'required|string',
            'category_id' => 'nullable|exists:ticket_categories,id',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['is_public'] = $request->has('is_public') ? 1 : 0;

        $response->update($validated);

        return redirect()
            ->route('tickets.responses.index')
            ->with('success', 'Respuesta rápida actualizada exitosamente.');
    }

    /**
     * Remove the specified canned response from storage.
     */
    public function destroy(CannedResponse $response)
    {
        Gate::authorize('manage-ticket-categories');

        $response->delete();

        return redirect()
            ->route('tickets.responses.index')
            ->with('success', 'Respuesta rápida eliminada exitosamente.');
    }

    /**
     * Get canned response data as JSON for quick use.
     */
    public function show(CannedResponse $response)
    {
        Gate::authorize('edit-tickets');

        $response->incrementUsage();

        return response()->json([
            'id' => $response->id,
            'title' => $response->title,
            'content' => $response->content,
            'is_public' => $response->is_public,
        ]);
    }

    /**
     * Search canned responses by shortcut.
     */
    public function search(Request $request)
    {
        Gate::authorize('edit-tickets');

        $shortcut = $request->get('shortcut');

        $responses = CannedResponse::active()
            ->where('shortcut', 'LIKE', "%{$shortcut}%")
            ->limit(10)
            ->get(['id', 'title', 'shortcut', 'content', 'is_public']);

        return response()->json($responses);
    }
}

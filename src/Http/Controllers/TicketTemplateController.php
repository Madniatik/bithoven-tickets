<?php

namespace Bithoven\Tickets\Http\Controllers;

use Bithoven\Tickets\DataTables\TicketTemplatesDataTable;
use Bithoven\Tickets\Models\TicketCategory;
use Bithoven\Tickets\Models\TicketTemplate;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class TicketTemplateController extends Controller
{
    /**
     * Display a listing of the ticket templates.
     */
    public function index(TicketTemplatesDataTable $dataTable)
    {
        Gate::authorize('manage-ticket-categories'); // Reusing existing permission

        return $dataTable->render('tickets::templates.index');
    }

    /**
     * Show the form for creating a new template.
     */
    public function create()
    {
        Gate::authorize('manage-ticket-categories');

        $categories = TicketCategory::all();

        return view('tickets::templates.create', compact('categories'));
    }

    /**
     * Store a newly created template in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('manage-ticket-categories');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'nullable|exists:ticket_categories,id',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        TicketTemplate::create($validated);

        return redirect()
            ->route('tickets.templates.index')
            ->with('success', 'Plantilla creada exitosamente.');
    }

    /**
     * Show the form for editing the specified template.
     */
    public function edit(TicketTemplate $template)
    {
        Gate::authorize('manage-ticket-categories');

        $categories = TicketCategory::all();

        return view('tickets::templates.edit', compact('template', 'categories'));
    }

    /**
     * Update the specified template in storage.
     */
    public function update(Request $request, TicketTemplate $template)
    {
        Gate::authorize('manage-ticket-categories');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'nullable|exists:ticket_categories,id',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $template->update($validated);

        return redirect()
            ->route('tickets.templates.index')
            ->with('success', 'Plantilla actualizada exitosamente.');
    }

    /**
     * Remove the specified template from storage.
     */
    public function destroy(TicketTemplate $template)
    {
        Gate::authorize('manage-ticket-categories');

        $template->delete();

        return redirect()
            ->route('tickets.templates.index')
            ->with('success', 'Plantilla eliminada exitosamente.');
    }

    /**
     * Get template data as JSON for quick use.
     */
    public function show(TicketTemplate $template)
    {
        Gate::authorize('create-tickets');

        $template->incrementUsage();

        return response()->json([
            'id' => $template->id,
            'name' => $template->name,
            'subject' => $template->subject,
            'description' => $template->description,
            'category_id' => $template->category_id,
            'priority' => $template->priority,
        ]);
    }
}

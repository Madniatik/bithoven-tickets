<?php

namespace Bithoven\Tickets\Http\Controllers;

use App\Http\Controllers\Controller;
use Bithoven\Tickets\Models\TicketCategory;
use Bithoven\Tickets\Services\TicketService;
use Illuminate\Http\Request;

class TicketCategoryController extends Controller
{
    protected TicketService $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
        $this->middleware('permission:manage-ticket-categories');
    }

    /**
     * Display a listing of ticket categories
     */
    public function index()
    {
        $categories = TicketCategory::orderBy('sort_order')->orderBy('name')->get();
        
        return view('tickets::categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        return view('tickets::categories.create');
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:ticket_categories,name',
            'slug' => 'nullable|string|max:255|unique:ticket_categories,slug',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = \Str::slug($validated['name']);
        }

        $category = TicketCategory::create($validated);

        return redirect()
            ->route('ticket-categories.index')
            ->with('success', "Category '{$category->name}' created successfully.");
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit(TicketCategory $category)
    {
        return view('tickets::categories.edit', compact('category'));
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, TicketCategory $category)
    {
        $validated = $request->validate([
            'name' => "required|string|max:255|unique:ticket_categories,name,{$category->id}",
            'slug' => "nullable|string|max:255|unique:ticket_categories,slug,{$category->id}",
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $category->update($validated);

        return redirect()
            ->route('ticket-categories.index')
            ->with('success', "Category '{$category->name}' updated successfully.");
    }

    /**
     * Remove the specified category
     */
    public function destroy(TicketCategory $category)
    {
        // Check if category has tickets
        if ($category->tickets()->exists()) {
            return back()->with('error', "Cannot delete category '{$category->name}' because it has associated tickets.");
        }

        $name = $category->name;
        $category->delete();

        return redirect()
            ->route('ticket-categories.index')
            ->with('success', "Category '{$name}' deleted successfully.");
    }
}

<?php

namespace Bithoven\Tickets\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Bithoven\Tickets\Http\Requests\StoreTicketRequest;
use Bithoven\Tickets\Http\Requests\UpdateTicketRequest;
use Bithoven\Tickets\Models\Ticket;
use Bithoven\Tickets\Models\TicketCategory;
use Bithoven\Tickets\Services\AssignmentService;
use Illuminate\Support\Facades\Storage;
use Bithoven\Tickets\Services\TicketService;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function __construct(
        protected TicketService $ticketService,
        protected AssignmentService $assignmentService
    ) {
        $this->middleware(['auth', 'permission:view-tickets']);
    }

    /**
     * Display listing of tickets
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Ticket::class);

        $query = Ticket::with(['user', 'assignedUser', 'category'])
            ->latest();

        // Check if user is admin/agent (can edit tickets or manage categories)
        $isAdmin = auth()->user()->can('edit-tickets') || auth()->user()->can('manage-ticket-categories');

        // If not admin, only show user's own tickets (created or assigned)
        if (! $isAdmin) {
            $query->where(function ($q) {
                $q->where('user_id', auth()->id())
                    ->orWhere('assigned_to', auth()->id());
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by assigned user (only for admins)
        if ($isAdmin && $request->filled('assigned_to')) {
            if ($request->assigned_to === 'unassigned') {
                $query->whereNull('assigned_to');
            } else {
                $query->where('assigned_to', $request->assigned_to);
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // My tickets filter (only for admins)
        if ($isAdmin && $request->filled('mine')) {
            $query->where(function ($q) {
                $q->where('user_id', auth()->id())
                    ->orWhere('assigned_to', auth()->id());
            });
        }

        $tickets = $query->paginate($request->get('per_page', config('tickets.pagination.per_page')));

        // Get filter options
        $categories = TicketCategory::active()->ordered()->get();
        $agents = User::permission('edit-tickets')->get();

        // Get statistics filtered by user type
        $statisticsFilters = [];
        if (! $isAdmin) {
            // For normal users, only show their tickets in statistics
            $statisticsFilters['user_id'] = auth()->id();
        }
        $statistics = $this->ticketService->getStatistics($statisticsFilters);

        return view('tickets::tickets.index', compact(
            'tickets',
            'categories',
            'agents',
            'statistics'
        ));
    }

    /**
     * Show form for creating new ticket
     */
    public function create()
    {
        $this->authorize('create', Ticket::class);

        $categories = TicketCategory::active()->ordered()->get();
        $agents = $this->assignmentService->getAvailableAgents();

        return view('tickets::tickets.create', compact('categories', 'agents'));
    }

    /**
     * Store newly created ticket
     */
    public function store(StoreTicketRequest $request)
    {
        $this->authorize('create', Ticket::class);

        $ticket = $this->ticketService->createTicket($request->validated());

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Ticket created successfully');
    }

    /**
     * Display specified ticket
     */
    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $ticket->load([
            'user',
            'assignedUser',
            'category',
            'comments.user',
            'attachments',
        ]);

        $agents = $this->assignmentService->getAvailableAgents();
        $categories = TicketCategory::orderBy('name')->get();

        return view('tickets::tickets.show', compact('ticket', 'agents', 'categories'));
    }

    /**
     * Show form for editing ticket
     */
    public function edit(Ticket $ticket)
    {
        // Si solo se está actualizando el status, usar updateStatus policy
        if ($request->has('status') && count($request->only(['status', 'subject', 'description', 'priority'])) === count($request->validated())) {
            $this->authorize('updateStatus', $ticket);
        } else {
            $this->authorize('update', $ticket);
        }

        $categories = TicketCategory::active()->ordered()->get();
        $agents = $this->assignmentService->getAvailableAgents();

        return view('tickets::tickets.edit', compact('ticket', 'categories', 'agents'));
    }

    /**
     * Update specified ticket
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        // Si solo se está actualizando el status, usar updateStatus policy
        if ($request->has('status') && count($request->only(['status', 'subject', 'description', 'priority'])) === count($request->validated())) {
            $this->authorize('updateStatus', $ticket);
        } else {
            $this->authorize('update', $ticket);
        }

        $this->ticketService->updateTicket($ticket, $request->validated());

        // If it's an AJAX request, return JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ticket updated successfully',
                'ticket' => $ticket->fresh(['user', 'assignedUser', 'category']),
            ]);
        }

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Ticket updated successfully');
    }

    /**
     * Remove specified ticket
     */
    public function destroy(Ticket $ticket)
    {
        $this->authorize('delete', $ticket);

        $ticket->delete();

        return redirect()
            ->route('tickets.index')
            ->with('success', 'Ticket deleted successfully');
    }

    /**
     * Assign ticket to user
     */
    public function assign(Request $request, Ticket $ticket)
    {
        $this->authorize('assign', Ticket::class);

        $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $this->assignmentService->reassign($ticket, $request->assigned_to);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ticket assigned successfully',
                'ticket' => $ticket->fresh(['assignedUser']),
            ]);
        }

        return back()->with('success', 'Ticket assigned successfully');
    }

    /**
     * Add comment to ticket
     */
    public function addComment(Request $request, Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $request->validate([
            'comment' => 'required|string',
            'is_internal' => 'boolean',
            'attachments.*' => 'nullable|file|max:10240', // Max 10MB por archivo
        ]);

        $canAddInternal = auth()->user()->can('addInternalComment', $ticket);

        $comment = $this->ticketService->addComment($ticket, [
            'comment' => $request->comment,
            'is_internal' => $canAddInternal && $request->boolean('is_internal'),
        ]);

        // Manejar archivos adjuntos
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $this->ticketService->addAttachmentToComment($comment, $file);
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'comment' => $comment->load('user', 'attachments'),
            ]);
        }

        return back()->with('success', 'Comment added successfully');
    }

    /**
     * Close ticket
     */
    public function close(Ticket $ticket)
    {
        // Si solo se está actualizando el status, usar updateStatus policy
        if ($request->has('status') && count($request->only(['status', 'subject', 'description', 'priority'])) === count($request->validated())) {
            $this->authorize('updateStatus', $ticket);
        } else {
            $this->authorize('update', $ticket);
        }

        $this->ticketService->closeTicket($ticket);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ticket closed successfully',
            ]);
        }

        return back()->with('success', 'Ticket closed successfully');
    }

    /**
     * Reopen ticket
     */
    public function reopen(Ticket $ticket)
    {
        // Si solo se está actualizando el status, usar updateStatus policy
        if ($request->has('status') && count($request->only(['status', 'subject', 'description', 'priority'])) === count($request->validated())) {
            $this->authorize('updateStatus', $ticket);
        } else {
            $this->authorize('update', $ticket);
        }

        $this->ticketService->reopenTicket($ticket);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ticket reopened successfully',
            ]);
        }

        return back()->with('success', 'Ticket reopened successfully');
    }

    /**
     * Update ticket category
     */
    public function updateCategory(Request $request, Ticket $ticket)
    {
        abort_unless(auth()->user()->can('manage-ticket-categories'), 403, 'Only ticket administrators can change categories');

        $request->validate([
            'category_id' => 'nullable|exists:ticket_categories,id',
        ]);

        $ticket->update([
            'category_id' => $request->category_id,
        ]);

        return redirect()->back()->with('success', 'Category updated successfully');
    }

    /**
     * Delete a comment
     */
    public function deleteComment(Ticket $ticket, $commentId)
    {
        $comment = $ticket->comments()->findOrFail($commentId);

        // Only the comment creator can delete it
        if ($comment->user_id !== auth()->id()) {
            abort(403, 'Only the comment creator can delete it');
        }

        // Delete attachments associated with this comment
        foreach ($comment->attachments as $attachment) {
            // Delete file from storage
            if ($attachment->file_path && \Storage::exists($attachment->file_path)) {
                \Storage::delete($attachment->file_path);
            }
            $attachment->delete();
        }

        $comment->delete();

        return redirect()->back()->with('success', 'Comment deleted successfully');
    }
}

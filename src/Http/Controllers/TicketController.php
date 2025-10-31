<?php

namespace Bithoven\Tickets\Http\Controllers;

use App\Http\Controllers\Controller;
use Bithoven\Tickets\Models\Ticket;
use Bithoven\Tickets\Models\TicketCategory;
use Bithoven\Tickets\Services\TicketService;
use Bithoven\Tickets\Services\AssignmentService;
use Bithoven\Tickets\Http\Requests\StoreTicketRequest;
use Bithoven\Tickets\Http\Requests\UpdateTicketRequest;
use Illuminate\Http\Request;
use App\Models\User;

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

        // Filter by assigned user
        if ($request->filled('assigned_to')) {
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

        // My tickets filter
        if ($request->filled('mine')) {
            $query->where(function ($q) {
                $q->where('user_id', auth()->id())
                  ->orWhere('assigned_to', auth()->id());
            });
        }

        $tickets = $query->paginate($request->get('per_page', config('tickets.pagination.per_page')));

        // Get filter options
        $categories = TicketCategory::active()->ordered()->get();
        $agents = User::permission('edit-tickets')->get();
        $statistics = $this->ticketService->getStatistics();

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
            'attachments'
        ]);

        $agents = $this->assignmentService->getAvailableAgents();

        return view('tickets::tickets.show', compact('ticket', 'agents'));
    }

    /**
     * Show form for editing ticket
     */
    public function edit(Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $categories = TicketCategory::active()->ordered()->get();
        $agents = $this->assignmentService->getAvailableAgents();

        return view('tickets::tickets.edit', compact('ticket', 'categories', 'agents'));
    }

    /**
     * Update specified ticket
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $this->ticketService->updateTicket($ticket, $request->validated());

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
            'assigned_to' => 'required|exists:users,id'
        ]);

        $this->assignmentService->reassign($ticket, $request->assigned_to);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ticket assigned successfully',
                'ticket' => $ticket->fresh(['assignedUser'])
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
            'is_internal' => 'boolean'
        ]);

        $canAddInternal = auth()->user()->can('addInternalComment', $ticket);
        
        $comment = $this->ticketService->addComment($ticket, [
            'comment' => $request->comment,
            'is_internal' => $canAddInternal && $request->boolean('is_internal'),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'comment' => $comment->load('user')
            ]);
        }

        return back()->with('success', 'Comment added successfully');
    }

    /**
     * Close ticket
     */
    public function close(Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $this->ticketService->closeTicket($ticket);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ticket closed successfully'
            ]);
        }

        return back()->with('success', 'Ticket closed successfully');
    }

    /**
     * Reopen ticket
     */
    public function reopen(Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $this->ticketService->reopenTicket($ticket);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ticket reopened successfully'
            ]);
        }

        return back()->with('success', 'Ticket reopened successfully');
    }
}

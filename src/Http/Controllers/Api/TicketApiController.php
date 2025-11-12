<?php

namespace Bithoven\Tickets\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Bithoven\Tickets\Models\Ticket;
use Bithoven\Tickets\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TicketApiController extends Controller
{
    protected TicketService $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    /**
     * Display a listing of tickets
     */
    public function index(Request $request): JsonResponse
    {
        $query = Ticket::with(['user', 'assignedTo', 'category'])
            ->latest();

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->has('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        $tickets = $query->paginate($request->input('per_page', 15));

        return response()->json($tickets);
    }

    /**
     * Store a newly created ticket
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'category_id' => 'required|exists:ticket_categories,id',
        ]);

        $ticket = $this->ticketService->createTicket(
            userId: $request->user()->id,
            subject: $validated['subject'],
            description: $validated['description'],
            priority: $validated['priority'],
            categoryId: $validated['category_id']
        );

        $ticket->load(['user', 'assignedTo', 'category']);

        return response()->json([
            'message' => 'Ticket created successfully',
            'ticket' => $ticket,
        ], 201);
    }

    /**
     * Display the specified ticket
     */
    public function show(Ticket $ticket): JsonResponse
    {
        $ticket->load([
            'user',
            'assignedTo',
            'category',
            'comments.user',
            'attachments.user'
        ]);

        return response()->json($ticket);
    }

    /**
     * Update the specified ticket
     */
    public function update(Request $request, Ticket $ticket): JsonResponse
    {
        $validated = $request->validate([
            'subject' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'status' => 'sometimes|in:open,in_progress,waiting,resolved,closed',
            'category_id' => 'sometimes|exists:ticket_categories,id',
        ]);

        $ticket->update($validated);
        $ticket->load(['user', 'assignedTo', 'category']);

        return response()->json([
            'message' => 'Ticket updated successfully',
            'ticket' => $ticket,
        ]);
    }

    /**
     * Remove the specified ticket
     */
    public function destroy(Ticket $ticket): JsonResponse
    {
        $ticket->delete();

        return response()->json([
            'message' => 'Ticket deleted successfully',
        ]);
    }

    /**
     * Assign ticket to a user
     */
    public function assign(Request $request, Ticket $ticket): JsonResponse
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $success = $this->ticketService->assignTicket(
            ticketId: $ticket->id,
            assignedToId: $validated['assigned_to'],
            assignedById: $request->user()->id
        );

        if ($success) {
            $ticket->refresh()->load(['user', 'assignedTo', 'category']);
            
            return response()->json([
                'message' => 'Ticket assigned successfully',
                'ticket' => $ticket,
            ]);
        }

        return response()->json([
            'message' => 'Failed to assign ticket',
        ], 500);
    }

    /**
     * Add a comment to the ticket
     */
    public function addComment(Request $request, Ticket $ticket): JsonResponse
    {
        $validated = $request->validate([
            'comment' => 'required|string',
            'is_internal' => 'boolean',
            'is_solution' => 'boolean',
        ]);

        $comment = $this->ticketService->addComment(
            ticketId: $ticket->id,
            userId: $request->user()->id,
            comment: $validated['comment'],
            isInternal: $validated['is_internal'] ?? false,
            isSolution: $validated['is_solution'] ?? false
        );

        $comment->load('user');

        return response()->json([
            'message' => 'Comment added successfully',
            'comment' => $comment,
        ], 201);
    }

    /**
     * Close the ticket
     */
    public function close(Ticket $ticket): JsonResponse
    {
        $ticket->update(['status' => 'closed']);
        $ticket->load(['user', 'assignedTo', 'category']);

        return response()->json([
            'message' => 'Ticket closed successfully',
            'ticket' => $ticket,
        ]);
    }

    /**
     * Reopen the ticket
     */
    public function reopen(Ticket $ticket): JsonResponse
    {
        $ticket->update(['status' => 'open']);
        $ticket->load(['user', 'assignedTo', 'category']);

        return response()->json([
            'message' => 'Ticket reopened successfully',
            'ticket' => $ticket,
        ]);
    }
}

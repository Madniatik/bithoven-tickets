<?php

namespace Bithoven\Tickets\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Bithoven\Tickets\DataTables\TicketsDataTable;
use Bithoven\Tickets\Http\Requests\StoreTicketRequest;
use Bithoven\Tickets\Http\Requests\UpdateTicketRequest;
use Bithoven\Tickets\Models\Ticket;
use Bithoven\Tickets\Models\TicketCategory;
use Bithoven\Tickets\Services\AssignmentService;
use Illuminate\Support\Facades\Storage;
use Bithoven\Tickets\Services\TicketService;
use Illuminate\Http\Request;

// Events
use Bithoven\Tickets\Events\TicketCreated;
use Bithoven\Tickets\Events\TicketAssigned;
use Bithoven\Tickets\Events\CommentAdded;
use Bithoven\Tickets\Events\StatusChanged;
use Bithoven\Tickets\Events\PriorityEscalated;

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

    /**
     * Display listing of tickets
     */
    public function index(TicketsDataTable $dataTable)
    {
        $this->authorize('viewAny', Ticket::class);

        // Get filter options
        $categories = TicketCategory::active()->ordered()->get();
        $agents = User::permission('edit-tickets')->get();

        // Get statistics
        $isAdmin = auth()->user()->can('edit-tickets') || auth()->user()->can('manage-ticket-categories');
        $statisticsFilters = [];
        if (!$isAdmin) {
            $statisticsFilters['user_id'] = auth()->id();
        }
        $statistics = $this->ticketService->getStatistics($statisticsFilters);

        return $dataTable->render('tickets::tickets.index', compact(
            'categories',
            'agents',
            'statistics'
        ));
    }

    /**
     * Export tickets to CSV
     */
    protected function exportCsv($tickets)
    {
        $filename = 'tickets-' . now()->format('Y-m-d-His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];
        
        $callback = function() use ($tickets) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, [
                'Ticket #',
                'Subject',
                'Status',
                'Priority',
                'Category',
                'Created By',
                'Assigned To',
                'Created At',
                'Updated At',
                'Description'
            ]);
            
            // Data
            foreach ($tickets as $ticket) {
                fputcsv($file, [
                    $ticket->ticket_number,
                    $ticket->subject,
                    $ticket->status_label,
                    $ticket->priority_label,
                    $ticket->category?->name ?? 'N/A',
                    $ticket->user?->name ?? 'N/A',
                    $ticket->assignedUser?->name ?? 'Unassigned',
                    $ticket->created_at->format('Y-m-d H:i:s'),
                    $ticket->updated_at->format('Y-m-d H:i:s'),
                    strip_tags($ticket->description)
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
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

        // Fire TicketCreated event
        event(new TicketCreated($ticket));

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

        // Capture old values before update
        $oldStatus = $ticket->status;
        $oldPriority = $ticket->priority;

        $this->ticketService->updateTicket($ticket, $request->validated());

        // Fire events for changes
        if ($request->has('status') && $oldStatus !== $ticket->status) {
            event(new StatusChanged($ticket, $oldStatus, $ticket->status, auth()->user()));
        }

        if ($request->has('priority') && $oldPriority !== $ticket->priority) {
            // Only fire escalation event if priority increased
            $priorities = ['low', 'medium', 'high', 'urgent'];
            $oldIndex = array_search($oldPriority, $priorities);
            $newIndex = array_search($ticket->priority, $priorities);
            
            if ($newIndex > $oldIndex) {
                event(new PriorityEscalated($ticket, $oldPriority, $ticket->priority, auth()->user()));
            }
        }

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

        $assignedTo = User::find($request->assigned_to);
        $this->assignmentService->reassign($ticket, $request->assigned_to);

        // Fire TicketAssigned event
        event(new TicketAssigned($ticket, $assignedTo, auth()->user()));

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

        // Fire CommentAdded event (only for public comments)
        if (!$comment->is_internal) {
            event(new CommentAdded($ticket, $comment));
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

        $oldStatus = $ticket->status;
        $this->ticketService->closeTicket($ticket);

        // Fire StatusChanged event
        event(new StatusChanged($ticket, $oldStatus, 'closed', auth()->user()));

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

        $oldStatus = $ticket->status;
        $this->ticketService->reopenTicket($ticket);

        // Fire StatusChanged event
        event(new StatusChanged($ticket, $oldStatus, 'open', auth()->user()));

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
     * Update ticket priority
     */
    public function updatePriority(Request $request, Ticket $ticket)
    {
        abort_unless(auth()->user()->can('manage-ticket-categories'), 403, 'Only ticket administrators can change priority');

        $request->validate([
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $oldPriority = $ticket->priority;
        
        $ticket->update([
            'priority' => $request->priority,
        ]);

        // Fire PriorityEscalated event if priority increased
        $priorities = ['low', 'medium', 'high', 'urgent'];
        $oldIndex = array_search($oldPriority, $priorities);
        $newIndex = array_search($ticket->priority, $priorities);
        
        if ($newIndex > $oldIndex) {
            event(new PriorityEscalated($ticket, $oldPriority, $ticket->priority, auth()->user()));
        }

        return redirect()->back()->with('success', 'Priority updated successfully');
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

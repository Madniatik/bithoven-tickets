<?php

namespace Bithoven\Tickets\Services;

use Bithoven\Tickets\Events\TicketAssigned;
use Bithoven\Tickets\Events\TicketCreated;
use Bithoven\Tickets\Events\TicketResolved;
use Bithoven\Tickets\Models\Ticket;
use Bithoven\Tickets\Models\TicketComment;
use Illuminate\Support\Facades\DB;

class TicketService
{
    /**
     * Create a new ticket
     */
    public function createTicket(array $data): Ticket
    {
        return DB::transaction(function () use ($data) {
            $ticket = Ticket::create([
                'subject' => $data['subject'],
                'description' => $data['description'],
                'priority' => $data['priority'] ?? config('tickets.default_priority'),
                'status' => 'open',
                'user_id' => $data['user_id'] ?? auth()->id(),
                'category_id' => $data['category_id'] ?? null,
                'assigned_to' => $data['assigned_to'] ?? null,
            ]);

            // Handle attachments if provided
            if (! empty($data['attachments'])) {
                $this->attachFiles($ticket, $data['attachments']);
            }

            event(new TicketCreated($ticket));

            return $ticket->fresh(['user', 'assignedUser', 'category']);
        });
    }

    /**
     * Update ticket
     */
    public function updateTicket(Ticket $ticket, array $data): Ticket
    {
        $oldAssignedTo = $ticket->assigned_to;

        $ticket->update(array_filter([
            'subject' => $data['subject'] ?? $ticket->subject,
            'description' => $data['description'] ?? $ticket->description,
            'priority' => $data['priority'] ?? $ticket->priority,
            'status' => $data['status'] ?? $ticket->status,
            'category_id' => $data['category_id'] ?? $ticket->category_id,
            'assigned_to' => $data['assigned_to'] ?? $ticket->assigned_to,
        ]));

        // Fire event if assignment changed
        if ($oldAssignedTo !== $ticket->assigned_to) {
            event(new TicketAssigned($ticket));
        }

        // Fire event if resolved
        if ($ticket->status === 'resolved' && $ticket->wasChanged('status')) {
            event(new TicketResolved($ticket));
        }

        return $ticket->fresh();
    }

    /**
     * Assign ticket to user
     */
    public function assignTicket(Ticket $ticket, int $userId): Ticket
    {
        $ticket->update(['assigned_to' => $userId]);

        event(new TicketAssigned($ticket));

        return $ticket->fresh();
    }

    /**
     * Add comment to ticket
     */
    public function addComment(Ticket $ticket, array $data): TicketComment
    {
        $comment = $ticket->comments()->create([
            'user_id' => $data['user_id'] ?? auth()->id(),
            'comment' => $data['comment'],
            'is_internal' => $data['is_internal'] ?? false,
            'is_solution' => $data['is_solution'] ?? false,
        ]);

        // Update first_response_at if this is the first comment
        if (! $ticket->first_response_at) {
            $ticket->update(['first_response_at' => now()]);
        }

        return $comment;
    }

    /**
     * Attach files to ticket
     */
    public function attachFiles(Ticket $ticket, array $files): array
    {
        $attachments = [];

        foreach ($files as $file) {
            $filename = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs(
                config('tickets.uploads.path'),
                $filename,
                config('tickets.uploads.disk')
            );

            $attachments[] = $ticket->attachments()->create([
                'user_id' => auth()->id(),
                'filename' => $filename,
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'path' => $path,
                'disk' => config('tickets.uploads.disk'),
            ]);
        }

        return $attachments;
    }

    /**
     * Close ticket
     */
    public function closeTicket(Ticket $ticket): Ticket
    {
        $ticket->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return $ticket;
    }

    /**
     * Reopen ticket
     */
    public function reopenTicket(Ticket $ticket): Ticket
    {
        $ticket->update([
            'status' => 'open',
            'closed_at' => null,
            'resolved_at' => null,
        ]);

        return $ticket;
    }

    /**
     * Get ticket statistics
     */
    public function getStatistics(array $filters = []): array
    {
        $query = Ticket::query();

        // Filter by user_id (for normal users to see only their tickets)
        if (! empty($filters['user_id'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('user_id', $filters['user_id'])
                    ->orWhere('assigned_to', $filters['user_id']);
            });
        }

        if (! empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        if (! empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        return [
            'total' => (clone $query)->count(),
            'open' => (clone $query)->where('status', 'open')->count(),
            'in_progress' => (clone $query)->where('status', 'in_progress')->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'resolved' => (clone $query)->where('status', 'resolved')->count(),
            'closed' => (clone $query)->where('status', 'closed')->count(),
            'urgent' => (clone $query)->where('priority', 'urgent')->count(),
            'unassigned' => (clone $query)->whereNull('assigned_to')->count(),
            'avg_response_time' => (clone $query)->whereNotNull('first_response_at')
                ->avg(DB::raw('TIMESTAMPDIFF(HOUR, created_at, first_response_at)')),
            'avg_resolution_time' => (clone $query)->whereNotNull('resolved_at')
                ->avg(DB::raw('TIMESTAMPDIFF(HOUR, created_at, resolved_at)')),
        ];
    }

    /**
     * Close stale tickets
     */
    public function closeStaleTickets(?int $days = null): int
    {
        $days = $days ?? config('tickets.close_after_days', 30);

        $tickets = Ticket::where('status', 'resolved')
            ->where('resolved_at', '<', now()->subDays($days))
            ->get();

        foreach ($tickets as $ticket) {
            $this->closeTicket($ticket);
        }

        return $tickets->count();
    }

    /**
     * Add attachment to comment
     */
    public function addAttachmentToComment($comment, $file)
    {
        $filename = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs(
            config('tickets.uploads.path'),
            $filename,
            config('tickets.uploads.disk')
        );

        return $comment->attachments()->create([
            'ticket_id' => $comment->ticket_id,
            'user_id' => auth()->id(),
            'filename' => $filename,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'path' => $path,
        ]);
    }
}

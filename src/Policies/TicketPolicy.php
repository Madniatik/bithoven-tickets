<?php

namespace Bithoven\Tickets\Policies;

use Bithoven\Tickets\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    /**
     * Determine if user can view any tickets
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view-tickets');
    }

    /**
     * Determine if user can view the ticket
     */
    public function view(User $user, Ticket $ticket): bool
    {
        // User can view if they have permission and either:
        // - They created the ticket
        // - They are assigned to it
        // - They have edit-tickets permission (support staff)
        return $user->can('view-tickets') && (
            $ticket->user_id === $user->id ||
            $ticket->assigned_to === $user->id ||
            $user->can('edit-tickets')
        );
    }

    /**
     * Determine if user can create tickets
     */
    public function create(User $user): bool
    {
        return $user->can('create-tickets');
    }

    /**
     * Determine if user can update the ticket
     */
    public function update(User $user, Ticket $ticket): bool
    {
        // User can update if they have edit permission and either:
        // - They created the ticket
        // - They are assigned to it
        return $user->can('edit-tickets') && (
            $ticket->user_id === $user->id ||
            $ticket->assigned_to === $user->id
        );
    }

    /**
     * Determine if user can delete the ticket
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->can('delete-tickets') && $ticket->user_id === $user->id;
    }

    /**
     * Determine if user can assign tickets
     */
    public function assign(User $user): bool
    {
        return $user->can('assign-tickets');
    }

    /**
     * Determine if user can add internal comments
     */
    public function addInternalComment(User $user): bool
    {
        return $user->can('edit-tickets');
    }
}

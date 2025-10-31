<?php

namespace Bithoven\Tickets\Services;

use Bithoven\Tickets\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Collection;

class AssignmentService
{
    /**
     * Get available agents for assignment
     */
    public function getAvailableAgents(): Collection
    {
        return User::permission('edit-tickets')->get();
    }

    /**
     * Get agent with least assigned tickets
     */
    public function getLeastBusyAgent(): ?User
    {
        return User::permission('edit-tickets')
            ->withCount(['assignedTickets' => function ($query) {
                $query->whereNotIn('status', ['closed', 'resolved']);
            }])
            ->orderBy('assigned_tickets_count')
            ->first();
    }

    /**
     * Auto-assign ticket to available agent
     */
    public function autoAssign(Ticket $ticket): ?Ticket
    {
        if (!config('tickets.auto_assign')) {
            return null;
        }

        $agent = $this->getLeastBusyAgent();

        if (!$agent) {
            return null;
        }

        $ticket->update(['assigned_to' => $agent->id]);

        return $ticket;
    }

    /**
     * Reassign ticket to another agent
     */
    public function reassign(Ticket $ticket, int $newAgentId): Ticket
    {
        $ticket->update(['assigned_to' => $newAgentId]);
        
        return $ticket;
    }

    /**
     * Unassign ticket
     */
    public function unassign(Ticket $ticket): Ticket
    {
        $ticket->update(['assigned_to' => null]);
        
        return $ticket;
    }

    /**
     * Get agent workload statistics
     */
    public function getAgentWorkload(int $userId): array
    {
        return [
            'total_assigned' => Ticket::assignedTo($userId)->count(),
            'open' => Ticket::assignedTo($userId)->open()->count(),
            'in_progress' => Ticket::assignedTo($userId)->where('status', 'in_progress')->count(),
            'resolved_today' => Ticket::assignedTo($userId)
                ->where('status', 'resolved')
                ->whereDate('resolved_at', today())
                ->count(),
            'avg_response_time' => Ticket::assignedTo($userId)
                ->whereNotNull('first_response_at')
                ->avg(\DB::raw('TIMESTAMPDIFF(HOUR, created_at, first_response_at)')),
        ];
    }
}

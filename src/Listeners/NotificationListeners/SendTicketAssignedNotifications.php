<?php

namespace Bithoven\Tickets\Listeners\NotificationListeners;

use Bithoven\Tickets\Events\TicketAssigned;
use Bithoven\Tickets\Notifications\TicketAssignedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTicketAssignedNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TicketAssigned $event): void
    {
        $ticket = $event->ticket;

        // Notify the assigned user
        if ($ticket->assignedUser) {
            $ticket->assignedUser->notify(new TicketAssignedNotification($ticket));
        }

        // Also notify all other agents (for visibility)
        $userModel = config('auth.providers.users.model');
        $agents = $userModel::permission('edit-tickets')
            ->where('id', '!=', $ticket->assigned_to)
            ->where('id', '!=', $ticket->user_id)
            ->get();

        foreach ($agents as $agent) {
            $agent->notify(new TicketAssignedNotification($ticket));
        }
    }
}

<?php

namespace Bithoven\Tickets\Listeners\NotificationListeners;

use Bithoven\Tickets\Events\TicketCreated;
use Bithoven\Tickets\Notifications\TicketCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTicketCreatedNotifications implements ShouldQueue
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
    public function handle(TicketCreated $event): void
    {
        $ticket = $event->ticket;

        // Get all agents who can manage tickets
        $userModel = config('auth.providers.users.model');
        $agents = $userModel::permission('edit-tickets')->get();

        // Notify all agents about new ticket
        foreach ($agents as $agent) {
            // Don't notify the ticket creator
            if ($agent->id !== $ticket->user_id) {
                $agent->notify(new TicketCreatedNotification($ticket));
            }
        }

        // If ticket is already assigned, notify the assigned user
        if ($ticket->assignedUser && $ticket->assigned_to !== $ticket->user_id) {
            $ticket->assignedUser->notify(new TicketCreatedNotification($ticket));
        }
    }
}

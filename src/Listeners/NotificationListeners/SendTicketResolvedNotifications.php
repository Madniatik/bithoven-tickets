<?php

namespace Bithoven\Tickets\Listeners\NotificationListeners;

use Bithoven\Tickets\Events\TicketResolved;
use Bithoven\Tickets\Notifications\TicketResolvedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTicketResolvedNotifications implements ShouldQueue
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
    public function handle(TicketResolved $event): void
    {
        $ticket = $event->ticket;

        // Notify the ticket creator
        if ($ticket->user) {
            $ticket->user->notify(new TicketResolvedNotification($ticket));
        }

        // If ticket was assigned, notify the assigned user too
        if ($ticket->assignedUser && $ticket->assigned_to !== $ticket->user_id) {
            $ticket->assignedUser->notify(new TicketResolvedNotification($ticket));
        }
    }
}

<?php

namespace Bithoven\Tickets\Listeners\NotificationListeners;

use Bithoven\Tickets\Events\CommentAdded;
use Bithoven\Tickets\Notifications\TicketCommentedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTicketCommentedNotifications implements ShouldQueue
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
    public function handle(CommentAdded $event): void
    {
        $ticket = $event->ticket;
        $comment = $event->comment;
        $commentAuthorId = $comment->user_id;

        // Notify the ticket creator (if not the comment author)
        if ($ticket->user && $ticket->user_id !== $commentAuthorId) {
            $ticket->user->notify(new TicketCommentedNotification($ticket, $comment));
        }

        // Notify the assigned agent (if exists and is not the comment author)
        if ($ticket->assignedUser && $ticket->assigned_to !== $commentAuthorId && $ticket->assigned_to !== $ticket->user_id) {
            $ticket->assignedUser->notify(new TicketCommentedNotification($ticket, $comment));
        }

        // Notify all agents with edit-tickets permission (except comment author and already notified)
        $alreadyNotified = collect([$ticket->user_id, $ticket->assigned_to, $commentAuthorId])->filter()->unique();
        
        $userModel = config('auth.providers.users.model');
        $agents = $userModel::permission('edit-tickets')
            ->whereNotIn('id', $alreadyNotified)
            ->get();

        foreach ($agents as $agent) {
            $agent->notify(new TicketCommentedNotification($ticket, $comment));
        }
    }
}

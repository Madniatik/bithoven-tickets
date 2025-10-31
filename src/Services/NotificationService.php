<?php

namespace Bithoven\Tickets\Services;

use Bithoven\Tickets\Models\Ticket;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Send notification for ticket created
     */
    public function notifyTicketCreated(Ticket $ticket): void
    {
        if (!config('tickets.notifications.enabled')) {
            return;
        }

        if (!config('tickets.notifications.events.created')) {
            return;
        }

        // Notify assigned user (if assigned)
        if ($ticket->assignedUser) {
            $this->sendNotification(
                $ticket->assignedUser,
                'New Ticket Assigned',
                "You have been assigned ticket #{$ticket->ticket_number}: {$ticket->subject}",
                $ticket
            );
        }

        // TODO: Implement email notifications
        // TODO: Implement Slack notifications
    }

    /**
     * Send notification for ticket assigned
     */
    public function notifyTicketAssigned(Ticket $ticket): void
    {
        if (!config('tickets.notifications.enabled')) {
            return;
        }

        if (!config('tickets.notifications.events.assigned')) {
            return;
        }

        if ($ticket->assignedUser) {
            $this->sendNotification(
                $ticket->assignedUser,
                'Ticket Assigned to You',
                "Ticket #{$ticket->ticket_number} has been assigned to you: {$ticket->subject}",
                $ticket
            );
        }
    }

    /**
     * Send notification for ticket updated
     */
    public function notifyTicketUpdated(Ticket $ticket): void
    {
        if (!config('tickets.notifications.enabled')) {
            return;
        }

        if (!config('tickets.notifications.events.updated')) {
            return;
        }

        // Notify ticket creator
        $this->sendNotification(
            $ticket->user,
            'Ticket Updated',
            "Your ticket #{$ticket->ticket_number} has been updated",
            $ticket
        );
    }

    /**
     * Send notification for ticket resolved
     */
    public function notifyTicketResolved(Ticket $ticket): void
    {
        if (!config('tickets.notifications.enabled')) {
            return;
        }

        if (!config('tickets.notifications.events.resolved')) {
            return;
        }

        // Notify ticket creator
        $this->sendNotification(
            $ticket->user,
            'Ticket Resolved',
            "Your ticket #{$ticket->ticket_number} has been resolved: {$ticket->subject}",
            $ticket
        );
    }

    /**
     * Send notification for new comment
     */
    public function notifyNewComment(Ticket $ticket, $comment): void
    {
        if (!config('tickets.notifications.enabled')) {
            return;
        }

        if (!config('tickets.notifications.events.commented')) {
            return;
        }

        // Don't notify internal comments to customer
        if ($comment->is_internal) {
            return;
        }

        // Notify ticket creator (if they didn't write the comment)
        if ($ticket->user_id !== $comment->user_id) {
            $this->sendNotification(
                $ticket->user,
                'New Comment on Ticket',
                "New comment on ticket #{$ticket->ticket_number}",
                $ticket
            );
        }

        // Notify assigned user (if they didn't write the comment)
        if ($ticket->assignedUser && $ticket->assigned_to !== $comment->user_id) {
            $this->sendNotification(
                $ticket->assignedUser,
                'New Comment on Ticket',
                "New comment on ticket #{$ticket->ticket_number}",
                $ticket
            );
        }
    }

    /**
     * Send notification via configured channels
     */
    protected function sendNotification($user, string $title, string $message, Ticket $ticket): void
    {
        $channels = config('tickets.notifications.channels');

        // Database notification
        if ($channels['database'] ?? false) {
            $user->notify(new \Illuminate\Notifications\DatabaseNotification([
                'title' => $title,
                'message' => $message,
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'url' => route('tickets.show', $ticket),
            ]));
        }

        // Email notification
        if ($channels['mail'] ?? false) {
            // TODO: Implement mail notification
        }

        // Slack notification
        if ($channels['slack'] ?? false) {
            // TODO: Implement Slack notification
        }
    }
}

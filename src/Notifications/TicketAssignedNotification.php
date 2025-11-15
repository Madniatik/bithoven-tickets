<?php

namespace Bithoven\Tickets\Notifications;

use Bithoven\Tickets\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Ticket $ticket
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Ticket Assigned: #{$this->ticket->ticket_number}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("A ticket has been assigned to you.")
            ->line("**Ticket #:** {$this->ticket->ticket_number}")
            ->line("**Subject:** {$this->ticket->subject}")
            ->line("**Priority:** {$this->ticket->priority_label}")
            ->line("**Status:** {$this->ticket->status_label}")
            ->action('View Ticket', route('tickets.show', $this->ticket))
            ->line('Please review and respond to this ticket as soon as possible.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => "Ticket Assigned: #{$this->ticket->ticket_number}",
            'message' => $this->ticket->subject,
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'subject' => $this->ticket->subject,
            'priority' => $this->ticket->priority,
            'priority_label' => $this->ticket->priority_label,
            'status' => $this->ticket->status,
            'status_label' => $this->ticket->status_label,
            'url' => route('tickets.show', $this->ticket),
            'icon' => 'user-tick',
            'type' => 'success',
        ];
    }
}

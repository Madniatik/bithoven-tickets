<?php

namespace Bithoven\Tickets\Notifications;

use Bithoven\Tickets\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketResolvedNotification extends Notification implements ShouldQueue
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
            ->subject("Ticket Resolved: #{$this->ticket->ticket_number}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your support ticket has been resolved.")
            ->line("**Ticket #:** {$this->ticket->ticket_number}")
            ->line("**Subject:** {$this->ticket->subject}")
            ->line("**Resolved by:** " . ($this->ticket->assignedUser ? $this->ticket->assignedUser->name : 'Support Team'))
            ->action('View Ticket', route('tickets.show', $this->ticket))
            ->line('If you need further assistance, feel free to reopen this ticket or create a new one.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => "Ticket Resolved: #{$this->ticket->ticket_number}",
            'message' => $this->ticket->subject,
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'subject' => $this->ticket->subject,
            'resolved_by' => $this->ticket->assignedUser?->name ?? 'Support Team',
            'url' => route('tickets.show', $this->ticket),
            'icon' => 'verify',
            'type' => 'success',
        ];
    }
}

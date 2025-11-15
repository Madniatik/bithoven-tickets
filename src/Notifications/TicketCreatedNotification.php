<?php

namespace Bithoven\Tickets\Notifications;

use Bithoven\Tickets\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCreatedNotification extends Notification implements ShouldQueue
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
            ->subject("New Support Ticket: #{$this->ticket->ticket_number}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("A new support ticket has been created.")
            ->line("**Ticket #:** {$this->ticket->ticket_number}")
            ->line("**Subject:** {$this->ticket->subject}")
            ->line("**Priority:** {$this->ticket->priority_label}")
            ->line("**Created by:** {$this->ticket->user->name}")
            ->action('View Ticket', route('tickets.show', $this->ticket))
            ->line('Thank you for using our support system!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => "New Ticket: #{$this->ticket->ticket_number}",
            'message' => $this->ticket->subject,
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'subject' => $this->ticket->subject,
            'priority' => $this->ticket->priority,
            'priority_label' => $this->ticket->priority_label,
            'user_id' => $this->ticket->user_id,
            'user_name' => $this->ticket->user->name,
            'url' => route('tickets.show', $this->ticket),
            'icon' => 'message-text',
            'type' => 'info',
        ];
    }
}

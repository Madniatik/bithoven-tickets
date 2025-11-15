<?php

namespace Bithoven\Tickets\Notifications;

use Bithoven\Tickets\Models\Ticket;
use Bithoven\Tickets\Models\TicketComment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCommentedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Ticket $ticket,
        public TicketComment $comment
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
            ->subject("New Comment on Ticket #{$this->ticket->ticket_number}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("A new comment has been added to ticket #{$this->ticket->ticket_number}.")
            ->line("**Subject:** {$this->ticket->subject}")
            ->line("**Comment by:** {$this->comment->user->name}")
            ->line("**Comment:** " . \Illuminate\Support\Str::limit($this->comment->comment, 150))
            ->action('View Ticket', route('tickets.show', $this->ticket))
            ->line('Reply to stay updated on this ticket.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => "Comment on #{$this->ticket->ticket_number}",
            'message' => \Illuminate\Support\Str::limit($this->comment->comment, 100),
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'subject' => $this->ticket->subject,
            'comment_id' => $this->comment->id,
            'comment_user_id' => $this->comment->user_id,
            'comment_user_name' => $this->comment->user->name,
            'comment_preview' => \Illuminate\Support\Str::limit($this->comment->comment, 100),
            'url' => route('tickets.show', $this->ticket) . '#comment-' . $this->comment->id,
            'icon' => 'message-text-2',
            'type' => 'primary',
        ];
    }
}

<?php

namespace Bithoven\Tickets\Mail;

use Bithoven\Tickets\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PriorityEscalated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Ticket $ticket,
        public string $oldPriority,
        public string $newPriority,
        public ?string $reason = null
    ) {
        // Set locale based on user preference
        $locale = $ticket->user->locale ?? config('app.locale', 'en');
        App::setLocale($locale);
        $this->locale($locale);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('tickets::emails.priority_escalated.subject', ['number' => $this->ticket->ticket_number]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'tickets::emails.priority-escalated',
            with: [
                'ticket' => $this->ticket,
                'oldPriority' => $this->oldPriority,
                'newPriority' => $this->newPriority,
                'changedBy' => $this->changedBy,
                'ticketUrl' => route('tickets.show', $this->ticket->id),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

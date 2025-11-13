<?php

namespace Bithoven\Tickets\Mail;

use Bithoven\Tickets\Models\Ticket;
use Bithoven\Tickets\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class CommentAdded extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Comment $comment
    ) {
        // Set locale based on user preference
        $locale = $comment->ticket->user->locale ?? config('app.locale', 'en');
        App::setLocale($locale);
        $this->locale($locale);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('tickets::emails.comment_added.subject', ['number' => $this->comment->ticket->ticket_number]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'tickets::emails.comment-added',
            with: [
                'ticket' => $this->ticket,
                'comment' => $this->comment,
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

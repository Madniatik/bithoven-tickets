<?php

namespace Bithoven\Tickets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'subject',
        'description',
        'status',
        'priority',
        'user_id',
        'assigned_to',
        'category_id',
        'first_response_at',
        'resolved_at',
        'closed_at',
    ];

    protected $casts = [
        'first_response_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($ticket) {
            if (!$ticket->ticket_number) {
                $ticket->ticket_number = static::generateTicketNumber();
            }
        });
        
        static::updating(function ($ticket) {
            // Auto-set first_response_at when first comment is added
            if (!$ticket->first_response_at && $ticket->comments()->count() > 0) {
                $ticket->first_response_at = now();
            }
            
            // Auto-set resolved_at when status changes to resolved
            if ($ticket->isDirty('status') && $ticket->status === 'resolved' && !$ticket->resolved_at) {
                $ticket->resolved_at = now();
            }
            
            // Auto-set closed_at when status changes to closed
            if ($ticket->isDirty('status') && $ticket->status === 'closed' && !$ticket->closed_at) {
                $ticket->closed_at = now();
            }
        });
    }

    /**
     * Generate unique ticket number
     */
    protected static function generateTicketNumber(): string
    {
        $format = config('tickets.ticket_number_format', 'TKT-{sequence}');
        $padding = config('tickets.ticket_number_padding', 6);
        
        $lastTicket = static::withTrashed()->latest('id')->first();
        $sequence = $lastTicket ? $lastTicket->id + 1 : 1;
        
        return str_replace(
            ['{year}', '{month}', '{day}', '{sequence}'],
            [
                date('Y'),
                date('m'),
                date('d'),
                str_pad($sequence, $padding, '0', STR_PAD_LEFT)
            ],
            $format
        );
    }

    /**
     * User who created the ticket
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * User assigned to the ticket
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Ticket category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class);
    }

    /**
     * Ticket comments
     */
    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }

    /**
     * Ticket attachments
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    /**
     * Scope: Open tickets
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope: Closed tickets
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * Scope: Assigned to user
     */
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Scope: Created by user
     */
    public function scopeCreatedBy($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: By priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope: By status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Urgent tickets
     */
    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }
    /**
     * Scope: Filter tickets for a specific user (created by or assigned to)
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhere('assigned_to', $userId);
        });
    }

    /**
     * Check if ticket is open
     */
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    /**
     * Check if ticket is closed
     */
    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    /**
     * Check if ticket is resolved
     */
    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    /**
     * Check if ticket is assigned
     */
    public function isAssigned(): bool
    {
        return !is_null($this->assigned_to);
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return config("tickets.statuses.{$this->status}.label", ucfirst($this->status));
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute(): string
    {
        return config("tickets.statuses.{$this->status}.color", 'secondary');
    }

    /**
     * Get priority label
     */
    public function getPriorityLabelAttribute(): string
    {
        return config("tickets.priorities.{$this->priority}.label", ucfirst($this->priority));
    }

    /**
     * Get priority color
     */
    public function getPriorityColorAttribute(): string
    {
        return config("tickets.priorities.{$this->priority}.color", 'secondary');
    }

    /**
     * Get response time in hours
     */
    public function getResponseTimeAttribute(): ?float
    {
        if (!$this->first_response_at) {
            return null;
        }
        
        return $this->created_at->diffInHours($this->first_response_at);
    }

    /**
     * Get resolution time in hours
     */
    public function getResolutionTimeAttribute(): ?float
    {
        if (!$this->resolved_at) {
            return null;
        }
        
        return $this->created_at->diffInHours($this->resolved_at);
    }
}

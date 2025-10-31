<?php

namespace Bithoven\Tickets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class TicketComment extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'comment',
        'is_internal',
        'is_solution',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'is_solution' => 'boolean',
    ];

    /**
     * Ticket this comment belongs to
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * User who wrote the comment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Public comments (visible to customer)
     */
    public function scopePublic($query)
    {
        return $query->where('is_internal', false);
    }

    /**
     * Scope: Internal comments (staff only)
     */
    public function scopeInternal($query)
    {
        return $query->where('is_internal', true);
    }

    /**
     * Scope: Solution comments
     */
    public function scopeSolution($query)
    {
        return $query->where('is_solution', true);
    }
}

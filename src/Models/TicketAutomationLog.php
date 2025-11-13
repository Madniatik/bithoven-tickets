<?php

namespace Bithoven\Tickets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketAutomationLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'rule_id',
        'ticket_id',
        'action_type',
        'action_data',
        'result',
        'executed_at',
    ];

    protected $casts = [
        'action_data' => 'array',
        'executed_at' => 'datetime',
    ];

    /**
     * Get the rule that created this log
     */
    public function rule(): BelongsTo
    {
        return $this->belongsTo(TicketAutomationRule::class, 'rule_id');
    }

    /**
     * Get the ticket this log is for
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Check if execution was successful
     */
    public function wasSuccessful(): bool
    {
        return !str_starts_with($this->result ?? '', 'Error:');
    }
}

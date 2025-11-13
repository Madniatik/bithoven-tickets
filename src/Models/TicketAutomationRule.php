<?php

namespace Bithoven\Tickets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketAutomationRule extends Model
{
    protected $fillable = [
        'name',
        'type',
        'is_active',
        'description',
        'conditions',
        'actions',
        'config',
        'execution_count',
        'last_executed_at',
        'execution_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'conditions' => 'array',
        'actions' => 'array',
        'config' => 'array',
        'last_executed_at' => 'datetime',
    ];

    /**
     * Rule types constants
     */
    const TYPE_AUTO_CLOSE = 'auto_close';
    const TYPE_AUTO_ESCALATE = 'auto_escalate';
    const TYPE_AUTO_ASSIGN = 'auto_assign';
    const TYPE_AUTO_RESPONSE = 'auto_response';

    /**
     * Get all logs for this rule
     */
    public function logs(): HasMany
    {
        return $this->hasMany(TicketAutomationLog::class, 'rule_id');
    }

    /**
     * Scope: Active rules only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: By type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Ordered by execution priority
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('execution_order')->orderBy('id');
    }

    /**
     * Evaluate if ticket matches rule conditions
     */
    public function matches(Ticket $ticket): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // Ensure conditions is an array (handle both array cast and JSON string)
        $conditions = is_string($this->conditions) 
            ? json_decode($this->conditions, true) 
            : ($this->conditions ?? []);

        // Check category
        if (isset($conditions['category_id']) && $conditions['category_id'] !== $ticket->category_id) {
            return false;
        }

        // Check priority
        if (isset($conditions['priority']) && $conditions['priority'] !== $ticket->priority) {
            return false;
        }

        // Check status
        if (isset($conditions['status']) && $conditions['status'] !== $ticket->status) {
            return false;
        }

        // Check age (hours since last activity)
        if (isset($conditions['inactive_hours'])) {
            $hoursSinceActivity = $ticket->updated_at->diffInHours(now());
            if ($hoursSinceActivity < $conditions['inactive_hours']) {
                return false;
            }
        }

        // Check if unassigned
        if (isset($conditions['unassigned']) && $conditions['unassigned'] === true) {
            if ($ticket->assigned_to !== null) {
                return false;
            }
        }

        return true;
    }

    /**
     * Execute rule actions on ticket
     */
    public function execute(Ticket $ticket): bool
    {
        try {
            // Ensure actions is an array (handle both array cast and JSON string)
            $actions = is_string($this->actions) 
                ? json_decode($this->actions, true) 
                : ($this->actions ?? []);
            
            $actionData = [];

            foreach ($actions as $action => $value) {
                switch ($action) {
                    case 'close':
                        $oldStatus = $ticket->status;
                        $ticket->update(['status' => 'closed']);
                        $actionData['old_status'] = $oldStatus;
                        $actionData['new_status'] = 'closed';
                        break;

                    case 'escalate_priority':
                        $oldPriority = $ticket->priority;
                        $ticket->update(['priority' => $value]);
                        $actionData['old_priority'] = $oldPriority;
                        $actionData['new_priority'] = $value;
                        break;

                    case 'assign_to':
                        $ticket->update(['assigned_to' => $value]);
                        $actionData['assigned_to'] = $value;
                        break;

                    case 'add_comment':
                        $ticket->comments()->create([
                            'user_id' => 1, // System user
                            'comment' => $value,
                            'is_internal' => true,
                        ]);
                        $actionData['comment_added'] = true;
                        break;

                    case 'change_status':
                        $oldStatus = $ticket->status;
                        $ticket->update(['status' => $value]);
                        $actionData['old_status'] = $oldStatus;
                        $actionData['new_status'] = $value;
                        break;
                }
            }

            // Log execution
            $this->logs()->create([
                'ticket_id' => $ticket->id,
                'action_type' => $this->type,
                'action_data' => $actionData,
                'result' => 'Success',
                'executed_at' => now(),
            ]);

            // Update execution stats
            $this->increment('execution_count');
            $this->update(['last_executed_at' => now()]);

            return true;
        } catch (\Exception $e) {
            // Log error
            $this->logs()->create([
                'ticket_id' => $ticket->id,
                'action_type' => $this->type,
                'action_data' => [],
                'result' => 'Error: ' . $e->getMessage(),
                'executed_at' => now(),
            ]);

            return false;
        }
    }

    /**
     * Get type label for display
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            self::TYPE_AUTO_CLOSE => 'Auto-Close',
            self::TYPE_AUTO_ESCALATE => 'Auto-Escalate',
            self::TYPE_AUTO_ASSIGN => 'Auto-Assign',
            self::TYPE_AUTO_RESPONSE => 'Auto-Response',
            default => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }

    /**
     * Get type badge color
     */
    public function getTypeBadgeAttribute(): string
    {
        return match($this->type) {
            self::TYPE_AUTO_CLOSE => 'danger',
            self::TYPE_AUTO_ESCALATE => 'warning',
            self::TYPE_AUTO_ASSIGN => 'primary',
            self::TYPE_AUTO_RESPONSE => 'info',
            default => 'secondary',
        };
    }
}

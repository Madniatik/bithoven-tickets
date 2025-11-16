<?php

namespace Bithoven\Tickets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class NotificationPreference extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ticket_notification_preferences';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'ticket_created',
        'ticket_assigned',
        'comment_added',
        'status_changed',
        'priority_escalated',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'ticket_created' => 'boolean',
        'ticket_assigned' => 'boolean',
        'comment_added' => 'boolean',
        'status_changed' => 'boolean',
        'priority_escalated' => 'boolean',
    ];

    /**
     * Get the user that owns the preferences.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if user wants to receive a specific notification type.
     *
     * @param string $type Notification type (e.g., 'ticket_created')
     * @return bool
     */
    public function wantsNotification(string $type): bool
    {
        return $this->$type ?? true; // Default to true if preference doesn't exist
    }

    /**
     * Get or create notification preferences for a user.
     *
     * @param int $userId
     * @return self
     */
    public static function forUser(int $userId): self
    {
        return static::firstOrCreate(
            ['user_id' => $userId],
            [
                'ticket_created' => true,
                'ticket_assigned' => true,
                'comment_added' => true,
                'status_changed' => true,
                'priority_escalated' => true,
            ]
        );
    }

    /**
     * Get all enabled notification types for the user.
     *
     * @return array<string>
     */
    public function getEnabledNotifications(): array
    {
        $enabled = [];
        
        foreach (['ticket_created', 'ticket_assigned', 'comment_added', 'status_changed', 'priority_escalated'] as $type) {
            if ($this->$type) {
                $enabled[] = $type;
            }
        }
        
        return $enabled;
    }
}

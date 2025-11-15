<?php

namespace Bithoven\Tickets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CannedResponse extends Model
{
    protected $table = 'ticket_canned_responses';
    
    protected $fillable = [
        'title',
        'shortcut',
        'content',
        'category_id',
        'is_active',
        'is_public',
        'usage_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'usage_count' => 'integer',
    ];

    /**
     * Get the category that owns the canned response.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class);
    }

    /**
     * Scope a query to only include active responses.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope a query to only include public responses.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope a query to filter by shortcut.
     */
    public function scopeByShortcut($query, $shortcut)
    {
        return $query->where('shortcut', $shortcut);
    }

    /**
     * Increment the usage count.
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Get the type badge.
     */
    public function getTypeBadgeAttribute(): string
    {
        return $this->is_public ? 'success' : 'warning';
    }

    /**
     * Get the type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return $this->is_public ? 'Pública' : 'Interna';
    }
}

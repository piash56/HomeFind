<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseNotification extends Model
{
    protected $fillable = [
        'customer_name',
        'minutes_ago',
        'item_id',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get the item that was purchased.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Scope a query to only include active notifications.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Get formatted time text.
     */
    public function getTimeTextAttribute()
    {
        if ($this->minutes_ago < 1) {
            return __('Just now');
        } elseif ($this->minutes_ago == 1) {
            return __('1 min ago');
        } else {
            return $this->minutes_ago . ' ' . __('min ago');
        }
    }
}

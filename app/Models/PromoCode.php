<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    protected $fillable = ['title', 'code_name', 'discount', 'status', 'no_of_times', 'type', 'product_id', 'start_date', 'end_date', 'is_free_delivery', 'minimum_order_amount'];
    public $timestamps = false;

    /**
     * Get the product that this promo code is for.
     */
    public function product()
    {
        return $this->belongsTo(Item::class, 'product_id');
    }

    /**
     * Check if promo code is valid for a specific product
     */
    public function isValidForProduct($productId)
    {
        // If no specific product set, valid for all products
        if (!$this->product_id) {
            return true;
        }

        // Check if matches specific product
        return $this->product_id == $productId;
    }

    /**
     * Check if promo code is within valid date range
     */
    public function isValidDate()
    {
        $today = now()->startOfDay();

        // If no dates set, always valid
        if (!$this->start_date && !$this->end_date) {
            return true;
        }

        // Check start date
        if ($this->start_date && $today->lt($this->start_date)) {
            return false;
        }

        // Check end date
        if ($this->end_date && $today->gt($this->end_date)) {
            return false;
        }

        return true;
    }
}

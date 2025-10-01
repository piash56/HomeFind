<?php

namespace App\Models;

use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{

    protected $fillable = ['category_id', 'subcategory_id', 'childcategory_id', 'brand_id', 'name', 'slug', 'sku', 'tags', 'video', 'sort_details', 'specification_name', 'specification_description', 'is_specification', 'details', 'photo', 'thumbnail', 'discount_price', 'previous_price', 'stock', 'meta_keywords', 'meta_description', 'status', 'is_type', 'tax_id', 'date', 'item_type', 'file', 'link', 'file_type', 'license_name', 'license_key', 'affiliate_link', "seller_id", 'enable_bulk_pricing', 'bulk_pricing_data'];

    public function category()
    {
        return $this->belongsTo('App\Models\Category')->withDefault();
    }

    public function brand()
    {
        return $this->belongsTo('App\Models\Brand')->withDefault();
    }


    // Tax relationship removed in simplified system

    public function attributes()
    {
        return $this->hasMany('App\Models\Attribute');
    }

    public function galleries()
    {
        return $this->hasMany('App\Models\Gallery');
    }

    public function reviews()
    {
        return $this->hasMany('App\Models\Review');
    }

    public static function taxCalculate($item)
    {
        return 0;
    }




    public function getWishlistItemId()
    {
        return Wishlist::whereItemId($this->id)->first()->id;
    }


    public function user()
    {
        return $this->belongsTo('App\Models\User', 'vendor_id')->withDefault();
    }


    public function is_stock()
    {
        $item = $this;
        // license product stock check------------
        if ($item->item_type == 'license') {
            if ($item->license_key) {
                $lisense_key = json_decode($item->license_key, true);
                if (count($lisense_key) > 0) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        // digital product stock check-------------

        if ($item->item_type == 'digital') {
            return true;
        }
        if ($item->item_type == 'affiliate') {
            return true;
        }

        // physical product stock check

        if ($item->item_type == 'normal') {
            if ($item->stock) {
                if ($item->stock != 0) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    /**
     * Get bulk pricing data as array
     *
     * @return array
     */
    public function getBulkPricingData()
    {
        if (!$this->enable_bulk_pricing || !$this->bulk_pricing_data) {
            return [];
        }

        $data = json_decode($this->bulk_pricing_data, true);
        return is_array($data) ? $data : [];
    }

    /**
     * Get price for specific quantity
     *
     * @param int $quantity
     * @return float
     */
    public function getPriceForQuantity($quantity)
    {
        if (!$this->enable_bulk_pricing) {
            return $this->discount_price;
        }

        $bulkPricing = $this->getBulkPricingData();
        if (empty($bulkPricing)) {
            return $this->discount_price;
        }

        // Sort by quantity in descending order
        usort($bulkPricing, function ($a, $b) {
            return $b['quantity'] - $a['quantity'];
        });

        // Find the appropriate bulk pricing tier
        foreach ($bulkPricing as $tier) {
            if ($quantity >= $tier['quantity']) {
                return $tier['price'];
            }
        }

        // Return single price if no bulk tier matches
        return $this->discount_price;
    }
}

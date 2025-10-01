<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'item_id',
        'order_id',
        'customer_name',
        'customer_phone',
        'rating',
        'review_text',
        'review_images',
        'admin_reply',
        'admin_reply_date',
        'is_admin_added',
        'status'
    ];

    public function item()
    {
        return $this->belongsTo('App\Models\Item')->withDefault();
    }

    public function order()
    {
        return $this->belongsTo('App\Models\Order')->withDefault();
    }

    public static function ratings($item_id)
    {
        $stars = Review::whereStatus('approved')->whereItemId($item_id)->avg('rating');
        $ratings = number_format((float)$stars, 1, '.', '') * 20;
        return $ratings;
    }

    public static function getAverageRating($item_id)
    {
        return Review::whereStatus('approved')->whereItemId($item_id)->avg('rating') ?? 0;
    }

    public static function getReviewCount($item_id)
    {
        return Review::whereStatus('approved')->whereItemId($item_id)->count();
    }

    // Helper method to get review images as array
    public function getReviewImages()
    {
        if (!$this->review_images) {
            return [];
        }
        $images = json_decode($this->review_images, true);
        return is_array($images) ? $images : [];
    }

    // Helper method to set review images from array
    public function setReviewImages($images)
    {
        $this->review_images = json_encode($images);
    }

    // Helper method to add a single image
    public function addReviewImage($imagePath)
    {
        $images = $this->getReviewImages();
        if (count($images) < 3) { // Max 3 images
            $images[] = $imagePath;
            $this->setReviewImages($images);
        }
    }

    // Helper method to remove an image by index
    public function removeReviewImage($index)
    {
        $images = $this->getReviewImages();
        if (isset($images[$index])) {
            unset($images[$index]);
            $images = array_values($images); // Re-index array
            $this->setReviewImages($images);
        }
    }

    // Check if admin has replied
    public function hasAdminReply()
    {
        return !empty($this->admin_reply);
    }
}

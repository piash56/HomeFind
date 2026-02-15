<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeOption extends Model
{
  protected $fillable = ['attribute_id', 'name', 'keyword', 'price', 'previous_price', 'stock', 'image', 'color_code', 'gallery_image_id'];

  public function attribute()
  {
    return $this->belongsTo('App\Models\Attribute')->withDefault();
  }

  public function galleryImage()
  {
    return $this->belongsTo('App\Models\Gallery', 'gallery_image_id')->withDefault();
  }

  public $timestamps = false;
}

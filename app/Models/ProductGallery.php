<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductGallery extends Model
{
    protected $fillable = ['product_id', 'image', 'is_featured'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

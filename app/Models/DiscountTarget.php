<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountTarget extends Model
{
    protected $fillable = [
        'discount_id',
        'target_type',
        'target_id',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'level',
        'value_type',
        'value',
        'max_discount_amount',
        'min_purchase_amount',
        'start_at',
        'end_at',
        'usage_limit_total',
        'usage_limit_per_user',
        'is_stackable',
        'priority',
        'status',
        'created_by',
        'target_type',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function discountTargets(): HasMany
    {
        return $this->hasMany(DiscountTarget::class, 'discount_id');
    }
}

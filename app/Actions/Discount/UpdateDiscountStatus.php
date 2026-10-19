<?php

namespace App\Actions\Discount;

use App\Models\Discount;

class UpdateDiscountStatus
{
    public function toggle(int $discountId): Discount
    {
        $discount = Discount::findOrFail($discountId);
        $discount->status = $discount->status === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE';
        $discount->save();

        return $discount;
    }
}

<?php

namespace App\Actions\Discount;

use App\Models\Discount;
use Illuminate\Support\Facades\DB;

class UpdateDiscountStatus
{
    public function toggle(int $discountId): Discount
    {
        return DB::transaction(function () use ($discountId) {
            $discount = Discount::findOrFail($discountId);

            $audit = app(RecordDiscountAudit::class);
            $old = $audit->snapshot($discount);

            $discount->status = $discount->status === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE';
            $discount->save();

            $audit->updated($discount, $old);

            return $discount;
        });
    }
}

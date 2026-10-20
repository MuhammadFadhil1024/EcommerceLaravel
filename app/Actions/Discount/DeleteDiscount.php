<?php

namespace App\Actions\Discount;

use App\Models\Discount;
use Illuminate\Support\Facades\DB;

class DeleteDiscount
{
    public function delete(int $discountId): void
    {
        DB::transaction(function () use ($discountId) {
            $discount = Discount::findOrFail($discountId);

            $audit = app(RecordDiscountAudit::class);
            $old = $audit->snapshot($discount);

            $discount->delete();

            $audit->deleted($discount, $old);
        });
    }
}

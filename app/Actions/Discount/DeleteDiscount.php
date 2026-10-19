<?php

namespace App\Actions\Discount;

use App\Models\Discount;

class DeleteDiscount
{
    public function delete(int $discountId): void
    {
        Discount::findOrFail($discountId)->delete();
    }
}

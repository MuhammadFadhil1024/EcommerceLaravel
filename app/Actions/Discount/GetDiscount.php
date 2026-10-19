<?php

namespace App\Actions\Discount;

use App\DataTransferObject\DetailDataDiscount;
use App\Models\Discount;

class GetDiscount {
    public function GetAllDiscount( int $perPage = 10 ) {
        return Discount::query()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getByIdWithTargets(int $discountId): ?Discount
    {
        return Discount::with('discountTargets')->find($discountId);
    }
}
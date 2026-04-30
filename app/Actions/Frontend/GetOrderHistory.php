<?php

namespace App\Actions\Frontend;

use App\Models\Transaction;

class GetOrderHistory
{
    public function handle(int $userId, int $perPage = 10)
    {
        return Transaction::query()
            ->where('user_id', $userId)
            ->with([
                'address',
                'items.product.galleries',
                'items.product.category',
            ])
            ->latest('id')
            ->paginate($perPage);
    }
}

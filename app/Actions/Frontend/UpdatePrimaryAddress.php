<?php

namespace App\Actions\Frontend;

use App\Models\Address;

class UpdatePrimaryAddress
{
    public function handle(int $userId, int $addressId): void
    {
        Address::query()
            ->where('user_id', $userId)
            ->where('id', '!=', $addressId)
            ->update(['is_primary' => false]);

        Address::query()
            ->where('user_id', $userId)
            ->where('id', $addressId)
            ->update(['is_primary' => true]);
    }
}

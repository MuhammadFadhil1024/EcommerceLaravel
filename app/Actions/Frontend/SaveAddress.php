<?php

namespace App\Actions\Frontend;

use App\Models\Address;
use Illuminate\Support\Facades\DB;

class SaveAddress
{
    public function handle(
        int $userId,
        int $provinceId,
        int $cityId,
        int $districtId,
        string $fullAddress,
        string $phoneNumber,
        string $typeOfAddress,
        ?Address $address = null,
    ): Address
    {
        return DB::transaction(function () use ($userId, $provinceId, $cityId, $districtId, $fullAddress, $phoneNumber, $typeOfAddress, $address) {
            $currentAddress = $this->resolveAddressForUser(userId: $userId, address: $address);

            if ($currentAddress) {
                $currentAddress->update([
                    'full_address' => $fullAddress,
                    'phone_number' => $phoneNumber,
                    'province' => $provinceId,
                    'city' => $cityId,
                    'district' => $districtId,
                    'type_of_address' => $typeOfAddress,
                ]);
            } else {
                $currentAddress = Address::create([
                    'user_id' => $userId,
                    'full_address' => $fullAddress,
                    'phone_number' => $phoneNumber,
                    'postal_code' => 0,
                    'province' => $provinceId,
                    'city' => $cityId,
                    'district' => $districtId,
                    'type_of_address' => $typeOfAddress,
                    'is_primary' => false,
                ]);
            }

            app(UpdatePrimaryAddress::class)->handle(
                userId: $userId,
                addressId: (int) $currentAddress->id,
            );

            return $currentAddress->fresh();
        });
    }

    protected function resolveAddressForUser(int $userId, ?Address $address): ?Address
    {
        if (! $address) {
            return null;
        }

        if ((int) $address->user_id !== $userId) {
            return null;
        }

        return $address;
    }
}

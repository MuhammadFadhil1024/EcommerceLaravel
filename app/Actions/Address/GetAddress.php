<?php 

namespace App\Actions\Address;
use App\Models\Address;
use App\Models\Province;
use App\Models\City;
use App\Actions\Frontend\GetDistrict;

class GetAddress
{
    /**
     * Mengambil detail alamat berdasarkan ID.
     * @param int $addressId
     * @return string $FullAddress
     */
    public function getFullAddress(int $addressId): string
    {
        $address = Address::findOrFail($addressId);
        $Province = Province::where('province_id', $address->province)->first();
        $City = City::where('city_id', $address->city)->first();
        // $District = app( GetDistrict::class )->getDistrict($City->city_id);

        // $DistrictData = collect($District['data'])->firstWhere('id', $address->district);


        return "{$Province->name}, {$City->name}";
    }
}
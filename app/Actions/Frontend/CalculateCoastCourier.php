<?php 

namespace App\Actions\Frontend;

use App\Services\RajaOngkir\RajaOngkirServiceInterface;

Class CalculateCoastCourier
{
    public function __construct(
            protected RajaOngkirServiceInterface $rajaOngkirService        
    ) {}

    public function calculateCoastByDistrict(
        int $originDistrictId,
        int $destinationDistrictId,
        int $weightInGrams,
        string $courier,
    )
    {
        $courierCoast = $this->rajaOngkirService->getCourierCost(
            $originDistrictId,
            $destinationDistrictId,
            $weightInGrams,
            $courier,
        );
        return $courierCoast;
    }  

}

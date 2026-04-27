<?php 

namespace App\Actions\Frontend;

use App\Services\RajaOngkir\RajaOngkirServiceInterface;

Class GetDistrict
{
    public function __construct(
            protected RajaOngkirServiceInterface $rajaOngkirService        
    ) {}

    public function getDistrict(int $cityId)
    {
        $dataDistrict = $this->rajaOngkirService->getDistrict($cityId);
        return $dataDistrict;
    }  
}
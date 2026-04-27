<?php 

namespace App\Actions\Frontend;

use App\Services\RajaOngkir\RajaOngkirServiceInterface;

Class GetCity
{
public function __construct(
        protected RajaOngkirServiceInterface $rajaOngkirService        
) {}

public function getCity(int $provinceId)
{
    $dataCity = $this->rajaOngkirService->getCity($provinceId);
    return $dataCity;
}  
}
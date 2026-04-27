<?php 

namespace App\Actions\Frontend;

use App\Services\RajaOngkir\RajaOngkirServiceInterface;

Class GetProvince
{
public function __construct(
        protected RajaOngkirServiceInterface $rajaOngkirService        
) {}

public function getProvince()
{
    $dataProvince = $this->rajaOngkirService->getProvince();
    return $dataProvince;
}  
}
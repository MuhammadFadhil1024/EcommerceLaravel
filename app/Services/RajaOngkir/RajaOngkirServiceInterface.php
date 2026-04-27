<?php

namespace App\Services\RajaOngkir;

interface RajaOngkirServiceInterface
{
    public function getProvince();

    public function getCity(int $provinceId);

    public function getDistrict(int $cityId);
}
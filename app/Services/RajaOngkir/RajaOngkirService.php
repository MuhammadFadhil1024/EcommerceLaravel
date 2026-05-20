<?php

namespace App\Services\RajaOngkir;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaOngkirService implements RajaOngkirServiceInterface {

    public $rajaOngkirBaseUrl = "https://rajaongkir.komerce.id/api/v1/";

    public function getProvince()
    {
        // 1. Lakukan request ke API Raja Ongkir
        // (Kita bungkus dengan try-catch agar kalau internet mati tidak error 500)
        try {

            return Cache::remember('raja_ongkir_provinces', 2592000, function () {
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'key' => env('RAJA_ONGKIR_KEY'),
            ])->timeout(5)->get($this->rajaOngkirBaseUrl . "destination/province");
            
            // 2. Cek apakah user ditemukan (Status 200 OK)
            if ($response->successful()) {
                return $response->json();
            }
            
            // 3. Jika user tidak ditemukan (Status 404)
            if ($response->status() === 404) {
                throw new Exception("Provinsi tidak ditemukan.");
            }

            // 4. Jika error lain dari server Raja Ongkir
            $response->throw();
            });

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Koneksi ke Raja Ongkir gagal: ' . $e->getMessage());
            throw new Exception("Gagal terhubung ke server Raja Ongkir. Cek koneksi Anda.");
        }

        }

        public function getCity(int $provinceId)
        {

            try {
                
                $cacheKey = 'raja_ongkir_cities_province_' . $provinceId;
                
                return Cache::remember($cacheKey, 2592000, function () use ($provinceId) {
                $response = Http::withHeaders([
                    'accept' => 'application/json',
                    'key' => env('RAJA_ONGKIR_KEY'),
                ])->timeout(5)->get($this->rajaOngkirBaseUrl . "destination/city/" . $provinceId);

                if ($response->successful()) {
                    return $response->json();
                }

                if ($response->status() === 404) {
                    throw new Exception("City not found.");
                }

                $response->throw();

            });

            } catch (\Exception $e) {
                Log::error('Koneksi ke Raja Ongkir gagal: ' . $e->getMessage());
                throw new Exception("Gagal terhubung ke server Raja Ongkir. Cek koneksi Anda.");
            }
        }

        public function getDistrict(int $cityId)
        {

            try {
                
                $cacheKey = 'raja_ongkir_districts_city_' . $cityId;
                
                return Cache::remember($cacheKey, 2592000, function () use ($cityId) {
                $response = Http::withHeaders([
                    'accept' => 'application/json',
                    'key' => env('RAJA_ONGKIR_KEY'),
                ])->timeout(5)->get($this->rajaOngkirBaseUrl . "destination/district/" . $cityId);

                if ($response->successful()) {
                    return $response->json();
                }

                if ($response->status() === 404) {
                    throw new Exception("District not found.");
                }


                $response->throw();

            });

            } catch (\Exception $e) {
                Log::error('Koneksi ke Raja Ongkir gagal: ' . $e->getMessage());
                throw new Exception("Gagal terhubung ke server Raja Ongkir. Cek koneksi Anda.");
            }
        }

        public function getCourierCost(
            int $originDistrictId,
            int $destinationDistrictId,
            int $weightInGrams,
            string $courier,
        ) {
            $cacheKey = 'raja_ongkir_courier_cost_'
                . $originDistrictId . '_'
                . $destinationDistrictId . '_'
                . $weightInGrams . '_'
                . $courier;

            try {
                return Cache::remember($cacheKey, 3600, function () use ($originDistrictId, $destinationDistrictId, $weightInGrams, $courier) {
                    $response = Http::withHeaders([
                        'accept' => 'application/json',
                        'key' => env('RAJA_ONGKIR_KEY'),
                    ])->asForm()->timeout(10)->post($this->rajaOngkirBaseUrl . "calculate/district/domestic-cost", [
                        'origin' => $originDistrictId,
                        'destination' => $destinationDistrictId,
                        'weight' => $weightInGrams,
                        'courier' => $courier,
                        'price' => 'lowest',
                    ]);

                    if ($response->successful()) {
                        return $response->json();
                    }

                    if ($response->status() === 404) {
                        throw new Exception("Cost not found.");
                    }

                    $apiMessage = (string) data_get($response->json(), 'meta.message', 'Gagal menghitung ongkir.');

                    Log::error('Raja Ongkir calculate courier cost failed', [
                        'status' => $response->status(),
                        'response' => $response->body(),
                        'origin' => $originDistrictId,
                        'destination' => $destinationDistrictId,
                        'weight' => $weightInGrams,
                        'courier' => $courier,
                    ]);

                    throw new Exception($apiMessage);
                });
            } catch (ConnectionException $e) {
                Log::error('Koneksi ke Raja Ongkir gagal: ' . $e->getMessage());
                throw new Exception('Gagal terhubung ke server Raja Ongkir. Cek koneksi Anda.');
            } catch (\Exception $e) {
                Log::error('Raja Ongkir calculate courier cost exception: ' . $e->getMessage());
                throw $e;
            }
        }
}

<?php 

namespace App\Actions\External;

use App\Models\City;
use App\Models\Province;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncCityRajaOngkir // Typo fixed
{
    public function execute(): int
    {
        $upsertCount = 0; // 1. Fixed: Deklarasi awal

        // 2. Optimasi: Gunakan pluck() agar hemat RAM
        $provinceIds = Province::pluck('province_id');

        foreach ($provinceIds as $provinceId) {
            
            // 3. Optimasi: Beri pengaman Timeout & Retry
            $response = Http::timeout(10)
                ->retry(2, 200)
                ->withHeaders([
                    'accept' => 'application/json',
                    'key'    => config('services.rajaongkir.key'), // 4. Fixed: Ubah env() jadi config()
                ])
                ->get('https://rajaongkir.komerce.id/api/v1/destination/city/' . $provinceId);

            if ($response->failed()) {
                Log::error("Failed to fetch RajaOngkir cities for province: {$provinceId}", [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                
                throw new Exception("Failed to fetch cities from RajaOngkir API for province_id: {$provinceId}");
            }

            // 5. Optimasi: Langsung ambil isi array 'data'
            $cities = $response->json('data'); 

            if (empty($cities)) {
                continue; // Jika provinsinya kosong, lewati, jangan crash
            }

            DB::transaction(function () use ($cities, $provinceId, &$upsertCount) {
                foreach ($cities as $city) {
                    City::updateOrCreate(
                        ['city_id' => $city['id']], // 6. Fixed: Penutupan array parameter 1 yang benar
                        [
                            'province_id' => $provinceId,
                            'name'        => $city['name'],
                            'zip_code' => $city['zip_code'], // 6. Fixed: Penutupan array parameter 2 yang benar
                        ]
                    );

                    $upsertCount++; // 7. Fixed: Increment counter berjalan
                }
            });
        }

        return $upsertCount; // 8. Fixed: Kembalikan hasil angka ke Command
    }
}
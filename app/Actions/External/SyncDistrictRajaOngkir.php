<?php 

namespace App\Actions\External;

use App\Models\District;
use App\Models\City;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncDistrictRajaOngkir 
{
    // Gunakan Key Cache yang konsisten
    private const PROGRESS_CACHE_KEY = 'rajaongkir:last_synced_city_id';

    public function execute(): int
    {
        $upsertCount = 0;

        // 1. Ambil jejak ID kota terakhir dari Cache (Jika belum ada, return 0)
        $lastCityId = Cache::get(self::PROGRESS_CACHE_KEY, 0);

        // 2. Ambil 40 kota setelah ID terakhir, urutkan pasti secara Ascending
        $cityIds = City::where('city_id', '>', $lastCityId)
            ->orderBy('city_id', 'asc')
            ->take(40)
            ->pluck('city_id');

        if ($cityIds->isEmpty()) {
            Log::info("Semua data district dari seluruh kota sudah up-to-date.");
            return 0;
        }

        foreach ($cityIds as $cityId) {
            $response = Http::timeout(10)
                ->retry(2, 200)
                ->withHeaders([
                    'accept' => 'application/json',
                    'key'    => config('services.rajaongkir.key'),
                ])
                ->get('https://rajaongkir.komerce.id/api/v1/destination/district/' . $cityId);

            if ($response->failed()) {
                Log::error("Failed to fetch RajaOngkir districts for city: {$cityId}", [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                
                throw new Exception("Failed to fetch districts from RajaOngkir API for city_id: {$cityId}");
            }

            $districts = $response->json('data');

            if (empty($districts)) {
                // Catat progres meski datanya kosong agar tidak diulang besok
                Cache::forever(self::PROGRESS_CACHE_KEY, $cityId);
                continue;
            }

            DB::transaction(function () use ($districts, $cityId, &$upsertCount) {
                foreach ($districts as $district) {
                    District::updateOrCreate(
                        ['district_id' => $district['id']],
                        [
                            'city_id'  => $cityId,
                            'name'     => $district['name'],
                            'zip_code' => $district['zip_code'] ?? 0,
                        ]
                    );

                    $upsertCount++;
                }
            });

            // 3. Update bookmark setiap kali 1 kota sukses disinkronisasi
            Cache::forever(self::PROGRESS_CACHE_KEY, $cityId);
        }

        return $upsertCount;
    }
}
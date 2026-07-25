<?php 

namespace App\Actions\External;

use App\Models\Province;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncProvinceRajaOngkir
{
    public function execute()
    {
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'key' => env('RAJA_ONGKIR_KEY'),
        ])->get('https://rajaongkir.komerce.id/api/v1/destination/province');

        if ( $response->failed() ) {
            Log::error('Failed to fetch provinces from RajaOngkir API', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('Failed to fetch provinces from RajaOngkir API');
        }

        $provinces = $response->json();
        $upsertCount = 0;

        DB::transaction(function () use ($provinces, &$upsertCount) {
            foreach ($provinces['data'] as $province) {
                Province::updateOrCreate(
                    ['province_id' => $province['id']],
                    [
                        'name' => $province['name'],
                    ]
                );
                $upsertCount++;
            }
        });
        return $upsertCount;
    }
}
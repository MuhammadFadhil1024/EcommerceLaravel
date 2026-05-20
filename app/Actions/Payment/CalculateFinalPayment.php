<?php 

namespace App\Actions\Payment;

use App\Models\Address;
use App\Models\Product;
use App\Actions\Frontend\GetCart;
use App\Actions\Frontend\CalculateCoastCourier;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

Class CalculateFinalPayment
{
    public function handleCalculateFinalPayment(
        string $selectedCourierService,
        string $selectedCourierCode,
        string $selectedCourierServiceCode,
        int $courierCost,
    )
    {
        $destinationDistrictId = (int) Address::query()
            ->where('user_id', Auth::id())
            ->where('is_primary', true)
            ->value('district');

        if ($destinationDistrictId <= 0) {
            throw new Exception('Alamat utama belum tersedia atau kecamatan belum valid.');
        }

        $Cart = app(GetCart::class)->handleGetCart();
        $originDistrictId = 1391; // Contoh ID kecamatan asal
        $totalWeight = 0;

        foreach ($Cart as $item) {
            $totalWeight += ((int) data_get($item, 'weight', 0)) * ((int) data_get($item, 'quantity', 0));
        }

        if ($totalWeight <= 0) {
            $totalWeight = 1000;
        }

        $normalizedCourierCode = trim(strtolower($selectedCourierCode));

        if ($normalizedCourierCode === '') {
            $normalizedCourierCode = trim(strtolower((string) Str::before($selectedCourierService, ' ')));
        }

        if ($normalizedCourierCode === '') {
            $normalizedCourierCode = 'jne';
        }

        $ensureCourierCost = app(CalculateCoastCourier::class)->calculateCoastByDistrict(
            $originDistrictId,
            $destinationDistrictId,
            $totalWeight,
            $normalizedCourierCode,
        );

        $verifiedCourierCost = $this->resolveVerifiedCourierCost(
            $ensureCourierCost,
            $normalizedCourierCode,
            $selectedCourierService,
            $selectedCourierServiceCode,
        );

        if ($verifiedCourierCost <= 0) {
            throw new Exception('Layanan kurir yang dipilih tidak ditemukan, silakan pilih ulang.');
        }

        $DataPayment = [];
        $grandTotal = 0;
        foreach ($Cart as $item) {
            $grandTotal += $item['quantity'] * $item['product']->price;
        }
        $DataPayment['grand_total'] = $grandTotal;
        $DataPayment['courier_service'] = $selectedCourierService;
        $DataPayment['courier_cost'] = $verifiedCourierCost;
        $DataPayment['courier_cost_input'] = $courierCost;
        $DataPayment['total_payment'] = $grandTotal + $verifiedCourierCost;
        $DataPayment['cart_items'] = $Cart;
        $DataPayment['user_id'] = Auth::id();
        $DataPayment['address_id'] = Address::query()
            ->where('user_id', Auth::id())
            ->where('is_primary', true)
            ->value('id');

        // dd($DataPayment);
        
        return $DataPayment;
    }

    protected function resolveVerifiedCourierCost(
        mixed $ensureCourierCost,
        string $courierCode,
        string $selectedCourierService,
        string $selectedCourierServiceCode,
    ): int {
        $rows = collect((array) data_get($ensureCourierCost, 'data', []));

        if ($rows->isEmpty()) {
            return 0;
        }

        $normalizedCourier = trim(strtolower($courierCode));
        $normalizedServiceCode = trim(strtolower($selectedCourierServiceCode));

        if ($normalizedServiceCode === '') {
            $normalizedServiceCode = trim(strtolower((string) Str::afterLast($selectedCourierService, ' ')));
        }

        $flatPrice = $this->resolveFlatPrice($rows, $normalizedCourier, $normalizedServiceCode);

        if ($flatPrice > 0) {
            return $flatPrice;
        }

        return $this->resolveNestedPrice($rows, $normalizedCourier, $normalizedServiceCode);
    }

    protected function resolveFlatPrice(Collection $rows, string $courierCode, string $serviceCode): int
    {
        $filtered = $rows->filter(function ($item) use ($courierCode) {
            $itemCourierCode = trim(strtolower((string) data_get($item, 'code', '')));
            return $courierCode === '' || $itemCourierCode === '' || $itemCourierCode === $courierCode;
        });

        if ($filtered->isEmpty()) {
            $filtered = $rows;
        }

        if ($serviceCode !== '') {
            $matchByService = $filtered->first(function ($item) use ($serviceCode) {
                return trim(strtolower((string) data_get($item, 'service', ''))) === $serviceCode;
            });

            if ($matchByService) {
                return (int) data_get($matchByService, 'cost', 0);
            }
        }

        return (int) data_get($filtered->first(), 'cost', 0);
    }

    protected function resolveNestedPrice(Collection $rows, string $courierCode, string $serviceCode): int
    {
        $selectedCourier = $rows->first(function ($item) use ($courierCode) {
            $itemCourierCode = trim(strtolower((string) data_get($item, 'code', '')));
            return $courierCode === '' || $itemCourierCode === $courierCode;
        });

        if (! $selectedCourier) {
            $selectedCourier = $rows->first();
        }

        if (! $selectedCourier) {
            return 0;
        }

        $services = collect((array) data_get($selectedCourier, 'costs', []));

        if ($services->isEmpty()) {
            return 0;
        }

        if ($serviceCode !== '') {
            $matchByService = $services->first(function ($item) use ($serviceCode) {
                return trim(strtolower((string) data_get($item, 'service', ''))) === $serviceCode;
            });

            if ($matchByService) {
                return (int) data_get($matchByService, 'cost.0.value', 0);
            }
        }

        return (int) data_get($services->first(), 'cost.0.value', 0);
    }
}

<?php

namespace App\Livewire\Frontend;

use App\Actions\Frontend\CalculateCoastCourier;
use App\Actions\Frontend\GetCart;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Component;

class CourierList extends Component
{ 
    public string $selectedCourier = '';
    public string $courier = 'jne:sicepat';
    public int $cost = 0;

        #[Computed]
        public function courierList()
        {
            if (! Auth::check()) {
                return [];
            }

            $primaryAddress = Address::query()
                ->where('user_id', Auth::id())
                ->where('is_primary', true)
                ->first();

            $destinationDistrictId = (int) data_get($primaryAddress, 'district', 0);

            if ($destinationDistrictId <= 0) {
                return [];
            }

            $cartItems = app(GetCart::class)->handleGetCart();
            $totalWeight = 0;

            foreach ($cartItems as $item) {
                $totalWeight += $item['weight'] * $item['quantity'];
            }

            if ($totalWeight <= 0) {
                $totalWeight = 1000;
            }

            $originDistrictId = 1391; // Contoh ID kecamatan asal

            try {
                return app(CalculateCoastCourier::class)->calculateCoastByDistrict(
                    $originDistrictId,
                    $destinationDistrictId,
                    $totalWeight,
                    $this->courier,
                );
            } catch (\Exception $e) {
                Log::warning('Failed to load courier list', [
                    'message' => $e->getMessage(),
                    'origin' => $originDistrictId,
                    'destination' => $destinationDistrictId,
                    'weight' => $totalWeight,
                ]);

                return [];
            }
        }

    #[Computed]
    public function courierOptions(): array
    {
        $options = [];

        foreach ((array) data_get($this->courierList, 'data', []) as $courier) {
            $courierName = (string) data_get($courier, 'name', data_get($courier, 'code', 'Courier'));
            $courierCode = strtolower((string) data_get($courier, 'code', $courierName));

            // Format response baru (flat): data[].cost, data[].etd, data[].service
            if (data_get($courier, 'cost') !== null) {
                $serviceCode = (string) data_get($courier, 'service', '-');
                $priceValue = (int) data_get($courier, 'cost', 0);
                $etd = trim((string) data_get($courier, 'etd', ''));

                if ($priceValue <= 0) {
                    continue;
                }

                $options[] = [
                    'value' => $courierCode . ':' . strtolower($serviceCode),
                    'label' => trim($courierName . ' ' . $serviceCode . ' - ' . formatRupiah($priceValue) . ($etd !== '' ? ' (ETD ' . $etd . ')' : '')),
                    'cost' => $priceValue,
                    'service' => trim($courierName . ' ' . $serviceCode),
                    'courier' => $courierCode,
                    'service_code' => strtolower($serviceCode),
                ];

                continue;
            }

            // Format lama (nested): data[].costs[].cost[0]
            foreach ((array) data_get($courier, 'costs', []) as $service) {
                $serviceCode = (string) data_get($service, 'service', '-');
                $priceValue = (int) data_get($service, 'cost.0.value', 0);
                $etd = trim((string) data_get($service, 'cost.0.etd', ''));

                if ($priceValue <= 0) {
                    continue;
                }

                $options[] = [
                    'value' => $courierCode . ':' . strtolower($serviceCode),
                    'label' => trim($courierName . ' ' . $serviceCode . ' - ' . formatRupiah($priceValue) . ($etd !== '' ? ' (ETD ' . $etd . ')' : '')),
                    'cost' => $priceValue,
                    'service' => trim($courierName . ' ' . $serviceCode),
                    'courier' => $courierCode,
                    'service_code' => strtolower($serviceCode),
                ];
            }
        }

        return $options;
    }

    public function updatedSelectedCourier(string $value): void
    {
        if ($value === '') {
            $this->dispatch('courier-selected', cost: 0, service: '');
            return;
        }

        $selected = collect($this->courierOptions)->firstWhere('value', $value);

        if (! $selected) {
            $this->dispatch('courier-selected', cost: 0, service: '');
            return;
        }

        $this->dispatch(
            'courier-selected',
            cost: (int) data_get($selected, 'cost', 0),
            service: (string) data_get($selected, 'service', ''),
            courier: (string) data_get($selected, 'courier', ''),
            serviceCode: (string) data_get($selected, 'service_code', ''),
        );
    }

    public function render()
    {
        return view('livewire.frontend.courier-list');
    }

}

<?php

namespace App\Livewire\Frontend;

use App\Actions\Frontend\GetCity;
use App\Actions\Frontend\GetDistrict;
use App\Actions\Frontend\GetProvince;
use App\Actions\Frontend\SaveAddress;
use App\Actions\Frontend\UpdatePrimaryAddress;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class CheckoutAddress extends Component
{
    public array $provinceOptions = [];
    public array $cityOptions = [];
    public array $districtOptions = [];

    public string $provinceSearch = '';
    public string $citySearch = '';
    public string $districtSearch = '';
    public string $fullAddress = '';
    public string $phoneNumber = '';
    public string $typeOfAddress = 'home';

    public ?int $selectedProvinceId = null;
    public ?int $selectedCityId = null;
    public ?int $selectedDistrictId = null;

    public bool $showAddressModal = false;
    public bool $showAddressListModal = false;
    public string $addressModalMode = 'add';
    public bool $returnToAddressListAfterSave = false;

    public function mount(): void
    {
        $response = app(GetProvince::class)->getProvince();

        $this->provinceOptions = collect(data_get($response, 'data', []))
            ->map(fn ($province) => [
                'id' => (int) data_get($province, 'id'),
                'name' => (string) data_get($province, 'name'),
            ])
            ->filter(fn ($province) => $province['id'] > 0 && $province['name'] !== '')
            ->values()
            ->all();
    }

    #[Computed]
    public function addresses()
    {
        return Address::query()
            ->where('user_id', Auth::id())
            ->orderByDesc('is_primary')
            ->latest('id')
            ->get();
    }

    #[Computed]
    public function shippingAddress(): ?Address
    {
        return $this->addresses->first();
    }

    #[Computed]
    public function shippingProvinceName(): string
    {
        if (! $this->shippingAddress) {
            return '-';
        }

        return $this->getProvinceNameById((int) $this->shippingAddress->province);
    }

    public function handleAddressButtonClick(): void
    {
        if ($this->addresses->isNotEmpty()) {
            $this->openAddressListModal();
            return;
        }

        $this->openAddressCreateModal();
    }

    public function openAddressListModal(): void
    {
        $this->showAddressModal = false;
        $this->showAddressListModal = true;
    }

    public function closeAddressListModal(): void
    {
        $this->showAddressListModal = false;
    }

    public function openAddressCreateModal(): void
    {
        $this->returnToAddressListAfterSave = $this->showAddressListModal;
        $this->showAddressListModal = false;
        $this->addressModalMode = 'add';
        $this->resetAddressForm();
        $this->showAddressModal = true;
    }

    public function closeAddressModal(): void
    {
        $this->showAddressModal = false;
        $this->returnToAddressListAfterSave = false;
    }

    public function setPrimaryAddress(int $addressId): void
    {
        $address = Address::query()
            ->where('user_id', Auth::id())
            ->where('id', $addressId)
            ->first();

        if (! $address) {
            return;
        }

        app(UpdatePrimaryAddress::class)->handle(
            userId: (int) Auth::id(),
            addressId: $addressId,
        );

        session()->flash('address_message', 'Primary address berhasil diubah.');
    }

    public function saveAddress(): void
    {
        $validated = $this->validate([
            'fullAddress' => ['required', 'string', 'max:1000'],
            'phoneNumber' => ['required', 'digits_between:8,13'],
            'typeOfAddress' => ['required', 'in:home,work'],
            'provinceSearch' => ['required', 'string', 'max:100'],
            'citySearch' => ['required', 'string', 'max:100'],
            'districtSearch' => ['required', 'string', 'max:100'],
        ]);

        $this->selectedProvinceId = $this->selectedProvinceId ?: $this->findProvinceIdByName($validated['provinceSearch']);

        if (! $this->selectedProvinceId) {
            $this->addError('provinceSearch', 'Provinsi tidak ditemukan, silakan pilih dari daftar.');
            return;
        }

        $this->selectedCityId = $this->selectedCityId ?: $this->findCityIdByName($validated['citySearch']);

        if (! $this->selectedCityId) {
            $this->addError('citySearch', 'Kota tidak ditemukan, silakan pilih dari daftar.');
            return;
        }

        $this->selectedDistrictId = $this->selectedDistrictId ?: $this->findDistrictIdByName($validated['districtSearch']);

        if (! $this->selectedDistrictId) {
            $this->addError('districtSearch', 'Kecamatan tidak ditemukan, silakan pilih dari daftar.');
            return;
        }

        app(SaveAddress::class)->handle(
            userId: (int) Auth::id(),
            provinceId: $this->selectedProvinceId,
            cityId: $this->selectedCityId,
            districtId: $this->selectedDistrictId,
            fullAddress: $validated['fullAddress'],
            phoneNumber: $validated['phoneNumber'],
            typeOfAddress: $validated['typeOfAddress'],
            address: null,
        );

        session()->flash('address_message', 'Alamat berhasil disimpan.');
        $this->showAddressModal = false;

        if ($this->returnToAddressListAfterSave) {
            $this->showAddressListModal = true;
            $this->returnToAddressListAfterSave = false;
        }
    }

    public function selectProvince(int $provinceId): void
    {
        $this->selectedProvinceId = $provinceId;
        $this->provinceSearch = $this->getProvinceNameById($provinceId);

        $this->selectedCityId = null;
        $this->citySearch = '';
        $this->cityOptions = [];

        $this->selectedDistrictId = null;
        $this->districtSearch = '';
        $this->districtOptions = [];

        $this->resetErrorBag(['provinceSearch', 'citySearch', 'districtSearch']);
    }

    public function selectCity(int $cityId, string $cityName = ''): void
    {
        $this->selectedCityId = $cityId;
        $this->citySearch = $cityName !== '' ? $cityName : $this->getCityNameById($cityId);

        $this->selectedDistrictId = null;
        $this->districtSearch = '';
        $this->districtOptions = [];

        $this->resetErrorBag(['citySearch', 'districtSearch']);
    }

    public function selectDistrict(int $districtId, string $districtName = ''): void
    {
        $this->selectedDistrictId = $districtId;
        $this->districtSearch = $districtName !== '' ? $districtName : $this->getDistrictNameById($districtId);
        $this->resetErrorBag('districtSearch');
    }

    public function onCityFieldFocus(): void
    {
        $provinceId = $this->selectedProvinceId;

        if (! $provinceId) {
            $provinceId = $this->findProvinceIdByName($this->provinceSearch);
            if ($provinceId) {
                $this->selectedProvinceId = $provinceId;
            }
        }

        if (! $provinceId) {
            $this->addError('provinceSearch', 'Pilih provinsi terlebih dahulu sebelum memilih kota.');
            $this->cityOptions = [];
            return;
        }

        $this->resetErrorBag('provinceSearch');
        $this->loadCityOptions($provinceId);
    }

    public function onDistrictFieldFocus(): void
    {
        $cityId = $this->selectedCityId;

        if (! $cityId) {
            $cityId = $this->findCityIdByName($this->citySearch);
            if ($cityId) {
                $this->selectedCityId = $cityId;
            }
        }

        if (! $cityId) {
            $this->addError('citySearch', 'Pilih kota terlebih dahulu sebelum memilih kecamatan.');
            $this->districtOptions = [];
            return;
        }

        $this->resetErrorBag('citySearch');
        $this->loadDistrictOptions($cityId);
    }

    public function updatedProvinceSearch(string $value): void
    {
        $provinceId = $this->findProvinceIdByName($value);

        if (! $provinceId || $provinceId !== $this->selectedProvinceId) {
            $this->selectedProvinceId = $provinceId;
            $this->selectedCityId = null;
            $this->citySearch = '';
            $this->cityOptions = [];
            $this->selectedDistrictId = null;
            $this->districtSearch = '';
            $this->districtOptions = [];
        }
    }

    public function updatedCitySearch(string $value): void
    {
        $cityId = $this->findCityIdByName($value);

        if (! $cityId || $cityId !== $this->selectedCityId) {
            $this->selectedCityId = $cityId;
            $this->selectedDistrictId = null;
            $this->districtSearch = '';
            $this->districtOptions = [];
        }
    }

    public function updatedDistrictSearch(string $value): void
    {
        $this->selectedDistrictId = $this->findDistrictIdByName($value);
    }

    public function render()
    {
        return view('livewire.frontend.checkout-address');
    }

    protected function resetAddressForm(): void
    {
        $this->selectedProvinceId = null;
        $this->selectedCityId = null;
        $this->selectedDistrictId = null;
        $this->provinceSearch = '';
        $this->citySearch = '';
        $this->districtSearch = '';
        $this->cityOptions = [];
        $this->districtOptions = [];
        $this->fullAddress = '';
        $this->phoneNumber = '';
        $this->typeOfAddress = 'home';

        $this->resetErrorBag([
            'provinceSearch',
            'citySearch',
            'districtSearch',
            'fullAddress',
            'phoneNumber',
            'typeOfAddress',
        ]);
    }

    protected function loadCityOptions(int $provinceId): void
    {
        $response = app(GetCity::class)->getCity($provinceId);

        $this->cityOptions = collect(data_get($response, 'data', []))
            ->map(function ($city) {
                $name = (string) data_get($city, 'name', data_get($city, 'city_name', ''));

                return [
                    'id' => (int) data_get($city, 'id'),
                    'name' => $name,
                ];
            })
            ->filter(fn ($city) => $city['id'] > 0 && $city['name'] !== '')
            ->values()
            ->all();
    }

    protected function loadDistrictOptions(int $cityId): void
    {
        $response = app(GetDistrict::class)->getDistrict($cityId);

        $this->districtOptions = collect(data_get($response, 'data', []))
            ->map(function ($district) {
                $name = (string) data_get($district, 'name', data_get($district, 'district_name', ''));

                return [
                    'id' => (int) data_get($district, 'id'),
                    'name' => $name,
                ];
            })
            ->filter(fn ($district) => $district['id'] > 0 && $district['name'] !== '')
            ->values()
            ->all();
    }

    protected function getProvinceNameById(int $provinceId): string
    {
        $province = collect($this->provinceOptions)->firstWhere('id', $provinceId);
        return data_get($province, 'name', '-');
    }

    protected function getCityNameById(int $cityId): string
    {
        $city = collect($this->cityOptions)->firstWhere('id', $cityId);
        return data_get($city, 'name', '');
    }

    protected function getDistrictNameById(int $districtId): string
    {
        $district = collect($this->districtOptions)->firstWhere('id', $districtId);
        return data_get($district, 'name', '');
    }

    protected function findProvinceIdByName(string $provinceName): ?int
    {
        $normalized = strtolower(trim($provinceName));

        $province = collect($this->provinceOptions)->first(function ($item) use ($normalized) {
            return strtolower((string) data_get($item, 'name')) === $normalized;
        });

        return $province ? (int) data_get($province, 'id') : null;
    }

    protected function findCityIdByName(string $cityName): ?int
    {
        $normalized = strtolower(trim($cityName));

        $city = collect($this->cityOptions)->first(function ($item) use ($normalized) {
            return strtolower((string) data_get($item, 'name')) === $normalized;
        });

        return $city ? (int) data_get($city, 'id') : null;
    }

    protected function findDistrictIdByName(string $districtName): ?int
    {
        $normalized = strtolower(trim($districtName));

        $district = collect($this->districtOptions)->first(function ($item) use ($normalized) {
            return strtolower((string) data_get($item, 'name')) === $normalized;
        });

        return $district ? (int) data_get($district, 'id') : null;
    }
}

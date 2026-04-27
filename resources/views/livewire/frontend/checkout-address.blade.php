<div class="bg-white p-6 md:p-8 rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] border border-gray-100 mb-8">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl md:text-2xl font-bold text-gray-900">Shipping Address</h3>
        <button type="button" wire:click="handleAddressButtonClick"
            class="text-sm font-medium text-blue-500 hover:text-blue-700 transition-colors">
            {{ $this->shippingAddress ? 'Change' : 'Add Address' }}
        </button>
    </div>

    @if (session('address_message'))
        <div class="mb-4 rounded-xl border border-green-100 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('address_message') }}
        </div>
    @endif

    @if ($this->shippingAddress)
        <div class="flex flex-col md:flex-row md:items-start gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <h4 class="text-lg font-semibold text-gray-900">
                        {{ ucfirst($this->shippingAddress->type_of_address) }} - {{ auth()->user()->name }}
                    </h4>
                    @if ($this->shippingAddress->is_primary)
                        <span
                            class="bg-red-50 text-red-600 border border-red-200 text-xs font-bold px-2 py-1 rounded-md">Primary</span>
                    @endif
                </div>
                <p class="text-gray-600 font-medium mb-1">{{ $this->shippingAddress->phone_number }}</p>
                <p class="text-gray-500 leading-relaxed">{{ $this->shippingAddress->full_address }}</p>
                <p class="text-gray-500 leading-relaxed mt-1">Provinsi: {{ $this->shippingProvinceName }}</p>
            </div>
        </div>
    @else
        <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-5 text-gray-600">
            Belum ada data address. Klik <span class="font-semibold">Add Address</span> untuk menambahkan.
        </div>
    @endif

    <x-frontend.address-modal
        :show="$showAddressModal"
        :mode="$addressModalMode"
        :province-options="$provinceOptions"
        :city-options="$cityOptions"
        :district-options="$districtOptions"
    />

    <x-frontend.address-list-modal
        :show="$showAddressListModal"
        :addresses="$this->addresses"
    />
</div>

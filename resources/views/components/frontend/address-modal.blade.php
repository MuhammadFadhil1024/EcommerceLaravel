@props([
    'show' => false,
    'mode' => 'add',
    'provinceOptions' => [],
    'cityOptions' => [],
    'districtOptions' => [],
])

@if ($show)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-black/40 px-4 py-6">
        <div class="flex min-h-full items-start justify-center md:items-center">
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="mb-4 flex items-center justify-between">
                <h4 class="text-lg font-bold text-gray-900">
                    {{ $mode === 'change' ? 'Change Address' : 'Add Address' }}
                </h4>
                <button type="button" wire:click="closeAddressModal"
                    class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form wire:submit.prevent="saveAddress" class="space-y-4">
                <div>
                    <label for="fullAddress" class="mb-2 block text-sm font-semibold text-gray-700">Full Address</label>
                    <textarea id="fullAddress" wire:model.live="fullAddress" rows="4" placeholder="Masukkan alamat lengkap"
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-gray-900 focus:border-pink-400 focus:outline-none focus:ring-2 focus:ring-pink-200"></textarea>
                    @error('fullAddress')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phoneNumber" class="mb-2 block text-sm font-semibold text-gray-700">Phone Number</label>
                    <input id="phoneNumber" type="number" wire:model.live="phoneNumber" placeholder="Masukkan nomor telepon"
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-gray-900 focus:border-pink-400 focus:outline-none focus:ring-2 focus:ring-pink-200" />
                    @error('phoneNumber')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="typeOfAddress" class="mb-2 block text-sm font-semibold text-gray-700">Type of Address</label>
                    <select id="typeOfAddress" wire:model.live="typeOfAddress"
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-gray-900 focus:border-pink-400 focus:outline-none focus:ring-2 focus:ring-pink-200">
                        <option value="home">Home</option>
                        <option value="work">Work</option>
                    </select>
                    @error('typeOfAddress')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <x-frontend.address-location-fields
                    :province-options="$provinceOptions"
                    :city-options="$cityOptions"
                    :district-options="$districtOptions"
                />

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="closeAddressModal"
                        class="rounded-xl border border-gray-300 px-4 py-2 font-semibold text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                        class="rounded-xl bg-pink-400 px-4 py-2 font-semibold text-gray-900 hover:bg-pink-500">
                        Save Address
                    </button>
                </div>
            </form>
        </div>
        </div>
    </div>
@endif

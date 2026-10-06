@props([
    'search' => '',
    'status' => 'active',
    'min_amount' => null,
    'max_amount' => null,
])

<div class="rounded-2xl border border-gray-200 bg-white p-4 md:p-6 shadow-sm mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 items-center">
        
        <!-- Search Input -->
        <div>
            <label for="discount-search" class="mb-2 block text-sm font-semibold text-gray-700">Cari Kode Diskon</label>
            <input
                id="discount-search"
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Masukkan kode diskon..."
                class="w-full rounded-xl border border-gray-300 px-4 py-2 text-gray-900 focus:border-pink-400 focus:outline-none focus:ring-2 focus:ring-pink-200"
            />
        </div>

        <!-- Status Filter -->
        <div>
            <label for="discount-status" class="mb-2 block text-sm font-semibold text-gray-700">Status</label>
            <select
                id="discount-status"
                wire:model="status"
                class="w-full rounded-xl border border-gray-300 px-4 py-2 text-gray-900 focus:border-pink-400 focus:outline-none focus:ring-2 focus:ring-pink-200"
            >
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua</option>
                <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="expired" {{ $status === 'expired' ? 'selected' : '' }}>Kadaluarsa</option>
            </select>
        </div>

        <!-- Min Amount -->
        <div>
            <label for="discount-min" class="block text-sm font-medium text-gray-700">Min Amount</label>
            <input
                type="number"
                id="discount-min"
                wire:model.debounce.300ms="min_amount"
                placeholder="0"
                class="w-full rounded-xl border border-gray-300 px-4 py-2 text-gray-900 focus:border-pink-400 focus:outline-none focus:ring-2 focus:ring-pink-200"
                min="0"
            />
        </div>

        <!-- Max Amount -->
        <div>
            <label for="discount-max" class="block text-sm font-medium text-gray-700">Max Amount</label>
            <input
                type="number"
                id="discount-max"
                wire:model.debounce.300ms="max_amount"
                class="w-full rounded-xl border border-gray-300 px-4 py-2 text-gray-900 focus:border-pink-400 focus:outline-none focus:ring-2 focus:ring-pink-200"
                min="0"
            />
        </div>
    </div>
</div>
<div class="relative">
    <select
        wire:model.live="selectedCourier"
        class="w-full appearance-none bg-gray-50 border border-gray-300 text-gray-900 font-medium rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent transition-all">
        <option value="">Choose courier service...</option>

        @forelse ($this->courierOptions as $option)
            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
        @empty
            <option value="" disabled>Courier belum tersedia</option>
        @endforelse
    </select>

    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
        </svg>
    </div>
</div>

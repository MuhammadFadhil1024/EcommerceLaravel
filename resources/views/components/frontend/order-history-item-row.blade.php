@props([
    'item',
])

<div class="flex items-center gap-3 rounded-xl border border-gray-100 bg-white p-3">
    <img
        src="{{ $item->product?->galleries->first() ? asset('storage/' . $item->product->galleries->first()->image) : 'data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==' }}"
        alt="{{ $item->product?->name ?? 'Product' }}"
        class="h-14 w-14 rounded-lg object-cover"
    />

    <div class="min-w-0 flex-1">
        <p class="truncate text-sm font-semibold text-gray-900">{{ $item->product?->name ?? 'Product tidak ditemukan' }}</p>
        <p class="text-xs text-gray-500">Qty {{ $item->quantity }} • {{ formatRupiah($item->total_price) }}</p>
    </div>
</div>

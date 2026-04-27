@props([
    'product',
])

<article class="group overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">
    <a href="{{ route('detail', $product->slug) }}" wire:navigate class="block">
        <div class="relative aspect-[4/5] overflow-hidden bg-gray-100">
            <img
                src="{{ $product->galleries->first() ? asset('storage/' . $product->galleries->first()->image) : 'data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==' }}"
                alt="{{ $product->name }}"
                class="h-full w-full object-cover object-center transition duration-300 group-hover:scale-105"
            />

            @if ($product->category)
                <span class="absolute left-3 top-3 rounded-full bg-white/95 px-3 py-1 text-xs font-semibold text-gray-700">
                    {{ $product->category->name }}
                </span>
            @endif
        </div>

        <div class="space-y-1 p-4">
            <h3 class="line-clamp-2 text-base font-semibold text-gray-900">{{ $product->name }}</h3>
            <p class="text-sm font-bold text-pink-500">{{ formatRupiah($product->price) }}</p>
        </div>
    </a>
</article>

@props([
    'categories' => collect(),
    'selectedCategoryId' => null,
])

<div class="rounded-2xl border border-gray-200 bg-white p-4 md:p-6 shadow-sm">
    <div class="grid gap-4 md:grid-cols-[1fr_auto] md:items-center">
        <div>
            <label for="product-search" class="mb-2 block text-sm font-semibold text-gray-700">Search Product</label>
            <input
                id="product-search"
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search by product name"
                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-gray-900 focus:border-pink-400 focus:outline-none focus:ring-2 focus:ring-pink-200"
            />
        </div>

        <div class="md:pt-6">
            <button
                type="button"
                wire:click="clearFilters"
                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 md:w-auto"
            >
                Reset Filter
            </button>
        </div>
    </div>

    <div class="mt-4 flex flex-wrap gap-2">
        <button
            type="button"
            wire:click="filterByCategory"
            class="rounded-full border px-4 py-2 text-sm font-semibold transition-colors {{ $selectedCategoryId === null ? 'border-pink-500 bg-pink-500 text-white' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}"
        >
            All Categories
        </button>

        @foreach ($categories as $category)
            <button
                type="button"
                wire:click="filterByCategory({{ $category->id }})"
                class="rounded-full border px-4 py-2 text-sm font-semibold transition-colors {{ (int) $selectedCategoryId === (int) $category->id ? 'border-pink-500 bg-pink-500 text-white' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}"
            >
                {{ $category->name }}
            </button>
        @endforeach
    </div>
</div>

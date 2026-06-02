@props([
    'categories' => collect(),
    'selectedCategoryId' => null,
])

<div class="h-fit rounded-2xl border border-gray-200 bg-white p-4 shadow-sm md:p-6">
    
    <div class="flex flex-col gap-3 md:flex-row md:items-end md:gap-4">
        
        <div class="flex-1">
            <label for="product-search" class="mb-1.5 block text-sm font-semibold text-gray-700 md:mb-2">Search Product</label>
            <input
                id="product-search"
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search by product name"
                class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-gray-900 focus:border-pink-400 focus:outline-none focus:ring-2 focus:ring-pink-200 md:px-4 md:py-3"
            />
        </div>

        <div>
            <button
                type="button"
                wire:click="clearFilters"
                class="w-full whitespace-nowrap rounded-xl border border-gray-300 px-3 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 md:w-auto md:px-4 md:py-3"
            >
                Reset Filter
            </button>
        </div>
    </div>

    <div class="mt-4 flex flex-wrap gap-2">
        <button
            type="button"
            wire:click="filterByCategory"
            class="rounded-full border px-3 py-1.5 text-sm font-semibold transition-colors md:px-4 md:py-2 {{ $selectedCategoryId === null ? 'border-pink-500 bg-pink-500 text-white' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}"
        >
            All Categories
        </button>

        @foreach ($categories as $category)
            <button
                type="button"
                wire:click="filterByCategory({{ $category->id }})"
                class="rounded-full border px-3 py-1.5 text-sm font-semibold transition-colors md:px-4 md:py-2 {{ (int) $selectedCategoryId === (int) $category->id ? 'border-pink-500 bg-pink-500 text-white' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}"
            >
                {{ $category->name }}
            </button>
        @endforeach
    </div>
</div>

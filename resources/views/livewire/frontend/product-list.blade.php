<div>
    <section class="bg-gray-50 py-8 px-4">
        <div class="container mx-auto max-w-6xl">
            <ul class="flex items-center text-sm md:text-base text-gray-500">
                <li>
                    <a href="{{ route('home') }}" class="hover:text-pink-500 transition-colors" wire:navigate>Home</a>
                </li>
                <li>
                    <span class="mx-3">/</span>
                </li>
                <li>
                    <span aria-label="current-page" class="font-semibold text-gray-900">Products</span>
                </li>
            </ul>
        </div>
    </section>

    <section class="bg-gray-50 pb-12 px-4">
        <div class="container mx-auto max-w-6xl space-y-6">

            <x-frontend.product-list-filters :categories="$this->categories" :selected-category-id="$selectedCategoryId" />

            <div class="flex items-center justify-between text-sm text-gray-600">
                <p>Showing {{ $this->products->count() }} of {{ $this->totalProducts }} products</p>
                <p>Per load: {{ $perPage }} items</p>
            </div>

            @if ($this->products->isEmpty())
                <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center text-gray-500">
                    Product not found. Try changing your keyword or category.
                </div>
            @else
                <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 lg:gap-6">
                    @foreach ($this->products as $product)
                        <x-frontend.product-card :product="$product" wire:key="catalog-product-{{ $product->id }}" />
                    @endforeach
                </div>
            @endif

            @if ($this->hasMoreProducts)
                <div wire:intersect="loadMore" class="flex flex-col items-center gap-2 py-8 text-center">
                    <div wire:loading wire:target="loadMore" class="h-8 w-8 animate-spin rounded-full border-4 border-pink-200 border-t-pink-500"></div>
                    <p wire:loading wire:target="loadMore" class="text-sm font-medium text-gray-500">Loading next 20 products...</p>
                    <p wire:loading.remove wire:target="loadMore" class="text-sm text-gray-400">Scroll down to load more</p>
                </div>
            @endif
        </div>
    </section>
</div>

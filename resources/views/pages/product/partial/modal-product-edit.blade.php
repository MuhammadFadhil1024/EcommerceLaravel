<flux:modal name="product-edit" flyout>
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Edit Product</flux:heading>
            <flux:text class="mt-2 mb-2">Update the details of the product below.</flux:text>

            <form wire:submit="update" class="space-y-4">

                <flux:input type="text" label="Product Name" description="Enter the name of your new product."
                    class="mb-4" required wire:model="product_name" />

                <section class="w-full mb-9">
                    {{-- x-data mendefinisikan state dropdown Alpine.js --}}
                    {{-- @click.outside menutup dropdown jika user klik area luar --}}
                    <flux:field>
                        <div x-data="{ open: false }" @click.outside="open = false" class="relative w-full">

                            <flux:input wire:model.live.debounce.300ms="search" x-on:focus="open = true"
                                x-on:input="open = true" label="Choose Category" icon="magnifying-glass" wire:model="category_name" />

                            <input type="hidden" wire:model="category_id">

                            <div x-show="open && $wire.search.length >= 2" x-transition.opacity.duration.200ms
                                class="absolute z-50 w-full mt-1 bg-white border shadow-sm dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden"
                                style="display: none;">
                                <ul class="max-h-60 overflow-y-auto">
                                    @forelse($this->searchResults as $item)
                                        <li wire:click="selectItem({{ $item->id }}, '{{ $item->name }}')"
                                            x-on:click="open = false"
                                            class="px-3 py-2 text-sm cursor-pointer text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors">
                                            {{ $item->name }}
                                        </li>
                                    @empty
                                        <li class="px-3 py-3 text-sm text-center text-zinc-500 dark:text-zinc-400">
                                            Pencarian "{{ $search }}" tidak ditemukan.
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </flux:field>
                    <flux:error name="category_id" />
                </section>

                <flux:input type="number" label="Price" description="Enter the price of your new product."
                    class="mb-4" required wire:model="price" />
                <flux:input type="number" label="Stock" description="Enter the stock quantity of your new product."
                    class="mb-4" required wire:model="stock" />

                <flux:input type="number" label="Weight" description="Enter the weight in grams of your new product."
                    class="mb-4" required wire:model="weight" />

                <flux:select wire:model="is_available" label="Available">
                    <flux:select.option value="1">Yes</flux:select.option>
                    <flux:select.option value="0">No</flux:select.option>
                </flux:select>

                <flux:textarea label="Product Description" placeholder="Enter a description for your new product."
                    wire:model="description" class="mb-4" />


                <div class="flex justify-end space-x-2 mt-4">
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel</flux:button>
                    </flux:modal.close>

                    <flux:button type="submit" variant="primary">Save Changes</flux:button>
                </div>
            </form>
        </div>
    </div>
</flux:modal>

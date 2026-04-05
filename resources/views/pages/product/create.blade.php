<?php

use Livewire\Component;
use App\Actions\Product\CreateNewProduct;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use App\Actions\Category\GetCategory;

new class extends Component {

    #[Validate('required|string|max:255')]
    public $product_name = '';

    #[Validate('required', message: 'Please provide a category for the product.')]
    #[Validate('exists:categories,id')]
    public $category_id;

    #[Validate('required|numeric')]
    public $price;

    #[Validate('required|integer')]
    public $stock;

    #[Validate('required')]
    public $weight;

    #[Validate('nullable|string')]
    public $description;

    public $search = '';
    public $selectedId = null;

    #[Computed]
    public function searchResults()
    {
        // Jangan lakukan pencarian jika teks kurang dari 2 karakter
        if (strlen($this->search) < 2) {
            return collect();
        }

        return app(GetCategory::class)->searchCategory($this->search);
    }

    // Fungsi saat item diklik
    public function selectItem(int $id, string $name)
    {
        $this->category_id = $id;
        $this->search = $name; // Ubah teks input menjadi nama yang dipilih
        
        // Opsional: Bersihkan error validasi jika sebelumnya kosong
        $this->resetValidation('category_id');
    }

    // Reset ID jika user mengubah teks setelah memilih
    public function updatedSearch()
    {
        $this->category_id = null;
    }

    public function storeProduct(CreateNewProduct $createNewProduct)
    {
        $this->validate();

        try {
            $data = [
                'name' => $this->product_name,
                'category_id' => $this->category_id,
                'price' => $this->price,
                'stock' => $this->stock,
                'weight' => $this->weight,
                'description' => $this->description ?? null,
            ];

            $createNewProduct->create($data);

            session()->flash('success', 'Product created successfully.');

            return redirect()->route('product.index');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $key => $messages) {
                $this->addError($key, $messages[0]);
            }
        } catch (\Exception $e) {
            Log::error('Error create product' . $e->getMessage());
            session()->flash('error', 'Failed to create product.');
        }
    }
}; ?>

<section>
    @include('partials.product-heading')

    <flux:heading class="sr-only">{{ __('Product') }}</flux:heading>

    <x-pages::product.layout :heading="__('Create New Product')">
        <form method="POST" wire:submit="storeProduct" class="mt-6 space-y-6">
            <flux:input type="text" label="Product Name" description="Enter the name of your new product." class="mb-4"
                required wire:model="product_name" />

            <section class="w-full mb-9">
                {{-- x-data mendefinisikan state dropdown Alpine.js --}}
                {{-- @click.outside menutup dropdown jika user klik area luar --}}
                <flux:field>
                    <div x-data="{ open: false }" @click.outside="open = false" class="relative w-full">
    
                        <flux:input wire:model.live.debounce.300ms="search" x-on:focus="open = true"
                            x-on:input="open = true" label="Choose Category" placeholder="Type to search category..." icon="magnifying-glass" />
    
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

            <flux:input type="number" label="Price" description="Enter the price of your new product." class="mb-4"
                required wire:model="price" />
            <flux:input type="number" label="Stock" description="Enter the stock quantity of your new product." class="mb-4"
                required wire:model="stock" />

            <flux:input type="number" label="Weight" description="Enter the weight in grams of your new product." class="mb-4"
                required wire:model="weight" />

            <flux:textarea label="Product Description" placeholder="Enter a description for your new product." wire:model="description" class="mb-4" />

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">
                        {{ __('Save') }}
                    </flux:button>
                </div>
            </div>
        </form>
        </x-pages::category.layout>
</section>

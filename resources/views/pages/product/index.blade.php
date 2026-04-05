<?php

use Livewire\Component;
use App\Actions\Product\GetProduct;
use App\Actions\Product\UpdateProduct;
use App\Actions\Product\DeleteProduct;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

new class extends Component {
    use WithPagination;

    public $is_available;
    public $category_name;
    public $productId;

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

    #[Computed]
    public function products()
    {
        return app(GetProduct::class)->getAllProducts();
    }

    public function showDetails(int $productId): void
    {
        $product = app(GetProduct::class)->getProductById($productId);

        $this->product_name = $product->name;
        $this->category_name = $product->category ? $product->category->name : 'Uncategorized';
        $this->price = $product->price;
        $this->stock = $product->stock;
        $this->weight = $product->weight;
        $this->description = $product->description;
        $this->is_available = $product->is_available;

        Flux::modal('product-details')->show();
    }

    public function edit(int $productId): void
    {
        $this->resetValidation();
        $product = app(GetProduct::class)->getProductById($productId);

        $this->productId = $productId;
        $this->product_name = $product->name;
        $this->category_name = $product->category ? $product->category->name : 'Uncategorized';
        $this->category_id = $product->category_id;
        $this->price = formatRupiah($product->price, false);
        $this->stock = $product->stock;
        $this->weight = $product->weight;
        $this->description = $product->description;
        $this->is_available = $product->is_available;

        Flux::modal('product-edit')->show();
    }

    public function update()
    {
        $this->validate();

        try {
            $product = app(UpdateProduct::class)->update($this->productId, [
                'name' => $this->product_name,
                'category_id' => $this->category_id,
                'price' => $this->price,
                'stock' => $this->stock,
                'weight' => $this->weight,
                'description' => $this->description,
                'is_available' => $this->is_available
            ]);

            Flux::modal('product-edit')->close();
            session()->flash('success', 'Product updated successfully.');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $key => $messages) {
                $this->addError($key, $messages[0]);
            }
        } catch (\Exception $e) {
            Log::error("Error update product" . $e->getMessage());
            session()->flash('error', 'Failed to update product ');
        }
    }

    public function showDeleteConfirmation(int $productId)
    {
        $this->productId = $productId;
        Flux::modal('confirm-delete')->show();
    }

    public function delete(int $productId)
    {
        try {
            app(DeleteProduct::class)->delete($productId);
            Flux::modal('confirm-delete')->close();
            session()->flash('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            Log::error("Error delete product" . $e->getMessage());
            Flux::modal('confirm-delete')->close();
            session()->flash('error', 'Failed to delete product: ');
        }
    }
}; ?>

<section>
    @include('partials.product-heading')

    <flux:heading class="sr-only">{{ __('Product') }}</flux:heading>

    <x-pages::product.layout>
        <flux:table :paginate="$this->products">
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Price</flux:table.column>
                <flux:table.column>Stock</flux:table.column>
                <flux:table.column>Available</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @if ($this->products->count() > 0)
                    @foreach ($this->products as $product)
                        <flux:table.row :key="$product->id">
                            <flux:table.cell class="whitespace-nowarp">{{ $product->name }}</flux:table.cell>
                            <flux:table.cell class="whitespace-nowarp">{{ formatRupiah($product->price) }}</flux:table.cell>
                            <flux:table.cell class="whitespace-nowarp">{{ $product->stock }}</flux:table.cell>
                            <flux:table.cell class="whitespace-nowrap">
                                @if ($product->is_available === 1)
                                    <flux:badge size="sm" color="green">Yes</flux:badge>
                                @else
                                    <flux:badge size="sm" color="red">No</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex justify-end gap-2">
                                    <flux:button variant="ghost" size="sm" icon="eye" inset="top bottom"
                                        wire:click="showDetails({{ $product->id }})">
                                    </flux:button>
                                    <flux:button variant="ghost" size="sm" icon="pencil" inset="top bottom"
                                        wire:click="edit({{ $product->id }})">
                                    </flux:button>
                                    <flux:button variant="ghost" size="sm" icon="photo" inset="top bottom" :href="route('product.image.index', $product->id)" wire:navigate>
                                    </flux:button>
                                    <flux:button variant="ghost" size="sm" icon="trash" inset="top bottom"
                                        wire:click="showDeleteConfirmation({{ $product->id }})">
                                    </flux:button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                @else
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center">
                            {{ __('No products found.') }}
                        </flux:table.cell>
                    </flux:table.row>
                @endif
            </flux:table.rows>
        </flux:table>
        @include('pages.product.partial.modal-product-detail')
        @include('pages.product.partial.modal-product-edit')
        @include('pages.product.partial.modal-confirm-delete')
    </x-pages::product.layout>

</section>

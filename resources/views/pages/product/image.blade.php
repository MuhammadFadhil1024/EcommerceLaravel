<?php

use Livewire\Component;
use App\Actions\Product\GetProduct;
use App\Actions\Product\UpdateProduct;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public $productId;

    #[Validate('image|max:2048')]
    public $fileImage;

    public function mount($productid)
    {
        $this->productId = $productid;
    }

    #[Computed]
    public function productImages()
    {
        $productImage = app(GetProduct::class)->getProductImages($this->productId);
        return $productImage;
    }

    public function deletefileImage()
    {
        $this->fileImage = null;
    }

    public function saveImage()
    {
        $this->validate();

        if ($this->fileImage) {
            try {
                $data = [
                    'image' => $this->fileImage,
                ];
                app(UpdateProduct::class)->addProductImage($this->productId, $data);
                session()->flash('success', 'Image uploaded successfully.');
                $this->reset('fileImage');
            } catch (\Exception $e) {
                Log::error('Error uploading product image: ' . $e->getMessage());
                session()->flash('error', 'Failed to upload image: ' . $e->getMessage());
            }
        }
    }

    public function setAsFeatured(int $imageId)
    {
        try {
            // Panggil Action Class
            app(UpdateProduct::class)->setAsFeatured($this->productId, $imageId);

            session()->flash('success', 'Main image updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error setting featured image: ' . $e->getMessage());
            session()->flash('error', 'Failed to update main image.');
        }
    }

    public function deleteImage(int $imageId)
    {
        try {
            // Panggil Action Class
            app(UpdateProduct::class)->deleteProductImage($imageId);

            session()->flash('success', 'Image deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting product image: ' . $e->getMessage());
            session()->flash('error', 'Failed to delete image.');
        }
    }
    
}; ?>

<section>
    @include('partials.product-image-heading')

    <flux:heading class="sr-only">{{ __('Product Images') }}</flux:heading>

    <x-pages::product.layout :heading="__('Images')">
        <form wire:submit="saveImage" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mt-2 mb-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="md:col-span-1">
                        <flux:text class="mb-4">{{ __('Select Image') }}</flux:text>
                        <flux:input type="file" class="mb-4" wire:model="fileImage" />
                    </div>
                    <div class="md:col-span-2">
                        <flux:text class="mb-4">{{ __('Image Preview:') }}</flux:text>
                        <div class="p-4 rounded-md border border-dashed border-gray-300 dark:border-gray-600 mb-4">
                            <div wire:loading wire:target="fileImage" class="mt-2 flex items-center gap-2">
                                <flux:icon name="arrow-path" class="animate-spin" />
                                <span class="text-sm">Uploading...</span>
                            </div>
                            <div wire:loading.remove wire:target="fileImage">
                                @if ($fileImage)
                                    <img src="{{ $fileImage->temporaryUrl() }}" alt="Thumbnail Preview"
                                        class="mt-2 max-h-48 rounded-md" />
                                @else
                                    <flux:text class="flex justify-center">No file uploaded yet.</flux:text>
                                @endif
                            </div>
                        </div>
                        @if ($fileImage)
                            <div class="flex justify-start mt-2 gap-4">
                                <flux:button type="button" size="sm" variant="danger"
                                    wire:click="deletefileImage">
                                    Remove File</flux:button>
                                <flux:button type="button" size="sm" variant="primary" wire:click="saveImage">
                                    Save Image</flux:button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </form>

        <div class="p-4 rounded-md border border-dashed border-gray-300 dark:border-gray-600">
            @if ($this->productImages->count() > 0)
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($this->productImages as $image)
                        <flux:card size="sm"
                            class="{{ $image->is_featured ? 'ring-2 ring-green-500 dark:ring-green-400 bg-zinc-50 dark:bg-zinc-800' : 'hover:bg-zinc-50 dark:hover:bg-zinc-700' }}">

                            <flux:heading class="flex justify-between items-center mb-2 gap-2">

                                <div>
                                    @if ($image->is_featured)
                                        <flux:badge size="sm" color="green" icon="star">
                                            Main Display
                                        </flux:badge>
                                    @else
                                        <flux:button variant="subtle" size="sm" icon="star" class="text-xs"
                                            wire:click="setAsFeatured({{ $image->id }})" wire:loading.attr="disabled"
                                            wire:target="setAsFeatured({{ $image->id }})">
                                            Set as Main
                                        </flux:button>
                                    @endif
                                </div>

                                <flux:button variant="ghost" size="sm" icon="trash" inset="top bottom"
                                    color="red" wire:click="deleteImage({{ $image->id }})">
                                </flux:button>

                            </flux:heading>

                            <img src="{{ asset('storage/' . $image->image) }}" alt="Product Image"
                                class="w-full h-48 object-cover rounded-md {{ $image->is_featured ? 'opacity-100' : 'opacity-90 hover:opacity-100 transition-opacity' }}" />
                        </flux:card>
                    @endforeach
                </div>
            @else
                <div class="text-sm text-center text-gray-500 dark:text-gray-400">
                    {{ __('No images found for this product.') }}
                </div>
            @endif
        </div>
    </x-pages::product.layout>

</section>

<?php

use Livewire\Component;
use App\Actions\Category\CreateNewCategory;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

new class extends Component {

    use WithFileUploads;

    #[Validate('nullable|image|max:2048')] 
    public $fileThumbnail = '';

    #[Validate('required|string|max:255')] 
    public $category_name = '';

    public function deleteFileThumbnail()
    {
        $this->fileThumbnail = null;
    }

    public function storeCategory(CreateNewCategory $createNewCategory)
    {

        $this->validate();

        try {
            $data = [
                'name' => $validated['category_name'],
                'thumbnail' => $validated['fileThumbnail'] ?? null,

            ];

            if ($this->fileThumbnail) {
                $data['thumbnail'] = $this->fileThumbnail;
            }

            $createNewCategory->create($data);

            session()->flash('success', 'Category created successfully.');

            return redirect()->route('category.index');
        }catch (ValidationException $e) {
            foreach ($e->errors() as $key => $messages) {
                $this->addError($key, $messages[0]);
            }
        } catch (\Exception $e) {
            Log::error("Error create category" . $e->getMessage());
            session()->flash('error', 'Failed to create category: ');
        } 
    }
}; ?>

<section>
    @include('partials.category-heading')

    <flux:heading class="sr-only">{{ __('Category') }}</flux:heading>

    <x-pages::category.layout :heading="__('Create New Category')">
        <form method="POST" wire:submit="storeCategory" class="mt-6 space-y-6">
            <flux:input type="text" label="Category Name" description="Enter the name of your new category."
                class="mb-4" required wire:model="category_name" />

            <flux:input type="file" label="Thumbnail" description="Upload a thumbnail for the category (optional)."
                class="mb-4" wire:model="fileThumbnail" />

            <div class="mt-2">
                <flux:text class="mb-4">{{ __('Thumbnail Preview:') }}</flux:text>
                <div class="p-4 rounded-md border border-dashed border-gray-300 dark:border-gray-600">
                    <div wire:loading wire:target="fileThumbnail" class="mt-2 flex items-center gap-2">
                        <flux:icon name="arrow-path" class="animate-spin" />
                        <span class="text-sm">Uploading...</span>
                    </div>
                    <div wire:loading.remove wire:target="fileThumbnail">
                        @if ($fileThumbnail)
                            <img src="{{ $fileThumbnail->temporaryUrl() }}" alt="Thumbnail Preview"
                                class="mt-2 max-h-48 rounded-md" />
                        @else
                            <flux:text class="flex justify-center">No file uploaded yet.</flux:text>
                        @endif
                    </div>
                </div>
                @if ($fileThumbnail)
                    <div class="flex justify-start mt-2">
                        <flux:button type="button" size="sm" variant="danger" wire:click="deleteFileThumbnail">
                            Remove File</flux:button>
                    </div>
                @endif
            </div>


            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full" data-test="store-wallet-button">
                        {{ __('Save') }}
                    </flux:button>
                </div>

                <x-action-message class="me-3" on="wallet-saved">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>
    </x-pages::category.layout>
</section>

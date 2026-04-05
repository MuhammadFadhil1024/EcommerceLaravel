<?php

use Livewire\Component;
use App\Actions\Category\GetCategory;
use App\Actions\Category\UpdateCategory;
use App\Actions\Category\DeleteCategory;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

new class extends Component {
    use WithPagination;
    use WithFileUploads;

    #[Validate('required|string|max:255')]
    public $name;
    #[Validate('required|boolean|in:0,1')]
    public $is_active;
    #[Validate('nullable|image|max:2048')]
    public $new_thumbnail;

    public $thumbnail;
    public $imageLoader = false;
    public $categoryId;

    #[Computed]
    public function categories()
    {
        return app(GetCategory::class)->getALlCategory();
    }

    public function showDetails(int $categoryId): void
    {
        $category = app(GetCategory::class)->getCategoryById($categoryId);

        $this->name = $category->name;
        $this->is_active = $category->is_active;
        $this->thumbnail = $category->thumbnail;
        $this->imageLoader = true;

        Flux::modal('category-details')->show();
    }

    public function edit(int $categoryId): void
    {
        $this->resetValidation();
        $category = app(GetCategory::class)->getCategoryById($categoryId);

        $this->name = $category->name;
        $this->is_active = $category->is_active;
        $this->thumbnail = $category->thumbnail;
        $this->imageLoader = true;
        $this->categoryId = $categoryId;

        Flux::modal('category-edit')->show();
    }

    public function update()
    {
        $this->validate();

        try {
            $category = app(UpdateCategory::class)->update($this->categoryId, [
                'name' => $this->name,
                'is_active' => $this->is_active,
                'thumbnail' => $this->new_thumbnail ? $this->new_thumbnail : $this->thumbnail,
            ]);

            Flux::modal('category-edit')->close();
            session()->flash('success', 'Category updated successfully.');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $key => $messages) {
                $this->addError($key, $messages[0]);
            }
        } catch (\Exception $e) {
            Log::error("Error update category" . $e->getMessage());
            session()->flash('error', 'Failed to update category ');
        }
    }

    public function delete(int $categoryId)
    {
        try {
            $productExists = app(GetCategory::class)->checkAvailableProduct($categoryId);
            if ($productExists) {
                Flux::modal('confirm-delete')->show();
                return;
            }
            app(DeleteCategory::class)->delete($categoryId);
            session()->flash('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            Log::error("Error delete category" . $e->getMessage());
            session()->flash('error', 'Failed to delete category: ');
        }
    }
}; ?>

<section>
    @include('partials.category-heading')

    <flux:heading class="sr-only">{{ __('Categories') }}</flux:heading>

    <x-pages::category.layout :heading="__('Product Categories')">
        <flux:table :paginate="$this->categories">
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->categories as $category)
                    <flux:table.row :key="$category->id">
                        <flux:table.cell class="whitespace-nowarp">{{ $category->name }}</flux:table.cell>
                        <flux:table.cell class="whitespace-nowrap">
                            @if ($category->is_active === 1)
                                <flux:badge size="sm" color="green">Active</flux:badge>
                            @else
                                <flux:badge size="sm" color="red">Inactive</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex justify-end gap-2">
                                <flux:button variant="ghost" size="sm" icon="eye" inset="top bottom"
                                    wire:click="showDetails({{ $category->id }})">
                                </flux:button>
                                <flux:button variant="ghost" size="sm" icon="pencil" inset="top bottom"
                                    wire:click="edit({{ $category->id }})">
                                </flux:button>
                                <flux:button variant="ghost" size="sm" icon="trash" inset="top bottom"
                                    wire:click="delete({{ $category->id }})">
                                </flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
        @include('pages.category.partial.modal-category-detail')
        @include('pages.category.partial.modal-category-edit')
    </x-pages::category.layout>

</section>

<?php

namespace App\Livewire\Frontend;

use App\Actions\Frontend\GetProductCatalog;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.frontend')]
class ProductList extends Component
{
    public string $search = '';
    public ?int $selectedCategoryId = null;

    public int $perPage = 20;
    public int $page = 1;

    public function updatedSearch(): void
    {
        $this->resetListing();
    }

    public function filterByCategory(?int $categoryId = null): void
    {
        $this->selectedCategoryId = $categoryId;
        $this->resetListing();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->selectedCategoryId = null;
        $this->resetListing();
    }

    public function loadMore(): void
    {
        if (! $this->hasMoreProducts) {
            return;
        }

        $this->page++;
    }

    #[Computed]
    public function categories()
    {
        return app(GetProductCatalog::class)->getCategories();
    }

    #[Computed]
    public function products()
    {
        return app(GetProductCatalog::class)->getProducts(
            search: trim($this->search),
            categoryId: $this->selectedCategoryId,
            limit: $this->currentLimit,
        );
    }

    #[Computed]
    public function totalProducts(): int
    {
        return app(GetProductCatalog::class)->countProducts(
            search: trim($this->search),
            categoryId: $this->selectedCategoryId,
        );
    }

    #[Computed]
    public function currentLimit(): int
    {
        return $this->page * $this->perPage;
    }

    #[Computed]
    public function hasMoreProducts(): bool
    {
        return $this->products->count() < $this->totalProducts;
    }

    public function render()
    {
        return view('livewire.frontend.product-list');
    }

    protected function resetListing(): void
    {
        $this->page = 1;
    }
}

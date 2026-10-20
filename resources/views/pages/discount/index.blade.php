<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Log;
use App\Actions\Discount\SearchDiscounts;
use App\Actions\Discount\DeleteDiscount;
use App\Actions\Discount\UpdateDiscountStatus;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $type = '';
    public $level = '';

    public $deleteDiscountId = null;

    #[Computed]
    public function discounts()
    {
        return app(SearchDiscounts::class)->execute(
            filters: [
                'search' => $this->search,
                'type' => $this->type,
                'level' => $this->level,
            ],
            perPage: 10,
        );
    }

    public function updated($property): void
    {
        if (in_array($property, ['search', 'type', 'level'])) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->type = '';
        $this->level = '';
        $this->resetPage();
    }

    public function toggleStatus(int $discountId): void
    {
        try {
            app(UpdateDiscountStatus::class)->toggle($discountId);
            session()->flash('success', 'Discount status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating discount status: ' . $e->getMessage());
            session()->flash('error', 'Failed to update discount status.');
        }
    }

    public function confirmDelete(int $discountId): void
    {
        $this->deleteDiscountId = $discountId;
        Flux::modal('confirm-delete-discount')->show();
    }

    public function delete(int $discountId): void
    {
        try {
            app(DeleteDiscount::class)->delete($discountId);
            Flux::modal('confirm-delete-discount')->close();
            session()->flash('success', 'Discount deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting discount: ' . $e->getMessage());
            Flux::modal('confirm-delete-discount')->close();
            session()->flash('error', 'Failed to delete discount.');
        }
    }
};

?>

<section>
    @include('partials.discount-heading')

    <flux:heading class="sr-only">{{ __('Discounts') }}</flux:heading>

    <x-pages::discount.layout>

        {{-- ============ BAR FILTER & SEARCH ============ --}}
        <flux:card class="space-y-4 mb-4" size="sm">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

                {{-- Search: nama / kode --}}
                <div class="md:col-span-2">
                    <flux:input wire:model.live.debounce.400ms="search" icon="magnifying-glass"
                        placeholder="Search by name or code..." label="Search" clearable />
                </div>

                {{-- Filter Type --}}
                <flux:select wire:model.live="type" label="Type">
                    <flux:select.option value="">All Types</flux:select.option>
                    <flux:select.option value="FIXED_AMOUNT">Fixed Amount</flux:select.option>
                    <flux:select.option value="PERCENTAGE">Percentage</flux:select.option>
                </flux:select>

                {{-- Filter level --}}
                <flux:select wire:model.live="level" label="Level">
                    <flux:select.option value="">All Level</flux:select.option>
                    <flux:select.option value="PRODUCT">Product</flux:select.option>
                    <flux:select.option value="TRANSACTION">Transaction</flux:select.option>
                </flux:select>

                {{-- Reset --}}
                <div class="flex items-end">
                    <flux:button wire:click="resetFilters" variant="ghost" icon="x-mark" class="w-full">
                        Reset Filter
                    </flux:button>
                </div>
            </div>
        </flux:card>

        {{-- Indikator loading saat query sedangjalan --}}
        <div wire:loading.flex class="items-center gap-2 text-sm text-zinc-400">
            <flux:icon.loading variant="mini" />
            Memuat data...
        </div>

        <flux:table :paginate="$this->discounts()">
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Code</flux:table.column>
                <flux:table.column>Applied On</flux:table.column>
                <flux:table.column>Type</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($this->discounts as $discount)
                    <flux:table.row wire:key="discount-{{ $discount->id }}">
                        <flux:table.cell>{{ $discount->name }}</flux:table.cell>
                        <flux:table.cell>{{ $discount->code }}</flux:table.cell>
                        <flux:table.cell>{{ ucfirst(strtolower($discount->level)) }}</flux:table.cell>
                        <flux:table.cell>
                            @switch($discount->value_type)
                                @case('FIXED_AMOUNT')
                                    Fixed Amount
                                @break

                                @case('PERCENTAGE')
                                    Percentage
                                @break

                                @default
                                    Uknown
                            @endswitch
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($discount->status === 'ACTIVE')
                                <flux:badge size="sm" color="green">Active</flux:badge>
                            @elseif ($discount->status === 'INACTIVE')
                                <flux:badge size="sm" color="red">Inactive</flux:badge>
                            @else
                                <flux:badge size="sm">Draft</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex justify-end">
                                <flux:dropdown position="bottom" align="end">
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-vertical"
                                        inset="top bottom" />

                                    <flux:menu>
                                        <flux:menu.item
                                            :href="route('discount.create', ['discountId' => $discount->id])"
                                            icon="pencil" wire:navigate>
                                            Edit
                                        </flux:menu.item>

                                        <flux:menu.item
                                            wire:click="toggleStatus({{ $discount->id }})"
                                            icon="{{ $discount->status === 'ACTIVE' ? 'pause' : 'play' }}">
                                            {{ $discount->status === 'ACTIVE' ? 'Set Inactive' : 'Set Active' }}
                                        </flux:menu.item>

                                        <flux:menu.separator />

                                        <flux:menu.item
                                            wire:click="confirmDelete({{ $discount->id }})"
                                            icon="trash" variant="danger">
                                            Delete
                                        </flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6">
                                <div class="py-8 text-center text-zinc-500">
                                    Tidak ada data diskon.
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        @include('pages.discount.partial.modal-confirm-delete')
    </x-pages::discount.layout>
    </section>

<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Discount;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $type = '';
    public $level = '';

    #[Computed]
    public function discounts()
    {
        $query = Discount::query()->orderByDesc('created_at');

        // Filter search: carike name atau code
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'LIKE', "%{$this->search}%")
                    ->orWhere('code', 'LIKE', "%{$this->search}%");
            });
        }

        // Filter type: Fixed / Percentage
        if ($this->type !== '') {
            $query->where('value_type', $this->type);
        }

        // Filter level: PRODUCT / TRANSACTION
        if ($this->level !== '') {
            $query->where('level', $this->level);
        }

        return $query->paginate(10)->withQueryString();
    }

    public function updated($property): void
    {
        if (in_array($property, ['search', 'status', 'level'])) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->status = '';
        $this->level = '';
        $this->resetPage();
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
                            <flux:button variant="primary" size="sm">
                                Edit
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5">
                            <div class="py-8 text-center text-zinc-500">
                                Tidak ada data diskon.
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </x-pages::discount.layout>
</section>
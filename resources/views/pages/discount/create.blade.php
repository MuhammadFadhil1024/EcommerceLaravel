<?php

use Livewire\Component;
use Livewire\Attributes\Computed;

use App\Actions\Category\SearchCategory;
use App\Actions\Product\SearchProduct;
use App\Models\Discount;
use App\Actions\Discount\CreateDiscount;
use App\Actions\Discount\UpdateDiscount;
use App\Actions\Discount\GetDiscount;

new class extends Component {
    /*
    |--------------------------------------------------------------------------
    | Discount
    |--------------------------------------------------------------------------
    */

    public string $discount_name = '';

    public string $discount_code = '';

    public string $level = '';

    public string $type = '';

    public $discount_value = null;

    public $discount_cap = null;

    public $minimum_purchase = null;

    public string $start_date = '';

    public string $end_date = '';

    public $discount_limit = null;

    public $discount_limit_per_user = null;

    public $is_stacakble = '';

    public $discount_priority = null;

    public string $status = '';

    public string $target_type = '';

    public ?\App\Models\Discount $discount = null;

    /*
     * ID diskon yang sedang diedit (dari route).
     */
    public ?int $discountId = null;

    /*
    |--------------------------------------------------------------------------
    | Lifecycle
    |--------------------------------------------------------------------------
    */

    public function mount(): void
    {
        // Jika ada ID di route, load discount untuk diedit
        $this->discountId = request()->query('discountId');

        
        if ($this->discountId) {
            $this->discount = app(GetDiscount::class)->getByIdWithTargets((int) $this->discountId);
            if ($this->discount) {
                $this->discount_name = $this->discount->name;
                $this->discount_code = $this->discount->code;
                $this->level = $this->discount->level;
                $this->type = $this->discount->value_type;
                $this->discount_value = $this->discount->value;
                $this->discount_cap = $this->discount->max_discount_amount;
                $this->minimum_purchase = $this->discount->min_purchase_amount;
                $this->start_date = $this->discount->start_at
                    ? \Carbon\Carbon::parse($this->discount->start_at)->format('Y-m-d\TH:i')
                    : '';
                $this->end_date = $this->discount->end_at
                    ? \Carbon\Carbon::parse($this->discount->end_at)->format('Y-m-d\TH:i')
                    : '';
                $this->discount_limit = $this->discount->usage_limit_total;
                $this->discount_limit_per_user = $this->discount->usage_limit_per_user;
                $this->is_stacakble = $this->discount->is_stackable ? '1' : '0';
                $this->discount_priority = $this->discount->priority;
                $this->status = $this->discount->status;
                $this->target_type = $this->discount->target_type;
                $this->selected_target = $this->discount->discountTargets->pluck('target_id')->toArray();
            }
        }

    }

    /*
    |--------------------------------------------------------------------------
    | Discount Target
    |--------------------------------------------------------------------------
    */

    /**
     * ID Product / Category yang dipilih.
     */
    public array $selected_target = [];

    /**
     * Search target.
     */
    public string $target_search = '';

    /*
    |--------------------------------------------------------------------------
    | Lifecycle
    |--------------------------------------------------------------------------
    */

    /**
     * Ketika level berubah.
     *
     * PRODUCT    -> Product
     * CATEGORY   -> Category
     * TRANSACTION -> Tidak memiliki target
     */
    public function updatedLevel(): void
    {
        // Hapus target sebelumnya
        $this->selected_target = [];

        // Reset search
        $this->target_search = '';
    }

    /*
    |--------------------------------------------------------------------------
    | Target List
    |--------------------------------------------------------------------------
    */

    /**
     * Mengambil target berdasarkan level.
     *
     * PRODUCT  -> Product
     * CATEGORY -> Category
     */
    #[Computed]
    public function targets()
    {
        /*
        |--------------------------------------------------------------------------
        | Product
        |--------------------------------------------------------------------------
        */

        if ($this->target_type === 'PRODUCT') {
            return app(SearchProduct::class)->execute($this->target_search, 20);
        }

        /*
        |--------------------------------------------------------------------------
        | Category
        |--------------------------------------------------------------------------
        */

        if ($this->target_type === 'CATEGORY') {
            return app(SearchCategory::class)->execute($this->target_search, 20);
        }

        /*
        |--------------------------------------------------------------------------
        | Transaction
        |--------------------------------------------------------------------------
        */

        return collect();
    }

    /*
    |--------------------------------------------------------------------------
    | Selected Targets
    |--------------------------------------------------------------------------
    */

    /**
     * Mengambil data target yang sudah dipilih.
     *
     * Ini dipisahkan dari targets() karena targets()
     * hanya mengambil maksimal 20 hasil pencarian.
     */
    #[Computed]
    public function selectedTargets()
    {
        if (empty($this->selected_target)) {
            return collect();
        }

        /*
        |--------------------------------------------------------------------------
        | Selected Product
        |--------------------------------------------------------------------------
        */

        if ($this->target_type === 'PRODUCT') {
            return app(SearchProduct::class)->findByIds($this->selected_target);
        }

        /*
        |--------------------------------------------------------------------------
        | Selected Category
        |--------------------------------------------------------------------------
        */

        if ($this->target_type === 'CATEGORY') {
            return app(SearchCategory::class)->findByIds($this->selected_target);
        }

        return collect();
    }

    /*
    |--------------------------------------------------------------------------
    | Select / Unselect Target
    |--------------------------------------------------------------------------
    */

    public function toggleTarget(int $id): void
    {
        /*
        |--------------------------------------------------------------------------
        | Jika sudah dipilih -> hapus
        |--------------------------------------------------------------------------
        */

        if (in_array($id, $this->selected_target)) {
            $this->selected_target = array_values(array_diff($this->selected_target, [$id]));

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Jika belum dipilih -> tambahkan
        |--------------------------------------------------------------------------
        */

        $this->selected_target[] = $id;
    }

    /*
    |--------------------------------------------------------------------------
    | Remove Selected Target
    |--------------------------------------------------------------------------
    */

    public function removeTarget(int $id): void
    {
        $this->selected_target = array_values(array_diff($this->selected_target, [$id]));
    }

    /*
    |--------------------------------------------------------------------------
    | Save
    |--------------------------------------------------------------------------
    */

    public function save(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated = $this->validate([
            'discount_name' => ['required', 'string', 'max:255'],

            'discount_code' => ['nullable', 'string', 'max:255'],

            'level' => ['required', 'in:PRODUCT,CATEGORY,TRANSACTION'],

            'type' => ['required', 'in:PERCENTAGE,FIXED_AMOUNT'],

            'discount_value' => ['required', 'numeric', 'min:0'],

            'discount_cap' => ['nullable', 'numeric', 'min:0'],

            'minimum_purchase' => ['nullable', 'numeric', 'min:0'],

            'start_date' => ['required', 'date_format:Y-m-d\TH:i'],

            'end_date' => ['required', 'date_format:Y-m-d\TH:i', 'after:start_date'],

            'discount_limit' => ['nullable', 'integer', 'min:0'],

            'discount_limit_per_user' => ['nullable', 'integer', 'min:0'],

            'is_stacakble' => ['required', 'in:0,1'],

            'discount_priority' => ['nullable', 'integer', 'min:0'],

            'status' => ['required', 'in:DRAFT,ACTIVE,INACTIVE'],
        ]);

        $validated['start_date'] = \Carbon\Carbon::parse($this->start_date)->format('Y-m-d H:i:s');
        $validated['end_date'] = \Carbon\Carbon::parse($this->end_date)->format('Y-m-d H:i:s');

        /*
        |--------------------------------------------------------------------------
        | Validate Target
        |--------------------------------------------------------------------------
        */

        if (in_array($this->level, ['PRODUCT', 'CATEGORY']) && empty($this->selected_target)) {
            $this->addError('selected_target', 'Please select at least one target.');

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Generate Discount Code
        |--------------------------------------------------------------------------
        */

        $discountCode = $this->discount_code;

        if (empty($discountCode)) {
            $discountCode = 'DISCOUNT_' . strtoupper(str()->random(8));
        } elseif (!str_starts_with($discountCode, 'DISCOUNT_')) {
            $discountCode = 'DISCOUNT_' . $discountCode;
        }

        /*
        |--------------------------------------------------------------------------
        | Save Discount
        |--------------------------------------------------------------------------
        |
        | Sesuaikan bagian ini dengan model/service
        | yang kamu gunakan.
        |
        */

        try {
            if ($this->discountId) {
                // Update existing discount
                app(UpdateDiscount::class)->updateDiscount(
                    $this->discount,
                    $validated,
                    $discountCode,
                    $this->selected_target,
                    $this->target_type
                );
            } else {
                // Create new discount
                app(CreateDiscount::class)->createDiscount($validated, $discountCode, $this->selected_target, $this->target_type);
            }

            session()->flash('success', $this->discountId ? 'Discount updated successfully.' : 'Discount created successfully.');
        } catch (\Exception $e) {
            Log::error('Error saving discount: ' . $e->getMessage());
            session()->flash('error', $this->discountId ? 'Failed to update discount.' : 'Failed to create discount.');
        }
    }
};

?>

<section>

    @include('partials.discount-heading')

    <flux:heading class="sr-only">
        {{ __('Discounts') }}
    </flux:heading>

    <x-pages::discount.layout>

        <form method="POST" wire:submit="save">
            <div class="mb-4">

                {{-- ========================================================= --}}
                {{-- DISCOUNT DETAILS --}}
                {{-- ========================================================= --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-8">

                    {{-- LEFT --}}
                    <div class="col-span-1">

                        <flux:text class="text-base lg:text-xl font-semibold">
                            Discount Details
                        </flux:text>

                    </div>


                    {{-- RIGHT --}}
                    <div class="col-span-2">

                        <flux:card size="sm">


                            {{-- Discount Name --}}
                            <div class="mb-4">
                                <flux:input type="text" label="Discount Name"
                                    placeholder="Enter the name of your new discount." required
                                    wire:model="discount_name" />
                            </div>

                            {{-- Discount Code --}}

                            <div class="mb-4">

                                <flux:input type="text" label="Discount Code"
                                    placeholder="Enter the code of your new discount." class="mb-1"
                                    wire:model="discount_code" />

                                <flux:description class="text-xs text-gray-500">
                                    Note: The discount code will be automatically
                                    prefixed with 'DISCOUNT_' if not provided.
                                </flux:description>

                            </div>


                            {{-- Applied On --}}
                            <div class="mb-4">
                                <flux:select wire:model.live="level" label="Applied On" required>

                                    <option value="">
                                        Select Level
                                    </option>

                                    <option value="PRODUCT">
                                        Product
                                    </option>

                                    <option value="TRANSACTION">
                                        Transaction
                                    </option>

                                </flux:select>
                            </div>

                            {{-- Target Type --}}

                            <div class="mb-4">
                                <flux:select wire:model.live="target_type" label="Target For" required>

                                    <option value="">
                                        Select Target Type
                                    </option>

                                    <option value="PRODUCT">
                                        Product
                                    </option>

                                    <option value="CATEGORY">
                                        Category
                                    </option>

                                </flux:select>
                            </div>

                            {{-- Discount Type --}}

                            <div class="mb-4">
                                <flux:select wire:model.live="type" label="Discount Type" required>

                                    <option value="">
                                        Select Type
                                    </option>

                                    <option value="PERCENTAGE">
                                        Percentage
                                    </option>

                                    <option value="FIXED_AMOUNT">
                                        Fixed Amount
                                    </option>

                                </flux:select>
                            </div>


                            {{-- Value --}}

                            <div class="mb-4">

                                <flux:input type="number" label="Value"
                                    placeholder="Enter the value of your new discount." required
                                    wire:model="discount_value" />

                            </div>


                            {{-- Discount Cap --}}

                            <div class="mb-4">

                                <flux:input type="number" label="Discount Cap"
                                    placeholder="Enter the cap of your new discount." wire:model="discount_cap" />

                            </div>


                            {{-- Minimum Purchase --}}

                            <div class="mb-4">

                                <flux:input type="number" label="Minimum Purchase"
                                    placeholder="Enter the minimum purchase amount for your new discount."
                                    wire:model="minimum_purchase" />

                            </div>


                            {{-- Date Range --}}

                            <div class="mb-4">

                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                    <flux:input type="datetime-local" label="Start Date" wire:model="start_date" />

                                    <flux:input type="datetime-local" label="End Date" wire:model="end_date" />
                                </div>

                            </div>


                            {{-- Limit --}}

                            <div class="mb-4">
                                <flux:input type="number" label="Limit"
                                    placeholder="Enter the total limit for your new discount."
                                    wire:model="discount_limit" />
                            </div>

                            {{-- Limit Per User --}}

                            <div class="mb-4">
                                <flux:input type="number" label="Limit per User"
                                    placeholder="Enter the limit per user for your new discount."
                                    wire:model="discount_limit_per_user" />
                            </div>

                            {{-- Stackable --}}

                            <div class="mb-4">
                                <flux:select wire:model="is_stacakble" label="Can be stacked" required>

                                    <option value="">
                                        Select Option
                                    </option>

                                    <option value="1">
                                        Yes
                                    </option>

                                    <option value="0">
                                        No
                                    </option>

                                </flux:select>
                            </div>

                            {{-- Priority --}}

                            <div class="mb-4">

                                <flux:input type="number" label="Priority"
                                    placeholder="Enter the priority for your new discount."
                                    wire:model="discount_priority" />

                            </div>


                            {{-- Status --}}

                            <div class="mb-4">

                                <flux:select wire:model="status" label="Status" required>

                                    <option value="">
                                        Select Option
                                    </option>

                                    <option value="DRAFT">
                                        Draft
                                    </option>

                                    <option value="ACTIVE">
                                        Active
                                    </option>

                                    <option value="INACTIVE">
                                        Inactive
                                    </option>

                                </flux:select>

                            </div>

                        </flux:card>

                    </div>

                </div>


                {{-- ========================================================= --}}
                {{-- DISCOUNT TARGET --}}
                {{-- ========================================================= --}}

                @if (in_array($this->level, ['PRODUCT', 'CATEGORY']) && in_array($target_type, ['PRODUCT', 'CATEGORY']))

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-8">


                        {{-- LEFT --}}

                        <div class="col-span-1">

                            <flux:text class="text-base lg:text-xl font-semibold text-start">
                                Discount Target
                            </flux:text>

                            <flux:text class="text-sm text-zinc-500 mt-1 text-start">
                                Select the products or categories that this discount
                                applies to.
                            </flux:text>

                        </div>


                        {{-- RIGHT --}}

                        <div class="col-span-2">

                            <flux:card size="sm">

                                <div class="space-y-3">


                                    {{-- Label --}}

                                    <flux:label class="pb-2">
                                        {{ $target_type === 'PRODUCT' ? 'Products' : 'Categories' }}
                                    </flux:label>


                                    {{-- Dropdown --}}

                                    <flux:dropdown class="w-full mt-2">


                                        {{-- Button --}}

                                        <flux:button variant="outline" class="w-full justify-between mb-2">

                                            <span class="truncate">

                                                @if (count($selected_target))
                                                    {{ count($selected_target) }}
                                                    {{ $target_type === 'PRODUCT' ? 'products' : 'categories' }}
                                                    selected
                                                @else
                                                    Select
                                                    {{ $target_type === 'PRODUCT' ? 'products' : 'categories' }}
                                                @endif

                                            </span>


                                            <flux:icon.chevron-down variant="micro" />

                                        </flux:button>


                                        {{-- Menu --}}

                                        <flux:menu class="w-[400px]">


                                            {{-- Search --}}

                                            <div class="p-2">

                                                <flux:input wire:model.live.debounce.300ms="target_search"
                                                    placeholder="{{ $target_type === 'PRODUCT' ? 'Search product...' : 'Search category...' }}"
                                                    icon="magnifying-glass" />

                                            </div>


                                            <flux:separator />


                                            {{-- Target List --}}

                                            <div class="max-h-64 overflow-y-auto">

                                                @forelse ($this->targets as $target)
                                                    <flux:menu.checkbox wire:click="toggleTarget({{ $target->id }})"
                                                        :checked="in_array(
                                                                                                                                                                                                                                                                                                                                                                            $target->id,
                                                                                                                                                                                                                                                                                                                                                                            $selected_target
                                                                                                                                                                                                                                                                                                                                                                        )">

                                                        {{ $target->name }}

                                                    </flux:menu.checkbox>

                                                @empty

                                                    <div class="px-3 py-4 text-center text-sm text-zinc-500">

                                                        No
                                                        {{ $target_type === 'PRODUCT' ? 'products' : 'categories' }}
                                                        found.

                                                    </div>
                                                @endforelse

                                            </div>

                                        </flux:menu>

                                    </flux:dropdown>


                                    {{-- Validation Error --}}

                                    @error('selected_target')
                                        <flux:text class="text-sm text-red-600">
                                            {{ $message }}
                                        </flux:text>
                                    @enderror


                                    {{-- Selected Targets --}}

                                    @if ($this->selectedTargets->isNotEmpty())

                                        <div class="flex flex-wrap gap-2">

                                            @foreach ($this->selectedTargets as $target)
                                                <flux:badge>

                                                    {{ $target->name }}

                                                    <button type="button"
                                                        wire:click="removeTarget({{ $target->id }})"
                                                        class="ml-1">

                                                        <flux:icon.x-mark variant="micro" />

                                                    </button>

                                                </flux:badge>
                                            @endforeach

                                        </div>

                                    @endif

                                </div>

                            </flux:card>

                        </div>

                    </div>

                @endif


                {{-- ========================================================= --}}
                {{-- SAVE BUTTON --}}
                {{-- ========================================================= --}}

                <div class="flex justify-end">

                    <flux:button variant="primary" type="submit">
                        Save Discount
                    </flux:button>

                </div>

            </div>
        </form>

    </x-pages::discount.layout>

</section>

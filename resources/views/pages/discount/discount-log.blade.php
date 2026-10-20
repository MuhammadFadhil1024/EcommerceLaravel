<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Actions\Discount\SearchDiscountLogs;
use App\Actions\Discount\GetDiscountLog;
use App\Actions\Discount\CompareDiscountChanges;
use App\Actions\Discount\ResolveDiscountAuditTargets;

new class extends Component {
    use WithPagination;

    public $search = '';

    public $action = '';

    public $selectedLogId = null;

    #[Computed]
    public function logs()
    {
        return app(SearchDiscountLogs::class)->execute(
            filters: [
                'search' => $this->search,
                'action' => $this->action,
            ],
            perPage: 10,
        );
    }

    #[Computed]
    public function selectedLog()
    {
        if (! $this->selectedLogId) {
            return null;
        }

        return app(GetDiscountLog::class)->getByIdWithRelations($this->selectedLogId);
    }

    #[Computed]
    public function selectedLogPayload()
    {
        $log = $this->selectedLog;

        if (! $log) {
            return ['old_data' => null, 'new_data' => null];
        }

        $resolver = app(ResolveDiscountAuditTargets::class);

        return [
            'old_data' => $resolver->resolve($log->old_data),
            'new_data' => $resolver->resolve($log->new_data),
        ];
    }

    #[Computed]
    public function selectedLogChanges()
    {
        $payload = $this->selectedLogPayload;

        return app(CompareDiscountChanges::class)->compare(
            $payload['old_data'],
            $payload['new_data'],
        );
    }

    public function updated($property): void
    {
        if (in_array($property, ['search', 'action'])) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->action = '';
        $this->resetPage();
    }

    public function viewChanges(int $logId): void
    {
        $this->selectedLogId = $logId;

        Flux::modal('discount-log-changes')->show();
    }
};

?>

<section>
    @include('partials.discount-heading')

    <flux:heading class="sr-only">{{ __('Discount Changes') }}</flux:heading>

    <x-pages::discount.layout>

        {{-- ============ FILTER & SEARCH ============ --}}
        <flux:card class="space-y-4 mb-4" size="sm">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <div class="md:col-span-2">
                    <flux:input wire:model.live.debounce.400ms="search" icon="magnifying-glass"
                        placeholder="Search by discount or admin..." label="Search" clearable />
                </div>

                <flux:select wire:model.live="action" label="Action">
                    <flux:select.option value="">All Actions</flux:select.option>
                    <flux:select.option value="CREATE">Create</flux:select.option>
                    <flux:select.option value="UPDATE">Update</flux:select.option>
                    <flux:select.option value="DELETE">Delete</flux:select.option>
                </flux:select>

                <div class="flex items-end">
                    <flux:button wire:click="resetFilters" variant="ghost" icon="x-mark" class="w-full">
                        Reset Filter
                    </flux:button>
                </div>
            </div>
        </flux:card>

        <div wire:loading.flex class="items-center gap-2 text-sm text-zinc-400">
            <flux:icon.loading variant="mini" />
            Loading...
        </div>

        <flux:table :paginate="$this->logs">
            <flux:table.columns>
                <flux:table.column>Date</flux:table.column>
                <flux:table.column>Discount</flux:table.column>
                <flux:table.column>Admin</flux:table.column>
                <flux:table.column>Action</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($this->logs as $log)
                    <flux:table.row wire:key="discount-log-{{ $log->id }}">
                        <flux:table.cell>
                            {{ $log->created_at?->format('d M Y, H:i') ?? '-' }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="font-medium">
                                {{ $log->discount?->name ?? 'Discount #' . $log->discount_id }}
                            </div>
                            @if ($log->discount?->code)
                                <flux:text class="text-xs text-zinc-500">{{ $log->discount->code }}</flux:text>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $log->admin?->name ?? 'Unknown' }}
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($log->action === 'CREATE')
                                <flux:badge size="sm" color="green">Create</flux:badge>
                            @elseif ($log->action === 'UPDATE')
                                <flux:badge size="sm" color="amber">Update</flux:badge>
                            @elseif ($log->action === 'DELETE')
                                <flux:badge size="sm" color="red">Delete</flux:badge>
                            @else
                                <flux:badge size="sm">{{ $log->action }}</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            @if (! empty($log->old_data) || ! empty($log->new_data))
                                <flux:button variant="ghost" size="sm" icon="eye"
                                    wire:click="viewChanges({{ $log->id }})">
                                    View
                                </flux:button>
                            @else
                                <span class="text-sm text-zinc-400">-</span>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5">
                            <div class="py-8 text-center text-zinc-500">
                                No discount changes recorded yet.
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        @include('pages.discount.partial.modal-log-changes', [
            'log' => $this->selectedLog,
            'oldData' => $this->selectedLogPayload['old_data'],
            'newData' => $this->selectedLogPayload['new_data'],
            'changes' => $this->selectedLogChanges,
        ])

    </x-pages::discount.layout>
</section>

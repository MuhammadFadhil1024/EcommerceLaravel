<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Actions\Transaction\GetTransaction;
use App\Actions\Address\GetAddress;

new class extends Component {
    use WithPagination;

    public $reference_id;
    public $total_payment;
    public $courier;
    public $courier_cost;
    public $status;
    public $items;
    public $xendit_session_id;
    public $payment_date;
    public $payment_url;

    #[Computed]
    public function transactions()
    {
        return app(GetTransaction::class)->getAllTransactions();
    }

    public function showDetails(int $transactionId): void
    {
        $transaction = app(GetTransaction::class)->getTransactionById($transactionId);

        $fullAdddress = app(GetAddress::class)->getFullAddress($transaction->address_id);

        $this->reference_id = $transaction->reference_id;
        $this->total_payment = formatRupiah($transaction->total_payment);
        $this->courier = $transaction->courier;
        $this->courier_cost = formatRupiah($transaction->courier_cost);
        $this->items = $transaction->items;
        $this->status = $transaction->status;
        $this->xendit_session_id = $transaction->session_id;
        $this->payment_date = $transaction->payment_date;
        $this->payment_url = $transaction->payment_url;
        $this->full_address = $fullAdddress;

        Flux::modal('transaction-details')->show();
    }
};

?>

<section>
    @include('partials.transaction-heading')

    <flux:heading class="sr-only">{{ __('Transactions') }}</flux:heading>

    <x-pages::transaction.layout :heading="__('Product Transactions')">
        <flux:table :paginate="$this->transactions">
            <flux:table.columns>
                <flux:table.column>Reference Id</flux:table.column>
                <flux:table.column>Total Payment</flux:table.column>
                <flux:table.column>Courier Cost</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($this->transactions as $transaction)
                    <flux:table.row :key="$transaction->id">
                        <flux:table.cell class="whitespace-nowrap">{{ $transaction->reference_id }}</flux:table.cell>
                        <flux:table.cell class="whitespace-nowrap">
                            {{ formatRupiah($transaction->total_payment) }}
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-nowrap">
                            {{ formatRupiah($transaction->courier_cost) }}
                        </flux:table.cell>
                        <flux:table.cell class="whitespace-nowrap">
                            @if ($transaction->status === 'paid')
                                <flux:badge size="sm" color="green">Paid</flux:badge>
                            @elseif ($transaction->status === 'pending')
                                <flux:badge size="sm" color="yellow">Pending</flux:badge>
                            @elseif ($transaction->status === 'cancelled')
                                <flux:badge size="sm" color="red">Cancelled</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button variant="ghost" size="sm" icon="eye" inset="top bottom"
                                wire:click="showDetails({{ $transaction->id }})">
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
        @include('pages.transaction.partial.modal-transaction-detail')
    </x-pages::transaction.layout>

</section>

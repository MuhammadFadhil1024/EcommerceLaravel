<flux:modal class="md:w-1/2" name="transaction-details" flyout>
    <div class="space-y-8">
        <div>
            <flux:heading size="lg">Transaction Details</flux:heading>
            <flux:text class="mt-2 mb-2">Here are the details of the selected transaction.</flux:text>
            <flux:table>
                <flux:table.rows>
                    <flux:table.row>
                        <flux:table.cell  variant="strong">Reference Id</flux:table.cell>
                        <flux:table.cell>{{ $this->reference_id ?? '' }}</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell class="align-top" variant="strong">Items</flux:table.cell>
                        @if ($this->items)
                            @php
                              $totalItemPrice = 0;   
                            @endphp
                            <flux:table.cell>
                                @foreach ($this->items as $index => $item)
                                    <div class="flex flex-row mb-2">
                                        <div class="basis-2/3">
                                            {{ $item->product->name }} x {{ $item->quantity }}
                                        </div>
                                        <div class="basis-1/3 text-end">
                                            {{ formatRupiah($item->total_price) }}
                                        </div>
                                    </div>
                                    @php
                                        $totalItemPrice += $item->total_price;
                                    @endphp
                                @endforeach
                                <hr class="my-1.5 border-gray-200" />
                                <div class="flex flex-row mb-2">
                                    <div class="basis-2/3 font-bold">
                                        Total Items Price
                                    </div>
                                    <div class="basis-1/3 text-end">
                                        {{ formatRupiah($totalItemPrice) }}
                                    </div>
                                </div>
                            </flux:table.cell>
                        @endif
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell  variant="strong">Courier</flux:table.cell>
                        <flux:table.cell>{{ $this->courier ?? '' }}</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell  variant="strong">Courier Cost</flux:table.cell>
                        <flux:table.cell>{{ $this->courier_cost ?? '' }}</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell  variant="strong">Address</flux:table.cell>
                        <flux:table.cell>{{ $this->full_address ?? '' }}</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell  variant="strong">Total Payment</flux:table.cell>
                        <flux:table.cell>{{ $this->total_payment ?? '' }}</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell  variant="strong">Status</flux:table.cell>
                        <flux:table.cell>
                            @if ($this->status === 'paid')
                                <flux:badge size="sm" color="green">Paid</flux:badge>
                            @elseif ($this->status === 'pending')
                                <flux:badge size="sm" color="yellow">Pending</flux:badge>
                            @elseif ($this->status === 'cancelled')
                                <flux:badge size="sm" color="red">Cancelled</flux:badge>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell  variant="strong">Payment Date</flux:table.cell>
                        <flux:table.cell>{{ $this->payment_date ?? '' }}</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell  variant="strong">Xendit Session Id</flux:table.cell>
                        <flux:table.cell>{{ $this->xendit_session_id ?? '' }}</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell  variant="strong">Payment Url</flux:table.cell>
                        <flux:table.cell>{{ $this->payment_url ?? '' }}</flux:table.cell>
                    </flux:table.row>
                </flux:table.rows>
            </flux:table>
        </div>
    </div>
</flux:modal>

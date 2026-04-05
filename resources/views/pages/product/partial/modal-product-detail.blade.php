<flux:modal name="product-details" flyout>
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Product Details</flux:heading>
            <flux:text class="mt-2 mb-2">Here are the details of the selected product.</flux:text>
            <flux:table>
                <flux:table.rows>
                    <flux:table.row>
                        <flux:table.cell>Name</flux:table.cell>
                        <flux:table.cell>{{ $this->name ?? '' }}</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell>Category</flux:table.cell>
                        <flux:table.cell>{{ $this->category ?? '' }}</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell>Price</flux:table.cell>
                        <flux:table.cell>{{ formatRupiah($this->price ?? 0) }}</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell>Stock</flux:table.cell>
                        <flux:table.cell>{{ $this->stock ?? 0 }}</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell>Weight</flux:table.cell>
                        <flux:table.cell>{{ $this->weight ?? 0 }} grams</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell>Description</flux:table.cell>
                        <flux:table.cell>{{ $this->description ?? '-' }}</flux:table.cell>
                    </flux:table.row>
                    <flux:table.row>
                        <flux:table.cell>Available</flux:table.cell>
                        <flux:table.cell>
                            @if ($this->is_available === 1)
                                <flux:badge size="sm" color="green">Yes</flux:badge>
                            @else
                                <flux:badge size="sm" color="red">No</flux:badge>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                </flux:table.rows>
            </flux:table>
        </div>
    </div>
</flux:modal>

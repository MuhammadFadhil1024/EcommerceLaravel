<flux:modal name="category-details" flyout>
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Category Details</flux:heading>
                    <flux:text class="mt-2 mb-2">Here are the details of the selected category.</flux:text>
                    <flux:table>
                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell>Name</flux:table.cell>
                                <flux:table.cell>{{ $this->name ?? '' }}</flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell>Status</flux:table.cell>
                                <flux:table.cell>
                                    @if ($this->is_active === 1)
                                        <flux:badge size="sm" color="green">Active</flux:badge>
                                    @else
                                        <flux:badge size="sm" color="red">Inactive</flux:badge>
                                    @endif
                                </flux:table.cell>
                            </flux:table.row>
                            <flux:table.row>
                                <flux:table.cell>Thumbnail</flux:table.cell>
                                <flux:table.cell>
                                    @if ($this->imageLoader && $this->thumbnail)
                                        <img src="{{ asset('storage/' . $this->thumbnail) }}" alt="{{ $this->name }} Thumbnail"
                                            class="mt-2 max-h-48 rounded-md" />
                                    @else
                                        <flux:text>No thumbnail available.</flux:text>
                                    @endif
                                </flux:table.cell>
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            </div>
        </flux:modal>
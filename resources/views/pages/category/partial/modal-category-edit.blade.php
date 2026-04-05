<flux:modal name="category-edit" flyout>
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Edit Category</flux:heading>
            <flux:text class="mt-2 mb-2">Update the details of the category below.</flux:text>
            
            <form wire:submit="update" class="space-y-4">
                
                <flux:input wire:model="name" label="Category Name" placeholder="Enter name..." />

                <flux:select wire:model="is_active" label="Status">
                    <flux:select.option value="1">Active</flux:select.option>
                    <flux:select.option value="0">Inactive</flux:select.option>
                </flux:select>

                <div>
                    <flux:input type="file" wire:model="new_thumbnail" label="Change Thumbnail (Optional)" />
                    
                    @if ($thumbnail && !$new_thumbnail)
                        <div class="mt-2">
                            <span class="text-xs text-gray-500">Current Thumbnail:</span>
                            <img src="{{ asset('storage/' . $thumbnail) }}" class="mt-1 h-16 w-16 object-cover rounded" />
                        </div>
                    @endif
                    
                    @if ($new_thumbnail)
                        <div class="mt-2">
                            <span class="text-xs text-gray-500">New Thumbnail Preview:</span>
                            <img src="{{ $new_thumbnail->temporaryUrl() }}" class="mt-1 h-16 w-16 object-cover rounded" />
                        </div>
                    @endif
                </div>

                <div class="flex justify-end space-x-2 mt-4">
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel</flux:button>
                    </flux:modal.close>
                    
                    <flux:button type="submit" variant="primary">Save Changes</flux:button>
                </div>
            </form>
        </div>
    </div>
</flux:modal>
<flux:modal name="confirm-delete" class="md:w-96">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Delete Category</flux:heading>
                    <flux:text class="mt-2 mb-2">Are you sure you want to delete this category? This action will delete related products.</flux:text>
                    <div class="flex justify-end space-x-2 mt-4">
                        <flux:modal.close>
                            <flux:button variant="ghost">Cancel</flux:button>
                        </flux:modal.close>
                        
                        <flux:button wire:click="delete({{ $categoryId }})" variant="danger">Delete</flux:button>
                    </div>
                </div>
            </div>
        </flux:modal>
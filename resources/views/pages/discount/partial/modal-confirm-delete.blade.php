<flux:modal name="confirm-delete-discount" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Delete Discount</flux:heading>
            <flux:text class="mt-2 mb-2">
                Are you sure you want to delete this discount? It will be moved to trash and can be restored.
            </flux:text>
            <div class="flex justify-end space-x-2 mt-4">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>

                <flux:button wire:click="delete({{ $deleteDiscountId }})" variant="danger">
                    Delete
                </flux:button>
            </div>
        </div>
    </div>
</flux:modal>

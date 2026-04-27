@props([
    'show' => false,
    'addresses' => collect(),
])

@if ($show)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-black/40 px-4 py-6">
        <div class="flex min-h-full items-start justify-center md:items-center">
            <div class="w-full max-w-2xl rounded-2xl bg-white p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
                <div class="mb-5 flex items-center justify-between">
                    <h4 class="text-lg font-bold text-gray-900">Address List</h4>
                    <button type="button" wire:click="closeAddressListModal" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <div class="mb-4 flex justify-end">
                    <button type="button" wire:click="openAddressCreateModal"
                        class="rounded-xl bg-pink-400 px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-pink-500">
                        Add Address
                    </button>
                </div>

                <div class="space-y-3">
                    @forelse ($addresses as $address)
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                            <div class="mb-2 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2">
                                    <p class="font-semibold text-gray-900">{{ ucfirst($address->type_of_address) }}</p>
                                    @if ($address->is_primary)
                                        <span class="rounded-md border border-red-200 bg-red-50 px-2 py-0.5 text-xs font-bold text-red-600">Primary</span>
                                    @endif
                                </div>

                                @if (! $address->is_primary)
                                    <button type="button" wire:click="setPrimaryAddress({{ $address->id }})"
                                        class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100">
                                        Set Primary
                                    </button>
                                @endif
                            </div>

                            <p class="text-sm font-medium text-gray-700">{{ $address->phone_number }}</p>
                            <p class="mt-1 text-sm text-gray-600">{{ $address->full_address }}</p>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-gray-300 p-4 text-sm text-gray-500">
                            Belum ada address.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endif

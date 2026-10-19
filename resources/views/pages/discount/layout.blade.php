<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <flux:navlist aria-label="{{ __('Discount') }}">
            <flux:navlist.item :href="route('discount.index')" wire:navigate>{{ __('All Discounts') }}</flux:navlist.item>
            <flux:navlist.item :href="route('discount.create')" wire:navigate>{{ __('Create New Discount') }}
            </flux:navlist.item>
        </flux:navlist>
        
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 self-stretch max-md:pt-6">
        @if (session('success'))
            <x-alert type="success">
                {{ session('success') }}
            </x-alert>
        @elseIf (session('error'))
            <x-alert type="error">
                {{ session('error') }}
            </x-alert>
        @endif

        <div class="mt-5 w-full">
            {{ $slot }}
        </div>
    </div>
</div>

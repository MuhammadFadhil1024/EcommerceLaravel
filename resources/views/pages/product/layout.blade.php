<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <flux:navlist aria-label="{{ __('Product') }}">
            <flux:navlist.item :href="route('product.index')" wire:navigate>{{ __('All Products') }}</flux:navlist.item>
            <flux:navlist.item :href="route('product.create')" wire:navigate>{{ __('Create New Product') }}
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
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>

        <div class="mt-5 w-full">
            {{ $slot }}
        </div>
    </div>
</div>

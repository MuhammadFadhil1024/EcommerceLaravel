<div>
    <section class="bg-gray-50 py-8 px-4">
        <div class="container mx-auto max-w-6xl">
            <ul class="flex items-center text-sm md:text-base text-gray-500">
                <li>
                    <a href="{{ route('home') }}" class="hover:text-pink-500 transition-colors" wire:navigate>Home</a>
                </li>
                <li>
                    <span class="mx-3">/</span>
                </li>
                <li>
                    <span aria-label="current-page" class="font-semibold text-gray-900">Order History</span>
                </li>
            </ul>
        </div>
    </section>

    <section class="bg-gray-50 pb-12 px-4">
        <div class="container mx-auto max-w-6xl">
            <div class="mb-6 rounded-3xl bg-white p-6 shadow-sm md:p-8">
                <h1 class="text-2xl font-bold text-gray-900 md:text-3xl">Your Order History</h1>
                <p class="mt-2 text-sm text-gray-500 md:text-base">Track all your previous orders in one place.</p>
            </div>

            @if ($orders->isEmpty())
                <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center">
                    <h2 class="text-lg font-semibold text-gray-800">No orders yet</h2>
                    <p class="mt-2 text-sm text-gray-500">You have not placed any order. Start shopping to see your history.</p>
                    <a href="{{ route('products') }}" wire:navigate
                        class="mt-4 inline-flex rounded-xl bg-pink-400 px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-pink-500">
                        Explore Products
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($orders as $order)
                        <x-frontend.order-history-card :order="$order" wire:key="order-history-{{ $order->id }}" />
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </section>
</div>

<!-- START: HEADER -->
<div x-data="{ mobileOpen: false, profileOpen: false }">
    <header class="{{ \Route::currentRouteName() === 'home' ? 'absolute' : '' }} w-full z-50 px-4">
        <div class="container mx-auto py-5">
            <div class="flex items-center justify-between gap-4">
                <div class="w-56 flex items-center">
                    <a href="{{ route('home') }}" wire:navigate>
                        <img src="{{ url('frontend/images/content/logo.png') }}"
                            alt="Luxspace | Fulfill your house with beautiful furniture" />
                    </a>
                </div>

                <nav class="hidden md:block">
                    <ul class="flex items-center gap-6">
                        <li>
                            <a href="{{ route('products') }}" class="text-black hover:underline" wire:navigate>Catalog</a>
                        </li>
                        <li>
                            <a href="#" class="text-black hover:underline">Delivery</a>
                        </li>
                        <li>
                            <a href="#" class="text-black hover:underline">Rewards</a>
                        </li>

                        @auth
                            <li>
                                <div class="relative" @click.outside="profileOpen = false">
                                    <button type="button" @click="profileOpen = !profileOpen"
                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-900 text-sm font-bold text-white focus:outline-none focus:ring-2 focus:ring-pink-300">
                                        {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(auth()->user()->name, 0, 1)) }}
                                    </button>

                                    <div x-cloak x-show="profileOpen"
                                        class="absolute right-0 z-50 mt-3 w-52 rounded-xl border border-gray-200 bg-white p-2 shadow-lg">
                                        <a href="{{ route('orders.history') }}" wire:navigate
                                            class="block rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                            Order History
                                        </a>
                                        <a href="{{ route('cart') }}" wire:navigate
                                            class="block rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                            Cart
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @else
                            <li>
                                <a href="{{ route('cart') }}" wire:navigate class="text-black hover:underline">Cart</a>
                            </li>
                        @endauth
                    </ul>
                </nav>

                <button type="button" @click="mobileOpen = !mobileOpen"
                    class="md:hidden flex items-center justify-center h-10 w-10 rounded-lg border border-gray-200 bg-white text-black focus:outline-none focus:ring-2 focus:ring-pink-300">
                    <svg class="fill-current" width="18" height="17" viewBox="0 0 18 17">
                        <path
                            d="M15.9773 0.461304H1.04219C0.466585 0.461304 0 0.790267 0 1.19609C0 1.60192 0.466668 1.93088 1.04219 1.93088H15.9773C16.5529 1.93088 17.0195 1.60192 17.0195 1.19609C17.0195 0.790208 16.5529 0.461304 15.9773 0.461304Z" />
                        <path
                            d="M15.9773 7.68802H1.04219C0.466585 7.68802 0 8.01698 0 8.42281C0 8.82864 0.466668 9.1576 1.04219 9.1576H15.9773C16.5529 9.1576 17.0195 8.82864 17.0195 8.42281C17.0195 8.01692 16.5529 7.68802 15.9773 7.68802Z" />
                        <path
                            d="M15.9773 14.9147H1.04219C0.466585 14.9147 0 15.2437 0 15.6495C0 16.0553 0.466668 16.3843 1.04219 16.3843H15.9773C16.5529 16.3843 17.0195 16.0553 17.0195 15.6495C17.0195 15.2436 16.5529 14.9147 15.9773 14.9147Z" />
                    </svg>
                </button>
            </div>

            <div x-cloak x-show="mobileOpen" class="mt-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-lg md:hidden">
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('products') }}" wire:navigate class="block rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Catalog</a>
                    </li>
                    <li>
                        <a href="#" class="block rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Delivery</a>
                    </li>
                    <li>
                        <a href="#" class="block rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Rewards</a>
                    </li>

                    @auth
                        <li class="pt-2">
                            <button type="button" @click="profileOpen = !profileOpen"
                                class="flex w-full items-center justify-between rounded-lg border border-gray-200 px-3 py-2 text-left text-sm font-semibold text-gray-800">
                                <span class="flex items-center gap-2">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-gray-900 text-xs font-bold text-white">
                                        {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(auth()->user()->name, 0, 1)) }}
                                    </span>
                                    Account
                                </span>
                                <span class="text-gray-400">▾</span>
                            </button>

                            <div x-cloak x-show="profileOpen" class="mt-2 space-y-1 rounded-lg bg-gray-50 p-2">
                                <a href="{{ route('orders.history') }}" wire:navigate
                                    class="block rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-white">
                                    Order History
                                </a>
                                <a href="{{ route('cart') }}" wire:navigate
                                    class="block rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-white">
                                    Cart
                                </a>
                                <a href="{{ route('dashboard') }}" wire:navigate
                                    class="block rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-white">
                                    Dashboard
                                </a>
                            </div>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('cart') }}" wire:navigate class="block rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cart</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </header>
</div>
<!-- END: HEADER -->

<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Actions\Frontend\Checkout;

new #[Layout('layouts.frontend')] class extends Component {
    #[Computed]
    public function carts()
    {
        return app(Checkout::class)->handleCheckout();
    }
};

?>

<div>
    <section class="bg-gray-50 py-8 px-4">
        <div class="container mx-auto max-w-6xl">
            <ul class="flex items-center text-sm md:text-base text-gray-500">
                <li>
                    <a href="index.html" class="hover:text-pink-500 transition-colors">Home</a>
                </li>
                <li>
                    <span class="mx-3">/</span>
                </li>
                <li>
                    <a href="#" aria-label="current-page" class="font-semibold text-gray-900">Checkout</a>
                </li>
            </ul>
        </div>
    </section>
    <section class="py-10 md:py-16 bg-gray-50">
        <div class="container mx-auto px-4 max-w-6xl">

            <livewire:frontend.checkout-address />
            <div
                class="bg-white p-6 md:p-8 rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] border border-gray-100 mb-8">
                <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-6">Product Ordered</h3>

                <div
                    class="hidden md:grid grid-cols-12 gap-4 border-b border-gray-200 pb-4 mb-6 text-sm font-semibold text-gray-400 uppercase tracking-wider">
                    <div class="col-span-6">Product Detail</div>
                    <div class="col-span-2 text-center">Quantity</div>
                    <div class="col-span-2 text-right">Unit Price</div>
                    <div class="col-span-2 text-right">Subtotal</div>
                </div>

                @foreach ($this->carts['Cart'] as $cart)
                    {{-- @dd($cart) --}}
                    <div
                        class="flex flex-col md:grid md:grid-cols-12 gap-4 items-start md:items-center border-b border-gray-100 pb-6 mb-6">
                        <div class="col-span-6 flex items-center gap-4 w-full">
                            <img src="{{ $cart['product']->galleries()->exists() ? asset('storage/' . $cart['product']->galleries->first()->image) : 'data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==' }}"
                                alt="{{ $cart['product']->name }}"
                                class="w-20 h-20 md:w-24 md:h-24 object-cover rounded-xl border border-gray-100" />
                            <div>
                                <h6 class="font-bold text-lg text-gray-900 mb-1">{{ $cart['product']->name }}</h6>
                                <p class="text-sm text-gray-500 mb-2 md:mb-0">{{ $cart['product']->category->name }}
                                </p>
                                <p class="text-pink-500 font-semibold md:hidden">
                                    {{ formatRupiah($cart['product']->price) }}</p>
                            </div>
                        </div>

                        <div class="col-span-2 w-full flex justify-between md:block md:text-center mt-3 md:mt-0">
                            <span class="text-gray-500 font-medium md:hidden">Quantity:</span>
                            <span class="font-bold text-gray-900 text-lg md:text-base">{{ $cart['quantity'] }}</span>
                        </div>

                        <div class="col-span-2 hidden md:block text-right font-medium text-gray-500">
                            {{ formatRupiah($cart['product']->price) }}
                        </div>

                        <div class="col-span-2 w-full flex justify-between md:block text-right mt-3 md:mt-0">
                            <span class="text-gray-500 font-medium md:hidden">Subtotal:</span>
                            <span
                                class="font-bold text-gray-900 text-lg md:text-base">{{ formatRupiah($cart['subtotal']) }}</span>
                        </div>
                    </div>
                @endforeach
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end mt-8 gap-6">
                    <div class="w-full md:w-1/3">
                        <label class="block text-sm font-bold text-gray-900 mb-2">Select Courier</label>
                        <div class="relative">
                            <select
                                class="w-full appearance-none bg-gray-50 border border-gray-300 text-gray-900 font-medium rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent transition-all">
                                <option value="">Choose courier service...</option>
                                <option value="jne" selected>JNE OKE (2-3 Days) - IDR 25,000</option>
                                <option value="pos">POS REG (3-4 Days) - IDR 20,000</option>
                                <option value="tiki">TIKI REG (1-2 Days) - IDR 35,000</option>
                            </select>
                            <div
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path
                                        d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div
                        class="w-full md:w-auto flex justify-between md:justify-end items-center md:gap-4 bg-gray-50 md:bg-transparent p-4 md:p-0 rounded-xl">
                        <span class="text-gray-500 font-medium">Total ({{ $this->carts['total_items'] }} Items):</span>
                        <span
                            class="text-xl font-bold text-gray-900">{{ formatRupiah($this->carts['grand_total']) }}</span>
                    </div>
                </div>
            </div>
            <div class="flex justify-end">
                <div class="w-full lg:w-5/12 bg-gray-900 text-white p-6 md:p-8 rounded-3xl shadow-xl">
                    <h3 class="text-xl font-bold mb-6">Payment Summary</h3>

                    <div class="space-y-4 mb-6 text-gray-300">
                        <div class="flex justify-between items-center">
                            <span>Total Product</span>
                            <span class="font-medium text-white">{{ formatRupiah($this->carts['grand_total']) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Courier Cost</span>
                            <span class="font-medium text-white">IDR 25,000</span>
                        </div>
                    </div>

                    <div class="border-t border-gray-700 pt-6 mb-8">
                        <div class="flex justify-between items-center">
                            <span class="text-lg">Total Payment</span>
                            <span
                                class="text-2xl md:text-3xl font-bold text-pink-400">{{ formatRupiah($this->carts['grand_total']) }}</span>
                        </div>
                    </div>

                    <form action="#">
                        <button type="submit"
                            class="w-full bg-pink-400 hover:bg-pink-500 text-gray-900 font-bold text-lg py-4 rounded-xl transition-colors duration-200 focus:outline-none focus:ring-4 focus:ring-pink-300">
                            Checkout Now
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

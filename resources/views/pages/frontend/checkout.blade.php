<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use App\Actions\Frontend\Checkout;
use App\Actions\Frontend\SaveTransaction;
use App\Actions\Payment\CalculateFinalPayment;
use App\Actions\Payment\OneTimePayment;


new #[Layout('layouts.frontend')] class extends Component {
    public int $courierCost = 0;
    public string $selectedCourierService = '';
    public string $selectedCourierCode = '';
    public string $selectedCourierServiceCode = '';
    public string $selectedCourier = '';

    #[Computed]
    public function carts()
    {
        return app(Checkout::class)->handleCheckout();
    }

    #[Computed]
    public function totalPayment(): int
    {
        return (int) $this->carts['grand_total'] + $this->courierCost;
    }

    #[On('courier-selected')]
    public function updateCourierCost(int $cost = 0, string $service = '', string $courier = '', string $serviceCode = ''): void
    {
        $this->courierCost = max(0, $cost);
        $this->selectedCourierService = $service;
        $this->selectedCourierCode = $courier;
        $this->selectedCourierServiceCode = $serviceCode;
    }

    public function payment()
    {
        $dataPayment = app(CalculateFinalPayment::class)->handleCalculateFinalPayment(
            $this->selectedCourierService,
            $this->selectedCourierCode,
            $this->selectedCourierServiceCode,
            $this->courierCost,
        );

        
        $xenditPayment = app(OneTimePayment::class)->sessionPayment($dataPayment);

        app(SaveTransaction::class)->handleSaveTransaction($dataPayment, $xenditPayment);
        return redirect()->away($xenditPayment['payment_link_url']);

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
                    <div class="col-span-5">Product Detail</div>
                    <div class="col-span-1 text-center">Qty</div>
                    <div class="col-span-2 text-right">Unit Price</div>
                    <div class="col-span-2 text-right">Discount</div>
                    <div class="col-span-2 text-right">Subtotal</div>
                </div>

                @foreach ($this->carts['Cart'] as $cart)
                    {{-- @dd($cart) --}}
                    <div
                        class="flex flex-col md:grid md:grid-cols-12 gap-4 items-start md:items-center border-b border-gray-100 pb-6 mb-6">
                        <div class="col-span-5 flex items-center gap-4 w-full min-w-0">
                            <img src="{{ $cart['product']->galleries()->exists() ? asset('storage/' . $cart['product']->galleries->first()->image) : 'data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==' }}"
                                alt="{{ $cart['product']->name }}"
                                class="w-20 h-20 md:w-24 md:h-24 shrink-0 object-cover rounded-xl border border-gray-100" />
                            <div class="min-w-0">
                                <h6 class="font-bold text-lg text-gray-900 mb-1 break-words">
                                    {{ $cart['product']->name }}</h6>
                                <p class="text-sm text-gray-500 mb-2 md:mb-0 break-words">
                                    {{ $cart['product']->category->name }}
                                </p>
                                <div class="md:hidden">
                                    <p class="text-pink-500 font-semibold">
                                        {{ formatRupiah($cart['unit_price']) }}</p>
                                    @if ($cart['discount_amount'] > 0)
                                        <div class="mt-1 flex flex-wrap items-center gap-2">
                                            <span class="text-sm text-gray-400 line-through">
                                                {{ formatRupiah($cart['original_price']) }}
                                            </span>
                                            <span
                                                class="rounded-full bg-pink-500 px-2 py-0.5 text-[10px] font-bold text-white">
                                                {{ $cart['product']->discount_badge }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-span-1 w-full flex justify-between items-center gap-4 md:block md:text-center mt-3 md:mt-0">
                            <span class="text-gray-500 font-medium md:hidden">Quantity:</span>
                            <span
                                class="font-bold text-gray-900 text-lg md:text-base shrink-0">{{ $cart['quantity'] }}</span>
                        </div>

                        <div class="col-span-2 hidden md:block text-right font-medium text-gray-500">
                            <div class="text-gray-900 font-semibold">{{ formatRupiah($cart['unit_price']) }}</div>
                            @if ($cart['discount_amount'] > 0)
                                <div class="mt-1 flex flex-col items-end gap-1">
                                    <span class="text-sm text-gray-400 line-through">
                                        {{ formatRupiah($cart['original_price']) }}
                                    </span>
                                    <span
                                        class="rounded-full bg-pink-500 px-2 py-0.5 text-[10px] font-bold text-white">
                                        {{ $cart['product']->discount_badge }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="col-span-2 hidden md:block text-right">
                            @if ($cart['line_discount'] > 0)
                                <span
                                    class="font-semibold text-pink-500">-{{ formatRupiah($cart['line_discount']) }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </div>

                        <div class="flex md:hidden w-full items-center justify-between gap-4 text-sm">
                            <span class="text-gray-500 font-medium">Discount:</span>
                            <span class="font-semibold text-pink-500 text-right shrink-0">
                                {{ $cart['line_discount'] > 0 ? '-' . formatRupiah($cart['line_discount']) : '-' }}
                            </span>
                        </div>

                        <div class="col-span-2 w-full flex items-center justify-between gap-4 md:block text-right mt-3 md:mt-0">
                            <span class="text-gray-500 font-medium md:hidden shrink-0">Subtotal:</span>
                            <div class="min-w-0">
                                <span
                                    class="font-bold text-gray-900 text-lg md:text-base">{{ formatRupiah($cart['subtotal']) }}</span>
                                @if ($cart['line_discount'] > 0)
                                    <div class="text-sm text-gray-400 line-through">
                                        {{ formatRupiah($cart['original_subtotal']) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end mt-8 gap-6">
                    <div class="w-full md:w-1/3">
                        <label class="block text-sm font-bold text-gray-900 mb-2">Select Courier</label>
                        <livewire:frontend.courier-list />
                    </div>

                    <div
                        class="w-full md:w-auto flex justify-between md:justify-end items-center gap-4 bg-gray-50 md:bg-transparent p-4 md:p-0 rounded-xl">
                        <span class="text-gray-500 font-medium">Grand Total ({{ $this->carts['total_items'] }}
                            Items):</span>
                        <span
                            class="text-xl font-bold text-gray-900 text-right shrink-0 whitespace-nowrap tabular-nums">{{ formatRupiah($this->totalPayment) }}</span>
                    </div>
                </div>
            </div>
            <div class="flex justify-end">
                <div class="w-full lg:w-5/12 bg-gray-900 text-white p-6 md:p-8 rounded-3xl shadow-xl">
                    <h3 class="text-xl font-bold mb-6">Payment Summary</h3>

                    <div class="space-y-4 mb-6 text-gray-300">
                        <div class="flex items-start justify-between gap-3">
                            <span class="text-sm md:text-base leading-snug min-w-0 break-words">Original Price</span>
                            <span
                                class="text-sm md:text-base font-medium text-white text-right shrink-0 whitespace-nowrap tabular-nums">{{ formatRupiah($this->carts['original_grand_total']) }}</span>
                        </div>
                        <div class="flex items-start justify-between gap-3">
                            <span class="text-sm md:text-base leading-snug min-w-0 break-words">Total Discount</span>
                            <span
                                class="text-sm md:text-base font-medium text-pink-400 text-right shrink-0 whitespace-nowrap tabular-nums">-{{ formatRupiah($this->carts['total_discount']) }}</span>
                        </div>
                        <div class="flex items-start justify-between gap-3">
                            <span class="text-sm md:text-base leading-snug min-w-0 break-words">Product Subtotal</span>
                            <span
                                class="text-sm md:text-base font-medium text-white text-right shrink-0 whitespace-nowrap tabular-nums">{{ formatRupiah($this->carts['grand_total']) }}</span>
                        </div>
                        <div class="flex items-start justify-between gap-3">
                            <span class="text-sm md:text-base leading-snug min-w-0 break-words">Courier Cost
                                {{ $selectedCourierService !== '' ? '(' . $selectedCourierService . ')' : '' }}</span>
                            <span
                                class="text-sm md:text-base font-medium text-white text-right shrink-0 whitespace-nowrap tabular-nums">{{ formatRupiah($courierCost) }}</span>
                        </div>
                    </div>

                    <div class="border-t border-gray-700 pt-6 mb-8">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-base md:text-lg">Total Payment</span>
                            <span
                                class="text-xl md:text-3xl font-bold text-pink-400 text-right whitespace-nowrap tabular-nums">{{ formatRupiah($this->totalPayment) }}</span>
                        </div>
                    </div>

                    <button type="submit" wire:click="payment()"
                        class="w-full bg-pink-400 hover:bg-pink-500 text-gray-900 font-bold text-lg py-4 rounded-xl transition-colors duration-200 focus:outline-none focus:ring-4 focus:ring-pink-300">
                        Checkout Now
                    </button>
                </div>
            </div>
        </div>
    </section>
</div>

<?php

use Livewire\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.frontend')] class extends Component {
    public string $ref = '';

    public function mount(): void
    {
        $this->ref = (string) request()->query('ref', '');
    }
};

?>

<div>
    <section class="bg-gray-50 py-8 px-4">
        <div class="container mx-auto max-w-6xl">
            <ul class="flex items-center text-sm md:text-base text-gray-500">
                <li>
                    <a href="{{ route('home') }}" class="hover:text-pink-500 transition-colors">Home</a>
                </li>
                <li>
                    <span class="mx-3">/</span>
                </li>
                <li>
                    <span class="font-semibold text-gray-900">Payment Success</span>
                </li>
            </ul>
        </div>
    </section>

    <section class="py-16 md:py-24 bg-gray-50">
        <div class="container mx-auto px-4 max-w-lg text-center">
            <div class="bg-white p-8 md:p-12 rounded-3xl shadow-lg">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Payment Successful!</h1>
                <p class="text-gray-500 mb-2">Thank you for your purchase.</p>
                @if ($ref !== '')
                    <p class="text-sm text-gray-400">Reference: <span class="font-mono font-medium text-gray-600">{{ $ref }}</span></p>
                @endif
                <a href="{{ route('orders.history') }}"
                    class="inline-block mt-8 bg-pink-400 hover:bg-pink-500 text-gray-900 font-bold px-8 py-3 rounded-xl transition-colors">
                    View Order History
                </a>
            </div>
        </div>
    </section>
</div>

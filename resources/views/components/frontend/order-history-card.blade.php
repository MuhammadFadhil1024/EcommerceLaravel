@props([
    'order',
])

@php
    $statusColors = [
        'pending' => 'border-amber-200 bg-amber-50 text-amber-700',
        'paid' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
        'shipped' => 'border-blue-200 bg-blue-50 text-blue-700',
        'delivered' => 'border-green-200 bg-green-50 text-green-700',
        'cancelled' => 'border-red-200 bg-red-50 text-red-700',
    ];

    $statusClass = $statusColors[$order->status] ?? 'border-gray-200 bg-gray-50 text-gray-700';
    $statusLabel = ucfirst(str_replace('_', ' ', $order->status));
    $hasPaymentLink = filled($order->payment_url);
@endphp

<article class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm transition-shadow hover:shadow-md md:p-6">
    <div class="flex flex-col gap-3 border-b border-gray-100 pb-4 md:flex-row md:items-center md:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Order</p>
            <h3 class="text-lg font-bold text-gray-900">#ORD-{{ str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) }}</h3>
            <p class="mt-1 text-xs text-gray-500">{{ $order->created_at?->format('d M Y, H:i') }}</p>
        </div>

        <span class="inline-flex w-fit rounded-full border px-3 py-1 text-xs font-semibold {{ $statusClass }}">
            {{ $statusLabel }}
        </span>
    </div>

    <div class="mt-4 grid gap-2.5 text-sm sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-gray-100 bg-gray-50/80 p-3">
            <p class="text-xs uppercase tracking-wide text-gray-400">Total Payment</p>
            <p class="mt-1 font-bold text-gray-900">{{ formatRupiah($order->total_payment) }}</p>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-gray-50/80 p-3">
            <p class="text-xs uppercase tracking-wide text-gray-400">Courier</p>
            <p class="mt-1 font-semibold text-gray-800">{{ $order->courier ?: '-' }}</p>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-gray-50/80 p-3">
            <p class="text-xs uppercase tracking-wide text-gray-400">Payment Method</p>
            <p class="mt-1 font-semibold text-gray-800">{{ $order->payment_method ?: '-' }}</p>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-gray-50/80 p-3">
            <p class="text-xs uppercase tracking-wide text-gray-400">Payment Link</p>

            @if ($hasPaymentLink)
                <a
                    href="{{ $order->payment_url }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="mt-2 inline-flex items-center justify-center rounded-xl bg-pink-500 px-3 py-1.5 text-sm font-semibold text-white transition-colors hover:bg-pink-600"
                >
                    {{ $order->status === 'pending' ? 'Pay Now' : 'Open Link' }}
                </a>
            @else
                <p class="mt-1 text-sm font-medium text-gray-500">Not available</p>
            @endif
        </div>
    </div>

    @if ($order->items->isNotEmpty())
        <div class="mt-5 space-y-3">
            @foreach ($order->items as $item)
                <x-frontend.order-history-item-row :item="$item" />
            @endforeach
        </div>
    @endif
</article>

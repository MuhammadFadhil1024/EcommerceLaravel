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
    $statusLabel = ucfirst($order->status);
@endphp

<article class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm md:p-5">
    <div class="flex flex-col gap-3 border-b border-gray-100 pb-3 md:flex-row md:items-center md:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Order</p>
            <h3 class="text-base font-bold text-gray-900">#ORD-{{ str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) }}</h3>
            <p class="text-xs text-gray-500">{{ $order->created_at?->format('d M Y, H:i') }}</p>
        </div>

        <span class="inline-flex w-fit rounded-full border px-3 py-1 text-xs font-semibold {{ $statusClass }}">
            {{ $statusLabel }}
        </span>
    </div>

    <div class="mt-4 grid gap-3 text-sm md:grid-cols-3">
        <div class="rounded-xl bg-gray-50 p-3">
            <p class="text-xs uppercase tracking-wide text-gray-400">Total Payment</p>
            <p class="mt-1 font-bold text-gray-900">{{ formatRupiah($order->total_price) }}</p>
        </div>

        <div class="rounded-xl bg-gray-50 p-3">
            <p class="text-xs uppercase tracking-wide text-gray-400">Courier</p>
            <p class="mt-1 font-semibold text-gray-800">{{ $order->courier ?: '-' }}</p>
        </div>

        <div class="rounded-xl bg-gray-50 p-3">
            <p class="text-xs uppercase tracking-wide text-gray-400">Payment Method</p>
            <p class="mt-1 font-semibold text-gray-800">{{ $order->payment_method ?: '-' }}</p>
        </div>
    </div>

    @if ($order->items->isNotEmpty())
        <div class="mt-4 space-y-2">
            @foreach ($order->items as $item)
                <x-frontend.order-history-item-row :item="$item" />
            @endforeach
        </div>
    @endif
</article>

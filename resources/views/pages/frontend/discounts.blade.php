@extends('layouts.frontend')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    
    <!-- Filter Section -->
    <div class="mb-8">
        <x-frontend.discount-filter 
            :search="$search" 
            :status="$status" 
            :min_amount="$min_amount" 
            :max_amount="$max_amount" 
        />
        
        <!-- Hidden state variables for filtering -->
        <input type="hidden" name="discount_search" value="{{ $search }}">
        <input type="hidden" name="discount_status" value="{{ $status }}">
        <input type="hidden" name="discount_min_amount" value="{{ $min_amount }}">
        <input type="hidden" name="discount_max_amount" value="{{ $max_amount }}">
    </div>

    <!-- Discount Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach ($discounts as $discount)
            <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden hover:shadow-lg transition-shadow">
                <div class="p-4">
                    <h3 class="text-lg font-bold text-gray-900 line-clamp-2">{{ $discount->code }}</h3>
                    <p class="text-sm text-gray-500 mt-1">Jumlah Diskon: Rp {{ number_format($discount->amount) }}</p>
                    <p class="text-xs text-gray-500 mt-2">Min Order: Rp {{ number_format($discount->min_amount) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Max Order: Rp {{ number_format($discount->max_amount) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Status: <span class="font-medium {{ $discount->status === 'active' ? 'text-green-600' : 'text-gray-400' }}">{{ ucfirst($discount->status) }}</span></p>
                    <p class="text-xs text-gray-500 mt-1">Kadaluarsa: {{ $discount->expires_at ? $discount->expires_at->format('d M Y') : 'Tidak berlaku' }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if ($discounts->hasMorePages())
        <div class="mt-8 text-center">
            {{ $discounts->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Initialize discount filter Livewire wire:model updates
    // Additional JS if needed
</push>
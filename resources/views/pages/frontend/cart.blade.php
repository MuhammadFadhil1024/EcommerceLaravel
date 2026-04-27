<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Actions\Frontend\GetCart;
use Livewire\Attributes\Computed;

new #[Layout('layouts.frontend')] class extends Component {
    public $slug;

    // #[Computed]
    public function carts()
    {
        return app(GetCart::class)->handleGetCart();
    }

    #[Computed]
    public function totalPrice()
    {
        return app(GetCart::class)->calculateTotalPriceCart();
    }

    function decreaseQuantity($productId)
    {
        app(GetCart::class)->decreaseQuantity($productId);
        app(GetCart::class)->calculateTotalPriceCart();
    }

    function incraseQuantity($productId)
    {
        app(GetCart::class)->increaseQuantity($productId);
        app(GetCart::class)->calculateTotalPriceCart();
    }

    function deleteItemCart($productId)
    {
        app(GetCart::class)->deleteItemCart($productId);
        app(GetCart::class)->calculateTotalPriceCart();
    }
};

?>

<div>
    <section class="bg-gray-100 py-8 px-4">
        <div class="container mx-auto">
            <ul class="breadcrumb">
                <li>
                    <a href="{{ route('home') }}">Home</a>
                </li>
                <li>
                    <a href="#" aria-label="current-page">Shopping Cart</a>
                </li>
            </ul>
        </div>
    </section>
    <section class="md:py-16" id="section-cart">
        <div class="container mx-auto px-4">

            <div class="w-full px-4 mb-4 md:mb-0" id="shopping-cart">
                <div class="flex flex-start mb-4 mt-8 pb-3 border-b border-gray-200 md:border-b-0">
                    <h3 class="text-2xl">Shopping Cart</h3>
                </div>

                <div class="border-b border-gray-200 mb-4 hidden md:block">
                    <div class="flex flex-start items-center pb-2 -mx-4">
                        <div class="px-4 flex-none">
                            <div class="" style="width: 80px">
                                <h6>Photo</h6>
                            </div>
                        </div>
                        <div class="px-4 w-5/12">
                            <div class="">
                                <h6>Product</h6>
                            </div>
                        </div>
                        <div class="px-4 w-2/12">
                            <div class="text-center">
                                <h6>Price</h6>
                            </div>
                        </div>
                        <div class="px-4 w-2/12">
                            <div class="text-center">
                                <h6>Quantity</h6>
                            </div>
                        </div>
                        <div class="px-4 w-1/12">
                            <div class="text-center">
                                <h6>Action</h6>
                            </div>
                        </div>
                    </div>
                </div>

                @forelse ($this->carts() as $item)
                    <div class="flex flex-start flex-wrap items-center mb-4 -mx-4" data-row="1">
                        <div class="px-4 flex-none">
                            <div class="" style="width: 80px; height: 80px">
                                <img src="{{ $item['product']->galleries()->exists() ? asset('storage/' . $item['product']->galleries->first()->image) : 'data:image/gif;base64,R0lGODlhAQABAIAAAMLCwgAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==' }}"
                                    alt="{{ $item['product']->name }}" class="object-cover rounded-xl w-full h-full" />
                            </div>
                        </div>
                        <div class="px-4 w-auto md:w-5/12">
                            <div class="">
                                <h6 class="font-semibold text-lg md:text-xl leading-8 mb-2">
                                    {{ $item['product']->name }}
                                </h6>
                                <div class="text-sm md:text-lg mb-4">Office Room</div>

                                <div class="flex justify-center md:hidden mb-4">
                                    <form class="decrase-quantity-form" data-product-id="{{ $item['product']->id }}">
                                        @csrf
                                        <button type="submit"
                                            class="font-semibold text-lg px-4 border border-gray-600">-</button>
                                    </form>
                                    <h6 class="font-semibold text-lg px-4 border border-gray-600 quantityDisplayMobile"
                                        data-product-id="{{ $item['product']->id }}" name="quantity">
                                        {{ $item['quantity'] }}</h6>
                                    <form class="incrase-quantity-form" data-product-id="{{ $item['product']->id }}">
                                        @csrf
                                        <button type="submit"
                                            class="font-semibold text-lg px-4 border border-gray-600">+</button>
                                    </form>
                                </div>
                                <h6 class="font-semibold text-base md:text-lg block md:hidden">
                                    IDR {{ number_format($item['product']->price) }}
                                </h6>
                            </div>
                        </div>

                        <div class="px-4 w-auto flex-none md:w-2/12 hidden md:block">
                            <div class="text-center">
                                <h6 class="font-semibold text-lg">IDR {{ number_format($item['product']->price) }}</h6>
                            </div>
                        </div>

                        <div class="px-4 w-auto flex-none md:w-2/12 hidden md:block">
                            <div class="flex justify-center">
                                <button type="submit" wire:click="decreaseQuantity({{ $item['product']->id }})"
                                    class="font-semibold text-lg px-4 border border-gray-600">-</button>
                                <h6 class="font-semibold text-lg px-4 border border-gray-600 quantityDisplayWeb"
                                    data-product-id="{{ $item['product']->id }}" name="quantity">
                                    {{ $item['quantity'] }}</h6>
                                <button type="submit" wire:click="incraseQuantity({{ $item['product']->id }})"
                                    class="font-semibold text-lg px-4 border border-gray-600">+</button>
                            </div>
                        </div>

                        <div class="px-4 md:w-1/12">
                            <div class="text-center">
                                <button wire:click="deleteItemCart({{ $item['product']->id }})"
                                    class="text-red-600 border-none focus:outline-none px-3 py-1 hover:bg-red-50 rounded-lg transition-colors">
                                    X
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <p id="cart-empty" class="text-center py-8 text-gray-500">
                        Ooops... Keranjang Anda masih kosong. <br>
                        <a href="{{ route('home') }}" class="text-pink-500 underline mt-2 inline-block">Mulai Belanja
                            Sekarang</a>
                    </p>
                @endforelse
            </div>

            @if (count($this->carts()) > 0)
                <div class="mt-10 pt-8 border-t border-gray-200">
                    <div
                        class="flex flex-col md:flex-row justify-between items-center bg-white rounded-2xl shadow-[0_4px_20px_rgb(0,0,0,0.05)] border border-gray-100 p-6 md:p-8">

                        <div
                            class="flex flex-col md:flex-row items-center gap-4 mb-6 md:mb-0 w-full md:w-auto text-center md:text-left">
                            <div class="bg-pink-50 text-pink-500 rounded-full p-4 hidden md:flex">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">
                                    Total Payment
                                </h4>
                                <p class="text-2xl md:text-3xl font-bold text-gray-900">
                                    {{ formatRupiah($this->totalPrice()) }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    Includes tax and service charges
                                </p>
                            </div>
                        </div>

                        <div class="w-full md:w-auto">
                            <button 
                                class="group relative flex items-center justify-center w-full md:w-64 py-4 px-8 font-semibold text-black bg-pink-400 rounded-full overflow-hidden transition-all duration-300 hover:bg-black hover:text-white hover:shadow-xl hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-pink-300">
                                <div
                                    class="absolute inset-0 w-full h-full -x-full bg-gradient-to-r from-transparent via-white/20 to-transparent group-hover:animate-shimmer">
                                </div>

                                <span class="relative flex items-center gap-2">
                                    Checkout Now
                                    <svg class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-1"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </span>
                            </button>
                        </div>

                    </div>
                </div>
            @endif
        </div>
    </section>
</div>
{{-- <script>
        $(document).ready(function() {

            // FUNCTION FOR INCREASE QUANTITY CART 
            $('.incrase-quantity-form').on('submit', function(e) {
                e.preventDefault();

                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                var productId = $(this).data('product-id');

                $.ajax({
                    type: 'PUT',
                    dataType: 'json',
                    url: '/cart/incrasequantity',
                    data: {
                        _token: CSRF_TOKEN,
                        quantity: 1,
                        productItemCartId: productId
                    },
                    success: function(response) {
                        console.log(response);
                        updateQuantityDisplay(productId, response);
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });
            // END OF FUNCTION FOR INCREASE QUANTITY CART 

            // FUNCTION FOR DECREASE QUANTITY CART 
            $('.decrase-quantity-form').on('submit', function(e) {
                e.preventDefault();

                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                var productId = $(this).data('product-id');
                var quantityDisplayWeb = $('.quantityDisplayWeb[data-product-id="' + productId + '"]');
                var quantityDisplayMobile = $('.quantityDisplayMobile[data-product-id="' + productId +
                    '"]');
                var quantityValueWeb = parseInt(quantityDisplayWeb.text());
                var quantityValueMobile = parseInt(quantityDisplayMobile.text());

                if (quantityValueWeb > 1) {
                    // If quantity is greater than 1, decrease the quantity
                    $.ajax({
                        type: 'PUT',
                        dataType: 'json',
                        url: '/cart/decrasequantity/',
                        data: {
                            _token: CSRF_TOKEN,
                            quantity: 1,
                            productItemCartId: productId
                        },
                        success: function(response) {
                            console.log(response);
                            updateQuantityDisplay(productId, response);
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                } else {
                    // If quantity is 1 or less, prompt for deletion or take appropriate action
                    console.log('quantity is 1 or less');
                    // Disable the decrease button for the specific product

                    $.ajax({
                        type: 'DELETE',
                        dataType: 'json',
                        url: '/cart/delete/',
                        data: {
                            _token: CSRF_TOKEN,
                            quantity: 1,
                            productItemCartId: productId
                        },
                        success: function(response) {
                            window.location.reload(true)
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });

                    // $('.decrase-quantity-form[data-product-id="' + productId + '"] button').prop('disabled',
                    //     true);

                }
            });
            // END OF FUNCTION FOR DECREASE QUANTITY CART 

            // Function to update quantity display for a specific product
            function updateQuantityDisplay(productId, newQuantity) {
                var quantityDisplayWeb = $('.quantityDisplayWeb[data-product-id="' + productId + '"]');
                var quantityDisplayMobile = $('.quantityDisplayMobile[data-product-id="' + productId + '"]');
                quantityDisplayWeb.text(newQuantity);
                quantityDisplayMobile.text(newQuantity);
                // Enable the decrease button after updating the quantity
                $('.decrase-quantity-form[data-product-id="' + productId + '"] button').prop('disabled', false);
            }
        });
    </script> --}}

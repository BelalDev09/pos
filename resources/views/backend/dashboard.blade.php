{{-- File: resources/views/pos/index.blade.php --}}

@extends('layouts.pos') {{-- Updated layout --}}

@section('content')
<div class="flex h-screen w-full" x-data="posApp()" x-init="init()">

    {{-- ── LEFT PANEL: Product Grid  --}}
    <div class="flex flex-col w-3/5 bg-white border-r border-gray-200">

        {{-- Search / Barcode Bar --}}
        <div class="p-3 bg-gray-50 border-b border-gray-200">
            <div class="relative">
                <input type="text"
                    id="barcode-input"
                    x-model="searchQuery"
                    @input.debounce.300ms="searchProducts()"
                    @keydown.enter.prevent="handleEnterKey()"
                    placeholder="🔍 Scan barcode or search product (F2)"
                    class="w-full pl-4 pr-12 py-3 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    autocomplete="off">
                <button @click="toggleCameraScanner()"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600">
                    📷
                </button>
            </div>
        </div>

        {{-- Category Pills --}}
        <div class="flex gap-2 px-3 py-2 overflow-x-auto border-b border-gray-100">
            <button @click="selectedCategory = null; loadProducts()"
                :class="selectedCategory === null ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600'"
                class="flex-shrink-0 px-4 py-1.5 rounded-full text-xs font-medium transition-colors">
                All
            </button>
            @foreach($categories as $category)
            <button @click="selectedCategory = {{ $category->id }}; loadProducts()"
                :class="selectedCategory === {{ $category->id }} ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600'"
                class="flex-shrink-0 px-4 py-1.5 rounded-full text-xs font-medium transition-colors">
                {{ $category->name }}
            </button>
            @endforeach
        </div>

        {{-- Product Grid --}}
        <div class="flex-1 overflow-y-auto p-3">
            <div class="grid grid-cols-3 xl:grid-cols-4 gap-3" id="product-grid">

                {{-- Loading state --}}
                <template x-if="loadingProducts">
                    <div class="col-span-full flex justify-center py-12">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    </div>
                </template>

                {{-- Products --}}
                <template x-for="product in products" :key="product.id">
                    <div @click="addToCart(product.default_variant_id ?? product.id)"
                        class="bg-white border border-gray-200 rounded-xl p-3 cursor-pointer
                                hover:border-blue-400 hover:shadow-md transition-all select-none"
                        :class="{'opacity-50 cursor-not-allowed': product.stock_qty <= 0 && product.track_stock}">

                        {{-- Product Image --}}
                        <div class="aspect-square rounded-lg bg-gray-100 mb-2 overflow-hidden flex items-center justify-center">
                            <img x-show="product.image"
                                :src="'/storage/' + product.image"
                                :alt="product.name"
                                class="w-full h-full object-cover">
                            <span x-show="!product.image" class="text-3xl">📦</span>
                        </div>

                        {{-- Product Info --}}
                        <p class="text-xs text-gray-500 truncate" x-text="product.category?.name"></p>
                        <p class="text-sm font-semibold text-gray-800 truncate leading-tight" x-text="product.name"></p>

                        {{-- Price & Stock --}}
                        <div class="flex items-center justify-between mt-1">
                            <span class="text-blue-700 font-bold text-sm" x-text="formatMoney(product.selling_price)"></span>
                            <span class="text-xs px-1.5 py-0.5 rounded"
                                :class="{
                                      'bg-green-100 text-green-700': product.stock_qty > 5,
                                      'bg-yellow-100 text-yellow-700': product.stock_qty > 0 && product.stock_qty <= 5,
                                      'bg-red-100 text-red-700': product.stock_qty <= 0
                                  }"
                                x-text="product.track_stock ? product.stock_qty + ' left' : '∞'">
                            </span>
                        </div>
                    </div>
                </template>

                {{-- Empty state --}}
                <template x-if="!loadingProducts && products.length === 0">
                    <div class="col-span-full text-center py-12 text-gray-400">
                        <p class="text-4xl mb-2">🔍</p>
                        <p class="text-sm">No products found</p>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ── RIGHT PANEL: Cart --}}
    <div class="flex flex-col w-2/5 bg-gray-50">

        {{-- Cart Header --}}
        <div class="flex items-center justify-between px-4 py-3 bg-white border-b border-gray-200">
            <div>
                <h2 class="font-semibold text-gray-800">Current Sale</h2>
                <p class="text-xs text-gray-500" x-text="cart.item_count + ' items'"></p>
            </div>
            <div class="flex gap-2">
                {{-- Customer selector --}}
                <button @click="openCustomerModal()"
                    class="text-sm px-3 py-1.5 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center gap-1">
                    <span x-text="selectedCustomer ? selectedCustomer.name : 'Walk-in'"></span>
                    <span class="text-gray-400">▾</span>
                </button>
                <button @click="clearCart()"
                    class="text-sm px-3 py-1.5 border border-red-200 text-red-600 rounded-lg hover:bg-red-50"
                    x-show="cart.item_count > 0">
                    Clear
                </button>
            </div>
        </div>

        {{-- Cart Items --}}
        <div class="flex-1 overflow-y-auto px-3 py-2 space-y-2">

            <template x-if="cart.item_count === 0">
                <div class="flex flex-col items-center justify-center h-40 text-gray-400">
                    <span class="text-5xl mb-2">🛒</span>
                    <p class="text-sm">Cart is empty</p>
                    <p class="text-xs">Scan or click a product to add</p>
                </div>
            </template>

            <template x-for="item in cart.items" :key="item.variant_id">
                @include('components.pos.cart-item')
            </template>
        </div>

        {{-- Coupon input --}}
        <div class="px-3 pb-2" x-show="cart.item_count > 0">
            <div class="flex gap-2" x-show="!cart.coupon">
                <input type="text"
                    x-model="couponCode"
                    @keydown.enter="applyCoupon()"
                    placeholder="Coupon code..."
                    class="flex-1 text-sm px-3 py-2 border border-gray-300 rounded-lg">
                <button @click="applyCoupon()"
                    class="px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                    Apply
                </button>
            </div>
            <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded-lg px-3 py-2"
                x-show="cart.coupon">
                <span class="text-green-700 text-sm font-medium" x-text="'Coupon: ' + cart.coupon?.code"></span>
                <button @click="removeCoupon()" class="text-red-400 hover:text-red-600 text-xs">Remove</button>
            </div>
        </div>

        {{-- Order Totals --}}
        <div class="bg-white border-t border-gray-200 px-4 py-3 space-y-1.5">
            <div class="flex justify-between text-sm text-gray-600">
                <span>Subtotal</span>
                <span x-text="formatMoney(cart.subtotal)"></span>
            </div>
            <div class="flex justify-between text-sm text-red-500" x-show="cart.discount_total > 0">
                <span>Discount</span>
                <span x-text="'- ' + formatMoney(cart.discount_total)"></span>
            </div>
            <div class="flex justify-between text-sm text-gray-600">
                <span>Tax</span>
                <span x-text="formatMoney(cart.tax_total)"></span>
            </div>
            <div class="flex justify-between text-lg font-bold text-gray-900 border-t pt-2 mt-2">
                <span>TOTAL</span>
                <span x-text="formatMoney(cart.grand_total)" class="text-blue-700"></span>
            </div>
        </div>

        {{-- Payment Buttons --}}
        <div class="p-3 grid grid-cols-3 gap-2 bg-white border-t">
            <button @click="openPayment('cash')"
                :disabled="cart.item_count === 0"
                class="py-4 bg-green-600 text-white rounded-xl font-semibold
                           hover:bg-green-700 disabled:opacity-40 disabled:cursor-not-allowed
                           flex flex-col items-center gap-1">
                <span class="text-xl">💵</span>
                <span class="text-xs">Cash</span>
            </button>
            <button @click="openPayment('card')"
                :disabled="cart.item_count === 0"
                class="py-4 bg-blue-600 text-white rounded-xl font-semibold
                           hover:bg-blue-700 disabled:opacity-40 disabled:cursor-not-allowed
                           flex flex-col items-center gap-1">
                <span class="text-xl">💳</span>
                <span class="text-xs">Card</span>
            </button>
            <button @click="openPayment('split')"
                :disabled="cart.item_count === 0"
                class="py-4 bg-purple-600 text-white rounded-xl font-semibold
                           hover:bg-purple-700 disabled:opacity-40 disabled:cursor-not-allowed
                           flex flex-col items-center gap-1">
                <span class="text-xl">⚡</span>
                <span class="text-xs">Split</span>
            </button>
        </div>
    </div>

    {{-- ── Modals  --}}
    @include('components.pos.payment-modal')
    @include('components.pos.numpad')
</div>

@push('scripts')
<script>
    import('/resources/js/pos/cart.js');
</script>
@endpush
@endsection
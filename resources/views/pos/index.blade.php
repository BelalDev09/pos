@extends('backend.app')

@section('content')
<div class="h-screen bg-[#f3f6f9] overflow-hidden"
     x-data="posApp()" x-init="init()">

    <div class="h-full flex gap-6 p-6">

        {{-- ================= LEFT: PRODUCTS ================= --}}
        <section class="w-3/5 flex flex-col bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

            {{-- TOP BAR --}}
            <div class="px-5 py-4 border-b bg-white flex items-center gap-4">

                <div class="relative flex-1">
                    <i class="ri-search-line absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>

                    <input
                        id="barcode-input"
                        x-model="searchQuery"
                        @input.debounce.300ms="searchProducts()"
                        @keydown.enter.prevent="handleEnterKey()"
                        placeholder="Search product, barcode..."
                        class="w-full pl-11 pr-4 py-3 rounded-lg bg-gray-50 border border-gray-200
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                    >
                </div>

                <div class="hidden xl:flex text-xs text-gray-400 space-y-1 leading-tight">
                    <div><span class="font-semibold text-gray-500">F2</span> Search</div>
                    <div><span class="font-semibold text-gray-500">Enter</span> Add</div>
                </div>

            </div>

            {{-- CATEGORY BAR --}}
            <div class="px-5 py-3 border-b bg-[#f8fafc]">

                <div class="flex gap-2 overflow-x-auto no-scrollbar">

                    <button
                        @click="selectedCategory=null;loadProducts()"
                        class="px-4 py-2 text-xs font-medium rounded-lg transition border"
                        :class="selectedCategory===null
                            ? 'bg-blue-600 text-white border-blue-600'
                            : 'bg-white text-gray-600 border-gray-200'">
                        All
                    </button>

                    @foreach($categories as $category)
                        <button
                            @click="selectedCategory={{ $category->id }};loadProducts()"
                            class="px-4 py-2 text-xs font-medium rounded-lg transition border whitespace-nowrap"
                            :class="selectedCategory==={{ $category->id }}
                                ? 'bg-blue-600 text-white border-blue-600'
                                : 'bg-white text-gray-600 border-gray-200'">
                            {{ $category->name }}
                        </button>
                    @endforeach

                </div>
            </div>

            {{-- PRODUCTS GRID --}}
            <div class="flex-1 overflow-y-auto p-5 bg-[#f3f6f9]">

                <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">

                    {{-- loading --}}
                    <template x-if="loadingProducts">
                        <div class="col-span-full flex justify-center py-16">
                            <div class="w-10 h-10 border-4 border-gray-200 border-t-blue-600 rounded-full animate-spin"></div>
                        </div>
                    </template>

                    {{-- product card --}}
                    <template x-for="product in products" :key="product.id">
                        <div
                            @click="addToCart(product.default_variant_id ?? product.id)"
                            class="bg-white border border-gray-200 rounded-lg p-3 cursor-pointer
                                   hover:shadow-md hover:-translate-y-0.5 transition">

                            {{-- IMAGE --}}
                            <div class="aspect-square rounded-lg bg-gray-50 flex items-center justify-center overflow-hidden mb-3">
                                <img x-show="product.image"
                                     :src="'/storage/' + product.image"
                                     class="w-full h-full object-cover">

                                <i x-show="!product.image" class="ri-box-3-line text-3xl text-gray-300"></i>
                            </div>

                            {{-- CATEGORY --}}
                            <p class="text-[10px] text-gray-400 uppercase truncate"
                               x-text="product.category?.name"></p>

                            {{-- NAME --}}
                            <p class="text-sm font-semibold text-gray-800 truncate"
                               x-text="product.name"></p>

                            {{-- PRICE --}}
                            <div class="flex justify-between items-center mt-2">
                                <span class="text-blue-600 font-bold text-sm"
                                      x-text="formatMoney(product.selling_price)">
                                </span>

                                <span class="text-[10px] px-2 py-0.5 rounded-md font-medium"
                                      :class="{
                                        'bg-green-100 text-green-700': product.stock_qty > 5,
                                        'bg-yellow-100 text-yellow-700': product.stock_qty > 0 && product.stock_qty <= 5,
                                        'bg-red-100 text-red-700': product.stock_qty <= 0
                                      }"
                                      x-text="product.track_stock ? product.stock_qty+' pcs' : '∞'">
                                </span>
                            </div>

                        </div>
                    </template>

                </div>
            </div>

        </section>

        {{-- ================= RIGHT: CART ================= --}}
        <aside class="w-2/5 flex flex-col bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

            {{-- HEADER --}}
            <div class="px-5 py-4 border-b flex justify-between items-center bg-white">

                <div>
                    <h2 class="font-semibold text-gray-800">POS Cart</h2>
                    <p class="text-xs text-gray-400" x-text="cart.item_count + ' items'"></p>
                </div>

                <button @click="clearCart()"
                        class="text-xs px-3 py-1.5 border border-red-200 text-red-600 rounded-md hover:bg-red-50">
                    Clear
                </button>

            </div>

            {{-- ITEMS --}}
            <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-[#f3f6f9]">

                <template x-if="cart.item_count===0">
                    <div class="h-full flex flex-col items-center justify-center text-gray-400">
                        <i class="ri-shopping-cart-line text-5xl mb-2"></i>
                        <p class="font-medium">Cart Empty</p>
                    </div>
                </template>

                <template x-for="item in cart.items" :key="item.variant_id">
                    @include('components.pos.cart-item')
                </template>

            </div>

            {{-- TOTAL --}}
            <div class="border-t bg-white p-5 space-y-2">

                <div class="flex justify-between text-sm text-gray-500">
                    <span>Subtotal</span>
                    <span x-text="formatMoney(cart.subtotal)"></span>
                </div>

                <div class="flex justify-between text-sm text-red-500" x-show="cart.discount_total>0">
                    <span>Discount</span>
                    <span x-text="'-'+formatMoney(cart.discount_total)"></span>
                </div>

                <div class="flex justify-between text-sm text-gray-500">
                    <span>Tax</span>
                    <span x-text="formatMoney(cart.tax_total)"></span>
                </div>

                <div class="flex justify-between pt-2 border-t">
                    <span class="font-bold text-lg text-gray-900">Total</span>
                    <span class="font-bold text-xl text-blue-600"
                          x-text="formatMoney(cart.grand_total)">
                    </span>
                </div>

            </div>

            {{-- ACTIONS --}}
            <div class="grid grid-cols-3 gap-3 p-4 border-t bg-white">

                <button @click="openPayment('cash')"
                        class="bg-green-600 text-white py-3 rounded-lg text-sm font-medium hover:shadow">
                    Cash
                </button>

                <button @click="openPayment('card')"
                        class="bg-blue-600 text-white py-3 rounded-lg text-sm font-medium hover:shadow">
                    Card
                </button>

                <button @click="openPayment('split')"
                        class="bg-purple-600 text-white py-3 rounded-lg text-sm font-medium hover:shadow">
                    Split
                </button>

            </div>

        </aside>

    </div>

    @include('components.pos.payment-modal')
    @include('components.pos.numpad')

</div>
@endsection

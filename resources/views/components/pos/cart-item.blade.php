{{-- File: resources/views/components/pos/cart-item.blade.php --}}

<div class="bg-white rounded-xl border border-gray-200 p-3 mb-2"
    x-data="{ qty: item.quantity }">

    <div class="flex items-start gap-3">

        {{-- Product Image --}}
        <div class="w-12 h-12 rounded-lg bg-gray-100 flex-shrink-0 overflow-hidden">
            <img x-show="item.image"
                :src="'/storage/' + item.image"
                class="w-full h-full object-cover">
            <span x-show="!item.image" class="flex items-center justify-center h-full text-lg">📦</span>
        </div>

        {{-- Product Details --}}
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-800 truncate" x-text="item.name"></p>
            <p x-show="item.variant_name"
                class="text-xs text-gray-400 truncate"
                x-text="item.variant_name"></p>

            {{-- Quantity Controls --}}
            <div class="flex items-center gap-2 mt-1">
                <button @click="$parent.updateQuantity(item.variant_id, item.quantity - 1)"
                    class="w-6 h-6 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700
                               flex items-center justify-center text-sm font-bold">
                    −
                </button>

                <input type="number"
                    x-model.number="qty"
                    @change="$parent.updateQuantity(item.variant_id, qty)"
                    @keydown.enter="$parent.updateQuantity(item.variant_id, qty)"
                    class="w-14 text-center text-sm border border-gray-300 rounded-lg py-0.5"
                    min="1"
                    step="1">

                <button @click="$parent.updateQuantity(item.variant_id, item.quantity + 1)"
                    class="w-6 h-6 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700
                               flex items-center justify-center text-sm font-bold">
                    +
                </button>
            </div>
        </div>

        {{-- Price + Remove --}}
        <div class="flex flex-col items-end gap-1">
            <button @click="$parent.removeItem(item.variant_id)"
                class="text-gray-300 hover:text-red-500 transition-colors">
                ✕
            </button>
            <span class="text-sm font-bold text-gray-800" x-text="$parent.formatMoney(item.line_total)"></span>
            <span x-show="item.line_discount > 0" class="text-xs text-red-500"
                x-text="'- ' + $parent.formatMoney(item.line_discount)"></span>
        </div>
    </div>
</div>
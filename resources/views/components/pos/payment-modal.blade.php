{{-- File: resources/views/components/pos/payment-modal.blade.php --}}

<div x-show="paymentModal"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/60"
    @keydown.escape.window="paymentModal = false">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4"
        @click.stop
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h2 class="text-lg font-bold text-gray-900">
                <span x-text="paymentMethod === 'cash' ? '💵 Cash Payment'
                             : paymentMethod === 'card' ? '💳 Card Payment'
                             : '⚡ Split Payment'">
                </span>
            </h2>
            <button @click="paymentModal = false" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>

        {{-- Body --}}
        <div class="px-6 py-5 space-y-4">

            {{-- Total due --}}
            <div class="bg-blue-50 rounded-xl p-4 text-center">
                <p class="text-sm text-blue-600">Amount Due</p>
                <p class="text-4xl font-bold text-blue-700" x-text="formatMoney(cart.grand_total)"></p>
            </div>

            {{-- Cash payment: tendered amount + change --}}
            <div x-show="paymentMethod === 'cash'" class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-700">Amount Tendered</label>
                    <input type="number"
                        x-model="amountTendered"
                        @input="calculateChange()"
                        class="w-full mt-1 px-4 py-3 text-xl text-center border-2 border-gray-300
                                  rounded-xl focus:border-blue-500 focus:outline-none font-bold"
                        step="0.01"
                        min="0"
                        autofocus>
                </div>

                {{-- Quick cash buttons --}}
                <div class="grid grid-cols-4 gap-2">
                    <template x-for="amount in quickCashAmounts">
                        <button @click="amountTendered = amount; calculateChange()"
                            class="py-2 border border-gray-200 rounded-lg text-sm hover:bg-gray-50 font-medium"
                            x-text="formatMoney(amount)">
                        </button>
                    </template>
                </div>

                {{-- Change --}}
                <div class="flex justify-between items-center bg-green-50 rounded-xl px-4 py-3"
                    x-show="changeAmount >= 0">
                    <span class="text-sm font-medium text-green-700">Change</span>
                    <span class="text-2xl font-bold text-green-700"
                        x-text="formatMoney(changeAmount)"></span>
                </div>
            </div>

            {{-- Card payment --}}
            <div x-show="paymentMethod === 'card'" class="space-y-3">
                <div class="bg-gray-50 rounded-xl p-4 text-center text-gray-600 text-sm">
                    <p class="text-xl mb-1">💳</p>
                    Present card to terminal for <strong x-text="formatMoney(cart.grand_total)"></strong>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Transaction Reference (optional)</label>
                    <input type="text"
                        x-model="cardReference"
                        placeholder="Auth code / reference"
                        class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
            </div>

        </div>

        {{-- Footer --}}
        <div class="px-6 pb-6 flex gap-3">
            <button @click="paymentModal = false"
                class="flex-1 py-3 border border-gray-300 rounded-xl text-gray-700 font-medium hover:bg-gray-50">
                Cancel
            </button>
            <button @click="processPayment()"
                :disabled="processingPayment || (paymentMethod === 'cash' && amountTendered < cart.grand_total)"
                class="flex-2 flex-1 py-3 bg-green-600 text-white rounded-xl font-bold
                           hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed
                           flex items-center justify-center gap-2">
                <span x-show="processingPayment" class="animate-spin">⚙️</span>
                <span x-show="!processingPayment">✅ Complete Sale</span>
            </button>
        </div>
    </div>
</div>
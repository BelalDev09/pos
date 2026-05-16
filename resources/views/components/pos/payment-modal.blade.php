{{-- File: resources/views/components/pos/payment-modal.blade.php --}}

<div
    x-show="paymentModal"
    x-transition
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4"
    @keydown.escape.window="paymentModal = false"
>

    <div
        @click.stop
        class="w-full max-w-lg bg-white rounded-3xl shadow-2xl overflow-hidden"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
    >

        {{-- Header --}}
        <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">
                    <span x-text="
                        paymentMethod === 'cash' ? 'Cash Payment' :
                        paymentMethod === 'card' ? 'Card Payment' :
                        'Split Payment'
                    "></span>
                </h2>
                <p class="text-sm text-slate-400">Complete transaction securely</p>
            </div>

            <button
                @click="paymentModal = false"
                class="w-10 h-10 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-500"
            >
                <i class="ri-close-line text-xl"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-6 space-y-5">

            {{-- Amount Due --}}
            <div class="rounded-3xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-6 text-center">
                <p class="text-sm opacity-80">Amount Due</p>
                <p class="text-4xl font-extrabold mt-1" x-text="formatMoney(cart.grand_total)"></p>
            </div>

            {{-- CASH --}}
            <div x-show="paymentMethod === 'cash'" class="space-y-4">

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Amount Tendered
                    </label>

                    <input
                        type="number"
                        x-model="amountTendered"
                        @input="calculateChange()"
                        class="w-full px-4 py-4 text-center text-2xl font-bold border-2 border-slate-300 rounded-2xl focus:border-blue-500 focus:outline-none"
                        step="0.01"
                        min="0"
                    >
                </div>

                {{-- Quick Cash --}}
                <div class="grid grid-cols-4 gap-2">
                    <template x-for="amount in quickCashAmounts">
                        <button
                            @click="amountTendered = amount; calculateChange()"
                            class="py-3 rounded-2xl border border-slate-200 hover:bg-slate-50 text-sm font-semibold"
                            x-text="formatMoney(amount)"
                        ></button>
                    </template>
                </div>

                {{-- Change --}}
                <div
                    x-show="changeAmount >= 0"
                    class="rounded-2xl bg-green-50 border border-green-200 px-5 py-4 flex justify-between items-center"
                >
                    <span class="text-green-700 font-semibold">Change</span>
                    <span class="text-2xl font-bold text-green-700"
                          x-text="formatMoney(changeAmount)">
                    </span>
                </div>

            </div>

            {{-- CARD --}}
            <div x-show="paymentMethod === 'card'" class="space-y-4">

                <div class="rounded-2xl bg-slate-50 p-5 text-center border border-slate-200">
                    <i class="ri-bank-card-line text-4xl text-blue-500"></i>
                    <p class="mt-2 text-sm text-slate-600">
                        Charge customer card for
                    </p>
                    <p class="text-xl font-bold text-slate-900"
                       x-text="formatMoney(cart.grand_total)">
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Transaction Reference
                    </label>

                    <input
                        type="text"
                        x-model="cardReference"
                        placeholder="Authorization code / reference"
                        class="w-full px-4 py-3 border border-slate-300 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    >
                </div>

            </div>

        </div>

        {{-- Footer --}}
        <div class="px-6 pb-6 flex gap-3">

            <button
                @click="paymentModal = false"
                class="flex-1 py-4 rounded-2xl border border-slate-300 font-semibold text-slate-700 hover:bg-slate-50"
            >
                Cancel
            </button>

            <button
                @click="processPayment()"
                :disabled="processingPayment || (paymentMethod === 'cash' && amountTendered < cart.grand_total)"
                class="flex-1 py-4 rounded-2xl bg-green-600 hover:bg-green-700 text-white font-bold
                       disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
                <span x-show="processingPayment" class="animate-spin">
                    <i class="ri-loader-4-line"></i>
                </span>

                <span x-show="!processingPayment">
                    Complete Sale
                </span>
            </button>

        </div>
    </div>
</div>

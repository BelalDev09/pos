{{-- File: resources/views/components/pos/numpad.blade.php --}}

<div
    x-show="showNumpad"
    x-transition
    class="fixed bottom-6 right-6 z-40 w-80 bg-white rounded-3xl shadow-2xl border border-slate-200 p-4"
>

    <div class="grid grid-cols-3 gap-3">

        <template x-for="key in ['7','8','9','4','5','6','1','2','3','.','0','⌫']">

            <button
                @click="pressNumpad(key)"
                class="h-16 rounded-2xl bg-slate-100 hover:bg-slate-200
                       text-lg font-bold text-slate-800 transition"
                x-text="key"
            ></button>

        </template>

    </div>

    <button
        @click="confirmNumpad()"
        class="w-full mt-4 py-4 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold"
    >
        Confirm
    </button>
</div>

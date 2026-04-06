<x-app-layout>
    <div class="max-w-4xl mx-auto py-6 px-4">

        {{-- Restaurant Header --}}
        <div class="text-center mb-8">
            {{-- <h1 class="text-3xl font-bold">{{ $table->restaurant->name }}</h1> --}}
            <p class="text-gray-500 mt-1">Table: {{ $table->table_number }}</p>
        </div>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-6 text-center">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('table.order', $table->qr_token) }}">
            @csrf

            {{-- Group by Category --}}
            @php
                $grouped = $table->menuItems
                    ->where('is_available', true)
                    ->groupBy(fn($item) => $item->category->name ?? 'Others');
            @endphp

            @foreach ($grouped as $categoryName => $items)
                {{-- Category Title --}}
                <h2 class="text-xl font-bold text-gray-700 mb-3 mt-6 border-b pb-2">
                    {{ $categoryName }}
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach ($items as $item)
                        <div class="border rounded-lg p-4 shadow-sm hover:shadow-md transition">

                            {{-- Item Image --}}
                            @if ($item->image_url)
                                <img src="{{ asset($item->image_url) }}" class="w-full h-36 object-cover rounded mb-3">
                            @endif

                            {{-- Name & Description --}}
                            <h4 class="font-semibold text-gray-800">{{ $item->name }}</h4>
                            <p class="text-sm text-gray-500 mb-2">{{ $item->description }}</p>

                            {{-- Price --}}
                            <p class="text-green-600 font-bold mb-3">৳ {{ $item->base_price }}</p>

                            {{-- Hidden inputs --}}
                            <input type="hidden" name="items[{{ $item->id }}][menu_item_id]"
                                value="{{ $item->id }}">
                            <input type="hidden" name="items[{{ $item->id }}][unit_price]"
                                value="{{ $item->base_price }}">

                            {{-- Quantity --}}
                            <div class="flex items-center gap-2">
                                <label class="text-sm font-medium text-gray-700">Qty:</label>
                                <input type="number" name="items[{{ $item->id }}][quantity]" value="0"
                                    min="0"
                                    class="w-20 border rounded p-1 text-center focus:ring-2 focus:ring-green-400">
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach

            {{-- Place Order Button --}}
            <div class="mt-8 text-center">
                <button type="submit"
                    class="bg-green-600 text-white px-8 py-3 rounded-lg font-semibold
                       hover:bg-green-700 transition text-lg">
                    🛒 Place Order
                </button>
            </div>

        </form>
    </div>
</x-app-layout>

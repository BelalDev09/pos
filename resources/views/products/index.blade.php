{{-- File: resources/views/products/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="p-6" x-data="productManager()">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Products</h1>
            <p class="text-sm text-gray-500">{{ $products->total() }} products total</p>
        </div>
        <div class="flex gap-3">
            @can('products.create')
            <button @click="openCreateModal()"
                class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700">
                + New Product
            </button>
            @endcan
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-5">
        <form method="GET" class="flex flex-wrap gap-3 items-center">
            <input type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search name, SKU, barcode..."
                class="flex-1 min-w-48 px-3 py-2 text-sm border border-gray-300 rounded-lg">

            <select name="category_id" class="px-3 py-2 text-sm border border-gray-300 rounded-lg">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
                @endforeach
            </select>

            <select name="brand_id" class="px-3 py-2 text-sm border border-gray-300 rounded-lg">
                <option value="">All Brands</option>
                @foreach($brands as $brand)
                <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                    {{ $brand->name }}
                </option>
                @endforeach
            </select>

            <select name="is_active" class="px-3 py-2 text-sm border border-gray-300 rounded-lg">
                <option value="">All Status</option>
                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-lg">Filter</button>
            <a href="{{ route('products.index') }}"
                class="px-4 py-2 border border-gray-300 text-sm rounded-lg hover:bg-gray-50">Reset</a>
        </form>
    </div>

    {{-- Products Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">SKU / Barcode</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Cost</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Price</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($products as $product)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0">
                                @if($product->image)
                                <img src="{{ asset('storage/'.$product->image) }}"
                                    class="w-full h-full object-cover">
                                @else
                                <div class="w-full h-full flex items-center justify-center text-lg">📦</div>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $product->name }}</p>
                                <p class="text-xs text-gray-400">{{ $product->unit }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-700">{{ $product->sku ?? '—' }}</p>
                        <p class="text-xs text-gray-400">{{ $product->barcode ?? '—' }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-600">{{ $product->category?->name ?? '—' }}</span>
                    </td>
                    <td class="px-6 py-4 text-right text-sm text-gray-700">
                        {{ number_format($product->cost_price, 2) }}
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-semibold text-blue-700">
                        {{ number_format($product->selling_price, 2) }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                     {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @can('products.update')
                            <button @click="editProduct({{ $product->id }})"
                                class="text-xs px-3 py-1.5 border border-blue-200 rounded-lg hover:bg-blue-50 text-blue-600">
                                Edit
                            </button>
                            @endcan
                            @can('products.delete')
                            <button @click="deleteProduct({{ $product->id }}, '{{ $product->name }}')"
                                class="text-xs px-3 py-1.5 border border-red-200 rounded-lg hover:bg-red-50 text-red-600">
                                Delete
                            </button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                        <p class="text-4xl mb-2">📦</p>
                        <p class="text-sm">No products found.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($products->hasPages())
        <div class="px-6 py-4 border-t">{{ $products->withQueryString()->links() }}</div>
        @endif
    </div>

    {{-- Product Modal --}}
    @include('products._modal')
</div>

@push('scripts')
<script type="module" src="{{ asset('js/products/product-crud.js') }}"></script>
@endpush
@endsection
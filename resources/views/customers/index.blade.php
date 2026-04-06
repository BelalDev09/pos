{{-- File: resources/views/customers/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="p-6" x-data="customerManager()">

    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Customers</h1>
            <p class="text-sm text-gray-500 mt-1">
                {{ $customers->total() }} total customers
            </p>
        </div>
        @can('customers.create')
        <button @click="openCreateModal()"
            class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white
                       rounded-xl hover:bg-blue-700 transition-colors font-medium">
            <span>+</span> New Customer
        </button>
        @endcan
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-5">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search name, phone, email..."
                class="flex-1 min-w-48 px-3 py-2 text-sm border border-gray-300 rounded-lg">

            <select name="tier"
                class="px-3 py-2 text-sm border border-gray-300 rounded-lg">
                <option value="">All Tiers</option>
                @foreach(['standard','silver','gold','platinum'] as $tier)
                <option value="{{ $tier }}" {{ request('tier') === $tier ? 'selected' : '' }}>
                    {{ ucfirst($tier) }}
                </option>
                @endforeach
            </select>

            <select name="is_active"
                class="px-3 py-2 text-sm border border-gray-300 rounded-lg">
                <option value="">All Status</option>
                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
            </select>

            <button type="submit"
                class="px-4 py-2 bg-gray-800 text-white text-sm rounded-lg hover:bg-gray-700">
                Filter
            </button>
            <a href="{{ route('customers.index') }}"
                class="px-4 py-2 border border-gray-300 text-sm rounded-lg hover:bg-gray-50">
                Reset
            </a>
        </form>
    </div>

    {{-- Customers Table --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tier</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Total Spent</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Points</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($customers as $customer)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center
                                        justify-center text-blue-700 font-semibold text-sm flex-shrink-0">
                                {{ strtoupper(substr($customer->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $customer->name }}</p>
                                <p class="text-xs text-gray-400">{{ $customer->orders_count }} orders</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-700">{{ $customer->phone ?? '—' }}</p>
                        <p class="text-xs text-gray-400">{{ $customer->email ?? '—' }}</p>
                    </td>
                    <td class="px-6 py-4">
                        @php
                        $tierColors = [
                        'standard' => 'bg-gray-100 text-gray-600',
                        'silver' => 'bg-slate-100 text-slate-600',
                        'gold' => 'bg-yellow-100 text-yellow-700',
                        'platinum' => 'bg-purple-100 text-purple-700',
                        ];
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                     {{ $tierColors[$customer->tier] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($customer->tier) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-semibold text-gray-800">
                        {{ number_format($customer->total_purchases, 2) }}
                    </td>
                    <td class="px-6 py-4 text-right text-sm text-blue-600 font-medium">
                        {{ number_format($customer->loyalty_points, 0) }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                     {{ $customer->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                            {{ $customer->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('customers.show', $customer) }}"
                                class="text-xs px-3 py-1.5 border border-gray-200 rounded-lg
                                      hover:bg-gray-50 text-gray-600">
                                View
                            </a>
                            @can('customers.update')
                            <button @click="editCustomer({{ $customer->id }})"
                                class="text-xs px-3 py-1.5 border border-blue-200 rounded-lg
                                           hover:bg-blue-50 text-blue-600">
                                Edit
                            </button>
                            @endcan
                            @can('customers.delete')
                            <button @click="deleteCustomer({{ $customer->id }}, '{{ $customer->name }}')"
                                class="text-xs px-3 py-1.5 border border-red-200 rounded-lg
                                           hover:bg-red-50 text-red-600">
                                Delete
                            </button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                        <p class="text-3xl mb-2">👥</p>
                        <p class="text-sm">No customers found.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if ($customers->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $customers->withQueryString()->links() }}
        </div>
        @endif
    </div>

    {{-- Create / Edit Modal --}}
    @include('customers._modal')
</div>
@endsection
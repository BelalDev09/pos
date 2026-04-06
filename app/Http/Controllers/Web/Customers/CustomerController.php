<?php

namespace App\Http\Controllers\Web\Customers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerService $customerService
    ) {}

    public function index(Request $request): View
    {
        $customers = $this->customerService->paginate(20, $request->only([
            'search',
            'tier',
            'is_active',
        ]));

        return view('customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(StoreCustomerRequest $request): JsonResponse|RedirectResponse
    {
        $customer = $this->customerService->create($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully.',
                'data'    => $customer,
            ], 201);
        }

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer created successfully.');
    }

    public function show(int $id): View
    {
        $customer = $this->customerService->findById($id);
        return view('customers.show', compact('customer'));
    }

    public function edit(int $id): View|JsonResponse
    {
        $customer = $this->customerService->findById($id);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $customer,
            ]);
        }

        return view('customers.edit', compact('customer'));
    }

    public function update(UpdateCustomerRequest $request, int $id): JsonResponse|RedirectResponse
    {
        $customer = $this->customerService->update($id, $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer updated.',
                'data'    => $customer,
            ]);
        }

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer updated.');
    }

    public function destroy(int $id): JsonResponse|RedirectResponse
    {
        $this->customerService->delete($id);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer deleted.',
            ]);
        }

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer deleted.');
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:2']);

        $customers = $this->customerService->searchForPos($request->q);

        return response()->json([
            'success' => true,
            'data'    => $customers,
        ]);
    }
}

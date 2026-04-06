<?php

namespace App\Http\Controllers\Web\Purchases;

use App\Http\Controllers\Controller;
use App\Http\Requests\Purchase\StorePurchaseOrderRequest;
use App\Http\Requests\Purchase\ReceiveGoodsRequest;
use App\Models\Store;
use App\Repositories\SupplierRepository;
use App\Repositories\ProductRepository;
use App\Services\Purchase\PurchaseOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    public function __construct(
        private readonly PurchaseOrderService $poService,
        private readonly SupplierRepository   $supplierRepository,
        private readonly ProductRepository    $productRepository
    ) {}

    public function index(Request $request): View
    {
        $purchaseOrders = $this->poService->paginate(20, $request->only([
            'search',
            'status',
            'store_id',
            'supplier_id',
        ]));

        $stores    = Store::active()->get(['id', 'name']);
        $suppliers = $this->supplierRepository->all();

        return view('purchases.index', compact('purchaseOrders', 'stores', 'suppliers'));
    }

    public function create(): View
    {
        $suppliers = $this->supplierRepository->all();
        $stores    = Store::active()->get(['id', 'name']);
        $products  = $this->productRepository->paginate(100, ['is_active' => true]);

        return view('purchases.create', compact('suppliers', 'stores', 'products'));
    }

    public function store(StorePurchaseOrderRequest $request): JsonResponse|RedirectResponse
    {
        $po = $this->poService->create(
            $request->validated(),
            auth()->id(),
            auth()->user()->tenant_id
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Purchase order {$po->po_number} created.",
                'data'    => $po,
            ], 201);
        }

        return redirect()
            ->route('purchases.show', $po->id)
            ->with('success', "Purchase order {$po->po_number} created.");
    }

    public function show(int $id): View
    {
        $po = $this->poService->findById($id);
        return view('purchases.show', compact('po'));
    }

    public function edit(int $id): View
    {
        $po        = $this->poService->findById($id);
        $suppliers = $this->supplierRepository->all();
        $stores    = Store::active()->get(['id', 'name']);

        return view('purchases.edit', compact('po', 'suppliers', 'stores'));
    }

    public function update(StorePurchaseOrderRequest $request, int $id): JsonResponse|RedirectResponse
    {
        $po = $this->poService->update($id, $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Purchase order updated.',
                'data'    => $po,
            ]);
        }

        return redirect()
            ->route('purchases.show', $id)
            ->with('success', 'Purchase order updated.');
    }

    public function approve(int $id): JsonResponse|RedirectResponse
    {
        $this->authorize('purchases.approve');
        $po = $this->poService->approve($id, auth()->id());

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "PO {$po->po_number} approved.",
                'data'    => $po,
            ]);
        }

        return back()->with('success', "Purchase order {$po->po_number} approved.");
    }

    public function receive(ReceiveGoodsRequest $request, int $id): JsonResponse|RedirectResponse
    {
        $po = $this->poService->receiveGoods(
            $id,
            $request->validated()['items'],
            auth()->id()
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Goods received and inventory updated.',
                'data'    => $po,
            ]);
        }

        return back()->with('success', 'Goods received and inventory updated.');
    }

    public function destroy(int $id): JsonResponse|RedirectResponse
    {
        $this->poService->delete($id);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'PO deleted.']);
        }

        return redirect()
            ->route('purchases.index')
            ->with('success', 'Purchase order deleted.');
    }
}

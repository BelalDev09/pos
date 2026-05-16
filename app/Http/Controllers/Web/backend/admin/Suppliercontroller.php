<?php

namespace App\Http\Controllers\Web\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class SupplierController extends Controller
{
    /* =========================
        LIST (VELZON STYLE)
    ========================= */
    public function index(Request $request)
    {
        $suppliers = $this->query($request)
            ->paginate($request->input('per_page', 25))
            ->withQueryString();

        $totals = $this->totals();

        return view('backend.layout.admin.suppliers.index', compact('suppliers', 'totals'));
    }

    /* =========================
        CREATE
    ========================= */
    public function create()
    {
        return view('backend.layout.admin.suppliers.create');
    }

    /* =========================
        STORE
    ========================= */
    public function store(Request $request)
    {
        $data = $this->validated($request);

        $tenantId = auth()->user()?->tenant_id
            ?? $request->attributes->get('tenant_id')
            ?? config('app.tenant_id')
            ?? (app()->bound('tenant') ? app('tenant')->id : null);

        if (!$tenantId) {
            abort(403, 'Tenant missing');
        }

        $data['tenant_id'] = $tenantId;
        $data['contact_id'] = $this->generateContactId();

        $supplier = Supplier::create($data);

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', "Supplier {$supplier->business_name} created successfully.");
    }

    /* =========================
        SHOW
    ========================= */
    public function show(Supplier $supplier)
    {
        $supplier->load('purchaseOrders');

        return view('backend.layout.admin.suppliers.show', compact('supplier'));
    }

    /* =========================
        EDIT
    ========================= */
    public function edit(Supplier $supplier)
    {
        return view('backend.layout.admin.suppliers.edit', compact('supplier'));
    }

    /* =========================
        UPDATE
    ========================= */
    public function update(Request $request, Supplier $supplier)
    {
        $supplier->update($this->validated($request));

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', "Supplier {$supplier->business_name} updated.");
    }

    /* =========================
        DELETE
    ========================= */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return back()->with('success', 'Supplier deleted successfully.');
    }

    /* =========================
        TOGGLE STATUS (VELZON ACTION)
    ========================= */
    public function toggleStatus(Supplier $supplier)
    {
        $supplier->update([
            'is_active' => !$supplier->is_active
        ]);

        return back()->with('success', 'Supplier status updated.');
    }

    /* =========================
        EXPORT CSV
    ========================= */
    public function exportCsv(Request $request)
    {
        $suppliers = $this->query($request)->get();

        $filename = "suppliers_" . now()->format('Ymd_His') . ".csv";

        return response()->stream(function () use ($suppliers) {

            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID',
                'Business',
                'Name',
                'Email',
                'Phone',
                'Pay Term',
                'Opening',
                'Advance',
                'Status'
            ]);

            foreach ($suppliers as $s) {
                fputcsv($handle, [
                    $s->contact_id,
                    $s->business_name,
                    $s->name,
                    $s->email,
                    $s->phone,
                    $s->payment_terms ? $s->payment_terms . ' days' : '-',
                    $s->opening_balance,
                    $s->advance_balance,
                    $s->is_active ? 'Active' : 'Inactive',
                ]);
            }

            fclose($handle);
        }, 200, [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
        ]);
    }

    /* =========================
        EXPORT PDF (VELZON REPORT STYLE)
    ========================= */
    public function exportPdf(Request $request)
    {
        $suppliers = $this->query($request)->get();

        $pdf = Pdf::loadView(
            'backend.layout.admin.suppliers.pdf',
            compact('suppliers')
        )->setPaper('a4', 'landscape');

        return $pdf->download('suppliers_' . now()->format('Ymd') . '.pdf');
    }

    /* =========================
        QUERY BUILDER (CLEAN FILTER)
    ========================= */
    private function query(Request $request)
    {
        return Supplier::query()
            ->when($request->search, function ($q, $s) {
                $q->where(function ($q) use ($s) {
                    $q->where('business_name', 'like', "%$s%")
                        ->orWhere('name', 'like', "%$s%")
                        ->orWhere('contact_id', 'like', "%$s%")
                        ->orWhere('email', 'like', "%$s%");
                });
            })
            ->when($request->status, function ($q, $status) {
                $q->where('is_active', $status === 'active');
            })
            ->orderByDesc('id');
    }

    /* =========================
        TOTALS (DASHBOARD WIDGETS)
    ========================= */
    private function totals()
    {
        return Supplier::selectRaw('
                COUNT(*) as total_count,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_count,
                SUM(total_purchase_due) as total_purchase_due,
                SUM(total_return_due) as total_return_due,
                SUM(advance_balance) as total_advance
            ')
            ->first();
    }

    /* =========================
        VALIDATION (REUSABLE)
    ========================= */
    private function validated(Request $request): array
    {
        return $request->validate([
            'business_name'   => 'required|string|max:255',
            'name'            => 'required|string|max:255',
            'email'           => 'nullable|email|max:255',
            'phone'           => 'nullable|string|max:30',
            'tax_number'      => 'nullable|string|max:50',
            'payment_terms'   => 'nullable|integer|min:0',
            'opening_balance' => 'nullable|numeric|min:0',
            'advance_balance' => 'nullable|numeric|min:0',
            'credit_limit'    => 'nullable|numeric|min:0',
            'address'         => 'nullable|string|max:500',
            'city'            => 'nullable|string|max:100',
            'state'           => 'nullable|string|max:100',
            'country'         => 'nullable|string|max:100',
            'postal_code'     => 'nullable|string|max:20',
            'notes'           => 'nullable|string',
            'is_active'       => 'boolean',
        ]);
    }

    /* =========================
        CONTACT ID GENERATOR
    ========================= */
    private function generateContactId(): string
    {
        $last = Supplier::orderByDesc('id')
            ->value('contact_id');

        $num = $last ? (int) substr($last, 2) + 1 : 1;

        return 'CO' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}

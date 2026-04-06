<?php

namespace App\Http\Controllers\Web\backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CustomerCreateRequest;
use App\Http\Requests\Customer\CustomerUpdateRequest;
use App\Models\Customer;
use App\Models\Restaurant;
use App\Traits\AuthorizesRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class CustomerController extends Controller
{
    use AuthorizesRequest;

    protected $customer;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    // DataTable AJAX endpoint
    public function index(Request $request)
    {
        // Authorize: Only admin can view customers
        $this->authorizeAdmin('You do not have permission to view customers');

        if ($request->ajax()) {
            $customers = $this->customer->latest();

            return DataTables::of($customers)
                // ->addColumn('restaurant', fn($c) => $c->restaurant?->name ?? '-')
                ->addColumn('action', function ($c) {
                    $edit = '<a href="' . route('customer.edit', $c->id) . '" class="btn btn-sm btn-primary">Edit</a>';
                    $delete = '<button data-id="' . $c->id . '" class="btn btn-sm btn-danger delete-customer">Delete</button>';
                    return $edit . ' ' . $delete;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('backend.layout.customers.index');
    }

    // Create form
    public function create()
    {
        // Authorize: Only admin can create customers
        $this->authorizeAdmin('You do not have permission to create customers');

        // $restaurants = Restaurant::all();
        $customers = Customer::all();
        return view('backend.layout.customers.form', compact('customers'));
    }

    // Store new customer
    public function store(CustomerCreateRequest $request)
    {
        // Authorize: Only admin can store customers
        $this->authorizeAdmin('You do not have permission to store customers');

        try {
            $customer = new $this->customer();
            // $customer->restaurant_id = $request->restaurant_id;
            $customer->name = $request->name;
            $customer->phone = $request->phone;
            $customer->email = $request->email;
            $customer->address = $request->address;
            $customer->loyalty_points = $request->loyalty_points ?? 0;
            $customer->save();

            flash()->success('Customer created successfully.');
            return redirect()->route('customer.index');
        } catch (Exception $e) {
            flash()->error('Something went wrong! Please try again.');
            return redirect()->back()->withInput();
        }
    }

    // Edit form
    public function edit($id)
    {
        // Authorize: Only admin can edit customers
        $this->authorizeAdmin('You do not have permission to edit customers');

        $customer = $this->customer->findOrFail($id);
        // $restaurants = Restaurant::all();
        return view('backend.layout.customers.form', compact('customer', 'restaurants'));
    }

    // Update customer
    public function update(CustomerUpdateRequest $request, $id)
    {
        // Authorize: Only admin can update customers
        $this->authorizeAdmin('You do not have permission to update customers');

        try {
            $customer = $this->customer->findOrFail($id);
            // $customer->restaurant_id = $request->restaurant_id;
            $customer->name = $request->name;
            $customer->phone = $request->phone;
            $customer->email = $request->email;
            $customer->address = $request->address;
            $customer->loyalty_points = $request->loyalty_points ?? 0;
            $customer->save();

            flash()->success('Customer updated successfully.');
            return redirect()->route('customer.index');
        } catch (Exception $e) {
            flash()->error('Something went wrong! Please try again.');
            return redirect()->back()->withInput();
        }
    }

    // Delete customer
    public function destroy($id)
    {
        // Authorize: Only admin can delete customers
        $this->authorizeAdmin('You do not have permission to delete customers');

        try {
            $customer = $this->customer->findOrFail($id);
            $customer->delete();
            return response()->json(['success' => true, 'message' => 'Customer deleted successfully.']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong!']);
        }
    }
}

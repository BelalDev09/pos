
<!-- ================= CUSTOMERS INDEX (PRO LEVEL) ================= -->

@extends('layouts.app')

@section('title','Customers')

@section('content')

<div class="card">

    <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0">Customers</h5>
        <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">
            <i class="ri-add-line"></i> Add Customer
        </a>
    </div>

    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th width="120">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($customers as $customer)
                    <tr>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->phone }}</td>
                        <td>
                            <a href="{{ route('customers.edit',$customer->id) }}" class="btn btn-sm btn-soft-warning">
                                Edit
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

    </div>

</div>

@endsection
<?php

namespace App\Http\Controllers\Api\V1\Orders;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreInventoryRequest;

class OrderController extends Controller
{
    public function store(StoreInventoryRequest $request)
    {
        $validated = $request->validated();
    }
}

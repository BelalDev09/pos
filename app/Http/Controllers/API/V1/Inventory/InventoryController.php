<?php

namespace App\Http\Controllers\Api\V1\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreInventoryRequest;

class InventoryController extends Controller
{
    public function store(StoreInventoryRequest $request)
    {
        $validated = $request->validated();
    }
}

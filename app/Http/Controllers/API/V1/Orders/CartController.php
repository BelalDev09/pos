<?php 

namespace App\Http\Controllers\Api\V1\Orders;

use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\Cart\AddToCartRequest;

class CartController extends Controller
{
    public function addToCart(AddToCartRequest $request)
    {
        $validated = $request->validated();
    }
}
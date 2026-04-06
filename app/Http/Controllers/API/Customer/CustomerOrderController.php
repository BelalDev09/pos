<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\MenuItemVariant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\RestaurantTable;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerOrderController extends Controller
{
    use ApiResponse;


    //  Place Order
    public function placeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'restaurant_id'         => 'required|exists:restaurants,id',
            'table_id'              => 'required_if:order_type,eat_in,qr_self|nullable|exists:restaurant_tables,id',
            'order_type'            => 'required|in:eat_in,collection,delivery,qr_self',
            'customer_name'         => 'required|string|max:100',
            'phone'                 => 'required|string|max:20',
            'order_notes'           => 'nullable|string|max:500',
            'payment_method'        => 'nullable|in:cash,card,mobile_banking',
            'items'                 => 'required|array|min:1',
            'items.*.menu_item_id'  => 'required|exists:menu_items,id',
            'items.*.quantity'      => 'required|integer|min:1',
            'items.*.variant_id'    => 'nullable|exists:menu_item_variants,id',
            'items.*.notes'         => 'nullable|string|max:255',
            'items.*.addons'        => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 422);
        }

        //  Guard: Table occupied check
        if ($request->table_id) {
            $table = RestaurantTable::findOrFail($request->table_id);
            if ($table->status === 'occupied') {
                return $this->error('Table is currently occupied', 409);
            }
            if (!$table->is_qr_active) {
                return $this->error('QR code is not active', 403);
            }
        }

        //  Calculate prices from DB
        $subTotal  = 0;
        $itemsData = [];

        foreach ($request->items as $item) {
            $menuItem = MenuItem::find($item['menu_item_id']);

            // Item available check
            if (!$menuItem || !$menuItem->is_available) {
                return $this->error('Item unavailable: ' . ($menuItem->name ?? $item['menu_item_id']), 400);
            }

            // Price: variant
            if (!empty($item['variant_id'])) {
                $variant = MenuItemVariant::find($item['variant_id']);
                $price   = $variant
                    ? $menuItem->base_price + $variant->price_adjustment
                    : $menuItem->base_price;
            } else {
                $price = $menuItem->base_price;
            }

            $subTotal    += $price * $item['quantity'];
            $itemsData[]  = [
                'item'  => $item,
                'price' => $price,
                'name'  => $menuItem->name,
            ];
        }

        //  Tax & Grand Total
        $taxAmount     = round($subTotal * 0.05, 2);   // VAT 5%
        $serviceCharge = round($subTotal * 0.05, 2);   // Service 5%
        $grandTotal    = $subTotal + $taxAmount + $serviceCharge;

        //  DB Transaction
        DB::beginTransaction();
        try {

            //  Order create
            $order = Order::create([
                // 'restaurant_id'  => $request->restaurant_id,
                'order_number'   => 'ORD' . now()->format('YmdHis') . rand(10, 99),
                'table_id'       => $request->table_id,
                'order_type'     => $request->order_type,
                'status'         => 'pending',
                'kitchen_status' => 'pending',
                'payment_status' => 'pending',
                'tax_amount'     => $taxAmount,
                'service_charge' => $serviceCharge,
                'grand_total'    => $grandTotal,
            ]);

            // Order items create
            foreach ($itemsData as $d) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'menu_item_id' => $d['item']['menu_item_id'],
                    'variant_id'   => $d['item']['variant_id'] ?? null,
                    'quantity'     => $d['item']['quantity'],
                    'unit_price'   => $d['price'],
                    'notes'        => $d['item']['notes'] ?? null,
                    'addon_details' => isset($d['item']['addons'])
                        ? json_encode($d['item']['addons'])
                        : null,
                ]);
            }

            //  Table status = occupied
            if ($request->table_id) {
                RestaurantTable::where('id', $request->table_id)
                    ->update(['status' => 'occupied']);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }

        return $this->success([
            'order_id'      => $order->id,
            'order_number'  => $order->order_number,
            'status'        => $order->status,
            'sub_total'     => $subTotal,
            'tax_amount'    => $taxAmount,
            'service_charge' => $serviceCharge,
            'grand_total'   => $grandTotal,
        ], 'Order placed successfully', 201);
    }
}

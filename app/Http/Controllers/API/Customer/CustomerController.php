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

class CustomerController extends Controller
{
    use ApiResponse;

    //  QR Scan
    public function scanQr($token)
    {
        $table = RestaurantTable::with('restaurant')
            ->where('qr_token', $token)
            ->first();

        // QR valid
        if (!$table) {
            return $this->error('Invalid QR code', 404);
        }

        // QR active
        if (!$table->is_qr_active) {
            return $this->error('This QR code is no longer active', 403);
        }

        return $this->success([
            'table_id'        => $table->id,
            'table_number'    => $table->table_number,
            'capacity'        => $table->capacity,
            'status'          => $table->status,
            // 'restaurant_id'   => $table->restaurant_id,
            // 'restaurant_name' => $table->restaurant->name,
            // 'restaurant_logo' => $table->restaurant->logo_url ?? null,
            // 'currency'        => $table->restaurant->currency_code ?? 'BDT',
        ], 'Table info fetched');
    }

    //  Menu Categories
    public function getCategories(Request $request)
    {

        $categories = Category::orderBy('id', 'asc')
            ->get(['id', 'name', 'slug']);

        if (!$categories) {
            return $this->error('Categories not found or unavailable', 404);
        }

        return $this->success($categories, 'Categories fetched');
    }


    // Menu Items List
    public function getMenuItems(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id'   => 'nullable|exists:categories,id',
            'search'        => 'nullable|string|max:100',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors(), 422);
        }
        $items = MenuItem::with(['category', 'variants', 'addons'])
            ->where('is_available', true)
            // category filter
            ->when($request->category_id, fn($q, $v) =>
            $q->where('category_id', $v))
            // search bar
            ->when($request->search, fn($q, $v) =>
            $q->where('name', 'like', "%{$v}%"))
            ->orderBy('name')
            ->get([
                'id',
                'category_id',
                'name',
                'description',
                'base_price',
                'images',
                'preparation_time_minutes',
            ]);
        // dd($items->category_id);
        if (!$items) {
            return $this->error('Item not found or unavailable', 404);
        }

        return $this->success($items, 'Menu items fetched');
    }

    // Item Detail
    public function getItemDetail($id)
    {
        $item = MenuItem::with(['variants', 'addons', 'category'])
            ->where('is_available', true)
            ->find($id);

        if (!$item) {
            return $this->error('Item not found or unavailable', 404);
        }

        return $this->success($item, 'Item detail fetched');
    }


    //  Get Order Detail

    public function getOrder($id)
    {
        $order = Order::with([
            'items.menuItem',
            'items.variant',
            'table',
        ])->find($id);

        if (!$order) {
            return $this->error('Order not found', 404);
        }

        return $this->success($order, 'Order fetched');
    }

    // Track Order
    public function trackOrder($id)
    {
        $order = Order::with('items.menuItem')->find($id);

        if (!$order) {
            return $this->error('Order not found', 404);
        }

        // Figma timeline: 4 steps
        $timeline = [
            [
                'step'   => 'Order Received',
                'label'  => 'Your order has been received',
                'done'   => true,
                'active' => $order->status === 'pending',
                'time'   => $order->created_at->format('h:i A'),
            ],
            [
                'step'   => 'Approved by Waiter',
                'label'  => 'Your order has been approved',
                'done'   => in_array($order->status, ['preparing', 'ready', 'served', 'completed']),
                'active' => $order->status === 'preparing',
                'time'   => null,
            ],
            [
                'step'   => 'Kitchen Preparing',
                'label'  => 'Your order is being prepared',
                'done'   => in_array($order->kitchen_status, ['in_progress', 'completed']),
                'active' => $order->kitchen_status === 'in_progress',
                'time'   => null,
            ],
            [
                'step'   => 'Ready to Serve',
                'label'  => 'Your order is ready',
                'done'   => $order->kitchen_status === 'completed',
                'active' => $order->status === 'ready',
                'time'   => $order->served_at?->format('h:i A'),
            ],
        ];

        return $this->success([
            'order_id'       => $order->id,
            'order_number'   => $order->order_number,
            'status'         => $order->status,
            'kitchen_status' => $order->kitchen_status,
            'payment_status' => $order->payment_status,
            'grand_total'    => $order->grand_total,
            'timeline'       => $timeline,
            'items'          => $order->items->map(fn($i) => [
                'name'       => $i->menuItem->name ?? '',
                'quantity'   => $i->quantity,
                'unit_price' => $i->unit_price,
                'total'      => $i->unit_price * $i->quantity,
                'status'     => $order->kitchen_status,
            ]),
        ], 'Order tracking fetched');
    }
}

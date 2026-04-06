{{-- File: resources/views/receipts/pdf.blade.php --}}

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            width: 226px;
            padding: 10px 5px;
            color: #000;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .separator {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
        }

        .grand-total {
            font-size: 14px;
            font-weight: bold;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 4px 0;
        }

        .footer {
            margin-top: 10px;
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>

<body>

    {{-- Store Header --}}
    <div class="center bold" style="font-size: 13px;">{{ $store->name }}</div>
    @if($store->address)
    <div class="center">{{ $store->address }}</div>
    @endif
    @if($store->phone)
    <div class="center">{{ $store->phone }}</div>
    @endif

    <div class="separator"></div>

    {{-- Order Info --}}
    <div><strong>Order:</strong> {{ $order->order_number }}</div>
    <div><strong>Date:</strong> {{ $order->completed_at?->format('d/m/Y H:i') }}</div>
    <div><strong>Staff:</strong> {{ $order->cashier?->name }}</div>
    @if($order->customer && !$order->customer->is_walk_in)
    <div><strong>Customer:</strong> {{ $order->customer->name }}</div>
    @endif

    <div class="separator"></div>

    {{-- Items --}}
    @foreach($order->items as $item)
    <div style="margin: 3px 0;">
        <div class="bold">{{ $item->product_name }}
            @if($item->variant_name) - {{ $item->variant_name }} @endif
        </div>
        <div class="item-row">
            <span>{{ number_format($item->quantity, 0) }} x {{ number_format($item->unit_price, 2) }}</span>
            <span>{{ number_format($item->total, 2) }}</span>
        </div>
        @if($item->discount_amount > 0)
        <div class="item-row" style="color: #555;">
            <span>Discount</span>
            <span>-{{ number_format($item->discount_amount, 2) }}</span>
        </div>
        @endif
    </div>
    @endforeach

    <div class="separator"></div>

    {{-- Totals --}}
    <div class="item-row">
        <span>Subtotal</span>
        <span>{{ number_format($order->subtotal, 2) }}</span>
    </div>

    @if($order->discount_amount > 0)
    <div class="item-row" style="color:#333;">
        <span>Discount</span>
        <span>-{{ number_format($order->discount_amount, 2) }}</span>
    </div>
    @endif

    <div class="item-row">
        <span>Tax</span>
        <span>{{ number_format($order->tax_amount, 2) }}</span>
    </div>

    <div class="separator"></div>

    <div class="item-row grand-total">
        <span>TOTAL</span>
        <span>{{ number_format($order->total, 2) }} {{ $store->currency }}</span>
    </div>

    <div class="separator"></div>

    {{-- Payments --}}
    @foreach($order->payments as $payment)
    <div class="item-row">
        <span>{{ ucfirst($payment->method) }}</span>
        <span>{{ number_format($payment->amount, 2) }}</span>
    </div>
    @endforeach

    @if($order->change_given > 0)
    <div class="item-row">
        <span>Change</span>
        <span>{{ number_format($order->change_given, 2) }}</span>
    </div>
    @endif

    @if($order->loyalty_points_earned > 0)
    <div class="separator"></div>
    <div class="center" style="font-size: 10px;">
        You earned {{ number_format($order->loyalty_points_earned, 0) }} loyalty points!
    </div>
    @endif

    {{-- Footer --}}
    <div class="separator"></div>
    <div class="footer">
        {{ $store->receipt_settings['footer'] ?? 'Thank you for shopping with us!' }}
    </div>
    <div class="footer" style="margin-top: 4px;">
        Powered by {{ config('app.name') }}
    </div>

</body>

</html>
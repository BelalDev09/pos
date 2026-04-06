<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\Pos\ReceiptService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessReceipt implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int    $tries   = 3;
    public int    $timeout = 30;
    public string $queue   = 'receipts';

    public function __construct(
        private readonly int    $orderId,
        private readonly string $channel = 'email'  // email|print|both
    ) {}

    public function handle(ReceiptService $receiptService): void
    {
        $order = Order::with(['items.product', 'customer', 'cashier', 'store', 'payments'])
            ->findOrFail($this->orderId);

        match ($this->channel) {
            'email'  => $receiptService->sendByEmail($order),
            'print'  => $receiptService->sendToPrinter($order),
            'both'   => $receiptService->sendByEmail($order) && $receiptService->sendToPrinter($order),
            default  => null,
        };
    }
}

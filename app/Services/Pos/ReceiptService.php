<?php

namespace App\Services\Pos;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ReceiptService
{
    /**
     * Generate a PDF receipt and return the file path.
     */
    public function generatePdf(Order $order): string
    {
        $order->loadMissing(['items.product', 'customer', 'cashier', 'store', 'payments']);

        $pdf  = Pdf::loadView('receipts.pdf', [
            'order'   => $order,
            'store'   => $order->store,
            'tenant'  => $order->store->tenant,
        ])
            ->setPaper([0, 0, 226.77, 841.89]) // 80mm thermal receipt width
            ->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
            ]);

        $path = "receipts/{$order->tenant_id}/{$order->order_number}.pdf";
        Storage::disk('local')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Send receipt to customer email.
     */
    public function sendByEmail(Order $order): void
    {
        if (!$order->customer?->email) {
            return;
        }

        $pdfPath = $this->generatePdf($order);

        Mail::send(
            'emails.receipt',
            ['order' => $order],
            function ($message) use ($order, $pdfPath) {
                $message->to($order->customer->email, $order->customer->name)
                    ->subject("Receipt #{$order->order_number} - {$order->store->name}")
                    ->attach(Storage::disk('local')->path($pdfPath), [
                        'as'   => "receipt-{$order->order_number}.pdf",
                        'mime' => 'application/pdf',
                    ]);
            }
        );
    }

    /**
     * Send ESC/POS commands to thermal printer.
     */
    public function sendToPrinter(Order $order): void
    {
        // Build ESC/POS receipt data
        $receiptData = $this->buildEscPosReceipt($order);

        // Send to printer via configured printer endpoint
        $printerUrl = $order->register?->printer_settings['url'] ?? null;

        if (!$printerUrl) {
            \Log::warning("No printer configured for register #{$order->register_id}");
            return;
        }

        try {
            \Http::post($printerUrl, ['data' => base64_encode($receiptData)]);
        } catch (\Exception $e) {
            \Log::error("Print failed for order {$order->order_number}: {$e->getMessage()}");
        }
    }

    /**
     * Generate plain-text receipt for thermal printer.
     */
    public function buildTextReceipt(Order $order): string
    {
        $store     = $order->store;
        $separator = str_repeat('-', 42);
        $lines     = [];

        // Header
        $lines[] = $this->center($store->name, 42);
        if ($store->address) {
            $lines[] = $this->center($store->address, 42);
        }
        if ($store->phone) {
            $lines[] = $this->center($store->phone, 42);
        }

        $lines[] = $separator;
        $lines[] = "Order: {$order->order_number}";
        $lines[] = "Date:  " . $order->completed_at?->format('d/m/Y H:i');
        $lines[] = "Staff: " . $order->cashier?->name;
        if ($order->customer && !$order->customer->is_walk_in) {
            $lines[] = "Cust:  {$order->customer->name}";
        }
        $lines[] = $separator;

        // Items
        foreach ($order->items as $item) {
            $qty  = number_format($item->quantity, 0);
            $name = substr($item->product_name, 0, 22);
            $price = number_format($item->unit_price, 2);
            $total = number_format($item->total, 2);

            $lines[] = sprintf("%-22s %3s x %7s", $name, $qty, $price);
            $lines[] = sprintf("%-33s %8s", '', $total);
        }

        $lines[] = $separator;
        $lines[] = sprintf("%-22s %18s", 'Subtotal', number_format($order->subtotal, 2));

        if ($order->discount_amount > 0) {
            $lines[] = sprintf("%-22s %18s", 'Discount', '-' . number_format($order->discount_amount, 2));
        }

        $lines[] = sprintf("%-22s %18s", 'Tax', number_format($order->tax_amount, 2));
        $lines[] = $separator;
        $lines[] = sprintf("%-22s %18s", 'TOTAL', number_format($order->total, 2));
        $lines[] = $separator;

        foreach ($order->payments as $payment) {
            $lines[] = sprintf("%-22s %18s", ucfirst($payment->method), number_format($payment->amount, 2));
        }

        if ($order->change_given > 0) {
            $lines[] = sprintf("%-22s %18s", 'Change', number_format($order->change_given, 2));
        }

        $lines[] = $separator;

        // Footer
        $footer = $store->receipt_settings['footer']
            ?? 'Thank you for your purchase!';
        $lines[] = $this->center($footer, 42);
        $lines[] = "\n\n\n"; // Paper cut space

        return implode("\n", $lines);
    }

    private function center(string $text, int $width): string
    {
        $len     = strlen($text);
        $padding = max(0, intval(($width - $len) / 2));
        return str_repeat(' ', $padding) . $text;
    }

    private function buildEscPosReceipt(Order $order): string
    {
        $text = $this->buildTextReceipt($order);

        // ESC/POS basic commands
        $esc   = "\x1B";
        $init  = $esc . "@";       // Initialize printer
        $cut   = $esc . "d" . "\x04" . "\x1D" . "V" . "\x41" . "\x00"; // Feed + cut

        return $init . $text . $cut;
    }
}

<?php

namespace App\Notifications;

use App\Models\Inventory;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Inventory $inventory) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $productName = $this->inventory->product?->name ?? 'Unknown product';

        return (new MailMessage)
            ->subject('Low stock alert')
            ->line("{$productName} is at or below its reorder level.")
            ->line('Current quantity: '.$this->inventory->quantity)
            ->line('Reorder level: '.$this->inventory->reorder_level);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'inventory_id' => $this->inventory->id,
            'product_id' => $this->inventory->product_id,
            'store_id' => $this->inventory->store_id,
            'quantity' => $this->inventory->quantity,
            'reorder_level' => $this->inventory->reorder_level,
        ];
    }
}

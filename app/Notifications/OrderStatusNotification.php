<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification
{
    public function __construct(public readonly Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $label = Order::STATUS_LABELS[$this->order->status] ?? $this->order->status;

        return [
            'icon' => 'fa-bag-shopping',
            'tone' => Order::STATUS_TONES[$this->order->status] ?? 'neutral',
            'title' => 'Commande '.$this->order->order_number,
            'message' => 'Statut mis à jour : '.$label,
            'url' => route('account.orders.show', $this->order),
        ];
    }
}

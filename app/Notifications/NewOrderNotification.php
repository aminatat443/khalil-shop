<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
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
        return [
            'icon' => 'fa-cart-shopping',
            'tone' => 'primary',
            'title' => 'Nouvelle commande '.$this->order->order_number,
            'message' => $this->order->customer_name.' — '.number_format($this->order->total, 0, ',', ' ').' FCFA',
            'url' => route('admin.orders.show', $this->order),
        ];
    }
}

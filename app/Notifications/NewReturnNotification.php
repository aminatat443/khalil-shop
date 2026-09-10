<?php

namespace App\Notifications;

use App\Models\ProductReturn;
use Illuminate\Notifications\Notification;

class NewReturnNotification extends Notification
{
    public function __construct(public readonly ProductReturn $return)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $order = $this->return->orderItem->order;

        return [
            'icon' => 'fa-rotate-left',
            'tone' => 'amber',
            'title' => 'Nouvelle demande de retour',
            'message' => $order->order_number.' — '.$this->return->orderItem->product_name,
            'url' => route('admin.returns.index'),
        ];
    }
}

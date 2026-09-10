<?php

namespace App\Notifications;

use App\Models\ProductReturn;
use Illuminate\Notifications\Notification;

class ReturnStatusNotification extends Notification
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
        $label = ProductReturn::STATUS_LABELS[$this->return->status] ?? $this->return->status;

        return [
            'icon' => 'fa-rotate-left',
            'tone' => ProductReturn::STATUS_TONES[$this->return->status] ?? 'neutral',
            'title' => 'Retour — '.$order->order_number,
            'message' => 'Statut mis à jour : '.$label,
            'url' => route('account.orders.show', $order),
        ];
    }
}

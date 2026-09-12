<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class SecurityAlertNotification extends Notification
{
    public function __construct(public readonly string $summary)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'icon' => 'fa-shield-halved',
            'tone' => 'red',
            'title' => 'Alerte de sécurité',
            'message' => $this->summary,
            'url' => route('admin.security.index'),
        ];
    }
}

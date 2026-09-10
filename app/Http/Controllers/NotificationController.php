<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Interrogé par la cloche de notifications (client et équipe) toutes les quelques
     * secondes — pas de WebSocket/Pusher en place, ce polling léger fait office de
     * "temps réel" sans infrastructure supplémentaire à héberger.
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()->notifications()->latest()->take(15)->get();

        return response()->json([
            'unread_count' => $request->user()->unreadNotifications()->count(),
            'notifications' => $notifications->map(fn ($n) => [
                'id' => $n->id,
                'read' => $n->read_at !== null,
                'created_at' => $n->created_at->diffForHumans(),
                ...$n->data,
            ]),
        ]);
    }

    public function markRead(Request $request, string $id): RedirectResponse|JsonResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return $request->wantsJson() ? response()->json(['status' => 'ok']) : back();
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['status' => 'ok']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\DummyAuthService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected DummyAuthService $auth;

    public function __construct(DummyAuthService $auth)
    {
        $this->auth = $auth;
    }

    public function index()
    {
        $user = $this->auth->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $notifications = Notification::forUser($user['id'])
            ->latest()
            ->limit(20)
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => Notification::unreadCountForUser($user['id']),
        ]);
    }

    public function unreadCount()
    {
        $user = $this->auth->user();
        if (!$user) {
            return response()->json(['count' => 0]);
        }

        return response()->json([
            'count' => Notification::unreadCountForUser($user['id']),
        ]);
    }

    public function markAsRead($id)
    {
        $user = $this->auth->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $notification = Notification::forUser($user['id'])->findOrFail($id);
        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        $user = $this->auth->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        Notification::forUser($user['id'])->unread()->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}

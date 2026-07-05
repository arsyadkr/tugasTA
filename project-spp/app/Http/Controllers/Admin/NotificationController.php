<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // FIX P1013: Auth::user() mengembalikan ?Authenticatable yang tidak punya
    // method notifications() dan unreadNotifications() — keduanya milik trait
    // Illuminate\Notifications\Notifiable yang ada di App\Models\User.
    // Solusi: cast eksplisit ke User model via @var docblock.

    public function index(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $notifications = $user->notifications()
            ->latest()
            ->limit(20)
            ->get()
            ->map(fn($n) => [
                'id'         => $n->id,
                'data'       => $n->data,
                'read_at'    => $n->read_at,
                'created_at' => $n->created_at->diffForHumans(),
            ]);

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $user->unreadNotifications()->count(),
        ]);
    }

    public function markAsRead(string $id): JsonResponse|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $notification = $user->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
        }

        if (request()->expectsJson()) {
            return response()->json(['message' => 'OK']);
        }

        return back();
    }

    public function markAllAsRead(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $user->unreadNotifications->markAsRead();

        return response()->json(['message' => 'All marked as read.']);
    }
}

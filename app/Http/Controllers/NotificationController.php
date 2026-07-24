<?php

namespace App\Http\Controllers;

use App\Events\UserNotificationUpdated;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(DatabaseNotification $notification): RedirectResponse
    {
        abort_unless($notification->notifiable_id === auth()->id(), 403);

        $notification->markAsRead();
        broadcast(new UserNotificationUpdated(auth()->user()));

        $url = $notification->data['action_url'] ?? null;

        return $url ? redirect($url) : back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead(): RedirectResponse
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
        broadcast(new UserNotificationUpdated(auth()->user()));

        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(DatabaseNotification $notification): RedirectResponse
    {
        abort_unless($notification->notifiable_id === auth()->id(), 403);

        $notification->delete();
        broadcast(new UserNotificationUpdated(auth()->user()));

        return back()->with('success', 'Notification removed.');
    }
}

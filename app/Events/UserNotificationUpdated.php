<?php

namespace App\Events;

use App\Models\User;
use App\Notifications\NewMessageNotification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserNotificationUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public User $user)
    {
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("user.{$this->user->id}");
    }

    public function broadcastAs(): string
    {
        return 'notifications.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'message_unread_count' => $this->user->unreadNotifications()
                ->where('type', NewMessageNotification::class)
                ->count(),
            'notification_count' => $this->user->unreadNotifications()->count(),
            'pending_requests' => $this->user->pendingFriendRequests()->count(),
        ];
    }
}

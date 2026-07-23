<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FriendRequestNotification extends Notification
{
    use Queueable;

    public function __construct(protected User $sender)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'kind' => 'friend_request',
            'title' => 'New ally request',
            'body' => $this->sender->name . ' wants to join your squad.',
            'actor_id' => $this->sender->id,
            'actor_name' => $this->sender->name,
            'actor_avatar' => $this->sender->profile_photo_url,
            'action_url' => route('friends.requests'),
            'icon' => 'fas fa-user-plus',
            'color' => 'orange',
        ];
    }
}

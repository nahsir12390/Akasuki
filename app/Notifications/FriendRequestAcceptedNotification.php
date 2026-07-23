<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FriendRequestAcceptedNotification extends Notification
{
    use Queueable;

    public function __construct(protected User $friend)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'kind' => 'friend_accept',
            'title' => 'Ally request accepted',
            'body' => $this->friend->name . ' accepted your ally request.',
            'actor_id' => $this->friend->id,
            'actor_name' => $this->friend->name,
            'actor_avatar' => $this->friend->profile_photo_url,
            'action_url' => route('chat.index', ['user' => $this->friend->id]),
            'icon' => 'fas fa-user-check',
            'color' => 'green',
        ];
    }
}

<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class FriendRequestAcceptedNotification extends Notification
{
    use Queueable;

    public function __construct(protected User $friend)
    {
    }

    public function via($notifiable): array
    {
        $channels = ['database'];

        if ($this->webPushIsConfigured()) {
            $channels[] = WebPushChannel::class;
        }

        return $channels;
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

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('Ally request accepted')
            ->body($this->friend->name . ' accepted your ally request.')
            ->icon('/icons/icon-192.png')
            ->badge('/icons/icon-192.png')
            ->tag('friend-accepted-' . $this->friend->id)
            ->data([
                'url' => route('chat.index', ['user' => $this->friend->id], false),
                'notification_id' => $notification->id,
            ])
            ->options(['TTL' => 86400]);
    }

    private function webPushIsConfigured(): bool
    {
        return filled(config('webpush.vapid.public_key')) && filled(config('webpush.vapid.private_key'));
    }
}

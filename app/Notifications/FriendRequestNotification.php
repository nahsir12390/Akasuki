<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class FriendRequestNotification extends Notification
{
    use Queueable;

    public function __construct(protected User $sender)
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

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('New ally request')
            ->body($this->sender->name . ' wants to join your squad.')
            ->icon('/icons/icon-192.png')
            ->badge('/icons/icon-192.png')
            ->tag('friend-request-' . $this->sender->id)
            ->data([
                'url' => route('friends.requests', absolute: false),
                'notification_id' => $notification->id,
            ])
            ->options(['TTL' => 86400]);
    }

    private function webPushIsConfigured(): bool
    {
        return filled(config('webpush.vapid.public_key')) && filled(config('webpush.vapid.private_key'));
    }
}

<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification
{
    use Queueable;

    public function __construct(protected Message $message)
    {
    }

    public function via($notifiable): array
    {
        $channels = ['database'];

        if ($notifiable->email_messages ?? true) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toDatabase($notifiable): array
    {
        return [
            'kind' => 'message',
            'title' => 'New squad message',
            'body' => $this->message->sender->name . ': ' . str($this->message->message)->limit(120),
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'sender_avatar' => $this->message->sender->profile_photo_url,
            'message' => $this->message->message,
            'time' => $this->message->created_at->format('h:i A'),
            'action_url' => route('chat.index', ['user' => $this->message->sender_id]),
            'icon' => 'fas fa-comment-dots',
            'color' => 'orange',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New message from ' . $this->message->sender->name)
            ->greeting('New squad message')
            ->line($this->message->sender->name . ' sent you a message on Akatsuki Devs.')
            ->line('"' . str($this->message->message)->limit(120) . '"')
            ->action('Open Messages', route('chat.index', ['user' => $this->message->sender_id]))
            ->line('You can turn off message email alerts from your account preferences.');
    }
}
